<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SortieCaisse extends Model
{
    use HasFactory;

    protected $table = 'sorties_caisse';

    protected $fillable = [
        'caisse_id',
        'montant',
        'motif',
        'notes',
        'date_sortie',
    ];

    protected $casts = [
        'montant' => 'decimal:0',
        'date_sortie' => 'date',
    ];

    /**
     * Relation avec la caisse
     */
    public function caisse()
    {
        return $this->belongsTo(Caisse::class);
    }
}
