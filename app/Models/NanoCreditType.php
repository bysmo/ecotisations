<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class NanoCreditType extends Model
{
    protected $table = 'nano_credit_types';

    protected $fillable = [
        'nom',
        'description',
        'montant_min',
        'montant_max',
        'duree_mois',
        'taux_interet',
        'frequence_remboursement',
        'actif',
        'ordre',
    ];

    protected $casts = [
        'montant_min' => 'decimal:0',
        'montant_max' => 'decimal:0',
        'taux_interet' => 'decimal:2',
        'actif' => 'boolean',
    ];

    public function nanoCredits(): HasMany
    {
        return $this->hasMany(NanoCredit::class, 'nano_credit_type_id');
    }

    public function getFrequenceRemboursementLabelAttribute(): string
    {
        return match ($this->frequence_remboursement ?? 'mensuel') {
            'hebdomadaire' => 'Hebdomadaire',
            'mensuel' => 'Mensuel',
            'trimestriel' => 'Trimestriel',
            default => $this->frequence_remboursement,
        };
    }

    /**
     * Nombre d'échéances de remboursement selon la durée et la fréquence
     */
    public function getNombreEcheancesAttribute(): int
    {
        $duree = (int) ($this->duree_mois ?? 12);
        return match ($this->frequence_remboursement ?? 'mensuel') {
            'hebdomadaire' => (int) min(52 * 2, ceil(52 * $duree / 12)),
            'mensuel' => $duree,
            'trimestriel' => (int) max(1, ceil($duree / 3)),
            default => $duree,
        };
    }

    /**
     * Calcule tableau d'amortissement : intérêt simple.
     * interet_total = montant * (taux/100) * (duree_mois/12)
     * montant_total_du = montant + interet_total
     * echéance = montant_total_du / nb_echeances
     */
    public function calculAmortissement(float $montant): array
    {
        $dureeMois = (int) ($this->duree_mois ?? 12);
        $taux = (float) ($this->taux_interet ?? 0);
        $interetTotal = round($montant * ($taux / 100) * ($dureeMois / 12), 0);
        $montantTotalDu = $montant + $interetTotal;
        $nbEcheances = $this->nombre_echeances;
        $montantEcheance = $nbEcheances > 0 ? (int) round($montantTotalDu / $nbEcheances, 0) : 0;
        return [
            'montant_emprunte' => (int) round($montant, 0),
            'interet_total' => $interetTotal,
            'montant_total_du' => $montantTotalDu,
            'nombre_echeances' => $nbEcheances,
            'montant_echeance' => $montantEcheance,
        ];
    }
}
