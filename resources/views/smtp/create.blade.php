@extends('layouts.app')

@section('title', 'Nouvelle Configuration SMTP')

@section('content')
<div class="page-header">
    <h1><i class="bi bi-envelope"></i> Nouvelle Configuration SMTP</h1>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-plus-circle"></i> Créer une Configuration SMTP
            </div>
            <div class="card-body">
                <form action="{{ route('smtp.store') }}" method="POST">
                    @csrf
                    
                    <div class="mb-3">
                        <label for="nom" class="form-label">
                            Nom <span class="text-danger">*</span>
                        </label>
                        <input type="text" 
                               class="form-control @error('nom') is-invalid @enderror" 
                               id="nom" 
                               name="nom" 
                               value="{{ old('nom') }}" 
                               required>
                        @error('nom')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted" style="font-size: 0.7rem;">Nom identifiant cette configuration</small>
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
                                   value="{{ old('host') }}" 
                                   placeholder="smtp.example.com"
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
                                   value="{{ old('port', 587) }}" 
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
                                   value="{{ old('username') }}" 
                                   required>
                            @error('username')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="password" class="form-label">
                                Mot de passe <span class="text-danger">*</span>
                            </label>
                            <input type="password" 
                                   class="form-control @error('password') is-invalid @enderror" 
                                   id="password" 
                                   name="password" 
                                   required>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
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
                            <option value="tls" {{ old('encryption', 'tls') === 'tls' ? 'selected' : '' }}>TLS</option>
                            <option value="ssl" {{ old('encryption') === 'ssl' ? 'selected' : '' }}>SSL</option>
                            <option value="none" {{ old('encryption') === 'none' ? 'selected' : '' }}>Aucun</option>
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
                                   value="{{ old('from_address') }}" 
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
                                   value="{{ old('from_name') }}" 
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
                                   {{ old('actif') ? 'checked' : '' }}>
                            <label class="form-check-label" for="actif">
                                Activer cette configuration
                            </label>
                        </div>
                        <small class="form-text text-muted" style="font-size: 0.7rem;">Si activée, les autres configurations seront désactivées</small>
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('smtp.index') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Retour
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle"></i> Créer la configuration
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-info-circle"></i> À propos de SMTP
            </div>
            <div class="card-body">
                <h6 class="mb-3" style="font-weight: 300; font-family: 'Ubuntu', sans-serif; color: var(--primary-dark-blue);">
                    <i class="bi bi-envelope-check"></i> Qu'est-ce que SMTP ?
                </h6>
                <p style="font-size: 0.75rem; line-height: 1.5; font-weight: 300; font-family: 'Ubuntu', sans-serif; color: #666;">
                    SMTP (Simple Mail Transfer Protocol) est le protocole standard utilisé pour l'envoi d'emails sur Internet. Cette configuration permet à l'application d'envoyer des emails aux membres, notamment pour confirmer leurs paiements.
                </p>
                
                <h6 class="mt-4 mb-3" style="font-weight: 300; font-family: 'Ubuntu', sans-serif; color: var(--primary-dark-blue);">
                    <i class="bi bi-server"></i> Paramètres courants
                </h6>
                <ul style="font-size: 0.75rem; line-height: 1.8; font-weight: 300; font-family: 'Ubuntu', sans-serif; color: #666; padding-left: 1.2rem;">
                    <li><strong>Gmail :</strong> smtp.gmail.com, Port 587 (TLS)</li>
                    <li><strong>Outlook :</strong> smtp-mail.outlook.com, Port 587 (TLS)</li>
                    <li><strong>Yahoo :</strong> smtp.mail.yahoo.com, Port 587 (TLS)</li>
                    <li><strong>Serveur personnalisé :</strong> Consultez votre hébergeur</li>
                </ul>
                
                <h6 class="mt-4 mb-3" style="font-weight: 300; font-family: 'Ubuntu', sans-serif; color: var(--primary-dark-blue);">
                    <i class="bi bi-shield-lock"></i> Sécurité
                </h6>
                <p style="font-size: 0.75rem; line-height: 1.5; font-weight: 300; font-family: 'Ubuntu', sans-serif; color: #666;">
                    <strong>TLS</strong> (recommandé) : Chiffrement des données lors de l'envoi<br>
                    <strong>SSL</strong> : Chiffrement avec connexion sécurisée<br>
                    <strong>Aucun</strong> : Non recommandé pour la production
                </p>
                
                <h6 class="mt-4 mb-3" style="font-weight: 300; font-family: 'Ubuntu', sans-serif; color: var(--primary-dark-blue);">
                    <i class="bi bi-lightbulb"></i> Astuce
                </h6>
                <p style="font-size: 0.75rem; line-height: 1.5; font-weight: 300; font-family: 'Ubuntu', sans-serif; color: #666;">
                    Testez toujours votre configuration après la création en utilisant le bouton "Tester" dans la liste des configurations SMTP. Cela permet de vérifier que les paramètres sont corrects avant de l'activer.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
