<?php

namespace Database\Seeders;

use App\Models\Remboursement;
use App\Models\Paiement;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class RemboursementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $paiements = Paiement::with(['membre'])->get();
        $users = User::all();

        if ($paiements->isEmpty()) {
            $this->command->warn('Aucun paiement trouvé. Veuillez d\'abord exécuter PaiementSeeder.');
            return;
        }

        $this->command->info('Création des remboursements...');

        $raisons = [
            'Double paiement',
            'Erreur de montant',
            'Paiement annulé',
            'Cotisation non due',
            'Erreur de saisie',
            'Demande du membre',
        ];

        $commentairesAdmin = [
            'Dossier vérifié.',
            'Pièces justificatives reçues.',
            'Traitement validé.',
            'Demande incomplète.',
            'Refus après vérification.',
            null,
            null,
        ];

        $created = 0;
        $nbRemboursements = 50;

        for ($i = 0; $i < $nbRemboursements; $i++) {
            $paiement = $paiements->random();

            $statutRand = rand(1, 100);
            $statut = $statutRand <= 55 ? 'en_attente' : ($statutRand <= 80 ? 'approuve' : 'refuse');

            $montantPaiement = (int) ($paiement->montant ?? 0);
            if ($montantPaiement <= 0) {
                // fallback si le paiement n'a pas de montant exploitable
                $montantPaiement = rand(10000, 100000);
            }

            // Remboursement partiel dans la plupart des cas, parfois total
            $montant = (rand(1, 10) <= 2)
                ? $montantPaiement
                : rand((int) max(1000, $montantPaiement * 0.1), $montantPaiement);

            // Date de création proche du paiement, mais postérieure
            $baseDate = $paiement->date_paiement ? Carbon::parse($paiement->date_paiement) : Carbon::now()->subDays(rand(1, 60));
            $createdAt = (clone $baseDate)->addDays(rand(0, 20));

            $traitePar = null;
            $traiteLe = null;
            $commentaire = null;
            if ($statut !== 'en_attente') {
                $traitePar = $users->isNotEmpty() ? $users->random()->id : null;
                $traiteLe = (clone $createdAt)->addDays(rand(0, 5));
                $commentaire = $commentairesAdmin[array_rand($commentairesAdmin)];
            }

            // Numéro unique
            do {
                $numero = 'REM-' . strtoupper(Str::random(6));
            } while (Remboursement::where('numero', $numero)->exists());

            Remboursement::create([
                'numero' => $numero,
                'paiement_id' => $paiement->id,
                'membre_id' => $paiement->membre_id,
                'caisse_id' => $paiement->caisse_id,
                'montant' => $montant,
                'statut' => $statut,
                'raison' => $raisons[array_rand($raisons)],
                'commentaire_admin' => $commentaire,
                'traite_par' => $traitePar,
                'traite_le' => $traiteLe,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ]);

            $created++;
        }

        $this->command->info("{$created} remboursement(s) créé(s) avec succès.");
    }
}

