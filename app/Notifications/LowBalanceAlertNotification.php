<?php

namespace App\Notifications;

use App\Models\Caisse;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LowBalanceAlertNotification extends Notification
{
    use Queueable;

    public $caisse;
    public $soldeActuel;
    public $seuil;

    /**
     * Create a new notification instance.
     */
    public function __construct(Caisse $caisse, $soldeActuel, $seuil)
    {
        $this->caisse = $caisse;
        $this->soldeActuel = $soldeActuel;
        $this->seuil = $seuil;
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
            'type' => 'low_balance',
            'title' => 'Alerte - Solde faible',
            'message' => "La caisse \"{$this->caisse->nom}\" a un solde faible : " . number_format($this->soldeActuel, 0, ',', ' ') . " XOF (seuil: " . number_format($this->seuil, 0, ',', ' ') . " XOF)",
            'caisse_id' => $this->caisse->id,
            'caisse_nom' => $this->caisse->nom,
            'solde_actuel' => $this->soldeActuel,
            'seuil' => $this->seuil,
        ];
    }
}
