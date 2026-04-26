<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PiSpiOperationAlias extends Model
{
    use HasFactory;
    
    protected $table = 'pispi_operation_aliases';

    protected $fillable = [
        'operation_type',
        'alias',
        'label',
    ];

    /**
     * Récupère l'alias pour un type d'opération spécifique
     */
    public static function getForType(string $type)
    {
        return self::where('operation_type', $type)->first()?->alias;
    }
}
