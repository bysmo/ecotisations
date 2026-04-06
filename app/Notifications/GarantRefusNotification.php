<?php

namespace App\Notifications;

use App\Models\NanoCreditGarant;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class GarantRefusNotification extends Notification
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
        $garantMembre = $this->garant->membre;

        return [
            'title' => 'Garant refusé',
            'body' => "{$garantMembre->nom_complet} a refusé sa garantie. Veuillez choisir un nouveau garant.",
            'data' => [
                'type' => 'garant_refus',
                'nano_credit_id' => $this->garant->nano_credit_id,
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
        $garantMembre = $this->garant->membre;

        return [
            'type' => 'garant_refus',
            'title' => 'Un garant a refusé votre demande de crédit',
            'message' => "{$garantMembre->nom_complet} a décliné son association comme garant pour votre demande de nano-crédit #{$nanoCredit->id}. Veuillez modifier vos garants pour débloquer la demande.",
            'nano_credit_id' => $nanoCredit->id,
            'garant_id' => $this->garant->id,
            'url' => route('membre.nano-credits.modifier-garants', $nanoCredit),
        ];
    }
}
