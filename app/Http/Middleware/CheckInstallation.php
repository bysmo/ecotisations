<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckInstallation
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Vérifier si l'application est installée
        $installed = file_exists(storage_path('installed'));
        
        // Si l'application n'est pas installée, utiliser le driver 'file' pour les sessions
        // car la table 'sessions' n'existe pas encore en base de données
        if (!$installed) {
            config(['session.driver' => 'file']);
        }
        
        // Si l'application n'est pas installée, rediriger vers l'installation (sauf pour les routes d'installation elles-mêmes)
        if (!$installed && !$request->is('install*') && !$request->is('admin/login') && !$request->is('membre/login') && !$request->is('/')) {
            return redirect()->route('install.index');
        }
        
        // Rediriger vers la page de login si l'application est installée et que l'utilisateur essaie d'accéder à l'installation
        if ($installed && $request->is('install*')) {
            return redirect()->route('admin.login')->with('info', 'L\'application est déjà installée.');
        }
        
        return $next($request);
    }
}