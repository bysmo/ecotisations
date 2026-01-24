<?php

namespace App\Notifications;

use App\Models\Engagement;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EngagementDueNotification extends Notification
{
    use Queueable;

    public $engagement;
    public $joursRestants;

    /**
     * Create a new notification instance.
     */
    public function __construct(Engagement $engagement, $joursRestants)
    {
        $this->engagement = $engagement;
        $this->joursRestants = $joursRestants;
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
        $cotisation = $this->engagement->cotisation;
        return [
            'type' => 'engagement_due',
            'title' => 'Engagement arrivant à échéance',
            'message' => "Votre engagement pour la cotisation \"{$cotisation->nom}\" arrive à échéance dans {$this->joursRestants} jour(s).",
            'engagement_id' => $this->engagement->id,
            'cotisation_id' => $cotisation->id,
            'cotisation_nom' => $cotisation->nom,
            'jours_restants' => $this->joursRestants,
        ];
    }
}
