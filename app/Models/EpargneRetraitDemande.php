<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasChecksum;

class EpargneRetraitDemande extends Model
{
    use HasFactory, HasChecksum;

    protected $table = 'epargne_retrait_demandes';

    protected $fillable = [
        'souscription_id',
        'membre_id',
        'montant_demande',
        'statut', // en_attente, traite, rejete
        'traite_par_user_id',
        'traite_le',
        'commentaire',
        'mode_retrait', // virement_interne, pispi
        'checksum',
    ];

    protected $casts = [
        'montant_demande' => \App\Casts\EncryptedDecimal::class,
        'traite_le'       => 'datetime',
    ];

    public function souscription()
    {
        return $this->belongsTo(EpargneSouscription::class, 'souscription_id');
    }

    public function membre()
    {
        return $this->belongsTo(Membre::class);
    }

    public function traitePar()
    {
        return $this->belongsTo(User::class, 'traite_par_user_id');
    }

    public static function statutLabels(): array
    {
        return [
            'en_attente' => 'En attente',
            'traite'     => 'Traité',
            'rejete'     => 'Rejeté',
        ];
    }

    public function getStatutLabelAttribute(): string
    {
        return self::statutLabels()[$this->statut] ?? $this->statut;
    }
}
