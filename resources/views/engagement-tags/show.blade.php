@extends('layouts.app')

@section('title', 'Engagements du Tag: ' . $tag)

@section('content')
<div class="page-header">
    <h1>
        <i class="bi bi-tags"></i> Engagements du Tag: 
        <span class="badge bg-info">{{ $tag }}</span>
    </h1>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-clipboard-check"></i> Liste des Engagements ({{ $engagements->total() }})</span>
        <a href="{{ route('engagement-tags.index') }}" class="btn btn-light btn-sm">
            <i class="bi bi-arrow-left"></i> Retour
        </a>
    </div>
    <div class="card-body">
        @if($engagements->count() > 0)
            <style>
                .table-engagements-tag thead th {
                    padding: 0.15rem 0.35rem !important;
                    font-size: 0.6rem !important;
                    line-height: 1.05 !important;
                    vertical-align: middle !important;
                    font-weight: 300 !important;
                    font-family: 'Ubuntu', sans-serif !important;
                    color: var(--primary-dark-blue) !important;
                }
                .table-engagements-tag tbody td {
                    padding: 0.15rem 0.35rem !important;
                    font-size: 0.65rem !important;
                    line-height: 1.05 !important;
                    vertical-align: middle !important;
                    border-bottom: 1px solid #f0f0f0 !important;
                    font-weight: 300 !important;
                    font-family: 'Ubuntu', sans-serif !important;
                    color: var(--primary-dark-blue) !important;
                }
                .table-engagements-tag .btn {
                    padding: 0.1rem 0.25rem !important;
                    font-size: 0.55rem !important;
                    line-height: 1.1 !important;
                }
                .table-engagements-tag .btn i {
                    font-size: 0.65rem !important;
                }
                .table-engagements-tag tbody tr:last-child td {
                    border-bottom: none !important;
                }
                table.table.table-engagements-tag.table-hover tbody tr {
                    background-color: #ffffff !important;
                    transition: background-color 0.2s ease !important;
                }
                table.table.table-engagements-tag.table-hover tbody tr:nth-child(even) {
                    background-color: #d4dde8 !important;
                }
                table.table.table-engagements-tag.table-hover tbody tr:hover {
                    background-color: #b8c7d9 !important;
                    cursor: pointer !important;
                }
                table.table.table-engagements-tag.table-hover tbody tr:nth-child(even):hover {
                    background-color: #9fb3cc !important;
                }
            </style>
            <div class="table-responsive">
                <table class="table table-engagements-tag table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Numéro</th>
                            <th>Membre</th>
                            <th>Cotisation</th>
                            <th>Montant</th>
                            <th>Période</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($engagements as $engagement)
                            <tr>
                                <td>{{ $engagement->numero ?? '-' }}</td>
                                <td>{{ $engagement->membre->nom_complet ?? '-' }}</td>
                                <td>{{ $engagement->cotisation->nom ?? '-' }}</td>
                                <td>{{ number_format($engagement->montant_engage, 0, ',', ' ') }} XOF</td>
                                <td>
                                    {{ $engagement->periode_debut ? $engagement->periode_debut->format('d/m/Y') : '-' }} - 
                                    {{ $engagement->periode_fin ? $engagement->periode_fin->format('d/m/Y') : '-' }}
                                </td>
                                <td>
                                    @if($engagement->statut === 'en_cours')
                                        <i class="bi bi-check-circle"></i> En cours
                                    @elseif($engagement->statut === 'termine')
                                        <i class="bi bi-check-circle-fill"></i> Terminé
                                    @else
                                        <i class="bi bi-x-circle"></i> Annulé
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a href="{{ route('engagements.show', $engagement) }}" 
                                           class="btn btn-outline-primary" 
                                           title="Voir">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('engagements.edit', $engagement) }}" 
                                           class="btn btn-outline-warning" 
                                           title="Modifier">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination Bootstrap -->
            @if($engagements->hasPages() || $engagements->total() > 0)
                <div class="d-flex justify-content-end mt-3">
                    <div class="pagination-custom">
                        {{ $engagements->links() }}
                    </div>
                </div>
            @endif
        @else
            <div class="text-center py-3">
                <i class="bi bi-inbox" style="font-size: 1.5rem; color: #ccc;"></i>
                <p class="text-muted mt-2 mb-2" style="font-size: 0.75rem;">Aucun engagement avec ce tag</p>
            </div>
        @endif
    </div>
</div>
@endsection
