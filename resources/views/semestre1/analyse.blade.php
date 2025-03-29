@extends('layouts.app')

@section('title', 'Analyse des disciplines - Semestre 1')

@section('styles')
<style>
    /* Styles pour le dashboard d'analyse */
    .dashboard-container {
        background-color: #f5f8fa;
        padding: 1rem;
        border-radius: 0.5rem;
    }
    
    .sidebar {
        background-color: white;
        border-radius: 0.5rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        height: 100%;
    }
    
    .sidebar-title {
        padding: 1rem;
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        font-weight: 600;
        font-size: 1.1rem;
        display: flex;
        align-items: center;
    }
    
    .sidebar-title i {
        margin-right: 0.5rem;
        color: #0062cc;
    }
    
    .sidebar-menu {
        list-style: none;
        padding: 0;
        margin: 0;
    }
    
    .sidebar-menu li a {
        display: flex;
        align-items: center;
        padding: 0.875rem 1rem;
        color: #495057;
        text-decoration: none;
        transition: all 0.2s;
        border-left: 3px solid transparent;
    }
    
    .sidebar-menu li a:hover {
        background-color: rgba(0, 98, 204, 0.05);
        color: #0062cc;
    }
    
    .sidebar-menu li a.active {
        background-color: rgba(0, 98, 204, 0.1);
        color: #0062cc;
        border-left: 3px solid #0062cc;
        font-weight: 600;
    }
    
    .sidebar-menu li a i {
        margin-right: 0.5rem;
        width: 20px;
        text-align: center;
    }
    
    .filter-container {
        background-color: white;
        border-radius: 0.5rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        margin-bottom: 1.5rem;
        padding: 1rem;
    }
    
    .filter-title {
        font-size: 1rem;
        font-weight: 600;
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
    }
    
    .filter-title i {
        margin-right: 0.5rem;
        color: #0062cc;
    }
    
    .form-label {
        font-weight: 500;
        font-size: 0.85rem;
        margin-bottom: 0.375rem;
    }
    
    .form-select, .form-control {
        font-size: 0.875rem;
    }
    
    .filter-container .btn {
        padding-top: 0.5rem;
        padding-bottom: 0.5rem;
        font-size: 0.875rem;
    }
    
    .discipline-list {
        max-height: 400px;
        overflow-y: auto;
    }
    
    .discipline-item {
        display: block;
        padding: 0.75rem 1rem;
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        color: #495057;
        text-decoration: none;
        transition: all 0.2s;
    }
    
    .discipline-item:hover {
        background-color: rgba(0, 98, 204, 0.05);
    }
    
    .discipline-item.active {
        background-color: rgba(0, 98, 204, 0.1);
        color: #0062cc;
        font-weight: 600;
    }
    
    .stat-card {
        background-color: white;
        border-radius: 0.5rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        height: 100%;
        transition: transform 0.2s, box-shadow 0.2s;
    }
    
    .stat-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }
    
    .stat-card-header {
        padding: 0.75rem 1rem;
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        font-weight: 600;
        font-size: 0.9rem;
        color: #495057;
        background-color: rgba(0, 0, 0, 0.02);
    }
    
    .stat-card-body {
        padding: 1rem;
        text-align: center;
    }
    
    .stat-card-value {
        font-size: 1.75rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
        line-height: 1.2;
    }
    
    .stat-card-label {
        font-size: 0.85rem;
        color: #6c757d;
        margin-bottom: 0.5rem;
    }
    
    .chart-container {
        position: relative;
        height: 280px;
        width: 100%;
    }
    
    .comparison-container {
        display: flex;
        justify-content: space-between;
        margin-top: 1rem;
    }
    
    .comparison-item {
        text-align: center;
        padding: 0.5rem;
        border-radius: 0.375rem;
        background-color: #f8f9fa;
        flex: 1;
        margin: 0 0.25rem;
    }
    
    .comparison-value {
        font-size: 1.25rem;
        font-weight: 600;
        margin-bottom: 0.25rem;
    }
    
    .comparison-label {
        font-size: 0.75rem;
        color: #6c757d;
    }
    
    .comparison-icon {
        font-size: 1.5rem;
        margin-bottom: 0.5rem;
    }
    
    .male-color {
        color: #0062cc;
    }
    
    .female-color {
        color: #e83e8c;
    }
    
    .progress {
        height: 8px;
        border-radius: 4px;
        background-color: #e9ecef;
        margin-bottom: 0.5rem;
    }
    
    .progress-bar {
        border-radius: 4px;
    }
    
    .table-container {
        background-color: white;
        border-radius: 0.5rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        overflow: hidden;
    }
    
    .stats-table {
        width: 100%;
        margin-bottom: 0;
        font-size: 0.875rem;
    }
    
    .stats-table th {
        background-color: rgba(0, 0, 0, 0.02);
        font-weight: 600;
        color: #495057;
        padding: 0.75rem 1rem;
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
    }
    
    .stats-table td {
        padding: 0.75rem 1rem;
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        vertical-align: middle;
    }
    
    .stats-table tr:last-child td {
        border-bottom: none;
    }
    
    .badge {
        font-weight: 500;
        padding: 0.35em 0.65em;
        font-size: 0.75em;
        border-radius: 0.25rem;
    }
    
    .section-title {
        margin-bottom: 1rem;
        font-size: 1.1rem;
        font-weight: 600;
        color: #343a40;
        display: flex;
        align-items: center;
    }
    
    .section-title i {
        margin-right: 0.5rem;
        color: #0062cc;
    }
    
    .no-data-message {
        padding: 2rem;
        text-align: center;
        background-color: white;
        border-radius: 0.5rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }
    
    .no-data-icon {
        font-size: 3rem;
        color: #6c757d;
        margin-bottom: 1rem;
        opacity: 0.5;
    }
