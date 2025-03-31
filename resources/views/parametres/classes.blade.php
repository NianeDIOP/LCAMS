@extends('layouts.app')

@section('title', 'Gestion des classes')

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('parametres.index') }}">Paramètres</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Classes du niveau {{ $niveau->libelle }}</li>
                </ol>
            </nav>
            <h1><i class="fas fa-users me-2"></i>Classes du niveau {{ $niveau->libelle }}</h1>
            <p class="text-muted">Gérez les classes pour ce niveau d'enseignement.</p>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-4">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0"><i class="fas fa-plus-circle me-2"></i>Ajouter une classe</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('parametres.classe') }}" method="POST">
                        @csrf
                        <input type="hidden" name="niveau_id" value="{{ $niveau->id }}">
                        
                        <div class="mb-3">
                            <label for="libelle" class="form-label">Libellé *</label>
                            <input type="text" class="form-control" id="libelle" name="libelle" 
                                placeholder="Ex: 6ème A" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="code" class="form-label">Code</label>
                            <input type="text" class="form-control" id="code" name="code" 
                                placeholder="Ex: 6A">
                        </div>
                        
                        <div class="mb-3">
                            <label for="effectif" class="form-label">Effectif</label>
                            <input type="number" class="form-control" id="effectif" name="effectif" 
                                placeholder="Nombre d'élèves">
                        </div>
                        
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="actif" name="actif" value="1" checked>
                            <label class="form-check-label" for="actif">Classe active</label>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">Ajouter</button>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-info text-white">
                    <h5 class="card-title mb-0"><i class="fas fa-list me-2"></i>Liste des classes</h5>
                </div>
                <div class="card-body">
                    @if($classes->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Libellé</th>
                                    <th>Code</th>
                                    <th>Effectif</th>
                                    <th>Statut</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($classes as $classe)
                                <tr>
                                    <td>{{ $classe->libelle }}</td>
                                    <td>{{ $classe->code ?: '-' }}</td>
                                    <td>{{ $classe->effectif ?: '-' }}</td>
                                    <td>
                                        @if($classe->actif)
                                            <span class="badge bg-success">Active</span>
                                        @else
                                            <span class="badge bg-secondary">Inactive</span>
                                        @endif
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-primary edit-classe-btn"
                                            data-id="{{ $classe->id }}" 
                                            data-libelle="{{ $classe->libelle }}" 
                                            data-code="{{ $classe->code }}" 
                                            data-effectif="{{ $classe->effectif }}"
                                            data-actif="{{ $classe->actif ? '1' : '0' }}">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="alert alert-info">
                        Aucune classe n'a encore été créée pour ce niveau. Utilisez le formulaire ci-contre pour ajouter une classe.
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Édition Classe -->
<div class="modal fade" id="editClasseModal" tabindex="-1" aria-labelledby="editClasseModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editClasseModalLabel">Modifier la classe</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editClasseForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_classe_libelle" class="form-label">Libellé</label>
                        <input type="text" class="form-control" id="edit_classe_libelle" name="libelle" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_classe_code" class="form-label">Code</label>
                        <input type="text" class="form-control" id="edit_classe_code" name="code">
                    </div>
                    <div class="mb-3">
                        <label for="edit_classe_effectif" class="form-label">Effectif</label>
                        <input type="number" class="form-control" id="edit_classe_effectif" name="effectif">
                        </div>
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="edit_classe_actif" name="actif" value="1">
                        <label class="form-check-label" for="edit_classe_actif">Active</label>
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
        // Gestion du modal d'édition d'une classe
        $('.edit-classe-btn').click(function() {
            const id = $(this).data('id');
            const libelle = $(this).data('libelle');
            const code = $(this).data('code');
            const effectif = $(this).data('effectif');
            const actif = $(this).data('actif');
            
            $('#edit_classe_libelle').val(libelle);
            $('#edit_classe_code').val(code);
            $('#edit_classe_effectif').val(effectif);
            $('#edit_classe_actif').prop('checked', actif == 1);
            
            $('#editClasseForm').attr('action', '/parametres/classe/' + id);
            $('#editClasseModal').modal('show');
        });
    });
</script>
@endsection