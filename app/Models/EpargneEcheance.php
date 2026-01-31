<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EpargneEcheance extends Model
{
    use HasFactory;

    protected $table = 'epargne_echeances';

    protected $fillable = [
        'souscription_id',
        'date_echeance',
        'montant',
        'statut',
        'paye_le',
    ];

    protected $casts = [
        'montant' => 'decimal:0',
        'date_echeance' => 'date',
        'paye_le' => 'datetime',
    ];

    public function souscription()
    {
        return $this->belongsTo(EpargneSouscription::class);
    }

    public function versement()
    {
        return $this->hasOne(EpargneVersement::class, 'echeance_id');
    }
}
