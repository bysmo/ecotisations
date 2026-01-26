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
    
    <title>FlexFin - Inscription Membre</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <!-- Google Fonts - Ubuntu -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Ubuntu:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}?v={{ time() }}">
    <style>
        .auth-card { max-width: 800px; }
        @media (max-width: 992px) { .auth-card { max-width: 500px; } }
    </style>
</head>
<body>
    <div class="auth-container">
        <!-- Sidebar Branding -->
        <div class="auth-sidebar">
            <div class="auth-logo-box">
                <i class="bi bi-person-plus"></i>
            </div>
            <h1 class="product-name">FlexFin</h1>
            <p class="product-tagline">vos finances, en toute flexibilité!</p>
            
            <div class="text-start mt-4">
                <div class="d-flex align-items-center mb-3">
                    <div class="rounded-circle bg-warning p-2 me-3" style="width: 40px; height: 40px; display: flex; justify-content: center; align-items: center;">
                        <i class="bi bi-check-lg text-dark"></i>
                    </div>
                    <span>Gérez vos cotisations en ligne</span>
                </div>
                <div class="d-flex align-items-center mb-3">
                    <div class="rounded-circle bg-warning p-2 me-3" style="width: 40px; height: 40px; display: flex; justify-content: center; align-items: center;">
                        <i class="bi bi-shield-check text-dark"></i>
                    </div>
                    <span>Transactions sécurisées</span>
                </div>
                <div class="d-flex align-items-center mb-3">
                    <div class="rounded-circle bg-warning p-2 me-3" style="width: 40px; height: 40px; display: flex; justify-content: center; align-items: center;">
                        <i class="bi bi-bell text-dark"></i>
                    </div>
                    <span>Alertes et notifications</span>
                </div>
            </div>
        </div>

        <!-- Registration Form -->
        <div class="auth-content">
            <div class="auth-card">
                <div class="mb-4">
                    <h2 class="auth-title">Créer votre compte</h2>
                    <p class="auth-subtitle">Rejoignez FlexFin pour gérer vos finances</p>
                </div>
                
                @if (session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif
                
                <form method="POST" action="{{ route('membre.register') }}" enctype="multipart/form-data" id="registrationForm">
                    @csrf
                    
                    <!-- Stepper -->
                    <div class="registration-stepper">
                        <div class="step-circle active" data-step="1">1</div>
                        <div class="step-circle" data-step="2">2</div>
                        <div class="step-circle" data-step="3">3</div>
                        <div class="step-circle" data-step="4">4</div>
                    </div>

                    <!-- Step 1: Informations Personnelles -->
                    <div class="auth-step active" id="step-1">
                        <h5 class="mb-4 d-flex align-items-center"><i class="bi bi-person me-2"></i> Informations Personnelles</h5>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="nom" class="form-label">Nom <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('nom') is-invalid @enderror" id="nom" name="nom" value="{{ old('nom') }}" required placeholder="Votre nom">
                                @error('nom') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="prenom" class="form-label">Prénom <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('prenom') is-invalid @enderror" id="prenom" name="prenom" value="{{ old('prenom') }}" required placeholder="Votre prénom">
                                @error('prenom') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="date_naissance" class="form-label">Date de naissance</label>
                                <input type="date" class="form-control @error('date_naissance') is-invalid @enderror" id="date_naissance" name="date_naissance" value="{{ old('date_naissance') }}">
                                @error('date_naissance') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="lieu_naissance" class="form-label">Lieu de naissance</label>
                                <input type="text" class="form-control @error('lieu_naissance') is-invalid @enderror" id="lieu_naissance" name="lieu_naissance" value="{{ old('lieu_naissance') }}" placeholder="Lieu de naissance">
                                @error('lieu_naissance') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="sexe" class="form-label">Sexe</label>
                                <select class="form-select @error('sexe') is-invalid @enderror" id="sexe" name="sexe">
                                    <option value="">-- Sélectionner --</option>
                                    <option value="M" {{ old('sexe') === 'M' ? 'selected' : '' }}>Masculin</option>
                                    <option value="F" {{ old('sexe') === 'F' ? 'selected' : '' }}>Féminin</option>
                                    <option value="Autre" {{ old('sexe') === 'Autre' ? 'selected' : '' }}>Autre</option>
                                </select>
                                @error('sexe') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="nom_mere" class="form-label">Nom de la mère</label>
                                <input type="text" class="form-control @error('nom_mere') is-invalid @enderror" id="nom_mere" name="nom_mere" value="{{ old('nom_mere') }}" placeholder="Nom complet de la mère">
                                @error('nom_mere') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="step-footer mt-4">
                            <button type="button" class="btn btn-primary w-100 next-step">Suivant <i class="bi bi-arrow-right ms-2"></i></button>
                        </div>
                    </div>

                    <!-- Step 2: Contact & Localisation -->
                    <div class="auth-step" id="step-2">
                        <h5 class="mb-4 d-flex align-items-center"><i class="bi bi-geo-alt me-2"></i> Contact & Localisation</h5>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required placeholder="votre@email.com">
                                @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="telephone" class="form-label">Téléphone</label>
                                <input type="text" class="form-control @error('telephone') is-invalid @enderror" id="telephone" name="telephone" value="{{ old('telephone') }}" placeholder="+226 ...">
                                @error('telephone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="adresse" class="form-label">Adresse Détaillée <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <textarea class="form-control @error('adresse') is-invalid @enderror" id="adresse" name="adresse" rows="2" placeholder="Votre adresse physique précise" required>{{ old('adresse') }}</textarea>
                                <button class="btn btn-outline-primary" type="button" id="detectLocation" title="Ma position actuelle">
                                    <i class="bi bi-geo-alt"></i>
                                </button>
                            </div>
                            <input type="hidden" id="latitude" name="latitude" value="{{ old('latitude') }}">
                            <input type="hidden" id="longitude" name="longitude" value="{{ old('longitude') }}">
                            @error('adresse') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="step-footer">
                            <button type="button" class="btn btn-outline-secondary prev-step">Précédent</button>
                            <button type="button" class="btn btn-primary flex-grow-1 next-step">Suivant <i class="bi bi-arrow-right ms-2"></i></button>
                        </div>
                    </div>

                    <!-- Step 3: KYC Verification -->
                    <div class="auth-step" id="step-3">
                        <h5 class="mb-4 d-flex align-items-center"><i class="bi bi-shield-check me-2"></i> Vérification d'identité</h5>
                        <div class="row mb-3 p-3 border rounded bg-light mx-0">
                            <div class="col-md-6 mb-3">
                                <label for="piece_identite_recto" class="form-label">Pièce d'identité (Recto) <span class="text-danger">*</span></label>
                                <input type="file" class="form-control @error('piece_identite_recto') is-invalid @enderror" id="piece_identite_recto" name="piece_identite_recto" required accept="image/*">
                                @error('piece_identite_recto') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="piece_identite_verso" class="form-label">Pièce d'identité (Verso) <span class="text-danger">*</span></label>
                                <input type="file" class="form-control @error('piece_identite_verso') is-invalid @enderror" id="piece_identite_verso" name="piece_identite_verso" required accept="image/*">
                                @error('piece_identite_verso') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-12 mb-3">
                                <label class="form-label">Capture d'un Selfie Live <span class="text-danger">*</span></label>
                                <div class="d-flex flex-column align-items-center text-center">
                                    <div id="cameraPreview" class="border rounded mb-2 bg-dark d-none" style="width: 320px; height: 240px; position: relative;">
                                        <video id="video" width="320" height="240" autoplay class="rounded"></video>
                                        <canvas id="canvas" width="320" height="240" class="d-none rounded"></canvas>
                                    </div>
                                    <img id="selfiePreview" src="" class="border rounded mb-2 d-none" style="width: 320px; height: 240px; object-fit: cover;">
                                    
                                    <div class="btn-group">
                                        <button type="button" id="startCamera" class="btn btn-outline-primary btn-sm">
                                            <i class="bi bi-camera"></i> Activer la caméra
                                        </button>
                                        <button type="button" id="takePhoto" class="btn btn-warning btn-sm d-none">
                                            <i class="bi bi-camera-fill"></i> Prendre la photo
                                        </button>
                                        <button type="button" id="retakePhoto" class="btn btn-outline-secondary btn-sm d-none">
                                            <i class="bi bi-arrow-counterclockwise"></i> Reprendre
                                        </button>
                                    </div>
                                    <input type="hidden" name="selfie_base64" id="selfie_base64">
                                    @error('selfie_base64') <div class="text-danger mt-1 small">{{ $message }}</div> @enderror
                                </div>
                            </div>
                        </div>

                        <div class="step-footer">
                            <button type="button" class="btn btn-outline-secondary prev-step">Précédent</button>
                            <button type="button" class="btn btn-primary flex-grow-1 next-step">Suivant <i class="bi bi-arrow-right ms-2"></i></button>
                        </div>
                    </div>

                    <!-- Step 4: Compte & Segment -->
                    <div class="auth-step" id="step-4">
                        <h5 class="mb-4 d-flex align-items-center"><i class="bi bi-gear me-2"></i> Paramètres du Compte</h5>
                        <div class="mb-3">
                            <label for="segment" class="form-label">Segment</label>
                            <select class="form-select @error('segment') is-invalid @enderror" id="segment" name="segment">
                                <option value="">-- Aucun segment --</option>
                                @foreach($segments as $seg)
                                    <option value="{{ $seg }}" {{ old('segment') === $seg ? 'selected' : '' }}>{{ $seg }}</option>
                                @endforeach
                                <option value="__nouveau__" {{ old('segment') === '__nouveau__' ? 'selected' : '' }}>+ Ajouter un nouveau segment</option>
                            </select>
                            <div id="nouveauSegmentContainer" style="display: none; margin-top: 0.5rem;">
                                <input type="text" class="form-control" id="nouveauSegment" name="nouveau_segment" value="{{ old('nouveau_segment') }}" placeholder="Nom du nouveau segment">
                            </div>
                            @error('segment') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="password" class="form-label">Mot de passe <span class="text-danger">*</span></label>
                                <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required>
                                @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6 mb-4">
                                <label for="password_confirmation" class="form-label">Confirmez <span class="text-danger">*</span></label>
                                <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                            </div>
                        </div>

                        <div class="step-footer">
                            <button type="button" class="btn btn-outline-secondary prev-step">Précédent</button>
                            <button type="submit" class="btn btn-primary flex-grow-1">
                                <i class="bi bi-check-circle me-2"></i> Créer mon compte
                            </button>
                        </div>
                    </div>
                    
                    <div class="text-center p-3 border-top mt-4">
                        <p class="mb-0" style="font-size: 0.9rem;">Déjà inscrit ? <a href="{{ route('membre.login') }}" class="text-decoration-none fw-bold" style="color: var(--aladin-blue);">Connectez-vous ici</a></p>
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
    
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('registrationForm');
        const steps = document.querySelectorAll('.auth-step');
        const circles = document.querySelectorAll('.step-circle');
        const nextBtns = document.querySelectorAll('.next-step');
        const prevBtns = document.querySelectorAll('.prev-step');
        let currentStep = 1;

        function updateSteps() {
            steps.forEach(step => step.classList.remove('active'));
            circles.forEach(circle => {
                const stepNum = parseInt(circle.dataset.step);
                circle.classList.remove('active', 'completed');
                if (stepNum === currentStep) circle.classList.add('active');
                if (stepNum < currentStep) circle.classList.add('completed');
            });
            document.getElementById(`step-${currentStep}`).classList.add('active');
            window.scrollTo(0, 0);
        }

        function validateStep(stepNum) {
            const stepEl = document.getElementById(`step-${stepNum}`);
            const inputs = stepEl.querySelectorAll('input[required], select[required], textarea[required]');
            let isValid = true;

            inputs.forEach(input => {
                if (!input.value.trim()) {
                    input.classList.add('is-invalid');
                    isValid = false;
                } else {
                    input.classList.remove('is-invalid');
                }
            });

            // Validation spécifique Selfie
            if (stepNum === 3) {
                const selfie = document.getElementById('selfie_base64');
                if (!selfie.value) {
                    alert("Veuillez prendre un selfie pour continuer.");
                    isValid = false;
                }
            }

            return isValid;
        }

        nextBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                if (validateStep(currentStep)) {
                    currentStep++;
                    updateSteps();
                }
            });
        });

        prevBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                currentStep--;
                updateSteps();
            });
        });

        const segmentSelect = document.getElementById('segment');
        const nouveauSegmentContainer = document.getElementById('nouveauSegmentContainer');

        segmentSelect.addEventListener('change', function() {
            if (this.value === '__nouveau__') {
                nouveauSegmentContainer.style.display = 'block';
                document.getElementById('nouveauSegment').focus();
            } else {
                nouveauSegmentContainer.style.display = 'none';
            }
        });

        if (segmentSelect.value === '__nouveau__') {
            nouveauSegmentContainer.style.display = 'block';
        }

        // --- Geolocation ---
        const detectLocationBtn = document.getElementById('detectLocation');
        const latInput = document.getElementById('latitude');
        const lonInput = document.getElementById('longitude');

        detectLocationBtn.addEventListener('click', function() {
            if (navigator.geolocation) {
                detectLocationBtn.innerHTML = '<i class="bi bi-hourglass-split"></i>';
                navigator.geolocation.getCurrentPosition(function(position) {
                    latInput.value = position.coords.latitude;
                    lonInput.value = position.coords.longitude;
                    detectLocationBtn.innerHTML = '<i class="bi bi-check-lg text-success"></i>';
                }, function(error) {
                    alert('Erreur de géolocalisation: ' + error.message);
                    detectLocationBtn.innerHTML = '<i class="bi bi-geo-alt"></i>';
                });
            } else {
                alert("La géolocalisation n'est pas supportée par votre navigateur.");
            }
        });

        // --- Camera & Selfie ---
        const video = document.getElementById('video');
        const canvas = document.getElementById('canvas');
        const startBtn = document.getElementById('startCamera');
        const takeBtn = document.getElementById('takePhoto');
        const retakeBtn = document.getElementById('retakePhoto');
        const previewImg = document.getElementById('selfiePreview');
        const base64Input = document.getElementById('selfie_base64');
        const cameraBox = document.getElementById('cameraPreview');
        let stream = null;

        startBtn.addEventListener('click', async function() {
            try {
                stream = await navigator.mediaDevices.getUserMedia({ video: true });
                video.srcObject = stream;
                cameraBox.classList.remove('d-none');
                takeBtn.classList.remove('d-none');
                startBtn.classList.add('d-none');
            } catch (err) {
                alert("Impossible d'accéder à la caméra: " + err.message);
            }
        });

        takeBtn.addEventListener('click', function() {
            const context = canvas.getContext('2d');
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            context.drawImage(video, 0, 0, canvas.width, canvas.height);
            
            const dataUrl = canvas.toDataURL('image/jpeg');
            base64Input.value = dataUrl;
            previewImg.src = dataUrl;
            
            previewImg.classList.remove('d-none');
            cameraBox.classList.add('d-none');
            takeBtn.classList.add('d-none');
            retakeBtn.classList.remove('d-none');
            
            if (stream) {
                stream.getTracks().forEach(track => track.stop());
            }
        });

        retakeBtn.addEventListener('click', function() {
            previewImg.classList.add('d-none');
            retakeBtn.classList.add('d-none');
            startBtn.classList.remove('d-none');
            base64Input.value = '';
        });
    });
    </script>
</body>
</html>
