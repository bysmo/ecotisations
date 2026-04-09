@extends('layouts.app')

@section('title', 'Tableau de bord de Sécurité (Intégrité des Données)')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h1><i class="bi bi-shield-check"></i> Tableau de bord de Sécurité (Audit Continu)</h1>
        <p class="text-muted mb-0" style="font-size: 0.8rem;">Historique des scans d'intégrité de la base de données (Calcul des Checksums et hachages récursistes).</p>
    </div>
    <div>
        <form action="{{ route('logs.security.scan') }}" method="POST" class="d-inline">
            @csrf
            <button type="submit" class="btn btn-primary shadow-sm" onclick="return confirm('Êtes-vous sûr de vouloir lancer un scan manuel des checksums de toute la base de données ? Cela peut ralentir temporairement le système.');">
                <i class="bi bi-play-circle-fill"></i> Lancer un Scan Manuel
            </button>
        </form>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card mb-4" style="border-top: 3px solid var(--primary-dark-blue);">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-list-check"></i> Historique des vérifications par l'Automate (Cron)</span>
                <span class="badge bg-secondary">Dernières vérifications (Toutes les 10 minutes)</span>
            </div>
            
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Date du Scan</th>
                                <th>Statut Global</th>
                                <th>Lignes Analysées</th>
                                <th>Corruptions Détectées</th>
                                <th>Détails d'Altération</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($logs as $log)
                                <tr class="{{ $log->is_valid ? '' : 'table-danger' }}">
                                    <td class="align-middle">
                                        <strong>{{ $log->created_at->format('d/m/Y H:i:s') }}</strong><br>
                                        <small class="text-muted">{{ $log->created_at->diffForHumans() }}</small>
                                    </td>
                                    
                                    <td class="align-middle">
                                        @if($log->is_valid)
                                            <span class="badge bg-success bg-opacity-10 text-success border border-success rounded-pill px-3 py-2">
                                                <i class="bi bi-check-circle-fill"></i> Intègre
                                            </span>
                                        @else
                                            <span class="badge bg-danger bg-opacity-10 text-danger border border-danger rounded-pill px-3 py-2">
                                                <i class="bi bi-exclamation-triangle-fill"></i> Compromis
                                            </span>
                                        @endif
                                    </td>
                                    
                                    <td class="align-middle fs-6">
                                        {{ number_format($log->rows_checked_count, 0, ',', ' ') }}
                                    </td>
                                    
                                    <td class="align-middle">
                                        @if($log->corrupted_count == 0)
                                            <span class="text-muted">-</span>
                                        @else
                                            <span class="text-danger fw-bold fs-6">{{ $log->corrupted_count }}</span>
                                        @endif
                                    </td>
                                    
                                    <td class="align-middle" style="max-width: 500px;">
                                        @if($log->is_valid)
                                            <span class="text-muted font-monospace" style="font-size: 0.75rem;">Aucune altération détectée. Le calcul concorde.</span>
                                        @else
                                            <div class="p-2 mb-0 rounded text-center" style="background-color: #f8d7da; border: 1px solid #f5c2c7;">
                                                <div class="mb-2 text-danger fw-bold">
                                                    <i class="bi bi-exclamation-octagon-fill fs-5 align-middle"></i> 
                                                    Plusieurs tables compromises
                                                </div>
                                                <a href="{{ route('logs.security.show', $log->id) }}" class="btn btn-danger btn-sm text-white fw-bold shadow-sm" style="text-decoration: none;">
                                                    <i class="bi bi-list-columns-reverse"></i> 
                                                    Traiter les {{ $log->corrupted_count }} altérations
                                                </a>
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">
                                        <i class="bi bi-inbox fs-3 d-block mb-2"></i>
                                        Aucun scan temporel effectué pour le moment (le planificateur passe toutes les 10 min).
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($logs->hasPages())
                <div class="card-footer bg-white border-top">
                    <!-- Utilise le design existant de pagination du layout -->
                    <div class="pagination-custom d-flex justify-content-center">
                        {{ $logs->links() }}
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
