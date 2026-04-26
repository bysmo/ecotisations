<?php

namespace App\Http\Controllers;

use App\Models\MembreWalletAlias;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MembreWalletAliasController extends Controller
{
    /**
     * Liste des alias du membre
     */
    public function index()
    {
        $membre = Auth::guard('membre')->user();
        $aliases = $membre->walletAliases()->orderBy('is_default', 'desc')->get();
        
        return view('membres.wallets.index', compact('aliases'));
    }

    /**
     * Enregistrer un nouvel alias
     */
    public function store(Request $request)
    {
        $request->validate([
            'alias' => 'required|uuid|unique:membre_wallet_aliases,alias',
            'label' => 'nullable|string|max:50',
        ]);

        $membre = Auth::guard('membre')->user();
        
        // Si c'est le premier, on le met par défaut
        $isFirst = $membre->walletAliases()->count() === 0;

        $membre->walletAliases()->create([
            'alias' => $request->alias,
            'label' => $request->label ?? 'Portefeuille Principal',
            'is_default' => $isFirst,
        ]);

        return back()->with('success', 'Alias de portefeuille ajouté avec succès.');
    }

    /**
     * Définir comme alias par défaut
     */
    public function setDefault(MembreWalletAlias $alias)
    {
        $membre = Auth::guard('membre')->user();
        
        if ($alias->membre_id !== $membre->id) {
            abort(403);
        }

        $membre->walletAliases()->update(['is_default' => false]);
        $alias->update(['is_default' => true]);

        return back()->with('success', 'Alias par défaut mis à jour.');
    }

    /**
     * Mettre à jour le libellé d'un alias
     */
    public function update(Request $request, MembreWalletAlias $alias)
    {
        $membre = Auth::guard('membre')->user();
        
        if ($alias->membre_id !== $membre->id) {
            abort(403);
        }

        $request->validate([
            'label' => 'required|string|max:50',
        ]);

        $alias->update([
            'label' => $request->label,
        ]);

        return back()->with('success', 'Alias mis à jour.');
    }

    /**
     * Supprimer un alias
     */
    public function destroy(MembreWalletAlias $alias)
    {
        $membre = Auth::guard('membre')->user();
        
        if ($alias->membre_id !== $membre->id) {
            abort(403);
        }

        $alias->delete();

        return back()->with('success', 'Alias supprimé.');
    }
}
