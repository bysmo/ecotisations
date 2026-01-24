<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Route;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;
use App\Models\AppSetting;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Schema::defaultStringLength(191);
        
        // Si l'application n'est pas installée, forcer le driver de session en 'file'
        // car la table 'sessions' n'existe pas encore en base de données
        if (!file_exists(storage_path('installed'))) {
            config(['session.driver' => 'file']);
        }
        
        // Utiliser Bootstrap 5 pour la pagination
        Paginator::useBootstrapFive();
        
        // Spécifier le nom du paramètre pour les routes caisses
        Route::bind('caiss', function ($value) {
            return \App\Models\Caisse::findOrFail($value);
        });
        
        // Partager les paramètres de l'application avec toutes les vues
        View::composer('layouts.app', function ($view) {
            $appNom = AppSetting::get('app_nom', 'Gestion Cotisations');
            $appDescription = AppSetting::get('app_description', 'Application de gestion des cotisations');
            
            $view->with([
                'appNom' => $appNom,
                'appDescription' => $appDescription,
            ]);
        });
    }
}
