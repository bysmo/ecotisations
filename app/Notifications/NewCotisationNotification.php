<?php

namespace App\Notifications;

use App\Models\Cotisation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewCotisationNotification extends Notification
{
    use Queueable;

    public $cotisation;

    /**
     * Create a new notification instance.
     */
    public function __construct(Cotisation $cotisation)
    {
        $this->cotisation = $cotisation;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $montant = $this->cotisation->montant ? number_format($this->cotisation->montant, 0, ',', ' ') . ' XOF' : 'Montant libre';
        return [
            'type' => 'new_cotisation',
            'title' => 'Nouvelle cotisation disponible',
            'message' => "Une nouvelle cotisation \"{$this->cotisation->nom}\" est disponible. Montant : {$montant}.",
            'cotisation_id' => $this->cotisation->id,
            'cotisation_nom' => $this->cotisation->nom,
            'cotisation_montant' => $this->cotisation->montant,
        ];
    }
}
