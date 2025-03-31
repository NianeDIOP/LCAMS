@extends('layouts.module')

@section('title', 'Paramètres')

@section('sidebar')
<div class="nav-title">Paramètres</div>

<li class="nav-item">
    <a class="nav-link {{ request()->routeIs('parametres.index') ? 'active' : '' }}" href="{{ route('parametres.index') }}">
        <span class="nav-icon"><i class="fas fa-school"></i></span>
        <span>Configuration</span>
    </a>
</li>

<div class="nav-title">Autres Modules</div>

<li class="nav-item">
    <a class="nav-link" href="{{ route('semestre1.index') }}">
        <span class="nav-icon"><i class="fas fa-calendar-alt"></i></span>
        <span>Semestre 1</span>
    </a>
</li>

<li class="nav-item">
    <a class="nav-link" href="{{ route('semestre2.index') }}">
        <span class="nav-icon"><i class="fas fa-calendar-alt"></i></span>
        <span>Semestre 2</span>
    </a>
</li>

<li class="nav-item">
    <a class="nav-link" href="{{ route('general.index') }}">
        <span class="nav-icon"><i class="fas fa-clipboard-list"></i></span>
        <span>Général</span>
    </a>
</li>
@endsection

@section('content')
<h1 class="page-title">
    <i class="fas fa-cog me-2"></i>Configuration du système
</h1>
<p class="page-subtitle">Configurez les informations de base pour l'application LCAMS.</p>

<div class="row">
    <div class="col-md-6">
        <!-- Informations de l'établissement -->
        <div class="card">
            <div class="card-header header-primary">
                <i class="fas fa-school me-2"></i>Informations de l'établissement
            </div>
            <div class="card-body">
                <form action="{{ route('parametres.configuration') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="mb-3">
                        <label for="nom_etablissement" class="form-label">Nom de l'établissement *</label>
                        <input type="text" class="form-control" id="nom_etablissement" name="nom_etablissement" 
                            value="{{ $configuration->nom_etablissement ?? '' }}" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="adresse" class="form-label">Adresse</label>
                        <input type="text" class="form-control" id="adresse" name="adresse" 
                            value="{{ $configuration->adresse ?? '' }}">
                    </div>
                    
                    <div class="mb-3">
                        <label for="telephone" class="form-label">Téléphone</label>
                        <input type="text" class="form-control" id="telephone" name="telephone" 
                            value="{{ $configuration->telephone ?? '' }}">
                    </div>
                    
                    <div class="mb-3">
                        <label for="inspection_academie" class="form-label">Inspection d'académie</label>
                        <input type="text" class="form-control" id="inspection_academie" name="inspection_academie" 
                            value="{{ $configuration->inspection_academie ?? '' }}">
                    </div>
                    
                    <div class="mb-3">
                        <label for="inspection_education_formation" class="form-label">Inspection de l'éducation et de la formation</label>
                        <input type="text" class="form-control" id="inspection_education_formation" name="inspection_education_formation" 
                            value="{{ $configuration->inspection_education_formation ?? '' }}">
                    </div>
                    
                    <div class="mb-3">
                        <label for="logo" class="form-label">Logo</label>
                        <input type="file" class="form-control" id="logo" name="logo">
                        @if(isset($configuration) && $configuration->logo_path)
                            <div class="mt-2">
                                <img src="{{ asset('storage/' . $configuration->logo_path) }}" alt="Logo actuel" class="img-thumbnail" style="max-width: 100px;">
                            </div>
                        @endif
                    </div>
                    
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Enregistrer
                    </button>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <!-- Année scolaire -->
        <div class="card mb-4">
            <div class="card-header header-success">
                <i class="fas fa-calendar-alt me-2"></i>Années scolaires
            </div>
            <div class="card-body">
                <div class="mb-4">
                    <h6 class="fw-bold">Ajouter une année scolaire</h6>
                    <form action="{{ route('parametres.annee-scolaire') }}" method="POST">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-5">
                                <input type="text" class="form-control" name="libelle" placeholder="Ex: 2023-2024" required>
                            </div>
                            <div class="col-md-3">
                                <input type="date" class="form-control" name="date_debut" placeholder="Date début">
                            </div>
                            <div class="col-md-3">
                                <input type="date" class="form-control" name="date_fin" placeholder="Date fin">
                            </div>
                            <div class="col-md-1">
                                <div class="form-check mt-2">
                                    <input class="form-check-input" type="checkbox" name="active" value="1" id="actif">
                                </div>
                            </div>
                        </div>
                        <div class="mt-2">
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-plus me-2"></i>Ajouter
                            </button>
                        </div>
                    </form>
                </div>
                
                <h6 class="fw-bold">Années scolaires disponibles</h6>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Libellé</th>
                                <th>Début</th>
                                <th>Fin</th>
                                <th>Statut</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($anneeScolaires as $annee)
                            <tr>
                                <td>{{ $annee->libelle }}</td>
                                <td>{{ $annee->date_debut ? $annee->date_debut->format('d/m/Y') : '-' }}</td>
                                <td>{{ $annee->date_fin ? $annee->date_fin->format('d/m/Y') : '-' }}</td>
                                <td>
                                    @if($annee->active)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-secondary">Inactive</span>
                                    @endif
                                </td>
                                <td>
                                <button type="button" class="btn btn-sm btn-primary edit-annee-btn" 
                                        data-id="{{ $annee->id }}" 
                                        data-libelle="{{ $annee->libelle }}" 
                                        data-date-debut="{{ $annee->date_debut ? $annee->date_debut->format('Y-m-d') : '' }}" 
                                        data-date-fin="{{ $annee->date_fin ? $annee->date_fin->format('Y-m-d') : '' }}" 
                                        data-active="{{ $annee->active ? '1' : '0' }}">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center">Aucune année scolaire enregistrée</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <!-- Niveaux -->
        <div class="card">
            <div class="card-header header-info">
                <i class="fas fa-layer-group me-2"></i>Niveaux d'enseignement
            </div>
            <div class="card-body">
                <div class="mb-4">
                    <h6 class="fw-bold">Ajouter un niveau</h6>
                    <form action="{{ route('parametres.niveau') }}" method="POST">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-5">
                                <input type="text" class="form-control" name="libelle" placeholder="Ex: Sixième" required>
                            </div>
                            <div class="col-md-3">
                                <input type="text" class="form-control" name="code" placeholder="Ex: 6ème">
                            </div>
                            <div class="col-md-3">
                                <input type="text" class="form-control" name="description" placeholder="Description">
                            </div>
                            <div class="col-md-1">
                                <div class="form-check mt-2">
                                    <input class="form-check-input" type="checkbox" name="actif" value="1" id="niveau_actif" checked>
                                </div>
                            </div>
                        </div>
                        <div class="mt-2">
                            <button type="submit" class="btn btn-info text-white">
                                <i class="fas fa-plus me-2"></i>Ajouter
                            </button>
                        </div>
                    </form>
                </div>
                
                <h6 class="fw-bold">Niveaux disponibles</h6>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Libellé</th>
                                <th>Code</th>
                                <th>Statut</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($niveaux as $niveau)
                            <tr>
                                <td>{{ $niveau->libelle }}</td>
                                <td>{{ $niveau->code ?: '-' }}</td>
                                <td>
                                    @if($niveau->actif)
                                        <span class="badge bg-success">Actif</span>
                                    @else
                                        <span class="badge bg-secondary">Inactif</span>
                                    @endif
                                </td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-primary edit-niveau-btn"
                                        data-id="{{ $niveau->id }}" 
                                        data-libelle="{{ $niveau->libelle }}" 
                                        data-code="{{ $niveau->code }}" 
                                        data-description="{{ $niveau->description }}"
                                        data-actif="{{ $niveau->actif ? '1' : '0' }}">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <a href="{{ route('parametres.classes', $niveau->id) }}" class="btn btn-sm btn-success">
                                        <i class="fas fa-list"></i> Classes
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center">Aucun niveau enregistré</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Édition Année Scolaire -->
<div class="modal fade" id="editAnneeModal" tabindex="-1" aria-labelledby="editAnneeModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editAnneeModalLabel">Modifier l'année scolaire</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editAnneeForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_libelle" class="form-label">Libellé</label>
                        <input type="text" class="form-control" id="edit_libelle" name="libelle" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_date_debut" class="form-label">Date de début</label>
                        <input type="date" class="form-control" id="edit_date_debut" name="date_debut">
                    </div>
                    <div class="mb-3">
                        <label for="edit_date_fin" class="form-label">Date de fin</label>
                        <input type="date" class="form-control" id="edit_date_fin" name="date_fin">
                    </div>
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="edit_active" name="active" value="1">
                        <label class="form-check-label" for="edit_active">Active</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                    <button type="submit" class="btn btn-primary">Enregistrer les modifications</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Édition Niveau -->
