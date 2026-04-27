<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

use App\Traits\HasChecksum;

class NanoCreditEcheance extends Model
{
    use HasChecksum;
    protected $table = 'nano_credit_echeances';

    const ETAT_EN_ATTENTE = 'en_attente';
    const ETAT_EN_COURS = 'en_cours';
    const ETAT_PAYEE = 'payee';

    protected $fillable = [
        'nano_credit_id',
        'date_echeance',
        'montant',
        'statut', // État de paiement : en_attente, en_cours, payee
        'paye_le',
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
    ];

    public function nanoCredit(): BelongsTo
    {
        return $this->belongsTo(NanoCredit::class);
    }

    public function versements(): HasMany
    {
        return $this->hasMany(NanoCreditVersement::class, 'nano_credit_echeance_id');
    }
}
