<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KycVerification extends Model
{
    protected $table = 'kyc_verifications';

    protected $fillable = [
        'membre_id',
        'statut',
        'motif_rejet',
        'type_piece',
        'numero_piece',
        'date_naissance',
        'lieu_naissance',
        'adresse_kyc',
        'metier',
        'localisation',
        'contact_1',
        'contact_2',
        'validated_at',
        'rejected_at',
        'validated_by',
        'rejected_by',
    ];

    protected function casts(): array
    {
        return [
            'date_naissance' => 'date',
            'validated_at' => 'datetime',
            'rejected_at' => 'datetime',
        ];
    }

    public const STATUT_EN_ATTENTE = 'en_attente';
    public const STATUT_VALIDE = 'valide';
    public const STATUT_REJETE = 'rejete';

    public function membre(): BelongsTo
    {
        return $this->belongsTo(Membre::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(KycDocument::class, 'kyc_verification_id');
    }

    public function validatedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'validated_by');
    }

    public function rejectedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'rejected_by');
    }

    public function isEnAttente(): bool
    {
        return $this->statut === self::STATUT_EN_ATTENTE;
    }

    public function isValide(): bool
    {
        return $this->statut === self::STATUT_VALIDE;
    }

    public function isRejete(): bool
    {
        return $this->statut === self::STATUT_REJETE;
    }
}
