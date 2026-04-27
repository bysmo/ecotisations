@extends('layouts.app')

@section('title', 'Numérotations Automatiques')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <h1><i class="bi bi-hash"></i> Numérotations Automatiques</h1>
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addConfigModal">
        <i class="bi bi-plus-circle me-2"></i> Nouvelle Configuration
    </button>
</div>

<div class="row mt-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4">Type d'Objet</th>
                                <th>Description</th>
                                <th>Format (Aperçu)</th>
                                <th>Compteur Actuel</th>
                                <th>Statut</th>
                                <th class="text-end pe-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($configs as $config)
                                <tr>
                                    <td class="ps-4">
                                        <span class="fw-bold text-primary">{{ $objectTypes[$config->object_type] ?? strtoupper($config->object_type) }}</span>
                                        <br><small class="text-muted">{{ $config->object_type }}</small>
                                    </td>
                                    <td>{{ $config->description ?? '-' }}</td>
                                    <td>
                                        <code class="bg-light px-2 py-1 rounded" id="preview-{{ $config->id }}">
                                            Chargement...
                                        </code>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary">{{ $config->current_value }}</span>
                                    </td>
                                    <td>
                                        @if($config->is_active)
                                            <span class="badge bg-success">Actif</span>
                                        @else
                                            <span class="badge bg-danger">Inactif</span>
                                        @endif
                                    </td>
                                    <td class="text-end pe-4">
                                        <button class="btn btn-sm btn-outline-primary me-1" 
                                                onclick="editConfig({{ json_encode($config) }})"
                                                data-bs-toggle="modal" 
                                                data-bs-target="#editConfigModal">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <form action="{{ route('admin.auto-numbering.destroy', $config) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette configuration ?')">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5">
                                        <div class="text-muted">
                                            <i class="bi bi-info-circle fs-2 d-block mb-2"></i>
                                            Aucune configuration de numérotation définie.
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Ajout -->
<div class="modal fade" id="addConfigModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow">
            <form action="{{ route('admin.auto-numbering.store') }}" method="POST">
                @csrf
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">Nouvelle Numérotation</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Type d'Objet</label>
                            <select name="object_type" class="form-select" required>
                                <option value="">Sélectionner...</option>
                                @foreach($objectTypes as $key => $label)
                                    <option value="{{ $key }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Description</label>
                            <input type="text" name="description" class="form-control" placeholder="ex: Numérotation des clients">
                        </div>
                    </div>

                    <hr>

                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="mb-0 fw-bold"><i class="bi bi-puzzle me-2"></i> Blocs de Construction</h6>
                        <div class="btn-group btn-group-sm">
                            <button type="button" class="btn btn-outline-secondary" onclick="addBlock('constant')">+ Constante</button>
                            <button type="button" class="btn btn-outline-secondary" onclick="addBlock('date')">+ Date</button>
                            <button type="button" class="btn btn-outline-secondary" onclick="addBlock('separator')">+ Séparateur</button>
                            <button type="button" class="btn btn-outline-secondary" onclick="addBlock('sequence')">+ Séquence</button>
                        </div>
                    </div>

                    <div id="blocks-container" class="bg-light p-3 rounded mb-3" style="min-height: 100px;">
                        <!-- Les blocs seront ajoutés ici -->
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Valeur Initiale (Compteur)</label>
                            <input type="number" name="current_value" class="form-control" value="0">
                        </div>
                        <div class="col-md-6 d-flex align-items-end">
                            <div class="form-check form-switch mb-2">
                                <input class="form-check-input" type="checkbox" name="is_active" value="1" checked id="add_is_active">
                                <label class="form-check-label" for="add_is_active">Activé</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edition -->
