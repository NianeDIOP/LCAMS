@extends('semestre1.layout')

@section('title', 'Importation Excel')

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
                                        <div class="progress-bar bg-primary" role="progressbar" style="width: 33%"></div>
                                    </div>
                                    
                                    <!-- Étape 1: Sélection classe/niveau -->
                                    <div class="progress-step completed">
                                        <button class="btn btn-success rounded-circle" style="width: 45px; height: 45px; font-size: 1.2rem; z-index: 1;" disabled>
                                            <i class="fas fa-check"></i>
                                        </button>
                                        <div class="mt-2">Sélection classe</div>
                                    </div>
                                    
                                    <!-- Étape 2: Importation fichier Excel -->
                                    <div class="progress-step active">
                                        <button class="btn btn-primary rounded-circle" style="width: 45px; height: 45px; font-size: 1.2rem; z-index: 1;" disabled>2</button>
                                        <div class="mt-2">Import Excel</div>
                                    </div>
                                    
                                    <!-- Étape 3: Visualisation des données -->
                                    <div class="progress-step">
                                        <button class="btn btn-outline-secondary rounded-circle" style="width: 45px; height: 45px; font-size: 1.2rem; z-index: 1;" disabled>3</button>
                                        <div class="mt-2">Visualisation</div>
                                    </div>
                                    
                                    <!-- Étape 4: Validation dans la base -->
                                    <div class="progress-step">
                                        <button class="btn btn-outline-secondary rounded-circle" style="width: 45px; height: 45px; font-size: 1.2rem; z-index: 1;" disabled>4</button>
                                        <div class="mt-2">Validation</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Contenu de l'étape 2: Importation fichier Excel -->
                    <div class="step-content">
                        <!-- Affichage de la classe sélectionnée -->
                        <div class="selected-class-info alert alert-info mb-4 d-flex align-items-center" role="alert">
                            <i class="fas fa-info-circle me-3" style="font-size: 1.5rem;"></i>
                            <div>
                                <h6 class="mb-0" id="selectedClassInfo">Chargement...</h6>
                                <small class="text-muted">Le fichier sera importé pour cette classe uniquement.</small>
                            </div>
                        </div>

                        <div class="card mb-4 import-section">
                            <div class="card-body">
                                <h5 class="card-title mb-4">Importation du fichier Excel</h5>
                                
                                <div class="import-wrapper">
                                    <!-- Zone de drop et sélection du fichier -->
                                    <div class="drop-zone mb-4" id="dropZone">
                                        <div class="drop-zone-content text-center py-5">
                                            <i class="fas fa-file-excel text-muted mb-3" style="font-size: 3rem;"></i>
                                            <h5>Glissez et déposez votre fichier Excel ici</h5>
                                            <p class="text-muted">ou</p>
                                            <label class="btn btn-outline-primary">
                                                Sélectionner un fichier
                                                <input type="file" id="fileInput" accept=".xlsx,.xls" hidden>
                                            </label>
                                            <p class="mt-3 text-muted small">Formats acceptés: .xlsx, .xls</p>
                                        </div>
                                    </div>
                                    
                                    <!-- Affichage du fichier sélectionné -->
                                    <div class="selected-file d-none" id="selectedFile">
                                        <div class="card bg-light">
                                            <div class="card-body">
                                                <div class="d-flex align-items-center">
                                                    <i class="fas fa-file-excel text-success me-3" style="font-size: 2rem;"></i>
                                                    <div class="flex-grow-1">
                                                        <h6 class="mb-1" id="fileName">nom-du-fichier.xlsx</h6>
                                                        <div class="d-flex align-items-center">
                                                            <span class="badge bg-success me-2" id="fileSize">0 KB</span>
                                                            <span class="text-muted small" id="uploadTime"></span>
                                                        </div>
                                                    </div>
                                                    <button type="button" class="btn btn-sm btn-outline-danger ms-3" id="removeFile">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Erreurs et infos -->
                                <div id="uploadError" class="alert alert-danger d-none mt-3">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    <span class="error-text"></span>
                                </div>
                                
                                <!-- Bouton de vérification et prévisualisation -->
                                <div class="mt-4 text-end">
                                    <button type="button" id="checkFile" class="btn btn-primary" disabled>
                                        <i class="fas fa-check me-2"></i>
                                        Vérifier et prévisualiser
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Section de prévisualisation (initialement cachée) -->
                        <div class="card mb-4 preview-section d-none">
                            <div class="card-body">
                                <h5 class="card-title d-flex justify-content-between align-items-center">
                                    <span>Aperçu du fichier</span>
                                    <button type="button" class="btn btn-sm btn-outline-secondary" id="changeFile">
                                        <i class="fas fa-exchange-alt me-1"></i>
                                        Changer de fichier
                                    </button>
                                </h5>
                                
                                <div class="row mt-4">
                                    <div class="col-md-6">
                                        <div class="card border-0 bg-light mb-3">
                                            <div class="card-body">
                                                <h6 class="card-subtitle text-muted mb-3">Informations générales</h6>
                                                <div class="d-flex justify-content-between mb-2">
                                                    <span>Nombre d'élèves</span>
                                                    <strong id="totalStudents">0</strong>
                                                </div>
                                                <div class="d-flex justify-content-between mb-2">
                                                    <span>Nombre de matières</span>
                                                    <strong id="totalSubjects">0</strong>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="card border-0 bg-light mb-3">
                                            <div class="card-body">
                                                <h6 class="card-subtitle text-muted mb-3">Structure détectée</h6>
                                                <div class="d-flex justify-content-between mb-2">
                                                    <span>Onglet "Moyennes eleves"</span>
                                                    <strong class="text-success"><i class="fas fa-check-circle"></i></strong>
                                                </div>
                                                <div class="d-flex justify-content-between">
                                                    <span>Onglet "Données détaillées"</span>
                                                    <strong class="text-success"><i class="fas fa-check-circle"></i></strong>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <hr>
                                
                                <h6 class="mb-3">Échantillon de données</h6>
                                <div class="table-responsive">
                                    <table class="table table-striped table-sm">
                                        <thead>
                                            <tr>
                                                <th>IEN</th>
                                                <th>Nom</th>
                                                <th>Prénom</th>
                                                <th>Moyenne</th>
                                                <th>Rang</th>
                                            </tr>
                                        </thead>
                                        <tbody id="previewTableBody">
                                            <!-- Les données seront insérées ici dynamiquement -->
                                        </tbody>
                                    </table>
                                </div>
                                
                                <div class="text-muted small mt-2">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Seul un échantillon des données est affiché. La page suivante montrera plus de détails.
                                </div>
                                
                                <div class="mt-4 text-end">
                                    <button type="button" id="continueToPreview" class="btn btn-success">
                                        Continuer à la visualisation
                                        <i class="fas fa-arrow-right ms-2"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Section de prévisualisation en cours (initialement cachée) -->
                        <div class="card mb-4 loading-section d-none">
                            <div class="card-body text-center py-5">
                                <div class="spinner-border text-primary mb-3" role="status" style="width: 3rem; height: 3rem;">
                                    <span class="visually-hidden">Chargement...</span>
                                </div>
                                <h5>Analyse du fichier en cours</h5>
                                <p class="text-muted">Cela peut prendre quelques instants...</p>
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-between">
                            <button type="button" id="prevStep" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-2"></i>
                                Retour à la sélection
                            </button>
                            
                            <button type="button" id="nextStep" class="btn btn-primary" disabled>
                                Continuer à la visualisation
                                <i class="fas fa-arrow-right ms-2"></i>
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
    
    .step-content {
        background-color: #f8f9fa;
        border-radius: 0.25rem;
        padding: 1rem;
    }
    
    .drop-zone {
        border: 2px dashed #ccc;
        border-radius: 5px;
        position: relative;
        cursor: pointer;
    }
    
    .drop-zone:hover {
        border-color: #0d6efd;
        background-color: rgba(13, 110, 253, 0.05);
    }
    
    .drop-zone.active {
        border-color: #198754;
        background-color: rgba(25, 135, 84, 0.05);
    }
    
    .drop-zone.error {
        border-color: #dc3545;
        background-color: rgba(220, 53, 69, 0.05);
    }
