@extends('layouts.module')

@section('title', 'Tableau de bord - Semestre 1')

@section('module-icon')
<i class="fas fa-calendar-alt me-2"></i>
@endsection

@section('module-title', 'Semestre 1')

@section('page-title', 'Tableau de bord')

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
    /* Styles communs basés sur index.blade.php */
    .dashboard-card {
        background-color: white;
        border-radius: 0.5rem;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        padding: 1.25rem;
        margin-bottom: 1.5rem;
    }
    
    .dashboard-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.5rem;
    }
    
    .dashboard-title {
        font-size: 1.25rem;
        font-weight: 600;
        color: #343a40;
        margin-bottom: 0;
    }
    
    .stats-card {
        background-color: white;
        border-radius: 0.5rem;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        padding: 1.25rem;
        margin-bottom: 1rem;
        text-align: center;
        height: 100%;
        transition: transform 0.2s, box-shadow 0.2s;
    }
    
    .stats-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    }
    
    .stats-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 50px;
        height: 50px;
        border-radius: 50%;
        margin-bottom: 1rem;
        font-size: 1.25rem;
    }
    
    .stats-value {
        font-size: 1.75rem;
        font-weight: 700;
        margin-bottom: 0.25rem;
    }
    
    .stats-label {
        font-size: 0.85rem;
        color: #6c757d;
    }
    
    .section-title {
        font-size: 1.25rem;
        font-weight: 600;
        margin-bottom: 1.25rem;
        color: #343a40;
    }
    
    .data-card {
        background-color: white;
        border-radius: 0.5rem;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        padding: 1.5rem;
        height: 100%;
        margin-bottom: 1.5rem;
    }
    
    .data-card-title {
        font-size: 1rem;
        font-weight: 600;
        margin-bottom: 1rem;
        color: #343a40;
        display: flex;
        align-items: center;
    }
    
    .data-card-title i {
        margin-right: 0.5rem;
        color: var(--primary);
    }
    
    .chart-container {
        height: 250px;
        position: relative;
        margin-bottom: 1rem;
    }
    
    /* Styles spécifiques pour le tableau de bord */
    .filter-form {
        display: flex;
        flex-wrap: wrap;
        gap: 0.75rem;
        margin-bottom: 1.5rem;
    }
    
    .filter-group {
        flex: 1;
        min-width: 180px;
    }
    
    .filter-buttons {
        display: flex;
        gap: 0.5rem;
        align-items: flex-end;
    }
    
    .btn-filter {
        white-space: nowrap;
    }
    
    .performance-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 1rem;
    }
    
    .indicator-card {
        display: flex;
        flex-direction: column;
        background-color: white;
        border-radius: 0.5rem;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        overflow: hidden;
        height: 100%;
    }
    
    .indicator-header {
        background-color: #f8f9fa;
        padding: 0.75rem 1rem;
        border-bottom: 1px solid #e9ecef;
        font-weight: 600;
        font-size: 0.9rem;
    }
    
    .indicator-body {
        padding: 1.25rem;
        text-align: center;
        flex-grow: 1;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }
    
    .indicator-value {
        font-size: 2rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
    }
    
    .indicator-label {
        font-size: 0.85rem;
        color: #6c757d;
    }
    
    .donut-chart {
        width: 180px;
        height: 180px;
        margin: 0 auto;
        position: relative;
    }
    
    .donut-center {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 120px;
        height: 120px;
        background: white;
        border-radius: 50%;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }
    
    .donut-percent {
        font-size: 2rem;
        font-weight: 700;
        line-height: 1;
        color: #ff9800;
    }
    
    .donut-label {
        font-size: 0.85rem;
        font-weight: 600;
        text-transform: uppercase;
    }
    
    .stats-table th {
        background-color: #f8f9fa;
        font-weight: 600;
        color: #495057;
    }
    
    .tableFixHead {
        overflow-y: auto;
        max-height: 400px;
    }
    
    .tableFixHead thead th {
        position: sticky;
        top: 0;
        z-index: 1;
        background-color: #f8f9fa;
    }
</style>
@endsection

