@extends('settings.layout')

@section('title', 'Classes de niveau ' . $gradeLevel->name)

@section('content')
    <div class="mb-4">
        <a href="{{ route('settings.grade_levels') }}" class="btn btn-outline-secondary mb-3">
            <i class="fas fa-arrow-left me-2"></i> Retour aux niveaux
        </a>
    </div>

    <div class="row">
        <div class="col-lg-5">
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-plus-circle me-2"></i> Ajouter une classe pour {{ $gradeLevel->name }}
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('settings.save_classroom', $gradeLevel->id) }}">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="name" class="form-label">Nom de la classe <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" placeholder="Ex: 6èmeA" required>
                            <div class="form-text">Exemple: 6èmeA, 6èmeB, etc.</div>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i> Ajouter
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-lg-7">
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-door-open me-2"></i> Classes de niveau {{ $gradeLevel->name }}
                </div>
                <div class="card-body">
                    @if($gradeLevel->classrooms->isEmpty())
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i> Aucune classe n'a été ajoutée pour ce niveau.
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Nom</th>
                                        <th>Statut</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($gradeLevel->classrooms as $classroom)
                                        <tr>
                                            <td>{{ $classroom->name }}</td>
                                            <td>
                                                @if($classroom->active)
                                                    <span class="badge bg-success">Active</span>
                                                @else
                                                    <span class="badge bg-danger">Inactive</span>
                                                @endif
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-outline-primary edit-classroom" data-bs-toggle="modal" data-bs-target="#editClassroomModal-{{ $classroom->id }}">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                
                                                <form method="POST" action="{{ route('settings.delete_classroom', $classroom->id) }}" class="d-inline delete-form" data-confirm="Êtes-vous sûr de vouloir supprimer cette classe?">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                </form>
                                                
                                                <!-- Modal pour l'édition -->
                                                <div class="modal fade" id="editClassroomModal-{{ $classroom->id }}" tabindex="-1" aria-labelledby="editClassroomModalLabel-{{ $classroom->id }}" aria-hidden="true">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="editClassroomModalLabel-{{ $classroom->id }}">Modifier {{ $classroom->name }}</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <form method="POST" action="{{ route('settings.update_classroom', $classroom->id) }}">
                                                                @csrf
                                                                @method('PUT')
                                                                <div class="modal-body">
                                                                    <div class="mb-3">
                                                                        <label for="name-{{ $classroom->id }}" class="form-label">Nom de la classe</label>
                                                                        <input type="text" class="form-control" id="name-{{ $classroom->id }}" name="name" value="{{ $classroom->name }}" required>
                                                                    </div>
                                                                    
                                                                    <div class="form-check form-switch">
                                                                        <input class="form-check-input" type="checkbox" id="active-{{ $classroom->id }}" name="active" value="1" {{ $classroom->active ? 'checked' : '' }}>
                                                                        <label class="form-check-label" for="active-{{ $classroom->id }}">Active</label>
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
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection