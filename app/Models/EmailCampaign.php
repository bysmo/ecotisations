<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailCampaign extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom',
        'sujet',
        'message',
        'statut',
        'filtres',
        'total_destinataires',
        'envoyes',
        'echecs',
        'cree_par',
        'envoyee_at',
    ];

    protected $casts = [
        'filtres' => 'array',
        'envoyee_at' => 'datetime',
    ];

    /**
     * Relation avec l'utilisateur créateur
     */
    public function creePar()
    {
        return $this->belongsTo(User::class, 'cree_par');
    }

    /**
     * Relation avec les logs d'emails
     */
    public function emailLogs()
    {
        return $this->hasMany(EmailLog::class, 'campagne_id');
    }

    /**
     * Vérifier si la campagne est terminée
     */
    public function isTerminee()
    {
        return $this->statut === 'terminee';
    }

    /**
     * Vérifier si la campagne est en cours
     */
    public function isEnCours()
    {
        return $this->statut === 'en_cours';
    }

    /**
     * Obtenir le pourcentage de progression
     */
    public function getProgressionAttribute()
    {
        if ($this->total_destinataires == 0) {
            return 0;
        }
        return round(($this->envoyes / $this->total_destinataires) * 100, 2);
    }
}
