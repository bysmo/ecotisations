<?php

namespace App\Observers;

use App\Models\Membre;
use App\Models\Caisse;
use Illuminate\Support\Str;

class MembreObserver
{
    /**
     * Gérer l'événement "créé" du membre.
     */
    public function created(Membre $membre): void
    {
        // 1. Créer le compte COURANT par défaut
        $this->createAccount($membre, 'courant', 'Compte Courant');

        // 2. Créer le compte ÉPARGNE par défaut
        $this->createAccount($membre, 'epargne', 'Compte Épargne');
    }

    /**
     * Créer un compte pour le membre (si inexistant pour ce type)
     */
    private function createAccount(Membre $membre, string $type, string $nom): void
    {
        Caisse::firstOrCreate(
            ['membre_id' => $membre->id, 'type' => $type],
            [
                'nom'           => $nom . ' - ' . $membre->nom_complet,
                'numero'        => $this->generateNumeroCaisse(),
                'solde_initial' => 0,
                'statut'        => 'active',
            ]
        );
    }

    /**
     * Générer un numéro de compte via le service de numérotation automatique
     */
    private function generateNumeroCaisse(): string
    {
        try {
            return app(\App\Services\AutoNumberingService::class)->generate('compte');
        } catch (\Exception $e) {
            // Fallback : Générer un numéro aléatoire si aucune configuration n'existe
            do {
                $part1 = strtoupper(\Illuminate\Support\Str::random(4));
                $part2 = strtoupper(\Illuminate\Support\Str::random(4));
                $numero = $part1 . '-' . $part2;
            } while (Caisse::where('numero', $numero)->exists());

            return $numero;
        }
    }
}
