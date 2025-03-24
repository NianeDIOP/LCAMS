@extends('layouts.app')

@section('title', 'Gestion des niveaux')

@section('styles')
<style>
    /* Styles spécifiques à la page des niveaux */
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
</style>
@endsection

@section('content')
<!-- En-tête de page -->
<div class="page-header">
    <div class="container">
        <h1 class="page-title">Paramètres</h1>
        <p class="page-subtitle text-muted">Gestion des niveaux scolaires</p>
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
                <a class="nav-link active" href="{{ route('parametres.niveaux') }}">
                    <i class="fas fa-layer-group me-2"></i>Niveaux
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link" href="{{ route('parametres.classes') }}">
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
        <!-- Liste des niveaux -->
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Liste des niveaux</h5>
                    <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#ajoutNiveauModal">
                        <i class="fas fa-plus me-1"></i> Ajouter un niveau
                    </button>
                </div>
                <div class="card-body p-0">
                    @if(isset($niveaux) && $niveaux->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Code</th>
                                        <th>Nom</th>
                                        <th>Cycle</th>
                                        <th>Ordre</th>
                                        <th class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($niveaux as $niveau)
                                        <tr>
                                            <td><span class="badge bg-light text-dark">{{ $niveau->code }}</span></td>
                                            <td>{{ $niveau->nom }}</td>
                                            <td>{{ $niveau->cycle }}</td>
                                            <td>{{ $niveau->ordre }}</td>
                                            <td class="text-end">
                                                <button type="button" class="btn btn-action btn-outline-primary me-1 edit-niveau-btn" 
                                                        data-id="{{ $niveau->id }}" 
                                                        data-code="{{ $niveau->code }}" 
                                                        data-nom="{{ $niveau->nom }}" 
                                                        data-cycle="{{ $niveau->cycle }}" 
                                                        data-ordre="{{ $niveau->ordre }}">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button type="button" class="btn btn-action btn-outline-danger delete-niveau-btn" 
                                                        data-id="{{ $niveau->id }}" 
                                                        data-nom="{{ $niveau->nom }}">
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
                                <i class="fas fa-info-circle me-1"></i> Aucun niveau n'a été configuré.
                            </div>
                            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#ajoutNiveauModal">
                                <i class="fas fa-plus me-1"></i> Ajouter un niveau
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- Guide d'utilisation -->
        <div class="col-lg-4">
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
                            <h6>À propos des niveaux</h6>
                            <p class="text-muted small mb-0">Les niveaux représentent les différentes années d'études (6ème, 5ème, etc.) et servent à organiser les classes.</p>
                        </div>
                    </div>
                    
                    <div class="d-flex align-items-start mb-3">
                        <div class="guide-icon bg-warning-subtle text-warning">
                            <i class="fas fa-lightbulb"></i>
                        </div>
                        <div>
                            <h6>Astuces</h6>
                            <ul class="text-muted small mb-0 ps-3">
                                <li>Utilisez le code comme identifiant court (ex: 6eme)</li>
                                <li>Le nom peut être plus descriptif (ex: Sixième)</li>
                                <li>Le cycle permet de regrouper (ex: Collège, Lycée)</li>
                                <li>L'ordre détermine le classement dans les rapports</li>
                            </ul>
                        </div>
                    </div>
                    
                    <div class="d-flex align-items-start">
                        <div class="guide-icon bg-danger-subtle text-danger">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        <div>
                            <h6>Important</h6>
                            <p class="text-muted small mb-0">Un niveau ne peut être supprimé s'il est associé à des classes. Vous devrez d'abord supprimer ou réassigner les classes associées.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Ajout/Modification de niveau -->
<div class="modal fade" id="ajoutNiveauModal" tabindex="-1" aria-labelledby="ajoutNiveauModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ajoutNiveauModalLabel">Ajouter un niveau</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="niveauForm" action="{{ route('parametres.saveNiveau') }}" method="POST">
                @csrf
                <input type="hidden" id="niveau_id" name="id">
                
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="code" class="form-label">Code du niveau *</label>
                        <input type="text" class="form-control" id="code" name="code" required placeholder="Ex: 6eme">
                        <div class="form-text small">Code court pour identifier le niveau (ex: 6eme, 5eme, 2nde)</div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="nom" class="form-label">Nom du niveau *</label>
                        <input type="text" class="form-control" id="nom" name="nom" required placeholder="Ex: Sixième">
                        <div class="form-text small">Nom complet du niveau (ex: Sixième, Cinquième, Seconde)</div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="cycle" class="form-label">Cycle *</label>
                        <select class="form-select" id="cycle" name="cycle" required>
                            <option value="Collège">Collège</option>
                            <option value="Lycée">Lycée</option>
                        </select>
                        <div class="form-text small">Le cycle scolaire auquel appartient ce niveau</div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="ordre" class="form-label">Ordre d'affichage *</label>
                        <input type="number" class="form-control" id="ordre" name="ordre" required min="1" value="1">
                        <div class="form-text small">Détermine l'ordre d'affichage des niveaux (1, 2, 3, etc.)</div>
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
<div class="modal fade" id="deleteNiveauModal" tabindex="-1" aria-hidden="true">
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
                <p>Êtes-vous sûr de vouloir supprimer le niveau <strong id="delete-niveau-name"></strong> ?</p>
                <p class="text-danger small">Cette action ne peut pas être annulée.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <form id="deleteNiveauForm" action="" method="POST">
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
        // Gestion du modal de modification
        const editButtons = document.querySelectorAll('.edit-niveau-btn');
        const ajoutNiveauModal = document.getElementById('ajoutNiveauModal');
        const modalTitle = ajoutNiveauModal.querySelector('.modal-title');
        const niveauForm = document.getElementById('niveauForm');
        const niveauIdInput = document.getElementById('niveau_id');
        const codeInput = document.getElementById('code');
        const nomInput = document.getElementById('nom');
        const cycleInput = document.getElementById('cycle');
        const ordreInput = document.getElementById('ordre');
        
        // Réinitialiser le formulaire lors de l'ouverture pour l'ajout
        const addButton = document.querySelector('[data-bs-target="#ajoutNiveauModal"]');
        addButton.addEventListener('click', function() {
            modalTitle.textContent = 'Ajouter un niveau';
            niveauForm.reset();
            niveauIdInput.value = '';
        });
        
        // Remplir le formulaire lors de l'édition
        editButtons.forEach(button => {
            button.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                const code = this.getAttribute('data-code');
                const nom = this.getAttribute('data-nom');
                const cycle = this.getAttribute('data-cycle');
                const ordre = this.getAttribute('data-ordre');
                
                modalTitle.textContent = 'Modifier un niveau';
                niveauIdInput.value = id;
                codeInput.value = code;
                nomInput.value = nom;
                cycleInput.value = cycle;
                ordreInput.value = ordre;
                
                // Ouvrir le modal
                const modal = new bootstrap.Modal(ajoutNiveauModal);
                modal.show();
            });
        });
        
        // Gestion du modal de suppression
        const deleteButtons = document.querySelectorAll('.delete-niveau-btn');
        const deleteModal = document.getElementById('deleteNiveauModal');
        const deleteForm = document.getElementById('deleteNiveauForm');
        const deleteNiveauName = document.getElementById('delete-niveau-name');
        
        deleteButtons.forEach(button => {
            button.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                const nom = this.getAttribute('data-nom');
                
                deleteNiveauName.textContent = nom;
                deleteForm.action = `/parametres/niveaux/delete/${id}`;
                
                // Ouvrir le modal
                const modal = new bootstrap.Modal(deleteModal);
                modal.show();
            });
        });
    });
</script>
@endsection