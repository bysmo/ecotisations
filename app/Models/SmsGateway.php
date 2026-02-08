<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SmsGateway extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'description',
        'config',
        'is_active',
        'order',
    ];

    protected $casts = [
        'config' => 'array',
        'is_active' => 'boolean',
    ];

    /**
     * Récupérer la passerelle SMS active (utilisée pour l'envoi OTP).
     */
    public static function getActive(): ?self
    {
        return self::where('is_active', true)->first();
    }

    /**
     * Récupérer une valeur de configuration.
     */
    public function getConfig(string $key, $default = null)
    {
        return $this->config[$key] ?? $default;
    }

    /**
     * Définir les champs de configuration attendus par code passerelle.
     */
    public static function configFields(string $code): array
    {
        $fields = [
            'log' => [],
            'twilio' => [
                ['key' => 'account_sid', 'label' => 'Account SID', 'type' => 'text', 'required' => true],
                ['key' => 'auth_token', 'label' => 'Auth Token', 'type' => 'password', 'required' => true],
                ['key' => 'from', 'label' => 'Numéro expéditeur (ex: +33123456789)', 'type' => 'text', 'required' => true],
            ],
            'vonage' => [
                ['key' => 'api_key', 'label' => 'API Key', 'type' => 'text', 'required' => true],
                ['key' => 'api_secret', 'label' => 'API Secret', 'type' => 'password', 'required' => true],
                ['key' => 'from', 'label' => 'Expéditeur (nom ou numéro)', 'type' => 'text', 'required' => true],
            ],
            'africas_talking' => [
                ['key' => 'username', 'label' => 'Username', 'type' => 'text', 'required' => true],
                ['key' => 'api_key', 'label' => 'API Key', 'type' => 'password', 'required' => true],
                ['key' => 'from', 'label' => 'Expéditeur (court code)', 'type' => 'text', 'required' => false],
            ],
            'infobip' => [
                ['key' => 'base_url', 'label' => 'Base URL (ex: https://api.infobip.com)', 'type' => 'text', 'required' => true],
                ['key' => 'api_key', 'label' => 'API Key', 'type' => 'password', 'required' => true],
                ['key' => 'from', 'label' => 'Expéditeur', 'type' => 'text', 'required' => true],
            ],
            'messagebird' => [
                ['key' => 'api_key', 'label' => 'API Key', 'type' => 'password', 'required' => true],
                ['key' => 'originator', 'label' => 'Originator (expéditeur)', 'type' => 'text', 'required' => true],
            ],
        ];
        return $fields[$code] ?? [];
    }
}
