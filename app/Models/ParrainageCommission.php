<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ParrainageCommission extends Model
{
    use HasFactory;

    protected $table = 'parrainage_commissions';

    protected $fillable = [
        'parrain_id',
        'filleul_id',
        'niveau',
        'montant',
        'statut',
        'declencheur',
        'disponible_le',
        'reclame_le',
        'paye_le',
        'traite_par',
        'note_admin',
        'reference',
    ];

    protected function casts(): array
    {
        return [
            'montant'       => 'decimal:2',
            'niveau'        => 'integer',
            'disponible_le' => 'datetime',
            'reclame_le'    => 'datetime',
            'paye_le'       => 'datetime',
        ];
    }

    /**
     * Hook de création pour générer une référence unique
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($commission) {
            if (empty($commission->reference)) {
                $commission->reference = 'PAR-' . strtoupper(Str::random(8)) . '-' . date('Ymd');
            }
        });
    }

    // ─── Relations ────────────────────────────────────────────────────────────

    public function parrain()
    {
        return $this->belongsTo(Membre::class, 'parrain_id');
    }

    public function filleul()
    {
        return $this->belongsTo(Membre::class, 'filleul_id');
    }

    public function traitePar()
    {
        return $this->belongsTo(User::class, 'traite_par');
    }

    // ─── Scopes ───────────────────────────────────────────────────────────────

    public function scopeDisponibles($query)
    {
        return $query->where('statut', 'disponible')
                     ->where(function ($q) {
                         $q->whereNull('disponible_le')
                           ->orWhere('disponible_le', '<=', now());
                     });
    }

    public function scopeReclames($query)
    {
        return $query->where('statut', 'reclame');
    }

    public function scopeEnAttente($query)
    {
        return $query->where('statut', 'en_attente');
    }

    public function scopePourParrain($query, int $parrainId)
    {
        return $query->where('parrain_id', $parrainId);
    }

    // ─── Accesseurs ───────────────────────────────────────────────────────────

    /**
     * Libellé coloré du statut
     */
    public function getLabelStatutAttribute(): string
    {
        return match ($this->statut) {
            'en_attente'    => 'En attente',
            'disponible'    => 'Disponible',
            'reclame'       => 'Réclamé',
            'paye'          => 'Payé',
            'annule'        => 'Annulé',
            default         => ucfirst($this->statut),
        };
    }

    public function getBadgeStatutAttribute(): string
    {
        return match ($this->statut) {
            'en_attente'    => 'warning',
            'disponible'    => 'success',
            'reclame'       => 'info',
            'paye'          => 'primary',
            'annule'        => 'danger',
            default         => 'secondary',
        };
    }

    public function getLabelDeclencheurAttribute(): string
    {
        return match ($this->declencheur) {
            'premier_paiement'      => 'Premier paiement',
            'adhesion_cotisation'   => 'Adhésion cotisation',
            default                 => 'Inscription',
        };
    }

    /**
     * Vérifie si la commission est réclamable par le parrain
     */
    public function isReclaimable(): bool
    {
        return $this->statut === 'disponible'
            && ($this->disponible_le === null || $this->disponible_le->lte(now()));
    }
}
