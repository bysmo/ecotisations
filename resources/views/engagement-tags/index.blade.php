@extends('layouts.app')

@section('title', 'Gestion des Tags d\'Engagements')

@section('content')
<div class="page-header">
    <h1><i class="bi bi-tags"></i> Gestion des Tags d'Engagements</h1>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-list-ul"></i> Liste des Tags</span>
        <a href="{{ route('engagement-tags.create') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-circle"></i> Nouveau Tag
        </a>
    </div>
    <div class="card-body">
        <!-- Barre de recherche -->
        <form method="GET" action="{{ route('engagement-tags.index') }}" class="mb-3">
            <div class="row g-2">
                <div class="col-md-10">
                    <input type="text" 
                           name="search" 
                           class="form-control form-control-sm" 
                           placeholder="Rechercher un tag..." 
                           value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary btn-sm w-100">
                        <i class="bi bi-search"></i> Rechercher
                    </button>
                </div>
            </div>
            @if(request('search'))
                <div class="mt-2">
                    <a href="{{ route('engagement-tags.index') }}" class="btn btn-secondary btn-sm">
                        <i class="bi bi-x-circle"></i> Effacer
                    </a>
                </div>
            @endif
        </form>
        
        @if($tags->count() > 0 || $engagementsSansTag > 0)
            <style>
                .table-engagement-tags thead th {
                    padding: 0.15rem 0.35rem !important;
                    font-size: 0.6rem !important;
                    line-height: 1.05 !important;
                    vertical-align: middle !important;
                    font-weight: 300 !important;
                    font-family: 'Ubuntu', sans-serif !important;
                    color: var(--primary-dark-blue) !important;
                }
                .table-engagement-tags tbody td {
                    padding: 0.15rem 0.35rem !important;
                    font-size: 0.65rem !important;
                    line-height: 1.05 !important;
                    vertical-align: middle !important;
                    border-bottom: 1px solid #f0f0f0 !important;
                    font-weight: 300 !important;
                    font-family: 'Ubuntu', sans-serif !important;
                    color: var(--primary-dark-blue) !important;
                }
                .table-engagement-tags .btn {
                    padding: 0 !important;
                    font-size: 0.5rem !important;
                    line-height: 1 !important;
                    height: 18px !important;
                    width: 22px !important;
                    display: inline-flex !important;
                    align-items: center !important;
                    justify-content: center !important;
                }
                .table-engagement-tags .btn i {
                    font-size: 0.6rem !important;
                    line-height: 1 !important;
                }
                .table-engagement-tags .btn-group-sm > .btn,
                .table-engagement-tags .btn-group > .btn {
                    border-radius: 0.2rem !important;
                }
                .table-engagement-tags tbody tr:last-child td {
                    border-bottom: none !important;
                }
                table.table.table-engagement-tags.table-hover tbody tr {
                    background-color: #ffffff !important;
                    transition: background-color 0.2s ease !important;
                }
                table.table.table-engagement-tags.table-hover tbody tr:nth-child(even) {
                    background-color: #d4dde8 !important;
                }
                table.table.table-engagement-tags.table-hover tbody tr:hover {
                    background-color: #b8c7d9 !important;
                    cursor: pointer !important;
                }
                table.table.table-engagement-tags.table-hover tbody tr:nth-child(even):hover {
                    background-color: #9fb3cc !important;
                }
            </style>
            <div class="table-responsive">
                <table class="table table-engagement-tags table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Tag</th>
                            <th class="text-end">Nombre d'engagements</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if($engagementsSansTag > 0)
                            <tr>
                                <td>
                                    <span class="text-muted"><i class="bi bi-dash-circle"></i> Sans tag</span>
                                </td>
                                <td class="text-end">
                                    <span class="text-muted">{{ $engagementsSansTag }}</span>
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('engagements.index', ['tag' => '']) }}" class="btn btn-sm btn-outline-primary" title="Voir engagements">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @endif
                        @foreach($tags as $tag)
                            <tr>
                                <td>
                                    {{ $tag->nom }}
                                </td>
                                <td class="text-end">
                                    {{ $tag->nombre_engagements ?? 0 }}
                                </td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a href="{{ route('engagement-tags.show', urlencode($tag->nom)) }}" class="btn btn-sm btn-outline-primary" title="Voir engagements">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('engagement-tags.edit', $tag) }}" class="btn btn-sm btn-outline-warning" title="Modifier">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form action="{{ route('engagement-tags.destroy', $tag) }}" 
                                              method="POST" 
                                              class="d-inline delete-form"
                                              data-message="Êtes-vous sûr de vouloir supprimer ce tag ? Tous les engagements associés perdront leur tag.">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Supprimer">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-3">
                <i class="bi bi-inbox" style="font-size: 1.5rem; color: #ccc;"></i>
                <p class="text-muted mt-2 mb-2" style="font-size: 0.75rem;">Aucun tag défini</p>
                <a href="{{ route('engagement-tags.create') }}" class="btn btn-primary btn-sm">
                    <i class="bi bi-plus-circle"></i> Créer un tag
                </a>
            </div>
        @endif
    </div>
</div>
@endsection
