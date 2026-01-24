<?php

namespace App\Notifications;

use App\Models\Cotisation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentOverdueNotification extends Notification
{
    use Queueable;

    public $cotisation;
    public $joursRetard;

    /**
     * Create a new notification instance.
     */
    public function __construct(Cotisation $cotisation, $joursRetard)
    {
        $this->cotisation = $cotisation;
        $this->joursRetard = $joursRetard;
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
        return [
            'type' => 'payment_overdue',
            'title' => 'Paiement en retard',
            'message' => "Votre paiement pour la cotisation \"{$this->cotisation->nom}\" est en retard de {$this->joursRetard} jour(s).",
            'cotisation_id' => $this->cotisation->id,
            'cotisation_nom' => $this->cotisation->nom,
            'jours_retard' => $this->joursRetard,
        ];
    }
}
