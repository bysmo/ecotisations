<?php

namespace App\Notifications;

use App\Models\Membre;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\URL;

class VerifyMembreEmailNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Membre $membre)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        // Utiliser la configuration SMTP paramétrée par l'admin (pas le .env)
        (new \App\Services\EmailService())->configureSMTP();

        $appNom = \App\Models\AppSetting::get('app_nom', 'Gestion des Cotisations');
        $url = $this->verificationUrl($notifiable);

        return (new MailMessage)
            ->subject("Vérifiez votre adresse email - {$appNom}")
            ->greeting("Bonjour {$notifiable->prenom},")
            ->line("Merci de vous être inscrit sur {$appNom}. Veuillez cliquer sur le bouton ci-dessous pour vérifier votre adresse email.")
            ->action('Vérifier mon email', $url)
            ->line('Ce lien expirera dans 60 minutes. Si vous n\'êtes pas à l\'origine de cette inscription, vous pouvez ignorer cet email.');
    }

    protected function verificationUrl(object $notifiable): string
    {
        return URL::temporarySignedRoute(
            'membre.verification.verify',
            Carbon::now()->addMinutes(60),
            [
                'id' => $notifiable->getKey(),
                'hash' => sha1($notifiable->getEmailForVerification()),
            ]
        );
    }
}
