<?php

namespace App\Http\Controllers;

use App\Models\CotisationAdhesion;
use Illuminate\Http\Request;

class CotisationAdhesionController extends Controller
{
    /**
     * Liste des demandes d'adhésion en attente (uniquement pour cotisations créées par l'admin app).
     * Les cotisations créées par des membres : l'admin de la cotisation (créateur) gère ses demandes.
     */
    public function index(Request $request)
    {
        $query = CotisationAdhesion::with(['membre', 'cotisation'])
            ->where('statut', 'en_attente')
            ->whereHas('cotisation', fn($q) => $q->whereNull('created_by_membre_id'));

        if ($request->filled('cotisation_id')) {
            $query->where('cotisation_id', $request->cotisation_id);
        }

        $adhesions = $query->orderBy('created_at', 'desc')->paginate(15);
        return view('cotisation-adhesions.index', compact('adhesions'));
    }

    /**
     * Accepter une demande d'adhésion
     */
    public function accepter(CotisationAdhesion $adhesion)
    {
        if ($adhesion->statut !== 'en_attente') {
            return redirect()->back()->with('error', 'Cette demande a déjà été traitée.');
        }

        $adhesion->update([
            'statut' => 'accepte',
            'traite_par' => auth()->id(),
            'traite_le' => now(),
        ]);

        return redirect()->back()
            ->with('success', 'La demande d\'adhésion de ' . $adhesion->membre->nom_complet . ' a été acceptée.');
    }

    /**
     * Refuser une demande d'adhésion
     */
    public function refuser(Request $request, CotisationAdhesion $adhesion)
    {
        if ($adhesion->statut !== 'en_attente') {
            return redirect()->back()->with('error', 'Cette demande a déjà été traitée.');
        }

        $adhesion->update([
            'statut' => 'refuse',
            'traite_par' => auth()->id(),
            'traite_le' => now(),
            'commentaire_admin' => $request->commentaire,
        ]);

        return redirect()->back()
            ->with('success', 'La demande d\'adhésion a été refusée.');
    }
}
