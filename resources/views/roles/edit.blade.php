@extends('layouts.app')

@section('title', 'Modifier un Rôle')

@section('content')
<div class="page-header">
    <h1><i class="bi bi-pencil"></i> Modifier le Rôle : {{ $role->nom }}</h1>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-info-circle"></i> Informations du Rôle
            </div>
            <div class="card-body">
                <form action="{{ route('roles.update', $role) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-3">
                        <label for="nom" class="form-label">
                            Nom du rôle <span class="text-danger">*</span>
                        </label>
                        <input type="text" 
                               class="form-control @error('nom') is-invalid @enderror" 
                               id="nom" 
                               name="nom" 
                               value="{{ old('nom', $role->nom) }}" 
                               required>
                        @error('nom')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                  id="description" 
                                  name="description" 
                                  rows="2">{{ old('description', $role->description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3 form-check">
                        <input type="checkbox" 
                               class="form-check-input" 
                               id="actif" 
                               name="actif" 
                               value="1"
                               {{ old('actif', $role->actif) ? 'checked' : '' }}>
                        <label class="form-check-label" for="actif">
                            Rôle actif
                        </label>
                    </div>
                    
                    <hr class="my-4">
                    
                    <h5 class="mb-3" style="font-weight: 300; font-family: 'Ubuntu', sans-serif; color: var(--primary-dark-blue);">
                        <i class="bi bi-shield-check"></i> Permissions
                    </h5>
                    
                    @if($permissionsByCategory->count() === 0)
                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle"></i> 
                            <strong>Aucune permission trouvée dans la base de données.</strong><br>
                            <small>Vous devez d'abord exécuter le seeder des permissions :</small><br>
                            <code>php artisan db:seed --class=PermissionSeeder</code>
                        </div>
                    @else
                        <div class="mb-3">
                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="selectAllPermissions()">
                                <i class="bi bi-check-all"></i> Tout sélectionner
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="deselectAllPermissions()">
                                <i class="bi bi-x-square"></i> Tout désélectionner
                            </button>
                        </div>
                    @endif
                    
                    @php
                        $selectedPermissions = old('permissions', $role->permissions->pluck('id')->toArray());
                    @endphp
                    
                    @foreach($permissionsByCategory as $categorie => $perms)
                        <div class="card mb-3">
                            <div class="card-header" style="background-color: var(--primary-dark-blue); color: white; font-weight: 300; font-family: 'Ubuntu', sans-serif;">
                                <i class="bi bi-folder"></i> {{ $categorie ?? 'Autres' }}
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    @foreach($perms as $permission)
                                        <div class="col-md-6 mb-3">
                                            <div class="form-check">
                                                <input class="form-check-input permission-checkbox" 
                                                       type="checkbox" 
                                                       id="permission_{{ $permission->id }}" 
                                                       name="permissions[]" 
                                                       value="{{ $permission->id }}"
                                                       {{ in_array($permission->id, $selectedPermissions) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="permission_{{ $permission->id }}" style="font-weight: 300; font-family: 'Ubuntu', sans-serif; cursor: pointer;">
                                                    <strong>{{ $permission->nom }}</strong>
                                                    @if($permission->description)
                                                        <br><small class="text-muted" style="font-size: 0.75rem;">{{ $permission->description }}</small>
                                                    @endif
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endforeach
                    
                    <div class="d-flex justify-content-between mt-4">
                        <a href="{{ route('roles.index') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Retour
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle"></i> Enregistrer les modifications
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-info-circle"></i> À propos des Rôles
            </div>
            <div class="card-body">
                <h6 class="mb-3" style="font-weight: 300; font-family: 'Ubuntu', sans-serif; color: var(--primary-dark-blue);">
                    <i class="bi bi-pencil-square"></i> Modification d'un rôle
                </h6>
                <p style="font-size: 0.75rem; line-height: 1.5; font-weight: 300; font-family: 'Ubuntu', sans-serif; color: #666;">
                    Vous pouvez modifier le nom, la description, le statut et les permissions d'un rôle. Les modifications seront appliquées à tous les utilisateurs ayant ce rôle.
                </p>
                
                <h6 class="mt-4 mb-3" style="font-weight: 300; font-family: 'Ubuntu', sans-serif; color: var(--primary-dark-blue);">
                    <i class="bi bi-shield-check"></i> Permissions
                </h6>
                <ul style="font-size: 0.75rem; line-height: 1.8; font-weight: 300; font-family: 'Ubuntu', sans-serif; color: #666; padding-left: 1.2rem;">
                    <li>Sélectionnez les permissions nécessaires pour ce rôle</li>
                    <li>Les permissions sont organisées par catégories</li>
                    <li>Utilisez "Tout sélectionner" ou "Tout désélectionner" pour gagner du temps</li>
                    <li>Les changements prennent effet immédiatement</li>
                </ul>
                
                <h6 class="mt-4 mb-3" style="font-weight: 300; font-family: 'Ubuntu', sans-serif; color: var(--primary-dark-blue);">
                    <i class="bi bi-lightbulb"></i> Conseils
                </h6>
                <p style="font-size: 0.75rem; line-height: 1.5; font-weight: 300; font-family: 'Ubuntu', sans-serif; color: #666;">
                    Assurez-vous de ne pas retirer des permissions essentielles aux utilisateurs qui dépendent de ce rôle. Vérifiez les utilisateurs affectés avant de désactiver un rôle.
                </p>
            </div>
        </div>
    </div>
</div>

<script>
function selectAllPermissions() {
    document.querySelectorAll('.permission-checkbox').forEach(checkbox => {
        checkbox.checked = true;
    });
}

function deselectAllPermissions() {
    document.querySelectorAll('.permission-checkbox').forEach(checkbox => {
        checkbox.checked = false;
    });
}
</script>
@endsection
