<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        $user = auth()->user();
        
        if (!$user) {
            return redirect()->route('admin.login');
        }
        
        // Les administrateurs ont accès à tout
        if ($user->hasRole('admin')) {
            return $next($request);
        }
        
        // Vérifier si l'utilisateur a la permission
        if (!$user->hasPermission($permission)) {
            abort(403, 'Accès refusé. Vous n\'avez pas la permission nécessaire.');
        }
        
        return $next($request);
    }
}
