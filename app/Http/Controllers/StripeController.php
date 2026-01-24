<?php

namespace App\Http\Controllers;

use App\Models\PaymentMethod;
use Illuminate\Http\Request;

class StripeController extends Controller
{
    /**
     * Afficher la page de configuration Stripe
     */
    public function index()
    {
        $paymentMethod = PaymentMethod::where('code', 'stripe')->first();
        
        if (!$paymentMethod) {
            return redirect()->route('payment-methods.index')
                ->with('error', 'Stripe n\'est pas configuré. Veuillez d\'abord initialiser les moyens de paiement.');
        }
        
        $config = $paymentMethod->config ?? [];
        
        return view('stripe.index', compact('paymentMethod', 'config'));
    }

    /**
     * Mettre à jour la configuration Stripe
     */
    public function update(Request $request)
    {
        $paymentMethod = PaymentMethod::where('code', 'stripe')->first();
        
        if (!$paymentMethod) {
            return redirect()->route('payment-methods.index')
                ->with('error', 'Stripe n\'est pas configuré.');
        }

        $validated = $request->validate([
            'publishable_key' => 'nullable|string|max:255',
            'secret_key' => 'nullable|string',
            'mode' => 'required|in:test,live',
        ], [
            'mode.required' => 'Le mode est obligatoire.',
            'mode.in' => 'Le mode doit être "test" ou "live".',
        ]);

        try {
            $config = [
                'publishable_key' => $validated['publishable_key'] ?? null,
                'secret_key' => $validated['secret_key'] ?? null,
                'mode' => $validated['mode'],
            ];

            $paymentMethod->config = $config;
            // Vérifier si la checkbox enabled est cochée
            $paymentMethod->enabled = $request->has('enabled') && ($request->input('enabled') == '1' || $request->input('enabled') === true);
            $paymentMethod->save();
            
            \Log::info('Stripe: Configuration mise à jour', [
                'enabled' => $paymentMethod->enabled,
                'has_enabled' => $request->has('enabled'),
                'enabled_value' => $request->input('enabled'),
            ]);

            return redirect()->route('payment-methods.index')
                ->with('success', 'Configuration Stripe mise à jour avec succès.');
                
        } catch (\Exception $e) {
            \Log::error('Stripe: Erreur lors de la mise à jour', ['error' => $e->getMessage()]);
            return redirect()->route('stripe.index')
                ->with('error', 'Erreur lors de l\'enregistrement : ' . $e->getMessage())
                ->withInput();
        }
    }
}
