<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sérénité - Sécurité</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Ubuntu:wght@300;400;500;700&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Ubuntu', sans-serif; font-weight: 300; }
        body { min-height: 100vh; background-color: #f5f7fa; display: flex; align-items: center; justify-content: center; }
        .card { max-width: 500px; width: 100%; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.1); border-radius: 8px; }
        .card-header { background: #1e3a5f; color: white; border-radius: 8px 8px 0 0 !important; padding: 1.5rem; text-align: center; }
        .form-control { font-size: 0.9rem; padding: 0.5rem 0.8rem; }
    </style>
</head>
<body>
    <div class="card">
        <div class="card-header">
            <h4 class="mb-0"><i class="bi bi-shield-lock"></i> Expiration du Mot de passe</h4>
        </div>
        <div class="card-body p-4">
            @if(session('warning'))
                <div class="alert alert-warning small">
                    <i class="bi bi-exclamation-triangle"></i> {{ session('warning') }}
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger small">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <p class="text-muted small mb-4">
                Pour des raisons de sécurité, vous devez changer votre mot de passe périodiquement. Veuillez en créer un nouveau.
            </p>

            <form method="POST" action="{{ route('password.expire.update') }}">
                @csrf
                <div class="mb-3">
                    <label class="form-label small">Mot de passe actuel</label>
                    <input type="password" name="current_password" class="form-control" required autofocus>
                </div>
                <div class="mb-3">
                    <label class="form-label small">Nouveau mot de passe</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <div class="mb-4">
                    <label class="form-label small">Confirmez le nouveau mot de passe</label>
                    <input type="password" name="password_confirmation" class="form-control" required>
                </div>

                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary" style="background:#1e3a5f; border:none;">
                        Mettre à jour mon mot de passe
                    </button>
                    <!-- Permettre la déconnexion si l'utilisateur ne veut pas changer -->
                    <a href="{{ route(session()->get('password_expire_guard', 'web') === 'membre' ? 'membre.logout' : 'admin.logout') }}" class="btn btn-outline-secondary btn-sm mt-3" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        Se déconnecter
                    </a>
                </div>
            </form>

            <form id="logout-form" action="{{ route(session()->get('password_expire_guard', 'web') === 'membre' ? 'membre.logout' : 'admin.logout') }}" method="POST" class="d-none">
                @csrf
            </form>
        </div>
    </div>
</body>
</html>
