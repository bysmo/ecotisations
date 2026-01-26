<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>FlexFin - Sécurité de connexion</title>
    
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
                <i class="bi bi-fingerprint"></i>
            </div>
            <h1 class="product-name">FlexFin</h1>
            <p class="product-tagline">vos finances, en toute flexibilité!</p>
            
            <div class="auth-stats mt-4">
                <div class="stat-item">
                    <span class="stat-value">MFA</span>
                    <span class="stat-label">Double Protection Active</span>
                </div>
            </div>
        </div>

        <!-- MFA Verification Form -->
        <div class="auth-content">
            <div class="auth-card text-center">
                <div class="mb-4">
                    <h2 class="auth-title">Sécurité renforcée</h2>
                    <p class="auth-subtitle">Choisissez une méthode pour valider votre identité.</p>
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
                
                <!-- Nav tabs -->
                <ul class="nav nav-pills mb-4 justify-content-center" id="mfaTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active btn-sm" id="sms-tab" data-bs-toggle="pill" data-bs-target="#sms-mfa" type="button" role="tab">
                            <i class="bi bi-chat-dots me-1"></i> SMS
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link btn-sm" id="bio-tab" data-bs-toggle="pill" data-bs-target="#bio-mfa" type="button" role="tab">
                            <i class="bi bi-upc-scan me-1"></i> Biométrie
                        </button>
                    </li>
                </ul>

                <div class="tab-content border-top pt-4" id="mfaTabContent">
                    <!-- SMS MFA -->
                    <div class="tab-pane fade show active" id="sms-mfa" role="tabpanel">
                        <form method="POST" action="{{ route('membre.verify.mfa.post') }}">
                            @csrf
                            <div class="mb-4">
                                <label for="mfa_code" class="form-label">Code SMS</label>
                                <input type="text" 
                                       class="form-control text-center fw-bold" 
                                       id="mfa_code" 
                                       name="mfa_code" 
                                       placeholder="· · · · · ·"
                                       maxlength="6"
                                       style="font-size: 1.5rem; letter-spacing: 0.5rem;"
                                       required>
                            </div>
                            <button type="submit" class="btn btn-primary w-100 mb-3">
                                Valider par SMS
                            </button>
                        </form>
                    </div>

                    <!-- BIOMETRIC MFA -->
                    <div class="tab-pane fade" id="bio-mfa" role="tabpanel">
                        <div class="py-3">
                            <i class="bi bi-person-bounding-box text-primary" style="font-size: 4rem;"></i>
                            <p class="mt-3 small text-muted">Afin de vous authentifier par biométrie, veuillez placer votre visage ou votre doigt sur le capteur de votre appareil.</p>
                            
                            <div id="bioLoading" class="mt-3 d-none">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Chargement...</span>
                                </div>
                                <p class="mt-2 small">Scan en cours...</p>
                            </div>

                            <button type="button" id="startBio" class="btn btn-outline-primary w-100">
                                Scanner maintenant
                            </button>
                        </div>
                    </div>
                </div>

                <div class="auth-footer mt-5">
                    <p class="mb-1">© 2026 FlexFin+</p>
                    <p>Powered by Aladin Technologies Solutions (ALTES)</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Auto-format OTP input
        const mfaInput = document.getElementById('mfa_code');
        if (mfaInput) {
            mfaInput.addEventListener('input', function (e) {
                this.value = this.value.replace(/[^0-9]/g, '');
            });
        }

        // Biometric Simulation
        const startBioBtn = document.getElementById('startBio');
        const bioLoading = document.getElementById('bioLoading');

        if (startBioBtn) {
            startBioBtn.addEventListener('click', function() {
                startBioBtn.classList.add('d-none');
                bioLoading.classList.remove('d-none');

                // Simulation de succès après 2 secondes
                setTimeout(() => {
                    alert("Succès de l'authentification biométrique !");
                    // Pour la démo, on pourrait envoyer une requête POST ici
                    // window.location.href = "{{ route('membre.dashboard') }}";
                }, 2000);
            });
        }
    </script>
</body>
</html>