</style>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Variables pour les éléments du DOM
        const selectedClassInfo = document.getElementById('selectedClassInfo');
        const dropZone = document.getElementById('dropZone');
        const fileInput = document.getElementById('fileInput');
        const selectedFile = document.getElementById('selectedFile');
        const fileName = document.getElementById('fileName');
        const fileSize = document.getElementById('fileSize');
        const uploadTime = document.getElementById('uploadTime');
        const removeFile = document.getElementById('removeFile');
        const uploadError = document.getElementById('uploadError');
        const checkFileBtn = document.getElementById('checkFile');
        const prevStepBtn = document.getElementById('prevStep');
        const nextStepBtn = document.getElementById('nextStep');
        const importSection = document.querySelector('.import-section');
        const previewSection = document.querySelector('.preview-section');
        const loadingSection = document.querySelector('.loading-section');
        const changeFileBtn = document.getElementById('changeFile');
        const continueToPreviewBtn = document.getElementById('continueToPreview');
        const previewTableBody = document.getElementById('previewTableBody');
        const totalStudents = document.getElementById('totalStudents');
        const totalSubjects = document.getElementById('totalSubjects');
        
        let selectedFileData = null;
        const uploadedFile = { file: null };
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        // Récupérer les informations de classe depuis sessionStorage
        const classroomId = sessionStorage.getItem('selectedClassroomId');
        const classroomName = sessionStorage.getItem('selectedClassroomName');
        const gradeLevelName = sessionStorage.getItem('selectedGradeLevelName');
        
        if (!classroomId || !classroomName) {
            // Rediriger vers la page de sélection de classe si les informations ne sont pas disponibles
            window.location.href = '{{ route('semestre1.importation') }}';
        } else {
            // Afficher les informations de la classe sélectionnée
            selectedClassInfo.textContent = `Importation pour la classe ${classroomName} (${gradeLevelName})`;
        }
        
        // Navigation entre les étapes
        prevStepBtn.addEventListener('click', function() {
            window.location.href = '{{ route('semestre1.importation') }}';
        });
        
        // Gérer le drag & drop
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            dropZone.addEventListener(eventName, preventDefaults, false);
        });
        
        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }
        
        ['dragenter', 'dragover'].forEach(eventName => {
            dropZone.addEventListener(eventName, highlight, false);
        });
        
        ['dragleave', 'drop'].forEach(eventName => {
            dropZone.addEventListener(eventName, unhighlight, false);
        });
        
        function highlight() {
            dropZone.classList.add('active');
        }
        
        function unhighlight() {
            dropZone.classList.remove('active');
        }
        
        // Gestion du drop de fichier
        dropZone.addEventListener('drop', handleDrop, false);
        
        function handleDrop(e) {
            const dt = e.dataTransfer;
            const files = dt.files;
            
            if (files.length > 0) {
                handleFiles(files[0]);
            }
        }
        
        // Gestion du clic sur la zone de drop
        dropZone.addEventListener('click', function() {
            fileInput.click();
        });
        
        // Gestion de la sélection de fichier via l'input
        fileInput.addEventListener('change', function() {
            if (this.files.length > 0) {
                handleFiles(this.files[0]);
            }
        });
        
        function handleFiles(file) {
            // Vérifier le type de fichier
            const validTypes = ['application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'];
            const extension = file.name.split('.').pop().toLowerCase();
            
            if (!validTypes.includes(file.type) && !['xls', 'xlsx'].includes(extension)) {
                showError('Le fichier doit être au format Excel (.xls ou .xlsx).');
                return;
            }
            
            // Stocker le fichier et afficher les informations
            uploadedFile.file = file;
            fileName.textContent = file.name;
            fileSize.textContent = formatFileSize(file.size);
            uploadTime.textContent = 'Importé ' + formatDate(new Date());
            
            // Afficher la section du fichier sélectionné
            selectedFile.classList.remove('d-none');
            uploadError.classList.add('d-none');
            
            // Activer le bouton de vérification
            checkFileBtn.disabled = false;
        }
        
        // Supprimer le fichier
        removeFile.addEventListener('click', function() {
            uploadedFile.file = null;
            fileInput.value = '';
            selectedFile.classList.add('d-none');
            checkFileBtn.disabled = true;
        });
        
        // Fonctions utilitaires
        function formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        }
        
        function formatDate(date) {
            const now = new Date();
            if (date.toDateString() === now.toDateString()) {
                // Aujourd'hui
                return `aujourd'hui à ${date.getHours().toString().padStart(2, '0')}:${date.getMinutes().toString().padStart(2, '0')}`;
            } else {
                // Autre jour
                return `le ${date.getDate().toString().padStart(2, '0')}/${(date.getMonth() + 1).toString().padStart(2, '0')}/${date.getFullYear()}`;
            }
        }
        
        // Afficher une erreur
        function showError(message) {
            const errorText = uploadError.querySelector('.error-text');
            errorText.textContent = message;
            uploadError.classList.remove('d-none');
            dropZone.classList.add('error');
            
            setTimeout(() => {
                dropZone.classList.remove('error');
            }, 3000);
        }
        
        // Vérifier et prévisualiser le fichier
        checkFileBtn.addEventListener('click', function() {
            if (!uploadedFile.file) {
                showError('Veuillez sélectionner un fichier Excel.');
                return;
            }
            
            // Afficher l'écran de chargement
            importSection.classList.add('d-none');
            loadingSection.classList.remove('d-none');
            
            // Créer le FormData
            const formData = new FormData();
            formData.append('file', uploadedFile.file);
            formData.append('_token', csrfToken);
            
            // Envoyer la requête de prévisualisation
            fetch('{{ route('semestre1.importation.preview') }}', {
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
                if (data.success) {
                    // Stocker les données de prévisualisation
                    selectedFileData = data.data;
                    
                    // Afficher le nombre total d'élèves et de matières
                    totalStudents.textContent = data.data.total_students;
                    totalSubjects.textContent = data.data.subjects.length;
                    
                    // Générer la table de prévisualisation
                    previewTableBody.innerHTML = '';
                    data.data.students.forEach(student => {
                        const row = document.createElement('tr');
                        row.innerHTML = `
                            <td>${student.matricule}</td>
                            <td>${student.nom}</td>
                            <td>${student.prenom}</td>
                            <td>${student.moyenne}</td>
                            <td>${student.rang}</td>
                        `;
                        previewTableBody.appendChild(row);
                    });
                    
                    // Cacher le loader et afficher la prévisualisation
                    loadingSection.classList.add('d-none');
                    previewSection.classList.remove('d-none');
                    
                    // Activer le bouton suivant
                    nextStepBtn.disabled = false;
                } else {
                    // Afficher l'erreur
                    loadingSection.classList.add('d-none');
                    importSection.classList.remove('d-none');
                    showError(data.message || 'Une erreur s\'est produite lors de la prévisualisation.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                
                if (error.json) {
                    error.json().then(errorData => {
                        loadingSection.classList.add('d-none');
                        importSection.classList.remove('d-none');
                        showError(errorData.message || 'Une erreur s\'est produite lors de la prévisualisation.');
                    });
                } else {
                    loadingSection.classList.add('d-none');
                    importSection.classList.remove('d-none');
                    showError('Une erreur s\'est produite lors de la prévisualisation.');
                }
            });
        });
        
        // Changer de fichier
        changeFileBtn.addEventListener('click', function() {
            previewSection.classList.add('d-none');
            importSection.classList.remove('d-none');
        });
        
        // Continuer à la visualisation après prévisualisation
        continueToPreviewBtn.addEventListener('click', function() {
            // Stocker les données de prévisualisation dans sessionStorage pour la page suivante
            sessionStorage.setItem('previewData', JSON.stringify(selectedFileData));
            sessionStorage.setItem('excelFileName', uploadedFile.file.name);
            
            // Uploader le fichier sur le serveur
            uploadFile();
        });
        
        // Navigation vers l'étape suivante
        nextStepBtn.addEventListener('click', function() {
            if (selectedFileData) {
                // Stocker les données de prévisualisation dans sessionStorage pour la page suivante
                sessionStorage.setItem('previewData', JSON.stringify(selectedFileData));
                sessionStorage.setItem('excelFileName', uploadedFile.file.name);
                
                // Uploader le fichier sur le serveur
                uploadFile();
            }
        });

        // Fonction pour uploader le fichier vers le serveur et continuer à la page de visualisation
        function uploadFile() {
            // Afficher l'écran de chargement
            previewSection.classList.add('d-none');
            loadingSection.classList.remove('d-none');

            // Vérifier si on a un ID de fichier dans la réponse de prévisualisation
            if (selectedFileData && selectedFileData.file_id) {
                // Si on a déjà un ID de fichier, on peut rediriger directement
                redirectToVisualization(selectedFileData.file_id);
            } else if (selectedFileData && selectedFileData.session_id) {
                // Si on a un ID de session, on peut aussi rediriger avec cet ID
                redirectToVisualization(selectedFileData.session_id);
            } else {
                // Sinon, on doit d'abord uploader le fichier pour obtenir un ID
                const formData = new FormData();
                formData.append('file', uploadedFile.file);
                formData.append('classroom_id', classroomId);
                formData.append('_token', csrfToken);
                
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
                    if (data.success && data.file_id) {
                        // Rediriger vers la page de visualisation avec l'ID du fichier
                        redirectToVisualization(data.file_id);
                    } else {
                        throw new Error(data.message || 'Erreur lors du traitement du fichier.');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    
                    if (error.json) {
                        error.json().then(errorData => {
                            loadingSection.classList.add('d-none');
                            importSection.classList.remove('d-none');
                            showError(errorData.message || 'Une erreur s\'est produite lors du traitement du fichier.');
                        });
                    } else {
                        loadingSection.classList.add('d-none');
                        importSection.classList.remove('d-none');
                        showError('Une erreur s\'est produite lors du traitement du fichier.');
                    }
                });
            }
        }

        // Fonction pour rediriger vers la page de visualisation
        function redirectToVisualization(fileId) {
            window.location.href = '{{ route('semestre1.importation.visualize') }}?file_id=' + fileId + '&classroom_id=' + classroomId;
        }
    });
</script>
@endsection