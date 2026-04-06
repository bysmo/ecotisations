<?php

namespace App\Services;

use App\Models\Membre;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PushNotificationService
{
    /**
     * Envoyer une notification push via FCM (Format Legacy pour la simplicité de démo)
     * Pour FCM v1, il faudrait utiliser un token OAuth2.
     */
    public function sendPush(Membre $membre, string $title, string $body, array $data = [])
    {
        if (!$membre->fcm_token) {
            return false;
        }

        $serverKey = env('FCM_SERVER_KEY');
        
        if (!$serverKey) {
            Log::info("FCM Log (Simulé) - To: {$membre->nom_complet}, Title: {$title}, Body: {$body}");
            return true; // Simulé si pas de clé
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'key=' . $serverKey,
                'Content-Type' => 'application/json',
            ])->post('https://fcm.googleapis.com/fcm/send', [
                'to' => $membre->fcm_token,
                'notification' => [
                    'title' => $title,
                    'body' => $body,
                    'sound' => 'default',
                ],
                'data' => array_merge($data, [
                    'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
                ]),
                'priority' => 'high',
            ]);

            return $response->successful();
        } catch (\Exception $e) {
            Log::error("Erreur d'envoi Push à {$membre->telephone}: " . $e->getMessage());
            return false;
        }
    }
}