@section('module-content')
    <!-- Titre et filtres -->
    <div class="dashboard-card">
        <div class="dashboard-header">
            <h2 class="dashboard-title">Tableau de bord des résultats du Semestre 1</h2>
            <button class="btn btn-primary btn-sm" type="button" data-bs-toggle="collapse" data-bs-target="#filterCollapse">
                <i class="fas fa-filter me-1"></i> Filtres
            </button>
        </div>
        
        <!-- Filtres -->
        <div class="collapse mb-3" id="filterCollapse">
            <form action="{{ route('semestre1.dashboard') }}" method="GET" class="filter-form">
                <div class="filter-group">
                    <label for="niveau_id" class="form-label">Niveau</label>
                    <select class="form-select form-select-sm" id="niveau_id" name="niveau_id">
                        <option value="">Tous les niveaux</option>
                        @foreach($niveauxTous ?? [] as $niveau)
                            <option value="{{ $niveau->id }}" {{ ($niveau_id ?? '') == $niveau->id ? 'selected' : '' }}>{{ $niveau->nom }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="filter-group">
                    <label for="classe_id" class="form-label">Classe</label>
                    <select class="form-select form-select-sm" id="classe_id" name="classe_id">
                        <option value="">Toutes les classes</option>
                        @foreach($classes ?? [] as $classe)
                            <option value="{{ $classe->id }}" {{ ($classe_id ?? '') == $classe->id ? 'selected' : '' }}>{{ $classe->nom }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="filter-group">
                    <label for="sexe" class="form-label">Sexe</label>
                    <select class="form-select form-select-sm" id="sexe" name="sexe">
                        <option value="">Tous</option>
                        <option value="F" {{ ($sexe ?? '') == 'F' ? 'selected' : '' }}>Filles</option>
                        <option value="M" {{ ($sexe ?? '') == 'M' ? 'selected' : '' }}>Garçons</option>
                    </select>
                </div>
                
                <div class="filter-group">
                    <label for="min_moyenne" class="form-label">Moyenne min</label>
                    <input type="number" class="form-control form-control-sm" id="min_moyenne" name="min_moyenne" step="0.01" min="0" max="20" value="{{ $min_moyenne ?? '' }}">
                </div>
                
                <div class="filter-group">
                    <label for="max_moyenne" class="form-label">Moyenne max</label>
                    <input type="number" class="form-control form-control-sm" id="max_moyenne" name="max_moyenne" step="0.01" min="0" max="20" value="{{ $max_moyenne ?? '' }}">
                </div>
                
                <div class="filter-buttons">
                    <button type="submit" class="btn btn-primary btn-sm btn-filter">
                        <i class="fas fa-search me-1"></i> Appliquer
                    </button>
                    <a href="{{ route('semestre1.dashboard') }}" class="btn btn-outline-secondary btn-sm btn-filter">
                        <i class="fas fa-undo me-1"></i> Réinitialiser
                    </a>
                    <button type="submit" name="export" value="1" class="btn btn-success btn-sm btn-filter">
                        <i class="fas fa-file-export me-1"></i> Exporter
                    </button>
                </div>
            </form>
        </div>
        
        <!-- Statistiques générales -->
        <div class="row">
            <div class="col-md-3 mb-3">
                <div class="stats-card">
                    <div class="stats-icon bg-primary-subtle text-primary">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stats-value text-primary">{{ $totalEleves ?? '0' }}</div>
                    <div class="stats-label">Total des élèves</div>
                </div>
            </div>
            
            <div class="col-md-3 mb-3">
                <div class="stats-card">
                    <div class="stats-icon bg-success-subtle text-success">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="stats-value text-success">{{ $elevesAvecMoyenne ?? '0' }}</div>
                    <div class="stats-label">Élèves admis (≥10)</div>
                </div>
            </div>
            
            <div class="col-md-3 mb-3">
                <div class="stats-card">
                    <div class="stats-icon bg-info-subtle text-info">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <div class="stats-value text-info">{{ number_format(($noteMoyenne ?? 0), 2) }}</div>
                    <div class="stats-label">Moyenne générale</div>
                </div>
            </div>
            
            <div class="col-md-3 mb-3">
                <div class="stats-card">
                    <div class="stats-icon bg-warning-subtle text-warning">
                        <i class="fas fa-percentage"></i>
                    </div>
                    <div class="stats-value text-warning">{{ $tauxReussite ?? '0' }}%</div>
                    <div class="stats-label">Taux de réussite</div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Répartition par Sexe et Taux de Réussite -->
    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="data-card">
                <h3 class="data-card-title">
                    <i class="fas fa-venus-mars"></i> Répartition par sexe
                </h3>
                
                <div class="row mb-3">
                    <div class="col-6 text-center">
                        <div class="stats-value text-primary">{{ $fillesCount ?? '0' }}</div>
                        <div class="stats-label">Filles</div>
                    </div>
                    <div class="col-6 text-center">
                        <div class="stats-value text-primary">{{ $garconsCount ?? '0' }}</div>
                        <div class="stats-label">Garçons</div>
                    </div>
                </div>
                
                <div class="donut-chart" id="genderDonut">
                    <div class="donut-center">
                        <div class="donut-percent">{{ $fillesCount ? round($fillesCount / $totalEleves * 100) : '0' }}%</div>
                        <div class="donut-label">Filles</div>
                    </div>
                </div>
                
                <div class="mt-3 text-center">
                    <div class="d-inline-block me-3">
                        <i class="fas fa-circle text-primary me-1"></i> Filles: {{ $fillesCount ? round($fillesCount / $totalEleves * 100) : '0' }}%
                    </div>
                    <div class="d-inline-block">
                        <i class="fas fa-circle text-secondary me-1"></i> Garçons: {{ $garconsCount ? round($garconsCount / $totalEleves * 100) : '0' }}%
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-6 mb-4">
            <div class="data-card">
                <h3 class="data-card-title">
                    <i class="fas fa-award"></i> Taux de réussite (≥10)
                </h3>
                
                <div class="row mb-3">
                    <div class="col-4 text-center">
                        <div class="stats-value text-success">{{ $elevesAvecMoyenne ?? '0' }}</div>
                        <div class="stats-label">Total</div>
                    </div>
                    <div class="col-4 text-center">
                        <div class="stats-value text-success">{{ $fillesAvecMoyenne ?? '0' }}</div>
                        <div class="stats-label">Filles</div>
                    </div>
                    <div class="col-4 text-center">
                        <div class="stats-value text-success">{{ $garconsAvecMoyenne ?? '0' }}</div>
                        <div class="stats-label">Garçons</div>
                    </div>
                </div>
                
                <div class="donut-chart" id="successRateDonut">
                    <div class="donut-center">
                        <div class="donut-percent">{{ $tauxReussite ?? '0' }}%</div>
                        <div class="donut-label">Taux</div>
                    </div>
                </div>
                
                <div class="mt-3 text-center">
                    <div class="d-inline-block me-3">
                        <i class="fas fa-circle text-success me-1"></i> Admis: {{ $tauxReussite ?? '0' }}%
                    </div>
                    <div class="d-inline-block">
                        <i class="fas fa-circle text-danger me-1"></i> Non-admis: {{ 100 - ($tauxReussite ?? 0) }}%
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Indicateurs de performance -->
    <h2 class="section-title">Indicateurs de performance</h2>
    
    <div class="row mb-4">
        <div class="col-md-7">
            <div class="data-card h-100">
                <h3 class="data-card-title">
                    <i class="fas fa-chart-bar"></i> Moyennes générales par niveau
                </h3>
                
                <div class="chart-container">
                    <canvas id="niveauxChart"></canvas>
                </div>
                
                <div class="tableFixHead">
                    <table class="table table-hover table-sm mb-0 stats-table">
                        <thead>
                            <tr>
                                <th>Niveau</th>
                                <th class="text-center">Moyenne</th>
                                <th class="text-center">Effectif</th>
                                <th class="text-center">Taux réussite</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($statsNiveaux ?? [] as $niveau)
                            <tr>
                                <td>{{ $niveau['nom'] }}</td>
                                <td class="text-center">{{ $niveau['moyenne'] }}</td>
                                <td class="text-center">{{ $niveau['effectif'] }}</td>
                                <td class="text-center">{{ $niveau['taux_reussite'] }}%</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <div class="col-md-5">
            <div class="data-card h-100">
                <h3 class="data-card-title">
                    <i class="fas fa-trophy"></i> Distinctions et observations
                </h3>
                
                <div class="performance-grid">
                    <div class="indicator-card">
                        <div class="indicator-header">Félicitations</div>
                        <div class="indicator-body">
                            <div class="indicator-value text-primary">{{ $felicitations ?? '0' }}</div>
                        </div>
                    </div>
                    
                    <div class="indicator-card">
                        <div class="indicator-header">Encouragements</div>
                        <div class="indicator-body">
                            <div class="indicator-value text-info">{{ $encouragements ?? '0' }}</div>
                        </div>
                    </div>
                    
                    <div class="indicator-card">
                        <div class="indicator-header">Tableau d'honneur</div>
                        <div class="indicator-body">
                            <div class="indicator-value text-success">{{ $tableauHonneur ?? '0' }}</div>
                        </div>
                    </div>
                    
                    <div class="indicator-card">
                        <div class="indicator-header">Mieux faire</div>
                        <div class="indicator-body">
                            <div class="indicator-value text-warning">{{ $mieuxFaire ?? '0' }}</div>
                        </div>
                    </div>
                    
                    <div class="indicator-card">
                        <div class="indicator-header">Doit continuer</div>
                        <div class="indicator-body">
                            <div class="indicator-value text-secondary">{{ $doitContinuer ?? '0' }}</div>
                        </div>
                    </div>
                    
                    <div class="indicator-card">
                        <div class="indicator-header">Risque d'échec</div>
                        <div class="indicator-body">
                            <div class="indicator-value text-danger">{{ $risqueRedoubler ?? '0' }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Absences et Retards -->
    <div class="data-card">
        <h3 class="data-card-title">
            <i class="fas fa-clock"></i> Retards et absences par classe
        </h3>
        
        <div class="chart-container">
            <canvas id="absencesChart"></canvas>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Donut Chart pour la répartition par sexe
        const genderCanvas = document.createElement('canvas');
        genderCanvas.width = 180;
        genderCanvas.height = 180;
        document.getElementById('genderDonut').appendChild(genderCanvas);
        
        new Chart(genderCanvas, {
            type: 'doughnut',
            data: {
                labels: ['Filles', 'Garçons'],
                datasets: [{
                    data: [
                        {{ $fillesCount ?? 0 }}, 
                        {{ $garconsCount ?? 0 }}
                    ],
                    backgroundColor: [
                        '#0d6efd',
                        '#6c757d'
                    ],
                    borderWidth: 0,
                    hoverOffset: 4
                }]
            },
            options: {
                cutout: '70%',
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        enabled: true
                    }
                },
                animation: {
                    animateRotate: true,
                    animateScale: true
                },
                responsive: true,
                maintainAspectRatio: false
            }
        });
        
        // Donut Chart pour le taux de réussite
        const successCanvas = document.createElement('canvas');
        successCanvas.width = 180;
        successCanvas.height = 180;
        document.getElementById('successRateDonut').appendChild(successCanvas);
        
        new Chart(successCanvas, {
            type: 'doughnut',
            data: {
                labels: ['Admis', 'Non-admis'],
                datasets: [{
                    data: [
                        {{ $tauxReussite ?? 0 }}, 
                        {{ 100 - ($tauxReussite ?? 0) }}
                    ],
                    backgroundColor: [
                        '#198754',
                        '#dc3545'
                    ],
                    borderWidth: 0,
                    hoverOffset: 4
                }]
            },
            options: {
                cutout: '70%',
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        enabled: true
                    }
                },
                animation: {
                    animateRotate: true,
                    animateScale: true
                },
                responsive: true,
                maintainAspectRatio: false
            }
        });
        
        // Chart des moyennes par niveau
        const niveauxCtx = document.getElementById('niveauxChart').getContext('2d');
        const niveauxChart = new Chart(niveauxCtx, {
            type: 'line',
            data: {
                labels: @json($statsNiveaux ? array_column($statsNiveaux, 'code') : []),
                datasets: [{
                    label: 'Moyenne',
                    data: @json($statsNiveaux ? array_map(function($n) { return floatval($n['moyenne']); }, $statsNiveaux) : []),
                    backgroundColor: 'rgba(13, 110, 253, 0.2)',
                    borderColor: '#0d6efd',
                    borderWidth: 2,
                    tension: 0.3,
                    fill: true,
                    pointBackgroundColor: '#0d6efd',
                    pointRadius: 4
                }, {
                    label: 'Taux de réussite (%)',
                    data: @json($statsNiveaux ? array_column($statsNiveaux, 'taux_reussite') : []),
                    backgroundColor: 'rgba(25, 135, 84, 0)',
                    borderColor: '#198754',
                    borderWidth: 2,
                    borderDash: [5, 5],
                    tension: 0.3,
                    fill: false,
                    pointBackgroundColor: '#198754',
                    pointRadius: 4,
                    yAxisID: 'y1'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: false,
                        min: 8,
                        max: 16,
                        title: {
                            display: true,
                            text: 'Moyenne'
                        }
                    },
                    y1: {
                        position: 'right',
                        beginAtZero: true,
                        max: 100,
                        title: {
                            display: true,
                            text: 'Taux de réussite (%)'
                        },
                        grid: {
                            drawOnChartArea: false
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
        
        // Graphique des absences et retards
        const absencesCtx = document.getElementById('absencesChart').getContext('2d');
        const absencesChart = new Chart(absencesCtx, {
            type: 'bar',
            data: {
                labels: @json($classesData ?? []),
                datasets: [{
                    label: 'Retards',
                    data: @json($retardsData ?? []),
                    backgroundColor: 'rgba(13, 110, 253, 0.7)',
                    borderColor: 'rgba(13, 110, 253, 1)',
                    borderWidth: 1,
                    borderRadius: 4,
                    barPercentage: 0.6,
                    categoryPercentage: 0.7
                }, {
                    label: 'Absences',
                    data: @json($absencesData ?? []),
                    backgroundColor: 'rgba(255, 193, 7, 0.7)',
                    borderColor: 'rgba(255, 193, 7, 1)',
                    borderWidth: 1,
                    borderRadius: 4,
                    barPercentage: 0.6,
                    categoryPercentage: 0.7
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Nombre'
                        }
                    }
                }
            }
        });
    });
</script>
@endsection