<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Vérifier l'installation (doit être en premier, avant le middleware de session)
        $middleware->prependToGroup('web', \App\Http\Middleware\CheckInstallation::class);
        
        // Configurer la redirection pour les utilisateurs non authentifiés vers admin.login
        $middleware->redirectGuestsTo(function () {
            // Si l'application n'est pas installée, rediriger vers l'installation
            if (!file_exists(storage_path('installed'))) {
                return route('install.index');
            }
            return route('admin.login');
        });
        
        // Enregistrer le middleware de permissions
        $middleware->alias([
            'permission' => \App\Http\Middleware\CheckPermission::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
