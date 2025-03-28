<!-- resources/views/semestre1/base.blade.php -->
@extends('layouts.module')

@section('title', 'Base des moyennes - Semestre 1')

@section('module-icon')
<i class="fas fa-calendar-alt me-2"></i>
@endsection

@section('module-title', 'Semestre 1')

@section('page-title', 'Base des moyennes')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('semestre1.index') }}">Semestre 1</a></li>
<li class="breadcrumb-item active">Base des moyennes</li>
@endsection

@section('sidebar-menu')
<li>
    <a href="{{ route('semestre1.index') }}" class="nav-link ps-3 py-2 {{ request()->routeIs('semestre1.index') ? 'active bg-light fw-bold' : '' }}">
        <i class="fas fa-home me-2"></i> Vue d'ensemble
    </a>
</li>
<li>
    <a href="{{ route('semestre1.dashboard') }}" class="nav-link ps-3 py-2 {{ request()->routeIs('semestre1.dashboard') ? 'active bg-light fw-bold' : '' }}">
        <i class="fas fa-tachometer-alt me-2"></i> Tableau de bord
    </a>
</li>
<li>
    <a href="{{ route('semestre1.analyse') }}" class="nav-link ps-3 py-2 {{ request()->routeIs('semestre1.analyse') ? 'active bg-light fw-bold' : '' }}">
        <i class="fas fa-chart-line me-2"></i> Analyse des disciplines
    </a>
</li>
<li>
    <a href="{{ route('semestre1.rapports') }}" class="nav-link ps-3 py-2 {{ request()->routeIs('semestre1.rapports') ? 'active bg-light fw-bold' : '' }}">
        <i class="fas fa-file-alt me-2"></i> Génération des rapports
    </a>
</li>
<li>
    <a href="{{ route('semestre1.base') }}" class="nav-link ps-3 py-2 {{ request()->routeIs('semestre1.base') ? 'active bg-light fw-bold' : '' }}">
        <i class="fas fa-database me-2"></i> Base des moyennes
    </a>
</li>
@endsection

@section('styles')
<style>
    .import-card {
        background-color: white;
        border-radius: 0.5rem;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        margin-bottom: 1.5rem;
    }
    
    .file-list {
        background-color: white;
        border-radius: 0.5rem;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    }
    
    .card-header {
        padding: 1rem 1.25rem;
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        font-weight: 600;
    }
    
    .file-icon {
        font-size: 1.5rem;
        color: #0062cc;
        margin-right: 0.75rem;
    }
    
    .file-table {
        font-size: 0.9rem;
    }
    
    .file-table th {
        font-weight: 600;
        padding: 0.75rem 1rem;
    }
    
    .file-table td {
        padding: 0.75rem 1rem;
        vertical-align: middle;
    }
    
    .file-info {
        display: flex;
        align-items: center;
    }
    
    .file-actions .btn {
        padding: 0.25rem 0.5rem;
        font-size: 0.8rem;
    }
    
    .upload-zone {
        border: 2px dashed #dee2e6;
        border-radius: 0.5rem;
        padding: 2rem;
        text-align: center;
        cursor: pointer;
        transition: all 0.2s;
    }
    
    .upload-zone:hover {
        border-color: #0062cc;
        background-color: rgba(0, 98, 204, 0.03);
    }
    
    .upload-icon {
        font-size: 2.5rem;
        color: #6c757d;
        margin-bottom: 1rem;
    }
    
    .upload-title {
        font-size: 1.1rem;
        font-weight: 600;
        margin-bottom: 0.5rem;
    }
    
    .no-files-message {
        text-align: center;
        padding: 2rem;
        color: #6c757d;
    }
    
    .file-badge {
        font-size: 0.75rem;
        padding: 0.25rem 0.5rem;
        border-radius: 0.375rem;
    }
</style>
@endsection

