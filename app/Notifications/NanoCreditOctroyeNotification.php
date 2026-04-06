<?php

namespace App\Notifications;

use App\Models\NanoCredit;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NanoCreditOctroyeNotification extends Notification
{
    use Queueable;

    public function __construct(public NanoCredit $nanoCredit)
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
        return [
            'title' => 'Argent reçu !',
            'body' => 'Votre nano-crédit de ' . number_format($this->nanoCredit->montant, 0, ',', ' ') . ' XOF a été décaissé sur votre mobile money.',
            'data' => [
                'type' => 'nano_credit_octroye',
                'nano_credit_id' => $this->nanoCredit->id,
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'nano_credit_octroye',
            'title' => 'Nano crédit octroyé',
            'message' => 'Votre demande de nano crédit de ' . number_format($this->nanoCredit->montant, 0, ',', ' ') . ' XOF a été accordée. Le montant a été envoyé sur votre mobile money.',
            'nano_credit_id' => $this->nanoCredit->id,
            'montant' => $this->nanoCredit->montant,
            'url' => route('membre.nano-credits.show', $this->nanoCredit),
        ];
    }
}
