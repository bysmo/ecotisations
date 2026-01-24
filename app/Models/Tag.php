<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom',
        'type',
        'description',
    ];

    /**
     * Relation avec les cotisations
     */
    public function cotisations()
    {
        return $this->hasMany(Cotisation::class, 'tag', 'nom');
    }

    /**
     * Relation avec les engagements
     */
    public function engagements()
    {
        return $this->hasMany(Engagement::class, 'tag', 'nom');
    }
}
