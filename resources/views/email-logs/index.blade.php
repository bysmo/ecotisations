@extends('layouts.app')

@section('title', 'Historique des Emails')

@section('content')
<div class="page-header">
    <h1><i class="bi bi-envelope-check"></i> Historique des Emails</h1>
</div>

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
                    <i class="bi bi-envelope" style="font-size: 1.25rem; opacity: 0.5;"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-2">
        <div class="card text-white" style="background: #198754;">
            <div class="card-body" style="padding: 0.5rem 0.75rem;">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-subtitle mb-0 text-white-50" style="font-size: 0.65rem; font-weight: 300; font-family: 'Ubuntu', sans-serif;">Envoyés</h6>
                        <h5 class="mb-0" style="font-size: 1rem; font-weight: 400; font-family: 'Ubuntu', sans-serif;">{{ $stats['envoyes'] }}</h5>
                    </div>
                    <i class="bi bi-check-circle" style="font-size: 1.25rem; opacity: 0.5;"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-2">
        <div class="card text-white" style="background: #dc3545;">
            <div class="card-body" style="padding: 0.5rem 0.75rem;">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-subtitle mb-0 text-white-50" style="font-size: 0.65rem; font-weight: 300; font-family: 'Ubuntu', sans-serif;">Échecs</h6>
                        <h5 class="mb-0" style="font-size: 1rem; font-weight: 400; font-family: 'Ubuntu', sans-serif;">{{ $stats['echecs'] }}</h5>
                    </div>
                    <i class="bi bi-x-circle" style="font-size: 1.25rem; opacity: 0.5;"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-2">
        <div class="card text-white" style="background: #17a2b8;">
            <div class="card-body" style="padding: 0.5rem 0.75rem;">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-subtitle mb-0 text-white-50" style="font-size: 0.65rem; font-weight: 300; font-family: 'Ubuntu', sans-serif;">En attente</h6>
                        <h5 class="mb-0" style="font-size: 1rem; font-weight: 400; font-family: 'Ubuntu', sans-serif;">{{ $stats['en_attente'] }}</h5>
                    </div>
                    <i class="bi bi-clock-history" style="font-size: 1.25rem; opacity: 0.5;"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <i class="bi bi-list-ul"></i> Liste des Emails
    </div>
    <div class="card-body">
        <!-- Barre de recherche et filtres -->
        <form method="GET" action="{{ route('email-logs.index') }}" class="mb-3">
            <div class="row g-2">
                <div class="col-md-2">
                    <input type="text" 
                           name="search" 
                           class="form-control form-control-sm" 
                           placeholder="Rechercher..." 
                           value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <select name="type" class="form-select form-select-sm">
                        <option value="">Tous les types</option>
                        <option value="campagne" {{ request('type') === 'campagne' ? 'selected' : '' }}>Campagne</option>
                        <option value="paiement" {{ request('type') === 'paiement' ? 'selected' : '' }}>Paiement</option>
                        <option value="engagement" {{ request('type') === 'engagement' ? 'selected' : '' }}>Engagement</option>
                        <option value="fin_mois" {{ request('type') === 'fin_mois' ? 'selected' : '' }}>Fin de mois</option>
                        <option value="rappel" {{ request('type') === 'rappel' ? 'selected' : '' }}>Rappel</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="statut" class="form-select form-select-sm">
                        <option value="">Tous les statuts</option>
                        <option value="envoye" {{ request('statut') === 'envoye' ? 'selected' : '' }}>Envoyé</option>
                        <option value="echec" {{ request('statut') === 'echec' ? 'selected' : '' }}>Échec</option>
                        <option value="en_attente" {{ request('statut') === 'en_attente' ? 'selected' : '' }}>En attente</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="date" 
                           name="date_debut" 
                           class="form-control form-control-sm" 
                           value="{{ request('date_debut') }}"
                           placeholder="Date début">
                </div>
                <div class="col-md-2">
                    <input type="date" 
                           name="date_fin" 
                           class="form-control form-control-sm" 
                           value="{{ request('date_fin') }}"
                           placeholder="Date fin">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary btn-sm w-100">
                        <i class="bi bi-search"></i> Filtrer
                    </button>
                </div>
            </div>
            @if(request('search') || request('type') || request('statut') || request('date_debut') || request('date_fin'))
                <div class="row mt-2">
                    <div class="col-md-12">
                        <a href="{{ route('email-logs.index') }}" class="btn btn-secondary btn-sm">
                            <i class="bi bi-x-circle"></i> Effacer
                        </a>
                    </div>
                </div>
            @endif
        </form>
        
        @if($logs->count() > 0)
            <style>
                .table-email-logs {
                    margin-bottom: 0;
                }
                .table-email-logs thead th {
                    padding: 0.15rem 0.35rem !important;
                    font-size: 0.6rem !important;
                    line-height: 1.05 !important;
                    vertical-align: middle !important;
                    font-weight: 300 !important;
                    font-family: 'Ubuntu', sans-serif !important;
                    color: white !important;
                    background-color: var(--primary-dark-blue) !important;
                }
                .table-email-logs tbody td {
                    padding: 0.15rem 0.35rem !important;
                    font-size: 0.65rem !important;
                    line-height: 1.05 !important;
                    vertical-align: middle !important;
                    border-bottom: 1px solid #f0f0f0 !important;
                    font-weight: 300 !important;
                    font-family: 'Ubuntu', sans-serif !important;
                    color: var(--primary-dark-blue) !important;
                }
                .table-email-logs tbody tr:last-child td {
                    border-bottom: none !important;
                }
                .table-email-logs .btn {
                    padding: 0 !important;
                    font-size: 0.5rem !important;
                    line-height: 1 !important;
                    height: 18px !important;
                    width: 22px !important;
                    display: inline-flex !important;
                    align-items: center !important;
                    justify-content: center !important;
                }
                .table-email-logs .btn i {
                    font-size: 0.6rem !important;
                }
                table.table.table-email-logs.table-hover tbody tr {
                    background-color: #ffffff !important;
                    transition: background-color 0.2s ease !important;
                }
                table.table.table-email-logs.table-hover tbody tr:nth-child(even) {
                    background-color: #d4dde8 !important;
                }
                table.table.table-email-logs.table-hover tbody tr:hover {
                    background-color: #b8c7d9 !important;
                    cursor: pointer !important;
                }
                table.table.table-email-logs.table-hover tbody tr:nth-child(even):hover {
                    background-color: #9fb3cc !important;
                }
            </style>
            <div class="table-responsive">
                <table class="table table-email-logs table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Type</th>
                            <th>Destinataire</th>
                            <th>Email</th>
                            <th>Sujet</th>
                            <th>Statut</th>
                            <th>Date envoi</th>
                            <th>Erreur</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($logs as $log)
                            <tr>
                                <td style="font-weight: 300; font-family: 'Ubuntu', sans-serif;">
                                    @if($log->type === 'campagne')
                                        Campagne
                                    @elseif($log->type === 'paiement')
                                        Paiement
                                    @elseif($log->type === 'engagement')
                                        Engagement
                                    @elseif($log->type === 'fin_mois')
                                        Fin de mois
                                    @elseif($log->type === 'rappel')
                                        Rappel
                                    @else
                                        Autre
                                    @endif
                                </td>
                                <td style="font-weight: 300; font-family: 'Ubuntu', sans-serif;">
                                    {{ $log->membre->nom_complet ?? '-' }}
                                </td>
                                <td style="font-weight: 300; font-family: 'Ubuntu', sans-serif;">
                                    {{ $log->destinataire_email }}
                                </td>
                                <td style="font-weight: 300; font-family: 'Ubuntu', sans-serif;">{{ \Illuminate\Support\Str::limit($log->sujet, 50) }}</td>
                                <td style="font-weight: 300; font-family: 'Ubuntu', sans-serif;">
                                    @if($log->statut === 'envoye')
                                        Envoyé
                                    @elseif($log->statut === 'echec')
                                        Échec
                                    @else
                                        En attente
                                    @endif
                                </td>
                                <td style="font-weight: 300; font-family: 'Ubuntu', sans-serif;">{{ $log->envoye_at ? $log->envoye_at->format('d/m/Y H:i') : '-' }}</td>
                                <td style="font-weight: 300; font-family: 'Ubuntu', sans-serif;">
                                    @if($log->erreur)
                                        <small class="text-danger">{{ \Illuminate\Support\Str::limit($log->erreur, 30) }}</small>
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            @if($logs->hasPages())
                <div class="d-flex justify-content-end mt-3">
                    <div class="pagination-custom">
                        {{ $logs->links() }}
                    </div>
                </div>
            @endif
        @else
            <div class="text-center py-3">
                <i class="bi bi-inbox" style="font-size: 1.5rem; color: #ccc;"></i>
                <p class="text-muted mt-2 mb-0">Aucun email trouvé</p>
            </div>
        @endif
    </div>
</div>
@endsection
