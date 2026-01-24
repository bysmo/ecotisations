@extends('layouts.app')

@section('title', 'Configurations SMTP')

@section('content')
<div class="page-header">
    <h1><i class="bi bi-envelope"></i> Configurations SMTP</h1>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-list-ul"></i> Liste des Configurations</span>
        <a href="{{ route('smtp.create') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-circle"></i> Nouvelle configuration
        </a>
    </div>
    <div class="card-body">
        @if($smtps->count() > 0)
            <style>
                .table-smtp {
                    margin-bottom: 0;
                }
                .table-smtp thead th {
                    padding: 0.15rem 0.35rem !important;
                    font-size: 0.6rem !important;
                    line-height: 1.05 !important;
                    vertical-align: middle !important;
                    font-weight: 300 !important;
                    font-family: 'Ubuntu', sans-serif !important;
                    color: var(--primary-dark-blue) !important;
                }
                .table-smtp tbody td {
                    padding: 0.15rem 0.35rem !important;
                    font-size: 0.65rem !important;
                    line-height: 1.05 !important;
                    vertical-align: middle !important;
                    border-bottom: 1px solid #f0f0f0 !important;
                    font-weight: 300 !important;
                    font-family: 'Ubuntu', sans-serif !important;
                    color: var(--primary-dark-blue) !important;
                }
                .table-smtp tbody tr:last-child td {
                    border-bottom: none !important;
                }
                table.table.table-smtp.table-hover tbody tr {
                    background-color: #ffffff !important;
                    transition: background-color 0.2s ease !important;
                }
                table.table.table-smtp.table-hover tbody tr:nth-child(even) {
                    background-color: #d4dde8 !important;
                }
                table.table.table-smtp.table-hover tbody tr:hover {
                    background-color: #b8c7d9 !important;
                    cursor: pointer !important;
                }
                table.table.table-smtp.table-hover tbody tr:nth-child(even):hover {
                    background-color: #9fb3cc !important;
                }
                .table-smtp .btn {
                    padding: 0.05rem 0.2rem !important;
                    font-size: 0.5rem !important;
                    line-height: 1 !important;
                    height: auto !important;
                    min-height: auto !important;
                }
                .table-smtp .btn i {
                    font-size: 0.6rem !important;
                    line-height: 1 !important;
                }
                .table-smtp .badge {
                    font-size: 0.55rem !important;
                    padding: 0.15rem 0.35rem !important;
                    font-weight: 300 !important;
                    font-family: 'Ubuntu', sans-serif !important;
                }
            </style>
            <div class="table-responsive">
                <table class="table table-smtp table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Nom</th>
                            <th>Host</th>
                            <th>Port</th>
                            <th>From</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($smtps as $smtp)
                            <tr>
                                <td>
                                    <strong>{{ $smtp->nom }}</strong>
                                </td>
                                <td>{{ $smtp->host }}</td>
                                <td>{{ $smtp->port }}</td>
                                <td>
                                    {{ $smtp->from_name }} &lt;{{ $smtp->from_address }}&gt;
                                </td>
                                <td>
                                    @if($smtp->actif)
                                        <span class="badge bg-success">Actif</span>
                                    @else
                                        <span class="badge bg-secondary">Inactif</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm" role="group">
                                        <form action="{{ route('smtp.test', $smtp) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-outline-success" title="Tester">
                                                <i class="bi bi-send"></i>
                                            </button>
                                        </form>
                                        <a href="{{ route('smtp.show', $smtp) }}" class="btn btn-outline-info" title="Voir">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('smtp.edit', $smtp) }}" class="btn btn-outline-warning" title="Modifier">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form action="{{ route('smtp.destroy', $smtp) }}" method="POST" class="d-inline" onsubmit="return confirmAction('Êtes-vous sûr de vouloir supprimer cette configuration ?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger" title="Supprimer">
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
                <p class="text-muted mt-2 mb-2" style="font-size: 0.75rem;">Aucune configuration SMTP</p>
            </div>
        @endif
    </div>
</div>
@endsection
