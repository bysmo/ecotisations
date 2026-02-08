<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transfert extends Model
{
    use HasFactory;

    protected $fillable = [
        'caisse_source_id',
        'caisse_destination_id',
        'montant',
        'motif',
    ];

    protected $casts = [
        'montant' => \App\Casts\EncryptedDecimal::class,
    ];

    /**
     * Relation avec la caisse source
     */
    public function caisseSource()
    {
        return $this->belongsTo(Caisse::class, 'caisse_source_id');
    }

    /**
     * Relation avec la caisse destination
     */
    public function caisseDestination()
    {
        return $this->belongsTo(Caisse::class, 'caisse_destination_id');
    }
}
