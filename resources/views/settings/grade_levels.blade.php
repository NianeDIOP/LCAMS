@extends('settings.layout')

@section('title', 'Gestion des Niveaux')

@section('content')
    <div class="row mb-4">
        <div class="col-lg-5">
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-plus-circle me-2"></i> Ajouter un niveau
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('settings.save_grade_level') }}">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="name" class="form-label">Nom du niveau <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" placeholder="Ex: Sixième" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="2">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="order" class="form-label">Ordre d'affichage</label>
                            <input type="number" class="form-control @error('order') is-invalid @enderror" id="order" name="order" value="{{ old('order', 0) }}">
                            <div class="form-text">Numéro déterminant l'ordre d'affichage (plus petit = plus haut)</div>
                            @error('order')
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
                    <i class="fas fa-layer-group me-2"></i> Liste des niveaux
                </div>
                <div class="card-body">
                    @if($gradeLevels->isEmpty())
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i> Aucun niveau n'a été ajouté pour le moment.
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Nom</th>
                                        <th>Description</th>
                                        <th>Ordre</th>
                                        <th>Classes</th>
                                        <th>Statut</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($gradeLevels as $level)
                                        <tr>
                                            <td>{{ $level->name }}</td>
                                            <td>{{ $level->description ?: 'N/A' }}</td>
                                            <td>{{ $level->order }}</td>
                                            <td>
                                                <a href="{{ route('settings.classrooms', $level->id) }}" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-door-open me-1"></i> Classes ({{ $level->classrooms->count() }})
                                                </a>
                                            </td>
                                            <td>
                                                @if($level->active)
                                                    <span class="badge bg-success">Actif</span>
                                                @else
                                                    <span class="badge bg-danger">Inactif</span>
                                                @endif
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-outline-primary edit-level" data-bs-toggle="modal" data-bs-target="#editLevelModal-{{ $level->id }}">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                
                                                <form method="POST" action="{{ route('settings.delete_grade_level', $level->id) }}" class="d-inline delete-form" data-confirm="Êtes-vous sûr de vouloir supprimer ce niveau? Toutes les classes associées seront également supprimées.">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                </form>
                                                
                                                <!-- Modal pour l'édition -->
                                                <div class="modal fade" id="editLevelModal-{{ $level->id }}" tabindex="-1" aria-labelledby="editLevelModalLabel-{{ $level->id }}" aria-hidden="true">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="editLevelModalLabel-{{ $level->id }}">Modifier {{ $level->name }}</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <form method="POST" action="{{ route('settings.update_grade_level', $level->id) }}">
                                                                @csrf
                                                                @method('PUT')
                                                                <div class="modal-body">
                                                                    <div class="mb-3">
                                                                        <label for="name-{{ $level->id }}" class="form-label">Nom du niveau</label>
                                                                        <input type="text" class="form-control" id="name-{{ $level->id }}" name="name" value="{{ $level->name }}" required>
                                                                    </div>
                                                                    
                                                                    <div class="mb-3">
                                                                        <label for="description-{{ $level->id }}" class="form-label">Description</label>
                                                                        <textarea class="form-control" id="description-{{ $level->id }}" name="description" rows="2">{{ $level->description }}</textarea>
                                                                    </div>
                                                                    
                                                                    <div class="mb-3">
                                                                        <label for="order-{{ $level->id }}" class="form-label">Ordre d'affichage</label>
                                                                        <input type="number" class="form-control" id="order-{{ $level->id }}" name="order" value="{{ $level->order }}">
                                                                    </div>
                                                                    
                                                                    <div class="form-check form-switch">
                                                                        <input class="form-check-input" type="checkbox" id="active-{{ $level->id }}" name="active" value="1" {{ $level->active ? 'checked' : '' }}>
                                                                        <label class="form-check-label" for="active-{{ $level->id }}">Actif</label>
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