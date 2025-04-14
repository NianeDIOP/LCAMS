@extends('semestre1.layout')

@section('title', 'Visualisation des données')

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
                                        <div class="progress-bar bg-primary" role="progressbar" style="width: 66%"></div>
                                    </div>
                                    
                                    <!-- Étapes 1-2: Terminées -->
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
                                    
                                    <!-- Étape 3: Visualisation (active) -->
                                    <div class="progress-step active">
                                        <button class="btn btn-primary rounded-circle" style="width: 45px; height: 45px; font-size: 1.2rem; z-index: 1;" disabled>3</button>
                                        <div class="mt-2">Visualisation</div>
                                    </div>
                                    
                                    <!-- Étape 4: Validation (à venir) -->
                                    <div class="progress-step">
                                        <button class="btn btn-outline-secondary rounded-circle" style="width: 45px; height: 45px; font-size: 1.2rem; z-index: 1;" disabled>4</button>
                                        <div class="mt-2">Validation</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Section d'import en cours (affichée lors de l'importation) -->
                    <div id="importInProgress" class="d-none">
                        <div class="card mb-4">
                            <div class="card-body text-center py-5">
                                <div class="spinner-border text-primary mb-3" role="status" style="width: 3rem; height: 3rem;">
                                    <span class="visually-hidden">Chargement...</span>
                                </div>
                                <h5>Importation en cours...</h5>
                                <p class="text-muted">Veuillez patienter pendant que nous importons vos données</p>
                            </div>
                        </div>
                    </div>

                    <!-- Section principale (visible après chargement) -->
                    <div id="mainContent" class="d-none">
                        <!-- Informations sur le fichier -->
                        <div class="d-flex justify-content-between mb-4">
                            <div>
                                <h5 class="mb-1" id="fileTitle">Visualisation des données</h5>
                                <div class="text-muted d-flex align-items-center">
                                    <i class="fas fa-file-excel me-2"></i>
                                    <span id="fileName">nom-du-fichier.xlsx</span>
                                </div>
                            </div>
                            <button class="btn btn-sm btn-outline-secondary" id="backToImport">
                                <i class="fas fa-arrow-left me-1"></i>
                                Changer de fichier
                            </button>
                        </div>

                        <!-- Résumé des données -->
                        <div class="row mb-4">
                            <div class="col-md-4 mb-3 mb-md-0">
                                <div class="card h-100 border-0 bg-light">
                                    <div class="card-body">
                                        <h6 class="text-muted mb-3">Informations générales</h6>
                                        <div class="d-flex justify-content-between mb-2">
                                            <span>Classe</span>
                                            <strong id="classroomName"></strong>
                                        </div>
                                        <div class="d-flex justify-content-between mb-2">
                                            <span>Niveau scolaire</span>
                                            <strong id="gradeLevelName"></strong>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-4 mb-3 mb-md-0">
                                <div class="card h-100 border-0 bg-light">
                                    <div class="card-body">
                                        <h6 class="text-muted mb-3">Statistiques d'importation</h6>
                                        <div class="d-flex justify-content-between mb-2">
                                            <span>Nombre total d'élèves</span>
                                            <strong id="totalStudents">0</strong>
                                        </div>
                                        <div class="d-flex justify-content-between mb-2">
                                            <span>Moyenne générale</span>
                                            <strong id="averageGrade">0.00</strong>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="card h-100 border-0 bg-light">
                                    <div class="card-body">
                                        <h6 class="text-muted mb-3">Matières détectées</h6>
                                        <div id="subjectsList" class="d-flex flex-wrap">
                                            <!-- Les matières seront ajoutées ici dynamiquement -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tableau des élèves -->
                        <div class="card mb-4">
                            <div class="card-body">
                                <h5 class="card-title mb-3">Liste des élèves</h5>
                                <div class="table-responsive">
                                    <table class="table table-sm table-striped table-hover">
                                        <thead>
                                            <tr>
                                                <th>IEN</th>
                                                <th>Nom</th>
                                                <th>Prénom</th>
                                                <th>Sexe</th>
                                                <th>Moyenne</th>
                                                <th>Rang</th>
                                                <th>Décision</th>
                                            </tr>
                                        </thead>
                                        <tbody id="studentsTableBody">
                                            <!-- Les données seront insérées ici dynamiquement -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Alertes et avertissements -->
                        <div id="warningsContainer" class="d-none mb-4">
                            <div class="alert alert-warning">
                                <h6 class="alert-heading d-flex align-items-center">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    Avertissements
                                </h6>
                                <ul id="warningsList" class="mb-0">
                                    <!-- Les avertissements seront ajoutés ici dynamiquement -->
                                </ul>
                            </div>
                        </div>

                        <!-- Boutons de navigation -->
                        <div class="d-flex justify-content-between">
                            <button type="button" class="btn btn-outline-secondary" id="prevStep">
                                <i class="fas fa-arrow-left me-2"></i>
                                Retour
                            </button>
                            
                            <button type="button" class="btn btn-success" id="importData">
                                <i class="fas fa-database me-2"></i>
                                Importer dans la base de données
                            </button>
                        </div>
                    </div>

                    <!-- Section d'erreur -->
                    <div id="errorSection" class="d-none">
                        <div class="alert alert-danger">
                            <h6 class="alert-heading d-flex align-items-center">
                                <i class="fas fa-exclamation-circle me-2"></i>
                                Erreur
                            </h6>
                            <p id="errorMessage">Une erreur s'est produite lors de la visualisation des données.</p>
                            <hr>
                            <button class="btn btn-sm btn-outline-danger" id="returnToImport">
                                Retour à l'importation
                            </button>
                        </div>
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
        border-color: #0d6efd;
    }
    
    .progress-step.completed button {
        background-color: #198754;
        border-color: #198754;
    }
    
    .subject-badge {
        display: inline-block;
        padding: 0.25rem 0.5rem;
        background-color: #e9ecef;
        border-radius: 0.25rem;
        margin-right: 0.5rem;
        margin-bottom: 0.5rem;
        font-size: 0.875rem;
    }
