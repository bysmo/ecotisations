<?php

namespace App\Channels;

use App\Services\PushNotificationService;
use Illuminate\Notifications\Notification;

class FcmChannel
{
    public function __construct(protected PushNotificationService $pushService)
    {
    }

    /**
     * Envoyer la notification via FCM
     */
    public function send($notifiable, Notification $notification)
    {
        if (!method_exists($notification, 'toFcm')) {
            return;
        }

        $message = $notification->toFcm($notifiable);
        if (!$message) {
            return;
        }

        $this->pushService->sendPush(
            $notifiable,
            $message['title'] ?? 'Notification',
            $message['body'] ?? '',
            $message['data'] ?? []
        );
    }
}
