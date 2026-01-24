<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Remboursement extends Model
{
    use HasFactory;

    protected $fillable = [
        'numero',
        'paiement_id',
        'membre_id',
        'caisse_id',
        'montant',
        'statut',
        'raison',
        'commentaire_admin',
        'traite_par',
        'traite_le',
    ];

    protected $casts = [
        'montant' => 'decimal:0',
        'traite_le' => 'datetime',
    ];

    /**
     * Relation avec le paiement
     */
    public function paiement()
    {
        return $this->belongsTo(Paiement::class);
    }

    /**
     * Relation avec le membre
     */
    public function membre()
    {
        return $this->belongsTo(Membre::class);
    }

    /**
     * Relation avec la caisse
     */
    public function caisse()
    {
        return $this->belongsTo(Caisse::class);
    }

    /**
     * Relation avec l'utilisateur qui a traité
     */
    public function traitePar()
    {
        return $this->belongsTo(User::class, 'traite_par');
    }

    /**
     * Vérifier si le remboursement est en attente
     */
    public function isEnAttente()
    {
        return $this->statut === 'en_attente';
    }

    /**
     * Vérifier si le remboursement est approuvé
     */
    public function isApprouve()
    {
        return $this->statut === 'approuve';
    }

    /**
     * Vérifier si le remboursement est refusé
     */
    public function isRefuse()
    {
        return $this->statut === 'refuse';
    }
}
