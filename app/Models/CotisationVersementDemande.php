<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CotisationVersementDemande extends Model
{
    protected $table = 'cotisation_versement_demandes';

    protected $fillable = [
        'cotisation_id',
        'caisse_id',
        'demande_par_membre_id',
        'montant_demande',
        'statut',
        'traite_par_user_id',
        'traite_le',
        'commentaire',
    ];

    protected $casts = [
        'montant_demande' => \App\Casts\EncryptedDecimal::class,
        'traite_le' => 'datetime',
    ];

    public function cotisation(): BelongsTo
    {
        return $this->belongsTo(Cotisation::class);
    }

    public function caisse(): BelongsTo
    {
        return $this->belongsTo(Caisse::class);
    }

    public function demandeParMembre(): BelongsTo
    {
        return $this->belongsTo(Membre::class, 'demande_par_membre_id');
    }

    public function traiteParUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'traite_par_user_id');
    }

    public function isEnAttente(): bool
    {
        return $this->statut === 'en_attente';
    }
}
