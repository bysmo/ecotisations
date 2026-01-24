@extends('layouts.app')

@section('title', 'Campagnes d\'Emails')

@section('content')
<div class="page-header">
    <h1><i class="bi bi-envelope-paper"></i> Campagnes d'Emails</h1>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-x-circle"></i> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<!-- Statistiques -->
<div class="row mb-3">
    <div class="col-md-3 mb-2">
        <div class="card text-white" style="background: var(--primary-dark-blue);">
            <div class="card-body" style="padding: 0.5rem 0.75rem;">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-subtitle mb-0 text-white-50" style="font-size: 0.65rem; font-weight: 300; font-family: 'Ubuntu', sans-serif;">Total</h6>
                        <h5 class="mb-0" style="font-size: 1rem; font-weight: 400; font-family: 'Ubuntu', sans-serif;">{{ $stats['total'] }}</h5>
                    </div>
                    <i class="bi bi-envelope-paper" style="font-size: 1.25rem; opacity: 0.5;"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-2">
        <div class="card text-white" style="background: #6c757d;">
            <div class="card-body" style="padding: 0.5rem 0.75rem;">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-subtitle mb-0 text-white-50" style="font-size: 0.65rem; font-weight: 300; font-family: 'Ubuntu', sans-serif;">Brouillons</h6>
                        <h5 class="mb-0" style="font-size: 1rem; font-weight: 400; font-family: 'Ubuntu', sans-serif;">{{ $stats['brouillon'] }}</h5>
                    </div>
                    <i class="bi bi-file-earmark" style="font-size: 1.25rem; opacity: 0.5;"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-2">
        <div class="card text-white" style="background: #17a2b8;">
            <div class="card-body" style="padding: 0.5rem 0.75rem;">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-subtitle mb-0 text-white-50" style="font-size: 0.65rem; font-weight: 300; font-family: 'Ubuntu', sans-serif;">En cours</h6>
                        <h5 class="mb-0" style="font-size: 1rem; font-weight: 400; font-family: 'Ubuntu', sans-serif;">{{ $stats['en_cours'] }}</h5>
                    </div>
                    <i class="bi bi-clock-history" style="font-size: 1.25rem; opacity: 0.5;"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-2">
        <div class="card text-white" style="background: #198754;">
            <div class="card-body" style="padding: 0.5rem 0.75rem;">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-subtitle mb-0 text-white-50" style="font-size: 0.65rem; font-weight: 300; font-family: 'Ubuntu', sans-serif;">Terminées</h6>
                        <h5 class="mb-0" style="font-size: 1rem; font-weight: 400; font-family: 'Ubuntu', sans-serif;">{{ $stats['terminee'] }}</h5>
                    </div>
                    <i class="bi bi-check-circle" style="font-size: 1.25rem; opacity: 0.5;"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <div><i class="bi bi-list-ul"></i> Liste des Campagnes</div>
        <a href="{{ route('campagnes.create') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-circle"></i> Nouvelle Campagne
        </a>
    </div>
    <div class="card-body">
        <!-- Barre de recherche et filtres -->
        <form method="GET" action="{{ route('campagnes.index') }}" class="mb-3">
            <div class="row g-2">
                <div class="col-md-4">
                    <input type="text" 
                           name="search" 
                           class="form-control form-control-sm" 
                           placeholder="Rechercher..." 
                           value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <select name="statut" class="form-select form-select-sm">
                        <option value="">Tous les statuts</option>
                        <option value="brouillon" {{ request('statut') === 'brouillon' ? 'selected' : '' }}>Brouillon</option>
                        <option value="en_cours" {{ request('statut') === 'en_cours' ? 'selected' : '' }}>En cours</option>
                        <option value="terminee" {{ request('statut') === 'terminee' ? 'selected' : '' }}>Terminée</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary btn-sm w-100">
                        <i class="bi bi-search"></i> Filtrer
                    </button>
                </div>
            </div>
            @if(request('search') || request('statut'))
                <div class="mt-2">
                    <a href="{{ route('campagnes.index') }}" class="btn btn-secondary btn-sm">
                        <i class="bi bi-x-circle"></i> Effacer
                    </a>
                </div>
            @endif
        </form>
        
        @if($campagnes->count() > 0)
            <style>
                .table-campagnes {
                    margin-bottom: 0;
                }
                .table-campagnes thead th {
                    padding: 0.15rem 0.35rem !important;
                    font-size: 0.6rem !important;
                    line-height: 1.05 !important;
                    vertical-align: middle !important;
                    font-weight: 300 !important;
                    font-family: 'Ubuntu', sans-serif !important;
                    color: white !important;
                    background-color: var(--primary-dark-blue) !important;
                }
                .table-campagnes tbody td {
                    padding: 0.15rem 0.35rem !important;
                    font-size: 0.65rem !important;
                    line-height: 1.05 !important;
                    vertical-align: middle !important;
                    border-bottom: 1px solid #f0f0f0 !important;
                    font-weight: 300 !important;
                    font-family: 'Ubuntu', sans-serif !important;
                    color: var(--primary-dark-blue) !important;
                }
                .table-campagnes tbody tr:last-child td {
                    border-bottom: none !important;
                }
                .table-campagnes .btn {
                    padding: 0 !important;
                    font-size: 0.5rem !important;
                    line-height: 1 !important;
                    height: 18px !important;
                    width: 22px !important;
                    display: inline-flex !important;
                    align-items: center !important;
                    justify-content: center !important;
                }
                .table-campagnes .btn i {
                    font-size: 0.6rem !important;
                }
                table.table.table-campagnes.table-hover tbody tr {
                    background-color: #ffffff !important;
                    transition: background-color 0.2s ease !important;
                }
                table.table.table-campagnes.table-hover tbody tr:nth-child(even) {
                    background-color: #d4dde8 !important;
                }
                table.table.table-campagnes.table-hover tbody tr:hover {
                    background-color: #b8c7d9 !important;
                    cursor: pointer !important;
                }
                table.table.table-campagnes.table-hover tbody tr:nth-child(even):hover {
                    background-color: #9fb3cc !important;
                }
            </style>
            <div class="table-responsive">
                <table class="table table-campagnes table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Nom</th>
                            <th>Sujet</th>
                            <th>Statut</th>
                            <th>Destinataires</th>
                            <th>Envoyés</th>
                            <th>Échecs</th>
                            <th>Créée par</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($campagnes as $campagne)
                            <tr>
                                <td style="font-weight: 300; font-family: 'Ubuntu', sans-serif;">{{ $campagne->nom }}</td>
                                <td style="font-weight: 300; font-family: 'Ubuntu', sans-serif;">{{ \Illuminate\Support\Str::limit($campagne->sujet, 40) }}</td>
                                <td style="font-weight: 300; font-family: 'Ubuntu', sans-serif;">
                                    @if($campagne->statut === 'brouillon')
                                        Brouillon
                                    @elseif($campagne->statut === 'en_cours')
                                        En cours
                                    @elseif($campagne->statut === 'terminee')
                                        Terminée
                                    @else
                                        {{ ucfirst($campagne->statut) }}
                                    @endif
                                </td>
                                <td style="font-weight: 300; font-family: 'Ubuntu', sans-serif;">{{ $campagne->total_destinataires }}</td>
                                <td style="font-weight: 300; font-family: 'Ubuntu', sans-serif;">{{ $campagne->envoyes }}</td>
                                <td style="font-weight: 300; font-family: 'Ubuntu', sans-serif;">{{ $campagne->echecs }}</td>
                                <td style="font-weight: 300; font-family: 'Ubuntu', sans-serif;">{{ $campagne->creePar->name ?? '-' }}</td>
                                <td style="font-weight: 300; font-family: 'Ubuntu', sans-serif;">{{ $campagne->created_at->format('d/m/Y H:i') }}</td>
                                <td>
                                    <a href="{{ route('campagnes.show', $campagne) }}" class="btn btn-outline-primary btn-sm" title="Voir les détails">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            @if($campagnes->hasPages())
                <div class="d-flex justify-content-end mt-3">
                    <div class="pagination-custom">
                        {{ $campagnes->links() }}
                    </div>
                </div>
            @endif
        @else
            <div class="text-center py-3">
                <i class="bi bi-inbox" style="font-size: 1.5rem; color: #ccc;"></i>
                <p class="text-muted mt-2 mb-0">Aucune campagne trouvée</p>
            </div>
        @endif
    </div>
</div>
@endsection
