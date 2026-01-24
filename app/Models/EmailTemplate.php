<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom',
        'sujet',
        'corps',
        'type',
        'actif',
    ];

    protected $casts = [
        'actif' => 'boolean',
    ];

    /**
     * Vérifier si le template est actif
     */
    public function isActive()
    {
        return $this->actif;
    }

    /**
     * Remplacer les variables dans le template
     */
    public function remplacerVariables(array $variables)
    {
        $sujet = $this->sujet;
        $corps = $this->corps;

        foreach ($variables as $key => $value) {
            $sujet = str_replace('{{' . $key . '}}', $value, $sujet);
            $corps = str_replace('{{' . $key . '}}', $value, $corps);
        }

        return [
            'sujet' => $sujet,
            'corps' => $corps,
        ];
    }
}
