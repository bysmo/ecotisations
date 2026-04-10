<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Modèle Segment — Segmentation clientèle des membres
 *
 * Segments disponibles (créés par SegmentSeeder) :
 *   NON CLASSÉ, Étudiant, Fonctionnaire, Commerçant,
 *   Entreprise Informelle, Entreprise Privée,
 *   Communauté Religieuse, Association, ONG, Diaspora, Retraité, Artisan
 *
 * Relations :
 *   - membres : liste des membres de ce segment
 *
 * Le segment "NON CLASSÉ" (id=1 par convention) est le segment par défaut.
 * Il ne peut pas être supprimé depuis l'interface admin.
 */
class Segment extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom',
        'slug',
        'description',
        'couleur',   // Code couleur hex pour l'UI (ex: #4a6cf7)
        'icone',     // Classe Bootstrap Icons (ex: bi bi-person-badge)
        'is_default',// Segment par défaut (NON CLASSÉ)
        'actif',
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'actif'      => 'boolean',
    ];

    // ─── Relations ────────────────────────────────────────────────────────────

    /**
     * Membres appartenant à ce segment.
     */
    public function membres(): HasMany
    {
        return $this->hasMany(Membre::class, 'segment_id');
    }

    // ─── Accesseurs ───────────────────────────────────────────────────────────

    /**
     * Nombre de membres dans ce segment.
     */
    public function getNombreMembresAttribute(): int
    {
        return $this->membres()->count();
    }

    // ─── Scopes ───────────────────────────────────────────────────────────────

    public function scopeActif($query)
    {
        return $query->where('actif', true);
    }

    // ─── Méthodes statiques ───────────────────────────────────────────────────

    /**
     * Retourne le segment par défaut (NON CLASSÉ).
     */
    public static function getDefault(): ?self
    {
        return self::where('is_default', true)->first();
    }

    /**
     * Retourne l'ID du segment par défaut (NON CLASSÉ).
     */
    public static function getDefaultId(): ?int
    {
        return self::where('is_default', true)->value('id');
    }
}
