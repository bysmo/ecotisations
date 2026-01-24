@extends('layouts.app')

@section('title', 'Gestion des Segments')

@section('content')
<div class="page-header">
    <h1><i class="bi bi-tags"></i> Gestion des Segments</h1>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-list-ul"></i> Liste des Segments</span>
        <a href="{{ route('segments.create') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-circle"></i> Nouveau Segment
        </a>
    </div>
    <div class="card-body">
        <!-- Barre de recherche -->
        <form method="GET" action="{{ route('segments.index') }}" class="mb-3">
            <div class="row g-2">
                <div class="col-md-10">
                    <input type="text" 
                           name="search" 
                           class="form-control form-control-sm" 
                           placeholder="Rechercher un segment..." 
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
                    <a href="{{ route('segments.index') }}" class="btn btn-secondary btn-sm">
                        <i class="bi bi-x-circle"></i> Effacer
                    </a>
                </div>
            @endif
        </form>
        
        @if($segments->count() > 0 || $membresSansSegment > 0)
            <style>
                .table-segments thead th {
                    padding: 0.15rem 0.35rem !important;
                    font-size: 0.6rem !important;
                    line-height: 1.05 !important;
                    vertical-align: middle !important;
                    font-weight: 300 !important;
                    font-family: 'Ubuntu', sans-serif !important;
                    color: var(--primary-dark-blue) !important;
                }
                .table-segments tbody td {
                    padding: 0.15rem 0.35rem !important;
                    font-size: 0.65rem !important;
                    line-height: 1.05 !important;
                    vertical-align: middle !important;
                    border-bottom: 1px solid #f0f0f0 !important;
                    font-weight: 300 !important;
                    font-family: 'Ubuntu', sans-serif !important;
                    color: var(--primary-dark-blue) !important;
                }
                .table-segments .btn {
                    padding: 0 !important;
                    font-size: 0.5rem !important;
                    line-height: 1 !important;
                    height: 18px !important;
                    width: 22px !important;
                    display: inline-flex !important;
                    align-items: center !important;
                    justify-content: center !important;
                }
                .table-segments .btn i {
                    font-size: 0.6rem !important;
                    line-height: 1 !important;
                }
                .table-segments .btn-group-sm > .btn,
                .table-segments .btn-group > .btn {
                    border-radius: 0.2rem !important;
                }
                .table-segments tbody tr:last-child td {
                    border-bottom: none !important;
                }
                table.table.table-segments.table-hover tbody tr {
                    background-color: #ffffff !important;
                    transition: background-color 0.2s ease !important;
                }
                table.table.table-segments.table-hover tbody tr:nth-child(even) {
                    background-color: #d4dde8 !important;
                }
                table.table.table-segments.table-hover tbody tr:hover {
                    background-color: #b8c7d9 !important;
                    cursor: pointer !important;
                }
                table.table.table-segments.table-hover tbody tr:nth-child(even):hover {
                    background-color: #9fb3cc !important;
                }
            </style>
            <div class="table-responsive">
                <table class="table table-segments table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Segment</th>
                            <th class="text-end">Nombre de membres</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if($membresSansSegment > 0)
                            <tr>
                                <td>
                                    <span class="text-muted"><i class="bi bi-dash-circle"></i> Sans segment</span>
                                </td>
                                <td class="text-end">
                                    <span class="text-muted" style="font-size: 0.65rem;">{{ $membresSansSegment }}</span>
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('membres.index', ['segment' => '']) }}" class="btn btn-sm btn-outline-primary" title="Voir membres">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @endif
                        @foreach($segments as $segment)
                            <tr>
                                <td>
                                    {{ $segment->nom }}
                                </td>
                                <td class="text-end">
                                    <span style="font-size: 0.65rem;">{{ $segment->nombre_membres ?? 0 }}</span>
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('segments.show', urlencode($segment->nom)) }}" class="btn btn-sm btn-outline-primary" title="Voir membres">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-3">
                <i class="bi bi-inbox" style="font-size: 1.5rem; color: #ccc;"></i>
                <p class="text-muted mt-2 mb-2" style="font-size: 0.75rem;">Aucun segment défini</p>
                <a href="{{ route('membres.create') }}" class="btn btn-primary btn-sm">
                    <i class="bi bi-plus-circle"></i> Créer un membre avec segment
                </a>
            </div>
        @endif
    </div>
</div>
@endsection
