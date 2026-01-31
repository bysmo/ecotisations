<?php

namespace App\Services;

use App\Models\Membre;
use Illuminate\Support\Facades\Log;

class SmsWhatsappService
{
    /**
     * Envoyer un code OTP par SMS ou WhatsApp
     *
     * @param Membre $membre
     * @param string $code
     * @param string $method 'sms' ou 'whatsapp'
     * @return bool
     */
    public function sendOTP(Membre $membre, string $code, string $method = 'sms'): bool
    {
        $message = "Votre code de vérification FlexFin est : $code. Ne le partagez avec personne.";
        
        return $this->sendMessage($membre->telephone, $message, $method);
    }

    /**
     * Envoyer une notification générique
     *
     * @param string $to Numéro de téléphone
     * @param string $message
     * @param string $method 'sms' ou 'whatsapp'
     * @return bool
     */
    public function sendMessage(string $to, string $message, string $method = 'sms'): bool
    {
        // Log pour le moment (simulation)
        Log::info("Envoi notification [$method] à $to : $message");

        // TODO: Intégrer une API de SMS (ex: Twilio, Orange SMS, Africa's Talking)
        // TODO: Intégrer une API WhatsApp (ex: Twilio WhatsApp, UltraMsg, interakt)

        return true; 
    }
}
