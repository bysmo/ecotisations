<?php

namespace App\Notifications;

use App\Models\Cotisation;
use App\Models\Membre;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class CotisationVersementDemandeNotification extends Notification
{
    use Queueable;

    public function __construct(
        public Cotisation $cotisation,
        public float $montant,
        public Membre $demandePar
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'cotisation_versement_demande',
            'title' => 'Demande de versement des fonds',
            'message' => "{$this->demandePar->nom_complet} demande le versement de " . number_format($this->montant, 0, ',', ' ') . " XOF pour la cotisation \"{$this->cotisation->nom}\".",
            'cotisation_id' => $this->cotisation->id,
            'montant' => $this->montant,
            'demande_par_membre_id' => $this->demandePar->id,
        ];
    }
}
