@extends('layouts.app')

@section('title', 'Gestion des classes')

@section('styles')
<style>
    /* Styles spécifiques à la page des classes */
    .page-header {
        background: linear-gradient(120deg, #f5f7fa, #e4e9f2);
        padding: 1.5rem 0;
        margin-bottom: 1.75rem;
    }
    
    .tab-header {
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        margin-bottom: 1.5rem;
    }
    
    .nav-tabs {
        border-bottom: none;
    }
    
    .nav-tabs .nav-item {
        margin-bottom: 0;
    }
    
    .nav-tabs .nav-link {
        border: none;
        font-size: 0.875rem;
        font-weight: 500;
        padding: 0.75rem 1rem;
        color: var(--secondary);
        border-bottom: 2px solid transparent;
        border-radius: 0;
        transition: all 0.2s;
    }
    
    .nav-tabs .nav-link:hover:not(.active) {
        color: var(--dark);
        border-color: rgba(0, 0, 0, 0.1);
    }
    
    .nav-tabs .nav-link.active {
        color: var(--primary);
        border-bottom: 2px solid var(--primary);
        background-color: transparent;
    }
    
    .table {
        font-size: 0.875rem;
    }
    
    .table th {
        font-weight: 600;
        color: #495057;
    }
    
    .table td {
        vertical-align: middle;
    }
    
    .btn-action {
        width: 28px;
        height: 28px;
        padding: 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 4px;
        font-size: 0.8rem;
    }
    
    .badge {
        font-weight: 500;
        padding: 0.35em 0.65em;
        font-size: 0.75em;
    }
    
    .guide-card {
        font-size: 0.875rem;
    }
    
    .guide-card h6 {
        font-size: 0.95rem;
        font-weight: 600;
    }
    
    .guide-icon {
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 6px;
        margin-right: 0.75rem;
    }
    
    .form-control, .form-select {
        font-size: 0.875rem;
        border-radius: 0.375rem;
        padding: 0.5rem 0.75rem;
        border-color: #dce1e6;
    }
    
    .year-badge {
        background-color: #f0f6ff;
        color: #0062cc;
        font-size: 1rem;
        font-weight: 600;
        padding: 0.5rem 1rem;
        border-radius: 6px;
    }
</style>
@endsection

@section('content')
<!-- En-tête de page -->
<div class="page-header">
    <div class="container">
        <h1 class="page-title">Paramètres</h1>
        <p class="page-subtitle text-muted">Gestion des classes</p>
    </div>
</div>

<div class="container">
    <!-- Messages de notification -->
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <!-- Onglets de navigation -->
    <div class="tab-header">
        <ul class="nav nav-tabs" id="parametresTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <a class="nav-link" href="{{ route('parametres.index') }}">
                    <i class="fas fa-school me-2"></i>Établissement
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link" href="{{ route('parametres.niveaux') }}">
                    <i class="fas fa-layer-group me-2"></i>Niveaux
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link active" href="{{ route('parametres.classes') }}">
                    <i class="fas fa-chalkboard me-2"></i>Classes
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link" href="{{ route('parametres.annee') }}">
                    <i class="fas fa-calendar-alt me-2"></i>Année scolaire
                </a>
            </li>
        </ul>
    </div>

    <!-- Contenu -->
    <div class="row">
        <!-- Liste des classes -->
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Liste des classes</h5>
                    <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#ajoutClasseModal">
                        <i class="fas fa-plus me-1"></i> Ajouter une classe
                    </button>
                </div>
                <div class="card-body p-0">
                    @if(isset($classes) && $classes->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Nom</th>
                                        <th>Niveau</th>
                                        <th>Effectif</th>
                                        <th>G / F</th>
                                        <th>Statut</th>
                                        <th class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($classes as $classe)
                                        <tr>
                                            <td>{{ $classe->nom }}</td>
                                            <td>{{ $classe->niveau->nom ?? 'N/A' }}</td>
                                            <td>{{ $classe->effectif_total }}</td>
                                            <td>{{ $classe->effectif_garcons }} / {{ $classe->effectif_filles }}</td>
                                            <td>
                                                @if($classe->active)
                                                    <span class="badge bg-success">Active</span>
                                                @else
                                                    <span class="badge bg-secondary">Inactive</span>
                                                @endif
                                            </td>
                                            <td class="text-end">
                                                <button type="button" class="btn btn-action btn-outline-primary me-1 edit-classe-btn" 
                                                        data-id="{{ $classe->id }}" 
                                                        data-nom="{{ $classe->nom }}" 
                                                        data-niveau="{{ $classe->niveau_id }}" 
                                                        data-effectif="{{ $classe->effectif_total }}" 
                                                        data-garcons="{{ $classe->effectif_garcons }}" 
                                                        data-filles="{{ $classe->effectif_filles }}" 
                                                        data-active="{{ $classe->active }}">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button type="button" class="btn btn-action btn-outline-danger delete-classe-btn" 
                                                        data-id="{{ $classe->id }}" 
                                                        data-nom="{{ $classe->nom }}">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center p-4">
                            <div class="text-muted mb-3">
                                <i class="fas fa-info-circle me-1"></i> Aucune classe n'a été configurée.
                            </div>
                            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#ajoutClasseModal">
                                <i class="fas fa-plus me-1"></i> Ajouter une classe
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- Informations et guide -->
        <div class="col-lg-4">
            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Année scolaire en cours</h5>
                </div>
                <div class="card-body text-center">
                    <div class="year-badge mb-3">
                        {{ $anneeScolaire ?? '2024-2025' }}
                    </div>
                    <p class="text-muted small mb-0">Toutes les classes créées seront associées à l'année scolaire en cours. Vous pouvez modifier l'année scolaire dans l'onglet "Année scolaire".</p>
                </div>
            </div>
            
            <div class="card shadow-sm guide-card">
                <div class="card-header">
                    <h5 class="mb-0">Guide d'utilisation</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-start mb-3">
                        <div class="guide-icon bg-primary-subtle text-primary">
                            <i class="fas fa-info"></i>
                        </div>
                        <div>
                            <h6>À propos des classes</h6>
                            <p class="text-muted small mb-0">Les classes sont regroupées par niveau et permettent d'organiser les élèves et leurs résultats.</p>
                        </div>
                    </div>
                    
                    <div class="d-flex align-items-start mb-3">
                        <div class="guide-icon bg-warning-subtle text-warning">
                            <i class="fas fa-lightbulb"></i>
                        </div>
                        <div>
                            <h6>Astuces</h6>
                            <ul class="text-muted small mb-0 ps-3">
                                <li>Créez d'abord les niveaux avant d'ajouter des classes</li>
                                <li>L'effectif total doit être égal à la somme des garçons et des filles</li>
                                <li>Désactivez les classes plutôt que de les supprimer si elles ne sont plus utilisées</li>
                            </ul>
                        </div>
                    </div>
                    
                    <div class="d-flex align-items-start">
                        <div class="guide-icon bg-danger-subtle text-danger">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        <div>
                            <h6>Important</h6>
                            <p class="text-muted small mb-0">La suppression d'une classe entraînera la perte de toutes les données associées (moyennes, évaluations, etc.)</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Ajout/Modification de classe -->
<div class="modal fade" id="ajoutClasseModal" tabindex="-1" aria-labelledby="ajoutClasseModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ajoutClasseModalLabel">Ajouter une classe</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="classeForm" action="{{ route('parametres.saveClasse') }}" method="POST">
                @csrf
                <input type="hidden" id="classe_id" name="id">
                
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="niveau_id" class="form-label">Niveau *</label>
                        <select class="form-select" id="niveau_id" name="niveau_id" required>
                            <option value="">Sélectionnez un niveau</option>
                            @foreach($niveaux as $niveau)
                                <option value="{{ $niveau->id }}">{{ $niveau->nom }} ({{ $niveau->cycle }})</option>
                            @endforeach
                        </select>
                        <div class="form-text small">Sélectionnez le niveau auquel appartient cette classe</div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="nom" class="form-label">Nom de la classe *</label>
                        <input type="text" class="form-control" id="nom" name="nom" required placeholder="Ex: 6ème A">
                        <div class="form-text small">Nom complet de la classe (ex: 6ème A, Terminale S1)</div>
                    </div>
                    
                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label for="effectif_total" class="form-label">Effectif total</label>
                            <input type="number" class="form-control" id="effectif_total" name="effectif_total" min="0" value="0" readonly>
                        </div>
                        
                        <div class="col-md-4">
                            <label for="effectif_garcons" class="form-label">Garçons</label>
                            <input type="number" class="form-control" id="effectif_garcons" name="effectif_garcons" min="0" value="0">
                        </div>
                        
                        <div class="col-md-4">
                            <label for="effectif_filles" class="form-label">Filles</label>
                            <input type="number" class="form-control" id="effectif_filles" name="effectif_filles" min="0" value="0">
                        </div>
                    </div>
                    
                    <div class="form-check form-switch mb-0">
                        <input class="form-check-input" type="checkbox" id="active" name="active" value="1" checked>
                        <label class="form-check-label" for="active">Classe active</label>
                        <div class="form-text small">Les classes inactives ne seront pas prises en compte dans les analyses</div>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal de confirmation de suppression -->
<div class="modal fade" id="deleteClasseModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmer la suppression</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <div class="mb-3 text-danger">
                    <i class="fas fa-exclamation-triangle fa-3x"></i>
                </div>
                <p>Êtes-vous sûr de vouloir supprimer la classe <strong id="delete-classe-name"></strong> ?</p>
                <p class="text-danger small">Cette action ne peut pas être annulée.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <form id="deleteClasseForm" action="" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-danger">Supprimer</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Calcul automatique de l'effectif total
        const garconsInput = document.getElementById('effectif_garcons');
        const fillesInput = document.getElementById('effectif_filles');
        const effectifInput = document.getElementById('effectif_total');
        
        function updateEffectifTotal() {
            const garcons = parseInt(garconsInput.value) || 0;
            const filles = parseInt(fillesInput.value) || 0;
            effectifInput.value = garcons + filles;
        }
        
        garconsInput.addEventListener('input', updateEffectifTotal);
        fillesInput.addEventListener('input', updateEffectifTotal);
        
        // Gestion du modal de modification
        const editButtons = document.querySelectorAll('.edit-classe-btn');
        const ajoutClasseModal = document.getElementById('ajoutClasseModal');
        const modalTitle = ajoutClasseModal.querySelector('.modal-title');
        const classeForm = document.getElementById('classeForm');
        const classeIdInput = document.getElementById('classe_id');
        const niveauInput = document.getElementById('niveau_id');
        const nomInput = document.getElementById('nom');
        const activeInput = document.getElementById('active');
        
        // Réinitialiser le formulaire lors de l'ouverture pour l'ajout
        const addButton = document.querySelector('[data-bs-target="#ajoutClasseModal"]');
        addButton.addEventListener('click', function() {
            modalTitle.textContent = 'Ajouter une classe';
            classeForm.reset();
            classeIdInput.value = '';
            activeInput.checked = true;
        });
        
        // Remplir le formulaire lors de l'édition
        editButtons.forEach(button => {
            button.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                const nom = this.getAttribute('data-nom');
                const niveau = this.getAttribute('data-niveau');
                const effectif = this.getAttribute('data-effectif');
                const garcons = this.getAttribute('data-garcons');
                const filles = this.getAttribute('data-filles');
                const active = this.getAttribute('data-active') === '1';
                
                modalTitle.textContent = 'Modifier une classe';
                classeIdInput.value = id;
                niveauInput.value = niveau;
                nomInput.value = nom;
                effectifInput.value = effectif;
                garconsInput.value = garcons;
                fillesInput.value = filles;
                activeInput.checked = active;
                
                // Ouvrir le modal
                const modal = new bootstrap.Modal(ajoutClasseModal);
                modal.show();
            });
        });
        
        // Gestion du modal de suppression
        const deleteButtons = document.querySelectorAll('.delete-classe-btn');
        const deleteModal = document.getElementById('deleteClasseModal');
        const deleteForm = document.getElementById('deleteClasseForm');
        const deleteClasseName = document.getElementById('delete-classe-name');
        
        deleteButtons.forEach(button => {
            button.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                const nom = this.getAttribute('data-nom');
                
                deleteClasseName.textContent = nom;
                deleteForm.action = `/parametres/classes/delete/${id}`;
                
                // Ouvrir le modal
                const modal = new bootstrap.Modal(deleteModal);
                modal.show();
            });
        });
    });
</script>
@endsection