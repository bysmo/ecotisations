<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PayDunyaConfiguration extends Model
{
    use HasFactory;

    protected $table = 'paydunya_configurations';

    protected $fillable = [
        'master_key',
        'private_key',
        'public_key',
        'token',
        'mode',
        'ipn_url',
        'enabled',
    ];

    protected $casts = [
        'enabled' => 'boolean',
    ];

    // Note: Les clés sensibles sont cryptées dans la base de données
    // Elles ne sont pas dans $hidden pour permettre l'affichage dans les formulaires
    // Le cryptage assure la sécurité

    /**
     * Récupérer la configuration active (singleton)
     */
    public static function getActive()
    {
        return self::first();
    }

    /**
     * Vérifier si PayDunya est activé
     */
    public function isEnabled()
    {
        return $this->enabled;
    }

    /**
     * Vérifier si on est en mode production
     */
    public function isLive()
    {
        return $this->mode === 'live';
    }
}
