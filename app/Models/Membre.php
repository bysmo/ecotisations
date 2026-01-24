<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Membre extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'numero',
        'nom',
        'prenom',
        'email',
        'telephone',
        'adresse',
        'date_adhesion',
        'statut',
        'segment',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'date_adhesion' => 'date',
            'password' => 'hashed',
        ];
    }

    /**
     * Nom complet du membre
     */
    public function getNomCompletAttribute()
    {
        return $this->prenom . ' ' . $this->nom;
    }

    /**
     * Vérifier si le membre est actif
     */
    public function isActif()
    {
        return $this->statut === 'actif';
    }

    /**
     * Obtenir le nom du guard pour l'authentification
     */
    public function getGuardName()
    {
        return 'membre';
    }

    /**
     * Relation avec les paiements
     */
    public function paiements()
    {
        return $this->hasMany(\App\Models\Paiement::class);
    }

    /**
     * Obtenir les cotisations via les paiements
     */
    public function cotisations()
    {
        return $this->hasManyThrough(
            \App\Models\Cotisation::class,
            \App\Models\Paiement::class,
            'membre_id',
            'id',
            'id',
            'cotisation_id'
        );
    }

    /**
     * Relation avec les engagements
     */
    public function engagements()
    {
        return $this->hasMany(\App\Models\Engagement::class);
    }

    /**
     * Relation avec les remboursements
     */
    public function remboursements()
    {
        return $this->hasMany(\App\Models\Remboursement::class);
    }
}
