<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\RateLimiter;
use App\Models\AppSetting;
use Illuminate\Support\Str;

class AdminAuthController extends Controller
{
    /**
     * Afficher le formulaire de connexion admin
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Traiter la connexion admin
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'captcha' => 'required|captcha',
        ], [
            'captcha.required' => 'Veuillez recopier les caractères de l\'image.',
            'captcha.captcha' => 'Le code saisi est incorrect. Veuillez réessayer.'
        ]);

        $throttleKey = Str::lower($request->input('email')) . '|' . $request->ip();
        $maxAttempts = AppSetting::get('lockout_max_attempts', 5);
        $lockoutMinutes = AppSetting::get('lockout_duration_min', 15);

        if (RateLimiter::tooManyAttempts($throttleKey, $maxAttempts)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            $minutes = ceil($seconds / 60);
            return back()->withInput($request->only('email'))
                         ->with('error', "Trop de tentatives. Veuillez réessayer dans {$minutes} minute(s).");
        }

        $credentials = $request->only('email', 'password');
        $remember = $request->filled('remember');

        if (Auth::guard('web')->attempt($credentials, $remember)) {
            RateLimiter::clear($throttleKey);
            $request->session()->regenerate();
            
            return redirect()->route('dashboard')->with('success', 'Connexion réussie ! Bienvenue.');
        }

        RateLimiter::hit($throttleKey, $lockoutMinutes * 60);

        return back()->withInput($request->only('email'))
                     ->with('error', 'Les identifiants fournis sont incorrects.');
    }

    /**
     * Déconnexion admin
     */
    public function logout(Request $request)
    {
        Auth::guard('web')->logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('admin.login')->with('success', 'Vous avez été déconnecté avec succès.');
    }
}