<div class="modal fade" id="editNiveauModal" tabindex="-1" aria-labelledby="editNiveauModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editNiveauModalLabel">Modifier le niveau</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editNiveauForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_niveau_libelle" class="form-label">Libellé</label>
                        <input type="text" class="form-control" id="edit_niveau_libelle" name="libelle" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_niveau_code" class="form-label">Code</label>
                        <input type="text" class="form-control" id="edit_niveau_code" name="code">
                    </div>
                    <div class="mb-3">
                        <label for="edit_niveau_description" class="form-label">Description</label>
                        <input type="text" class="form-control" id="edit_niveau_description" name="description">
                    </div>
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="edit_niveau_actif" name="actif" value="1">
                        <label class="form-check-label" for="edit_niveau_actif">Actif</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                    <button type="submit" class="btn btn-primary">Enregistrer les modifications</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Gestion du modal d'édition d'une année scolaire
        $('.edit-annee-btn').click(function() {
            const id = $(this).data('id');
            const libelle = $(this).data('libelle');
            const dateDebut = $(this).data('date-debut');
            const dateFin = $(this).data('date-fin');
            const active = $(this).data('active');
            
            $('#edit_libelle').val(libelle);
            $('#edit_date_debut').val(dateDebut);
            $('#edit_date_fin').val(dateFin);
            $('#edit_active').prop('checked', active == 1);
            
            $('#editAnneeForm').attr('action', '/parametres/annee-scolaire/' + id);
            $('#editAnneeModal').modal('show');
        });
        
        // Gestion du modal d'édition d'un niveau
        $('.edit-niveau-btn').click(function() {
            const id = $(this).data('id');
            const libelle = $(this).data('libelle');
            const code = $(this).data('code');
            const description = $(this).data('description');
            const actif = $(this).data('actif');
            
            $('#edit_niveau_libelle').val(libelle);
            $('#edit_niveau_code').val(code);
            $('#edit_niveau_description').val(description);
            $('#edit_niveau_actif').prop('checked', actif == 1);
            
            $('#editNiveauForm').attr('action', '/parametres/niveau/' + id);
            $('#editNiveauModal').modal('show');
        });
    });
</script>
@endsection