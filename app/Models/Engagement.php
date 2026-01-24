<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Engagement extends Model
{
    use HasFactory;

    protected $fillable = [
        'numero',
        'membre_id',
        'cotisation_id',
        'montant_engage',
        'periodicite',
        'periode_debut',
        'periode_fin',
        'statut',
        'tag',
        'notes',
    ];

    protected $casts = [
        'montant_engage' => 'decimal:0',
        'periode_debut' => 'date',
        'periode_fin' => 'date',
    ];

    /**
     * Relation avec le membre
     */
    public function membre()
    {
        return $this->belongsTo(Membre::class);
    }

    /**
     * Relation avec la cotisation
     */
    public function cotisation()
    {
        return $this->belongsTo(Cotisation::class);
    }

    /**
     * Relation avec la caisse (via la cotisation)
     */
    public function caisse()
    {
        return $this->hasOneThrough(
            Caisse::class,
            Cotisation::class,
            'id',
            'id',
            'cotisation_id',
            'caisse_id'
        );
    }

    /**
     * Vérifier si l'engagement est en cours
     */
    public function isEnCours()
    {
        return $this->statut === 'en_cours';
    }

    /**
     * Vérifier et mettre à jour le statut selon la date d'échéance
     */
    public function checkAndUpdateStatut()
    {
        // Si l'engagement est en cours et que la date de fin est passée, mettre à jour le statut
        if ($this->statut === 'en_cours' && $this->periode_fin && $this->periode_fin->isPast()) {
            // Calculer le montant payé en récupérant tous les paiements depuis le début de la période
            // même s'ils sont faits après la fin de la période
            $montantPaye = \App\Models\Paiement::where('membre_id', $this->membre_id)
                ->where('cotisation_id', $this->cotisation_id)
                ->where(function($query) {
                    if ($this->periode_debut) {
                        $query->whereDate('date_paiement', '>=', $this->periode_debut);
                    } else {
                        $query->whereNotNull('date_paiement');
                    }
                })
                ->sum('montant');
            
            $resteAPayer = $this->montant_engage - $montantPaye;
            
            if ($resteAPayer > 0) {
                // Il reste à payer, donc l'engagement est en retard
                $this->statut = 'en_retard';
                $this->save();
            } else {
                // Entièrement payé, donc honoré
                $this->statut = 'honore';
                $this->save();
            }
        }
        
        return $this;
    }

    /**
     * Vérifier si l'engagement est terminé
     */
    public function isTermine()
    {
        return $this->statut === 'termine';
    }

    /**
     * Vérifier si l'engagement est annulé
     */
    public function isAnnule()
    {
        return $this->statut === 'annule';
    }

    /**
     * Calculer le nombre de périodes selon la périodicité
     */
    public function getNombrePeriodesAttribute()
    {
        if (!$this->periode_debut || !$this->periode_fin) {
            return 1;
        }

        $debut = \Carbon\Carbon::parse($this->periode_debut);
        $fin = \Carbon\Carbon::parse($this->periode_fin);
        
        // Calculer le nombre de mois entre les deux dates
        // (année_fin - année_debut) * 12 + (mois_fin - mois_debut) + 1
        $anneeDebut = $debut->year;
        $moisDebut = $debut->month;
        $anneeFin = $fin->year;
        $moisFin = $fin->month;
        
        $nombreMois = (($anneeFin - $anneeDebut) * 12) + ($moisFin - $moisDebut) + 1;

        $periodicite = $this->periodicite ?? 'mensuelle';

        switch ($periodicite) {
            case 'mensuelle':
                return $nombreMois;
            case 'trimestrielle':
                return (int) ceil($nombreMois / 3);
            case 'semestrielle':
                return (int) ceil($nombreMois / 6);
            case 'annuelle':
                return (int) ceil($nombreMois / 12);
            case 'unique':
                return 1;
            default:
                return $nombreMois;
        }
    }

    /**
     * Calculer le montant total à payer selon la périodicité
     */
    public function getMontantTotalAttribute()
    {
        return $this->montant_engage * $this->nombre_periodes;
    }

    /**
     * Calculer le montant total payé pour cet engagement
     */
    public function getMontantPayeAttribute()
    {
        return \App\Models\Paiement::where('membre_id', $this->membre_id)
            ->where('cotisation_id', $this->cotisation_id)
            ->whereDate('date_paiement', '>=', $this->periode_debut)
            ->whereDate('date_paiement', '<=', $this->periode_fin)
            ->sum('montant');
    }

    /**
     * Calculer le reste à payer
     */
    public function getResteAPayerAttribute()
    {
        return $this->montant_total - $this->montant_paye;
    }

    /**
     * Relation avec les paiements liés à cet engagement
     */
    public function paiements()
    {
        return \App\Models\Paiement::where('membre_id', $this->membre_id)
            ->where('cotisation_id', $this->cotisation_id)
            ->whereDate('date_paiement', '>=', $this->periode_debut)
            ->whereDate('date_paiement', '<=', $this->periode_fin);
    }
}
