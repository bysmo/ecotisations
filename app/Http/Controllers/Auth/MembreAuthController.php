<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Membre;
use App\Models\Segment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class MembreAuthController extends Controller
{
    /**
     * Afficher le formulaire de connexion
     */
    public function showLoginForm()
    {
        return view('membres.login');
    }

    /**
     * Traiter la connexion
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $credentials = $request->only('email', 'password');
        $remember = $request->filled('remember');

        // Vérifier si le membre existe et est actif
        $membre = \App\Models\Membre::where('email', $credentials['email'])->first();
        
        if (!$membre) {
            throw ValidationException::withMessages([
                'email' => ['Les identifiants fournis sont incorrects.'],
            ]);
        }
        
        // Vérifier si le membre est actif
        if ($membre->statut !== 'actif') {
            throw ValidationException::withMessages([
                'email' => ['Votre compte est inactif. Veuillez contacter l\'administrateur.'],
            ]);
        }

        if (Auth::guard('membre')->attempt($credentials, $remember)) {
            $request->session()->regenerate();
            
            return redirect()->route('membre.dashboard');
        }

        throw ValidationException::withMessages([
            'email' => ['Les identifiants fournis sont incorrects.'],
        ]);
    }

    /**
     * Afficher le formulaire d'inscription
     */
    public function showRegisterForm()
    {
        // Récupérer les segments existants
        $segments = Segment::orderBy('nom')->pluck('nom')->toArray();
        
        return view('membres.register', compact('segments'));
    }

    /**
     * Traiter l'inscription
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:membres,email',
            'telephone' => 'nullable|string|max:20',
            'adresse' => 'nullable|string',
            'segment' => 'nullable|string|max:255',
            'nouveau_segment' => 'nullable|string|max:255|required_if:segment,__nouveau__',
            'password' => 'required|string|min:6|confirmed',
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
        
        // Définir les valeurs par défaut
        $validated['date_adhesion'] = now();
        $validated['statut'] = 'actif';

        // Hasher le mot de passe
        $validated['password'] = Hash::make($validated['password']);

        // Créer le membre
        $membre = Membre::create($validated);

        // Connecter automatiquement le membre après inscription
        Auth::guard('membre')->login($membre);

        return redirect()->route('membre.dashboard')
            ->with('success', 'Votre compte a été créé avec succès ! Bienvenue !');
    }

    /**
     * Générer un numéro de membre unique
     */
    private function generateNumeroMembre()
    {
        do {
            // Générer une combinaison de lettres et chiffres
            $numero = 'MEM-' . strtoupper(\Illuminate\Support\Str::random(6));
        } while (Membre::where('numero', $numero)->exists());

        return $numero;
    }

    /**
     * Déconnexion
     */
    public function logout(Request $request)
    {
        Auth::guard('membre')->logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('membre.login');
    }
}
