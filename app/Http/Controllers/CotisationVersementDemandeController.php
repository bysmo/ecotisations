<?php

namespace App\Http\Controllers;

use App\Models\CotisationVersementDemande;
use Illuminate\Http\Request;

class CotisationVersementDemandeController extends Controller
{
    /**
     * Liste des demandes de versement des fonds (cotisations créées par des membres)
     */
    public function index(Request $request)
    {
        $query = CotisationVersementDemande::with(['cotisation', 'caisse', 'demandeParMembre'])
            ->orderBy('created_at', 'desc');

        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        $demandes = $query->paginate(15);

        return view('cotisation-versement-demandes.index', compact('demandes'));
    }
}
