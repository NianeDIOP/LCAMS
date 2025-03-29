@extends('layouts.app')

@section('title', 'Tableau de bord - Semestre 1')

@section('styles')
<style>
    /* Styles de base */
    :root {
        --primary: #0062cc;
        --success: #28a745;
        --info: #17a2b8;
        --warning: #ffc107;
        --danger: #dc3545;
        --light: #f8f9fa;
        --dark: #343a40;
    }
    
    /* Conteneur principal */
    .dashboard-container {
        background-color: #f5f8fa;
        padding: 1rem;
        border-radius: 0.5rem;
    }
    
    /* Panel latéral */
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
        color: var(--primary);
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
        color: var(--primary);
    }
    
    .sidebar-menu li a.active {
        background-color: rgba(0, 98, 204, 0.1);
        color: var(--primary);
        border-left: 3px solid var(--primary);
        font-weight: 600;
    }
    
    .sidebar-menu li a i {
        margin-right: 0.5rem;
        width: 20px;
        text-align: center;
    }

    /* Carte de statistiques */
    .stat-card {
        background-color: white;
        border-radius: 0.5rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        height: 100%;
        transition: transform 0.2s, box-shadow 0.2s;
        overflow: hidden;
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
    
    .stat-card-footer {
        padding: 0.75rem 1rem;
        border-top: 1px solid rgba(0, 0, 0, 0.05);
        font-size: 0.8rem;
        background-color: rgba(0, 0, 0, 0.02);
    }
    
    /* Sections */
    .section {
        margin-bottom: 1.5rem;
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
        color: var(--primary);
    }
    
    /* Filtres */
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
        color: var(--primary);
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
    
    /* Graphiques */
    .chart-container {
        position: relative;
        height: 280px;
        width: 100%;
    }
    
    /* Tables */
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
    
    /* Indicateurs de performance */
    .performance-indicator {
        display: flex;
        align-items: center;
        margin-bottom: 1rem;
    }
    
    .indicator-value {
        font-size: 1.5rem;
        font-weight: 700;
        margin-right: 0.75rem;
        min-width: 60px;
    }
    
    .indicator-details {
        flex: 1;
    }
    
    .indicator-label {
        font-size: 0.85rem;
        font-weight: 600;
        margin-bottom: 0.25rem;
    }
    
    .progress {
        height: 8px;
        border-radius: 4px;
        background-color: #e9ecef;
        margin-bottom: 0.25rem;
    }
    
    .progress-bar {
        border-radius: 4px;
    }
    
    .gauge-container {
        display: flex;
        flex-direction: column;
        align-items: center;
        margin-bottom: 0.5rem;
    }
    
    .gender-icons {
        display: flex;
        justify-content: center;
        gap: 2rem;
        margin: 0.75rem 0;
    }
    
    .gender-stat {
        display: flex;
        flex-direction: column;
        align-items: center;
    }
    
    .gender-icon {
        font-size: 1.5rem;
        margin-bottom: 0.25rem;
    }
    
    .gender-value {
        font-size: 1.25rem;
        font-weight: 600;
        margin-bottom: 0.125rem;
    }
    
    .gender-label {
        font-size: 0.75rem;
        color: #6c757d;
    }
    
    .male-color {
        color: #0062cc;
    }
    
    .female-color {
        color: #e83e8c;
    }
    
    /* Badges */
    .badge {
        font-weight: 500;
        padding: 0.35em 0.65em;
        font-size: 0.75em;
        border-radius: 0.25rem;
    }
    
    /* Pour les écrans plus petits */
    @media (max-width: 992px) {
        .sidebar {
            margin-bottom: 1.5rem;
        }
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
                <h1 class="mb-4 fs-4">Tableau de bord analytique - Semestre 1</h1>
                
                <!-- Section des filtres -->
                <div class="filter-container">
                    <h5 class="filter-title">
                        <i class="fas fa-filter"></i> Filtres
                    </h5>
                    
                    <form id="filterForm" action="{{ route('semestre1.dashboard') }}" method="GET">
                        <div class="row align-items-end">
                            <div class="col-md-2 mb-3">
                                <label for="niveau_id" class="form-label">Niveau</label>
                                <select class="form-select form-control-lg" id="niveau_id" name="niveau_id">
                                    <option value="">Tous les niveaux</option>
                                    @foreach($niveauxTous ?? [] as $niveau)
                                        <option value="{{ $niveau->id }}" {{ ($niveau_id ?? '') == $niveau->id ? 'selected' : '' }}>{{ $niveau->nom }}</option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div class="col-md-2 mb-3">
                                <label for="classe_id" class="form-label">Classe</label>
                                <select class="form-select form-control-lg" id="classe_id" name="classe_id">
                                    <option value="">Toutes les classes</option>
                                    @foreach($classes ?? [] as $classe)
                                        <option value="{{ $classe->id }}" {{ ($classe_id ?? '') == $classe->id ? 'selected' : '' }}>{{ $classe->nom }}</option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div class="col-md-2 mb-3">
                                <label for="sexe" class="form-label">Sexe</label>
                                <select class="form-select form-control-lg" id="sexe" name="sexe">
                                    <option value="">Tous</option>
                                    <option value="F" {{ (request('sexe') ?? '') == 'F' ? 'selected' : '' }}>Filles</option>
                                    <option value="G" {{ (request('sexe') ?? '') == 'G' ? 'selected' : '' }}>Garçons</option>
                                </select>
                            </div>
                            
                            <div class="col-md-2 mb-3">
                                <label for="min_moyenne" class="form-label">Moyenne min</label>
                                <input type="number" class="form-control form-control-lg" id="min_moyenne" name="min_moyenne" step="0.01" min="0" max="20" value="{{ $min_moyenne ?? '' }}">
                            </div>
                            
                            <div class="col-md-2 mb-3">
                                <label for="max_moyenne" class="form-label">Moyenne max</label>
                                <input type="number" class="form-control form-control-lg" id="max_moyenne" name="max_moyenne" step="0.01" min="0" max="20" value="{{ $max_moyenne ?? '' }}">
                            </div>
                            
                            <div class="col-md-2 mb-3 d-flex align-items-center">
                                <button type="submit" class="btn btn-primary h-100 me-1" style="width: 68%;">
                                    Appliquer
                                </button>
                                <a href="{{ route('semestre1.dashboard') }}" class="btn btn-outline-secondary h-100" style="width: 30%;">
                                    <i class="fas fa-redo"></i>
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
                
                <!-- Statistiques générales -->
                <div class="section">
                    <h5 class="section-title">
                        <i class="fas fa-chart-pie"></i> Statistiques générales
                    </h5>
                    
                    <div class="row">
                        <!-- Effectifs -->
                        <div class="col-md-3 mb-3">
                            <div class="stat-card">
                                <div class="stat-card-header">
                                    Effectif Total
                                </div>
                                <div class="stat-card-body">
                                    <div class="stat-card-value text-primary">{{ $totalEleves ?? 0 }}</div>
                                    <div class="stat-card-label">Élèves</div>
                                    
                                    <div class="gender-icons">
                                        <div class="gender-stat">
                                            <div class="gender-icon male-color">
                                                <i class="fas fa-male"></i>
                                            </div>
                                            <div class="gender-value male-color">{{ $garconsCount ?? 0 }}</div>
                                            <div class="gender-label">{{ $totalEleves > 0 ? round(($garconsCount / $totalEleves) * 100) : 0 }}%</div>
                                        </div>
                                        
                                        <div class="gender-stat">
                                            <div class="gender-icon female-color">
                                                <i class="fas fa-female"></i>
                                            </div>
                                            <div class="gender-value female-color">{{ $fillesCount ?? 0 }}</div>
                                            <div class="gender-label">{{ $totalEleves > 0 ? round(($fillesCount / $totalEleves) * 100) : 0 }}%</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Réussite -->
                        <div class="col-md-3 mb-3">
                            <div class="stat-card">
                                <div class="stat-card-header">
                                    Réussite (≥ 10)
                                </div>
                                <div class="stat-card-body">
                                    <div class="stat-card-value text-success">{{ $elevesAvecMoyenne ?? 0 }}</div>
                                    <div class="stat-card-label mb-3">Taux : <strong>{{ $tauxReussite ?? 0 }}%</strong></div>
                                    
                                    <div class="progress mb-3">
                                        <div class="progress-bar bg-success" role="progressbar" style="width: {{ $tauxReussite ?? 0 }}%" aria-valuenow="{{ $tauxReussite ?? 0 }}" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-6 border-end">
                                            <div class="small mb-1">Filles</div>
                                            <div class="fw-bold">{{ $fillesAvecMoyenne ?? 0 }}</div>
                                            <div class="small text-muted">
                                                {{ $fillesCount > 0 ? round(($fillesAvecMoyenne / $fillesCount) * 100) : 0 }}%
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="small mb-1">Garçons</div>
                                            <div class="fw-bold">{{ $garconsAvecMoyenne ?? 0 }}</div>
                                            <div class="small text-muted">
                                                {{ $garconsCount > 0 ? round(($garconsAvecMoyenne / $garconsCount) * 100) : 0 }}%
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Moyennes -->
                        <div class="col-md-3 mb-3">
                            <div class="stat-card">
                                <div class="stat-card-header">
                                    Moyennes
                                </div>
                                <div class="stat-card-body">
                                    <div class="stat-card-value text-primary">{{ number_format(($noteMoyenne ?? 0), 2) }}</div>
                                    <div class="stat-card-label">Moyenne générale</div>
                                    
                                    <div class="d-flex justify-content-between mt-3">
                                        <div class="text-center">
                                            <div class="small text-muted">Min</div>
                                            <div class="fw-bold text-danger">{{ number_format(($plusFaibleMoyenne ?? 0), 2) }}</div>
                                        </div>
                                        <div class="text-center">
                                            <div class="small text-muted">Moy</div>
                                            <div class="fw-bold text-primary">{{ number_format(($noteMoyenne ?? 0), 2) }}</div>
                                        </div>
                                        <div class="text-center">
                                            <div class="small text-muted">Max</div>
                                            <div class="fw-bold text-success">{{ number_format(($plusForteMoyenne ?? 0), 2) }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Mentions -->
                        <div class="col-md-3 mb-3">
                            <div class="stat-card">
                                <div class="stat-card-header">
                                    Mentions
                                </div>
                                <div class="stat-card-body">
                                    <div class="stat-card-value text-info">{{ ($felicitations ?? 0) + ($encouragements ?? 0) + ($tableauHonneur ?? 0) }}</div>
                                    <div class="stat-card-label">Élèves avec mention</div>
                                    
                                    <div class="d-flex justify-content-between mt-3">
                                        <div class="text-center">
                                            <div class="badge bg-danger">{{ $felicitations ?? 0 }}</div>
                                            <div class="small mt-1">Félicitations</div>
                                        </div>
                                        <div class="text-center">
                                            <div class="badge bg-warning text-dark">{{ $encouragements ?? 0 }}</div>
                                            <div class="small mt-1">Encouragements</div>
                                        </div>
                                        <div class="text-center">
                                            <div class="badge bg-info">{{ $tableauHonneur ?? 0 }}</div>
                                            <div class="small mt-1">Tab. d'honneur</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Graphiques de performance -->
                <div class="section">
                    <div class="row">
                        <!-- Graphique de distribution -->
                        <div class="col-md-6 mb-3">
                            <div class="stat-card h-100">
                                <div class="stat-card-header">
                                    Distribution des performances
                                </div>
                                <div class="stat-card-body">
                                    <div class="chart-container" style="height: 220px;">
                                        <canvas id="distributionChart"></canvas>
                                    </div>
                                    
                                    <div class="row mt-2 small">
                                        <div class="col-3 text-center">
                                            <span class="d-block fw-bold text-success">{{ $performanceStats['excellent'] ?? 0 }}%</span>
                                            <span>Excellent<br>(≥ 16)</span>
                                        </div>
                                        <div class="col-3 text-center">
                                            <span class="d-block fw-bold text-info">{{ $performanceStats['good'] ?? 0 }}%</span>
                                            <span>Bien<br>(14-16)</span>
                                        </div>
                                        <div class="col-3 text-center">
                                            <span class="d-block fw-bold text-warning">{{ $performanceStats['average'] ?? 0 }}%</span>
                                            <span>Moyen<br>(10-14)</span>
                                        </div>
                                        <div class="col-3 text-center">
                                            <span class="d-block fw-bold text-danger">{{ $performanceStats['poor'] ?? 0 }}%</span>
                                            <span>Insuffisant<br>(< 10)</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Indicateurs de performance -->
                        <div class="col-md-6 mb-3">
                            <div class="stat-card h-100">
                                <div class="stat-card-header">
                                    Indicateurs de performance
                                </div>
                                <div class="stat-card-body">
                                    <div class="performance-indicator">
                                        <div class="indicator-value text-primary">{{ number_format(($noteMoyenne ?? 0), 2) }}</div>
                                        <div class="indicator-details">
                                            <div class="indicator-label">Moyenne générale</div>
                                            <div class="progress">
                                                <div class="progress-bar bg-primary" style="width: {{ min(100, ($noteMoyenne ?? 0) * 5) }}%"></div>
                                            </div>
                                            <div class="d-flex justify-content-between small">
                                                <span>0</span>
                                                <span>10</span>
                                                <span>20</span>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="performance-indicator">
                                        <div class="indicator-value text-success">{{ number_format(($plusForteMoyenne ?? 0), 2) }}</div>
                                        <div class="indicator-details">
                                            <div class="indicator-label">Plus forte moyenne</div>
                                            <div class="progress">
                                                <div class="progress-bar bg-success" style="width: {{ min(100, ($plusForteMoyenne ?? 0) * 5) }}%"></div>
                                            </div>
                                            <div class="d-flex justify-content-between small">
                                                <span>0</span>
                                                <span>10</span>
                                                <span>20</span>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="performance-indicator">
                                        <div class="indicator-value text-danger">{{ number_format(($plusFaibleMoyenne ?? 0), 2) }}</div>
                                        <div class="indicator-details">
                                            <div class="indicator-label">Plus faible moyenne</div>
                                            <div class="progress">
                                                <div class="progress-bar bg-danger" style="width: {{ min(100, ($plusFaibleMoyenne ?? 0) * 5) }}%"></div>
                                            </div>
                                            <div class="d-flex justify-content-between small">
                                                <span>0</span>
                                                <span>10</span>
                                                <span>20</span>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="performance-indicator">
                                        <div class="indicator-value text-warning">{{ $tauxReussite ?? 0 }}%</div>
                                        <div class="indicator-details">
                                            <div class="indicator-label">Taux de réussite</div>
                                            <div class="progress">
                                                <div class="progress-bar bg-warning" style="width: {{ $tauxReussite ?? 0 }}%"></div>
                                            </div>
                                            <div class="d-flex justify-content-between small">
                                                <span>0%</span>
                                                <span>50%</span>
                                                <span>100%</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Statistiques détaillées -->
                <div class="section">
                    <div class="row">
                        <!-- Statistiques par niveau -->
                        <div class="col-md-7 mb-3">
                            <div class="table-container">
                                <table class="table stats-table mb-0">
                                    <thead>
                                        <tr>
                                            <th>Niveau</th>
                                            <th class="text-center">Effectif</th>
                                            <th class="text-center">Moyenne</th>
                                            <th class="text-center">Taux Réussite</th>
                                            <th class="text-center">Mentions</th>
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
                                                <div class="progress" style="height: 5px; width: 80px; margin: 0 auto;">
                                                    <div class="progress-bar {{ $niveau['taux_reussite'] >= 70 ? 'bg-success' : ($niveau['taux_reussite'] >= 50 ? 'bg-warning' : 'bg-danger') }}" 
                                                         style="width: {{ $niveau['taux_reussite'] }}%" 
                                                         title="{{ $niveau['taux_reussite'] }}%">
                                                    </div>
                                                </div>
                                                <small>{{ $niveau['taux_reussite'] }}%</small>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-danger me-1" title="Félicitations">F: {{ $niveau['felicitations'] }}</span>
                                                <span class="badge bg-warning text-dark me-1" title="Encouragements">E: {{ $niveau['encouragements'] }}</span>
                                                <span class="badge bg-info" title="Tableau d'honneur">T: {{ $niveau['tableau_honneur'] }}</span>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        
                        <!-- Observations et décisions -->
                        <div class="col-md-5 mb-3">
                            <div class="stat-card h-100">
                                <div class="stat-card-header">
                                    Observations et décisions
                                </div>
                                <div class="stat-card-body p-3">
                                    <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-2">
                                        <span>
                                            <i class="fas fa-trophy text-danger me-2"></i> Félicitations
                                        </span>
                                        <span>
                                            <strong>{{ $felicitations ?? 0 }}</strong>
                                            <span class="text-muted ms-1 small">
                                                ({{ $totalEleves > 0 ? round(($felicitations / $totalEleves) * 100) : 0 }}%)
                                            </span>
                                        </span>
                                    </div>
                                    
                                    <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-2">
                                        <span>
                                            <i class="fas fa-thumbs-up text-warning me-2"></i> Encouragements
                                        </span>
                                        <span>
                                            <strong>{{ $encouragements ?? 0 }}</strong>
                                            <span class="text-muted ms-1 small">
                                                ({{ $totalEleves > 0 ? round(($encouragements / $totalEleves) * 100) : 0 }}%)
                                            </span>
                                        </span>
                                    </div>
                                    
                                    <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-2">
                                        <span>
                                            <i class="fas fa-award text-info me-2"></i> Tableau d'honneur
                                        </span>
                                        <span>
                                            <strong>{{ $tableauHonneur ?? 0 }}</strong>
                                            <span class="text-muted ms-1 small">
                                                ({{ $totalEleves > 0 ? round(($tableauHonneur / $totalEleves) * 100) : 0 }}%)
                                            </span>
                                        </span>
                                    </div>
                                    
                                    <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-2">
                                        <span>
                                            <i class="fas fa-exclamation-circle text-warning me-2"></i> Peut mieux faire
                                        </span>
                                        <span>
                                            <strong>{{ $mieuxFaire ?? 0 }}</strong>
                                            <span class="text-muted ms-1 small">
                                                ({{ $totalEleves > 0 ? round(($mieuxFaire / $totalEleves) * 100) : 0 }}%)
                                            </span>
                                        </span>
                                    </div>
                                    
                                    <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-2">
                                        <span>
                                            <i class="fas fa-arrows-alt-v text-secondary me-2"></i> Doit continuer
                                        </span>
                                        <span>
                                            <strong>{{ $doitContinuer ?? 0 }}</strong>
                                            <span class="text-muted ms-1 small">
                                                ({{ $totalEleves > 0 ? round(($doitContinuer / $totalEleves) * 100) : 0 }}%)
                                            </span>
                                        </span>
                                    </div>
                                    
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span>
                                            <i class="fas fa-times-circle text-danger me-2"></i> Risque de redoublement
                                        </span>
                                        <span>
                                            <strong>{{ $risqueRedoubler ?? 0 }}</strong>
                                            <span class="text-muted ms-1 small">
                                                ({{ $totalEleves > 0 ? round(($risqueRedoubler / $totalEleves) * 100) : 0 }}%)
                                            </span>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Graphiques par niveau et absences -->
                <div class="section">
                    <div class="row">
                        <!-- Moyenne par niveau -->
                        <div class="col-md-6 mb-3">
                            <div class="stat-card">
                                <div class="stat-card-header">
                                    Moyenne par niveau
                                </div>
                                <div class="stat-card-body">
                                    <div class="chart-container">
                                        <canvas id="niveauxMoyenneChart"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Taux de réussite par niveau -->
                        <div class="col-md-6 mb-3">
                            <div class="stat-card">
                                <div class="stat-card-header">
                                    Taux de réussite par niveau
                                </div>
                                <div class="stat-card-body">
                                    <div class="chart-container">
                                        <canvas id="niveauxReussiteChart"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Section des graphiques manquants -->
                <div class="section" id="graphiques-feedback" style="display: none;">
                    <div class="alert alert-warning">
                        <h5 class="alert-heading"><i class="fas fa-exclamation-triangle me-2"></i>Attention : Données insuffisantes pour les graphiques</h5>
                        <p>Certains graphiques ne peuvent pas être affichés car les données sont insuffisantes ou ne correspondent pas aux critères de filtrage actuels.</p>
                        <p>Suggestions :</p>
                        <ul>
                            <li>Élargissez les critères de filtrage</li>
                            <li>Assurez-vous d'avoir importé suffisamment de données pour le semestre 1</li>
                            <li>Vérifiez que les fichiers importés contiennent bien les informations nécessaires pour les graphiques</li>
                        </ul>
                    </div>
                </div>
                
                <!-- Retards et absences -->
                <div class="section">
                    <h5 class="section-title">
                        <i class="fas fa-clock"></i> Retards et absences
                    </h5>
                    
                    <div class="row">
                        <div class="col-12">
                            <div class="stat-card">
                                <div class="stat-card-body">
                                    <div class="chart-container">
                                        <canvas id="absencesChart"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
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
                    
                    // Ajouter les classes
                    data.forEach(classe => {
                        const option = document.createElement('option');
                        option.value = classe.id;
                        option.textContent = classe.nom;
                        classeSelect.appendChild(option);
                    });
                    
                    // Si une classe était précédemment sélectionnée, essayer de la restaurer
                    const previousClasseId = "{{ $classe_id ?? '' }}";
                    if (previousClasseId) {
                        const option = classeSelect.querySelector(`option[value="${previousClasseId}"]`);
                        if (option) option.selected = true;
                    }
                    
                    // Réactiver le sélecteur de classe
                    classeSelect.disabled = false;
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
    
    // Notification des filtres actifs
    const urlParams = new URLSearchParams(window.location.search);
    const hasFilters = urlParams.has('niveau_id') || urlParams.has('classe_id') || 
                      urlParams.has('sexe') || urlParams.has('min_moyenne') || 
                      urlParams.has('max_moyenne');
    
    if (hasFilters) {
        // Ajouter une indication visuelle que des filtres sont actifs
        const filterContainer = document.querySelector('.filter-container');
        if (filterContainer) {
            const filterBadge = document.createElement('div');
            filterBadge.className = 'alert alert-info mt-2 mb-0 py-2';
            filterBadge.innerHTML = '<i class="fas fa-info-circle me-2"></i>Les données affichées sont filtrées selon les critères sélectionnés.';
            
            // Insérer après le formulaire
            const filterForm = document.getElementById('filterForm');
            if (filterForm) {
                filterForm.after(filterBadge);
            }
        }
    }
    
    // Configuration des couleurs
    const primaryColor = '#0062cc';
    const secondaryColor = '#6c757d';
    const successColor = '#28a745';
    const dangerColor = '#dc3545';
    const warningColor = '#ffc107';
    const infoColor = '#17a2b8';
    
    // IMPORTANT: S'assurer que les données existent avant de créer les graphiques
    // Convertir les chaînes JSON en objets JavaScript
    const statsNiveauxCodes = {{ json_encode(array_column($statsNiveaux ?? [], 'code') ?? []) }};
    const statsNiveauxMoyennes = {{ json_encode(array_map(function($n) { return floatval($n['moyenne']); }, $statsNiveaux ?? [])) }};
    const statsNiveauxTauxReussite = {{ json_encode(array_column($statsNiveaux ?? [], 'taux_reussite') ?? []) }};
    const classesData = {{ json_encode($classesData ?? []) }};
    const retardsData = {{ json_encode($retardsData ?? []) }};
    const absencesData = {{ json_encode($absencesData ?? []) }};
    
    console.log("Données pour les graphiques:", {
        niveauxCodes: statsNiveauxCodes,
        niveauxMoyennes: statsNiveauxMoyennes,
        niveauxReussite: statsNiveauxTauxReussite,
        classes: classesData,
        retards: retardsData,
        absences: absencesData
    });
    
    // 1. Graphique de distribution (ce graphique semble fonctionner correctement)
    if (document.getElementById('distributionChart')) {
        try {
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
                                padding: 15,
                                font: {
                                    size: 11
                                }
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
            console.log("Graphique de distribution créé avec succès");
        } catch (error) {
            console.error("Erreur lors de la création du graphique de distribution:", error);
        }
    }
    
    // 2. Graphique des moyennes par niveau
    if (document.getElementById('niveauxMoyenneChart') && statsNiveauxCodes && statsNiveauxCodes.length > 0) {
        try {
            const niveauxMoyenneCtx = document.getElementById('niveauxMoyenneChart').getContext('2d');
            new Chart(niveauxMoyenneCtx, {
                type: 'bar',
                data: {
                    labels: statsNiveauxCodes,
                    datasets: [{
                        label: 'Moyenne générale',
                        data: statsNiveauxMoyennes,
                        backgroundColor: statsNiveauxMoyennes.map(val => parseFloat(val) >= 10 ? successColor : dangerColor),
                        borderColor: statsNiveauxMoyennes.map(val => parseFloat(val) >= 10 ? successColor : dangerColor),
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
                                    return `Moyenne: ${parseFloat(context.parsed.y).toFixed(2)}`;
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 20,
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
            console.log("Graphique des moyennes par niveau créé avec succès");
        } catch (error) {
            console.error("Erreur lors de la création du graphique des moyennes par niveau:", error);
            // Afficher un message dans le conteneur du graphique
            const container = document.getElementById('niveauxMoyenneChart').parentNode;
            container.innerHTML = '<div class="alert alert-warning mt-3 mb-0">Impossible d\'afficher ce graphique avec les filtres actuels. Données insuffisantes.</div>';
        }
    } else if (document.getElementById('niveauxMoyenneChart')) {
        // Aucune donnée, afficher un message
        const container = document.getElementById('niveauxMoyenneChart').parentNode;
        container.innerHTML = '<div class="alert alert-warning mt-3 mb-0">Aucune donnée disponible pour ce graphique avec les filtres actuels.</div>';
    }
    
    // 3. Graphique du taux de réussite par niveau
    if (document.getElementById('niveauxReussiteChart') && statsNiveauxCodes && statsNiveauxCodes.length > 0) {
        try {
            const niveauxReussiteCtx = document.getElementById('niveauxReussiteChart').getContext('2d');
            new Chart(niveauxReussiteCtx, {
                type: 'bar',
                data: {
                    labels: statsNiveauxCodes,
                    datasets: [{
                        label: 'Taux de réussite',
                        data: statsNiveauxTauxReussite,
                        backgroundColor: statsNiveauxTauxReussite.map(val => 
                            parseInt(val) >= 70 ? successColor : (parseInt(val) >= 50 ? warningColor : dangerColor)
                        ),
                        borderColor: statsNiveauxTauxReussite.map(val => 
                            parseInt(val) >= 70 ? successColor : (parseInt(val) >= 50 ? warningColor : dangerColor)
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
            console.log("Graphique du taux de réussite par niveau créé avec succès");
        } catch (error) {
            console.error("Erreur lors de la création du graphique du taux de réussite par niveau:", error);
            // Afficher un message dans le conteneur du graphique
            const container = document.getElementById('niveauxReussiteChart').parentNode;
            container.innerHTML = '<div class="alert alert-warning mt-3 mb-0">Impossible d\'afficher ce graphique avec les filtres actuels. Données insuffisantes.</div>';
        }
    } else if (document.getElementById('niveauxReussiteChart')) {
        // Aucune donnée, afficher un message
        const container = document.getElementById('niveauxReussiteChart').parentNode;
        container.innerHTML = '<div class="alert alert-warning mt-3 mb-0">Aucune donnée disponible pour ce graphique avec les filtres actuels.</div>';
    }
    
    // 4. Graphique des retards et absences
    if (document.getElementById('absencesChart') && classesData && classesData.length > 0) {
        try {
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
            console.log("Graphique des retards et absences créé avec succès");
        } catch (error) {
            console.error("Erreur lors de la création du graphique des retards et absences:", error);
            // Afficher un message dans le conteneur du graphique
            const container = document.getElementById('absencesChart').parentNode;
            container.innerHTML = '<div class="alert alert-warning mt-3 mb-0">Impossible d\'afficher ce graphique avec les filtres actuels. Données insuffisantes.</div>';
        }
    } else if (document.getElementById('absencesChart')) {
        // Aucune donnée, afficher un message
        const container = document.getElementById('absencesChart').parentNode;
        container.innerHTML = '<div class="alert alert-warning mt-3 mb-0">Aucune donnée disponible pour ce graphique avec les filtres actuels.</div>';
    }
    
    // Ajouter des boutons d'exportation
    document.querySelector('.dashboard-container').insertAdjacentHTML('beforeend', `
        <div class="d-flex justify-content-end mt-4">
            <a href="{{ route('semestre1.dashboard', ['export' => 'pdf'] + request()->query()) }}" class="btn btn-danger me-2">
                <i class="fas fa-file-pdf me-1"></i> Exporter en PDF
            </a>
            <a href="{{ route('semestre1.dashboard', ['export' => 'excel'] + request()->query()) }}" class="btn btn-success me-2">
                <i class="fas fa-file-excel me-1"></i> Exporter en Excel
            </a>
            <button type="button" class="btn btn-primary" onclick="window.print()">
                <i class="fas fa-print me-1"></i> Imprimer
            </button>
        </div>
    `);
});
</script>
@endsection