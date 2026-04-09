@extends('layouts.membre')

@section('title', 'Vérification du Code PIN')

@section('content')
<div class="row justify-content-center mt-5">
    <div class="col-md-6 col-lg-5">
        <div class="card shadow-sm" style="border-radius: 12px; border: none;">
            <div class="card-header text-center" style="background-color: var(--primary-dark-blue); color: white; border-radius: 12px 12px 0 0;">
                <i class="bi bi-shield-lock-fill" style="font-size: 2rem;"></i>
                <h5 class="mt-2 mb-0" style="font-family: 'Ubuntu', sans-serif;">Action Protégée</h5>
            </div>
            
            <div class="card-body p-4 text-center">
                <p class="text-muted mb-4">
                    Pour des raisons de sécurité, cette opération nécessite la saisie de votre code PIN à 4 chiffres.
                </p>

                <form action="{{ route('membre.pin.verify-action.submit') }}" method="POST">
                    @csrf
                    
                    <div class="mb-4">
                        <input type="password" 
                               name="pin" 
                               class="form-control form-control-lg text-center @error('pin') is-invalid @enderror" 
                               placeholder="• • • •"
                               pattern="\d{4}"
                               maxlength="4"
                               autofocus
                               required
                               style="letter-spacing: 0.5em; font-size: 1.5rem; width: 150px; margin: 0 auto; border-radius: 10px;">
                        
                        @error('pin')
                            <div class="invalid-feedback d-block mt-2">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary btn-lg" style="border-radius: 8px;">
                            <i class="bi bi-check-circle"></i> Valider et Continuer
                        </button>
                        <a href="{{ route('membre.dashboard') }}" class="btn btn-light" style="border-radius: 8px;">
                            Annuler
                        </a>
                    </div>
                </form>
            </div>
        </div>
        
        @if($membre->isPinModeSession())
        <div class="text-center mt-3 text-muted" style="font-size: 0.85rem;">
            <i class="bi bi-info-circle"></i> La validation ouvrira une session sécurisée de 5 minutes.
        </div>
        @endif
    </div>
</div>
@endsection
