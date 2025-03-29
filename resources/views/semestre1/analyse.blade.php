@extends('layouts.module')

@section('title', 'Analyse des Disciplines - Semestre 1')

@section('module-icon')
<i class="fas fa-chart-line me-2"></i>
@endsection

@section('module-title', 'Semestre 1')

@section('page-title', 'Analyse des Disciplines')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('semestre1.index') }}">Semestre 1</a></li>
<li class="breadcrumb-item active">Analyse des Disciplines</li>
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
    /* Styles généraux */
    :root {
        --primary: #3A506B;
        --secondary: #5BC0BE;
        --tertiary: #1C2541;
        --success: #43AA8B;
        --warning: #F9C74F;
        --danger: #F94144;
        --light: #F7F9FB;
        --dark: #212529;
    }
    
    /* Conteneur principal */
    .analyse-container {
        background-color: #f8f9fa;
        border-radius: 0.5rem;
        padding: 1.5rem;
    }
    
    /* Filtres */
    .filters-card {
        background-color: white;
        border-radius: 0.5rem;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        margin-bottom: 1.5rem;
        overflow: hidden;
    }
    
    .filters-header {
        background-color: var(--primary);
        color: white;
        padding: 1rem 1.25rem;
        font-weight: 600;
    }
    
    .filters-body {
        padding: 1.25rem;
    }
    
    /* Cartes de disciplines */
    .disciplines-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 1rem;
    }
    
    .discipline-card {
        background-color: white;
        border-radius: 0.5rem;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        overflow: hidden;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        cursor: pointer;
        height: 100%;
    }
    
    .discipline-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 15px rgba(0, 0, 0, 0.1);
    }
    
    .discipline-card-header {
        background-color: var(--primary);
        color: white;
        padding: 0.75rem 1rem;
        font-weight: 600;
        text-align: center;
    }
    
    .discipline-card-body {
        padding: 1rem;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }
    
    .discipline-count {
        background-color: var(--light);
        color: var(--primary);
        padding: 0.5rem 1rem;
        border-radius: 1rem;
        font-weight: 600;
        margin-top: 0.5rem;
    }
    
    .discipline-icon {
        font-size: 2rem;
        color: var(--primary);
        margin-bottom: 0.5rem;
    }
    
    /* Stats cards */
    .stats-card {
        background-color: white;
        border-radius: 0.5rem;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        margin-bottom: 1.5rem;
        overflow: hidden;
    }
    
    .stats-header {
        background-color: var(--primary);
        color: white;
        padding: 1rem 1.25rem;
        font-weight: 600;
    }
    
    .stats-body {
        padding: 1.25rem;
    }
    
    .stats-value {
        font-size: 2rem;
        font-weight: 700;
        margin-bottom: 0.25rem;
    }
    
    .stats-label {
        color: #6c757d;
        margin-bottom: 0.5rem;
    }
    
    /* Graphiques */
    .chart-container {
        height: 300px;
        position: relative;
    }
    
    /* Distribution bar */
    .distribution-bar {
        display: flex;
        height: 25px;
        border-radius: 12.5px;
        overflow: hidden;
        margin-bottom: 0.75rem;
    }
    
    .segment-excellent {
        background-color: var(--success);
    }
    
    .segment-good {
        background-color: var(--secondary);
    }
    
    .segment-average {
        background-color: var(--warning);
    }
    
    .segment-poor {
        background-color: var(--danger);
    }
    
    .distribution-legend {
        display: flex;
        flex-wrap: wrap;
        font-size: 0.8rem;
        margin-bottom: 1rem;
    }
    
    .legend-item {
        display: flex;
        align-items: center;
        margin-right: 1rem;
        margin-bottom: 0.5rem;
    }
    
    .legend-color {
        width: 12px;
        height: 12px;
        border-radius: 2px;
        margin-right: 4px;
    }
    
    /* Tables */
    .data-table {
        width: 100%;
    }
    
    .data-table th {
        background-color: #f8f9fa;
        font-weight: 600;
    }
    
    /* Comparaison par genre */
    .gender-comparison {
        display: flex;
        justify-content: space-around;
        margin-bottom: 1.5rem;
    }
    
    .gender-stats {
        text-align: center;
    }
    
    .gender-icon {
        font-size: 2rem;
        margin-bottom: 0.5rem;
    }
    
    .male-color {
        color: #007bff;
    }
    
    .female-color {
        color: #e83e8c;
    }
    
    /* Animations */
    .animate-fade-in {
        animation: fadeIn 0.5s ease-in-out forwards;
    }
    
    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
    
    /* Responsive */
    @media (max-width: 768px) {
        .disciplines-grid {
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
        }
    }
