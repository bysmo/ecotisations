@extends('layouts.app')

@section('title', 'Modifier Configuration SMTP')

@section('content')
<div class="page-header">
    <h1><i class="bi bi-envelope"></i> Modifier Configuration SMTP</h1>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-pencil"></i> Modifier la Configuration SMTP
            </div>
            <div class="card-body">
                <form action="{{ route('smtp.update', $smtp) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-3">
                        <label for="nom" class="form-label">
                            Nom <span class="text-danger">*</span>
                        </label>
                        <input type="text" 
                               class="form-control @error('nom') is-invalid @enderror" 
                               id="nom" 
                               name="nom" 
                               value="{{ old('nom', $smtp->nom) }}" 
                               required>
                        @error('nom')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="row">
                        <div class="col-md-8 mb-3">
                            <label for="host" class="form-label">
                                Serveur SMTP <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control @error('host') is-invalid @enderror" 
                                   id="host" 
                                   name="host" 
                                   value="{{ old('host', $smtp->host) }}" 
                                   required>
                            @error('host')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="port" class="form-label">
                                Port <span class="text-danger">*</span>
                            </label>
                            <input type="number" 
                                   class="form-control @error('port') is-invalid @enderror" 
                                   id="port" 
                                   name="port" 
                                   value="{{ old('port', $smtp->port) }}" 
                                   min="1" 
                                   max="65535"
                                   required>
                            @error('port')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="username" class="form-label">
                                Nom d'utilisateur <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control @error('username') is-invalid @enderror" 
                                   id="username" 
                                   name="username" 
                                   value="{{ old('username', $smtp->username) }}" 
                                   required>
                            @error('username')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="password" class="form-label">
                                Mot de passe
                            </label>
                            <input type="password" 
                                   class="form-control @error('password') is-invalid @enderror" 
                                   id="password" 
                                   name="password">
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted" style="font-size: 0.7rem;">Laisser vide pour conserver le mot de passe actuel</small>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="encryption" class="form-label">
                            Chiffrement <span class="text-danger">*</span>
                        </label>
                        <select class="form-select @error('encryption') is-invalid @enderror" 
                                id="encryption" 
                                name="encryption" 
                                required>
                            <option value="tls" {{ old('encryption', $smtp->encryption) === 'tls' ? 'selected' : '' }}>TLS</option>
                            <option value="ssl" {{ old('encryption', $smtp->encryption) === 'ssl' ? 'selected' : '' }}>SSL</option>
                            <option value="none" {{ old('encryption', $smtp->encryption) === 'none' ? 'selected' : '' }}>Aucun</option>
                        </select>
                        @error('encryption')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="from_address" class="form-label">
                                Adresse email expéditeur <span class="text-danger">*</span>
                            </label>
                            <input type="email" 
                                   class="form-control @error('from_address') is-invalid @enderror" 
                                   id="from_address" 
                                   name="from_address" 
                                   value="{{ old('from_address', $smtp->from_address) }}" 
                                   required>
                            @error('from_address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="from_name" class="form-label">
                                Nom expéditeur <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control @error('from_name') is-invalid @enderror" 
                                   id="from_name" 
                                   name="from_name" 
                                   value="{{ old('from_name', $smtp->from_name) }}" 
                                   required>
                            @error('from_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" 
                                   type="checkbox" 
                                   id="actif" 
                                   name="actif" 
                                   value="1"
                                   {{ old('actif', $smtp->actif) ? 'checked' : '' }}>
                            <label class="form-check-label" for="actif">
                                Activer cette configuration
                            </label>
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('smtp.index') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Retour
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle"></i> Mettre à jour
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
