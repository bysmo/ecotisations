<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cotisation extends Model
{
    use HasFactory;

    protected $fillable = [
        'numero',
        'nom',
        'caisse_id',
        'type',
        'frequence',
        'type_montant',
        'montant',
        'description',
        'notes',
        'actif',
        'tag',
        'segment',
    ];

    protected $casts = [
        'montant' => 'decimal:0',
        'actif' => 'boolean',
    ];

    /**
     * Relation avec la caisse
     */
    public function caisse()
    {
        return $this->belongsTo(Caisse::class);
    }

    /**
     * Relation avec les paiements
     */
    public function paiements()
    {
        return $this->hasMany(Paiement::class);
    }

    /**
     * Relation avec les engagements
     */
    public function engagements()
    {
        return $this->hasMany(\App\Models\Engagement::class);
    }

    /**
     * Vérifier si la cotisation est active
     */
    public function isActive()
    {
        return $this->actif === true;
    }
}
