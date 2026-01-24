<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Segment extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom',
        'description',
    ];

    /**
     * Récupérer le nombre de membres pour ce segment
     */
    public function getNombreMembresAttribute()
    {
        return \App\Models\Membre::where('segment', $this->nom)->count();
    }
}
