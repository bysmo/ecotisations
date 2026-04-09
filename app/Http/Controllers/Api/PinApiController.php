<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Membre;
use App\Services\PinService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Endpoints de gestion du code PIN de sécurité (4 chiffres).
 *
 * GET  /api/membre/pin/status   → statut du PIN (défini, verrouillé, tentatives)
 * POST /api/membre/pin/setup    → créer le PIN (1ère fois, après validation OTP)
 * POST /api/membre/pin/change   → modifier le PIN (requiert l'ancien PIN)
 * POST /api/membre/pin/verify   → vérifier le PIN (réponse simple OK/KO)
 */
class PinApiController extends Controller
{
    public function __construct(protected PinService $pinService) {}

    /**
     * Statut du PIN du membre connecté.
     */
    public function status(Request $request): JsonResponse
    {
        /** @var Membre $membre */
        $membre = $request->user();

        return response()->json([
            'has_pin'         => $membre->hasPin(),
            'pin_locked'      => $membre->isPinLocked(),
            'pin_locked_until' => $membre->isPinLocked()
                ? $membre->pin_locked_until?->toIso8601String()
                : null,
            'pin_attempts'    => $membre->pin_attempts ?? 0,
            'max_attempts'    => Membre::PIN_MAX_ATTEMPTS,
            'lock_minutes'    => Membre::PIN_LOCK_MINUTES,
        ]);
    }

    /**
     * Créer le code PIN pour la première fois.
     * Appelé juste après la validation de l'OTP si le membre n'a pas encore de PIN.
     */
    public function setup(Request $request): JsonResponse
    {
        /** @var Membre $membre */
        $membre = $request->user();

        if ($membre->hasPin()) {
            return response()->json([
                'message' => 'Vous avez déjà un code PIN. Utilisez l\'endpoint /pin/change pour le modifier.',
            ], 422);
        }

        $request->validate([
            'pin'              => 'required|string|size:' . PinService::PIN_LENGTH . '|regex:/^\d+$/',
            'pin_confirmation' => 'required|string|same:pin',
        ], [
            'pin.required'              => 'Le code PIN est obligatoire.',
            'pin.size'                  => 'Le code PIN doit comporter exactement ' . PinService::PIN_LENGTH . ' chiffres.',
            'pin.regex'                 => 'Le code PIN ne doit contenir que des chiffres.',
            'pin_confirmation.required' => 'La confirmation du PIN est obligatoire.',
            'pin_confirmation.same'     => 'Les deux codes PIN ne correspondent pas.',
        ]);

        $membre->setPin($request->input('pin'));

        return response()->json([
            'message'  => 'Code PIN créé avec succès.',
            'has_pin'  => true,
        ]);
    }

    /**
     * Modifier le code PIN (l'ancien PIN est requis pour confirmation).
     */
    public function change(Request $request): JsonResponse
    {
        /** @var Membre $membre */
        $membre = $request->user();

        if (! $membre->hasPin()) {
            return response()->json([
                'message'           => 'Vous n\'avez pas encore de code PIN. Utilisez /pin/setup.',
                'require_pin_setup' => true,
            ], 422);
        }

        if ($membre->isPinLocked()) {
            return response()->json([
                'message'    => 'Compte PIN temporairement verrouillé. Réessayez plus tard.',
                'pin_locked' => true,
            ], 403);
        }

        $request->validate([
            'old_pin'          => 'required|string|size:' . PinService::PIN_LENGTH . '|regex:/^\d+$/',
            'pin'              => 'required|string|size:' . PinService::PIN_LENGTH . '|regex:/^\d+$/|different:old_pin',
            'pin_confirmation' => 'required|string|same:pin',
        ], [
            'old_pin.required'          => 'L\'ancien code PIN est obligatoire.',
            'pin.required'              => 'Le nouveau code PIN est obligatoire.',
            'pin.size'                  => 'Le code PIN doit comporter exactement ' . PinService::PIN_LENGTH . ' chiffres.',
            'pin.regex'                 => 'Le code PIN ne doit contenir que des chiffres.',
            'pin.different'             => 'Le nouveau PIN doit être différent de l\'ancien.',
            'pin_confirmation.required' => 'La confirmation du PIN est obligatoire.',
            'pin_confirmation.same'     => 'Les deux nouveaux codes PIN ne correspondent pas.',
        ]);

        // Vérifier l'ancien PIN
        if (! $membre->verifyPin($request->input('old_pin'))) {
            $membre->refresh();

            if ($membre->isPinLocked()) {
                return response()->json([
                    'message'    => 'Trop de tentatives. Compte PIN verrouillé pendant ' . Membre::PIN_LOCK_MINUTES . ' minutes.',
                    'pin_locked' => true,
                ], 403);
            }

            $remaining = Membre::PIN_MAX_ATTEMPTS - ($membre->pin_attempts ?? 0);
            return response()->json([
                'message'   => "Ancien code PIN incorrect. {$remaining} tentative(s) restante(s).",
                'remaining' => $remaining,
            ], 422);
        }

        $membre->setPin($request->input('pin'));

        return response()->json([
            'message' => 'Code PIN modifié avec succès.',
        ]);
    }

    /**
     * Vérifier le PIN (endpoint utilitaire pour l'app mobile).
     * Ne réalise aucune action critique — sert uniquement à valider le PIN
     * avant d'afficher des informations sensibles ou de débloquer l'UI.
     */
    public function verify(Request $request): JsonResponse
    {
        /** @var Membre $membre */
        $membre = $request->user();

        if (! $membre->hasPin()) {
            return response()->json([
                'message'           => 'Vous n\'avez pas encore de code PIN.',
                'require_pin_setup' => true,
            ], 403);
        }

        if ($membre->isPinLocked()) {
            return response()->json([
                'message'    => 'Compte PIN temporairement verrouillé. Réessayez plus tard.',
                'pin_locked' => true,
            ], 403);
        }

        $request->validate([
            'pin' => 'required|string|size:' . PinService::PIN_LENGTH . '|regex:/^\d+$/',
        ]);

        if (! $membre->verifyPin($request->input('pin'))) {
            $membre->refresh();

            if ($membre->isPinLocked()) {
                return response()->json([
                    'message'    => 'Code PIN incorrect. Compte verrouillé pendant ' . Membre::PIN_LOCK_MINUTES . ' minutes.',
                    'pin_locked' => true,
                    'valid'      => false,
                ], 403);
            }

            $remaining = Membre::PIN_MAX_ATTEMPTS - ($membre->pin_attempts ?? 0);
            return response()->json([
                'message'   => "Code PIN incorrect. {$remaining} tentative(s) restante(s).",
                'valid'     => false,
                'remaining' => $remaining,
            ], 422);
        }

        return response()->json([
            'message' => 'Code PIN vérifié avec succès.',
            'valid'   => true,
        ]);
    }
}
