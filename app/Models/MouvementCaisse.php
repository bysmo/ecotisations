<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MouvementCaisse extends Model
{
    use HasFactory;

    protected $table = 'mouvements_caisse';

    protected $fillable = [
        'caisse_id',
        'type',
        'sens',
        'montant',
        'date_operation',
        'libelle',
        'notes',
        'reference_type',
        'reference_id',
    ];

    protected $casts = [
        'montant' => 'decimal:0',
        'date_operation' => 'datetime',
    ];

    public function caisse()
    {
        return $this->belongsTo(Caisse::class);
    }

    public function reference()
    {
        return $this->morphTo();
    }

    public function isEntree(): bool
    {
        return $this->sens === 'entree';
    }
}

