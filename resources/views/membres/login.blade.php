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
    <!-- Google Fonts - Ubuntu Light -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Ubuntu:wght@300;400;500;700&display=swap" rel="stylesheet">
    
    <style>
        * {
            font-family: 'Ubuntu', sans-serif;
            font-weight: 300;
        }
        
        body {
            margin: 0;
            padding: 0;
            min-height: 100vh;
            background-image: url('{{ asset('images/background.jpg') }}');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
            position: relative;
        }
        
        
        .login-container {
            position: relative;
            z-index: 1;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }
        
        .login-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
            width: 100%;
            max-width: 420px;
            padding: 1.5rem;
        }
        
        .login-header {
            text-align: center;
            margin-bottom: 1.25rem;
        }
        
        .login-header h2 {
            color: var(--primary-dark-blue, #1e3a5f);
            font-weight: 300;
            font-family: 'Ubuntu', sans-serif;
            font-size: 1.25rem;
            margin-bottom: 0.25rem;
        }
        
        .login-header p {
            color: #666;
            font-size: 0.75rem;
            font-weight: 300;
            font-family: 'Ubuntu', sans-serif;
        }
        
        .form-label {
            font-weight: 300;
            font-family: 'Ubuntu', sans-serif;
            font-size: 0.8rem;
            color: #333;
            margin-bottom: 0.35rem;
        }
        
        .form-control {
            font-weight: 300;
            font-family: 'Ubuntu', sans-serif;
            font-size: 0.8rem;
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 0.4rem 0.6rem;
        }
        
        .form-control:focus {
            border-color: var(--primary-dark-blue, #1e3a5f);
            box-shadow: 0 0 0 0.2rem rgba(30, 58, 95, 0.25);
        }
        
        .btn-primary {
            background-color: var(--primary-dark-blue, #1e3a5f);
            border-color: var(--primary-dark-blue, #1e3a5f);
            font-weight: 300;
            font-family: 'Ubuntu', sans-serif;
            font-size: 0.8rem;
            padding: 0.4rem;
        }
        
        .btn-primary:hover {
            background-color: var(--primary-blue, #2c5282);
            border-color: var(--primary-blue, #2c5282);
        }
        
        .alert {
            font-weight: 300;
            font-family: 'Ubuntu', sans-serif;
            font-size: 0.75rem;
            border-radius: 5px;
            padding: 0.5rem 0.75rem;
        }
        
        .form-check-label {
            font-weight: 300;
            font-family: 'Ubuntu', sans-serif;
            font-size: 0.75rem;
        }
        
        .mb-3 {
            margin-bottom: 0.75rem !important;
        }
        
        .invalid-feedback {
            font-weight: 300;
            font-family: 'Ubuntu', sans-serif;
            font-size: 0.7rem;
        }
        
        .login-register-section {
            font-family: 'Ubuntu', sans-serif;
            font-weight: 300;
        }
        .login-register-section p,
        .login-register-section .btn,
        .login-register-section a {
            font-family: 'Ubuntu', sans-serif;
            font-weight: 300;
        }
        .login-register-section .btn-success {
            font-size: 0.8rem;
            font-family: 'Ubuntu', sans-serif;
            font-weight: 300;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <h2><i class="bi bi-person-circle"></i> Connexion Membre</h2>
                <p>Accédez à vos cotisations</p>
            </div>
            
            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0" style="font-size: 0.8rem;">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @if(session('unverified_email'))
                    <form method="POST" action="{{ route('membre.verification.resend') }}" class="mt-2">
                        @csrf
                        <input type="hidden" name="email" value="{{ session('unverified_email') }}">
                        <button type="submit" class="btn btn-link btn-sm p-0 login-register-section" style="font-size: 0.8rem;">
                            <i class="bi bi-envelope"></i> Renvoyer le lien de vérification
                        </button>
                    </form>
                @endif
            @endif
            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif
            
            <form method="POST" action="{{ route('membre.login') }}">
                @csrf
                
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" 
                           class="form-control @error('email') is-invalid @enderror" 
                           id="email" 
                           name="email" 
                           value="{{ old('email') }}" 
                           required 
                           autofocus>
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="mb-3">
                    <label for="password" class="form-label">Mot de passe</label>
                    <input type="password" 
                           class="form-control @error('password') is-invalid @enderror" 
                           id="password" 
                           name="password" 
                           required>
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="mb-3 form-check">
                    <input type="checkbox" class="form-check-input" id="remember" name="remember">
                    <label class="form-check-label" for="remember">
                        Se souvenir de moi
                    </label>
                </div>
                
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-box-arrow-in-right"></i> Se connecter
                </button>
            </form>
            
            <div class="text-center mt-3 login-register-section">
                <p class="mb-2" style="font-size: 0.9rem; color: #666;">Vous n'avez pas encore de compte ?</p>
                <a href="{{ route('membre.register') }}" class="btn btn-success btn-sm me-2">
                    <i class="bi bi-person-plus"></i> Créer un compte
                </a>
                
                <div class="mt-3">
                    <a href="{{ route('admin.login') }}" class="text-decoration-none" style="font-size: 0.75rem; color: #666;">
                        <i class="bi bi-shield-check"></i> Accès Administrateur
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
