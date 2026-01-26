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
     * Afficher le formulaire de mot de passe oublié
     */
    public function showForgotPasswordForm()
    {
        return view('auth.forgot-password', ['type' => 'membre']);
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

        // Vérifier si le membre existe
        $membre = \App\Models\Membre::where('email', $credentials['email'])->first();
        
        if (!$membre || !Hash::check($request->password, $membre->password)) {
            throw ValidationException::withMessages([
                'email' => ['Les identifiants fournis sont incorrects.'],
            ]);
        }
        
        // Vérifier si le membre est actif
        if ($membre->statut !== 'actif') {
            // Si le compte est inactif et a un code de vérification, on propose de finir l'OTP
            if ($membre->verification_code) {
                session(['membre_verification_id' => $membre->id]);
                return redirect()->route('membre.verify.otp')
                    ->with('info', 'Votre compte n\'est pas encore activé. Veuillez saisir le code reçu.');
            }
            
            throw ValidationException::withMessages([
                'email' => ['Votre compte est inactif. Veuillez contacter l\'administrateur.'],
            ]);
        }

        // --- Gestion du MFA ---
        // Le user a entré le bon mot de passe, mais MFA activé ?
        // Pour ce projet, on force le MFA par défaut ou on vérifie le champ mfa_enabled
        if ($membre->mfa_enabled || true) { // Forcé à true pour la démo/sécurité demandée
            // Générer un code MFA temporaire (SMS simulé)
            $mfaCode = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
            $membre->update(['verification_code' => $mfaCode]);
            
            \Illuminate\Support\Facades\Log::info("MFA Code pour {$membre->email}: {$mfaCode}");
            
            session(['membre_mfa_id' => $membre->id, 'membre_remember' => $remember]);
            
            return redirect()->route('membre.verify.mfa')
                ->with('info', 'Un code de sécurité a été envoyé pour valider votre connexion.');
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
     * Afficher le formulaire MFA
     */
    public function showMfa()
    {
        if (!session('membre_mfa_id')) {
            return redirect()->route('membre.login');
        }
        return view('membres.verify-mfa');
    }

    /**
     * Vérifier le code MFA
     */
    public function verifyMfa(Request $request)
    {
        $request->validate([
            'mfa_code' => 'required|string|size:6',
        ]);

        $membreId = session('membre_mfa_id');
        $remember = session('membre_remember', false);
        
        $membre = Membre::findOrFail($membreId);

        if ($request->mfa_code === $membre->verification_code) {
            $membre->update(['verification_code' => null]);
            
            Auth::guard('membre')->login($membre, $remember);
            
            session()->forget(['membre_mfa_id', 'membre_remember']);
            $request->session()->regenerate();
            
            return redirect()->route('membre.dashboard');
        }

        return back()->with('error', 'Le code de sécurité est incorrect.');
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
            'date_naissance' => 'nullable|date',
            'lieu_naissance' => 'nullable|string|max:255',
            'sexe' => 'nullable|string|in:M,F,Autre',
            'nom_mere' => 'nullable|string|max:255',
            'email' => 'required|email|max:255|unique:membres,email',
            'telephone' => 'nullable|string|max:20',
            'adresse' => 'required|string',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'segment' => 'nullable|string|max:255',
            'nouveau_segment' => 'nullable|string|max:255|required_if:segment,__nouveau__',
            'password' => 'required|string|min:6|confirmed',
            'piece_identite_recto' => 'required|image|max:2048',
            'piece_identite_verso' => 'required|image|max:2048',
            'selfie_base64' => 'required|string',
        ]);

        // Gestion du segment
        if ($request->segment === '__nouveau__' && $request->filled('nouveau_segment')) {
            $validated['segment'] = trim($request->nouveau_segment);
        } elseif ($request->segment === '__nouveau__') {
            $validated['segment'] = null;
        }
        unset($validated['nouveau_segment']);

        // Upload des documents KYC
        if ($request->hasFile('piece_identite_recto')) {
            $validated['piece_identite_recto'] = $request->file('piece_identite_recto')->store('kyc/recto', 'public');
        }
        if ($request->hasFile('piece_identite_verso')) {
            $validated['piece_identite_verso'] = $request->file('piece_identite_verso')->store('kyc/verso', 'public');
        }

        // Traitement du selfie (Base64)
        if ($request->filled('selfie_base64')) {
            $img = $request->selfie_base64;
            $img = str_replace('data:image/jpeg;base64,', '', $img);
            $img = str_replace(' ', '+', $img);
            $data = base64_decode($img);
            $fileName = 'kyc/selfies/' . uniqid() . '.jpg';
            \Illuminate\Support\Facades\Storage::disk('public')->put($fileName, $data);
            $validated['selfie'] = $fileName;
        }
        unset($validated['selfie_base64']);

        // Infos de base
        $validated['numero'] = $this->generateNumeroMembre();
        $validated['date_adhesion'] = now();
        $validated['statut'] = 'inactif'; // En attente de vérification
        $validated['password'] = Hash::make($validated['password']);

        // Génération du code OTP
        $otp = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
        $validated['verification_code'] = $otp;

        // Créer le membre
        $membre = Membre::create($validated);

        // Envoyer le code par Mail (simulé/log)
        // \Illuminate\Support\Facades\Mail::to($membre->email)->send(new \App\Mail\VerificationOTP($otp));
        \Illuminate\Support\Facades\Log::info("OTP pour {$membre->email}: {$otp}");

        // Envoyer le code par SMS (simulé)
        \Illuminate\Support\Facades\Log::info("SMS OTP pour {$membre->telephone}: {$otp}");

        // Stocker l'ID en session pour la vérification
        session(['membre_verification_id' => $membre->id]);

        return redirect()->route('membre.verify.otp')
            ->with('info', 'Un code de vérification a été envoyé par Email et SMS.');
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
     * Afficher le formulaire de vérification OTP
     */
    public function showVerifyOtp()
    {
        if (!session('membre_verification_id')) {
            return redirect()->route('membre.register');
        }
        return view('membres.verify-otp');
    }

    /**
     * Vérifier le code OTP
     */
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp' => 'required|string|size:6',
        ]);

        $membreId = session('membre_verification_id');
        if (!$membreId) {
            return redirect()->route('membre.register');
        }

        $membre = Membre::findOrFail($membreId);

        if ($request->otp === $membre->verification_code) {
            $membre->update([
                'statut' => 'actif',
                'email_verified_at' => now(),
                'sms_verified_at' => now(),
                'verification_code' => null, // On vide le code après usage
            ]);

            session()->forget('membre_verification_id');
            Auth::guard('membre')->login($membre);

            return redirect()->route('membre.dashboard')
                ->with('success', 'Votre compte a été activé avec succès ! Bienvenue !');
        }

        return back()->with('error', 'Le code de vérification est incorrect.');
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
