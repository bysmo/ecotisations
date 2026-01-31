<?php

namespace App\Http\Controllers;

use App\Models\Membre;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class MembreController extends Controller
{
    /**
     * Afficher la liste des membres
     */
    public function index(Request $request)
    {
        $query = Membre::query();
        
        // Recherche par nom, prénom, email ou numéro
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nom', 'like', "%{$search}%")
                  ->orWhere('prenom', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('numero', 'like', "%{$search}%");
            });
        }
        
        $perPage = \App\Models\AppSetting::get('pagination_par_page', 15);
        $membres = $query->orderBy('created_at', 'desc')->paginate($perPage);
        
        return view('membres.index', compact('membres'));
    }

    /**
     * Afficher le formulaire de création
     */
    public function create()
    {
        // Récupérer tous les segments uniques existants
        $segments = Membre::select('segment')
            ->whereNotNull('segment')
            ->where('segment', '!=', '')
            ->distinct()
            ->orderBy('segment')
            ->pluck('segment')
            ->toArray();
        
        return view('membres.create', compact('segments'));
    }

    /**
     * Générer un numéro de membre unique
     */
    private function generateNumeroMembre(): string
    {
        do {
            $numero = 'MEM-' . strtoupper(Str::random(6));
        } while (Membre::where('numero', $numero)->exists());

        return $numero;
    }

    /**
     * Enregistrer un nouveau membre
     */
    public function store(Request $request)
    {
        // Normaliser le téléphone avant validation et recherche d'unicité
        if ($request->has('telephone')) {
            $request->merge([
                'telephone' => \App\Models\Membre::normalizePhoneNumber($request->telephone)
            ]);
        }

        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'email' => 'nullable|email|max:255|unique:membres,email',
            'telephone' => 'required|string|max:20|unique:membres,telephone',
            'adresse' => 'nullable|string',
            'date_adhesion' => 'required|date',
            'statut' => 'required|in:actif,inactif,suspendu',
            'segment' => 'nullable|string|max:255',
            'nouveau_segment' => 'nullable|string|max:255|required_if:segment,__nouveau__',
            'password' => 'required|string|min:6',
        ]);
        
        // Si un nouveau segment est fourni, l'utiliser
        if ($request->segment === '__nouveau__' && $request->filled('nouveau_segment')) {
            $validated['segment'] = trim($request->nouveau_segment);
        } elseif ($request->segment === '__nouveau__') {
            $validated['segment'] = null;
        }
        
        unset($validated['nouveau_segment']);

        // Générer un numéro de membre unique
        $validated['numero'] = $this->generateNumeroMembre();

        // Hasher le mot de passe
        $validated['password'] = Hash::make($validated['password']);

        Membre::create($validated);

        return redirect()->route('membres.index')
            ->with('success', 'Membre créé avec succès.');
    }

    /**
     * Afficher les détails d'un membre
     */
    public function show(Membre $membre)
    {
        return view('membres.show', compact('membre'));
    }

    /**
     * Afficher le formulaire d'édition
     */
    public function edit(Membre $membre)
    {
        // Récupérer tous les segments uniques existants
        $segments = Membre::select('segment')
            ->whereNotNull('segment')
            ->where('segment', '!=', '')
            ->distinct()
            ->orderBy('segment')
            ->pluck('segment')
            ->toArray();
        
        return view('membres.edit', compact('membre', 'segments'));
    }

    /**
     * Mettre à jour un membre
     */
    public function update(Request $request, Membre $membre)
    {
        // Normaliser le téléphone avant validation et recherche d'unicité
        if ($request->has('telephone')) {
            $request->merge([
                'telephone' => \App\Models\Membre::normalizePhoneNumber($request->telephone)
            ]);
        }

        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'email' => [
                'nullable',
                'email',
                'max:255',
                Rule::unique('membres')->ignore($membre->id),
            ],
            'telephone' => [
                'required',
                'string',
                'max:20',
                Rule::unique('membres')->ignore($membre->id),
            ],
            'adresse' => 'nullable|string',
            'date_adhesion' => 'required|date',
            'statut' => 'required|in:actif,inactif,suspendu',
            'segment' => 'nullable|string|max:255',
            'nouveau_segment' => 'nullable|string|max:255|required_if:segment,__nouveau__',
            'password' => 'nullable|string|min:6',
        ]);
        
        // Si un nouveau segment est fourni, l'utiliser
        if ($request->segment === '__nouveau__' && $request->filled('nouveau_segment')) {
            $validated['segment'] = trim($request->nouveau_segment);
        } elseif ($request->segment === '__nouveau__') {
            $validated['segment'] = null;
        }
        
        unset($validated['nouveau_segment']);

        // Si le mot de passe est fourni, le hasher
        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $membre->update($validated);

        return redirect()->route('membres.index')
            ->with('success', 'Membre mis à jour avec succès.');
    }

    /**
     * Supprimer un membre
     */
    public function destroy(Membre $membre)
    {
        $membre->delete();

        return redirect()->route('membres.index')
            ->with('success', 'Membre supprimé avec succès.');
    }
}
