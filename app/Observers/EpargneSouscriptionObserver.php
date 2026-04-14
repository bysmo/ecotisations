<?php

namespace App\Observers;

use App\Models\EpargneSouscription;
use App\Models\Caisse;
use Illuminate\Support\Str;

class EpargneSouscriptionObserver
{
    /**
     * Gérer l'événement "création" (avant insertion).
     * On crée le compte et on l'associe à la souscription.
     */
    public function creating(EpargneSouscription $souscription): void
    {
        $membre = $souscription->membre;
        $plan = $souscription->plan;

        // Créer le compte dédié à cette tontine
        $compte = Caisse::create([
            'membre_id'   => $membre->id,
            'nom'         => 'Compte Tontine (' . $plan->nom . ') - ' . $membre->nom_complet,
            'numero'      => $this->generateNumeroCaisse(),
            'solde_init'  => 0,
            'solde_actuel'=> 0,
            'type'        => 'tontine',
            'actif'       => true,
        ]);

        // Lier le compte à la souscription
        $souscription->caisse_id = $compte->id;
    }

    /**
     * Générer un numéro de compte unique (format XXXX-XXXX)
     */
    private function generateNumeroCaisse(): string
    {
        do {
            $part1 = strtoupper(Str::random(4));
            $part2 = strtoupper(Str::random(4));
            $numero = $part1 . '-' . $part2;
        } while (Caisse::where('numero', $numero)->exists());

        return $numero;
    }
}
