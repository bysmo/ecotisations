@extends('layouts.app')

@section('title', 'Créer un Rôle')

@section('content')
<div class="page-header">
    <h1><i class="bi bi-plus-circle"></i> Créer un Nouveau Rôle</h1>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-info-circle"></i> Informations du Rôle
            </div>
            <div class="card-body">
                <form action="{{ route('roles.store') }}" method="POST">
                    @csrf
                    
                    <div class="mb-3">
                        <label for="nom" class="form-label">
                            Nom du rôle <span class="text-danger">*</span>
                        </label>
                        <input type="text" 
                               class="form-control @error('nom') is-invalid @enderror" 
                               id="nom" 
                               name="nom" 
                               value="{{ old('nom') }}" 
                               placeholder="Ex: Gestionnaire, Secrétaire, etc."
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
                                  rows="2">{{ old('description') }}</textarea>
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
                               {{ old('actif', true) ? 'checked' : '' }}>
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
                                                       {{ in_array($permission->id, old('permissions', [])) ? 'checked' : '' }}>
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
                            <i class="bi bi-check-circle"></i> Créer le Rôle
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
                    <i class="bi bi-shield-check"></i> Qu'est-ce qu'un rôle ?
                </h6>
                <p style="font-size: 0.75rem; line-height: 1.5; font-weight: 300; font-family: 'Ubuntu', sans-serif; color: #666;">
                    Un rôle définit un ensemble de permissions qui peut être affecté à un ou plusieurs utilisateurs. Cela permet de gérer facilement les accès selon les fonctions de chaque utilisateur.
                </p>
                
                <h6 class="mt-4 mb-3" style="font-weight: 300; font-family: 'Ubuntu', sans-serif; color: var(--primary-dark-blue);">
                    <i class="bi bi-lightbulb"></i> Création d'un rôle
                </h6>
                <ul style="font-size: 0.75rem; line-height: 1.8; font-weight: 300; font-family: 'Ubuntu', sans-serif; color: #666; padding-left: 1.2rem;">
                    <li>Donnez un nom clair au rôle</li>
                    <li>Sélectionnez les permissions nécessaires</li>
                    <li>Le rôle peut être actif ou inactif</li>
                    <li>Vous pourrez l'affecter aux utilisateurs après création</li>
                </ul>
                
                <h6 class="mt-4 mb-3" style="font-weight: 300; font-family: 'Ubuntu', sans-serif; color: var(--primary-dark-blue);">
                    <i class="bi bi-shield-check"></i> Rôles par défaut
                </h6>
                <p style="font-size: 0.75rem; line-height: 1.5; font-weight: 300; font-family: 'Ubuntu', sans-serif; color: #666;">
                    <strong>Administrateur :</strong> Accès complet<br>
                    <strong>Trésorier :</strong> Gestion financière<br>
                    <strong>Membre :</strong> Consultation uniquement
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
