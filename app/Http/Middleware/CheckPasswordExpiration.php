<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use App\Models\AppSetting;
use Carbon\Carbon;

class CheckPasswordExpiration
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Ne pas interrompre le processus si on est déjà sur la route de redéfinition ou déconnexion
        if ($request->is('password/expire') || $request->is('password/expire/update') || $request->is('logout') || str_contains($request->route()?->getName(), 'logout')) {
            return $next($request);
        }

        $user = Auth::guard('web')->user() ?? Auth::guard('membre')->user();

        if ($user) {
            $lifetimeDays = AppSetting::get('pwd_lifetime_days', 90);
            $changedAt = $user->password_changed_at ? Carbon::parse($user->password_changed_at) : null;

            if (!$changedAt || now()->diffInDays($changedAt) >= $lifetimeDays) {
                // Determine which prefix to use based on guard (admin vs membre)
                $prefix = Auth::guard('membre')->check() ? 'membre.' : 'admin.';
                
                // On passe l'identifiant du guard dans la session pour savoir vers où revenir
                session()->put('password_expire_guard', Auth::guard('membre')->check() ? 'membre' : 'web');
                
                return redirect()->route('password.expire')->with('warning', 'Votre mot de passe a expiré. Vous devez le changer pour continuer.');
            }
        }

        return $next($request);
    }
}
