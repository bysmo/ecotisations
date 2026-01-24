<?php

namespace App\Notifications;

use App\Models\Paiement;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PayDunyaPaymentNotification extends Notification
{
    use Queueable;

    public $paiement;

    /**
     * Create a new notification instance.
     */
    public function __construct(Paiement $paiement)
    {
        $this->paiement = $paiement;
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
        $membre = $this->paiement->membre;
        $cotisation = $this->paiement->cotisation;
        $montant = number_format($this->paiement->montant, 0, ',', ' ') . ' XOF';
        
        return [
            'type' => 'paydunya_payment',
            'title' => 'Nouveau paiement PayDunya',
            'message' => "Le membre {$membre->prenom} {$membre->nom} a effectué un paiement de {$montant} pour la cotisation \"{$cotisation->nom}\" via PayDunya.",
            'paiement_id' => $this->paiement->id,
            'paiement_numero' => $this->paiement->numero,
            'membre_id' => $membre->id,
            'membre_nom' => $membre->prenom . ' ' . $membre->nom,
            'cotisation_id' => $cotisation->id,
            'cotisation_nom' => $cotisation->nom,
            'montant' => $this->paiement->montant,
        ];
    }
}
