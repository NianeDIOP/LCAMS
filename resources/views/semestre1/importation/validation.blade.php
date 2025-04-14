@extends('semestre1.layout')

@section('title', 'Validation de l\'importation')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <!-- Barre de progression avec 4 onglets -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="progress-steps">
                                <div class="d-flex justify-content-between position-relative mb-4">
                                    <div class="progress" style="height: 2px; position: absolute; top: 21px; width: 100%; z-index: 0;">
                                        <div class="progress-bar bg-primary" role="progressbar" style="width: 100%"></div>
                                    </div>
                                    
                                    <!-- Toutes les étapes sont terminées -->
                                    <div class="progress-step completed">
                                        <button class="btn btn-success rounded-circle" style="width: 45px; height: 45px; font-size: 1.2rem; z-index: 1;" disabled>
                                            <i class="fas fa-check"></i>
                                        </button>
                                        <div class="mt-2">Sélection classe</div>
                                    </div>
                                    
                                    <div class="progress-step completed">
                                        <button class="btn btn-success rounded-circle" style="width: 45px; height: 45px; font-size: 1.2rem; z-index: 1;" disabled>
                                            <i class="fas fa-check"></i>
                                        </button>
                                        <div class="mt-2">Import Excel</div>
                                    </div>
                                    
                                    <div class="progress-step completed">
                                        <button class="btn btn-success rounded-circle" style="width: 45px; height: 45px; font-size: 1.2rem; z-index: 1;" disabled>
                                            <i class="fas fa-check"></i>
                                        </button>
                                        <div class="mt-2">Visualisation</div>
                                    </div>
                                    
                                    <div class="progress-step completed active">
                                        <button class="btn btn-success rounded-circle" style="width: 45px; height: 45px; font-size: 1.2rem; z-index: 1;" disabled>
                                            <i class="fas fa-check"></i>
                                        </button>
                                        <div class="mt-2">Validation</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Message de succès -->
                    <div class="text-center pt-3 pb-5">
                        <div class="d-inline-block p-4 mb-4 bg-light rounded-circle">
                            <i class="fas fa-check-circle text-success" style="font-size: 5rem;"></i>
                        </div>
                        <h3 class="mb-3">Importation réussie !</h3>
                        <p class="lead text-muted mb-4">Les données ont été correctement importées dans la base de données.</p>
                    </div>

                    <!-- Résumé de l'importation -->
                    <div class="card border-0 bg-light mb-4">
                        <div class="card-body p-4">
                            <div class="row">
                                <div class="col-md-6">
                                    <h5>Résumé</h5>
                                    <div class="text-muted mb-3" id="fileName">
                                        <i class="fas fa-file-excel me-2"></i>
                                        <span></span>
                                    </div>
                                    <div class="row">
                                        <div class="col-6 mb-3">
                                            <div class="text-muted small">Classe</div>
                                            <div class="fw-bold" id="classroomName"></div>
                                        </div>
                                        <div class="col-6 mb-3">
                                            <div class="text-muted small">Niveau</div>
                                            <div class="fw-bold" id="gradeLevelName"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="row">
                                        <div class="col-6 mb-3">
                                            <div class="text-muted small">Élèves importés</div>
                                            <div class="fw-bold" id="totalStudents">0</div>
                                        </div>
                                        <div class="col-6 mb-3">
                                            <div class="text-muted small">Matières importées</div>
                                            <div class="fw-bold" id="totalSubjects">0</div>
                                        </div>
                                        <div class="col-6 mb-3">
                                            <div class="text-muted small">Notes individuelles</div>
                                            <div class="fw-bold" id="totalMarks">0</div>
                                        </div>
                                        <div class="col-6 mb-3">
                                            <div class="text-muted small">Date d'importation</div>
                                            <div class="fw-bold" id="importDate"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Section d'erreur (cachée par défaut) -->
                    <div id="errorSection" class="alert alert-danger d-none mb-4">
                        <h5 class="alert-heading d-flex align-items-center">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            Données manquantes
                        </h5>
                        <p>Impossible de récupérer les détails de l'importation. Il est possible que l'importation ait été interrompue.</p>
                    </div>

                    <!-- Actions possibles -->
                    <div class="d-flex flex-column flex-md-row justify-content-center gap-3">
                        <a href="{{ route('semestre1.resultats.classe', ['id' => 0]) }}" class="btn btn-primary px-4">
                            <i class="fas fa-chart-bar me-2"></i>
                            Voir les résultats de la classe
                        </a>
                        <a href="{{ route('semestre1.importation') }}" class="btn btn-outline-secondary px-4">
                            <i class="fas fa-upload me-2"></i>
                            Importer une autre classe
                        </a>
                        <a href="{{ route('semestre1.index') }}" class="btn btn-outline-secondary px-4">
                            <i class="fas fa-home me-2"></i>
                            Retour à l'accueil
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- CSS pour personnaliser les étapes -->
<style>
    .progress-step {
        text-align: center;
        width: 120px;
    }
    
    .progress-step.active button {
        border-color: #198754;
    }
    
    .progress-step.completed button {
        background-color: #198754;
        border-color: #198754;
    }
</style>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Éléments du DOM
        const fileName = document.getElementById('fileName').querySelector('span');
        const classroomName = document.getElementById('classroomName');
        const gradeLevelName = document.getElementById('gradeLevelName');
        const totalStudents = document.getElementById('totalStudents');
        const totalSubjects = document.getElementById('totalSubjects');
        const totalMarks = document.getElementById('totalMarks');
        const importDate = document.getElementById('importDate');
        const errorSection = document.getElementById('errorSection');
        
        // Récupérer les données d'importation
        const importResult = JSON.parse(sessionStorage.getItem('importResult') || '{}');
        const excelFileName = sessionStorage.getItem('excelFileName');
        const classroomId = sessionStorage.getItem('selectedClassroomId');
        const classroomNameValue = sessionStorage.getItem('selectedClassroomName');
        const gradeLevelNameValue = sessionStorage.getItem('selectedGradeLevelName');
        
        // Mettre à jour le lien vers les résultats de la classe
        const resultatsLink = document.querySelector('a[href*="semestre1.resultats.classe"]');
        if (resultatsLink && classroomId) {
            resultatsLink.href = resultatsLink.href.replace('/0', '/' + classroomId);
        }
        
        // Vérifier si les données d'importation sont disponibles
        if (!importResult || Object.keys(importResult).length === 0) {
            errorSection.classList.remove('d-none');
        } else {
            // Afficher les détails de l'importation
            fileName.textContent = excelFileName || 'Fichier Excel importé';
            classroomName.textContent = classroomNameValue || '-';
            gradeLevelName.textContent = gradeLevelNameValue || '-';
            
            // Afficher les statistiques d'importation
            totalStudents.textContent = importResult.students_count || '0';
            totalSubjects.textContent = importResult.subjects_count || '0';
            totalMarks.textContent = importResult.marks_count || '0';
            
            // Afficher la date d'importation
            const now = new Date();
            importDate.textContent = `${now.toLocaleDateString('fr-FR')} à ${now.toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' })}`;
        }
        
        // Nettoyer sessionStorage après affichage
        setTimeout(() => {
            // Garder uniquement l'ID de classe pour la page de résultats
            sessionStorage.removeItem('importResult');
            sessionStorage.removeItem('previewData');
            sessionStorage.removeItem('excelFileName');
            sessionStorage.removeItem('fileUploading');
        }, 1000);
    });
</script>
@endsection