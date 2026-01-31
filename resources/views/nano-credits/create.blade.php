@extends('layouts.app')

@section('title', 'Nouveau nano crédit')

@section('content')
<div class="page-header">
    <h1><i class="bi bi-phone"></i> Nouveau nano crédit</h1>
</div>

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-send"></i> Envoyer un nano crédit (déboursement mobile money)
            </div>
            <div class="card-body">
                <p class="text-muted small mb-3">
                    Le montant sera envoyé au numéro du bénéficiaire via le canal choisi (Orange Money, Wave, etc.).
                    Le numéro doit être saisi <strong>sans indicatif pays</strong> (ex. 771234567 pour le Sénégal).
                </p>
                <form action="{{ route('nano-credits.store') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label for="membre_id" class="form-label">Membre bénéficiaire <span class="text-danger">*</span></label>
                        <select class="form-select @error('membre_id') is-invalid @enderror" id="membre_id" name="membre_id" required>
                            <option value="">Choisir un membre...</option>
                            @foreach($membres as $m)
                                <option value="{{ $m->id }}" {{ old('membre_id') == $m->id ? 'selected' : '' }}
                                        data-telephone="{{ $m->telephone ? preg_replace('/\D/', '', $m->telephone) : '' }}">
                                    {{ $m->nom_complet }} — {{ $m->telephone ?? 'Pas de tél.' }}
                                </option>
                            @endforeach
                        </select>
                        @error('membre_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="telephone" class="form-label">Numéro du bénéficiaire (sans indicatif pays) <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('telephone') is-invalid @enderror"
                               id="telephone" name="telephone" value="{{ old('telephone') }}"
                               placeholder="Ex: 771234567" required>
                        <small class="form-text text-muted">Saisissez uniquement les chiffres (ex. 771234567). L'indicatif pays sera retiré automatiquement si présent.</small>
                        @error('telephone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="withdraw_mode" class="form-label">Canal de retrait <span class="text-danger">*</span></label>
                        <select class="form-select @error('withdraw_mode') is-invalid @enderror" id="withdraw_mode" name="withdraw_mode" required>
                            <option value="">Choisir...</option>
                            @foreach($withdrawModes as $value => $label)
                                <option value="{{ $value }}" {{ old('withdraw_mode') === $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('withdraw_mode')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="montant" class="form-label">Montant (XOF) <span class="text-danger">*</span></label>
                        <input type="number" step="1" min="100" class="form-control @error('montant') is-invalid @enderror"
                               id="montant" name="montant" value="{{ old('montant') }}" required>
                        @error('montant')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-send"></i> Envoyer le nano crédit
                        </button>
                        <a href="{{ route('nano-credits.index') }}" class="btn btn-outline-secondary">Annuler</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-light">
            <div class="card-header">
                <i class="bi bi-info-circle"></i> Information
            </div>
            <div class="card-body small">
                <p>Le nano crédit utilise l’<strong>API PUSH (déboursement)</strong> PayDunya. Assurez-vous que :</p>
                <ul class="mb-0">
                    <li>L’API PER / Déboursement est activée dans votre dashboard PayDunya (WebPay / MobPay).</li>
                    <li>Votre compte marchand est suffisamment approvisionné.</li>
                    <li>Le numéro est valide pour le canal choisi (Orange Money, Wave, etc.).</li>
                </ul>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    var membreSelect = document.getElementById('membre_id');
    var telephoneInput = document.getElementById('telephone');
    if (membreSelect && telephoneInput) {
        membreSelect.addEventListener('change', function() {
            var opt = this.options[this.selectedIndex];
            var tel = opt.getAttribute('data-telephone');
            if (tel) {
                telephoneInput.value = tel;
            }
        });
    }
});
</script>
@endpush
@endsection
