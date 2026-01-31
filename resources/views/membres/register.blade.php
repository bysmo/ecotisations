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
    
    <title>{{ $appNomComplet }} - Inscription Membre</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Ubuntu:wght@300;400;500;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary-dark-blue: #1e3a5f;
            --primary-blue: #2c5282;
        }
        * {
            font-family: 'Ubuntu', sans-serif;
            font-weight: 300;
        }
        body {
            background-color: #f5f7fa;
            font-family: 'Ubuntu', sans-serif;
            font-weight: 300;
            min-height: 100vh;
            padding: 1.5rem 0;
        }
        .page-header {
            margin-bottom: 1rem;
        }
        .page-header h1 {
            color: var(--primary-dark-blue);
            font-weight: 300;
            font-family: 'Ubuntu', sans-serif;
            font-size: 1.25rem;
        }
        .page-header h1 i {
            font-size: 0.9rem;
        }
        .card {
            border: none;
            border-radius: 4px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
            font-family: 'Ubuntu', sans-serif;
            font-weight: 300;
        }
        .card-header {
            background-color: var(--primary-dark-blue);
            color: white;
            border-radius: 4px 4px 0 0 !important;
            padding: 0.4rem 0.6rem;
            font-weight: 400;
            font-size: 0.75rem;
            font-family: 'Ubuntu', sans-serif;
            line-height: 1.3;
        }
        .card-header i {
            font-size: 0.75rem;
        }
        .card-body {
            padding: 0.75rem;
            font-family: 'Ubuntu', sans-serif;
            font-weight: 300;
            font-size: 0.8rem;
        }
        .form-label {
            font-weight: 300;
            font-family: 'Ubuntu', sans-serif;
            font-size: 0.8rem;
            color: #333;
            margin-bottom: 0.35rem;
        }
        .form-control, .form-select {
            font-weight: 300;
            font-family: 'Ubuntu', sans-serif;
            font-size: 0.8rem;
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 0.4rem 0.6rem;
        }
        .form-control:focus, .form-select:focus {
            border-color: var(--primary-dark-blue);
            box-shadow: 0 0 0 0.2rem rgba(30, 58, 95, 0.25);
        }
        .btn-primary {
            background-color: var(--primary-dark-blue);
            border-color: var(--primary-dark-blue);
            font-weight: 300;
            font-family: 'Ubuntu', sans-serif;
            font-size: 0.8rem;
        }
        .btn-primary:hover {
            background-color: var(--primary-blue);
            border-color: var(--primary-blue);
        }
        .btn-secondary {
            font-weight: 300;
            font-family: 'Ubuntu', sans-serif;
            font-size: 0.8rem;
        }
        .invalid-feedback {
            font-weight: 300;
            font-family: 'Ubuntu', sans-serif;
            font-size: 0.7rem;
        }
        .form-text {
            font-size: 0.7rem;
            color: #6c757d;
        }
        .register-footer-links {
            font-family: 'Ubuntu', sans-serif;
            font-weight: 300;
            font-size: 0.8rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="page-header">
            <h1><i class="bi bi-plus-circle"></i> Créer un Nouveau Membre</h1>
        </div>

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <i class="bi bi-info-circle"></i> Informations du Membre
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('membre.register') }}">
                            @csrf
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="nom" class="form-label">Nom <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           class="form-control @error('nom') is-invalid @enderror" 
                                           id="nom" name="nom" value="{{ old('nom') }}" required>
                                    @error('nom')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="prenom" class="form-label">Prénom <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           class="form-control @error('prenom') is-invalid @enderror" 
                                           id="prenom" name="prenom" value="{{ old('prenom') }}" required>
                                    @error('prenom')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                    <input type="email" 
                                           class="form-control @error('email') is-invalid @enderror" 
                                           id="email" name="email" value="{{ old('email') }}" required>
                                    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="telephone" class="form-label">Téléphone</label>
                                    <input type="text" 
                                           class="form-control @error('telephone') is-invalid @enderror" 
                                           id="telephone" name="telephone" value="{{ old('telephone') }}">
                                    @error('telephone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="adresse" class="form-label">Adresse</label>
                                <textarea class="form-control @error('adresse') is-invalid @enderror" 
                                          id="adresse" name="adresse" rows="2">{{ old('adresse') }}</textarea>
                                @error('adresse')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            
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
                                    <input type="text" class="form-control form-control-sm" id="nouveauSegment" 
                                           name="nouveau_segment" value="{{ old('nouveau_segment') }}" placeholder="Nom du nouveau segment">
                                    <small class="form-text text-muted">Saisissez le nom du nouveau segment</small>
                                </div>
                                @error('segment')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                @error('nouveau_segment')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="password" class="form-label">Mot de passe <span class="text-danger">*</span></label>
                                    <input type="password" 
                                           class="form-control @error('password') is-invalid @enderror" 
                                           id="password" name="password" required>
                                    @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    <small class="form-text text-muted">Minimum 6 caractères</small>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="password_confirmation" class="form-label">Confirmer le mot de passe <span class="text-danger">*</span></label>
                                    <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                                </div>
                            </div>
                            
                            <div class="d-flex justify-content-between">
                                <a href="{{ route('membre.login') }}" class="btn btn-secondary">
                                    <i class="bi bi-arrow-left"></i> Retour
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-check-circle"></i> Créer mon compte
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                
                <div class="text-center mt-3 register-footer-links">
                    <p class="mb-0">Vous avez déjà un compte ? <a href="{{ route('membre.login') }}"><i class="bi bi-box-arrow-in-right"></i> Se connecter</a></p>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <i class="bi bi-info-circle"></i> À propos des Membres
                    </div>
                    <div class="card-body">
                        <h6 class="mb-3" style="font-weight: 300; font-family: 'Ubuntu', sans-serif; color: var(--primary-dark-blue);">
                            <i class="bi bi-person"></i> Qu'est-ce qu'un membre ?
                        </h6>
                        <p style="font-size: 0.75rem; line-height: 1.5; font-weight: 300; font-family: 'Ubuntu', sans-serif; color: #666;">
                            Un membre est une personne inscrite dans l'organisation qui peut effectuer des paiements de cotisations. Chaque membre reçoit un numéro unique et peut se connecter après validation de son adresse email.
                        </p>
                        <h6 class="mt-4 mb-3" style="font-weight: 300; font-family: 'Ubuntu', sans-serif; color: var(--primary-dark-blue);">
                            <i class="bi bi-shield-check"></i> Informations requises
                        </h6>
                        <ul style="font-size: 0.75rem; line-height: 1.8; font-weight: 300; font-family: 'Ubuntu', sans-serif; color: #666; padding-left: 1.2rem;">
                            <li><strong>Nom et prénom :</strong> Identité complète</li>
                            <li><strong>Email :</strong> Pour les notifications et connexion (sera vérifié par mail)</li>
                            <li><strong>Mot de passe :</strong> Minimum 6 caractères</li>
                        </ul>
                        <h6 class="mt-4 mb-3" style="font-weight: 300; font-family: 'Ubuntu', sans-serif; color: var(--primary-dark-blue);">
                            <i class="bi bi-envelope-check"></i> Validation par email
                        </h6>
                        <p style="font-size: 0.75rem; line-height: 1.5; font-weight: 300; font-family: 'Ubuntu', sans-serif; color: #666;">
                            Après l'inscription, un lien de vérification vous sera envoyé par email. Cliquez sur ce lien pour activer votre compte et pouvoir vous connecter.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const segmentSelect = document.getElementById('segment');
        const nouveauSegmentContainer = document.getElementById('nouveauSegmentContainer');
        const nouveauSegmentInput = document.getElementById('nouveauSegment');
        segmentSelect.addEventListener('change', function() {
            if (this.value === '__nouveau__') {
                nouveauSegmentContainer.style.display = 'block';
                if (nouveauSegmentInput) nouveauSegmentInput.required = true;
            } else {
                nouveauSegmentContainer.style.display = 'none';
                if (nouveauSegmentInput) { nouveauSegmentInput.required = false; nouveauSegmentInput.value = ''; }
            }
        });
        if (segmentSelect.value === '__nouveau__') {
            nouveauSegmentContainer.style.display = 'block';
            if (nouveauSegmentInput) nouveauSegmentInput.required = true;
        }
    });
    </script>
</body>
</html>
