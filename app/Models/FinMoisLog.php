<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class FinMoisLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'membre_id',
        'periode_debut',
        'periode_fin',
        'email_destinataire',
        'sujet_email',
        'corps_email',
        'statut',
        'erreur',
        'envoye_at',
        'envoye_par',
        'resume_paiements',
        'nombre_paiements',
        'montant_total',
    ];

    protected $casts = [
        'periode_debut' => 'date',
        'periode_fin' => 'date',
        'envoye_at' => 'datetime',
        'resume_paiements' => 'array',
        'montant_total' => \App\Casts\EncryptedDecimal::class,
    ];

    /**
     * Relation avec le membre
     */
    public function membre()
    {
        return $this->belongsTo(Membre::class);
    }

    /**
     * Relation avec l'admin qui a lancé le traitement
     */
    public function envoyePar()
    {
        return $this->belongsTo(User::class, 'envoye_par');
    }

    /**
     * Vérifier si un email a déjà été envoyé pour cette période
     */
    public static function dejaEnvoye($membreId, $periodeDebut, $periodeFin)
    {
        return self::where('membre_id', $membreId)
            ->where('periode_debut', $periodeDebut)
            ->where('periode_fin', $periodeFin)
            ->where('statut', 'envoye')
            ->exists();
    }
}
