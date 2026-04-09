<?php

namespace App\Http\Middleware;

use App\Models\Membre;
use App\Services\PinService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class VerifyMembrePinWeb
{
    public function __construct(protected PinService $pinService) {}

    /**
     * Handle an incoming request.
     * Intercepte les actions Web critiques et redirige vers la saisie du PIN si nécessaire.
     */
    public function handle(Request $request, Closure $next)
    {
        /** @var Membre|null $membre */
        $membre = $request->user('membre');

        if (!$membre || !$membre->isPinEnabled()) {
            return $next($request);
        }

        // Si le PIN est exigé, mais que la requête courante a déjà été validée juste avant (via la session flashée)
        // on laisse passer ! (Mécanisme du auto-submit)
        if (Session::has('pin_validated_for_request') && Session::get('pin_validated_for_request') === true) {
            // On peut optionnellement vider la clé, mais elle expirera d'elle-même (flash)
            return $next($request);
        }

        $needsPin = false;

        if ($membre->isPinModeSession()) {
            // Mode B : Vérifier s'il y a une session active. Si oui, on la prolonge et on continue
            if ($this->pinService->hasActiveSession($membre->id)) {
                // Prolongement pris en charge par le PinService ? En théorie on peut juste raviver la session
                $this->pinService->openSession($membre->id); 
                return $next($request);
            }
            $needsPin = true;
        } else {
            // Mode A (each_time) : l'utilisateur doit toujours saisir le PIN en intercept
            $needsPin = true;
        }

        if ($needsPin) {
            // 1. Sauvegarder la requête en cours (URL, POST body, METHOD)
            Session::put('pin_intended_action', [
                'url'    => $request->fullUrl(),
                'method' => $request->method(),
                // On exclut les tokens laravel internes et pin si jamais il traîne
                'data'   => $request->except(['_token', '_method', 'pin']),
            ]);

            // 2. Rediriger l'utilisateur vers l'écran bloquant
            return redirect()->route('membre.pin.verify-action')->with('warning', 'Opération protégée. Veuillez saisir votre code PIN.');
        }

        return $next($request);
    }
}
