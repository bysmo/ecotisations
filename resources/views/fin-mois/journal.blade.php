@extends('layouts.app')

@section('title', 'Journal d\'envoi - Fin de mois')

@section('content')
<div class="page-header">
    <h1><i class="bi bi-journal-text"></i> Journal d'envoi - Fin de mois</h1>
</div>

<div class="card">
    <div class="card-header">
        <i class="bi bi-list-ul"></i> Historique des envois
    </div>
    <div class="card-body">
        <!-- Filtres -->
        <form method="GET" action="{{ route('fin-mois.journal') }}" class="mb-3">
            <div class="row g-2">
                <div class="col-md-3">
                    <select class="form-select form-select-sm" name="membre_id">
                        <option value="">Tous les membres</option>
                        @foreach($membres as $membre)
                            <option value="{{ $membre->id }}" {{ request('membre_id') == $membre->id ? 'selected' : '' }}>
                                {{ $membre->prenom }} {{ $membre->nom }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <select class="form-select form-select-sm" name="statut">
                        <option value="">Tous les statuts</option>
                        <option value="envoye" {{ request('statut') == 'envoye' ? 'selected' : '' }}>Envoyé</option>
                        <option value="echec" {{ request('statut') == 'echec' ? 'selected' : '' }}>Échec</option>
                        <option value="en_attente" {{ request('statut') == 'en_attente' ? 'selected' : '' }}>En attente</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <input type="month" 
                           class="form-control form-control-sm" 
                           name="periode" 
                           value="{{ request('periode', date('Y-m')) }}">
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary btn-sm w-100">
                        <i class="bi bi-search"></i> Filtrer
                    </button>
                </div>
            </div>
        </form>
        
        @if($logs->count() > 0)
            <style>
                .table-fin-mois {
                    margin-bottom: 0;
                }
                .table-fin-mois thead th {
                    padding: 0.15rem 0.35rem !important;
                    font-size: 0.6rem !important;
                    font-weight: 500 !important;
                    vertical-align: middle !important;
                    color: var(--primary-dark-blue) !important;
                    border-bottom: 1px solid #dee2e6 !important;
                    background-color: #f8f9fa !important;
                    font-family: 'Ubuntu', sans-serif !important;
                }
                .table-fin-mois tbody td {
                    padding: 0.2rem 0.35rem !important;
                    font-size: 0.65rem !important;
                    vertical-align: middle !important;
                    color: var(--primary-dark-blue) !important;
                    font-family: 'Ubuntu', sans-serif !important;
                    line-height: 1.3 !important;
                }
                table.table.table-fin-mois.table-hover tbody tr {
                    transition: background-color 0.2s ease;
                }
                table.table.table-fin-mois.table-hover tbody tr:nth-child(even) {
                    background-color: rgba(30, 58, 95, 0.03) !important;
                }
                table.table.table-fin-mois.table-hover tbody tr:hover {
                    background-color: rgba(30, 58, 95, 0.08) !important;
                }
                table.table.table-fin-mois.table-hover tbody tr:nth-child(even):hover {
                    background-color: rgba(30, 58, 95, 0.12) !important;
                }
                .table-fin-mois .btn {
                    padding: 0.15rem 0.35rem !important;
                    font-size: 0.65rem !important;
                }
                .table-fin-mois .btn i {
                    font-size: 0.7rem !important;
                }
            </style>
            <div class="table-responsive">
                <table class="table table-fin-mois table-striped table-hover">
                    <thead>
                        <tr>
                            <th style="width: 60px;">ID</th>
                            <th>Membre</th>
                            <th>Email</th>
                            <th>Période</th>
                            <th>Paiements</th>
                            <th>Montant total</th>
                            <th>Statut</th>
                            <th>Date envoi</th>
                            <th style="width: 100px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($logs as $log)
                            <tr>
                                <td>{{ $log->id }}</td>
                                <td>
                                    {{ $log->membre->prenom }} {{ $log->membre->nom }}
                                </td>
                                <td>
                                    @if($log->email_destinataire)
                                        <small>{{ $log->email_destinataire }}</small>
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </td>
                                <td>
                                    <small>{{ \Carbon\Carbon::parse($log->periode_debut)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($log->periode_fin)->format('d/m/Y') }}</small>
                                </td>
                                <td>{{ $log->nombre_paiements }}</td>
                                <td>{{ number_format($log->montant_total, 0, ',', ' ') }} XOF</td>
                                <td>
                                    @if($log->statut == 'envoye')
                                        <span class="badge bg-success" style="font-size: 0.6rem;">Envoyé</span>
                                    @elseif($log->statut == 'echec')
                                        <span class="badge bg-danger" style="font-size: 0.6rem;">Échec</span>
                                    @else
                                        <span class="badge bg-warning" style="font-size: 0.6rem;">En attente</span>
                                    @endif
                                </td>
                                <td>
                                    @if($log->envoye_at)
                                        <small>{{ \Carbon\Carbon::parse($log->envoye_at)->format('d/m/Y H:i') }}</small>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        @if($log->statut != 'envoye')
                                            <form action="{{ route('fin-mois.resend', $log) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-primary" title="Renvoyer">
                                                    <i class="bi bi-arrow-repeat"></i>
                                                </button>
                                            </form>
                                        @endif
                                        <button type="button" class="btn btn-info" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#detailModal{{ $log->id }}"
                                                title="Voir détails">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            
                            <!-- Modal détail -->
                            <div class="modal fade" id="detailModal{{ $log->id }}" tabindex="-1">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header" style="background: var(--primary-dark-blue); color: white;">
                                            <h5 class="modal-title">Détails de l'envoi #{{ $log->id }}</h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body" style="font-size: 0.8rem; font-family: 'Ubuntu', sans-serif;">
                                            <div class="row mb-3">
                                                <div class="col-md-6">
                                                    <strong>Membre :</strong><br>
                                                    {{ $log->membre->prenom }} {{ $log->membre->nom }}
                                                </div>
                                                <div class="col-md-6">
                                                    <strong>Email :</strong><br>
                                                    {{ $log->email_destinataire ?? 'N/A' }}
                                                </div>
                                            </div>
                                            
                                            <div class="row mb-3">
                                                <div class="col-md-6">
                                                    <strong>Période :</strong><br>
                                                    {{ \Carbon\Carbon::parse($log->periode_debut)->format('d/m/Y') }} - 
                                                    {{ \Carbon\Carbon::parse($log->periode_fin)->format('d/m/Y') }}
                                                </div>
                                                <div class="col-md-6">
                                                    <strong>Statut :</strong><br>
                                                    @if($log->statut == 'envoye')
                                                        <span class="badge bg-success">Envoyé</span>
                                                    @elseif($log->statut == 'echec')
                                                        <span class="badge bg-danger">Échec</span>
                                                    @else
                                                        <span class="badge bg-warning">En attente</span>
                                                    @endif
                                                </div>
                                            </div>
                                            
                                            @if($log->erreur)
                                                <div class="alert alert-danger">
                                                    <strong>Erreur :</strong> {{ $log->erreur }}
                                                </div>
                                            @endif
                                            
                                            <div class="mb-3">
                                                <strong>Sujet email :</strong><br>
                                                {{ $log->sujet_email }}
                                            </div>
                                            
                                            <div class="mb-3">
                                                <strong>Corps de l'email :</strong><br>
                                                <pre style="background: #f8f9fa; padding: 1rem; border-radius: 4px; font-size: 0.75rem; max-height: 300px; overflow-y: auto;">{{ $log->corps_email }}</pre>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Fermer</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination Bootstrap -->
            @if($logs->hasPages() || $logs->total() > 0)
                <div class="d-flex justify-content-end mt-3">
                    <div class="pagination-custom">
                        {{ $logs->links() }}
                    </div>
                </div>
            @endif
        @else
            <div class="text-center py-5">
                <i class="bi bi-journal-x" style="font-size: 3rem; color: #adb5bd;"></i>
                <p class="mt-3" style="color: #6c757d;">Aucun envoi enregistré</p>
            </div>
        @endif
    </div>
</div>

<div class="mt-3">
    <a href="{{ route('fin-mois.index') }}" class="btn btn-secondary btn-sm">
        <i class="bi bi-arrow-left"></i> Retour au traitement
    </a>
</div>
@endsection
