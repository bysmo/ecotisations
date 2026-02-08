<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'campagne_id',
        'membre_id',
        'paiement_id',
        'engagement_id',
        'destinataire_email',
        'sujet',
        'message',
        'statut',
        'erreur',
        'envoye_at',
        'metadata',
    ];

    protected $casts = [
        'envoye_at' => 'datetime',
        'metadata' => 'array',
    ];

    /**
     * Types d'emails
     */
    const TYPE_CAMPAGNE = 'campagne';
    const TYPE_PAIEMENT = 'paiement';
    const TYPE_ENGAGEMENT = 'engagement';
    const TYPE_FIN_MOIS = 'fin_mois';
    const TYPE_RAPPEL = 'rappel';
    const TYPE_AUTRE = 'autre';

    /**
     * Statuts
     */
    const STATUT_EN_ATTENTE = 'en_attente';
    const STATUT_ENVOYE = 'envoye';
    const STATUT_ECHEC = 'echec';

    /**
     * Relation avec la campagne
     */
    public function campagne()
    {
        return $this->belongsTo(EmailCampaign::class, 'campagne_id');
    }

    /**
     * Relation avec le membre
     */
    public function membre()
    {
        return $this->belongsTo(Membre::class, 'membre_id');
    }

    /**
     * Relation avec le paiement
     */
    public function paiement()
    {
        return $this->belongsTo(Paiement::class, 'paiement_id');
    }

    /**
     * Relation avec l'engagement
     */
    public function engagement()
    {
        return $this->belongsTo(Engagement::class, 'engagement_id');
    }

    /**
     * Marquer comme envoyé
     */
    public function markAsSent()
    {
        $this->update([
            'statut' => self::STATUT_ENVOYE,
            'envoye_at' => now(),
        ]);
    }

    /**
     * Marquer comme échoué
     */
    public function markAsFailed($errorMessage)
    {
        $this->update([
            'statut' => self::STATUT_ECHEC,
            'erreur' => $errorMessage,
        ]);
    }
}
