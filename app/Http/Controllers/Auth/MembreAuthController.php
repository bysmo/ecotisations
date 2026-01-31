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

        /*
        // Vérifier si l'email a été vérifié
        if (!$membre->hasVerifiedEmail()) {
            $request->session()->flash('unverified_email', $membre->email);
            throw ValidationException::withMessages([
                'email' => ['Vous devez vérifier votre adresse email avant de vous connecter. Un lien vous a été envoyé par email.'],
            ]);
        }
        */

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

        // Créer le membre et marquer comme vérifié automatiquement
        $validated['email_verified_at'] = now();
        $membre = Membre::create($validated);

        /*
        try {
            $membre->sendEmailVerificationNotification();
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Envoi email vérification après inscription: ' . $e->getMessage());
            return redirect()->route('membre.login')
                ->with('error', 'Votre compte a été créé mais l\'envoi du lien de vérification a échoué. Vérifiez la configuration SMTP dans Paramètres > SMTP, ou demandez à l\'administrateur de renvoyer le lien.');
        }

        return redirect()->route('membre.login')
            ->with('success', 'Un lien de vérification a été envoyé à votre adresse email. Cliquez sur ce lien pour activer votre compte, puis connectez-vous.');
        */

        Auth::guard('membre')->login($membre);
        return redirect()->route('membre.dashboard')->with('success', 'Bienvenue sur FlexFin ! Votre compte a été créé avec succès.');
    }

    /**
     * Vérifier l'email du membre via le lien reçu par mail
     */
    public function verifyEmail(Request $request)
    {
        // id et hash sont dans l'URL (paramètres de route), pas dans le corps de la requête
        $id = $request->route('id');
        $hash = $request->route('hash');

        $validated = validator(['id' => $id, 'hash' => $hash], [
            'id' => 'required|integer|exists:membres,id',
            'hash' => 'required|string',
        ])->validate();

        $membre = Membre::findOrFail($validated['id']);

        if (!hash_equals((string) $validated['hash'], sha1($membre->email))) {
            return redirect()->route('membre.login')
                ->with('error', 'Le lien de vérification est invalide ou a expiré.');
        }

        if ($membre->hasVerifiedEmail()) {
            return redirect()->route('membre.login')
                ->with('success', 'Votre adresse email est déjà vérifiée. Vous pouvez vous connecter.');
        }

        $membre->markEmailAsVerified();

        return redirect()->route('membre.login')
            ->with('success', 'Votre adresse email a été vérifiée. Vous pouvez maintenant vous connecter.');
    }

    /**
     * Renvoyer l'email de vérification
     */
    public function resendVerification(Request $request)
    {
        $request->validate(['email' => 'required|email|exists:membres,email']);

        $membre = Membre::where('email', $request->email)->first();

        if ($membre->hasVerifiedEmail()) {
            return redirect()->route('membre.login')
                ->with('success', 'Votre adresse email est déjà vérifiée.');
        }

        try {
            app(\App\Services\EmailService::class)->sendVerificationEmail($membre);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Renvoyer email vérification: ' . $e->getMessage());
            return redirect()->route('membre.login')
                ->with('error', 'Impossible d\'envoyer le lien. Vérifiez la configuration SMTP (Paramètres > SMTP).');
        }

        return redirect()->route('membre.login')
            ->with('success', 'Un nouveau lien de vérification a été envoyé à votre adresse email.');
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
