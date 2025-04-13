@extends('semestre1.layout')

@section('title', 'Importation de données')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Importation des données du semestre 1</h5>
                
                <!-- Ajout de liens rapides vers l'historique -->
                <div>
                    <a href="#historique" class="btn btn-sm btn-outline-secondary me-2">
                        <i class="fas fa-history"></i> Historique des importations
                    </a>
                    <a href="{{ route('semestre1.index') }}" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-arrow-left"></i> Retour au semestre 1
                    </a>
                </div>
            </div>

            <div class="card-body">
                @if (session('success'))
                    <div class="alert alert-success" role="alert">
                        {{ session('success') }}
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger" role="alert">
                        {{ session('error') }}
                    </div>
                @endif

                @if (session('info'))
                    <div class="alert alert-info" role="alert">
                        {{ session('info') }}
                    </div>
                @endif

                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="mb-0">Nouvelle importation</h6>
                            </div>
                            <div class="card-body">
                                <form method="POST" action="{{ route('semestre1.import-moyennes') }}" enctype="multipart/form-data">
                                    @csrf

                                    <div class="mb-3">
                                        <label for="grade_level_id" class="form-label">Niveau scolaire <span class="text-danger">*</span></label>
                                        <select id="grade_level_id" name="grade_level_id" class="form-select @error('grade_level_id') is-invalid @enderror" required>
                                            <option value="">Sélectionner un niveau</option>
                                            @foreach($gradeLevels as $gradeLevel)
                                                <option value="{{ $gradeLevel->id }}" {{ old('grade_level_id') == $gradeLevel->id ? 'selected' : '' }}>
                                                    {{ $gradeLevel->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('grade_level_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="form-text text-muted">Le niveau scolaire auquel seront associées les données importées.</small>
                                    </div>

                                    <!-- Ajout du sélecteur de classe -->
                                    <div class="mb-3">
                                        <label for="classroom_id" class="form-label">Classe (optionnel)</label>
                                        <select id="classroom_id" name="classroom_id" class="form-select @error('classroom_id') is-invalid @enderror">
                                            <option value="">Toutes les classes</option>
                                            <!-- Les options de classes seront chargées dynamiquement via JavaScript -->
                                        </select>
                                        @error('classroom_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="form-text text-muted">Sélectionnez une classe spécifique ou laissez vide pour importer toutes les classes du niveau.</small>
                                    </div>

                                    <div class="mb-3">
                                        <label for="excel_file" class="form-label">Fichier Excel <span class="text-danger">*</span></label>
                                        <input type="file" id="excel_file" name="excel_file" class="form-control @error('excel_file') is-invalid @enderror" required>
                                        @error('excel_file')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="form-text text-muted">
                                            Le fichier doit contenir l'onglet "Moyennes eleves".<br>
                                            Format accepté : .xlsx, .xls (max 10 Mo)
                                        </small>
                                    </div>

                                    <div class="alert alert-info">
                                        <h6 class="alert-heading">Format du fichier Excel</h6>
                                        <p class="small mb-0">
                                            Le fichier doit contenir l'onglet <strong>Moyennes eleves</strong> avec les moyennes générales des élèves.
                                            <button type="button" class="btn btn-link p-0" data-bs-toggle="modal" data-bs-target="#formatModal">
                                                Voir le format requis en détail
                                            </button>
                                        </p>
                                    </div>

                                    <div class="d-grid gap-2">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-file-import"></i> Importer le fichier
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card h-100">
                            <div class="card-header">
                                <h6 class="mb-0">Instructions d'importation</h6>
                            </div>
                            <div class="card-body">
                                <div class="mb-4">
                                    <h6 class="fw-bold">Préparation du fichier Excel</h6>
                                    <p>Le fichier d'import doit suivre un format spécifique :</p>
                                    <ol>
                                        <li>Créer un fichier Excel avec l'onglet nommé <code>Moyennes eleves</code></li>
                                        <li>Dans cet onglet, les données commencent à partir de la ligne 12</li>
                                        <li>Les colonnes doivent respecter l'ordre suivant (A à H) : Matricule, Nom, Prénom, Classe, Moyenne, Rang, Appréciation, Sexe</li>
                                    </ol>
                                </div>
                                
                                <div class="mb-4">
                                    <h6 class="fw-bold">Processus d'importation</h6>
                                    <ol>
                                        <li>Sélectionnez le niveau scolaire concerné</li>
                                        <li>Sélectionnez éventuellement une classe spécifique</li>
                                        <li>Choisissez votre fichier Excel préparé</li>
                                        <li>Cliquez sur "Importer le fichier"</li>
                                        <li>Le système validera les données et les importera</li>
                                        <li>Un rapport d'importation vous sera présenté</li>
                                    </ol>
                                </div>

                                <div class="alert alert-warning mb-0">
                                    <i class="fas fa-exclamation-triangle"></i> <strong>Important :</strong> L'importation peut modifier ou ajouter des données. Assurez-vous d'avoir préparé correctement votre fichier avant l'importation.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card" id="historique">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">Historique des importations</h6>
                        <div class="btn-group">
                            <button type="button" class="btn btn-sm btn-outline-secondary" id="refreshHistoryBtn">
                                <i class="fas fa-sync-alt"></i> Actualiser
                            </button>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Niveau</th>
                                        <th>Statut</th>
                                        <th>Élèves importés</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($importHistory as $import)
                                        @php
                                            $details = json_decode($import->details);
                                            $stats = $details->statistiques ?? null;
                                        @endphp
                                        <tr>
                                            <td>{{ $import->created_at->format('d/m/Y H:i') }}</td>
                                            <td>{{ $import->gradeLevel->name ?? 'N/A' }}</td>
                                            <td>
                                                @if($import->status === 'terminé')
                                                    <span class="badge bg-success">Terminé</span>
                                                @elseif($import->status === 'en_cours')
                                                    <span class="badge bg-warning">En cours</span>
                                                @else
                                                    <span class="badge bg-danger">Échoué</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($import->status === 'terminé' && $stats)
                                                    {{ $stats->total_students ?? 0 }}
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm" role="group">
                                                    <a href="{{ route('semestre1.show-import', $import->id) }}" class="btn btn-outline-primary">
                                                        <i class="fas fa-eye"></i> Détails
                                                    </a>
                                                    @if($import->status !== 'en_cours')
                                                        <form method="POST" action="{{ route('semestre1.delete-import', $import->id) }}" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette importation ?');">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-outline-danger">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </form>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center py-3">Aucune importation récente</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal pour le format du fichier -->
<div class="modal fade" id="formatModal" tabindex="-1" aria-labelledby="formatModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="formatModalLabel">Format du fichier Excel requis</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <h6>Onglet "Moyennes eleves"</h6>
                <p>Cet onglet doit contenir les informations de base des élèves et leur moyenne générale.</p>
                <p>Les données commencent à la ligne 12 (les lignes 1-11 sont réservées aux en-têtes) et doivent contenir les colonnes suivantes:</p>
                
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Colonne</th>
                            <th>Description</th>
                            <th>Obligatoire</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>A</td>
                            <td>Matricule</td>
                            <td>Non</td>
                        </tr>
                        <tr>
                            <td>B</td>
                            <td>Nom</td>
                            <td>Oui</td>
                        </tr>
                        <tr>
                            <td>C</td>
                            <td>Prénom</td>
                            <td>Oui</td>
                        </tr>
                        <tr>
                            <td>D</td>
                            <td>Classe</td>
                            <td>Oui</td>
                        </tr>
                        <tr>
                            <td>E</td>
                            <td>Moyenne</td>
                            <td>Oui</td>
                        </tr>
                        <tr>
                            <td>F</td>
                            <td>Rang</td>
                            <td>Non</td>
                        </tr>
                        <tr>
                            <td>G</td>
                            <td>Appréciation</td>
                            <td>Non</td>
                        </tr>
                        <tr>
                            <td>H</td>
                            <td>Sexe (M/F)</td>
                            <td>Non</td>
                        </tr>
                    </tbody>
                </table>

                <div class="text-center mt-4">
                    <img src="{{ asset('img/Moyennes eleves.PNG') }}" class="img-fluid border" alt="Exemple de format" style="max-width: 100%;">
                    <p class="text-muted mt-2">Exemple de format de l'onglet "Moyennes eleves"</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    console.log('Script de chargement des classes initialisé');
    
    // Fonction de débogage pour vérifier la disponibilité de jQuery
    if (typeof $ === 'undefined') {
        console.error('jQuery n\'est pas chargé!');
        alert('Une erreur est survenue: jQuery n\'est pas chargé correctement.');
        return;
    }
    
    // Attendre un court instant pour s'assurer que tous les éléments DOM sont bien initialisés
    setTimeout(function() {
        // Déclencher l'événement change si un niveau est déjà sélectionné (pour les rechargements de page)
        var selectedGradeLevel = $('#grade_level_id').val();
        console.log('Niveau préalablement sélectionné:', selectedGradeLevel);
        if (selectedGradeLevel) {
            console.log('Déclenchement automatique du chargement des classes');
            loadClassrooms(selectedGradeLevel);
        }
    }, 100);

    // Fonction pour charger les classes d'un niveau
    function loadClassrooms(gradeLevelId) {
        if (!gradeLevelId) {
            console.log('Aucun ID de niveau fourni');
            return;
        }
        
        console.log('Chargement des classes pour le niveau:', gradeLevelId);
        
        // Désactiver le sélecteur de classe pendant le chargement
        var classroomSelect = $('#classroom_id');
        classroomSelect.prop('disabled', true);
        classroomSelect.empty().append('<option value="">Chargement en cours...</option>');
        
        // URL absolue pour éviter les problèmes de chemins relatifs
        var apiUrl = window.location.origin + '/api/classrooms/' + gradeLevelId;
        console.log('Appel API vers:', apiUrl);
        
        $.ajax({
            url: apiUrl,
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                console.log('Données reçues:', data);
                
                classroomSelect.empty();
                classroomSelect.append('<option value="">Toutes les classes</option>');
                
                if (data && data.length > 0) {
                    $.each(data, function(key, value) {
                        classroomSelect.append('<option value="' + value.id + '">' + value.name + '</option>');
                    });
                    console.log(data.length + ' classes chargées');
                    
                    // Notification visuelle du succès
                    $('<div class="alert alert-success alert-dismissible fade show mt-2" role="alert">')
                        .text(data.length + ' classes chargées avec succès')
                        .append('<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>')
                        .insertAfter(classroomSelect)
                        .delay(3000)
                        .fadeOut(function() { $(this).remove(); });
                        
                } else {
                    console.log('Aucune classe trouvée pour ce niveau');
                    classroomSelect.append('<option value="" disabled>Aucune classe disponible</option>');
                    
                    // Notification d'avertissement
                    $('<div class="alert alert-warning alert-dismissible fade show mt-2" role="alert">')
                        .text('Aucune classe active n\'est disponible pour ce niveau')
                        .append('<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>')
                        .insertAfter(classroomSelect)
                        .delay(3000)
                        .fadeOut(function() { $(this).remove(); });
                }
                
                classroomSelect.prop('disabled', false);
            },
            error: function(xhr, status, error) {
                console.error('Erreur lors du chargement des classes:', error);
                console.error('Statut:', status);
                console.error('Réponse:', xhr.responseText);
                
                classroomSelect.empty();
                classroomSelect.append('<option value="">Erreur de chargement</option>');
                classroomSelect.prop('disabled', true);
                
                // Afficher une alerte pour informer l'utilisateur
                $('<div class="alert alert-danger alert-dismissible fade show mt-2" role="alert">')
                    .text('Erreur lors du chargement des classes: ' + error)
                    .append('<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>')
                    .insertAfter(classroomSelect);
            }
        });
    }

    // Charger les classes en fonction du niveau sélectionné
    $('#grade_level_id').change(function() {
        var gradeLevelId = $(this).val();
        console.log('Niveau scolaire sélectionné:', gradeLevelId);
        loadClassrooms(gradeLevelId);
    });

    // Actualiser l'historique des importations
    $('#refreshHistoryBtn').click(function() {
        location.reload();
    });
});
</script>
@endpush
@endsection