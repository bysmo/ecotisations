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
    
    <title>FlexFin - Créer votre compte</title>
    
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
        .step-container { display: none; }
        .step-container.active { display: block; animation: fadeIn 0.5s ease; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        
        .stepper {
            display: flex;
            justify-content: space-between;
            margin-bottom: 2rem;
            position: relative;
        }
        .stepper::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 1px;
            background: #eee;
            z-index: 1;
        }
        .step-item {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background: #fff;
            border: 1px solid #ddd;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.8rem;
            z-index: 2;
            color: #999;
            transition: all 0.3s ease;
        }
        .step-item.active {
            background: var(--aladin-blue, #1e3a5f);
            border-color: var(--aladin-blue, #1e3a5f);
            color: white;
            box-shadow: 0 0 0 5px rgba(30, 58, 95, 0.1);
        }
        .step-item.completed {
            background: #ffc107;
            border-color: #ffc107;
            color: white;
        }
        
        .camera-box {
            width: 100%;
            max-width: 400px;
            aspect-ratio: 4/3;
            background: #f8f9fa;
            border: 2px dashed #ddd;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 1rem auto;
            position: relative;
            overflow: hidden;
        }
        #webcam { width: 100%; height: 100%; object-fit: cover; }
        .kyc-upload-preview {
            width: 100%;
            height: 120px;
            background: #f8f9fa;
            border: 1px dashed #ccc;
            border-radius: 5px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            font-size: 0.7rem;
            color: #666;
            cursor: pointer;
        }
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
            
            <div class="mt-5">
                <div class="d-flex align-items-center mb-3">
                    <div class="rounded-circle bg-warning p-2 me-3"><i class="bi bi-check2 text-white"></i></div>
                    <span>Gérez vos cotisations en ligne</span>
                </div>
                <div class="d-flex align-items-center mb-3">
                    <div class="rounded-circle bg-warning p-2 me-3"><i class="bi bi-shield-lock text-white"></i></div>
                    <span>Transactions sécurisées</span>
                </div>
                <div class="d-flex align-items-center mb-3">
                    <div class="rounded-circle bg-warning p-2 me-3"><i class="bi bi-bell text-white"></i></div>
                    <span>Alertes et notifications</span>
                </div>
            </div>
        </div>

        <!-- Register Form -->
        <div class="auth-content">
            <div class="auth-card" style="max-width: 550px;">
                <div class="mb-4">
                    <h2 class="auth-title">Créer votre compte</h2>
                    <p class="auth-subtitle">Rejoignez FlexFin pour gérer vos finances</p>
                </div>
                
                <!-- Steps UI -->
                <div class="stepper">
                    <div class="step-item active" id="dot-1">1</div>
                    <div class="step-item" id="dot-2">2</div>
                    <div class="step-item" id="dot-3">3</div>
                    <div class="step-item" id="dot-4">4</div>
                </div>

                <form id="registerForm" method="POST" action="{{ route('membre.register') }}" enctype="multipart/form-data">
                    @csrf
                    
                    <!-- STEP 1: Identification -->
                    <div class="step-container active" id="step-1">
                        <h5 class="mb-3 d-flex align-items-center" style="font-size: 1rem;">
                            <i class="bi bi-person-badge me-2 text-primary"></i> Identification
                        </h5>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Nom <span class="text-danger">*</span></label>
                                <input type="text" name="nom" class="form-control" placeholder="Entrez votre nom" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Prénom <span class="text-danger">*</span></label>
                                <input type="text" name="prenom" class="form-control" placeholder="Entrez votre prénom" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Date de Naissance <span class="text-danger">*</span></label>
                                <input type="date" name="date_naissance" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Sexe <span class="text-danger">*</span></label>
                                <select name="sexe" class="form-select" required>
                                    <option value="M">Masculin</option>
                                    <option value="F">Féminin</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Lieu de Naissance</label>
                                <input type="text" name="lieu_naissance" class="form-control" placeholder="Ville ou Localité">
                            </div>
                        </div>
                        <div class="d-flex justify-content-end mt-4">
                            <button type="button" class="btn btn-primary px-4" onclick="nextStep(2)">Suivant <i class="bi bi-arrow-right ms-2"></i></button>
                        </div>
                    </div>

                    <!-- STEP 2: Contact & Localisation -->
                    <div class="step-container" id="step-2">
                        <h5 class="mb-3 d-flex align-items-center" style="font-size: 1rem;">
                            <i class="bi bi-geo-alt me-2 text-primary"></i> Contact & Localisation
                        </h5>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" name="email" class="form-control" placeholder="votre@email.com" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Téléphone</label>
                                <input type="text" name="telephone" class="form-control" placeholder="+226 ...">
                            </div>
                            <div class="col-12">
                                <label class="form-label">Adresse Détaillée <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <textarea name="adresse" class="form-control" rows="2" placeholder="Votre adresse physique précise" required></textarea>
                                    <span class="input-group-text bg-white"><i class="bi bi-geo text-muted"></i></span>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex justify-content-between mt-4">
                            <button type="button" class="btn btn-outline-secondary px-4" onclick="prevStep(1)">Précédent</button>
                            <button type="button" class="btn btn-primary px-4" onclick="nextStep(3)">Suivant <i class="bi bi-arrow-right ms-2"></i></button>
                        </div>
                    </div>

                    <!-- STEP 3: Vérification d'identité -->
                    <div class="step-container" id="step-3">
                        <h5 class="mb-3 d-flex align-items-center" style="font-size: 1rem;">
                            <i class="bi bi-shield-check me-2 text-primary"></i> Vérification d'identité
                        </h5>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Pièce d'identité (Recto) <span class="text-danger">*</span></label>
                                <div class="kyc-upload-preview" onclick="document.getElementById('id_recto').click()">
                                    <i class="bi bi-cloud-arrow-up fs-3 mb-1"></i>
                                    <span>Choisir le fichier</span>
                                    <input type="file" id="id_recto" name="piece_identite_recto" class="d-none">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Pièce d'identité (Verso) <span class="text-danger">*</span></label>
                                <div class="kyc-upload-preview" onclick="document.getElementById('id_verso').click()">
                                    <i class="bi bi-cloud-arrow-up fs-3 mb-1"></i>
                                    <span>Choisir le fichier</span>
                                    <input type="file" id="id_verso" name="piece_identite_verso" class="d-none">
                                </div>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Capture d'un Selfie Live <span class="text-danger">*</span></label>
                                <div class="camera-box">
                                    <i class="bi bi-camera text-muted fs-1"></i>
                                    <!-- Si JS actif, on mettra ici la webcam -->
                                </div>
                                <div class="text-center">
                                    <button type="button" class="btn btn-light btn-sm"><i class="bi bi-arrow-repeat me-1"></i> Reprendre</button>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex justify-content-between mt-4">
                            <button type="button" class="btn btn-outline-secondary px-4" onclick="prevStep(2)">Précédent</button>
                            <button type="button" class="btn btn-primary px-4" onclick="nextStep(4)">Suivant <i class="bi bi-arrow-right ms-2"></i></button>
                        </div>
                    </div>

                    <!-- STEP 4: Paramètres du Compte -->
                    <div class="step-container" id="step-4">
                        <h5 class="mb-3 d-flex align-items-center" style="font-size: 1rem;">
                            <i class="bi bi-gear me-2 text-primary"></i> Paramètres du Compte
                        </h5>
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label">Segment</label>
                                <select name="segment" class="form-select">
                                    @foreach($segments as $seg)
                                        <option value="{{ $seg }}">{{ $seg }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Mot de passe <span class="text-danger">*</span></label>
                                <input type="password" name="password" class="form-control" placeholder="••••••" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Confirmez <span class="text-danger">*</span></label>
                                <input type="password" name="password_confirmation" class="form-control" placeholder="••••••" required>
                            </div>
                        </div>
                        <div class="d-flex justify-content-between mt-4">
                            <button type="button" class="btn btn-outline-secondary px-4" onclick="prevStep(3)">Précédent</button>
                            <button type="submit" class="btn btn-primary px-4"><i class="bi bi-check-circle me-2"></i> Créer mon compte</button>
                        </div>
                    </div>

                    <div class="text-center mt-4">
                        <p class="mb-1" style="font-size: 0.9rem;">Déjà inscrit ? <a href="{{ route('membre.login') }}" class="text-decoration-none fw-bold" style="color: var(--aladin-blue);">Connectez-vous ici</a></p>
                    </div>
                </form>

                <div class="auth-footer">
                    <p class="mb-1">© 2026 FlexFin+</p>
                    <p>Powered by Aladin Technologies Solutions (ALTES)</p>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        function nextStep(step) {
            document.querySelectorAll('.step-container').forEach(c => c.classList.remove('active'));
            document.getElementById('step-' + step).classList.add('active');
            
            document.querySelectorAll('.step-item').forEach((dot, index) => {
                if (index + 1 < step) dot.classList.add('completed');
                else if (index + 1 === step) dot.classList.add('active');
                else { dot.classList.remove('active'); dot.classList.remove('completed'); }
            });
        }
        
        function prevStep(step) {
            nextStep(step);
        }
    </script>
</body>
</html>
