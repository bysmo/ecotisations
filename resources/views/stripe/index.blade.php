@extends('layouts.app')

@section('title', 'Configuration Stripe')

@section('content')
<div class="page-header">
    <h1><i class="bi bi-credit-card-2-front"></i> Configuration Stripe</h1>
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
                <form action="{{ route('stripe.update') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row g-2 mb-2">
                        <div class="col-md-6">
                            <div class="form-check form-switch mb-2">
                                <input class="form-check-input" type="checkbox" id="enabled" name="enabled" value="1"
                                       {{ old('enabled', $paymentMethod->enabled ?? false) ? 'checked' : '' }}>
                                <label class="form-check-label" for="enabled" style="font-weight: 300; font-family: 'Ubuntu', sans-serif; font-size: 0.75rem;">
                                    Activer Stripe
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="row g-2 mb-2">
                        <div class="col-md-6">
                            <label for="mode" class="form-label">Mode <span class="text-danger">*</span></label>
                            <select class="form-select" id="mode" name="mode" required>
                                <option value="test" {{ old('mode', $config['mode'] ?? 'test') === 'test' ? 'selected' : '' }}>Test</option>
                                <option value="live" {{ old('mode', $config['mode'] ?? 'test') === 'live' ? 'selected' : '' }}>Production (Live)</option>
                            </select>
                            <small class="text-muted" style="font-size: 0.7rem;">Utilisez le mode Test pour les tests, et Live pour la production</small>
                        </div>
                    </div>

                    <div class="row g-2 mb-2">
                        <div class="col-md-12">
                            <label for="publishable_key" class="form-label">Publishable Key</label>
                            <input type="text" class="form-control" id="publishable_key" name="publishable_key" 
                                   value="{{ old('publishable_key', $config['publishable_key'] ?? '') }}" 
                                   placeholder="Votre Publishable Key Stripe">
                            <small class="text-muted" style="font-size: 0.7rem;">Publishable Key fournie par Stripe</small>
                        </div>
                    </div>

                    <div class="row g-2 mb-2">
                        <div class="col-md-12">
                            <label for="secret_key" class="form-label">Secret Key</label>
                            <input type="text" class="form-control" id="secret_key" name="secret_key" 
                                   value="{{ old('secret_key', $config['secret_key'] ?? '') }}" 
                                   placeholder="Votre Secret Key Stripe">
                            <small class="text-muted" style="font-size: 0.7rem;">Secret Key fournie par Stripe</small>
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
                <i class="bi bi-info-circle"></i> À propos de Stripe
            </div>
            <div class="card-body">
                <p style="font-size: 0.75rem; line-height: 1.5; font-weight: 300; font-family: 'Ubuntu', sans-serif; color: #666;">
                    Stripe est une plateforme de paiement qui permet d'accepter les paiements par carte bancaire en ligne de manière sécurisée.
                </p>
                <p style="font-size: 0.75rem; line-height: 1.5; font-weight: 300; font-family: 'Ubuntu', sans-serif; color: #666; margin-top: 1rem;">
                    Pour obtenir vos clés API, connectez-vous à votre tableau de bord Stripe et accédez à la section "Developers" > "API keys".
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
