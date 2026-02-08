<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CotisationAdhesion extends Model
{
    protected $fillable = [
        'membre_id',
        'cotisation_id',
        'statut',
        'traite_par',
        'traite_le',
        'commentaire_admin',
    ];

    protected $casts = [
        'traite_le' => 'datetime',
    ];

    public function membre(): BelongsTo
    {
        return $this->belongsTo(Membre::class);
    }

    public function cotisation(): BelongsTo
    {
        return $this->belongsTo(Cotisation::class);
    }

    public function traitePar(): BelongsTo
    {
        return $this->belongsTo(User::class, 'traite_par');
    }

    public function isEnAttente(): bool
    {
        return $this->statut === 'en_attente';
    }

    public function isAccepte(): bool
    {
        return $this->statut === 'accepte';
    }

    public function isRefuse(): bool
    {
        return $this->statut === 'refuse';
    }

    public static function statutLabels(): array
    {
        return [
            'en_attente' => 'En attente',
            'accepte' => 'Accepté',
            'refuse' => 'Refusé',
        ];
    }
}
