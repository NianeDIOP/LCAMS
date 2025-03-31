@extends('layouts.app')

@section('title', 'Semestre 1 - Rapports')

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('semestre1.index') }}">Semestre 1</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Rapports</li>
                </ol>
            </nav>
            <h1><i class="fas fa-file-pdf me-2"></i>Rapports - Semestre 1</h1>
            <p class="text-muted">Générez des rapports pour le premier semestre.</p>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-6">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0"><i class="fas fa-file-alt me-2"></i>Générer un rapport de classe</h5>
                </div>
                <div class="card-body">
                    <form id="report-form">
                        <div class="mb-3">
                            <label for="niveau_id" class="form-label">Niveau</label>
                            <select class="form-select" id="niveau_id" required>
                                <option value="">Sélectionnez un niveau</option>
                                @foreach($niveaux as $niveau)
                                    <option value="{{ $niveau->id }}">{{ $niveau->libelle }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="classe_id" class="form-label">Classe</label>
                            <select class="form-select" id="classe_id" required disabled>
                                <option value="">Sélectionnez d'abord un niveau</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Options du rapport</label>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="include_stats" checked>
                                <label class="form-check-label" for="include_stats">
                                    Inclure les statistiques générales
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="include_students" checked>
                                <label class="form-check-label" for="include_students">
                                    Inclure la liste des élèves
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="include_disciplines" checked>
                                <label class="form-check-label" for="include_disciplines">
                                    Inclure les statistiques par discipline
                                </label>
                            </div>
                        </div>
                        
                        <button type="submit" class="btn btn-primary" id="generate-btn">
                            <i class="fas fa-file-pdf me-2"></i>Générer le rapport
                        </button>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="alert alert-info">
                <h5><i class="fas fa-info-circle me-2"></i>Information</h5>
                <p>Les rapports généreront un fichier PDF contenant les données sélectionnées pour la classe choisie.</p>
                <p>Vous pourrez télécharger le rapport une fois généré.</p>
            </div>
            
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white">
                    <h5 class="card-title mb-0"><i class="fas fa-list me-2"></i>Types de rapports disponibles</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Rapport de classe
                            <span class="badge bg-primary rounded-pill">Disponible</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Rapport par discipline
                            <span class="badge bg-secondary rounded-pill">À venir</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Rapport de niveau
                            <span class="badge bg-secondary rounded-pill">À venir</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Rapports individuels
                            <span class="badge bg-secondary rounded-pill">À venir</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de prévisualisation -->
<div class="modal fade" id="previewModal" tabindex="-1" aria-labelledby="previewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="previewModalLabel">Prévisualisation du rapport</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="text-center py-5" id="preview-loader">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Chargement...</span>
                    </div>
                    <p class="mt-2">Génération du rapport en cours...</p>
                </div>
                <div id="preview-content" class="d-none">
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle me-2"></i>Rapport généré avec succès !
                    </div>
                    <div class="text-center">
                        <a href="#" id="download-link" class="btn btn-primary btn-lg">
                            <i class="fas fa-download me-2"></i>Télécharger le rapport
                        </a>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Chargement dynamique des classes quand un niveau est sélectionné
        $('#niveau_id').change(function() {
            const niveau_id = $(this).val();
            if (niveau_id) {
                $.ajax({
                    url: '/api/niveaux/' + niveau_id + '/classes',
                    type: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        let options = '<option value="">Sélectionnez une classe</option>';
                        $.each(data, function(key, classe) {
                            options += '<option value="' + classe.id + '">' + classe.libelle + '</option>';
                        });
                        $('#classe_id').html(options).prop('disabled', false);
                    },
                    error: function() {
                        alert('Erreur lors du chargement des classes');
                    }
                });
            } else {
                $('#classe_id').html('<option value="">Sélectionnez d\'abord un niveau</option>').prop('disabled', true);
            }
        });
        
        // Gestion du formulaire de génération de rapport
        $('#report-form').submit(function(e) {
            e.preventDefault();
            
            const classe_id = $('#classe_id').val();
            if (!classe_id) {
                alert('Veuillez sélectionner une classe');
                return;
            }
            
            // Afficher le modal de prévisualisation
            $('#preview-content').addClass('d-none');
            $('#preview-loader').removeClass('d-none');
            $('#previewModal').modal('show');
            
            // Simuler le temps de génération du rapport (à remplacer par une vraie requête AJAX)
            setTimeout(function() {
                $('#preview-loader').addClass('d-none');
                $('#preview-content').removeClass('d-none');
                
                // Mettre à jour le lien de téléchargement (à remplacer par le vrai lien)
                $('#download-link').attr('href', '/semestre1/rapport-classe/' + classe_id);
            }, 2000);
        });
    });
</script>
@endsection