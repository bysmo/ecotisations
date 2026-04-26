<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MembreWalletAlias extends Model
{
    use HasFactory;

    protected $fillable = [
        'membre_id',
        'alias',
        'label',
        'is_default',
    ];

    protected $casts = [
        'is_default' => 'boolean',
    ];

    public function membre()
    {
        return $this->belongsTo(Membre::class);
    }
}
