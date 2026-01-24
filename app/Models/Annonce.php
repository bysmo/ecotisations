<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Annonce extends Model
{
    use HasFactory;

    protected $fillable = [
        'titre',
        'contenu',
        'date_debut',
        'date_fin',
        'statut',
        'type',
        'ordre',
        'segment',
    ];

    protected $casts = [
        'date_debut' => 'date',
        'date_fin' => 'date',
        'ordre' => 'integer',
    ];

    /**
     * Vérifier si l'annonce est active
     */
    public function isActive()
    {
        if ($this->statut !== 'active') {
            return false;
        }

        $now = Carbon::now();

        // Vérifier la date de début
        if ($this->date_debut && $now->lt($this->date_debut)) {
            return false;
        }

        // Vérifier la date de fin
        if ($this->date_fin && $now->gt($this->date_fin)) {
            return false;
        }

        return true;
    }

    /**
     * Scope pour récupérer les annonces actives
     */
    public function scopeActive($query)
    {
        $now = Carbon::now();
        
        return $query->where('statut', 'active')
            ->where(function($q) use ($now) {
                $q->whereNull('date_debut')
                  ->orWhere('date_debut', '<=', $now);
            })
            ->where(function($q) use ($now) {
                $q->whereNull('date_fin')
                  ->orWhere('date_fin', '>=', $now);
            });
    }
}
