<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\AppSetting;
use Illuminate\Validation\Rules\Password;

class PasswordExpirationController extends Controller
{
    public function showExpireForm()
    {
        return view('auth.password-expire');
    }

    public function postExpire(Request $request)
    {
        $guard = session()->get('password_expire_guard', 'web');
        $user = Auth::guard($guard)->user();

        if (!$user) {
            return redirect()->route('admin.login');
        }

        // Validate complex password rules from settings
        $minLength = AppSetting::get('pwd_min_length', 8);
        $requireComplex = filter_var(AppSetting::get('pwd_require_complex', false), FILTER_VALIDATE_BOOLEAN);

        $passwordRule = Password::min($minLength);
        if ($requireComplex) {
            $passwordRule->letters()->mixedCase()->numbers()->symbols();
        }

        $request->validate([
            'current_password' => 'required',
            'password' => ['required', 'confirmed', $passwordRule],
        ]);

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'L\'ancien mot de passe est incorrect.']);
        }

        if (Hash::check($request->password, $user->password)) {
            return back()->withErrors(['password' => 'Le nouveau mot de passe doit être différent de l\'ancien.']);
        }

        $user->forceFill([
            'password' => Hash::make($request->password),
            'password_changed_at' => now()
        ])->save();

        session()->forget('password_expire_guard');

        $redirectRoute = $guard === 'membre' ? 'membre.dashboard' : 'dashboard';
        return redirect()->route($redirectRoute)->with('success', 'Votre mot de passe a été mis à jour avec succès.');
    }
}
