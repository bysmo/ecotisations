<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>FlexFin - Mot de passe oublié</title>
    
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
                <i class="bi bi-shield-lock"></i>
            </div>
            <h1 class="product-name">FlexFin</h1>
            <p class="product-tagline">vos finances, en toute flexibilité!</p>
            
            <div class="auth-stats mt-4">
                <div class="stat-item">
                    <span class="stat-value">Sécurisé</span>
                    <span class="stat-label">Le lien expire après 60 minutes</span>
                </div>
            </div>
        </div>

        <!-- Forgot Password Form -->
        <div class="auth-content">
            <div class="auth-card">
                <div class="mb-4">
                    <h2 class="auth-title">Mot de passe oublié ?</h2>
                    <p class="auth-subtitle">Aucun problème. Indiquez-nous simplement votre adresse e-mail et nous vous enverrons un lien de réinitialisation.</p>
                </div>
                
                <form method="POST" action="#">
                    @csrf
                    
                    <div class="mb-4">
                        <label for="email" class="form-label">Entrez votre adresse e-mail</label>
                        <input type="email" 
                               class="form-control" 
                               id="email" 
                               name="email" 
                               placeholder="votre@email.com"
                               required 
                               autofocus>
                    </div>
                    
                    <button type="submit" class="btn btn-primary w-100 mb-4">
                        Envoyer le lien de réinitialisation
                    </button>
                    
                    <div class="text-center">
                        <a href="{{ $type === 'admin' ? route('admin.login') : route('membre.login') }}" class="text-decoration-none" style="font-size: 0.9rem; color: var(--aladin-blue);">
                            <i class="bi bi-arrow-left"></i> Retour à la connexion
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
</body>
</html>