</style>
@endsection

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <!-- Barre latérale -->
        <div class="col-lg-2">
            <div class="sidebar">
                <div class="sidebar-title">
                    <i class="fas fa-calendar-alt"></i> Semestre 1
                </div>
                <ul class="sidebar-menu">
                    <li>
                        <a href="{{ route('semestre1.index') }}" class="{{ request()->routeIs('semestre1.index') ? 'active' : '' }}">
                            <i class="fas fa-home"></i> Vue d'ensemble
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('semestre1.dashboard') }}" class="{{ request()->routeIs('semestre1.dashboard') ? 'active' : '' }}">
                            <i class="fas fa-tachometer-alt"></i> Tableau de bord
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('semestre1.analyse') }}" class="{{ request()->routeIs('semestre1.analyse') ? 'active' : '' }}">
                            <i class="fas fa-chart-line"></i> Analyse
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('semestre1.rapports') }}" class="{{ request()->routeIs('semestre1.rapports') ? 'active' : '' }}">
                            <i class="fas fa-file-alt"></i> Rapports
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('semestre1.base') }}" class="{{ request()->routeIs('semestre1.base') ? 'active' : '' }}">
                            <i class="fas fa-database"></i> Base de données
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        
        <!-- Contenu principal -->
        <div class="col-lg-10">
            <div class="dashboard-container">
                <h1 class="mb-4 fs-4">Analyse des disciplines - Semestre 1</h1>
                
                <!-- Filtres -->
                <div class="filter-container">
                    <h5 class="filter-title">
                        <i class="fas fa-filter"></i> Filtres
                    </h5>
                    
                    <form id="filterForm" action="{{ route('semestre1.analyse') }}" method="GET">
                        <div class="row align-items-end">
                            <div class="col-md-2 mb-3">
                                <label for="niveau_id" class="form-label">Niveau</label>
                                <select class="form-select" id="niveau_id" name="niveau_id">
                                    <option value="">Tous les niveaux</option>
                                    @foreach($niveaux ?? [] as $n)
                                        <option value="{{ $n->id }}" {{ ($niveau_id ?? '') == $n->id ? 'selected' : '' }}>{{ $n->nom }}</option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div class="col-md-2 mb-3">
                                <label for="classe_id" class="form-label">Classe</label>
                                <select class="form-select" id="classe_id" name="classe_id">
                                    <option value="">Toutes les classes</option>
                                    @foreach($classes ?? [] as $c)
                                        <option value="{{ $c->id }}" {{ ($classe_id ?? '') == $c->id ? 'selected' : '' }}>{{ $c->nom }}</option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div class="col-md-2 mb-3">
                                <label for="sexe" class="form-label">Sexe</label>
                                <select class="form-select" id="sexe" name="sexe">
                                    <option value="">Tous</option>
                                    <option value="F" {{ ($sexe ?? '') == 'F' ? 'selected' : '' }}>Filles</option>
                                    <option value="H" {{ ($sexe ?? '') == 'H' ? 'selected' : '' }}>Garçons</option>
                                </select>
                            </div>
                            
                            <div class="col-md-2 mb-3">
                                <label for="min_moyenne" class="form-label">Moyenne min</label>
                                <input type="number" class="form-control" id="min_moyenne" name="min_moyenne" step="0.01" min="0" max="20" value="{{ $min_moyenne ?? '' }}">
                            </div>
                            
                            <div class="col-md-2 mb-3">
                                <label for="max_moyenne" class="form-label">Moyenne max</label>
                                <input type="number" class="form-control" id="max_moyenne" name="max_moyenne" step="0.01" min="0" max="20" value="{{ $max_moyenne ?? '' }}">
                            </div>
                            
                            <div class="col-md-2 mb-3 d-flex align-items-center">
                                <button type="submit" class="btn btn-primary h-100 me-1" style="width: 68%;">
                                    Appliquer
                                </button>
                                <a href="{{ route('semestre1.analyse') }}" class="btn btn-outline-secondary h-100" style="width: 30%;">
                                    <i class="fas fa-redo"></i>
                                </a>
                            </div>
                        </div>
                        
                        @if(isset($discipline) && $discipline)
                            <input type="hidden" name="discipline" value="{{ $discipline }}">
                        @endif
                    </form>
                </div>
                
                <div class="row">
                    <!-- Liste des disciplines -->
                    <div class="col-md-3 mb-4">
                        <div class="stat-card h-100">
                            <div class="stat-card-header">
                                <i class="fas fa-book me-2"></i> Disciplines
                            </div>
                            <div class="stat-card-body p-0">
                                <div class="discipline-list">
                                    @if(isset($disciplines) && count($disciplines) > 0)
                                        @foreach($disciplines as $code => $name)
                                            <a href="{{ route('semestre1.analyse', ['discipline' => $code] + request()->except('discipline')) }}" 
                                              class="discipline-item {{ (isset($discipline) && $discipline == $code) ? 'active' : '' }}">
                                                {{ $name }}
                                            </a>
                                        @endforeach
                                    @else
                                        <div class="p-3 text-center text-muted">
                                            Aucune discipline trouvée. Veuillez importer des fichiers de statistiques.
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Contenu d'analyse -->
                    <div class="col-md-9">
                        @if(isset($discipline) && isset($disciplineDetails) && $discipline && $disciplineDetails)
                            <!-- Statistiques générales de la discipline -->
                            <div class="row mb-4">
                                <div class="col-md-4 mb-3">
                                    <div class="stat-card">
                                        <div class="stat-card-header">
                                            Moyenne générale
                                        </div>
                                        <div class="stat-card-body">
                                            <div class="stat-card-value text-primary">{{ number_format($disciplineDetails['moyenne_generale'], 2) }}</div>
                                            <div class="stat-card-label">{{ $disciplineDetails['nom'] }}</div>
                                            
                                            @if(isset($globalStats) && isset($globalStats->moyenne_generale))
                                                <div class="mt-2 pt-2 border-top">
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <small class="text-muted">Moyenne globale</small>
                                                        <strong>{{ number_format($globalStats->moyenne_generale, 2) }}</strong>
                                                    </div>
                                                    <div class="progress mt-1">
                                                        @php
                                                            $ratio = min(100, ($disciplineDetails['moyenne_generale'] / max(0.01, $globalStats->moyenne_generale)) * 100);
                                                            $color = $ratio >= 100 ? 'bg-success' : ($ratio >= 90 ? 'bg-info' : 'bg-warning');
                                                        @endphp
                                                        <div class="progress-bar {{ $color }}" role="progressbar" style="width: {{ $ratio }}%" aria-valuenow="{{ $ratio }}" aria-valuemin="0" aria-valuemax="100"></div>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-4 mb-3">
                                    <div class="stat-card">
                                        <div class="stat-card-header">
                                            Répartition par sexe
                                        </div>
                                        <div class="stat-card-body">
                                            <div class="row">
                                                <div class="col-6">
                                                    <div class="comparison-icon female-color">
                                                        <i class="fas fa-female"></i>
                                                    </div>
                                                    <div class="comparison-value text-primary">{{ number_format($disciplineDetails['moyenne_filles'], 2) }}</div>
                                                    <div class="comparison-label">Filles ({{ $disciplineDetails['nb_filles'] }})</div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="comparison-icon male-color">
                                                        <i class="fas fa-male"></i>
                                                    </div>
                                                    <div class="comparison-value text-primary">{{ number_format($disciplineDetails['moyenne_garcons'], 2) }}</div>
                                                    <div class="comparison-label">Garçons ({{ $disciplineDetails['nb_garcons'] }})</div>
                                                </div>
                                            </div>
                                            
                                            <div class="mt-3">
                                                <div class="progress">
                                                    @php
                                                        $femalePercent = $disciplineDetails['nb_eleves'] ? ($disciplineDetails['nb_filles'] / $disciplineDetails['nb_eleves'] * 100) : 0;
                                                    @endphp
                                                    <div class="progress-bar female-color" role="progressbar" style="width: {{ $femalePercent }}%" aria-valuenow="{{ $femalePercent }}" aria-valuemin="0" aria-valuemax="100"></div>
                                                    <div class="progress-bar male-color" role="progressbar" style="width: {{ 100 - $femalePercent }}%" aria-valuenow="{{ 100 - $femalePercent }}" aria-valuemin="0" aria-valuemax="100"></div>
                                                </div>
                                                <div class="d-flex justify-content-between small">
                                                    <span>{{ round($femalePercent) }}% Filles</span>
                                                    <span>{{ round(100 - $femalePercent) }}% Garçons</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-4 mb-3">
                                    <div class="stat-card">
                                        <div class="stat-card-header">
                                            Taux de réussite
                                        </div>
                                        <div class="stat-card-body">
                                            <div class="stat-card-value text-success">{{ $disciplineDetails['reussite'] }}%</div>
                                            <div class="stat-card-label">Élèves avec moyenne ≥ 10</div>
                                            
                                            <div class="row mt-3">
                                                <div class="col-6">
                                                    <div class="comparison-value text-success">{{ $disciplineDetails['reussite_filles'] }}%</div>
                                                    <div class="comparison-label">Filles</div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="comparison-value text-success">{{ $disciplineDetails['reussite_garcons'] }}%</div>
                                                    <div class="comparison-label">Garçons</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Distribution des notes -->
                            <div class="row mb-4">
                                <div class="col-md-6 mb-3">
                                    <div class="stat-card h-100">
                                        <div class="stat-card-header">
                                            <i class="fas fa-chart-pie me-2"></i> Distribution des notes
                                        </div>
                                        <div class="stat-card-body">
                                            <div class="chart-container">
                                                <canvas id="distributionChart"></canvas>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <div class="stat-card h-100">
                                        <div class="stat-card-header">
                                            <i class="fas fa-chart-bar me-2"></i> Comparaison par sexe
                                        </div>
                                        <div class="stat-card-body">
                                            <div class="chart-container">
                                                <canvas id="genderComparisonChart"></canvas>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Statistiques par niveau et classe -->
                            <div class="row">
                                <div class="col-md-6 mb-4">
                                    <div class="table-container">
                                        <table class="table stats-table mb-0">
                                            <thead>
                                                <tr>
                                                    <th colspan="3">
                                                        <i class="fas fa-layer-group me-2"></i> Analyse par niveau
                                                    </th>
                                                </tr>
                                                <tr>
                                                    <th>Niveau</th>
                                                    <th class="text-center">Effectif</th>
                                                    <th class="text-center">Moyenne</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($disciplineDetails['niveaux'] as $niveau)
                                                    <tr>
                                                        <td>
                                                            <strong>{{ $niveau['code'] }}</strong> - {{ $niveau['nom'] }}
                                                        </td>
                                                        <td class="text-center">{{ $niveau['count_eleves'] }}</td>
                                                        <td class="text-center">
                                                            <span class="badge {{ $niveau['moyenne'] >= 10 ? 'bg-success' : 'bg-danger' }}">
                                                                {{ number_format($niveau['moyenne'], 2) }}
                                                            </span>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                
                                <div class="col-md-6 mb-4">
                                    <div class="table-container">
                                        <table class="table stats-table mb-0">
                                            <thead>
                                                <tr>
                                                    <th colspan="3">
                                                        <i class="fas fa-chalkboard me-2"></i> Analyse par classe
                                                    </th>
                                                </tr>
                                                <tr>
                                                    <th>Classe</th>
                                                    <th class="text-center">Effectif</th>
                                                    <th class="text-center">Moyenne</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($disciplineDetails['classes'] as $classe)
                                                    <tr>
                                                        <td>{{ $classe['nom'] }}</td>
                                                        <td class="text-center">{{ $classe['count_eleves'] }}</td>
                                                        <td class="text-center">
                                                            <span class="badge {{ $classe['moyenne'] >= 10 ? 'bg-success' : 'bg-danger' }}">
                                                                {{ number_format($classe['moyenne'], 2) }}
                                                            </span>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Boutons d'action -->
                            <div class="d-flex justify-content-end mt-3">
                                <button type="button" class="btn btn-primary me-2" onclick="window.print()">
                                    <i class="fas fa-print me-1"></i> Imprimer
                                </button>
                                <button type="button" class="btn btn-success me-2">
                                    <i class="fas fa-file-excel me-1"></i> Exporter en Excel
                                </button>
                                <button type="button" class="btn btn-danger">
                                    <i class="fas fa-file-pdf me-1"></i> Exporter en PDF
                                </button>
                            </div>
                            
                        @elseif(isset($disciplines) && !empty($disciplines))
                            <!-- Message pour sélectionner une discipline -->
                            <div class="no-data-message">
                                <div class="no-data-icon">
                                    <i class="fas fa-book"></i>
                                </div>
                                <h5>Sélectionnez une discipline</h5>
                                <p class="text-muted">Veuillez choisir une discipline dans la liste à gauche pour afficher son analyse détaillée.</p>
                            </div>
                        @else
                            <!-- Message si aucune discipline disponible -->
                            <div class="no-data-message">
                                <div class="no-data-icon">
                                    <i class="fas fa-exclamation-circle"></i>
                                </div>
                                <h5>Aucune donnée disponible</h5>
                                <p class="text-muted">Aucune discipline n'a été trouvée. Veuillez importer des fichiers Excel contenant des données de disciplines.</p>
                                <a href="{{ route('semestre1.base') }}" class="btn btn-primary mt-2">
                                    <i class="fas fa-file-import me-2"></i> Importer des fichiers
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Gestion du changement de niveau pour charger les classes associées
    const niveauSelect = document.getElementById('niveau_id');
    const classeSelect = document.getElementById('classe_id');
    
    if (niveauSelect && classeSelect) {
        niveauSelect.addEventListener('change', function() {
            const niveauId = this.value;
            
            // Désactiver temporairement le sélecteur de classe pendant le chargement
            classeSelect.disabled = true;
            
            if (!niveauId) {
                // Si aucun niveau n'est sélectionné, réinitialiser le sélecteur de classe
                classeSelect.innerHTML = '<option value="">Toutes les classes</option>';
                classeSelect.disabled = false;
                return;
            }
            
            // Charger les classes associées au niveau sélectionné via AJAX
            fetch(`/semestre1/classes-by-niveau/${niveauId}`)
                .then(response => response.json())
                .then(data => {
                    // Réinitialiser le sélecteur
                    classeSelect.innerHTML = '<option value="">Toutes les classes</option>';
                    
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
                })
                .catch(error => {
                    console.error('Erreur lors du chargement des classes:', error);
                    classeSelect.innerHTML = '<option value="">Erreur de chargement</option>';
                    classeSelect.disabled = false;
                });
        });
        
        // Déclencher l'événement au chargement de la page si un niveau est déjà sélectionné
        if (niveauSelect.value) {
            niveauSelect.dispatchEvent(new Event('change'));
        }
    }
    
    // Graphiques - seulement si la discipline est sélectionnée
    @if(isset($discipline) && isset($disciplineDetails) && $discipline && $disciplineDetails)
        // Graphique de distribution des notes
        const distributionCtx = document.getElementById('distributionChart');
        if (distributionCtx) {
            new Chart(distributionCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Excellent (≥16)', 'Bien (14-15.99)', 'Assez bien (12-13.99)', 'Passable (10-11.99)', 'Insuffisant (<10)'],
                    datasets: [{
                        data: [
                            {{ $disciplineDetails['distribution']['excellent'] ?? 0 }},
                            {{ $disciplineDetails['distribution']['bien'] ?? 0 }},
                            {{ $disciplineDetails['distribution']['assez_bien'] ?? 0 }},
                            {{ $disciplineDetails['distribution']['passable'] ?? 0 }},
                            {{ $disciplineDetails['distribution']['insuffisant'] ?? 0 }}
                        ],
                        backgroundColor: [
                            '#28a745', // Excellent - vert
                            '#17a2b8', // Bien - bleu clair
                            '#ffc107', // Assez bien - jaune
                            '#fd7e14', // Passable - orange
                            '#dc3545'  // Insuffisant - rouge
                        ],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                boxWidth: 12,
                                padding: 15,
                                font: {
                                    size: 11
                                }
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return `${context.label}: ${context.raw}%`;
                                }
                            }
                        }
                    },
                    cutout: '60%'
                }
            });
        }
        
        // Graphique de comparaison par sexe
        const genderCtx = document.getElementById('genderComparisonChart');
        if (genderCtx) {
            new Chart(genderCtx, {
                type: 'bar',
                data: {
                    labels: ['Moyenne', 'Taux de réussite'],
                    datasets: [
                        {
                            label: 'Filles',
                            data: [{{ $disciplineDetails['moyenne_filles'] ?? 0 }}, {{ $disciplineDetails['reussite_filles'] ?? 0 }}],
                            backgroundColor: '#e83e8c',
                            borderColor: '#e83e8c',
                            borderWidth: 1
                        },
                        {
                            label: 'Garçons',
                            data: [{{ $disciplineDetails['moyenne_garcons'] ?? 0 }}, {{ $disciplineDetails['reussite_garcons'] ?? 0 }}],
                            backgroundColor: '#0062cc',
                            borderColor: '#0062cc',
                            borderWidth: 1
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value, index, values) {
                                    // Pour la moyenne, on utilise le format décimal
                                    // Pour le taux de réussite, on ajoute le symbole %
                                    return value > 20 ? value + '%' : value.toFixed(1);
                                }
                            }
                        }
                    },
                    plugins: {
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const label = context.dataset.label || '';
                                    const value = context.raw;
                                    return `${label}: ${context.datasetIndex === 0 && context.dataIndex === 0 ? value.toFixed(2) : context.dataIndex === 1 ? value + '%' : value}`;
                                }
                            }
                        }
                    }
                }
            });
        }
    @endif
});
</script>
@endsection