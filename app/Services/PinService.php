<?php

namespace App\Services;

use App\Models\Membre;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Service de gestion du code PIN de sécurité.
 *
 * Le PIN (4 chiffres) est demandé pour toutes les opérations critiques :
 * souscriptions tontines, demandes de nano-crédit, actions garant, etc.
 *
 * Stratégie retenue (approche A) :
 * L'app mobile envoie le PIN directement dans le corps de chaque requête critique.
 * Le champ attendu est : "pin" (string, 4 chiffres).
 */
class PinService
{
    public const PIN_LENGTH = 4;

    /**
     * Vérifie le PIN fourni dans la requête pour une opération critique.
     *
     * Usage dans un controller :
     *   $error = app(PinService::class)->requirePin($request, $membre);
     *   if ($error) return $error;
     *
     * Retourne null si le PIN est valide, ou une JsonResponse 403 explicite sinon.
     */
    public function requirePin(Request $request, Membre $membre): ?JsonResponse
    {
        // Le membre n'a pas encore de PIN — lui demander d'en créer un
        if (! $membre->hasPin()) {
            return response()->json([
                'message'          => 'Vous devez d\'abord créer votre code PIN.',
                'require_pin_setup' => true,
            ], 403);
        }

        // PIN verrouillé Suite aux tentatives échouées
        if ($membre->isPinLocked()) {
            $unlockAt = $membre->pin_locked_until->diffForHumans();
            return response()->json([
                'message'   => "Trop de tentatives incorrectes. Réessayez {$unlockAt}.",
                'pin_locked' => true,
            ], 403);
        }

        $pin = $request->input('pin');

        // Champ PIN absent
        if ($pin === null || $pin === '') {
            return response()->json([
                'message'     => 'Code PIN requis pour cette opération.',
                'require_pin' => true,
            ], 403);
        }

        // Longueur invalide
        if (! preg_match('/^\d{' . self::PIN_LENGTH . '}$/', (string) $pin)) {
            return response()->json([
                'message' => 'Le code PIN doit comporter exactement ' . self::PIN_LENGTH . ' chiffres.',
            ], 422);
        }

        // Vérification (gère tentatives + verrouillage automatiquement via Membre::verifyPin)
        if (! $membre->verifyPin((string) $pin)) {
            $membre->refresh();

            if ($membre->isPinLocked()) {
                return response()->json([
                    'message'    => 'Code PIN incorrect. Compte temporairement verrouillé pendant ' . Membre::PIN_LOCK_MINUTES . ' minutes.',
                    'pin_locked' => true,
                ], 403);
            }

            $remaining = Membre::PIN_MAX_ATTEMPTS - ($membre->pin_attempts ?? 0);
            return response()->json([
                'message'   => "Code PIN incorrect. {$remaining} tentative(s) restante(s).",
                'remaining' => $remaining,
            ], 403);
        }

        // PIN valide
        return null;
    }

    /**
     * Valide le format du PIN (4 chiffres uniquement).
     */
    public function isValidFormat(string $pin): bool
    {
        return (bool) preg_match('/^\d{' . self::PIN_LENGTH . '}$/', $pin);
    }
}
