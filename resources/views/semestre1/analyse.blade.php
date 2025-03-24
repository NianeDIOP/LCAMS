@extends('layouts.sidebar')

@section('title', 'Analyse des disciplines - Semestre 1')

@section('sidebar-title')
    <i class="fas fa-calendar-alt me-2"></i> Semestre 1
@endsection

@section('sidebar-menu')
    <li>
        <a href="{{ route('semestre1.dashboard') }}" class="{{ request()->routeIs('semestre1.dashboard') ? 'active' : '' }}">
            <span class="icon"><i class="fas fa-tachometer-alt"></i></span> Tableau de bord
        </a>
    </li>
    <li>
        <a href="{{ route('semestre1.analyse') }}" class="{{ request()->routeIs('semestre1.analyse') ? 'active' : '' }}">
            <span class="icon"><i class="fas fa-chart-line"></i></span> Analyse des disciplines
        </a>
    </li>
    <li>
        <a href="{{ route('semestre1.rapports') }}" class="{{ request()->routeIs('semestre1.rapports') ? 'active' : '' }}">
            <span class="icon"><i class="fas fa-file-alt"></i></span> Génération des rapports
        </a>
    </li>
    <li>
        <a href="{{ route('semestre1.base') }}" class="{{ request()->routeIs('semestre1.base') ? 'active' : '' }}">
            <span class="icon"><i class="fas fa-database"></i></span> Base des moyennes
        </a>
    </li>
@endsection

@section('content-title')
    Analyse des disciplines - Semestre 1
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('semestre1.index') }}">Semestre 1</a></li>
    <li class="breadcrumb-item active">Analyse des disciplines</li>
@endsection

@section('main-content')
    <!-- Filtres de recherche -->
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="fas fa-filter me-2"></i> Filtres</h5>
        </div>
        <div class="card-body">
            <form action="" method="GET">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="niveau" class="form-label">Niveau</label>
                        <select class="form-select" id="niveau" name="niveau">
                            <option value="">Tous les niveaux</option>
                            <option value="6eme">6ème</option>
                            <option value="5eme">5ème</option>
                            <option value="4eme">4ème</option>
                            <option value="3eme">3ème</option>
                            <option value="2nde">2nde</option>
                            <option value="1ere">1ère</option>
                            <option value="Tle">Terminale</option>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="classe" class="form-label">Classe</label>
                        <select class="form-select" id="classe" name="classe" disabled>
                            <option value="">Sélectionnez d'abord un niveau</option>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="discipline" class="form-label">Discipline</label>
                        <select class="form-select" id="discipline" name="discipline">
                            <option value="">Toutes les disciplines</option>
                            <option value="MATH">Mathématiques</option>
                            <option value="FR">Français</option>
                            <option value="ANG">Anglais</option>
                            <option value="HG">Histoire-Géographie</option>
                            <option value="SVT">SVT</option>
                            <option value="PC">Physique-Chimie</option>
                            <option value="EPS">EPS</option>
                        </select>
                    </div>
                </div>
                <div class="text-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search me-2"></i> Filtrer
                    </button>
                    <a href="{{ route('semestre1.analyse') }}" class="btn btn-secondary">
                        <i class="fas fa-redo me-2"></i> Réinitialiser
                    </a>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Message d'alerte si aucune donnée n'est disponible -->
    <div class="alert alert-info mb-4">
        <i class="fas fa-info-circle me-2"></i> Importez des données dans la section "Base des moyennes" pour visualiser l'analyse des disciplines du Semestre 1.
    </div>
    
    <!-- Graphique de performance par discipline -->
    <div class="card mb-4">
        <div class="card-header bg-success text-white">
            <h5 class="mb-0"><i class="fas fa-chart-bar me-2"></i> Performance par discipline</h5>
        </div>
        <div class="card-body">
            <div class="alert alert-light">
                Aucune donnée disponible pour le moment.
            </div>
            <!-- Un graphique sera affiché ici lorsque des données seront disponibles -->
        </div>
    </div>
    
    <!-- Tableau détaillé -->
    <div class="card">
        <div class="card-header bg-info text-white">
            <h5 class="mb-0"><i class="fas fa-table me-2"></i> Tableau détaillé des résultats</h5>
        </div>
        <div class="card-body">
            <div class="alert alert-light">
                Aucune donnée disponible pour le moment.
            </div>
            <!-- Un tableau sera affiché ici lorsque des données seront disponibles -->
        </div>
    </div>
@endsection

@section('scripts')
<script>
    // Script pour gérer la dépendance entre niveau et classe
    document.addEventListener('DOMContentLoaded', function() {
        const niveauSelect = document.getElementById('niveau');
        const classeSelect = document.getElementById('classe');
        
        niveauSelect.addEventListener('change', function() {
            // Réinitialiser la liste des classes
            classeSelect.innerHTML = '';
            
            // Désactiver le sélecteur de classe si aucun niveau n'est sélectionné
            if (!this.value) {
                classeSelect.disabled = true;
                classeSelect.innerHTML = '<option value="">Sélectionnez d'abord un niveau</option>';
                return;
            }
            
            // Activer le sélecteur de classe
            classeSelect.disabled = false;
            
            // Ajouter l'option "Toutes les classes"
            const defaultOption = document.createElement('option');
            defaultOption.value = '';
            defaultOption.textContent = 'Toutes les classes';
            classeSelect.appendChild(defaultOption);
            
            // Ajouter des classes en fonction du niveau sélectionné
            let classes = [];
            switch(this.value) {
                case '6eme':
                    classes = ['6ème A', '6ème B', '6ème C', '6ème D'];
                    break;
                case '5eme':
                    classes = ['5ème A', '5ème B', '5ème C', '5ème D'];
                    break;
                case '4eme':
                    classes = ['4ème A', '4ème B', '4ème C', '4ème D'];
                    break;
                case '3eme':
                    classes = ['3ème A', '3ème B', '3ème C', '3ème D'];
                    break;
                case '2nde':
                    classes = ['2nde A', '2nde B', '2nde C', '2nde S1', '2nde S2'];
                    break;
                case '1ere':
                    classes = ['1ère L1', '1ère L2', '1ère S1', '1ère S2'];
                    break;
                case 'Tle':
                    classes = ['Tle L1', 'Tle L2', 'Tle S1', 'Tle S2'];
                    break;
            }
            
            // Ajouter les options de classe
            classes.forEach(function(classe) {
                const option = document.createElement('option');
                option.value = classe;
                option.textContent = classe;
                classeSelect.appendChild(option);
            });
        });
    });
</script>
@endsection