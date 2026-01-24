@extends('layouts.app')

@section('title', 'Détails de l\'Utilisateur')

@section('content')
<div class="page-header">
    <h1><i class="bi bi-person-circle"></i> Détails de l'Utilisateur</h1>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-info-circle"></i> Informations</span>
                <div>
                    <a href="{{ route('users.edit', $user) }}" class="btn btn-primary btn-sm">
                        <i class="bi bi-pencil"></i> Modifier
                    </a>
                    <a href="{{ route('users.index') }}" class="btn btn-secondary btn-sm">
                        <i class="bi bi-arrow-left"></i> Retour
                    </a>
                </div>
            </div>
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-sm-3">ID :</dt>
                    <dd class="col-sm-9">{{ $user->id }}</dd>
                    
                    <dt class="col-sm-3">Nom :</dt>
                    <dd class="col-sm-9">{{ $user->name }}</dd>
                    
                    <dt class="col-sm-3">Email :</dt>
                    <dd class="col-sm-9">{{ $user->email }}</dd>
                    
                    <dt class="col-sm-3">Rôles :</dt>
                    <dd class="col-sm-9">
                        @if($user->roles->count() > 0)
                            @foreach($user->roles as $role)
                                <span class="me-2">{{ $role->nom }}</span>
                            @endforeach
                        @else
                            <span class="text-muted">Aucun rôle</span>
                        @endif
                    </dd>
                    
                    <dt class="col-sm-3">Date de création :</dt>
                    <dd class="col-sm-9">{{ $user->created_at->format('d/m/Y à H:i') }}</dd>
                    
                    <dt class="col-sm-3">Dernière mise à jour :</dt>
                    <dd class="col-sm-9">{{ $user->updated_at->format('d/m/Y à H:i') }}</dd>
                </dl>
            </div>
        </div>
    </div>
</div>
@endsection
