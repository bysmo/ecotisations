@extends('layouts.app')

@section('title', 'Affecter des Rôles aux Utilisateurs')

@section('content')
<div class="page-header">
    <h1><i class="bi bi-person-check"></i> Affecter des Rôles aux Utilisateurs</h1>
</div>

<div class="card">
    <div class="card-header">
        <i class="bi bi-list-ul"></i> Liste des Utilisateurs
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nom</th>
                        <th>Email</th>
                        <th>Rôles actuels</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        <tr>
                            <td>{{ $user->id }}</td>
                            <td><strong>{{ $user->name }}</strong></td>
                            <td>{{ $user->email }}</td>
                            <td>
                                @if($user->roles->count() > 0)
                                    @foreach($user->roles as $role)
                                        <span class="badge bg-primary me-1">{{ $role->nom }}</span>
                                        <form action="{{ route('users.remove-role', ['user' => $user, 'role' => $role]) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm p-0 text-danger" title="Retirer ce rôle">
                                                <i class="bi bi-x-circle"></i>
                                            </button>
                                        </form>
                                    @endforeach
                                @else
                                    <span class="text-muted">Aucun rôle</span>
                                @endif
                            </td>
                            <td>
                                <form action="{{ route('users.assign-role', $user) }}" method="POST" class="d-inline">
                                    @csrf
                                    <div class="input-group input-group-sm" style="width: 300px;">
                                        <select name="role_id" class="form-select form-select-sm" required>
                                            <option value="">Sélectionner un rôle</option>
                                            @foreach($roles as $role)
                                                @if(!$user->roles->contains($role->id))
                                                    <option value="{{ $role->id }}">{{ $role->nom }}</option>
                                                @endif
                                            @endforeach
                                        </select>
                                        <button type="submit" class="btn btn-primary btn-sm">
                                            <i class="bi bi-plus-circle"></i> Ajouter
                                        </button>
                                    </div>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center">Aucun utilisateur trouvé.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="mt-3">
            <a href="{{ route('roles.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Retour à la liste des rôles
            </a>
        </div>
    </div>
</div>
@endsection
