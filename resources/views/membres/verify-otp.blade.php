<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>FlexFin - Vérification de compte</title>
    
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
                <i class="bi bi-shield-check"></i>
            </div>
            <h1 class="product-name">FlexFin</h1>
            <p class="product-tagline">vos finances, en toute flexibilité!</p>
            
            <div class="auth-stats mt-4">
                <div class="stat-item">
                    <span class="stat-value">Validation</span>
                    <span class="stat-label">Vérification Email & SMS</span>
                </div>
            </div>
        </div>

        <!-- OTP Verification Form -->
        <div class="auth-content">
            <div class="auth-card">
                <div class="mb-4 text-center">
                    <h2 class="auth-title">Vérifiez votre compte</h2>
                    <p class="auth-subtitle">Saisissez le code de 6 chiffres que nous venons de vous envoyer par Email et SMS.</p>
                </div>
                
                @if(session('info'))
                    <div class="alert alert-info py-2" style="font-size: 0.85rem;">
                        <i class="bi bi-info-circle me-2"></i> {{ session('info') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger py-2" style="font-size: 0.85rem;">
                        <i class="bi bi-exclamation-triangle me-2"></i> {{ session('error') }}
                    </div>
                @endif
                
                <form method="POST" action="{{ route('membre.verify.otp.post') }}">
                    @csrf
                    
                    <div class="mb-4">
                        <label for="otp" class="form-label text-center d-block">Code de confirmation</label>
                        <input type="text" 
                               class="form-control text-center fw-bold" 
                               id="otp" 
                               name="otp" 
                               placeholder="· · · · · ·"
                               maxlength="6"
                               style="font-size: 1.5rem; letter-spacing: 0.5rem;"
                               required 
                               autofocus>
                        @error('otp') <div class="text-danger mt-1 small text-center">{{ $message }}</div> @enderror
                    </div>
                    
                    <button type="submit" class="btn btn-primary w-100 mb-3">
                        Activer mon compte
                    </button>
                    
                    <div class="text-center">
                        <p class="mb-0" style="font-size: 0.85rem;">Vous n'avez pas reçu le code ? <a href="#" class="text-decoration-none fw-bold" style="color: var(--aladin-blue);">Renvoyer</a></p>
                    </div>
                </form>

                <div class="auth-footer mt-5">
                    <p class="mb-1">© 2026 FlexFin+</p>
                    <p>Powered by Aladin Technologies Solutions (ALTES)</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Auto-format OTP input
        document.getElementById('otp').addEventListener('input', function (e) {
            this.value = this.value.replace(/[^0-9]/g, '');
        });
    </script>
</body>
</html>