@section('module-content')
    <div class="row">
        <div class="col-lg-12">
            <!-- Section d'importation -->
            <div class="import-card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <i class="fas fa-file-import me-2"></i> Importation de fichiers Excel
                    </div>
                </div>
                <div class="card-body p-4">
                <form action="{{ route('semestre1.importer') }}" method="POST" enctype="multipart/form-data" id="import-form">
                    @csrf
                    
                    <div class="row mb-4">
                        <div class="col-md-8">
                            <label for="excel_file" class="form-label">Fichier Excel PLANETE</label>
                            <div class="upload-zone" id="upload-zone" onclick="document.getElementById('excel_file').click();">
                                <input type="file" id="excel_file" name="excel_file" class="d-none" accept=".xlsx,.xls" onchange="updateFileName()">
                                <div class="upload-icon">
                                    <i class="fas fa-file-excel"></i>
                                </div>
                                <h4 class="upload-title">Déposer votre fichier ici</h4>
                                <p class="text-muted mb-0">ou cliquez pour choisir un fichier</p>
                                <div id="selected-file" class="mt-3 d-none">
                                    <span class="badge bg-primary" id="file-name"></span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="type_fichier" class="form-label">Type de fichier</label>
                                <select class="form-select" id="type_fichier" name="type_fichier">
                                    <option value="statistiques">Statistiques</option>
                                    <option value="moyennes">Moyennes</option>
                                    <option value="evaluations">Évaluations</option>
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label for="niveau_id" class="form-label">Niveau *</label>
                                <select class="form-select" id="niveau_id" name="niveau_id" required>
                                    <option value="">Sélectionnez un niveau</option>
                                    @foreach($niveaux as $niveau)
                                        <option value="{{ $niveau->id }}">{{ $niveau->nom }} ({{ $niveau->cycle }})</option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label for="classe_id" class="form-label">Classe *</label>
                                <select class="form-select" id="classe_id" name="classe_id" required disabled>
                                    <option value="">Sélectionnez d'abord un niveau</option>
                                </select>
                            </div>
                            
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary" id="import-btn" disabled>
                                    <i class="fas fa-upload me-2"></i> Importer le fichier
                                </button>
                            </div>
                            
                            <div class="mt-3">
                                <div class="alert alert-info p-2 small">
                                    <i class="fas fa-info-circle me-1"></i> Les données seront associées à la classe sélectionnée.
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
                </div>
            </div>
            
            <!-- Liste des fichiers importés -->
            <div class="file-list">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <i class="fas fa-database me-2"></i> Fichiers importés
                    </div>
                </div>
                <div class="card-body p-0">
                    @if(isset($importedFiles) && count($importedFiles) > 0)
                        <div class="table-responsive">
                        <table class="table table-hover mb-0 file-table">
                            <thead class="table-light">
                                <tr>
                                    <th>Fichier</th>
                                    <th>Type</th>
                                    <th>Niveau</th>
                                    <th>Classe</th>
                                    <th>Lignes</th>
                                    <th>Date d'importation</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($importedFiles as $file)
                                <tr>
                                    <td>
                                        <div class="file-info">
                                            <i class="fas fa-file-excel file-icon"></i>
                                            <div>
                                                <div>{{ $file->nom_fichier }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @if($file->type == 'statistiques')
                                            <span class="badge bg-primary file-badge">Statistiques</span>
                                        @elseif($file->type == 'moyennes')
                                            <span class="badge bg-success file-badge">Moyennes</span>
                                        @elseif($file->type == 'evaluations')
                                            <span class="badge bg-info file-badge">Évaluations</span>
                                        @else
                                            <span class="badge bg-secondary file-badge">{{ $file->type }}</span>
                                        @endif
                                    </td>
                                    <td>{{ $file->niveau_nom ?? 'Non spécifié' }}</td>
                                    <td>{{ $file->classe_nom ?? 'Non spécifié' }}</td>
                                    <td>{{ $file->nombre_lignes }}</td>
                                    <td>{{ \Carbon\Carbon::parse($file->created_at)->format('d/m/Y H:i') }}</td>
                                    <td class="text-end file-actions">
                                        <a href="{{ route('semestre1.viewImportedFile', $file->id) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-outline-danger delete-file" data-id="{{ $file->id }}" data-name="{{ $file->nom_fichier }}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        </div>
                    @else
                        <div class="no-files-message">
                            <div class="mb-3">
                                <i class="fas fa-inbox fa-3x text-muted"></i>
                            </div>
                            <h5>Aucun fichier importé</h5>
                            <p class="text-muted">Importez des fichiers Excel pour commencer l'analyse</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    
    <!-- Modal de confirmation de suppression -->
    <div class="modal fade" id="deleteFileModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirmer la suppression</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <div class="mb-3 text-danger">
                        <i class="fas fa-exclamation-triangle fa-3x"></i>
                    </div>
                    <p>Êtes-vous sûr de vouloir supprimer le fichier <strong id="delete-file-name"></strong> ?</p>
                    <p class="text-danger small">Cette action ne peut pas être annulée.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <form id="deleteFileForm" action="" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger">Supprimer</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    // Gestion de l'upload de fichier
    function updateFileName() {
        const input = document.getElementById('excel_file');
        const fileNameElement = document.getElementById('file-name');
        const selectedFileSection = document.getElementById('selected-file');
        const importBtn = document.getElementById('import-btn');
        
        if (input.files.length > 0) {
            const fileName = input.files[0].name;
            fileNameElement.textContent = fileName;
            selectedFileSection.classList.remove('d-none');
            updateImportButtonState();
        } else {
            selectedFileSection.classList.add('d-none');
            updateImportButtonState();
        }
    }
    
    // Mise à jour de l'état du bouton d'importation
    function updateImportButtonState() {
        const fileInput = document.getElementById('excel_file');
        const niveauSelect = document.getElementById('niveau_id');
        const classeSelect = document.getElementById('classe_id');
        const importBtn = document.getElementById('import-btn');
        
        // Le bouton est actif si un fichier est sélectionné ET un niveau ET une classe sont sélectionnés
        importBtn.disabled = !(fileInput.files.length > 0 && 
                               niveauSelect.value !== '' && 
                               classeSelect.value !== '');
    }
    
    // Gestion du drag & drop
    document.addEventListener('DOMContentLoaded', function() {
        const dropZone = document.getElementById('upload-zone');
        const niveauSelect = document.getElementById('niveau_id');
        const classeSelect = document.getElementById('classe_id');
        
        // Gestion du drag & drop pour l'upload
        dropZone.addEventListener('dragover', function(e) {
            e.preventDefault();
            dropZone.classList.add('border-primary');
        });
        
        dropZone.addEventListener('dragleave', function(e) {
            e.preventDefault();
            dropZone.classList.remove('border-primary');
        });
        
        dropZone.addEventListener('drop', function(e) {
            e.preventDefault();
            dropZone.classList.remove('border-primary');
            
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                const fileInput = document.getElementById('excel_file');
                fileInput.files = files;
                updateFileName();
            }
        });
        
        // Gestion des modals de suppression
        const deleteButtons = document.querySelectorAll('.delete-file');
        const deleteFileForm = document.getElementById('deleteFileForm');
        const deleteFileName = document.getElementById('delete-file-name');
        
        deleteButtons.forEach(button => {
            button.addEventListener('click', function() {
                const fileId = this.getAttribute('data-id');
                const fileName = this.getAttribute('data-name');
                
                deleteFileName.textContent = fileName;
                deleteFileForm.action = `/semestre1/base/delete/${fileId}`;
                
                const modal = new bootstrap.Modal(document.getElementById('deleteFileModal'));
                modal.show();
            });
        });
        
        // Gestion du changement de niveau pour charger les classes associées
        niveauSelect.addEventListener('change', function() {
            const niveauId = this.value;
            
            // Réinitialiser et désactiver le sélecteur de classe si aucun niveau n'est sélectionné
            if (!niveauId) {
                classeSelect.innerHTML = '<option value="">Sélectionnez d\'abord un niveau</option>';
                classeSelect.disabled = true;
                updateImportButtonState();
                return;
            }
            
            // Récupérer les classes du niveau sélectionné via AJAX
            fetch(`/semestre1/classes-by-niveau/${niveauId}`)
                .then(response => response.json())
                .then(data => {
                    // Effacer les options précédentes
                    classeSelect.innerHTML = '';
                    
                    // Ajouter l'option par défaut
                    const defaultOption = document.createElement('option');
                    defaultOption.value = '';
                    defaultOption.textContent = 'Sélectionnez une classe';
                    classeSelect.appendChild(defaultOption);
                    
                    // Ajouter les classes disponibles
                    if (data.length > 0) {
                        data.forEach(classe => {
                            const option = document.createElement('option');
                            option.value = classe.id;
                            option.textContent = classe.nom;
                            classeSelect.appendChild(option);
                        });
                        
                        // Activer le sélecteur de classe
                        classeSelect.disabled = false;
                    } else {
                        // Si aucune classe n'est disponible
                        const noClassOption = document.createElement('option');
                        noClassOption.value = '';
                        noClassOption.textContent = 'Aucune classe disponible pour ce niveau';
                        classeSelect.appendChild(noClassOption);
                        classeSelect.disabled = true;
                    }
                    
                    updateImportButtonState();
                })
                .catch(error => {
                    console.error('Erreur lors de la récupération des classes:', error);
                    classeSelect.innerHTML = '<option value="">Erreur de chargement</option>';
                    classeSelect.disabled = true;
                    updateImportButtonState();
                });
        });
        
        // Mettre à jour l'état du bouton d'importation lors du changement de classe
        classeSelect.addEventListener('change', function() {
            updateImportButtonState();
        });
    });
</script>
@endsection