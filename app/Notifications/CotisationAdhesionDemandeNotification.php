<?php

namespace App\Notifications;

use App\Models\CotisationAdhesion;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class CotisationAdhesionDemandeNotification extends Notification
{
    use Queueable;

    public function __construct(public CotisationAdhesion $adhesion)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $membre = $this->adhesion->membre;
        $cotisation = $this->adhesion->cotisation;
        return [
            'type' => 'cotisation_adhesion_demande',
            'title' => 'Demande d\'adhésion à une cotisation privée',
            'message' => "{$membre->nom_complet} demande à adhérer à la cotisation \"{$cotisation->nom}\".",
            'adhesion_id' => $this->adhesion->id,
            'membre_id' => $membre->id,
            'cotisation_id' => $cotisation->id,
        ];
    }
}
