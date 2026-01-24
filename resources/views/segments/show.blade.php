@extends('layouts.app')

@section('title', 'Membres du Segment: ' . $segmentNom)

@section('content')
<div class="page-header">
    <h1>
        <i class="bi bi-tags"></i> Membres du Segment: {{ $segmentNom }}
    </h1>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-people"></i> Liste des Membres ({{ $membres->total() }})</span>
        <a href="{{ route('segments.index') }}" class="btn btn-light btn-sm">
            <i class="bi bi-arrow-left"></i> Retour
        </a>
    </div>
    <div class="card-body">
        @if($membres->count() > 0)
            <style>
                .table-membres-segment thead th {
                    padding: 0.15rem 0.35rem !important;
                    font-size: 0.6rem !important;
                    line-height: 1.05 !important;
                    vertical-align: middle !important;
                    font-weight: 300 !important;
                    font-family: 'Ubuntu', sans-serif !important;
                    color: var(--primary-dark-blue) !important;
                }
                .table-membres-segment tbody td {
                    padding: 0.15rem 0.35rem !important;
                    font-size: 0.65rem !important;
                    line-height: 1.05 !important;
                    vertical-align: middle !important;
                    border-bottom: 1px solid #f0f0f0 !important;
                    font-weight: 300 !important;
                    font-family: 'Ubuntu', sans-serif !important;
                    color: var(--primary-dark-blue) !important;
                }
                .table-membres-segment .btn {
                    padding: 0.1rem 0.25rem !important;
                    font-size: 0.55rem !important;
                    line-height: 1.1 !important;
                }
                .table-membres-segment .btn i {
                    font-size: 0.65rem !important;
                }
                .table-membres-segment tbody tr:last-child td {
                    border-bottom: none !important;
                }
                table.table.table-membres-segment.table-hover tbody tr {
                    background-color: #ffffff !important;
                    transition: background-color 0.2s ease !important;
                }
                table.table.table-membres-segment.table-hover tbody tr:nth-child(even) {
                    background-color: #d4dde8 !important;
                }
                table.table.table-membres-segment.table-hover tbody tr:hover {
                    background-color: #b8c7d9 !important;
                    cursor: pointer !important;
                }
                table.table.table-membres-segment.table-hover tbody tr:nth-child(even):hover {
                    background-color: #9fb3cc !important;
                }
            </style>
            <div class="table-responsive">
                <table class="table table-membres-segment table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Numéro</th>
                            <th>Nom</th>
                            <th>Prénom</th>
                            <th>Email</th>
                            <th>Téléphone</th>
                            <th>Date d'adhésion</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($membres as $membre)
                            <tr>
                                <td>{{ $membre->numero ?? '-' }}</td>
                                <td>{{ $membre->nom }}</td>
                                <td>{{ $membre->prenom }}</td>
                                <td>{{ $membre->email }}</td>
                                <td>{{ $membre->telephone ?? '-' }}</td>
                                <td>{{ $membre->date_adhesion ? $membre->date_adhesion->format('d/m/Y') : '-' }}</td>
                                <td>
                                    @if($membre->statut === 'actif')
                                        <i class="bi bi-check-circle"></i> Actif
                                    @elseif($membre->statut === 'inactif')
                                        <i class="bi bi-x-circle"></i> Inactif
                                    @else
                                        <i class="bi bi-exclamation-triangle"></i> Suspendu
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a href="{{ route('membres.show', $membre) }}" 
                                           class="btn btn-outline-primary" 
                                           title="Voir">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('membres.edit', $membre) }}" 
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
            @if($membres->hasPages() || $membres->total() > 0)
                <div class="d-flex justify-content-end mt-3">
                    <div class="pagination-custom">
                        {{ $membres->links() }}
                    </div>
                </div>
            @endif
        @else
            <div class="text-center py-3">
                <i class="bi bi-inbox" style="font-size: 1.5rem; color: #ccc;"></i>
                <p class="text-muted mt-2 mb-2" style="font-size: 0.75rem;">Aucun membre dans ce segment</p>
            </div>
        @endif
    </div>
</div>
@endsection
