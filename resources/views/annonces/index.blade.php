@extends('layouts.app')

@section('title', 'Gestion des Annonces')

@section('content')
<div class="page-header">
    <h1><i class="bi bi-megaphone"></i> Gestion des Annonces</h1>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-list-ul"></i> Liste des Annonces</span>
        <a href="{{ route('annonces.create') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-circle"></i> Nouvelle annonce
        </a>
    </div>
    <div class="card-body">
        <!-- Barre de recherche -->
        <form method="GET" action="{{ route('annonces.index') }}" class="mb-3">
            <div class="row g-2">
                <div class="col-md-4">
                    <input type="text" 
                           name="search" 
                           class="form-control form-control-sm" 
                           placeholder="Rechercher par titre ou contenu..." 
                           value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <select name="statut" class="form-select form-select-sm">
                        <option value="">Tous les statuts</option>
                        <option value="active" {{ request('statut') === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('statut') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="type" class="form-select form-select-sm">
                        <option value="">Tous les types</option>
                        <option value="info" {{ request('type') === 'info' ? 'selected' : '' }}>Info</option>
                        <option value="warning" {{ request('type') === 'warning' ? 'selected' : '' }}>Avertissement</option>
                        <option value="success" {{ request('type') === 'success' ? 'selected' : '' }}>Succès</option>
                        <option value="danger" {{ request('type') === 'danger' ? 'selected' : '' }}>Danger</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary btn-sm w-100">
                        <i class="bi bi-search"></i> Rechercher
                    </button>
                </div>
            </div>
            @if(request('search') || request('statut') || request('type'))
                <div class="mt-2">
                    <a href="{{ route('annonces.index') }}" class="btn btn-secondary btn-sm">
                        <i class="bi bi-x-circle"></i> Effacer
                    </a>
                </div>
            @endif
        </form>
        
        @if($annonces->count() > 0)
            <style>
                .table-annonces {
                    margin-bottom: 0;
                }
                .table-annonces thead th {
                    padding: 0.15rem 0.35rem !important;
                    font-size: 0.6rem !important;
                    line-height: 1.05 !important;
                    vertical-align: middle !important;
                    font-weight: 300 !important;
                    font-family: 'Ubuntu', sans-serif !important;
                    color: var(--primary-dark-blue) !important;
                }
                .table-annonces tbody td {
                    padding: 0.15rem 0.35rem !important;
                    font-size: 0.65rem !important;
                    line-height: 1.05 !important;
                    vertical-align: middle !important;
                    border-bottom: 1px solid #f0f0f0 !important;
                    font-weight: 300 !important;
                    font-family: 'Ubuntu', sans-serif !important;
                    color: var(--primary-dark-blue) !important;
                }
                .table-annonces tbody tr:last-child td {
                    border-bottom: none !important;
                }
                table.table.table-annonces.table-hover tbody tr {
                    background-color: #ffffff !important;
                    transition: background-color 0.2s ease !important;
                }
                table.table.table-annonces.table-hover tbody tr:nth-child(even) {
                    background-color: #d4dde8 !important;
                }
                table.table.table-annonces.table-hover tbody tr:hover {
                    background-color: #b8c7d9 !important;
                    cursor: pointer !important;
                }
                table.table.table-annonces.table-hover tbody tr:nth-child(even):hover {
                    background-color: #9fb3cc !important;
                }
                .table-annonces .btn {
                    padding: 0.1rem 0.25rem !important;
                    font-size: 0.55rem !important;
                    line-height: 1.1 !important;
                }
                .table-annonces .btn i {
                    font-size: 0.65rem !important;
                }
                .table-annonces .badge {
                    font-size: 0.55rem !important;
                    padding: 0.15rem 0.35rem !important;
                }
            </style>
            <div class="table-responsive">
                <table class="table table-annonces table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Ordre</th>
                            <th>Titre</th>
                            <th>Type</th>
                            <th>Statut</th>
                            <th>Période</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($annonces as $annonce)
                            <tr>
                                <td>{{ $annonce->ordre }}</td>
                                <td><strong>{{ $annonce->titre }}</strong></td>
                                <td>
                                    <span class="badge bg-{{ $annonce->type }}">
                                        @if($annonce->type === 'info')
                                            Info
                                        @elseif($annonce->type === 'warning')
                                            Avertissement
                                        @elseif($annonce->type === 'success')
                                            Succès
                                        @else
                                            Danger
                                        @endif
                                    </span>
                                </td>
                                <td>
                                    @if($annonce->isActive())
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-secondary">Inactive</span>
                                    @endif
                                </td>
                                <td>
                                    @if($annonce->date_debut || $annonce->date_fin)
                                        {{ $annonce->date_debut ? $annonce->date_debut->format('d/m/Y') : 'Début' }} - 
                                        {{ $annonce->date_fin ? $annonce->date_fin->format('d/m/Y') : 'Fin' }}
                                    @else
                                        <span class="text-muted">Sans limite</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('annonces.show', $annonce) }}" class="btn btn-info btn-sm">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('annonces.edit', $annonce) }}" class="btn btn-warning btn-sm">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('annonces.destroy', $annonce) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm delete-button" data-message="Êtes-vous sûr de vouloir supprimer cette annonce ?">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-3">
                <i class="bi bi-inbox" style="font-size: 1.5rem; color: #ccc;"></i>
                <p class="text-muted mt-2 mb-2" style="font-size: 0.75rem;">Aucune annonce</p>
            </div>
        @endif
    </div>
</div>
@endsection
