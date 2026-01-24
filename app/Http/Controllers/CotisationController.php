<?php

namespace App\Http\Controllers;

use App\Models\Cotisation;
use App\Models\Caisse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CotisationController extends Controller
{
    /**
     * Afficher la liste des cotisations (templates)
     */
    public function index(Request $request)
    {
        $query = Cotisation::with('caisse');
        
        // Recherche
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('numero', 'like', "%{$search}%")
                  ->orWhere('nom', 'like', "%{$search}%")
                  ->orWhere('type', 'like', "%{$search}%")
                  ->orWhereHas('caisse', function($q) use ($search) {
                      $q->where('nom', 'like', "%{$search}%");
                  });
            });
        }
        
        // Filtre par statut actif
        if ($request->filled('actif')) {
            $query->where('actif', $request->actif === '1');
        }
        
        $perPage = \App\Models\AppSetting::get('pagination_par_page', 15);
        $cotisations = $query->orderBy('created_at', 'desc')->paginate($perPage);
        
        return view('cotisations.index', compact('cotisations'));
    }

    /**
     * Afficher le formulaire de création
     */
    public function create()
    {
        $caisses = Caisse::where('statut', 'active')->orderBy('nom')->get();
        
        // Récupérer tous les tags depuis la table tags
        $tags = \App\Models\Tag::where('type', 'cotisation')
            ->orderBy('nom')
            ->pluck('nom')
            ->toArray();
        
        // Récupérer tous les segments uniques existants
        $segments = \App\Models\Membre::select('segment')
            ->whereNotNull('segment')
            ->where('segment', '!=', '')
            ->distinct()
            ->orderBy('segment')
            ->pluck('segment')
            ->toArray();
        
        return view('cotisations.create', compact('caisses', 'tags', 'segments'));
    }

    /**
     * Générer un numéro de cotisation unique
     */
    private function generateNumeroCotisation(): string
    {
        do {
            $numero = 'COT-' . strtoupper(Str::random(8));
        } while (Cotisation::where('numero', $numero)->exists());

        return $numero;
    }

    /**
     * Enregistrer une nouvelle cotisation (template)
     */
    public function store(Request $request)
    {
        $rules = [
            'nom' => 'required|string|max:255',
            'caisse_id' => 'required|exists:caisses,id',
            'type' => 'required|string|in:reguliere,ponctuelle,exceptionnelle',
            'frequence' => 'required|in:mensuelle,trimestrielle,semestrielle,annuelle,unique',
            'type_montant' => 'required|in:libre,fixe',
            'description' => 'nullable|string',
            'notes' => 'nullable|string',
            'tag' => 'nullable|string|max:255',
            'nouveau_tag' => 'nullable|string|max:255|required_if:tag,__nouveau__',
            'segment' => 'nullable|string|max:255',
            'actif' => 'boolean',
        ];

        // Si le type de montant est fixe, le montant est requis
        if ($request->type_montant === 'fixe') {
            $rules['montant'] = 'required|numeric|min:1';
        } else {
            $rules['montant'] = 'nullable|numeric|min:0';
        }

        $validated = $request->validate($rules);

        // Si un nouveau tag est fourni, l'utiliser
        if ($request->tag === '__nouveau__' && $request->filled('nouveau_tag')) {
            $validated['tag'] = trim($request->nouveau_tag);
        } elseif ($request->tag === '__nouveau__') {
            $validated['tag'] = null;
        }
        
        unset($validated['nouveau_tag']);

        // Générer un numéro de cotisation unique
        $validated['numero'] = $this->generateNumeroCotisation();
        $validated['actif'] = $request->has('actif');
        
        // Si le montant est libre, mettre null
        if ($validated['type_montant'] === 'libre') {
            $validated['montant'] = null;
        }

        $cotisation = Cotisation::create($validated);

        // Envoyer des notifications aux membres concernés par le segment
        if ($cotisation->actif && $cotisation->segment) {
            // Récupérer tous les membres ayant ce segment
            $membres = \App\Models\Membre::where('segment', $cotisation->segment)->get();
            
            foreach ($membres as $membre) {
                $membre->notify(new \App\Notifications\NewCotisationNotification($cotisation));
            }
            
            \Log::info('Notifications de nouvelle cotisation envoyées', [
                'cotisation_id' => $cotisation->id,
                'segment' => $cotisation->segment,
                'nombre_membres' => $membres->count(),
            ]);
        } elseif ($cotisation->actif && (!$cotisation->segment || $cotisation->segment === '')) {
            // Si pas de segment, envoyer à tous les membres
            $membres = \App\Models\Membre::all();
            
            foreach ($membres as $membre) {
                $membre->notify(new \App\Notifications\NewCotisationNotification($cotisation));
            }
            
            \Log::info('Notifications de nouvelle cotisation envoyées à tous les membres', [
                'cotisation_id' => $cotisation->id,
                'nombre_membres' => $membres->count(),
            ]);
        }

        return redirect()->route('cotisations.index')
            ->with('success', 'Cotisation créée avec succès.');
    }

    /**
     * Afficher les détails d'une cotisation
     */
    public function show(Cotisation $cotisation)
    {
        $cotisation->load(['caisse', 'paiements.membre']);
        
        return view('cotisations.show', compact('cotisation'));
    }

    /**
     * Afficher le formulaire d'édition
     */
    public function edit(Cotisation $cotisation)
    {
        $caisses = Caisse::where('statut', 'active')->orderBy('nom')->get();
        
        // Récupérer tous les tags depuis la table tags
        $tags = \App\Models\Tag::where('type', 'cotisation')
            ->orderBy('nom')
            ->pluck('nom')
            ->toArray();
        
        // Récupérer tous les segments uniques existants
        $segments = \App\Models\Membre::select('segment')
            ->whereNotNull('segment')
            ->where('segment', '!=', '')
            ->distinct()
            ->orderBy('segment')
            ->pluck('segment')
            ->toArray();
        
        return view('cotisations.edit', compact('cotisation', 'caisses', 'tags', 'segments'));
    }

    /**
     * Mettre à jour une cotisation
     */
    public function update(Request $request, Cotisation $cotisation)
    {
        $rules = [
            'nom' => 'required|string|max:255',
            'caisse_id' => 'required|exists:caisses,id',
            'type' => 'required|string|in:reguliere,ponctuelle,exceptionnelle',
            'frequence' => 'required|in:mensuelle,trimestrielle,semestrielle,annuelle,unique',
            'type_montant' => 'required|in:libre,fixe',
            'description' => 'nullable|string',
            'notes' => 'nullable|string',
            'tag' => 'nullable|string|max:255',
            'nouveau_tag' => 'nullable|string|max:255|required_if:tag,__nouveau__',
            'segment' => 'nullable|string|max:255',
            'actif' => 'boolean',
        ];

        // Si le type de montant est fixe, le montant est requis
        if ($request->type_montant === 'fixe') {
            $rules['montant'] = 'required|numeric|min:1';
        } else {
            $rules['montant'] = 'nullable|numeric|min:0';
        }

        $validated = $request->validate($rules);

        $validated['actif'] = $request->has('actif');
        
        // Si le montant est libre, mettre null
        if ($validated['type_montant'] === 'libre') {
            $validated['montant'] = null;
        }

        $cotisation->update($validated);

        return redirect()->route('cotisations.index')
            ->with('success', 'Cotisation mise à jour avec succès.');
    }

    /**
     * Supprimer une cotisation
     */
    public function destroy(Cotisation $cotisation)
    {
        // Vérifier s'il y a des paiements associés
        if ($cotisation->paiements()->count() > 0) {
            return redirect()->route('cotisations.index')
                ->with('error', 'Impossible de supprimer cette cotisation car elle a des paiements associés.');
        }

        $cotisation->delete();

        return redirect()->route('cotisations.index')
            ->with('success', 'Cotisation supprimée avec succès.');
    }
}
