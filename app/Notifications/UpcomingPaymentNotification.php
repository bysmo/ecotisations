<?php

namespace App\Notifications;

use App\Models\Cotisation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Carbon\Carbon;

class UpcomingPaymentNotification extends Notification
{
    use Queueable;

    public $cotisation;
    public $dateEcheance;
    public $joursAvant;

    /**
     * Create a new notification instance.
     */
    public function __construct(Cotisation $cotisation, Carbon $dateEcheance, $joursAvant)
    {
        $this->cotisation = $cotisation;
        $this->dateEcheance = $dateEcheance;
        $this->joursAvant = $joursAvant;
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
            'type' => 'upcoming_payment',
            'title' => 'Paiement à venir',
            'message' => "Votre paiement pour la cotisation \"{$this->cotisation->nom}\" est prévu le {$this->dateEcheance->format('d/m/Y')}. Il vous reste {$this->joursAvant} jour(s).",
            'cotisation_id' => $this->cotisation->id,
            'cotisation_nom' => $this->cotisation->nom,
            'date_echeance' => $this->dateEcheance->format('Y-m-d'),
            'jours_avant' => $this->joursAvant,
        ];
    }
}
