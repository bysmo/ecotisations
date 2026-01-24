<?php

namespace App\Notifications;

use App\Models\Remboursement;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class RemboursementPendingNotification extends Notification
{
    use Queueable;

    public $remboursement;

    /**
     * Create a new notification instance.
     */
    public function __construct(Remboursement $remboursement)
    {
        $this->remboursement = $remboursement;
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
        $membre = $this->remboursement->membre;
        return [
            'type' => 'remboursement_pending',
            'title' => 'Nouvelle demande de remboursement',
            'message' => "Le membre {$membre->nom} {$membre->prenom} a demandé un remboursement de " . number_format($this->remboursement->montant, 0, ',', ' ') . " XOF pour le paiement {$this->remboursement->paiement->numero}",
            'remboursement_id' => $this->remboursement->id,
            'remboursement_numero' => $this->remboursement->numero,
            'membre_id' => $membre->id,
            'membre_nom' => $membre->nom,
            'membre_prenom' => $membre->prenom,
            'montant' => $this->remboursement->montant,
        ];
    }
}
