@extends('semestre1.layout')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Importation des données - Semestre 1</h5>
                    <a href="{{ route('semestre1.index') }}" class="btn btn-sm btn-secondary">Retour</a>
                </div>

                <div class="card-body">
                    <!-- Barre de progression avec 4 onglets -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="progress-steps">
                                <div class="d-flex justify-content-between position-relative mb-4">
                                    <div class="progress" style="height: 2px; position: absolute; top: 21px; width: 100%; z-index: 0;">
                                        <div class="progress-bar bg-primary" role="progressbar" style="width: 0%"></div>
                                    </div>
                                    
                                    <!-- Étape 1: Sélection classe/niveau -->
                                    <div class="progress-step active">
                                        <button class="btn btn-primary rounded-circle" style="width: 45px; height: 45px; font-size: 1.2rem; z-index: 1;" disabled>1</button>
                                        <div class="mt-2">Sélection classe</div>
                                    </div>
                                    
                                    <!-- Étape 2: Importation fichier Excel -->
                                    <div class="progress-step">
                                        <button class="btn btn-outline-secondary rounded-circle" style="width: 45px; height: 45px; font-size: 1.2rem; z-index: 1;" disabled>2</button>
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

                    <!-- Contenu de l'onglet 1: Sélection classe/niveau -->
                    <div class="step-content">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title mb-4">Sélection du niveau et de la classe</h5>
                                
                                <form id="selectClassForm">
                                    <div class="row mb-4">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="gradeLevel" class="form-label">Niveau scolaire</label>
                                                <select class="form-select" id="gradeLevel" required>
                                                    <option value="" selected disabled>Sélectionner un niveau</option>
                                                    <!-- Les options seront chargées dynamiquement -->
                                                </select>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="classroom" class="form-label">Classe</label>
                                                <select class="form-select" id="classroom" required disabled>
                                                    <option value="" selected disabled>Sélectionner d'abord un niveau</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="d-flex justify-content-end">
                                        <button type="button" id="nextStep" class="btn btn-primary" disabled>
                                            Continuer vers l'importation
                                            <i class="fas fa-arrow-right ms-2"></i>
                                        </button>
                                    </div>
                                </form>
                            </div>
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
    
    .step-content {
        background-color: #f8f9fa;
        border-radius: 0.25rem;
        padding: 1rem;
    }
</style>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const baseUrl = '{{ url('/') }}';
        
        // Charger les niveaux scolaires au chargement de la page
        fetch(`${baseUrl}/api/grade-levels`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Erreur lors de la récupération des niveaux');
                }
                return response.json();
            })
            .then(data => {
                const gradeLevelSelect = document.getElementById('gradeLevel');
                if (data && data.length > 0) {
                    data.forEach(level => {
                        const option = document.createElement('option');
                        option.value = level.id;
                        option.textContent = level.name;
                        gradeLevelSelect.appendChild(option);
                    });
                } else {
                    console.warn('Aucun niveau scolaire trouvé');
                    const option = document.createElement('option');
                    option.textContent = 'Aucun niveau disponible';
                    option.disabled = true;
                    gradeLevelSelect.appendChild(option);
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                const gradeLevelSelect = document.getElementById('gradeLevel');
                const option = document.createElement('option');
                option.textContent = 'Erreur de chargement';
                option.disabled = true;
                gradeLevelSelect.appendChild(option);
            });
        
        // Événement de changement du niveau scolaire
        const gradeLevelSelect = document.getElementById('gradeLevel');
        const classroomSelect = document.getElementById('classroom');
        const nextStepButton = document.getElementById('nextStep');
        
        gradeLevelSelect.addEventListener('change', function() {
            const gradeId = this.value;
            
            // Réinitialiser la sélection de classe
            classroomSelect.innerHTML = '<option value="" selected disabled>Chargement des classes...</option>';
            classroomSelect.disabled = true;
            nextStepButton.disabled = true;
            
            // Charger les classes pour ce niveau
            fetch(`${baseUrl}/api/classrooms/${gradeId}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Erreur lors de la récupération des classes');
                    }
                    return response.json();
                })
                .then(data => {
                    classroomSelect.innerHTML = '<option value="" selected disabled>Sélectionner une classe</option>';
                    
                    if (data && data.length > 0) {
                        data.forEach(classroom => {
                            const option = document.createElement('option');
                            option.value = classroom.id;
                            option.textContent = classroom.name;
                            classroomSelect.appendChild(option);
                        });
                        classroomSelect.disabled = false;
                    } else {
                        const option = document.createElement('option');
                        option.textContent = 'Aucune classe disponible';
                        option.disabled = true;
                        classroomSelect.appendChild(option);
                    }
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    classroomSelect.innerHTML = '<option value="" disabled>Erreur de chargement</option>';
                });
        });
        
        // Activer le bouton suivant quand une classe est sélectionnée
        classroomSelect.addEventListener('change', function() {
            nextStepButton.disabled = this.value === '';
        });
        
        // Navigation vers l'étape d'importation Excel
        nextStepButton.addEventListener('click', function() {
            // Stocker l'ID et le nom de la classe sélectionnée dans sessionStorage
            const classroomId = classroomSelect.value;
            const classroomName = classroomSelect.options[classroomSelect.selectedIndex].text;
            
            sessionStorage.setItem('selectedClassroomId', classroomId);
            sessionStorage.setItem('selectedClassroomName', classroomName);
            sessionStorage.setItem('selectedGradeLevelId', gradeLevelSelect.value);
            sessionStorage.setItem('selectedGradeLevelName', gradeLevelSelect.options[gradeLevelSelect.selectedIndex].text);
            
            window.location.href = '{{ route('semestre1.importation.excel') }}';
        });
    });
</script>
@endsection