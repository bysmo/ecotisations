<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotificationLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'recipient_type',
        'recipient_id',
        'recipient_email',
        'subject',
        'message',
        'status',
        'error_message',
        'sent_at',
        'metadata',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
        'metadata' => 'array',
    ];

    /**
     * Types de notifications
     */
    const TYPE_PAYMENT_REMINDER = 'payment_reminder';
    const TYPE_UPCOMING_PAYMENT = 'upcoming_payment';
    const TYPE_LOW_BALANCE = 'low_balance';
    const TYPE_ENGAGEMENT_DUE = 'engagement_due';

    /**
     * Statuts
     */
    const STATUS_PENDING = 'pending';
    const STATUS_SENT = 'sent';
    const STATUS_FAILED = 'failed';

    /**
     * Vérifier si un rappel a déjà été envoyé récemment
     */
    public static function hasRecentNotification($type, $recipientType, $recipientId, $days = 7)
    {
        return self::where('type', $type)
            ->where('recipient_type', $recipientType)
            ->where('recipient_id', $recipientId)
            ->where('status', self::STATUS_SENT)
            ->where('created_at', '>=', now()->subDays($days))
            ->exists();
    }

    /**
     * Créer un log de notification
     */
    public static function createLog($type, $recipientType, $recipientId, $recipientEmail, $subject, $message, $metadata = null)
    {
        return self::create([
            'type' => $type,
            'recipient_type' => $recipientType,
            'recipient_id' => $recipientId,
            'recipient_email' => $recipientEmail,
            'subject' => $subject,
            'message' => $message,
            'status' => self::STATUS_PENDING,
            'metadata' => $metadata,
        ]);
    }

    /**
     * Marquer comme envoyé
     */
    public function markAsSent()
    {
        $this->update([
            'status' => self::STATUS_SENT,
            'sent_at' => now(),
        ]);
    }

    /**
     * Marquer comme échoué
     */
    public function markAsFailed($errorMessage)
    {
        $this->update([
            'status' => self::STATUS_FAILED,
            'error_message' => $errorMessage,
        ]);
    }
}
