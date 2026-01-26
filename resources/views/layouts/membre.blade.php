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
    
    <title>{{ $appNomComplet }} - @yield('title', 'Mon Espace Membre')</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <!-- Google Fonts - Ubuntu Light -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Ubuntu:wght@300;400;500;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary-dark-blue: #1e3a5f;
            --primary-blue: #2c5282;
            --light-blue: #4299e1;
            --sidebar-width: 260px;
        }
        
        * {
            font-family: 'Ubuntu', sans-serif;
            font-weight: 300;
        }
        
        body {
            background-color: #f5f7fa;
            font-family: 'Ubuntu', sans-serif;
            font-weight: 300;
        }
        
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: var(--sidebar-width);
            background: var(--primary-dark-blue);
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
            z-index: 1000;
            overflow-y: auto;
        }
        
        .sidebar-header {
            padding: 1rem 1.25rem;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        
        .sidebar-header h4 {
            color: white;
            font-weight: 300;
            margin: 0;
            font-size: 0.85rem;
            font-family: 'Ubuntu', sans-serif;
        }
        
        .sidebar-menu {
            padding: 0.5rem 0;
        }
        
        .sidebar-menu .nav-link {
            color: rgba(255,255,255,0.8);
            padding: 0.5rem 1.25rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            transition: all 0.3s ease;
            border-left: 3px solid transparent;
            font-weight: 300;
            font-size: 0.75rem;
        }
        
        .sidebar-menu .nav-link:hover {
            background-color: rgba(255,255,255,0.1);
            color: white;
            border-left-color: var(--light-blue);
        }
        
        .sidebar-menu .nav-link.active {
            background-color: rgba(255,255,255,0.15);
            color: white;
            border-left-color: white;
            font-weight: 400;
        }
        
        .sidebar-menu .nav-link i {
            font-size: 0.85rem;
            width: 18px;
            text-align: center;
        }
        
        .top-bar {
            position: fixed;
            top: 0;
            left: var(--sidebar-width);
            right: 0;
            height: 50px;
            background: white;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            z-index: 999;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 1rem;
            font-family: 'Ubuntu', sans-serif;
            font-weight: 300;
        }
        
        .top-bar-left {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        
        .top-bar-right {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        
        .logout-btn {
            background: none;
            border: none;
            color: var(--primary-dark-blue);
            font-size: 0.8rem;
            cursor: pointer;
            padding: 0.4rem 0.8rem;
            border-radius: 4px;
            transition: background-color 0.2s ease;
            font-weight: 300;
            font-family: 'Ubuntu', sans-serif;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .logout-btn:hover {
            background-color: rgba(30, 58, 95, 0.1);
        }
        
        .main-content {
            margin-left: var(--sidebar-width);
            margin-top: 50px;
            padding: 1.5rem;
            min-height: calc(100vh - 50px);
        }
        
        .page-header {
            margin-bottom: 1.5rem;
        }
        
        .page-header h1 {
            color: var(--primary-dark-blue);
            font-weight: 300;
            font-size: 1.5rem;
            margin: 0;
            font-family: 'Ubuntu', sans-serif;
        }
        
        .card {
            border: none;
            border-radius: 6px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            font-family: 'Ubuntu', sans-serif;
            font-weight: 300;
            margin-bottom: 1.5rem;
        }
        
        .card-header {
            background-color: var(--primary-dark-blue);
            color: white;
            border-radius: 6px 6px 0 0 !important;
            padding: 0.75rem 1rem;
            font-weight: 300;
            font-size: 0.85rem;
            font-family: 'Ubuntu', sans-serif;
        }
        
        .card-body {
            padding: 1rem;
            font-size: 0.85rem;
        }
        
        .table {
            font-family: 'Ubuntu', sans-serif;
            font-weight: 300;
            font-size: 0.8rem;
        }
        
        .table thead th {
            background-color: var(--primary-dark-blue);
            color: white;
            font-weight: 300;
            padding: 0.5rem 0.75rem;
            font-size: 0.75rem;
        }
        
        .table tbody td {
            padding: 0.5rem 0.75rem;
            vertical-align: middle;
        }
        
        /* Styles pour la pagination personnalisée */
        .pagination-custom {
            font-family: 'Ubuntu', sans-serif;
            font-weight: 300;
        }
        
        .pagination-custom .pagination {
            margin-bottom: 0;
            font-size: 0.75rem;
        }
        
        .pagination-custom .page-link {
            color: white;
            background-color: var(--primary-dark-blue);
            border-color: var(--primary-dark-blue);
            font-family: 'Ubuntu', sans-serif;
            font-weight: 300;
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
            line-height: 1.3;
        }
        
        .pagination-custom .page-link:hover {
            color: white;
            background-color: var(--primary-blue);
            border-color: var(--primary-blue);
        }
        
        .pagination-custom .page-item.active .page-link {
            background-color: var(--primary-blue);
            border-color: var(--primary-blue);
            color: white;
        }
        
        .pagination-custom .page-item.disabled .page-link {
            background-color: rgba(30, 58, 95, 0.5);
            border-color: rgba(30, 58, 95, 0.5);
            color: rgba(255, 255, 255, 0.6);
        }
        
        /* Styles pour les boutons */
        .btn {
            font-family: 'Ubuntu', sans-serif;
            font-weight: 300;
            font-size: 0.8rem;
            padding: 0.4rem 0.8rem;
            line-height: 1.3;
        }
        
        .btn-sm {
            font-size: 0.75rem;
            padding: 0.3rem 0.6rem;
            line-height: 1.2;
        }
        
        .btn-lg {
            font-size: 0.9rem;
            padding: 0.5rem 1rem;
        }
        
        .btn-primary {
            background-color: var(--primary-dark-blue);
            border-color: var(--primary-dark-blue);
            font-weight: 300;
        }
        
        .btn-primary:hover {
            background-color: var(--primary-blue);
            border-color: var(--primary-blue);
        }
        
        .btn i {
            font-size: 0.8rem;
        }
        
        .btn-sm i {
            font-size: 0.75rem;
        }
    </style>
    
    @stack('styles')
</head>
<body>
    @php
        $membre = auth('membre')->user();
        $appNom = \App\Models\AppSetting::get('nom_app', 'E-Cotisations');
    @endphp
    
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-header">
            <h4><i class="bi bi-cash-coin"></i>  {{ $appNom }}</h4>
        </div>
        <nav class="sidebar-menu">
            <a href="{{ route('membre.dashboard') }}" class="nav-link {{ request()->routeIs('membre.dashboard') ? 'active' : '' }}">
                <i class="bi bi-speedometer2"></i>
                <span>Dashboard</span>
            </a>
            
            <a href="{{ route('membre.cotisations') }}" class="nav-link {{ request()->routeIs('membre.cotisations') ? 'active' : '' }}">
                <i class="bi bi-receipt-cutoff"></i>
                <span>Mes Cotisations</span>
            </a>
            
            <a href="{{ route('membre.paiements') }}" class="nav-link {{ request()->routeIs('membre.paiements') ? 'active' : '' }}">
                <i class="bi bi-receipt"></i>
                <span>Mes Paiements</span>
            </a>
            
            <a href="{{ route('membre.engagements') }}" class="nav-link {{ request()->routeIs('membre.engagements') ? 'active' : '' }}">
                <i class="bi bi-clipboard-check"></i>
                <span>Mes Engagements</span>
            </a>
            
            <a href="{{ route('membre.remboursements') }}" class="nav-link {{ request()->routeIs('membre.remboursements*') ? 'active' : '' }}">
                <i class="bi bi-arrow-counterclockwise"></i>
                <span>Mes Remboursements</span>
            </a>
            
            {{-- <a href="{{ route('membre.nano-credits') }}" class="nav-link {{ request()->routeIs('membre.nano-credits*') ? 'active' : '' }}">
                <i class="bi bi-credit-card-2-front"></i>
                <span>Nano Crédits</span>
            </a> --}}
            
            <a href="{{ route('membre.profil') }}" class="nav-link {{ request()->routeIs('membre.profil') ? 'active' : '' }}">
                <i class="bi bi-person-circle"></i>
                <span>Mes Infos Personnelles</span>
            </a>
        </nav>
    </div>
    
    <!-- Top Bar -->
    <div class="top-bar">
        <div class="top-bar-left">
            <span style="font-size: 0.85rem; color: var(--primary-dark-blue); font-weight: 300;">
                <i class="bi bi-person-circle"></i> 
                {{ $membre->nom_complet ?? 'Membre' }}
                <small class="text-muted ms-2">({{ $membre->numero ?? '' }})</small>
            </span>
        </div>
        <div class="top-bar-right">
            <form action="{{ route('membre.logout') }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="logout-btn" title="Déconnexion">
                    <i class="bi bi-box-arrow-right"></i>
                    <span>Déconnexion</span>
                </button>
            </form>
        </div>
    </div>
    
    <!-- Main Content -->
    <div class="main-content">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-circle"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        
        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-circle"></i> 
                <ul class="mb-0" style="font-size: 0.8rem;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        
        @yield('content')
    </div>
    
    <!-- Toast Container (en haut à droite) -->
    <div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 9999; margin-top: 50px;">
        <div id="toastContainer"></div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Fonction pour afficher un toast
        function showToast(message, type = 'success') {
            const toastContainer = document.getElementById('toastContainer');
            const toastId = 'toast-' + Date.now();
            const bgClass = type === 'success' ? 'bg-success' : type === 'error' ? 'bg-danger' : type === 'warning' ? 'bg-warning' : 'bg-info';
            const icon = type === 'success' ? 'bi-check-circle' : type === 'error' ? 'bi-x-circle' : type === 'warning' ? 'bi-exclamation-triangle' : 'bi-info-circle';
            
            const toastHTML = `
                <div id="${toastId}" class="toast align-items-center text-white ${bgClass} border-0" role="alert" aria-live="assertive" aria-atomic="true">
                    <div class="d-flex">
                        <div class="toast-body" style="font-weight: 300; font-family: 'Ubuntu', sans-serif; font-size: 0.875rem;">
                            <i class="bi ${icon} me-2"></i>${message}
                        </div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                    </div>
                </div>
            `;
            
            toastContainer.insertAdjacentHTML('beforeend', toastHTML);
            const toastElement = document.getElementById(toastId);
            const toast = new bootstrap.Toast(toastElement, {
                autohide: true,
                delay: 5000
            });
            toast.show();
            
            // Supprimer l'élément après la fermeture
            toastElement.addEventListener('hidden.bs.toast', function() {
                toastElement.remove();
            });
        }
        
        // Afficher les messages de session comme toasts
        @if(session('success'))
            showToast('{{ session('success') }}', 'success');
        @endif
        
        @if(session('error'))
            showToast('{{ session('error') }}', 'error');
        @endif
        
        @if(session('warning'))
            showToast('{{ session('warning') }}', 'warning');
        @endif
        
        @if(session('info'))
            showToast('{{ session('info') }}', 'info');
        @endif
    </script>
    
    @stack('scripts')
</body>
</html>
