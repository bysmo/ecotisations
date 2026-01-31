<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    @php
        $appNomComplet = \App\Models\AppSetting::get('app_nom', 'Gestion des Cotisations');
        $logoPath = \App\Models\AppSetting::get('entreprise_logo');
        $faviconUrl = null;
        
        if ($logoPath) {
            $logoFullPath = storage_path('app/public/' . $logoPath);
            $publicStorageExists = \Illuminate\Support\Facades\File::exists(public_path('storage'));
            
            if ($publicStorageExists && \Illuminate\Support\Facades\File::exists($logoFullPath)) {
                $faviconUrl = asset('storage/' . $logoPath);
            } else {
                $filename = basename($logoPath);
                $faviconUrl = route('storage.logo', ['filename' => $filename]);
            }
        }
    @endphp
    
    @if($faviconUrl)
        <link rel="icon" type="image/png" href="{{ $faviconUrl }}">
    @else
        <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    @endif
    
    <title>{{ $appNomComplet }} - Connexion Membre</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <!-- Google Fonts - Ubuntu -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Ubuntu:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}?v={{ time() }}">
    <!-- intl-tel-input CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/intl-tel-input@24.5.1/build/css/intlTelInput.css">
    <style>
        .iti { width: 100%; }
        .iti__flag-container { border-radius: 8px 0 0 8px; }
        .iti--separate-dial-code input { padding-left: 95px !important; }
        .iti--allow-dropdown input { padding-left: 95px !important; }
    </style>
</head>
<body>
    <div class="auth-container">
        <!-- Sidebar Branding -->
        <div class="auth-sidebar">
            <div class="auth-logo-box">
                <i class="bi bi-person-circle"></i>
            </div>
            <h1 class="product-name">Espace Membre</h1>
            <p class="product-tagline">Gérez vos cotisations en toute simplicité</p>
            
            <p class="mt-4 mb-4">Connectez-vous pour suivre vos engagements, effectuer des paiements et consulter vos états financiers.</p>
            
            <div class="auth-stats">
                <div class="stat-item">
                    <span class="stat-value">24/7</span>
                    <span class="stat-label">Disponibilité</span>
                </div>
                <div class="stat-item">
                    <span class="stat-value">100%</span>
                    <span class="stat-label">Sécurisé</span>
                </div>
            </div>
        </div>

        <!-- Login Form -->
        <div class="auth-content">
            <div class="auth-card">
                <div class="mb-4">
                    <h2 class="auth-title">Connexion Membre</h2>
                    <p class="auth-subtitle">Accédez à votre espace personnel FlexFin</p>
                </div>
                
                <form method="POST" action="{{ route('membre.login') }}">
                    @csrf
                    
                    <div class="mb-3">
                        <label for="telephone" class="form-label">Numéro de téléphone</label>
                        <input type="tel" 
                               class="form-control" 
                               id="telephone" 
                               name="telephone_input" 
                               placeholder="XXXXXXXX"
                               data-initial="{{ old('telephone') }}" 
                               required 
                               autofocus>
                        <input type="hidden" name="telephone" id="full_telephone">
                    </div>
                    
                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <label for="password" class="form-label">Mot de passe</label>
                            {{-- <a href="{{ route('membre.password.request') }}" class="text-decoration-none" style="font-size: 0.8rem; color: var(--aladin-blue);">Mot de passe oublié ?</a> --}}
                        </div>
                        <div class="input-group">
                            <input type="password" 
                                   class="form-control" 
                                   id="password" 
                                   name="password" 
                                   required>
                            <span class="input-group-text bg-white border-start-0">
                                <i class="bi bi-eye text-muted"></i>
                            </span>
                        </div>
                    </div>
                    
                    <div class="mb-4 form-check">
                        <input type="checkbox" class="checkbox-input" id="remember" name="remember">
                        <label class="form-check-label" for="remember">
                            Se souvenir de moi
                        </label>
                    </div>
                    
                    <button type="submit" class="btn btn-primary w-100 mb-3">
                        Se connecter
                    </button>
                    
                    <div class="text-center">
                        <p class="mb-0" style="font-size: 0.9rem;">Pas encore de compte ? <a href="{{ route('membre.register') }}" class="text-decoration-none fw-bold" style="color: var(--aladin-blue);">Créer un compte</a></p>
                    </div>

                    <div class="text-center mt-3">
                        <a href="{{ route('admin.login') }}" class="text-decoration-none" style="font-size: 0.8rem; color: #666;">
                            <i class="bi bi-shield-lock"></i> Accès Administrateur
                        </a>
                    </div>
                </form>

                <div class="auth-footer">
                    <p class="mb-1">© 2026 FlexFin+</p>
                    <p>Powered by Aladin Technologies Solutions (ALTES)</p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Toast Container -->
    <div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 9999;">
        <div id="toastContainer"></div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- intl-tel-input JS -->
    <script src="https://cdn.jsdelivr.net/npm/intl-tel-input@24.5.1/build/js/intlTelInput.min.js"></script>
    
    <script>
        // Initialisation de intl-tel-input
        const phoneInput = document.querySelector("#telephone");
        const fullPhoneInput = document.querySelector("#full_telephone");
        
        const iti = window.intlTelInput(phoneInput, {
            initialCountry: "bf", // Burkina Faso par défaut
            preferredCountries: ["bf", "sn", "ci", "ml", "tg", "bj"],
            utilsScript: "https://cdn.jsdelivr.net/npm/intl-tel-input@24.5.1/build/js/utils.js",
            separateDialCode: true,
        });

        // Gérer la valeur initiale pour éviter les duplications
        const initialNumber = phoneInput.getAttribute('data-initial');
        if (initialNumber) {
            iti.setNumber(initialNumber);
        }

        // Mettre à jour le champ caché avant la soumission du formulaire
        document.querySelector("form").addEventListener("submit", function() {
            fullPhoneInput.value = iti.getNumber();
        });

        function showToast(message, type = 'success') {
            const toastContainer = document.getElementById('toastContainer');
            const toastId = 'toast-' + Date.now();
            const bgClass = type === 'success' ? 'bg-success' : type === 'error' ? 'bg-danger' : type === 'warning' ? 'bg-warning' : 'bg-info';
            const icon = type === 'success' ? 'bi-check-circle' : type === 'error' ? 'bi-x-circle' : type === 'warning' ? 'bi-exclamation-triangle' : 'bi-info-circle';
            
            const toastHTML = `
                <div id="${toastId}" class="toast align-items-center text-white ${bgClass} border-0" role="alert" aria-live="assertive" aria-atomic="true">
                    <div class="d-flex">
                        <div class="toast-body" style="font-weight: 300;">
                            <i class="bi ${icon} me-2"></i>${message}
                        </div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                    </div>
                </div>
            `;
            
            toastContainer.insertAdjacentHTML('beforeend', toastHTML);
            const toastElement = document.getElementById(toastId);
            const toast = new bootstrap.Toast(toastElement, { autohide: true, delay: 5000 });
            toast.show();
            toastElement.addEventListener('hidden.bs.toast', () => toastElement.remove());
        }
        
        @if(session('success')) showToast('{{ session('success') }}', 'success'); @endif
        @if(session('error')) showToast('{{ session('error') }}', 'error'); @endif
        @if($errors->any()) @foreach($errors->all() as $error) showToast('{{ $error }}', 'error'); @endforeach @endif
    </script>
</body>
</html>
