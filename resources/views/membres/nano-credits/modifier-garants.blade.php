@extends('layouts.membre')

@section('title', 'Modifier les garants')

@section('content')
<!-- Tom Select CSS -->
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">

<style>
    .page-nano-modifier, .page-nano-modifier .btn, .page-nano-modifier p {
        font-family: 'Ubuntu', sans-serif !important;
        font-weight: 300 !important;
    }
    .refuse-card {
        border-left: 4px solid #f56565;
        border-radius: 12px;
    }
    .valide-card {
        border-left: 4px solid #48bb78;
        border-radius: 12px;
    }
</style>

<div class="page-header page-nano-modifier">
    <h1 style="font-weight: 300; font-family: 'Ubuntu', sans-serif;">
        <i class="bi bi-person-x-fill text-danger"></i> Remplacement de Garants
    </h1>
    <p class="text-muted">Un ou plusieurs garants ont refusé votre sollicitation pour le crédit #{{ $nanoCredit->id }}.</p>
</div>

<div class="row page-nano-modifier g-4">
    <div class="col-lg-8">
        <form action="{{ route('membre.nano-credits.update-garants', $nanoCredit) }}" method="POST">
            @csrf

            <!-- Garants Validés ou En Attente -->
            <div class="mb-4">
                <h6 class="fw-bold text-uppercase small text-muted mb-3">Garants maintenus</h6>
                @foreach($garantsValides as $garant)
                    <div class="card border-0 shadow-sm mb-2 valide-card bg-white">
                        <div class="card-body py-2 px-3 d-flex justify-content-between align-items-center">
                            <span>{{ $garant->membre->nom_complet }}</span>
                            <span class="badge bg-success bg-opacity-10 text-success rounded-pill fw-normal">
                                {{ $garant->statut === 'accepte' ? 'A accepté' : 'En attente' }}
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Remplacement des Refusés -->
            <div class="card border-0 shadow-sm mb-4" style="border-radius: 16px;">
                <div class="card-header bg-white border-bottom py-3 fw-bold">
                    <i class="bi bi-arrow-repeat me-2 text-primary"></i> Remplacer les {{ $garantsRefuses->count() }} garants refusés
                </div>
                <div class="card-body p-4">
                    <div class="mb-4">
                        @foreach($garantsRefuses as $refuse)
                            <div class="alert alert-danger border-0 mb-3 bg-danger bg-opacity-10 rounded-4 p-3 small">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <strong class="text-danger">{{ $refuse->membre->nom_complet }}</strong>
                                    <span class="badge bg-danger rounded-pill">A refusé</span>
                                </div>
                                @if($refuse->motif_refus)
                                    <div class="mt-2 p-2 bg-white rounded-3 fst-italic">
                                        "{{ $refuse->motif_refus }}"
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Sélectionnez {{ $garantsRefuses->count() }} nouveau(x) garant(s) <span class="text-danger">*</span></label>
                        <select id="new-guarantor-select" name="new_garant_ids[]" placeholder="Entrez un nom ou numéro..." multiple required></select>
                        @error('new_garant_ids')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>

            <div class="d-flex gap-3 mb-5">
                <button type="submit" class="btn btn-primary btn-lg px-5 shadow-sm" style="border-radius: 12px;">
                    <i class="bi bi-send-check me-1"></i> Envoyer les nouvelles sollicitations
                </button>
                <a href="{{ route('membre.nano-credits.show', $nanoCredit) }}" class="btn btn-light btn-lg px-4 border" style="border-radius: 12px;">Annuler</a>
            </div>
        </form>
    </div>

    <div class="col-lg-4">
        <div class="card border-0 bg-white shadow-sm rounded-4">
            <div class="card-body p-4">
                <h6 class="fw-bold mb-3">Pourquoi modifier ?</h6>
                <p class="small text-muted">Le déblocage automatique de votre crédit nécessite l'approbation de TOUS les garants. En remplaçant les garants qui ont refusé, vous relancez le processus de validation finale.</p>
                <hr class="my-4 opacity-10">
                <div class="d-flex align-items-center text-primary">
                    <i class="bi bi-info-circle-fill me-2 fs-4"></i>
                    <span class="small fw-bold">Le déblocage se fera instantanément dès que ces nouveaux garants auront accepté.</span>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    new TomSelect("#new-guarantor-select", {
        valueField: 'id',
        labelField: 'text',
        searchField: 'text',
        maxItems: {{ (int) $garantsRefuses->count() }},
        load: function(query, callback) {
            var url = '{{ route("membre.nano-credits.search-guarantors") }}?q=' + encodeURIComponent(query);
            fetch(url)
                .then(response => response.json())
                .then(json => {
                    callback(json);
                }).catch(()=>{
                    callback();
                });
        },
        render: {
            option: function(item, escape) {
                return `<div class="py-2 px-3 border-bottom">
                    <div class="fw-bold">${escape(item.text)}</div>
                    <div class="small text-muted"><i class="bi bi-star-fill text-warning me-1"></i>Qualité Garant : ${escape(item.qualite)}</div>
                </div>`;
            },
            item: function(item, escape) {
                return `<div>${escape(item.text)}</div>`;
            }
        }
    });
});
</script>
@endpush
