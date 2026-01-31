<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Membre extends Authenticatable implements MustVerifyEmail
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
            'email_verified_at' => 'datetime',
            'date_adhesion' => 'date',
            'password' => 'hashed',
        ];
    }

    /**
     * Envoyer la notification de vérification d'email (utilise la config SMTP admin)
     */
    public function sendEmailVerificationNotification(): void
    {
        app(\App\Services\EmailService::class)->sendVerificationEmail($this);
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

    /**
     * Relation KYC (une vérification par membre)
     */
    public function kycVerification()
    {
        return $this->hasOne(\App\Models\KycVerification::class);
    }

    /**
     * Vérifier si le membre a un KYC validé
     */
    public function hasKycValide(): bool
    {
        return $this->kycVerification && $this->kycVerification->isValide();
    }

    /**
     * Souscriptions épargne du membre
     */
    public function epargneSouscriptions()
    {
        return $this->hasMany(EpargneSouscription::class);
    }

    /**
     * Nano crédits (déboursements) du membre
     */
    public function nanoCredits()
    {
        return $this->hasMany(NanoCredit::class);
    }
}
