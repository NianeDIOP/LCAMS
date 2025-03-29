@extends('layouts.module')

@section('title', 'Tableau de bord - Semestre 1')

@section('module-icon')
<i class="fas fa-calendar-alt me-2"></i>
@endsection

@section('module-title', 'Semestre 1')

@section('page-title', 'Tableau de bord analytique')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('semestre1.index') }}">Semestre 1</a></li>
<li class="breadcrumb-item active">Tableau de bord</li>
@endsection

@section('sidebar-menu')
<li>
    <a href="{{ route('semestre1.index') }}" class="{{ request()->routeIs('semestre1.index') ? 'active' : '' }}">
        <i class="fas fa-home me-2"></i> Vue d'ensemble
    </a>
</li>
<li>
    <a href="{{ route('semestre1.dashboard') }}" class="{{ request()->routeIs('semestre1.dashboard') ? 'active' : '' }}">
        <i class="fas fa-tachometer-alt me-2"></i> Tableau de bord
    </a>
</li>
<li>
    <a href="{{ route('semestre1.analyse') }}" class="{{ request()->routeIs('semestre1.analyse') ? 'active' : '' }}">
        <i class="fas fa-chart-line me-2"></i> Analyse des disciplines
    </a>
</li>
<li>
    <a href="{{ route('semestre1.rapports') }}" class="{{ request()->routeIs('semestre1.rapports') ? 'active' : '' }}">
        <i class="fas fa-file-alt me-2"></i> Génération des rapports
    </a>
</li>
<li>
    <a href="{{ route('semestre1.base') }}" class="{{ request()->routeIs('semestre1.base') ? 'active' : '' }}">
        <i class="fas fa-database me-2"></i> Base des moyennes
    </a>
</li>
@endsection

