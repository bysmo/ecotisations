@extends('layouts.app')

@section('title', 'Templates d\'Emails')

@section('content')
<div class="page-header">
    <h1><i class="bi bi-file-text"></i> Templates d'Emails</h1>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-list-ul"></i> Liste des Templates</span>
        <a href="{{ route('email-templates.create') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-circle"></i> Nouveau template
        </a>
    </div>
    <div class="card-body">
        @if($templates->count() > 0)
            <style>
                .table-email-templates {
                    margin-bottom: 0;
                }
                .table-email-templates thead th {
                    padding: 0.15rem 0.35rem !important;
                    font-size: 0.6rem !important;
                    line-height: 1.05 !important;
                    vertical-align: middle !important;
                    font-weight: 300 !important;
                    font-family: 'Ubuntu', sans-serif !important;
                    color: var(--primary-dark-blue) !important;
                }
                .table-email-templates tbody td {
                    padding: 0.15rem 0.35rem !important;
                    font-size: 0.65rem !important;
                    line-height: 1.05 !important;
                    vertical-align: middle !important;
                    border-bottom: 1px solid #f0f0f0 !important;
                    font-weight: 300 !important;
                    font-family: 'Ubuntu', sans-serif !important;
                    color: var(--primary-dark-blue) !important;
                }
                .table-email-templates tbody tr:last-child td {
                    border-bottom: none !important;
                }
                table.table.table-email-templates.table-hover tbody tr {
                    background-color: #ffffff !important;
                    transition: background-color 0.2s ease !important;
                }
                table.table.table-email-templates.table-hover tbody tr:nth-child(even) {
                    background-color: #d4dde8 !important;
                }
                table.table.table-email-templates.table-hover tbody tr:hover {
                    background-color: #b8c7d9 !important;
                    cursor: pointer !important;
                }
                table.table.table-email-templates.table-hover tbody tr:nth-child(even):hover {
                    background-color: #9fb3cc !important;
                }
                .table-email-templates .btn {
                    padding: 0.05rem 0.2rem !important;
                    font-size: 0.5rem !important;
                    line-height: 1 !important;
                    height: auto !important;
                    min-height: auto !important;
                }
                .table-email-templates .btn i {
                    font-size: 0.6rem !important;
                    line-height: 1 !important;
                }
                .table-email-templates .badge {
                    font-size: 0.55rem !important;
                    padding: 0.15rem 0.35rem !important;
                    font-weight: 300 !important;
                    font-family: 'Ubuntu', sans-serif !important;
                }
            </style>
            <div class="table-responsive">
                <table class="table table-email-templates table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Nom</th>
                            <th>Type</th>
                            <th>Sujet</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($templates as $template)
                            <tr>
                                <td>
                                    <strong>{{ $template->nom }}</strong>
                                </td>
                                <td>
                                    <span class="badge bg-info">
                                        @if($template->type === 'paiement')
                                            Paiement
                                        @elseif($template->type === 'engagement')
                                            Engagement
                                        @else
                                            Autre
                                        @endif
                                    </span>
                                </td>
                                <td>{{ $template->sujet }}</td>
                                <td>
                                    @if($template->actif)
                                        <span class="badge bg-success">Actif</span>
                                    @else
                                        <span class="badge bg-secondary">Inactif</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a href="{{ route('email-templates.show', $template) }}" class="btn btn-outline-info" title="Voir">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('email-templates.edit', $template) }}" class="btn btn-outline-warning" title="Modifier">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form action="{{ route('email-templates.destroy', $template) }}" method="POST" class="d-inline" onsubmit="return confirmAction('Êtes-vous sûr de vouloir supprimer ce template ?');">
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
                <p class="text-muted mt-2 mb-2" style="font-size: 0.75rem;">Aucun template</p>
            </div>
        @endif
    </div>
</div>
@endsection
