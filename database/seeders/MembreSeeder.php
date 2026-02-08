<?php

namespace Database\Seeders;

use App\Models\Membre;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Carbon\Carbon;

class MembreSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Création des membres...');

        $prenoms = ['Jean', 'Marie', 'Pierre', 'Sophie', 'Paul', 'Julie', 'Luc', 'Anne', 'Marc', 'Céline', 'Thomas', 'Isabelle', 'David', 'Nathalie', 'Laurent', 'Sandrine', 'Nicolas', 'Caroline', 'Olivier', 'Emilie'];
        $noms = ['Dupont', 'Martin', 'Bernard', 'Dubois', 'Thomas', 'Robert', 'Richard', 'Petit', 'Durand', 'Leroy', 'Moreau', 'Simon', 'Laurent', 'Lefebvre', 'Michel', 'Garcia', 'David', 'Bertrand', 'Roux', 'Vincent'];
        $domaines = ['gmail.com', 'yahoo.fr', 'hotmail.com', 'outlook.com', 'example.com'];
        $villes = ['Paris', 'Lyon', 'Marseille', 'Toulouse', 'Nice', 'Nantes', 'Strasbourg', 'Montpellier', 'Bordeaux', 'Lille'];
        $rues = ['Rue de la Paix', 'Avenue des Champs', 'Boulevard Saint-Michel', 'Rue Victor Hugo', 'Avenue de la République', 'Rue du Commerce', 'Boulevard Voltaire', 'Rue Pasteur', 'Avenue Foch', 'Rue de France'];
        
        // Calculer le hash du mot de passe UNE SEULE FOIS (bcrypt est très lent)
        $passwordHash = bcrypt('password');
        
        $created = 0;
        $nbMembres = 50; // Générer 50 membres
        
        // Préparer les données en batch pour insertion plus rapide
        $membresData = [];

        for ($i = 0; $i < $nbMembres; $i++) {
            $prenom = $prenoms[array_rand($prenoms)];
            $nom = $noms[array_rand($noms)];
            $email = strtolower($prenom . '.' . $nom . rand(1, 999) . '@' . $domaines[array_rand($domaines)]);
            $telephone = '06' . rand(10000000, 99999999);
            $adresse = rand(1, 200) . ' ' . $rues[array_rand($rues)] . ', ' . $villes[array_rand($villes)];
            $statut = (rand(1, 10) <= 8) ? 'actif' : 'inactif'; // 80% actifs
            $dateAdhesion = Carbon::now()->subDays(rand(1, 365));
            
            // Les segments seront assignés par SegmentMembreSeeder
            $segment = null;
            
            // Générer un numéro unique (vérifier seulement dans le tableau en cours)
            do {
                $numero = 'MEM-' . strtoupper(Str::random(6));
            } while (in_array($numero, array_column($membresData, 'numero')));
            
            $membresData[] = [
                'numero' => $numero,
                'nom' => $nom,
                'prenom' => $prenom,
                'email' => $email,
                'telephone' => $telephone,
                'adresse' => $adresse,
                'date_adhesion' => $dateAdhesion,
                'statut' => $statut,
                'segment' => $segment,
                'password' => $passwordHash, // Utiliser le hash pré-calculé
                'created_at' => $dateAdhesion,
                'updated_at' => $dateAdhesion,
            ];
        }
        
        // Insertion en batch (beaucoup plus rapide)
        foreach (array_chunk($membresData, 10) as $chunk) {
            Membre::insert($chunk);
            $created += count($chunk);
            $this->command->info("Créé {$created}/{$nbMembres} membres...");
        }
        
        $this->command->info("{$created} membre(s) créé(s) avec succès.");
        $this->command->info("Les segments seront assignés par SegmentMembreSeeder.");
    }
}
