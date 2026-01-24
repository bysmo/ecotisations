<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permission;
use App\Models\User;
use Illuminate\Support\Str;

class AssignPermissionsToAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
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
        
        $this->command->info("✓ Rôle administrateur trouvé/créé (ID: {$adminRole->id})");
        
        // Récupérer toutes les permissions
        $allPermissions = Permission::all();
        
        if ($allPermissions->isEmpty()) {
            $this->command->error('Aucune permission trouvée dans la base de données !');
            $this->command->info('Exécutez d\'abord: php artisan db:seed --class=PermissionSeeder');
            return;
        }
        
        $this->command->info("✓ {$allPermissions->count()} permission(s) trouvée(s) dans la base de données");
        
        // Supprimer toutes les permissions existantes du rôle admin
        $adminRole->permissions()->detach();
        
        // Attribuer toutes les permissions au rôle admin
        $adminRole->permissions()->sync($allPermissions->pluck('id'));
        
        $this->command->info("✓ Toutes les permissions ({$allPermissions->count()}) ont été attribuées au rôle administrateur.");
        
        // Vérifier
        $adminPermissionsCount = $adminRole->permissions()->count();
        $this->command->info("✓ Le rôle administrateur a maintenant {$adminPermissionsCount} permission(s).");
        
        // S'assurer que tous les utilisateurs admin ont le rôle admin
        $adminUsers = User::whereHas('roles', function($query) {
            $query->where('slug', 'admin');
        })->get();
        
        $this->command->info("✓ {$adminUsers->count()} utilisateur(s) avec le rôle administrateur trouvé(s)");
        
        // S'assurer que l'utilisateur admin@ecotisations.com a le rôle admin
        $defaultAdmin = User::where('email', 'admin@ecotisations.com')->first();
        if ($defaultAdmin) {
            if (!$defaultAdmin->roles()->where('slug', 'admin')->exists()) {
                $defaultAdmin->roles()->attach($adminRole->id);
                $this->command->info("✓ Rôle administrateur attribué à l'utilisateur admin@ecotisations.com");
            } else {
                $this->command->info("✓ L'utilisateur admin@ecotisations.com a déjà le rôle administrateur");
            }
        }
    }
}