</style>
@endsection

@section('module-content')
<div class="analyse-container">
    <!-- Filtres -->
    <div class="filters-card">
        <div class="filters-header">
            <i class="fas fa-filter me-2"></i> Filtres d'analyse
        </div>
        <div class="filters-body">
            <form action="{{ route('semestre1.analyse') }}" method="GET">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label for="discipline" class="form-label">Discipline</label>
                        <select class="form-select" id="discipline" name="discipline">
                            <option value="">Toutes les disciplines</option>
                            @foreach($allDisciplines ?? [] as $disc => $count)
                                <option value="{{ $disc }}" {{ request('discipline') == $disc ? 'selected' : '' }}>
                                    {{ $disc }} ({{ $count }} élèves)
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="col-md-2">
                        <label for="niveau_id" class="form-label">Niveau</label>
                        <select class="form-select" id="niveau_id" name="niveau_id">
                            <option value="">Tous les niveaux</option>
                            @foreach($niveaux ?? [] as $niveau)
                                <option value="{{ $niveau->id }}" {{ request('niveau_id') == $niveau->id ? 'selected' : '' }}>
                                    {{ $niveau->nom }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="col-md-2">
                        <label for="classe_id" class="form-label">Classe</label>
                        <select class="form-select" id="classe_id" name="classe_id" {{ empty($classes) ? 'disabled' : '' }}>
                            <option value="">Toutes les classes</option>
                            @foreach($classes ?? [] as $classe)
                                <option value="{{ $classe->id }}" {{ request('classe_id') == $classe->id ? 'selected' : '' }}>
                                    {{ $classe->nom }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="col-md-2">
                        <label for="sexe" class="form-label">Sexe</label>
                        <select class="form-select" id="sexe" name="sexe">
                            <option value="">Tous</option>
                            <option value="F" {{ request('sexe') == 'F' ? 'selected' : '' }}>Filles</option>
                            <option value="H" {{ request('sexe') == 'H' ? 'selected' : '' }}>Garçons</option>
                        </select>
                    </div>
                    
                    <div class="col-md-2">
                        <div class="d-flex h-100 align-items-end">
                            <button type="submit" class="btn btn-primary me-2">
                                <i class="fas fa-search me-2"></i> Filtrer
                            </button>
                            <a href="{{ route('semestre1.analyse') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-undo"></i>
                            </a>
                        </div>
                    </div>
                </div>
                
                <div class="row mt-3">
                    <div class="col-md-2">
                        <label for="min_moyenne" class="form-label">Note minimale</label>
                        <input type="number" class="form-control" id="min_moyenne" name="min_moyenne" min="0" max="20" step="0.01" value="{{ request('min_moyenne') ?? '' }}">
                    </div>
                </div>
            </form>
        </div>
    </div>

    @if(empty($discipline))
        <!-- Liste des disciplines disponibles -->
        <div class="stats-card">
            <div class="stats-header">
                <i class="fas fa-th-list me-2"></i> Disciplines disponibles
            </div>
            <div class="stats-body">
                @if(count($allDisciplines ?? []) > 0)
                    <div class="disciplines-grid animate-fade-in">
                        @foreach($allDisciplines as $disc => $count)
                            <a href="{{ route('semestre1.analyse', ['discipline' => $disc]) }}" class="text-decoration-none">
                                <div class="discipline-card">
                                    <div class="discipline-card-header">
                                        {{ $disc }}
                                    </div>
                                    <div class="discipline-card-body">
                                        <div class="discipline-icon">
                                            <i class="fas fa-book"></i>
                                        </div>
                                        <div class="discipline-count">
                                            {{ $count }} élèves
                                        </div>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @else
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i> Aucune discipline n'a été trouvée. Veuillez importer des fichiers contenant des données de disciplines.
                    </div>
                @endif
            </div>
        </div>
    @else
        <!-- Affichage des détails de la discipline sélectionnée -->
        @if(isset($disciplineDetails))
            <!-- Header avec infos générales -->
            <div class="stats-card animate-fade-in">
                <div class="stats-header">
                    <i class="fas fa-chart-bar me-2"></i> Analyse de {{ $discipline }}
                </div>
                <div class="stats-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h5 class="mb-3">Répartition des performances</h5>
                            <div class="distribution-bar">
                                <div class="segment-excellent" style="width: {{ $disciplineDetails['distribution']['excellent'] }}%"></div>
                                <div class="segment-good" style="width: {{ $disciplineDetails['distribution']['bien'] + $disciplineDetails['distribution']['assez_bien'] }}%"></div>
                                <div class="segment-average" style="width: {{ $disciplineDetails['distribution']['passable'] }}%"></div>
                                <div class="segment-poor" style="width: {{ $disciplineDetails['distribution']['insuffisant'] }}%"></div>
                            </div>
                            
                            <div class="distribution-legend">
                                <div class="legend-item">
                                    <div class="legend-color" style="background-color: var(--success);"></div>
                                    <span>Excellent (≥16): {{ $disciplineDetails['distribution']['excellent'] }}%</span>
                                </div>
                                <div class="legend-item">
                                    <div class="legend-color" style="background-color: var(--secondary);"></div>
                                    <span>Bon (12-16): {{ $disciplineDetails['distribution']['bien'] + $disciplineDetails['distribution']['assez_bien'] }}%</span>
                                </div>
                                <div class="legend-item">
                                    <div class="legend-color" style="background-color: var(--warning);"></div>
                                    <span>Passable (10-12): {{ $disciplineDetails['distribution']['passable'] }}%</span>
                                </div>
                                <div class="legend-item">
                                    <div class="legend-color" style="background-color: var(--danger);"></div>
                                    <span>Insuffisant (<10): {{ $disciplineDetails['distribution']['insuffisant'] }}%</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="row text-center">
                                <div class="col-md-4">
                                    <div class="stats-value {{ $disciplineDetails['moyenne_generale'] >= 10 ? 'text-success' : 'text-danger' }}">
                                        {{ number_format($disciplineDetails['moyenne_generale'], 2) }}
                                    </div>
                                    <div class="stats-label">Moyenne générale</div>
                                </div>
                                <div class="col-md-4">
                                    <div class="stats-value text-primary">
                                        {{ $disciplineDetails['nb_eleves'] }}
                                    </div>
                                    <div class="stats-label">Élèves</div>
                                </div>
                                <div class="col-md-4">
                                    <div class="stats-value {{ $disciplineDetails['reussite'] >= 70 ? 'text-success' : ($disciplineDetails['reussite'] >= 50 ? 'text-warning' : 'text-danger') }}">
                                        {{ $disciplineDetails['reussite'] }}%
                                    </div>
                                    <div class="stats-label">Taux de réussite</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Statistiques détaillées -->
            <div class="row mb-4">
                <!-- Statistiques générales -->
                <div class="col-md-6">
                    <div class="stats-card h-100 animate-fade-in">
                        <div class="stats-header">
                            <i class="fas fa-chart-line me-2"></i> Indicateurs de performance
                        </div>
                        <div class="stats-body">
                            <div class="mb-3">
                                <label class="mb-1">Moyenne générale</label>
                                <div class="progress" style="height: 20px;">
                                    <div class="progress-bar {{ $disciplineDetails['moyenne_generale'] >= 10 ? 'bg-success' : 'bg-danger' }}" role="progressbar" 
                                         style="width: {{ ($disciplineDetails['moyenne_generale'] / 20) * 100 }}%">
                                        {{ number_format($disciplineDetails['moyenne_generale'], 2) }} / 20
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="mb-1">Taux de réussite (≥ 10)</label>
                                <div class="progress" style="height: 20px;">
                                    <div class="progress-bar {{ $disciplineDetails['reussite'] >= 70 ? 'bg-success' : ($disciplineDetails['reussite'] >= 50 ? 'bg-warning' : 'bg-danger') }}" role="progressbar" 
                                         style="width: {{ $disciplineDetails['reussite'] }}%">
                                        {{ $disciplineDetails['reussite'] }}%
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <div class="row">
                                    <div class="col-md-6">
                                        <label class="mb-1">Note minimale</label>
                                        <div class="progress" style="height: 20px;">
                                            <div class="progress-bar bg-danger" role="progressbar" 
                                                 style="width: {{ ($disciplineDetails['min_note'] / 20) * 100 }}%">
                                                {{ number_format($disciplineDetails['min_note'], 2) }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="mb-1">Note maximale</label>
                                        <div class="progress" style="height: 20px;">
                                            <div class="progress-bar bg-success" role="progressbar" 
                                                 style="width: {{ ($disciplineDetails['max_note'] / 20) * 100 }}%">
                                                {{ number_format($disciplineDetails['max_note'], 2) }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="alert alert-info mb-0">
                                <i class="fas fa-info-circle me-2"></i> Écart type: <strong>{{ $disciplineDetails['ecart_type'] }}</strong> - 
                                @if($disciplineDetails['ecart_type'] < 2)
                                    Faible dispersion (résultats homogènes)
                                @elseif($disciplineDetails['ecart_type'] < 4)
                                    Dispersion moyenne
                                @else
                                    Forte dispersion (résultats hétérogènes)
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Comparaison par genre -->
                <div class="col-md-6">
                    <div class="stats-card h-100 animate-fade-in">
                        <div class="stats-header">
                            <i class="fas fa-venus-mars me-2"></i> Analyse par genre
                        </div>
                        <div class="stats-body">
                            <div class="gender-comparison">
                                <div class="gender-stats">
                                    <div class="gender-icon female-color">
                                        <i class="fas fa-female"></i>
                                    </div>
                                    <div class="stats-value {{ $disciplineDetails['moyenne_filles'] >= 10 ? 'text-success' : 'text-danger' }}">
                                        {{ number_format($disciplineDetails['moyenne_filles'], 2) }}
                                    </div>
                                    <div class="stats-label">Filles ({{ $disciplineDetails['nb_filles'] }})</div>
                                </div>
                                
                                <div class="gender-stats">
                                    <div class="gender-icon male-color">
                                        <i class="fas fa-male"></i>
                                    </div>
                                    <div class="stats-value {{ $disciplineDetails['moyenne_garcons'] >= 10 ? 'text-success' : 'text-danger' }}">
                                        {{ number_format($disciplineDetails['moyenne_garcons'], 2) }}
                                    </div>
                                    <div class="stats-label">Garçons ({{ $disciplineDetails['nb_garcons'] }})</div>
                                </div>
                            </div>
                            
                            <hr>
                            
                            <div class="mb-3">
                                <label class="mb-1">Taux de réussite par genre</label>
                                <div class="mb-2">
                                    <div class="d-flex justify-content-between mb-1">
                                        <span><i class="fas fa-female me-1 female-color"></i> Filles</span>
                                        <span>{{ $disciplineDetails['reussite_filles'] }}%</span>
                                    </div>
                                    <div class="progress" style="height: 15px;">
                                        <div class="progress-bar bg-pink" role="progressbar" 
                                             style="width: {{ $disciplineDetails['reussite_filles'] }}%; background-color: #e83e8c;">
                                        </div>
                                    </div>
                                </div>
                                <div>
                                    <div class="d-flex justify-content-between mb-1">
                                        <span><i class="fas fa-male me-1 male-color"></i> Garçons</span>
                                        <span>{{ $disciplineDetails['reussite_garcons'] }}%</span>
                                    </div>
                                    <div class="progress" style="height: 15px;">
                                        <div class="progress-bar bg-blue" role="progressbar" 
                                             style="width: {{ $disciplineDetails['reussite_garcons'] }}%; background-color: #007bff;">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="alert {{ $disciplineDetails['moyenne_filles'] > $disciplineDetails['moyenne_garcons'] ? 'alert-pink' : 'alert-blue' }} mb-0" 
                                 style="{{ $disciplineDetails['moyenne_filles'] > $disciplineDetails['moyenne_garcons'] ? 'background-color: rgba(232, 62, 140, 0.1);' : 'background-color: rgba(0, 123, 255, 0.1);' }}">
                                @if($disciplineDetails['moyenne_filles'] > $disciplineDetails['moyenne_garcons'])
                                    <i class="fas fa-arrow-circle-up me-1 female-color"></i> 
                                    Les filles ont une moyenne supérieure de <strong>{{ number_format($disciplineDetails['moyenne_filles'] - $disciplineDetails['moyenne_garcons'], 2) }}</strong> points.
                                @elseif($disciplineDetails['moyenne_garcons'] > $disciplineDetails['moyenne_filles'])
                                    <i class="fas fa-arrow-circle-up me-1 male-color"></i> 
                                    Les garçons ont une moyenne supérieure de <strong>{{ number_format($disciplineDetails['moyenne_garcons'] - $disciplineDetails['moyenne_filles'], 2) }}</strong> points.
                                @else
                                    <i class="fas fa-equals me-1"></i> 
                                    Les performances sont identiques entre filles et garçons.
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Graphiques et analyses détaillées -->
            <div class="row mb-4">
                <!-- Distribution des notes -->
                <div class="col-md-6">
                    <div class="stats-card h-100 animate-fade-in">
                        <div class="stats-header">
                            <i class="fas fa-chart-bar me-2"></i> Distribution des notes
                        </div>
                        <div class="stats-body">
                            <div class="chart-container">
                                <canvas id="distributionChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Top des élèves -->
                <div class="col-md-6">
                    <div class="stats-card h-100 animate-fade-in">
                        <div class="stats-header">
                            <i class="fas fa-trophy me-2"></i> Top 10 des élèves
                        </div>
                        <div class="stats-body" style="max-height: 300px; overflow-y: auto;">
                            <table class="table table-sm table-striped data-table">
                                <thead>
                                    <tr>
                                        <th width="40">#</th>
                                        <th>Élève</th>
                                        <th width="60">Sexe</th>
                                        <th width="80">Note</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        // Trier les élèves par note décroissante
                                        $topEleves = collect($disciplineDetails['eleves'])
                                            ->sortByDesc('note')
                                            ->take(10)
                                            ->values();
                                    @endphp
                                    
                                    @foreach($topEleves as $index => $eleve)
                                        <tr>
                                            <td class="text-center">{{ $index + 1 }}</td>
                                            <td>{{ $eleve['prenom'] }} {{ $eleve['nom'] }}</td>
                                            <td class="text-center">
                                                @if($eleve['sexe'] == 'F')
                                                    <i class="fas fa-female female-color"></i>
                                                @else
                                                    <i class="fas fa-male male-color"></i>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <span class="badge {{ $eleve['note'] >= 16 ? 'bg-success' : ($eleve['note'] >= 14 ? 'bg-info' : ($eleve['note'] >= 10 ? 'bg-warning' : 'bg-danger')) }} rounded-pill">
                                                    {{ number_format($eleve['note'], 2) }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Comparaison par niveau et classe -->
            <div class="row mb-4">
                <!-- Comparaison par niveau -->
                <div class="col-md-6">
                    <div class="stats-card h-100 animate-fade-in">
                        <div class="stats-header">
                            <i class="fas fa-layer-group me-2"></i> Comparaison par niveau
                        </div>
                        <div class="stats-body">
                            @if(count($disciplineDetails['niveaux'] ?? []) > 0)
                                <div class="chart-container">
                                    <canvas id="niveauxChart"></canvas>
                                </div>
                            @else
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle me-2"></i> Aucune donnée par niveau disponible.
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                
                <!-- Comparaison par classe -->
                <div class="col-md-6">
                    <div class="stats-card h-100 animate-fade-in">
                        <div class="stats-header">
                            <i class="fas fa-school me-2"></i> Comparaison par classe
                        </div>
                        <div class="stats-body">
                            @if(count($disciplineDetails['classes'] ?? []) > 0)
                                <div class="chart-container">
                                    <canvas id="classesChart"></canvas>
                                </div>
                            @else
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle me-2"></i> Aucune donnée par classe disponible.
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle me-2"></i> Aucune donnée disponible pour la discipline "{{ $discipline }}". Veuillez vérifier vos filtres ou sélectionner une autre discipline.
            </div>
        @endif
    @endif
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Chargement dynamique des classes en fonction du niveau
        const niveauSelect = document.getElementById('niveau_id');
        const classeSelect = document.getElementById('classe_id');
        
        if (niveauSelect && classeSelect) {
            niveauSelect.addEventListener('change', function() {
                const niveauId = this.value;
                
                classeSelect.innerHTML = '<option value="">Toutes les classes</option>';
                classeSelect.disabled = !niveauId;
                
                if (niveauId) {
                    fetch(`/semestre1/classes-by-niveau/${niveauId}`)
                        .then(response => response.json())
                        .then(data => {
                            if (data && data.length > 0) {
                                data.forEach(classe => {
                                    const option = document.createElement('option');
                                    option.value = classe.id;
                                    option.textContent = classe.nom;
                                    classeSelect.appendChild(option);
                                });
                            } else {
                                const option = document.createElement('option');
                                option.value = '';
                                option.textContent = 'Aucune classe disponible';
                                classeSelect.appendChild(option);
                            }
                        })
                        .catch(error => {
                            console.error('Erreur:', error);
                        });
                }
            });
        }
        
        // Graphiques si une discipline est sélectionnée
        @if(isset($disciplineDetails))
            // Graphique de distribution des notes
            const distributionCtx = document.getElementById('distributionChart');
            if (distributionCtx) {
                // Création des intervalles de notes (0-2, 2-4, 4-6, etc.)
                const intervals = ['0-2', '2-4', '4-6', '6-8', '8-10', '10-12', '12-14', '14-16', '16-18', '18-20'];
                
                // Compter le nombre d'élèves dans chaque intervalle
                const eleves = @json($disciplineDetails['eleves']);
                const counts = Array(10).fill(0);
                
                eleves.forEach(eleve => {
                    const note = parseFloat(eleve.note);
                    const index = Math.min(Math.floor(note / 2), 9);
                    counts[index]++;
                });
                
                // Définir les couleurs selon les catégories de performance
                const colors = [
                    '#F94144', '#F94144',  // Rouge (0-4)
                    '#F3722C', '#F9C74F',  // Orange (4-8)
                    '#F9C74F', '#43AA8B',  // Jaune à Vert (8-12)
                    '#43AA8B', '#43AA8B',  // Vert (12-16)
                    '#277DA1', '#277DA1'   // Bleu (16-20)
                ];
                
                new Chart(distributionCtx, {
                    type: 'bar',
                    data: {
                        labels: intervals,
                        datasets: [{
                            label: 'Nombre d\'élèves',
                            data: counts,
                            backgroundColor: colors,
                            borderColor: colors,
                            borderWidth: 1,
                            borderRadius: 5
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                callbacks: {
                                    title: function(tooltipItems) {
                                        return `Notes entre ${tooltipItems[0].label}`;
                                    },
                                    label: function(context) {
                                        return `${context.raw} élève${context.raw > 1 ? 's' : ''}`;
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                title: {
                                    display: true,
                                    text: 'Nombre d\'élèves'
                                },
                                ticks: {
                                    precision: 0
                                }
                            },
                            x: {
                                title: {
                                    display: true,
                                    text: 'Intervalles de notes'
                                }
                            }
                        }
                    }
                });
            }
            
            // Graphique par niveau
            const niveauxCtx = document.getElementById('niveauxChart');
            if (niveauxCtx && @json(count($disciplineDetails['niveaux'] ?? [])) > 0) {
                const niveaux = @json($disciplineDetails['niveaux']);
                const labels = Object.values(niveaux).map(n => n.code);
                const moyennes = Object.values(niveaux).map(n => n.moyenne);
                const effectifs = Object.values(niveaux).map(n => n.count_eleves);
                
                new Chart(niveauxCtx, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Moyenne',
                            data: moyennes,
                            backgroundColor: moyennes.map(m => m >= 10 ? '#43AA8B' : '#F94144'),
                            borderColor: moyennes.map(m => m >= 10 ? '#43AA8B' : '#F94144'),
                            borderWidth: 1,
                            borderRadius: 5
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        const index = context.dataIndex;
                                        return [
                                            `Moyenne: ${parseFloat(context.raw).toFixed(2)}`,
                                            `Effectif: ${effectifs[index]} élèves`
                                        ];
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                suggestedMax: 20,
                                title: {
                                    display: true,
                                    text: 'Moyenne /20'
                                }
                            }
                        }
                    }
                });
            }
            
            // Graphique par classe
            const classesCtx = document.getElementById('classesChart');
            if (classesCtx && @json(count($disciplineDetails['classes'] ?? [])) > 0) {
                const classes = @json($disciplineDetails['classes']);
                const labels = Object.values(classes).map(c => c.nom);
                const moyennes = Object.values(classes).map(c => c.moyenne);
                const effectifs = Object.values(classes).map(c => c.count_eleves);
                
                new Chart(classesCtx, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Moyenne',
                            data: moyennes,
                            backgroundColor: moyennes.map(m => m >= 10 ? '#5BC0BE' : '#F94144'),
                            borderColor: moyennes.map(m => m >= 10 ? '#5BC0BE' : '#F94144'),
                            borderWidth: 1,
                            borderRadius: 5
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        const index = context.dataIndex;
                                        return [
                                            `Moyenne: ${parseFloat(context.raw).toFixed(2)}`,
                                            `Effectif: ${effectifs[index]} élèves`
                                        ];
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                suggestedMax: 20,
                                title: {
                                    display: true,
                                    text: 'Moyenne /20'
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