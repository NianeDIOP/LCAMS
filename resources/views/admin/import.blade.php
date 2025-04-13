@extends('layouts.app')

@section('title', 'Importation des données Excel')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">
                    <h4>Importation des données Excel</h4>
                </div>

                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    <div class="alert alert-info">
                        <p><strong>Instructions :</strong></p>
                        <ul>
                            <li>Sélectionnez un fichier Excel au format <code>.xlsx</code> ou <code>.xls</code></li>
                            <li>Le fichier doit contenir les onglets <code>Moyennes eleves</code> et <code>Données détaillées</code></li>
                            <li>Choisissez le niveau scolaire concerné et, optionnellement, une classe spécifique</li>
                            <li>Sélectionnez la méthode d'importation (<code>standard</code> ou <code>avancée</code>)</li>
                            <li>La méthode <strong>avancée</strong> est recommandée pour les fichiers complexes ou problématiques</li>
                        </ul>
                    </div>

                    <form id="importForm" action="{{ route('import.excel') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label for="excel_file" class="form-label">Fichier Excel</label>
                            <input type="file" class="form-control @error('excel_file') is-invalid @enderror" id="excel_file" name="excel_file" accept=".xlsx,.xls" required>
                            @error('excel_file')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="grade_level_id" class="form-label">Niveau scolaire</label>
                            <select class="form-control @error('grade_level_id') is-invalid @enderror" id="grade_level_id" name="grade_level_id" required>
                                <option value="">Sélectionner un niveau</option>
                                @foreach($gradeLevels as $level)
                                    <option value="{{ $level->id }}">{{ $level->name }}</option>
                                @endforeach
                            </select>
                            @error('grade_level_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="classroom_id" class="form-label">Classe (optionnel)</label>
                            <select class="form-control" id="classroom_id" name="classroom_id" disabled>
                                <option value="">Toutes les classes</option>
                            </select>
                            <small class="form-text text-muted">Sélectionnez d'abord un niveau scolaire pour afficher les classes.</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Méthode d'importation</label>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="import_method" id="standard" value="standard">
                                <label class="form-check-label" for="standard">
                                    Standard
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="import_method" id="advanced" value="advanced" checked>
                                <label class="form-check-label" for="advanced">
                                    Avancée (recommandée)
                                </label>
                            </div>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary" id="importBtn">
                                <i class="fas fa-upload me-1"></i> Importer
                            </button>
                        </div>
                    </form>

                    <!-- Progress Bar -->
                    <div class="progress mt-3 d-none" id="progressContainer">
                        <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%"></div>
                    </div>

                    <!-- Results -->
                    <div class="mt-4 d-none" id="importResults"></div>
                </div>
            </div>

            <!-- Liste des importations récentes -->
            <div class="card mt-4">
                <div class="card-header">
                    <h4>Historique des importations récentes</h4>
                </div>

                <div class="card-body">
                    @if($importHistory->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Niveau</th>
                                        <th>Utilisateur</th>
                                        <th>Statut</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($importHistory as $import)
                                        <tr>
                                            <td>{{ $import->created_at->format('d/m/Y H:i') }}</td>
                                            <td>{{ $import->gradeLevel->name }}</td>
                                            <td>{{ $import->user ? $import->user->name : 'Système' }}</td>
                                            <td>
                                                @if($import->status === 'terminé')
                                                    <span class="badge bg-success">Terminé</span>
                                                @elseif($import->status === 'échoué')
                                                    <span class="badge bg-danger">Échoué</span>
                                                @else
                                                    <span class="badge bg-warning">En cours</span>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('import.details', $import->id) }}" class="btn btn-sm btn-info">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <button class="btn btn-sm btn-danger delete-import" data-id="{{ $import->id }}">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info">
                            Aucune importation récente.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Charger les classes lorsqu'un niveau scolaire est sélectionné
        $('#grade_level_id').change(function() {
            var gradeLevelId = $(this).val();
            $('#classroom_id').prop('disabled', !gradeLevelId);
            
            if (gradeLevelId) {
                $.ajax({
                    url: "{{ route('import.get-classrooms') }}",
                    type: "GET",
                    data: { grade_level_id: gradeLevelId },
                    success: function(data) {
                        $('#classroom_id').empty();
                        $('#classroom_id').append('<option value="">Toutes les classes</option>');
                        
                        $.each(data, function(key, classroom) {
                            $('#classroom_id').append('<option value="' + classroom.id + '">' + classroom.name + '</option>');
                        });
                    }
                });
            } else {
                $('#classroom_id').empty();
                $('#classroom_id').append('<option value="">Toutes les classes</option>');
            }
        });

        // Gestion soumission du formulaire avec AJAX
        $('#importForm').submit(function(e) {
            e.preventDefault();
            
            $('#importBtn').prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Importation en cours...');
            $('#progressContainer').removeClass('d-none');
            $('#importResults').addClass('d-none').html('');
            
            let formData = new FormData(this);
            
            $.ajax({
                xhr: function() {
                    var xhr = new window.XMLHttpRequest();
                    xhr.upload.addEventListener('progress', function(e) {
                        if (e.lengthComputable) {
                            var percent = Math.round((e.loaded / e.total) * 100);
                            $('.progress-bar').css('width', percent + '%').text(percent + '%');
                        }
                    }, false);
                    return xhr;
                },
                url: $(this).attr('action'),
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    $('.progress-bar').css('width', '100%').text('100%');
                    
                    let resultHtml = `
                        <div class="alert alert-success">
                            <h5><i class="fas fa-check-circle me-1"></i> Importation réussie</h5>
                            <div class="mt-3">
                                <p><strong>Statistiques :</strong></p>
                                <ul>
                                    <li>Total d'élèves traités : ${response.stats.total_students}</li>
                                    <li>Nouveaux élèves ajoutés : ${response.stats.new_students}</li>
                                    <li>Élèves mis à jour : ${response.stats.updated_students}</li>
                                    <li>Notes par matières importées : ${response.stats.subjects_imported}</li>
                                    <li>Erreurs rencontrées : ${response.stats.errors}</li>
                                </ul>
                            </div>
                            <div>
                                <p class="mb-0">
                                    <a href="${window.location.href}" class="btn btn-outline-primary btn-sm">
                                        <i class="fas fa-sync me-1"></i> Rafraîchir la page
                                    </a>
                                    <a href="/import/details/${response.import_id}" class="btn btn-outline-info btn-sm">
                                        <i class="fas fa-eye me-1"></i> Voir les détails
                                    </a>
                                </p>
                            </div>
                        </div>`;
                    
                    $('#importResults').html(resultHtml).removeClass('d-none');
                    
                    // Réinitialiser le bouton après un délai
                    setTimeout(function() {
                        $('#importBtn').prop('disabled', false).html('<i class="fas fa-upload me-1"></i> Importer');
                    }, 5000);
                },
                error: function(xhr) {
                    let message = 'Une erreur est survenue';
                    let details = '';
                    
                    if (xhr.responseJSON) {
                        message = xhr.responseJSON.message || message;
                        details = xhr.responseJSON.error_code ? `Code erreur: ${xhr.responseJSON.error_code}` : '';
                    }
                    
                    let resultHtml = `
                        <div class="alert alert-danger">
                            <h5><i class="fas fa-exclamation-triangle me-1"></i> Échec de l'importation</h5>
                            <p class="mb-1">${message}</p>
                            ${details ? `<p class="small">${details}</p>` : ''}
                        </div>`;
                    
                    $('.progress-bar').css('width', '100%').addClass('bg-danger');
                    $('#importResults').html(resultHtml).removeClass('d-none');
                    
                    // Réinitialiser le bouton après un délai
                    setTimeout(function() {
                        $('#importBtn').prop('disabled', false).html('<i class="fas fa-upload me-1"></i> Importer');
                    }, 3000);
                }
            });
        });

        // Gestion suppression d'importation
        $('.delete-import').click(function() {
            if (confirm('Êtes-vous sûr de vouloir supprimer cette importation et toutes les données associées ?')) {
                var importId = $(this).data('id');
                var row = $(this).closest('tr');
                
                $.ajax({
                    url: "{{ url('import') }}/" + importId,
                    type: 'DELETE',
                    data: {
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        if (response.success) {
                            row.fadeOut(400, function() {
                                row.remove();
                                
                                // Afficher un message si la table est vide
                                if ($('.table tbody tr').length === 0) {
                                    $('.table-responsive').replaceWith(
                                        '<div class="alert alert-info">Aucune importation récente.</div>'
                                    );
                                }
                            });
                        } else {
                            alert('Erreur: ' + response.message);
                        }
                    },
                    error: function() {
                        alert('Une erreur est survenue lors de la suppression.');
                    }
                });
            }
        });
    });
</script>
@endsection