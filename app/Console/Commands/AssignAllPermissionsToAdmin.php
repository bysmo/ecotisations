<?php

namespace App\Console\Commands;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Console\Command;

class AssignAllPermissionsToAdmin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:assign-all-permissions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Attribue toutes les permissions au rôle administrateur';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Créer ou récupérer le rôle admin
        $adminRole = Role::updateOrCreate(
            ['slug' => 'admin'],
            [
                'nom' => 'Administrateur',
                'description' => 'Accès complet à toutes les fonctionnalités',
                'actif' => true,
            ]
        );
        
        $this->info("✓ Rôle administrateur trouvé/créé (ID: {$adminRole->id})");
        
        // Récupérer toutes les permissions
        $allPermissions = Permission::all();
        
        if ($allPermissions->isEmpty()) {
            $this->error('Aucune permission trouvée dans la base de données !');
            $this->info('Exécutez d\'abord: php artisan db:seed --class=PermissionSeeder');
            return 1;
        }
        
        $this->info("✓ {$allPermissions->count()} permission(s) trouvée(s) dans la base de données");
        
        // Supprimer toutes les permissions existantes du rôle admin
        $adminRole->permissions()->detach();
        
        // Attribuer toutes les permissions au rôle admin
        $adminRole->permissions()->sync($allPermissions->pluck('id'));
        
        $this->info("✓ Toutes les permissions ({$allPermissions->count()}) ont été attribuées au rôle administrateur.");
        
        // Vérifier
        $adminPermissionsCount = $adminRole->permissions()->count();
        $this->info("✓ Le rôle administrateur a maintenant {$adminPermissionsCount} permission(s).");
        
        // S'assurer que l'utilisateur admin@ecotisations.com a le rôle admin
        $defaultAdmin = \App\Models\User::where('email', 'admin@ecotisations.com')->first();
        if ($defaultAdmin) {
            if (!$defaultAdmin->roles()->where('slug', 'admin')->exists()) {
                $defaultAdmin->roles()->attach($adminRole->id);
                $this->info("✓ Rôle administrateur attribué à l'utilisateur admin@ecotisations.com");
            } else {
                $this->info("✓ L'utilisateur admin@ecotisations.com a déjà le rôle administrateur");
            }
        }
        
        return 0;
    }
}