</style>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Éléments du DOM
        const importInProgress = document.getElementById('importInProgress');
        const mainContent = document.getElementById('mainContent');
        const errorSection = document.getElementById('errorSection');
        const fileName = document.getElementById('fileName');
        const classroomName = document.getElementById('classroomName');
        const gradeLevelName = document.getElementById('gradeLevelName');
        const totalStudents = document.getElementById('totalStudents');
        const averageGrade = document.getElementById('averageGrade');
        const subjectsList = document.getElementById('subjectsList');
        const studentsTableBody = document.getElementById('studentsTableBody');
        const warningsContainer = document.getElementById('warningsContainer');
        const warningsList = document.getElementById('warningsList');
        const errorMessage = document.getElementById('errorMessage');
        const importDataBtn = document.getElementById('importData');
        const prevStepBtn = document.getElementById('prevStep');
        const backToImportBtn = document.getElementById('backToImport');
        const returnToImportBtn = document.getElementById('returnToImport');
        
        // Récupérer les données de la page précédente
        const previewData = JSON.parse(sessionStorage.getItem('previewData') || '{}');
        const excelFileName = sessionStorage.getItem('excelFileName');
        const classroomId = sessionStorage.getItem('selectedClassroomId');
        const classroomNameValue = sessionStorage.getItem('selectedClassroomName');
        const gradeLevelNameValue = sessionStorage.getItem('selectedGradeLevelName');
        const isFileUploading = sessionStorage.getItem('fileUploading') === 'true';
        
        // Vérifier que les données nécessaires sont présentes
        if (!previewData || !excelFileName || !classroomId) {
            showError('Données de prévisualisation manquantes. Veuillez revenir à l\'étape d\'importation.');
            return;
        }
        
        // Afficher les informations de base
        fileName.textContent = excelFileName;
        classroomName.textContent = classroomNameValue;
        gradeLevelName.textContent = gradeLevelNameValue;
        totalStudents.textContent = previewData.total_students || '0';
        
        // Afficher la section de chargement si l'importation est en cours
        if (isFileUploading) {
            importInProgress.classList.remove('d-none');
            importData();
        } else {
            // Sinon, afficher les données de prévisualisation
            displayPreviewData();
        }
        
        // Afficher les données de prévisualisation
        function displayPreviewData() {
            // Cacher la section de chargement et afficher le contenu principal
            importInProgress.classList.add('d-none');
            mainContent.classList.remove('d-none');
            
            // Calculer et afficher la moyenne générale
            if (previewData.students && previewData.students.length > 0) {
                const avgGrade = previewData.students
                    .map(student => parseFloat(student.moyenne) || 0)
                    .reduce((sum, grade) => sum + grade, 0) / previewData.students.length;
                
                averageGrade.textContent = avgGrade.toFixed(2);
            }
            
            // Afficher la liste des matières
            if (previewData.subjects && previewData.subjects.length > 0) {
                subjectsList.innerHTML = '';
                previewData.subjects.forEach(subject => {
                    const badge = document.createElement('span');
                    badge.classList.add('subject-badge');
                    badge.textContent = subject;
                    subjectsList.appendChild(badge);
                });
            }
            
            // Afficher les données des élèves
            if (previewData.students && previewData.students.length > 0) {
                studentsTableBody.innerHTML = '';
                previewData.students.forEach(student => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>${student.matricule}</td>
                        <td>${student.nom}</td>
                        <td>${student.prenom}</td>
                        <td>${student.sexe || '-'}</td>
                        <td class="fw-bold">${student.moyenne}</td>
                        <td>${student.rang}</td>
                        <td>${student.decision || '-'}</td>
                    `;
                    studentsTableBody.appendChild(row);
                });
            }
        }
        
        // Fonction pour importer les données
        function importData() {
            // Récupérer le fichier depuis la page d'importation
            const formData = new FormData();
            formData.append('classroom_id', classroomId);
            formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
            
            // Obtenir le fichier de sessionStorage ou FileReader
            if (window.uploadedExcelFile) {
                formData.append('file', window.uploadedExcelFile);
            }
            
            // Afficher la section de chargement
            importInProgress.classList.remove('d-none');
            mainContent.classList.add('d-none');
            
            // Envoyer la requête d'importation
            fetch('{{ route('semestre1.importation.import') }}', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                if (!response.ok) {
                    throw response;
                }
                return response.json();
            })
            .then(data => {
                // Supprimer l'indicateur d'importation en cours
                sessionStorage.removeItem('fileUploading');
                
                if (data.success) {
                    // Rediriger vers la page de validation
                    sessionStorage.setItem('importResult', JSON.stringify(data.data));
                    window.location.href = '{{ route('semestre1.importation.validation') }}';
                } else {
                    // Afficher l'erreur
                    showError(data.message || 'Erreur lors de l\'importation des données.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                
                // Supprimer l'indicateur d'importation en cours
                sessionStorage.removeItem('fileUploading');
                
                if (error.json) {
                    error.json().then(errorData => {
                        showError(errorData.message || 'Une erreur s\'est produite lors de l\'importation.');
                    });
                } else {
                    showError('Une erreur s\'est produite lors de l\'importation.');
                }
            });
        }
        
        // Fonction pour afficher une erreur
        function showError(message) {
            errorMessage.textContent = message;
            importInProgress.classList.add('d-none');
            mainContent.classList.add('d-none');
            errorSection.classList.remove('d-none');
        }
        
        // Événements des boutons
        importDataBtn.addEventListener('click', importData);
        
        prevStepBtn.addEventListener('click', function() {
            window.location.href = '{{ route('semestre1.importation.excel') }}';
        });
        
        backToImportBtn.addEventListener('click', function() {
            window.location.href = '{{ route('semestre1.importation.excel') }}';
        });
        
        returnToImportBtn.addEventListener('click', function() {
            window.location.href = '{{ route('semestre1.importation.excel') }}';
        });
    });
</script>
@endsection