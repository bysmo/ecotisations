@extends('layouts.app')

@section('title', 'Configuration PayDunya')

@section('content')
<div class="page-header">
    <h1><i class="bi bi-phone"></i> Configuration PayDunya</h1>
</div>

@if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-triangle"></i> 
        <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-gear"></i> Paramètres de Configuration
            </div>
            <div class="card-body">
                <form action="{{ route('paydunya.update') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row g-2 mb-2">
                        <div class="col-md-6">
                            <div class="form-check form-switch mb-2">
                                <input class="form-check-input" type="checkbox" id="enabled" name="enabled" value="1"
                                       {{ old('enabled', $config->enabled ?? false) ? 'checked' : '' }}>
                                <label class="form-check-label" for="enabled" style="font-weight: 300; font-family: 'Ubuntu', sans-serif; font-size: 0.75rem;">
                                    Activer PayDunya
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="row g-2 mb-2">
                        <div class="col-md-6">
                            <label for="mode" class="form-label">Mode <span class="text-danger">*</span></label>
                            <select class="form-select" id="mode" name="mode" required>
                                <option value="test" {{ old('mode', $config->mode ?? 'test') === 'test' ? 'selected' : '' }}>Test</option>
                                <option value="live" {{ old('mode', $config->mode ?? 'test') === 'live' ? 'selected' : '' }}>Production (Live)</option>
                            </select>
                            <small class="text-muted" style="font-size: 0.7rem;">Utilisez le mode Test pour les tests, et Live pour la production</small>
                        </div>
                    </div>

                    <div class="row g-2 mb-2">
                        <div class="col-md-12">
                            <label for="master_key" class="form-label">Master Key</label>
                            <input type="text" class="form-control" id="master_key" name="master_key" 
                                   value="{{ old('master_key', $config->master_key ?? '') }}" 
                                   placeholder="Votre Master Key PayDunya">
                            <small class="text-muted" style="font-size: 0.7rem;">Clé maître fournie par PayDunya</small>
                        </div>
                    </div>

                    <div class="row g-2 mb-2">
                        <div class="col-md-6">
                            <label for="private_key" class="form-label">Private Key</label>
                            <input type="text" class="form-control" id="private_key" name="private_key" 
                                   value="{{ old('private_key', $config->private_key ?? '') }}" 
                                   placeholder="Votre Private Key PayDunya">
                            <small class="text-muted" style="font-size: 0.7rem;">Clé privée fournie par PayDunya</small>
                        </div>
                        <div class="col-md-6">
                            <label for="public_key" class="form-label">Public Key</label>
                            <input type="text" class="form-control" id="public_key" name="public_key" 
                                   value="{{ old('public_key', $config->public_key ?? '') }}" 
                                   placeholder="Votre Public Key PayDunya">
                            <small class="text-muted" style="font-size: 0.7rem;">Clé publique fournie par PayDunya</small>
                        </div>
                    </div>

                    <div class="row g-2 mb-2">
                        <div class="col-md-12">
                            <label for="token" class="form-label">Token</label>
                            <input type="text" class="form-control" id="token" name="token" 
                                   value="{{ old('token', $config->token ?? '') }}" 
                                   placeholder="Votre Token PayDunya">
                            <small class="text-muted" style="font-size: 0.7rem;">Token d'authentification PayDunya</small>
                        </div>
                    </div>

                    <div class="row g-2 mb-2">
                        <div class="col-md-12">
                            <label for="ipn_url" class="form-label">URL IPN (Optionnel)</label>
                            <input type="url" class="form-control" id="ipn_url" name="ipn_url" 
                                   value="{{ old('ipn_url', $config->ipn_url ?? '') }}" 
                                   placeholder="https://votre-domaine.com/paydunya/ipn">
                            <small class="text-muted" style="font-size: 0.7rem;">
                                URL de notification instantanée de paiement (IPN). 
                                PayDunya enverra les notifications de paiement à cette URL.
                            </small>
                        </div>
                    </div>

                    <div class="row g-2">
                        <div class="col-md-12">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> Enregistrer la configuration
                            </button>
                            <a href="{{ route('payment-methods.index') }}" class="btn btn-secondary">
                                <i class="bi bi-arrow-left"></i> Retour à la liste des paiements
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-info-circle"></i> À propos de PayDunya
            </div>
            <div class="card-body">
                <h6 class="mb-3" style="font-weight: 300; font-family: 'Ubuntu', sans-serif; color: var(--primary-dark-blue);">
                    <i class="bi bi-phone"></i> Qu'est-ce que PayDunya ?
                </h6>
                <p style="font-size: 0.75rem; line-height: 1.5; font-weight: 300; font-family: 'Ubuntu', sans-serif; color: #666;">
                    PayDunya est une plateforme de paiement mobile qui permet d'accepter les paiements via Mobile Money (Orange Money, MTN Mobile Money, Moov Money, etc.) et par carte bancaire.
                </p>
                
                <h6 class="mt-4 mb-3" style="font-weight: 300; font-family: 'Ubuntu', sans-serif; color: var(--primary-dark-blue);">
                    <i class="bi bi-key"></i> Clés API nécessaires
                </h6>
                <ul style="font-size: 0.75rem; line-height: 1.8; font-weight: 300; font-family: 'Ubuntu', sans-serif; color: #666; padding-left: 1.2rem;">
                    <li><strong>Master Key :</strong> Clé principale d'authentification</li>
                    <li><strong>Private Key :</strong> Clé privée pour les transactions</li>
                    <li><strong>Public Key :</strong> Clé publique pour l'identification</li>
                    <li><strong>Token :</strong> Token d'authentification</li>
                </ul>
                
                <h6 class="mt-4 mb-3" style="font-weight: 300; font-family: 'Ubuntu', sans-serif; color: var(--primary-dark-blue);">
                    <i class="bi bi-toggle-on"></i> Modes disponibles
                </h6>
                <p style="font-size: 0.75rem; line-height: 1.5; font-weight: 300; font-family: 'Ubuntu', sans-serif; color: #666;">
                    <strong>Mode Test :</strong> Pour tester l'intégration avec des comptes fictifs<br>
                    <strong>Mode Live :</strong> Pour les paiements réels en production
                </p>
                
                <h6 class="mt-4 mb-3" style="font-weight: 300; font-family: 'Ubuntu', sans-serif; color: var(--primary-dark-blue);">
                    <i class="bi bi-bell"></i> IPN (Notification)
                </h6>
                <p style="font-size: 0.75rem; line-height: 1.5; font-weight: 300; font-family: 'Ubuntu', sans-serif; color: #666;">
                    L'URL IPN permet à PayDunya de notifier votre application en temps réel du statut des paiements. Cette fonctionnalité est essentielle pour confirmer automatiquement les transactions.
                </p>
                
                <h6 class="mt-4 mb-3" style="font-weight: 300; font-family: 'Ubuntu', sans-serif; color: var(--primary-dark-blue);">
                    <i class="bi bi-lightbulb"></i> Comment obtenir vos clés
                </h6>
                <ol style="font-size: 0.75rem; line-height: 1.8; font-weight: 300; font-family: 'Ubuntu', sans-serif; color: #666; padding-left: 1.2rem;">
                    <li>Connectez-vous à votre compte PayDunya Business</li>
                    <li>Accédez à <strong>"Intégrez notre API"</strong></li>
                    <li>Créez une nouvelle application</li>
                    <li>Copiez vos clés API</li>
                </ol>
                
                <h6 class="mt-4 mb-3" style="font-weight: 300; font-family: 'Ubuntu', sans-serif; color: var(--primary-dark-blue);">
                    <i class="bi bi-arrow-repeat"></i> Comment un membre paie via PayDunya ?
                </h6>
                <p style="font-size: 0.75rem; line-height: 1.5; font-weight: 300; font-family: 'Ubuntu', sans-serif; color: #666; margin-bottom: 0.5rem;">
                    <strong>Processus de paiement :</strong>
                </p>
                <ol style="font-size: 0.75rem; line-height: 1.8; font-weight: 300; font-family: 'Ubuntu', sans-serif; color: #666; padding-left: 1.2rem; margin-bottom: 0.5rem;">
                    <li>Le membre se connecte à son espace</li>
                    <li>Il consulte ses cotisations/engagements en attente</li>
                    <li>Il sélectionne une cotisation et clique sur <strong>"Payer via PayDunya"</strong></li>
                    <li>L'application crée une facture PayDunya et redirige vers la page de paiement</li>
                    <li>Le membre choisit son moyen de paiement (Mobile Money ou carte bancaire)</li>
                    <li>Il valide le paiement sur son téléphone</li>
                    <li>PayDunya envoie une notification IPN à l'application</li>
                    <li>Le paiement est automatiquement enregistré et la caisse est mise à jour</li>
                </ol>
                <p style="font-size: 0.75rem; line-height: 1.5; font-weight: 300; font-family: 'Ubuntu', sans-serif; color: #666; margin-top: 0.5rem; margin-bottom: 0;">
                    <strong>Note :</strong> Cette fonctionnalité nécessite l'intégration de l'API PayDunya dans l'application. Une fois configurée, les membres pourront payer directement depuis leur espace personnel.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
