<?php

namespace App\Http\Controllers;

use App\Models\Caisse;
use App\Models\MouvementCaisse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MembreCaisseController extends Controller
{
    /**
     * Liste des comptes du membre avec solde global.
     */
    public function index()
    {
        $membre = Auth::guard('membre')->user();
        $comptes = $membre->comptes()->get();
        $soldeGlobal = $membre->solde_global;
        $nbComptes = $comptes->count();

        return view('membres.comptes.index', compact('membre', 'comptes', 'soldeGlobal', 'nbComptes'));
    }

    /**
     * Détails d'un compte et historique des mouvements.
     */
    public function show($id)
    {
        $membre = Auth::guard('membre')->user();
        $compte = $membre->comptes()->findOrFail($id);
        
        // Historique des mouvements avec pagination
        $mouvements = $compte->mouvements()
            ->orderBy('date_operation', 'desc')
            ->paginate(15);
            
        return view('membres.comptes.show', compact('membre', 'compte', 'mouvements'));
    }
}
