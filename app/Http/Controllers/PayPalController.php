<?php

namespace App\Http\Controllers;

use App\Models\PaymentMethod;
use Illuminate\Http\Request;

class PayPalController extends Controller
{
    /**
     * Afficher la page de configuration PayPal
     */
    public function index()
    {
        $paymentMethod = PaymentMethod::where('code', 'paypal')->first();
        
        if (!$paymentMethod) {
            return redirect()->route('payment-methods.index')
                ->with('error', 'PayPal n\'est pas configuré. Veuillez d\'abord initialiser les moyens de paiement.');
        }
        
        $config = $paymentMethod->config ?? [];
        
        return view('paypal.index', compact('paymentMethod', 'config'));
    }

    /**
     * Mettre à jour la configuration PayPal
     */
    public function update(Request $request)
    {
        $paymentMethod = PaymentMethod::where('code', 'paypal')->first();
        
        if (!$paymentMethod) {
            return redirect()->route('payment-methods.index')
                ->with('error', 'PayPal n\'est pas configuré.');
        }

        $validated = $request->validate([
            'client_id' => 'nullable|string|max:255',
            'client_secret' => 'nullable|string',
            'mode' => 'required|in:sandbox,live',
        ], [
            'mode.required' => 'Le mode est obligatoire.',
            'mode.in' => 'Le mode doit être "sandbox" ou "live".',
        ]);

        try {
            $config = [
                'client_id' => $validated['client_id'] ?? null,
                'client_secret' => $validated['client_secret'] ?? null,
                'mode' => $validated['mode'],
            ];

            $paymentMethod->config = $config;
            // Vérifier si la checkbox enabled est cochée
            $paymentMethod->enabled = $request->has('enabled') && ($request->input('enabled') == '1' || $request->input('enabled') === true);
            $paymentMethod->save();
            
            \Log::info('PayPal: Configuration mise à jour', [
                'enabled' => $paymentMethod->enabled,
                'has_enabled' => $request->has('enabled'),
                'enabled_value' => $request->input('enabled'),
            ]);

            return redirect()->route('payment-methods.index')
                ->with('success', 'Configuration PayPal mise à jour avec succès.');
                
        } catch (\Exception $e) {
            \Log::error('PayPal: Erreur lors de la mise à jour', ['error' => $e->getMessage()]);
            return redirect()->route('paypal.index')
                ->with('error', 'Erreur lors de l\'enregistrement : ' . $e->getMessage())
                ->withInput();
        }
    }
}
