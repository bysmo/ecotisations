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
    
    <title>FlexFin - Connexion Membre</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <!-- Google Fonts - Ubuntu -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Ubuntu:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}?v={{ time() }}">
</head>
<body>
    <div class="auth-container">
        <!-- Sidebar Branding -->
        <div class="auth-sidebar">
            <div class="auth-logo-box">
                <i class="bi bi-person-heart"></i>
            </div>
            <h1 class="product-name">FlexFin</h1>
            <p class="product-tagline">vos finances, en toute flexibilité!</p>
            
            <p class="mt-4 mb-4">Espace Membre : Gérez vos cotisations et engagements en toute simplicité.</p>
            
            <div class="auth-stats">
                <div class="stat-item">
                    <span class="stat-value">10K+</span>
                    <span class="stat-label">Membres</span>
                </div>
                <div class="stat-item">
                    <span class="stat-value">24/7</span>
                    <span class="stat-label">Accessibilité</span>
                </div>
            </div>
        </div>

        <!-- Login Form -->
        <div class="auth-content">
            <div class="auth-card">
                <div class="mb-4">
                    <h2 class="auth-title">Espace Membre</h2>
                    <p class="auth-subtitle">Accédez à votre compte pour gérer vos cotisations</p>
                </div>
                
                <form method="POST" action="{{ route('membre.login') }}">
                    @csrf
                    
                    <div class="mb-3">
                        <label for="email" class="form-label">Entrez votre adresse e-mail</label>
                        <input type="email" 
                               class="form-control @error('email') is-invalid @enderror" 
                               id="email" 
                               name="email" 
                               placeholder="votre@email.com"
                               value="{{ old('email') }}" 
                               required 
                               autofocus>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <label for="password" class="form-label">Mot de passe</label>
                            <a href="{{ route('membre.password.request') }}" class="text-decoration-none" style="font-size: 0.8rem; color: var(--aladin-blue);">Mot de passe oublié ?</a>
                        </div>
                        <div class="input-group">
                            <input type="password" 
                                   class="form-control @error('password') is-invalid @enderror" 
                                   id="password" 
                                   name="password" 
                                   required>
                            <span class="input-group-text bg-white border-start-0">
                                <i class="bi bi-eye text-muted"></i>
                            </span>
                        </div>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-4 form-check">
                        <input type="checkbox" class="form-check-input" id="remember" name="remember">
                        <label class="form-check-label" for="remember">
                            Se souvenir de moi
                        </label>
                    </div>
                    
                    <button type="submit" class="btn btn-primary w-100 mb-4">
                        Connexion
                    </button>
                    
                    <div class="text-center p-3 border-top">
                        <p class="mb-2" style="font-size: 0.9rem;">Êtes-vous nouveau ici ?</p>
                        <a href="{{ route('membre.register') }}" class="btn btn-outline-primary w-100 fw-bold" style="border-width: 2px;">
                            Créer un compte
                        </a>
                    </div>
                    
                    <div class="text-center mt-3">
                        <a href="{{ route('admin.login') }}" class="text-decoration-none" style="font-size: 0.8rem; color: var(--text-muted);">
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
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
