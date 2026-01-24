@extends('layouts.app')

@section('title', 'Détails du Rôle')

@section('content')
<div class="page-header">
    <h1><i class="bi bi-shield-check"></i> Rôle : {{ $role->nom }}</h1>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card mb-3">
            <div class="card-header">
                <i class="bi bi-info-circle"></i> Informations
            </div>
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-sm-3" style="font-weight: 300; font-family: 'Ubuntu', sans-serif;">Nom</dt>
                    <dd class="col-sm-9" style="font-weight: 300; font-family: 'Ubuntu', sans-serif;">
                        <strong>{{ $role->nom }}</strong>
                    </dd>
                    
                    <dt class="col-sm-3" style="font-weight: 300; font-family: 'Ubuntu', sans-serif;">Slug</dt>
                    <dd class="col-sm-9" style="font-weight: 300; font-family: 'Ubuntu', sans-serif;">
                        <code>{{ $role->slug }}</code>
                    </dd>
                    
                    <dt class="col-sm-3" style="font-weight: 300; font-family: 'Ubuntu', sans-serif;">Description</dt>
                    <dd class="col-sm-9" style="font-weight: 300; font-family: 'Ubuntu', sans-serif;">
                        {{ $role->description ?? '-' }}
                    </dd>
                    
                    <dt class="col-sm-3" style="font-weight: 300; font-family: 'Ubuntu', sans-serif;">Statut</dt>
                    <dd class="col-sm-9" style="font-weight: 300; font-family: 'Ubuntu', sans-serif;">
                        @if($role->actif)
                            <span class="badge bg-success">Actif</span>
                        @else
                            <span class="badge bg-secondary">Inactif</span>
                        @endif
                    </dd>
                </dl>
            </div>
        </div>
        
        <div class="card mb-3">
            <div class="card-header">
                <i class="bi bi-shield-check"></i> Permissions ({{ $role->permissions->count() }})
            </div>
            <div class="card-body">
                @if($role->permissions->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Catégorie</th>
                                    <th>Permission</th>
                                    <th>Description</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($role->permissions->groupBy('categorie') as $categorie => $perms)
                                    @foreach($perms as $permission)
                                        <tr>
                                            <td>{{ $categorie ?? 'Autres' }}</td>
                                            <td><strong>{{ $permission->nom }}</strong></td>
                                            <td>{{ $permission->description ?? '-' }}</td>
                                        </tr>
                                    @endforeach
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-muted mb-0">Aucune permission assignée.</p>
                @endif
            </div>
        </div>
        
        <div class="card mb-3">
            <div class="card-header">
                <i class="bi bi-people"></i> Utilisateurs ({{ $role->users->count() }})
            </div>
            <div class="card-body">
                @if($role->users->count() > 0)
                    <ul class="list-unstyled mb-0">
                        @foreach($role->users as $user)
                            <li><i class="bi bi-person"></i> {{ $user->name }} ({{ $user->email }})</li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-muted mb-0">Aucun utilisateur assigné.</p>
                @endif
            </div>
        </div>
        
        <div class="d-flex justify-content-between">
            <a href="{{ route('roles.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Retour
            </a>
            <div>
                <a href="{{ route('roles.edit', $role) }}" class="btn btn-warning">
                    <i class="bi bi-pencil"></i> Modifier
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
