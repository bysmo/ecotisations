<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Segment extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom',
        'description',
    ];

    /**
     * Récupérer le nombre de membres pour ce segment.
     * Note: Le champ segment a été retiré de la table membres.
     */
    public function getNombreMembresAttribute()
    {
        return 0;
    }
}
