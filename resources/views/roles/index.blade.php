@extends('layouts.app')

@section('title', 'Rôles et Permissions')

@section('content')
<div class="page-header">
    <h1><i class="bi bi-shield-check"></i> Gestion des Rôles et Permissions</h1>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-list-ul"></i> Liste des Rôles</span>
        <a href="{{ route('roles.create') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-circle"></i> Créer un Rôle
        </a>
    </div>
    <div class="card-body">
        <style>
            .table-roles {
                margin-bottom: 0;
            }
            .table-roles thead th {
                padding: 0.15rem 0.35rem !important;
                font-size: 0.6rem !important;
                line-height: 1.05 !important;
                vertical-align: middle !important;
                font-weight: 300 !important;
                font-family: 'Ubuntu', sans-serif !important;
                color: var(--primary-dark-blue) !important;
            }
            .table-roles tbody td {
                padding: 0.15rem 0.35rem !important;
                font-size: 0.65rem !important;
                line-height: 1.05 !important;
                vertical-align: middle !important;
                border-bottom: 1px solid #f0f0f0 !important;
                font-weight: 300 !important;
                font-family: 'Ubuntu', sans-serif !important;
                color: var(--primary-dark-blue) !important;
            }
            .table-roles tbody tr:last-child td {
                border-bottom: none !important;
            }
            table.table.table-roles.table-hover tbody tr {
                background-color: #ffffff !important;
                transition: background-color 0.2s ease !important;
            }
            table.table.table-roles.table-hover tbody tr:nth-child(even) {
                background-color: #d4dde8 !important;
            }
            table.table.table-roles.table-hover tbody tr:hover {
                background-color: #b8c7d9 !important;
                cursor: pointer !important;
            }
            table.table.table-roles.table-hover tbody tr:nth-child(even):hover {
                background-color: #9fb3cc !important;
            }
            .table-roles .btn {
                padding: 0.1rem 0.25rem !important;
                font-size: 0.55rem !important;
                line-height: 1.1 !important;
            }
            .table-roles .btn i {
                font-size: 0.65rem !important;
            }
        </style>
        <div class="table-responsive">
            <table class="table table-roles table-striped table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nom</th>
                        <th>Slug</th>
                        <th>Description</th>
                        <th>Permissions</th>
                        <th>Utilisateurs</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($roles as $role)
                        <tr>
                            <td>{{ $role->id }}</td>
                            <td>{{ $role->nom }}</td>
                            <td><code>{{ $role->slug }}</code></td>
                            <td>{{ $role->description ?? '-' }}</td>
                            <td>{{ $role->permissions->count() }} permission(s)</td>
                            <td>{{ $role->users->count() }} utilisateur(s)</td>
                            <td>
                                @if($role->actif)
                                    <i class="bi bi-check-circle"></i> Actif
                                @else
                                    <i class="bi bi-x-circle"></i> Inactif
                                @endif
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm" role="group">
                                    <a href="{{ route('roles.show', $role) }}" class="btn btn-outline-primary" title="Voir">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('roles.edit', $role) }}" class="btn btn-outline-warning" title="Modifier">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('roles.destroy', $role) }}" method="POST" class="d-inline" onsubmit="return confirmAction('Êtes-vous sûr de vouloir supprimer ce rôle ?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger" title="Supprimer">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center">Aucun rôle trouvé.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="mt-3 d-flex justify-content-between align-items-center">
            <div>
                <a href="{{ route('roles.assign-users') }}" class="btn btn-secondary">
                    <i class="bi bi-person-check"></i> Affecter des Rôles aux Utilisateurs
                </a>
                @php
                    $adminRole = $roles->firstWhere('slug', 'admin');
                @endphp
                @if($adminRole)
                    <form action="{{ route('roles.admin.assign-all-permissions') }}" method="POST" class="d-inline ms-2" onsubmit="return confirmAction('Êtes-vous sûr de vouloir attribuer toutes les permissions au rôle Administrateur ?');">
                        @csrf
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-shield-check"></i> Attribuer toutes les permissions à l'Administrateur
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