<div class="modal fade" id="editConfigModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow">
            <form id="editForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header bg-dark text-white">
                    <h5 class="modal-title">Modifier la Numérotation</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Type d'Objet</label>
                            <input type="text" id="edit_object_type_label" class="form-control-plaintext fw-bold text-primary" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Description</label>
                            <input type="text" name="description" id="edit_description" class="form-control">
                        </div>
                    </div>

                    <hr>

                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="mb-0 fw-bold"><i class="bi bi-puzzle me-2"></i> Blocs de Construction</h6>
                        <div class="btn-group btn-group-sm">
                            <button type="button" class="btn btn-outline-secondary" onclick="addBlock('constant', 'edit')">+ Constante</button>
                            <button type="button" class="btn btn-outline-secondary" onclick="addBlock('date', 'edit')">+ Date</button>
                            <button type="button" class="btn btn-outline-secondary" onclick="addBlock('separator', 'edit')">+ Séparateur</button>
                            <button type="button" class="btn btn-outline-secondary" onclick="addBlock('sequence', 'edit')">+ Séquence</button>
                        </div>
                    </div>

                    <div id="edit-blocks-container" class="bg-light p-3 rounded mb-3" style="min-height: 100px;">
                        <!-- Les blocs seront ajoutés ici -->
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Valeur Actuelle (Compteur)</label>
                            <input type="number" name="current_value" id="edit_current_value" class="form-control">
                        </div>
                        <div class="col-md-6 d-flex align-items-end">
                            <div class="form-check form-switch mb-2">
                                <input class="form-check-input" type="checkbox" name="is_active" value="1" id="edit_is_active">
                                <label class="form-check-label" for="edit_is_active">Activé</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Mettre à jour</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .block-item {
        background: white;
        border: 1px solid #dee2e6;
        border-radius: 8px;
        padding: 10px;
        margin-bottom: 10px;
        display: flex;
        align-items: center;
        gap: 15px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        transition: all 0.2s;
    }
    .block-item:hover {
        border-color: var(--primary-color);
    }
    .block-type-badge {
        font-size: 0.7rem;
        text-transform: uppercase;
        padding: 4px 8px;
        border-radius: 4px;
        width: 100px;
        text-align: center;
        font-weight: bold;
    }
    .badge-constant { background: #e3f2fd; color: #1976d2; }
    .badge-date { background: #f3e5f5; color: #7b1fa2; }
    .badge-separator { background: #fff3e0; color: #ef6c00; }
    .badge-sequence { background: #e8f5e9; color: #2e7d32; }

    .block-controls {
        flex-grow: 1;
        display: flex;
        gap: 10px;
    }
    .remove-block {
        color: #dc3545;
        cursor: pointer;
        padding: 5px;
    }
</style>

@push('scripts')
<script>
    function addBlock(type, mode = 'add') {
        const containerId = mode === 'add' ? 'blocks-container' : 'edit-blocks-container';
        const container = document.getElementById(containerId);
        const index = container.children.length;
        
        const blockHtml = createBlockHtml(type, index, containerId, {});
        container.insertAdjacentHTML('beforeend', blockHtml);
    }

    function createBlockHtml(type, index, containerId, data = {}) {
        let controls = '';
        const namePrefix = `definition[${index}]`;

        switch(type) {
            case 'constant':
                controls = `<input type="hidden" name="${namePrefix}[type]" value="constant">
                            <input type="text" name="${namePrefix}[value]" class="form-control form-control-sm" placeholder="Valeur fixe" value="${data.value || ''}" required>`;
                break;
            case 'date':
                controls = `<input type="hidden" name="${namePrefix}[type]" value="date">
                            <select name="${namePrefix}[value]" class="form-select form-select-sm" required>
                                <option value="Y" ${data.value === 'Y' ? 'selected' : ''}>Année (2026)</option>
                                <option value="y" ${data.value === 'y' ? 'selected' : ''}>Année courte (26)</option>
                                <option value="m" ${data.value === 'm' ? 'selected' : ''}>Mois (01-12)</option>
                                <option value="d" ${data.value === 'd' ? 'selected' : ''}>Jour (01-31)</option>
                                <option value="Ymd" ${data.value === 'Ymd' ? 'selected' : ''}>AAAAMMJJ</option>
                                <option value="Ym" ${data.value === 'Ym' ? 'selected' : ''}>AAAAMM</option>
                            </select>`;
                break;
            case 'separator':
                controls = `<input type="hidden" name="${namePrefix}[type]" value="separator">
                            <select name="${namePrefix}[value]" class="form-select form-select-sm" required>
                                <option value="-" ${data.value === '-' ? 'selected' : ''}>- (Tiret)</option>
                                <option value="_" ${data.value === '_' ? 'selected' : ''}>_ (Underscore)</option>
                                <option value="/" ${data.value === '/' ? 'selected' : ''}>/ (Slash)</option>
                                <option value="." ${data.value === '.' ? 'selected' : ''}>. (Point)</option>
                                <option value=" " ${data.value === ' ' ? 'selected' : ''}>(Espace)</option>
                                <option value="" ${data.value === '' ? 'selected' : ''}>(Aucun)</option>
                            </select>`;
                break;
            case 'sequence':
                controls = `<input type="hidden" name="${namePrefix}[type]" value="sequence">
                            <div class="input-group input-group-sm">
                                <span class="input-group-text">Longueur</span>
                                <input type="number" name="${namePrefix}[length]" class="form-control" value="${data.length || 5}" min="1" max="10" required>
                            </div>`;
                break;
        }

        return `
            <div class="block-item" data-type="${type}">
                <div class="block-type-badge badge-${type}">${type}</div>
                <div class="block-controls">${controls}</div>
                <div class="remove-block" onclick="this.parentElement.remove(); reindexBlocks('${containerId}')">
                    <i class="bi bi-x-circle-fill"></i>
                </div>
            </div>
        `;
    }

    function reindexBlocks(containerId) {
        const container = document.getElementById(containerId);
        Array.from(container.children).forEach((block, index) => {
            const inputs = block.querySelectorAll('input, select');
            inputs.forEach(input => {
                const name = input.getAttribute('name');
                if (name) {
                    input.setAttribute('name', name.replace(/definition\[\d+\]/, `definition[${index}]`));
                }
            });
        });
    }

    function editConfig(config) {
        const form = document.getElementById('editForm');
        form.action = `/admin/settings/auto-numbering/${config.id}`;
        
        document.getElementById('edit_object_type_label').value = config.object_type;
        document.getElementById('edit_description').value = config.description;
        document.getElementById('edit_current_value').value = config.current_value;
        document.getElementById('edit_is_active').checked = !!config.is_active;

        const container = document.getElementById('edit-blocks-container');
        container.innerHTML = '';
        
        config.definition.forEach((block, index) => {
            const blockHtml = createBlockHtml(block.type, index, 'edit-blocks-container', block);
            container.insertAdjacentHTML('beforeend', blockHtml);
        });
    }

    // Charger les aperçus au chargement de la page
    document.addEventListener('DOMContentLoaded', function() {
        @foreach($configs as $config)
            fetchPreview('{{ $config->object_type }}', 'preview-{{ $config->id }}');
        @endforeach
    });

    function fetchPreview(type, elementId) {
        fetch(`/admin/settings/auto-numbering/preview/${type}`)
            .then(response => response.json())
            .then(data => {
                document.getElementById(elementId).innerText = data.preview;
            })
            .catch(() => {
                document.getElementById(elementId).innerText = 'Erreur';
            });
    }
</script>
@endpush
@endsection
