<?php

namespace App\Console\Commands;

use App\Models\Membre;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class ResetMembrePasswords extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'membres:reset-passwords {--password=password : Le mot de passe à définir pour tous les membres}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Réinitialise les mots de passe de tous les membres avec un mot de passe par défaut';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $password = $this->option('password');
        $hashedPassword = Hash::make($password);
        
        $membres = Membre::all();
        $count = 0;
        
        foreach ($membres as $membre) {
            $membre->password = $hashedPassword;
            $membre->save();
            $count++;
        }
        
        $this->info("✓ {$count} membre(s) mis à jour avec le mot de passe: {$password}");
        $this->warn("⚠ ATTENTION: Tous les membres ont maintenant le même mot de passe par défaut !");
        $this->info("   Demandez-leur de changer leur mot de passe après la première connexion.");
        
        return 0;
    }
}