@section('styles')
<style>
    /* Styles généraux */
    .dashboard-wrapper {
        background-color: #f8f9fa;
        padding: 0;
        border-radius: 8px;
        margin-bottom: 1.5rem;
    }
    
    .dashboard-header {
        background: linear-gradient(135deg, #0062cc, #0097a7);
        color: white;
        padding: 1.25rem;
        border-radius: 8px 8px 0 0;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    
    .dashboard-title {
        margin: 0;
        font-size: 1.25rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .dashboard-controls {
        display: flex;
        gap: 10px;
    }
    
    .dashboard-content {
        padding: 1.25rem;
    }
    
    .filter-container {
        background-color: #fff;
        padding: 1rem;
        border-radius: 8px;
        margin-bottom: 1.5rem;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
    }
    
    .filter-title {
        font-size: 0.9rem;
        font-weight: 600;
        margin-bottom: 1rem;
        color: #495057;
        display: flex;
        align-items: center;
    }
    
    .filter-title i {
        margin-right: 6px;
        color: #0062cc;
    }
    
    .filter-body {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
    }
    
    .filter-group {
        flex: 1;
        min-width: 200px;
    }
    
    .stats-card {
        background-color: white;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        height: 100%;
        transition: transform 0.2s, box-shadow 0.2s;
        overflow: hidden;
    }
    
    .stats-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }
    
    .stats-header {
        padding: 1rem;
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        background-color: #f8f9fa;
    }
    
    .stats-title {
        margin: 0;
        font-size: 0.9rem;
        font-weight: 600;
        color: #495057;
    }
    
    .stats-body {
        padding: 1.25rem;
        text-align: center;
    }
    
    .stats-value {
        font-size: 2rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
        color: #0062cc;
    }
    
    .stats-label {
        font-size: 0.85rem;
        color: #6c757d;
    }
    
    .stats-footer {
        background-color: #f8f9fa;
        padding: 0.75rem 1rem;
        border-top: 1px solid rgba(0, 0, 0, 0.05);
        font-size: 0.8rem;
        color: #6c757d;
    }
    
    .chart-container {
        position: relative;
        height: 300px;
        width: 100%;
    }
    
    .donut-container {
        position: relative;
        height: 150px;
        margin: 0 auto;
    }
    
    .stats-card .progress {
        height: 8px;
        margin: 1rem 0;
    }
    
    .metrics-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 10px;
        margin-top: 1rem;
    }
    
    .metric-item {
        background-color: #f8f9fa;
        padding: 0.75rem;
        border-radius: 6px;
        text-align: center;
    }
    
    .metric-value {
        font-size: 1.25rem;
        font-weight: 600;
        color: #0062cc;
        margin-bottom: 0.25rem;
    }
    
    .metric-label {
        font-size: 0.75rem;
        color: #6c757d;
    }
    
    .table-stats {
        font-size: 0.85rem;
    }
    
    .table-stats th {
        background-color: #f8f9fa;
        font-weight: 600;
    }
    
    .icon-box {
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: rgba(0, 98, 204, 0.1);
        color: #0062cc;
        border-radius: 8px;
        margin-right: 1rem;
        font-size: 1.1rem;
    }
    
    .section {
        margin-bottom: 1.5rem;
    }
    
    .section-header {
        display: flex;
        align-items: center;
        margin-bottom: 1rem;
    }
    
    .section-title {
        font-size: 1.1rem;
        font-weight: 600;
        margin: 0;
        color: #343a40;
    }
    
    .gender-stats {
        display: flex;
        justify-content: center;
        margin-top: 1rem;
        gap: 2rem;
    }
    
    .gender-stat {
        text-align: center;
    }
    
    .gender-icon {
        font-size: 1.5rem;
        margin-bottom: 0.5rem;
    }
    
    .gender-value {
        font-size: 1.25rem;
        font-weight: 600;
        margin-bottom: 0.25rem;
    }
    
    .gender-label {
        font-size: 0.8rem;
        color: #6c757d;
    }
    
    .male-color {
        color: #0062cc;
    }
    
    .female-color {
        color: #e83e8c;
    }
    
    .performance-indicator {
        display: flex;
        align-items: center;
        margin-bottom: 1rem;
    }
    
    .indicator-value {
        font-size: 1.75rem;
        font-weight: 700;
        margin-right: 1rem;
        min-width: 70px;
    }
    
    .indicator-details {
        flex: 1;
    }
    
    .indicator-label {
        font-size: 0.85rem;
        font-weight: 600;
        margin-bottom: 0.25rem;
    }
    
    .indicator-bar {
        height: 8px;
        background-color: #e9ecef;
        border-radius: 4px;
        overflow: hidden;
    }
    
    .indicator-fill {
        height: 100%;
        border-radius: 4px;
    }
    
    .observation-card {
        display: flex;
        align-items: center;
        background-color: white;
        padding: 1rem;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        margin-bottom: 1rem;
    }
    
    .observation-icon {
        width: 45px;
        height: 45px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        margin-right: 1rem;
        color: white;
        font-size: 1.25rem;
    }
    
    .observation-details {
        flex: 1;
    }
    
    .observation-title {
        font-size: 0.9rem;
        font-weight: 600;
        margin-bottom: 0.25rem;
    }
    
    .observation-value {
        font-size: 1.5rem;
        font-weight: 700;
    }
    
    .observation-footer {
        font-size: 0.75rem;
        color: #6c757d;
        margin-top: 0.25rem;
    }
    
    .export-button {
        position: fixed;
        bottom: 30px;
        right: 30px;
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background-color: #0062cc;
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        z-index: 999;
        transition: all 0.3s;
    }
    
    .export-button:hover {
        transform: scale(1.1);
        background-color: #004c9e;
    }
    
    /* Pour les petits écrans */
    @media (max-width: 768px) {
        .dashboard-header {
            flex-direction: column;
            align-items: flex-start;
        }
        
        .dashboard-controls {
            margin-top: 1rem;
            width: 100%;
            justify-content: space-between;
        }
        
        .metrics-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }
    
    @media (max-width: 576px) {
        .metrics-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endsection

@section('module-content')
<div class="dashboard-wrapper">
    <div class="dashboard-header">
        <!-- En-tête simple sans boutons -->
    </div>
    
    <div class="dashboard-content">
        <!-- Filtres simplifiés -->
        <div class="filter-container">
            <div class="filter-title">
                <i class="fas fa-filter"></i> Filtres
            </div>
            
            <form id="filterForm" action="{{ route('semestre1.dashboard') }}" method="GET">
                <div class="row">
                    <div class="col-md-2 mb-2">
                        <label for="filter_type" class="form-label">Afficher</label>
                        <select class="form-select form-select-sm" id="filter_type" name="filter_type">
                            <option value="all" {{ ($filterType ?? 'all') == 'all' ? 'selected' : '' }}>Tous</option>
                            <option value="niveau" {{ ($filterType ?? '') == 'niveau' ? 'selected' : '' }}>Par niveau</option>
                            <option value="classe" {{ ($filterType ?? '') == 'classe' ? 'selected' : '' }}>Par classe</option>
                            <option value="sexe" {{ ($filterType ?? '') == 'sexe' ? 'selected' : '' }}>Par sexe</option>
                            <option value="interval" {{ ($filterType ?? '') == 'interval' ? 'selected' : '' }}>Par moyenne</option>
                        </select>
                    </div>
                    
                    <div class="col-md-2 mb-2 niveau-filter" style="{{ ($filterType ?? '') != 'niveau' && ($filterType ?? '') != 'classe' ? 'display:none' : '' }}">
                        <label for="niveau_id" class="form-label">Niveau</label>
                        <select class="form-select form-select-sm" id="niveau_id" name="niveau_id" {{ ($filterType ?? '') != 'niveau' && ($filterType ?? '') != 'classe' ? 'disabled' : '' }}>
                            <option value="">Sélectionnez</option>
                            @foreach($niveauxTous ?? [] as $niveau)
                                <option value="{{ $niveau->id }}" {{ ($niveau_id ?? '') == $niveau->id ? 'selected' : '' }}>{{ $niveau->nom }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="col-md-2 mb-2 classe-filter" style="{{ ($filterType ?? '') != 'classe' ? 'display:none' : '' }}">
                        <label for="classe_id" class="form-label">Classe</label>
                        <select class="form-select form-select-sm" id="classe_id" name="classe_id" {{ ($filterType ?? '') != 'classe' ? 'disabled' : '' }}>
                            <option value="">Sélectionnez</option>
                            @foreach($classes ?? [] as $classe)
                                <option value="{{ $classe->id }}" {{ ($classe_id ?? '') == $classe->id ? 'selected' : '' }}>{{ $classe->nom }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="col-md-2 mb-2 sexe-filter" style="{{ ($filterType ?? '') != 'sexe' ? 'display:none' : '' }}">
                        <label for="sexe" class="form-label">Sexe</label>
                        <select class="form-select form-select-sm" id="sexe" name="sexe" {{ ($filterType ?? '') != 'sexe' ? 'disabled' : '' }}>
                            <option value="">Tous</option>
                            <option value="F" {{ (request('sexe') ?? '') == 'F' ? 'selected' : '' }}>Filles</option>
                            <option value="G" {{ (request('sexe') ?? '') == 'G' ? 'selected' : '' }}>Garçons</option>
                        </select>
                    </div>
                    
                    <div class="col-md-2 mb-2 interval-filter" style="{{ ($filterType ?? '') != 'interval' ? 'display:none' : '' }}">
                        <label for="min_moyenne" class="form-label">Min</label>
                        <input type="number" class="form-control form-control-sm" id="min_moyenne" name="min_moyenne" step="0.01" min="0" max="20" value="{{ $min_moyenne ?? '' }}" {{ ($filterType ?? '') != 'interval' ? 'disabled' : '' }}>
                    </div>
                    
                    <div class="col-md-2 mb-2 interval-filter" style="{{ ($filterType ?? '') != 'interval' ? 'display:none' : '' }}">
                        <label for="max_moyenne" class="form-label">Max</label>
                        <input type="number" class="form-control form-control-sm" id="max_moyenne" name="max_moyenne" step="0.01" min="0" max="20" value="{{ $max_moyenne ?? '' }}" {{ ($filterType ?? '') != 'interval' ? 'disabled' : '' }}>
                    </div>
                    
                    <div class="col-md-2 mb-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-sm btn-primary w-100">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>
        
        <!-- Statistiques générales -->
        <div class="row section">
            <div class="col-lg-12">
                <div class="section-header">
                    <div class="icon-box">
                        <i class="fas fa-chart-pie"></i>
                    </div>
                    <h2 class="section-title">Statistiques générales</h2>
                </div>
            </div>
            
            <!-- Effectifs totaux -->
            <div class="col-md-3 mb-4">
                <div class="stats-card">
                    <div class="stats-header">
                        <h3 class="stats-title">Effectif</h3>
                    </div>
                    <div class="stats-body">
                        <div class="stats-value">{{ $totalEleves ?? '0' }}</div>
                        <div class="stats-label">Élèves au total</div>
                        
                        <div class="gender-stats">
                            <div class="gender-stat">
                                <div class="gender-icon male-color">
                                    <i class="fas fa-male"></i>
                                </div>
                                <div class="gender-value male-color">{{ $garconsCount ?? '0' }}</div>
                                <div class="gender-label">Garçons</div>
                            </div>
                            
                            <div class="gender-stat">
                                <div class="gender-icon female-color">
                                    <i class="fas fa-female"></i>
                                </div>
                                <div class="gender-value female-color">{{ $fillesCount ?? '0' }}</div>
                                <div class="gender-label">Filles</div>
                            </div>
                        </div>
                    </div>
                    <div class="stats-footer text-center">
                        <div class="row">
                            <div class="col-6 border-end">
                                <span class="fw-bold male-color">{{ $totalEleves ? round(($garconsCount / $totalEleves) * 100) : '0' }}%</span> garçons
                            </div>
                            <div class="col-6">
                                <span class="fw-bold female-color">{{ $totalEleves ? round(($fillesCount / $totalEleves) * 100) : '0' }}%</span> filles
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Réussite -->
            <div class="col-md-3 mb-4">
                <div class="stats-card">
                    <div class="stats-header">
                        <h3 class="stats-title">Réussite (≥ 10)</h3>
                    </div>
                    <div class="stats-body">
                        <div class="stats-value text-success">{{ $elevesAvecMoyenne ?? '0' }}</div>
                        <div class="stats-label">Élèves avec moyenne ≥ 10</div>
                        
                        <div class="progress">
                            <div class="progress-bar bg-success" role="progressbar" style="width: {{ $tauxReussite ?? '0' }}%" aria-valuenow="{{ $tauxReussite ?? '0' }}" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <div class="stats-label mt-1">Taux de réussite: <strong>{{ $tauxReussite ?? '0' }}%</strong></div>
                        
                        <div class="metrics-grid">
                            <div class="metric-item">
                                <div class="metric-value text-success">{{ $fillesAvecMoyenne ?? '0' }}</div>
                                <div class="metric-label">Filles</div>
                            </div>
                            <div class="metric-item">
                                <div class="metric-value text-primary">{{ $garconsAvecMoyenne ?? '0' }}</div>
                                <div class="metric-label">Garçons</div>
                            </div>
                            <div class="metric-item">
                                <div class="metric-value">{{ $tauxReussite ?? '0' }}%</div>
                                <div class="metric-label">Taux</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Moyennes -->
            <div class="col-md-3 mb-4">
                <div class="stats-card">
                    <div class="stats-header">
                        <h3 class="stats-title">Moyennes</h3>
                    </div>
                    <div class="stats-body">
                        <div class="stats-value text-primary">{{ number_format(($noteMoyenne ?? 0), 2) }}</div>
                        <div class="stats-label">Moyenne générale</div>
                        
                        <div class="metrics-grid">
                            <div class="metric-item">
                                <div class="metric-value text-success">{{ number_format(($plusForteMoyenne ?? 0), 2) }}</div>
                                <div class="metric-label">Max</div>
                            </div>
                            <div class="metric-item">
                                <div class="metric-value text-danger">{{ number_format(($plusFaibleMoyenne ?? 0), 2) }}</div>
                                <div class="metric-label">Min</div>
                            </div>
                            <div class="metric-item">
                                <div class="metric-value text-warning">{{ number_format(($noteMoyenne ?? 0), 2) }}</div>
                                <div class="metric-label">Moy</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Mentions -->
            <div class="col-md-3 mb-4">
                <div class="stats-card">
                    <div class="stats-header">
                        <h3 class="stats-title">Mentions</h3>
                    </div>
                    <div class="stats-body">
                        <div class="stats-value text-warning">{{ ($felicitations ?? 0) + ($encouragements ?? 0) + ($tableauHonneur ?? 0) }}</div>
                        <div class="stats-label">Élèves avec mention</div>
                        
                        <div class="metrics-grid">
                            <div class="metric-item">
                                <div class="metric-value text-danger">{{ $felicitations ?? '0' }}</div>
                                <div class="metric-label">Félicitations</div>
                            </div>
                            <div class="metric-item">
                                <div class="metric-value text-warning">{{ $encouragements ?? '0' }}</div>
                                <div class="metric-label">Encouragements</div>
                            </div>
                            <div class="metric-item">
                                <div class="metric-value text-info">{{ $tableauHonneur ?? '0' }}</div>
                                <div class="metric-label">Tab. d'honneur</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Graphiques de performance -->
        <div class="row section">
            <!-- Graphique des moyennes par niveau -->
            <div class="col-lg-6 mb-4">
                <div class="stats-card">
                    <div class="stats-header">
                        <h3 class="stats-title">Moyenne par niveau</h3>
                    </div>
                    <div class="stats-body">
                        <div class="chart-container">
                            <canvas id="niveauxMoyenneChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Graphique du taux de réussite par niveau -->
            <div class="col-lg-6 mb-4">
                <div class="stats-card">
                    <div class="stats-header">
                        <h3 class="stats-title">Taux de réussite par niveau</h3>
                    </div>
                    <div class="stats-body">
                        <div class="chart-container">
                            <canvas id="niveauxReussiteChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Performance des élèves -->
        <div class="row section">
            <div class="col-lg-12">
                <div class="section-header">
                    <div class="icon-box">
                        <i class="fas fa-user-graduate"></i>
                    </div>
                    <h2 class="section-title">Performance des élèves</h2>
                </div>
            </div>
            
            <!-- Indicateurs de performance -->
            <div class="col-md-6 mb-4">
                <div class="stats-card">
                    <div class="stats-header">
                        <h3 class="stats-title">Indicateurs de performance</h3>
                    </div>
                    <div class="stats-body">
                        <div class="performance-indicator">
                            <div class="indicator-value text-primary">{{ number_format(($noteMoyenne ?? 0), 2) }}</div>
                            <div class="indicator-details">
                                <div class="indicator-label">Moyenne générale</div>
                                <div class="indicator-bar">
                                    <div class="indicator-fill bg-primary" style="width: {{ min(100, ($noteMoyenne ?? 0) * 5) }}%"></div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="performance-indicator">
                            <div class="indicator-value text-success">{{ number_format(($plusForteMoyenne ?? 0), 2) }}</div>
                            <div class="indicator-details">
                                <div class="indicator-label">Plus forte moyenne</div>
                                <div class="indicator-bar">
                                    <div class="indicator-fill bg-success" style="width: {{ min(100, ($plusForteMoyenne ?? 0) * 5) }}%"></div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="performance-indicator">
                            <div class="indicator-value text-danger">{{ number_format(($plusFaibleMoyenne ?? 0), 2) }}</div>
                            <div class="indicator-details">
                                <div class="indicator-label">Plus faible moyenne</div>
                                <div class="indicator-bar">
                                    <div class="indicator-fill bg-danger" style="width: {{ min(100, ($plusFaibleMoyenne ?? 0) * 5) }}%"></div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="performance-indicator">
                            <div class="indicator-value text-warning">{{ $tauxReussite ?? 0 }}%</div>
                            <div class="indicator-details">
                                <div class="indicator-label">Taux de réussite</div>
                                <div class="indicator-bar">
                                    <div class="indicator-fill bg-warning" style="width: {{ $tauxReussite ?? 0 }}%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Distribution des notes -->
            <div class="col-md-6 mb-4">
                <div class="stats-card">
                    <div class="stats-header">
                        <h3 class="stats-title">Distribution des notes</h3>
                    </div>
                    <div class="stats-body">
                        <div class="chart-container">
                            <canvas id="distributionChart"></canvas>
                        </div>
                    </div>
                    <div class="stats-footer text-center">
                        <div class="row">
                            <div class="col-3">
                                <small class="d-block fw-bold text-success">{{ $performanceStats['excellent'] ?? 0 }}%</small>
                                <small>Excellent<br>(≥ 16)</small>
                            </div>
                            <div class="col-3">
                                <small class="d-block fw-bold text-info">{{ $performanceStats['good'] ?? 0 }}%</small>
                                <small>Bien<br>(14-16)</small>
                            </div>
                            <div class="col-3">
                                <small class="d-block fw-bold text-warning">{{ $performanceStats['average'] ?? 0 }}%</small>
                                <small>Moyen<br>(10-14)</small>
                            </div>
                            <div class="col-3">
                                <small class="d-block fw-bold text-danger">{{ $performanceStats['poor'] ?? 0 }}%</small>
                                <small>Insuffisant<br>(< 10)</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Observations et décisions du conseil -->
        <div class="row section">
            <div class="col-lg-12">
                <div class="section-header">
                    <div class="icon-box">
                        <i class="fas fa-comments"></i>
                    </div>
                    <h2 class="section-title">Observations et décisions du conseil</h2>
                </div>
            </div>
            
            <div class="col-md-4 mb-4">
                <div class="observation-card">
                    <div class="observation-icon" style="background-color: #28a745;">
                        <i class="fas fa-trophy"></i>
                    </div>
                    <div class="observation-details">
                        <div class="observation-title">Félicitations</div>
                        <div class="observation-value">{{ $felicitations ?? 0 }}</div>
                        <div class="observation-footer">
                            {{ $totalEleves > 0 ? round(($felicitations / $totalEleves) * 100) : 0 }}% des élèves
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4 mb-4">
                <div class="observation-card">
                    <div class="observation-icon" style="background-color: #17a2b8;">
                        <i class="fas fa-thumbs-up"></i>
                    </div>
                    <div class="observation-details">
                        <div class="observation-title">Encouragements</div>
                        <div class="observation-value">{{ $encouragements ?? 0 }}</div>
                        <div class="observation-footer">
                            {{ $totalEleves > 0 ? round(($encouragements / $totalEleves) * 100) : 0 }}% des élèves
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4 mb-4">
                <div class="observation-card">
                    <div class="observation-icon" style="background-color: #ffc107;">
                        <i class="fas fa-award"></i>
                    </div>
                    <div class="observation-details">
                        <div class="observation-title">Tableau d'honneur</div>
                        <div class="observation-value">{{ $tableauHonneur ?? 0 }}</div>
                        <div class="observation-footer">
                            {{ $totalEleves > 0 ? round(($tableauHonneur / $totalEleves) * 100) : 0 }}% des élèves
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4 mb-4">
                <div class="observation-card">
                    <div class="observation-icon" style="background-color: #ff9800;">
                        <i class="fas fa-exclamation-circle"></i>
                    </div>
                    <div class="observation-details">
                        <div class="observation-title">Peut mieux faire</div>
                        <div class="observation-value">{{ $mieuxFaire ?? 0 }}</div>
                        <div class="observation-footer">
                            {{ $totalEleves > 0 ? round(($mieuxFaire / $totalEleves) * 100) : 0 }}% des élèves
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4 mb-4">
                <div class="observation-card">
                    <div class="observation-icon" style="background-color: #6c757d;">
                        <i class="fas fa-arrows-alt-v"></i>
                    </div>
                    <div class="observation-details">
                        <div class="observation-title">Doit continuer</div>
                        <div class="observation-value">{{ $doitContinuer ?? 0 }}</div>
                        <div class="observation-footer">
                            {{ $totalEleves > 0 ? round(($doitContinuer / $totalEleves) * 100) : 0 }}% des élèves
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4 mb-4">
                <div class="observation-card">
                    <div class="observation-icon" style="background-color: #dc3545;">
                        <i class="fas fa-times-circle"></i>
                    </div>
                    <div class="observation-details">
                        <div class="observation-title">Risque de redoublement</div>
                        <div class="observation-value">{{ $risqueRedoubler ?? 0 }}</div>
                        <div class="observation-footer">
                            {{ $totalEleves > 0 ? round(($risqueRedoubler / $totalEleves) * 100) : 0 }}% des élèves
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Tableau des statistiques par niveau -->
        <div class="row section">
            <div class="col-lg-12">
                <div class="section-header">
                    <div class="icon-box">
                        <i class="fas fa-table"></i>
                    </div>
                    <h2 class="section-title">Statistiques détaillées par niveau</h2>
                </div>
                
                <div class="stats-card">
                    <div class="table-responsive">
                        <table class="table table-hover table-stats">
                            <thead>
                                <tr>
                                    <th>Niveau</th>
                                    <th class="text-center">Effectif</th>
                                    <th class="text-center">Moyenne générale</th>
                                    <th class="text-center">Taux de réussite</th>
                                    <th class="text-center">Félicitations</th>
                                    <th class="text-center">Encouragements</th>
                                    <th class="text-center">Tableau d'honneur</th>
                                    <th class="text-center">Risque d'échec</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($statsNiveaux ?? [] as $niveau)
                                <tr>
                                    <td>
                                        <strong>{{ $niveau['code'] }}</strong> - {{ $niveau['nom'] }}
                                    </td>
                                    <td class="text-center">{{ $niveau['effectif'] }}</td>
                                    <td class="text-center">
                                        <span class="badge {{ floatval($niveau['moyenne']) >= 10 ? 'bg-success' : 'bg-danger' }}">
                                            {{ $niveau['moyenne'] }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <div class="progress" style="height: 5px;">
                                            <div class="progress-bar {{ $niveau['taux_reussite'] >= 70 ? 'bg-success' : ($niveau['taux_reussite'] >= 50 ? 'bg-warning' : 'bg-danger') }}" 
                                                 style="width: {{ $niveau['taux_reussite'] }}%" 
                                                 title="{{ $niveau['taux_reussite'] }}%">
                                            </div>
                                        </div>
                                        <small>{{ $niveau['taux_reussite'] }}%</small>
                                    </td>
                                    <td class="text-center">{{ $niveau['felicitations'] }}</td>
                                    <td class="text-center">{{ $niveau['encouragements'] }}</td>
                                    <td class="text-center">{{ $niveau['tableau_honneur'] }}</td>
                                    <td class="text-center">
                                        {{ $niveau['effectif'] - ($niveau['felicitations'] + $niveau['encouragements'] + $niveau['tableau_honneur']) }}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Retards et absences -->
        <div class="row section">
            <div class="col-lg-12">
                <div class="section-header">
                    <div class="icon-box">
                        <i class="fas fa-clock"></i>
                    </div>
                    <h2 class="section-title">Retards et absences</h2>
                </div>
                
                <div class="stats-card">
                    <div class="stats-body">
                        <div class="chart-container">
                            <canvas id="absencesChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bouton d'exportation flottant -->
<a href="{{ route('semestre1.dashboard', ['export' => 'pdf'] + request()->query()) }}" class="export-button" title="Exporter en PDF">
    <i class="fas fa-file-export"></i>
</a>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Gestion des filtres dynamiques
    const filterType = document.getElementById('filter_type');
    const niveauFilter = document.querySelectorAll('.niveau-filter');
    const classeFilter = document.querySelectorAll('.classe-filter');
    const sexeFilter = document.querySelectorAll('.sexe-filter');
    const intervalFilter = document.querySelectorAll('.interval-filter');
    const niveauSelect = document.getElementById('niveau_id');
    const classeSelect = document.getElementById('classe_id');
    const sexeSelect = document.getElementById('sexe');
    const minMoyenneInput = document.getElementById('min_moyenne');
    const maxMoyenneInput = document.getElementById('max_moyenne');
    
    // Gérer le changement du type de filtre
    filterType.addEventListener('change', function() {
        // Réinitialiser tous les filtres
        niveauFilter.forEach(el => el.style.display = 'none');
        classeFilter.forEach(el => el.style.display = 'none');
        sexeFilter.forEach(el => el.style.display = 'none');
        intervalFilter.forEach(el => el.style.display = 'none');
        
        niveauSelect.disabled = true;
        classeSelect.disabled = true;
        sexeSelect.disabled = true;
        minMoyenneInput.disabled = true;
        maxMoyenneInput.disabled = true;
        
        // Activer les filtres nécessaires
        if (this.value === 'niveau' || this.value === 'classe') {
            niveauFilter.forEach(el => el.style.display = 'block');
            niveauSelect.disabled = false;
            
            if (this.value === 'classe') {
                classeFilter.forEach(el => el.style.display = 'block');
                classeSelect.disabled = false;
            }
        } else if (this.value === 'sexe') {
            sexeFilter.forEach(el => el.style.display = 'block');
            sexeSelect.disabled = false;
        } else if (this.value === 'interval') {
            intervalFilter.forEach(el => el.style.display = 'block');
            minMoyenneInput.disabled = false;
            maxMoyenneInput.disabled = false;
        }
    });
    
    // Gérer le chargement des classes lors du changement de niveau
    niveauSelect.addEventListener('change', function() {
        const niveauId = this.value;
        
        // Réinitialiser le sélecteur de classe
        classeSelect.innerHTML = '<option value="">Sélectionnez</option>';
        
        if (niveauId && filterType.value === 'classe') {
            classeSelect.disabled = true; // Désactiver pendant le chargement
            
            // Afficher un indicateur de chargement
            classeSelect.innerHTML = '<option value="">Chargement...</option>';
            
            // Appel AJAX pour charger les classes du niveau
            fetch(`/semestre1/classes-by-niveau/${niveauId}`)
                .then(response => response.json())
                .then(data => {
                    // Réinitialiser le sélecteur
                    classeSelect.innerHTML = '<option value="">Sélectionnez</option>';
                    
                    // Ajouter les classes
                    data.forEach(classe => {
                        const option = document.createElement('option');
                        option.value = classe.id;
                        option.textContent = classe.nom;
                        classeSelect.appendChild(option);
                    });
                    
                    // Réactiver le sélecteur
                    classeSelect.disabled = false;
                    
                    // Si une classe était précédemment sélectionnée, essayer de la restaurer
                    const previousClasseId = "{{ $classe_id ?? '' }}";
                    if (previousClasseId) {
                        const option = classeSelect.querySelector(`option[value="${previousClasseId}"]`);
                        if (option) option.selected = true;
                    }
                })
                .catch(error => {
                    console.error('Erreur lors du chargement des classes:', error);
                    classeSelect.innerHTML = '<option value="">Erreur de chargement</option>';
                    classeSelect.disabled = true;
                });
        } else {
            classeSelect.disabled = true;
        }
    });
    
    // Bouton d'actualisation
    document.getElementById('refreshBtn')?.addEventListener('click', function() {
        location.reload();
    });
    
    // Données pour les graphiques
    const statsNiveauxCodes = @json(array_column($statsNiveaux ?? [], 'code') ?? []);
    const statsNiveauxMoyennes = @json(array_map(function($n) { return floatval($n['moyenne']); }, $statsNiveaux ?? []) ?? []);
    const statsNiveauxTauxReussite = @json(array_column($statsNiveaux ?? [], 'taux_reussite') ?? []);
    const classesData = @json($classesData ?? []);
    const retardsData = @json($retardsData ?? []);
    const absencesData = @json($absencesData ?? []);
    
    // Configuration des couleurs
    const primaryColor = '#0062cc';
    const secondaryColor = '#6c757d';
    const successColor = '#28a745';
    const dangerColor = '#dc3545';
    const warningColor = '#ffc107';
    const infoColor = '#17a2b8';
    
    // Graphique des moyennes par niveau
    if (document.getElementById('niveauxMoyenneChart')) {
        const niveauxMoyenneCtx = document.getElementById('niveauxMoyenneChart').getContext('2d');
        new Chart(niveauxMoyenneCtx, {
            type: 'bar',
            data: {
                labels: statsNiveauxCodes,
                datasets: [{
                    label: 'Moyenne générale',
                    data: statsNiveauxMoyennes,
                    backgroundColor: statsNiveauxMoyennes.map(val => val >= 10 ? successColor : dangerColor),
                    borderColor: statsNiveauxMoyennes.map(val => val >= 10 ? successColor : dangerColor),
                    borderWidth: 1,
                    borderRadius: 4
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
                                return `Moyenne: ${context.parsed.y.toFixed(2)}`;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: false,
                        min: Math.max(0, Math.min(...statsNiveauxMoyennes) - 1),
                        max: Math.max(...statsNiveauxMoyennes) + 1,
                        ticks: {
                            callback: function(value) {
                                return value.toFixed(1);
                            }
                        },
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    }
    
    // Graphique du taux de réussite par niveau
    if (document.getElementById('niveauxReussiteChart')) {
        const niveauxReussiteCtx = document.getElementById('niveauxReussiteChart').getContext('2d');
        new Chart(niveauxReussiteCtx, {
            type: 'bar',
            data: {
                labels: statsNiveauxCodes,
                datasets: [{
                    label: 'Taux de réussite',
                    data: statsNiveauxTauxReussite,
                    backgroundColor: statsNiveauxTauxReussite.map(val => 
                        val >= 70 ? successColor : (val >= 50 ? warningColor : dangerColor)
                    ),
                    borderColor: statsNiveauxTauxReussite.map(val => 
                        val >= 70 ? successColor : (val >= 50 ? warningColor : dangerColor)
                    ),
                    borderWidth: 1,
                    borderRadius: 4
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
                                return `Taux de réussite: ${context.parsed.y}%`;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100,
                        ticks: {
                            callback: function(value) {
                                return `${value}%`;
                            }
                        },
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    }
    
    // Graphique de distribution des notes
    if (document.getElementById('distributionChart')) {
        const distributionCtx = document.getElementById('distributionChart').getContext('2d');
        new Chart(distributionCtx, {
            type: 'doughnut',
            data: {
                labels: ['Excellent (≥16)', 'Bien (14-16)', 'Moyen (10-14)', 'Insuffisant (<10)'],
                datasets: [{
                    data: [
                        {{ $performanceStats['excellent'] ?? 0 }},
                        {{ $performanceStats['good'] ?? 0 }},
                        {{ $performanceStats['average'] ?? 0 }},
                        {{ $performanceStats['poor'] ?? 0 }}
                    ],
                    backgroundColor: [successColor, infoColor, warningColor, dangerColor],
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
                            padding: 15
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return `${context.label}: ${context.parsed}%`;
                            }
                        }
                    }
                },
                cutout: '60%'
            }
        });
    }
    
    // Graphique des retards et absences
    if (document.getElementById('absencesChart')) {
        const absencesCtx = document.getElementById('absencesChart').getContext('2d');
        new Chart(absencesCtx, {
            type: 'bar',
            data: {
                labels: classesData,
                datasets: [
                    {
                        label: 'Retards',
                        data: retardsData,
                        backgroundColor: infoColor,
                        borderColor: infoColor,
                        borderWidth: 1,
                        borderRadius: 4,
                        barPercentage: 0.7,
                        categoryPercentage: 0.7
                    },
                    {
                        label: 'Absences',
                        data: absencesData,
                        backgroundColor: dangerColor,
                        borderColor: dangerColor,
                        borderWidth: 1,
                        borderRadius: 4,
                        barPercentage: 0.7,
                        categoryPercentage: 0.7
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top'
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Nombre'
                        },
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    }
});
</script>
@endsection