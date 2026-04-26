@extends('layouts.membre')

@section('title', 'Mes Portefeuilles Pi-SPI')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-md-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0 text-primary fw-bold">
                        <i class="bi bi-wallet2 me-2"></i>Mes Alias Pi-SPI
                    </h5>
                    <button class="btn btn-primary btn-sm rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#addAliasModal">
                        <i class="bi bi-plus-lg me-1"></i> Ajouter
                    </button>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4">Libellé</th>
                                    <th>Alias (UUID)</th>
                                    <th>Statut</th>
                                    <th class="text-end pe-4">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($aliases as $alias)
                                    <tr>
                                        <td class="ps-4">
                                            <span class="fw-bold text-dark">{{ $alias->label }}</span>
                                        </td>
                                        <td>
                                            <code class="text-muted">{{ $alias->alias }}</code>
                                        </td>
                                        <td>
                                            @if($alias->is_default)
                                                <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3">Par défaut</span>
                                            @else
                                                <span class="badge bg-light text-muted rounded-pill px-3">Secondaire</span>
                                            @endif
                                        </td>
                                         <td class="text-end pe-4">
                                             <div class="btn-group">
                                                 <button type="button" class="btn btn-sm btn-outline-primary border-0" 
                                                         data-bs-toggle="modal" 
                                                         data-bs-target="#editAliasModal{{ $alias->id }}"
                                                         title="Modifier le libellé">
                                                     <i class="bi bi-pencil"></i>
                                                 </button>
                                                 
                                                 @if(!$alias->is_default)
                                                     <form action="{{ route('membre.wallets.default', $alias) }}" method="POST" class="d-inline">
                                                         @csrf
                                                         <button type="submit" class="btn btn-sm btn-outline-success border-0" title="Définir par défaut">
                                                             <i class="bi bi-check-circle"></i>
                                                         </button>
                                                     </form>
                                                 @endif
                                                 <form action="{{ route('membre.wallets.destroy', $alias) }}" method="POST" class="d-inline" onsubmit="return confirm('Supprimer cet alias ?')">
                                                     @csrf
                                                     @method('DELETE')
                                                     <button type="submit" class="btn btn-sm btn-outline-danger border-0" title="Supprimer">
                                                         <i class="bi bi-trash"></i>
                                                     </button>
                                                 </form>
                                             </div>

                                             <!-- Modal Modifier Alias -->
                                             <div class="modal fade" id="editAliasModal{{ $alias->id }}" tabindex="-1" aria-hidden="true">
                                                 <div class="modal-dialog modal-dialog-centered text-start">
                                                     <div class="modal-content border-0 shadow">
                                                         <div class="modal-header border-0 pb-0">
                                                             <h5 class="modal-title fw-bold">Modifier le Libellé</h5>
                                                             <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                         </div>
                                                         <form action="{{ route('membre.wallets.update', $alias) }}" method="POST">
                                                             @csrf
                                                             @method('PUT')
                                                             <div class="modal-body py-4">
                                                                 <div class="mb-0">
                                                                     <label class="form-label small fw-bold">Libellé</label>
                                                                     <input type="text" name="label" class="form-control rounded-pill px-3" value="{{ $alias->label }}" required>
                                                                 </div>
                                                             </div>
                                                             <div class="modal-footer border-0 pt-0">
                                                                 <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Annuler</button>
                                                                 <button type="submit" class="btn btn-primary rounded-pill px-4">Enregistrer</button>
                                                             </div>
                                                         </form>
                                                     </div>
                                                 </div>
                                             </div>
                                         </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="py-5 text-center text-muted">
                                            <i class="bi bi-wallet2 display-4 d-block mb-3 opacity-25"></i>
                                            Aucun alias enregistré. Ajoutez l'UUID fourni par votre banque pour payer via Pi-SPI.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card bg-primary text-white border-0 shadow-sm overflow-hidden mb-4">
                <div class="card-body p-4 position-relative">
                    <div class="position-absolute top-0 end-0 p-3 opacity-25">
                        <i class="bi bi-info-circle-fill fs-1"></i>
                    </div>
                    <h6 class="fw-bold mb-3 uppercase small">Comment ça marche ?</h6>
                    <p class="small mb-0">
                        L'alias est un identifiant unique (UUID) lié à votre compte bancaire ou mobile money compatible Pi-SPI. 
                        <br><br>
                        En l'enregistrant ici, vous pourrez valider vos opérations Serenity directement depuis votre application bancaire par une simple notification.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Ajouter Alias -->
<div class="modal fade" id="addAliasModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">Ajouter un Alias Pi-SPI</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('membre.wallets.store') }}" method="POST">
                @csrf
                <div class="modal-body py-4">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Nom du portefeuille (Optionnel)</label>
                        <input type="text" name="label" class="form-control rounded-pill px-3" placeholder="Ex: Ma banque BOA">
                    </div>
                    <div class="mb-0">
                        <label class="form-label small fw-bold">UUID fourni par votre banque <span class="text-danger">*</span></label>
                        <input type="text" name="alias" class="form-control font-monospace px-3" required placeholder="00000000-0000-0000-0000-000000000000">
                        <div class="form-text small">Respectez scrupuleusement le format UUID.</div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4">Confirmer</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
