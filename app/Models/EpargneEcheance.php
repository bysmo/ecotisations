<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Traits\HasChecksum;

class EpargneEcheance extends Model
{
    use HasFactory, HasChecksum;

    protected $table = 'epargne_echeances';

    const ETAT_EN_ATTENTE = 'en_attente';
    const ETAT_EN_COURS = 'en_cours';
    const ETAT_PAYEE = 'payee';

    protected $fillable = [
        'souscription_id',
        'date_echeance',
        'montant',
        'statut', // État de paiement : en_attente, en_cours, payee
        'paye_le',
        'dernier_rappel_at',
        'checksum',
    ];

    /**
     * Calcule le statut temporel (En retard, Aujourd'hui, À venir)
     */
    public function getTemporalStatusAttribute(): string
    {
        if ($this->statut === self::ETAT_PAYEE) {
            return 'termine';
        }

        $now = now()->startOfDay();
        $date = $this->date_echeance->copy()->startOfDay();

        if ($date->lt($now)) {
            return 'en_retard';
        } elseif ($date->eq($now)) {
            return 'aujourd_hui';
        } else {
            return 'a_venir';
        }
    }

    protected $casts = [
        'montant' => \App\Casts\EncryptedDecimal::class,
        'date_echeance' => 'date',
        'paye_le' => 'datetime',
        'dernier_rappel_at' => 'datetime',
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
