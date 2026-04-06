<?php

namespace App\Notifications;

use App\Models\NanoCreditGarant;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class GarantSollicitationNotification extends Notification
{
    use Queueable;

    public function __construct(public NanoCreditGarant $garant)
    {
    }

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        $channels = ['database'];
        if ($notifiable->fcm_token) {
            $channels[] = \App\Channels\FcmChannel::class;
        }
        return $channels;
    }

    public function toFcm(object $notifiable): array
    {
        $nanoCredit = $this->garant->nanoCredit;
        $demandeur = $nanoCredit->membre;

        return [
            'title' => 'Besoin d\'un garant !',
            'body' => "{$demandeur->prenom} {$demandeur->nom} vous sollicite pour un nano-crédit. Acceptez pour gagner une commission !",
            'data' => [
                'type' => 'garant_sollicitation',
                'nano_credit_id' => $nanoCredit->id,
                'garant_id' => $this->garant->id,
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $nanoCredit = $this->garant->nanoCredit;
        $demandeur = $nanoCredit->membre;
        $palier = $nanoCredit->palier;

        // Calcul du gain potentiel pour le garant
        $interetsTotaux = (float) ($palier->calculAmortissement((float) $nanoCredit->montant)['interet_total'] ?? 0);
        $pourcentagePartage = (float) ($palier->pourcentage_partage_garant ?? 0);
        $montantAPartager = $interetsTotaux * ($pourcentagePartage / 100);
        $nbGarants = $nanoCredit->garants()->count();
        $gainPotentiel = $nbGarants > 0 ? (int) round($montantAPartager / $nbGarants, 0) : 0;

        return [
            'type' => 'garant_sollicitation',
            'title' => 'Nouvelle sollicitation de garantie',
            'message' => "{$demandeur->prenom} {$demandeur->nom} vous sollicite comme garant pour un nano-crédit de " . number_format($nanoCredit->montant, 0, ',', ' ') . " XOF. Gain potentiel : " . number_format($gainPotentiel, 0, ',', ' ') . " XOF.",
            'nano_credit_id' => $nanoCredit->id,
            'garant_id' => $this->garant->id,
            'demandeur_id' => $demandeur->id,
            'url' => route('membre.garant.sollicitations'),
        ];
    }
}
