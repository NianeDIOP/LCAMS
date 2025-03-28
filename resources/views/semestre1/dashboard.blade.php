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
    /* Style général du dashboard */
    .dashboard-container {
        display: grid;
        grid-template-columns: repeat(12, 1fr);
        gap: 0.75rem;
    }
    
    /* Style des cartes */
    .dashboard-card {
        background: white;
        border-radius: 0.5rem;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        padding: 1rem;
        display: flex;
        flex-direction: column;
    }
    
    /* Style spécifique pour les cartes de statistiques */
    .stat-card {
        text-align: center;
        transition: transform 0.2s;
    }
    
    .stat-card:hover {
        transform: translateY(-3px);
    }
    
    .stat-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        margin-bottom: 0.75rem;
        font-size: 1.1rem;
    }
    
    .stat-value {
        font-size: 1.5rem;
        font-weight: 600;
        margin-bottom: 0.25rem;
    }
    
    .stat-label {
        font-size: 0.8rem;
        color: #6c757d;
    }
    
    /* Style des sections du dashboard */
    .dashboard-section {
        margin-bottom: 0.75rem;
    }
    
    .section-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 0.75rem;
        font-weight: 600;
        font-size: 0.9rem;
    }
    
    /* Style des filtres fixes */
    .filter-container {
        background: white;
        border-radius: 0.5rem;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        margin-bottom: 0.75rem;
        padding: 1rem;
        display: flex;
        flex-wrap: wrap;
        align-items: flex-end;
        gap: 0.75rem;
    }
    
    .filter-group {
        flex: 1;
        min-width: 120px;
    }
    
    .filter-group label {
        font-size: 0.8rem;
        margin-bottom: 0.25rem;
        display: block;
    }
    
    .filter-buttons {
        display: flex;
        gap: 0.5rem;
    }
    
    /* Indicateurs de performance */
    .performance-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
        gap: 0.75rem;
    }
    
    .indicator-card {
        padding: 0.75rem;
        text-align: center;
        border-radius: 0.5rem;
        background: white;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
    }
    
    .indicator-value {
        font-size: 1.5rem;
        font-weight: 600;
        margin-bottom: 0.25rem;
    }
    
    .indicator-label {
        font-size: 0.7rem;
        color: #6c757d;
    }
    
    /* Graphiques */
    .chart-container {
        width: 100%;
        height: 220px;
        position: relative;
    }
    
    /* Styles pour les tables */
    .stats-table {
        font-size: 0.8rem;
        width: 100%;
    }
    
    .stats-table th {
        background-color: #f8f9fa;
        font-weight: 600;
        padding: 0.5rem;
    }
    
    .stats-table td {
        padding: 0.5rem;
        vertical-align: middle;
    }
    
    .table-container {
        max-height: 220px;
        overflow-y: auto;
    }
    
    /* Styles pour les appareils mobiles */
    @media (max-width: 768px) {
        .dashboard-container {
            grid-template-columns: 1fr;
        }
        
        .filter-group {
            min-width: 100%;
        }
    }
    
    /* Styles spécifiques pour chaque section */
    .full-width {
        grid-column: span 12;
    }
    
    .half-width {
        grid-column: span 6;
    }
    
    .quarter-width {
        grid-column: span 3;
    }
    
    .third-width {
        grid-column: span 4;
    }
    
    .two-thirds-width {
        grid-column: span 8;
    }
    
    @media (max-width: 1200px) {
        .half-width {
            grid-column: span 12;
        }
        
        .quarter-width {
            grid-column: span 6;
        }
        
        .third-width {
            grid-column: span 6;
        }
        
        .two-thirds-width {
            grid-column: span 12;
        }
    }
    
    @media (max-width: 768px) {
        .quarter-width {
            grid-column: span 12;
        }
        
        .third-width {
            grid-column: span 12;
        }
    }
    
    /* Donut chart au centre */
    .donut-chart {
        position: relative;
        width: 140px;
        height: 140px;
        margin: 0 auto;
    }
    
    .donut-center {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        text-align: center;
        width: 90px;
        height: 90px;
        background: white;
        border-radius: 50%;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }
    
    .donut-percent {
        font-size: 1.5rem;
        font-weight: 700;
        line-height: 1;
    }
    
    .donut-label {
        font-size: 0.7rem;
        font-weight: 500;
    }
</style>
@endsection

@section('module-content')
    <!-- Filtres fixes en haut -->
    <div class="filter-container">
        <form id="dashboardFilterForm" action="{{ route('semestre1.dashboard') }}" method="GET" class="w-100 d-flex flex-wrap align-items-end gap-3">
            <div class="filter-group">
                <label for="filter_type">Type de filtre</label>
                <select class="form-select form-select-sm" id="filter_type" name="filter_type">
                    <option value="all" {{ ($filterType ?? 'all') == 'all' ? 'selected' : '' }}>Tous les élèves</option>
                    <option value="niveau" {{ ($filterType ?? '') == 'niveau' ? 'selected' : '' }}>Par niveau</option>
                    <option value="classe" {{ ($filterType ?? '') == 'classe' ? 'selected' : '' }}>Par classe</option>
                    <option value="interval" {{ ($filterType ?? '') == 'interval' ? 'selected' : '' }}>Par intervalle de moy.</option>
                </select>
            </div>
            
            <div class="filter-group niveau-filter" style="{{ ($filterType ?? '') != 'niveau' && ($filterType ?? '') != 'classe' ? 'display:none' : '' }}">
                <label for="niveau_id">Niveau</label>
                <select class="form-select form-select-sm" id="niveau_id" name="niveau_id" {{ ($filterType ?? '') != 'niveau' && ($filterType ?? '') != 'classe' ? 'disabled' : '' }}>
                    <option value="">Sélectionner un niveau</option>
                    @foreach($niveauxTous ?? [] as $niveau)
                        <option value="{{ $niveau->id }}" {{ ($niveau_id ?? '') == $niveau->id ? 'selected' : '' }}>{{ $niveau->nom }}</option>
                    @endforeach
                </select>
            </div>
            
            <div class="filter-group classe-filter" style="{{ ($filterType ?? '') != 'classe' ? 'display:none' : '' }}">
                <label for="classe_id">Classe</label>
                <select class="form-select form-select-sm" id="classe_id" name="classe_id" {{ ($filterType ?? '') != 'classe' ? 'disabled' : '' }}>
                    <option value="">Sélectionner une classe</option>
                    @foreach($classes ?? [] as $classe)
                        <option value="{{ $classe->id }}" {{ ($classe_id ?? '') == $classe->id ? 'selected' : '' }}>{{ $classe->nom }}</option>
                    @endforeach
                </select>
            </div>
            
            <div class="filter-group interval-filter" style="{{ ($filterType ?? '') != 'interval' ? 'display:none' : '' }}">
                <label for="min_moyenne">Moyenne min.</label>
                <input type="number" class="form-control form-control-sm" id="min_moyenne" name="min_moyenne" step="0.01" min="0" max="20" value="{{ $min_moyenne ?? '' }}" {{ ($filterType ?? '') != 'interval' ? 'disabled' : '' }}>
            </div>
            
            <div class="filter-group interval-filter" style="{{ ($filterType ?? '') != 'interval' ? 'display:none' : '' }}">
                <label for="max_moyenne">Moyenne max.</label>
                <input type="number" class="form-control form-control-sm" id="max_moyenne" name="max_moyenne" step="0.01" min="0" max="20" value="{{ $max_moyenne ?? '' }}" {{ ($filterType ?? '') != 'interval' ? 'disabled' : '' }}>
            </div>
            
            <div class="filter-buttons">
                <button type="submit" class="btn btn-sm btn-primary">
                    <i class="fas fa-filter me-1"></i> Appliquer
                </button>
                <a href="{{ route('semestre1.dashboard') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="fas fa-undo me-1"></i> Réinitialiser
                </a>
                <button type="submit" name="export" value="1" class="btn btn-sm btn-success">
                    <i class="fas fa-file-export me-1"></i> Exporter
                </button>
            </div>
        </form>
    </div>
    
    <!-- Contenu du dashboard -->
    <div class="dashboard-container">
        <!-- Section des statistiques générales - 4 cartes en ligne -->
        <div class="quarter-width">
            <div class="dashboard-card stat-card h-100">
                <div class="stat-icon bg-primary-subtle text-primary">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-value text-primary">{{ $totalEleves ?? '0' }}</div>
                <div class="stat-label">Total des élèves</div>
            </div>
        </div>
        
        <div class="quarter-width">
            <div class="dashboard-card stat-card h-100">
                <div class="stat-icon bg-success-subtle text-success">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-value text-success">{{ $tauxReussite ?? '0' }}%</div>
                <div class="stat-label">Taux de réussite ≥10</div>
            </div>
        </div>
        
        <div class="quarter-width">
            <div class="dashboard-card stat-card h-100">
                <div class="stat-icon bg-info-subtle text-info">
                    <i class="fas fa-chart-line"></i>
                </div>
                <div class="stat-value text-info">{{ number_format(($noteMoyenne ?? 0), 2) }}</div>
                <div class="stat-label">Moyenne générale</div>
            </div>
        </div>
        
        <div class="quarter-width">
            <div class="dashboard-card stat-card h-100">
                <div class="d-flex justify-content-between">
                    <div class="text-center w-50">
                        <div class="small fw-bold">Min</div>
                        <div class="h4 mb-0">{{ number_format(($plusFaibleMoyenne ?? 0), 2) }}</div>
                    </div>
                    <div class="text-center w-50">
                        <div class="small fw-bold">Max</div>
                        <div class="h4 mb-0">{{ number_format(($plusForteMoyenne ?? 0), 2) }}</div>
                    </div>
                </div>
                <div class="stat-label mt-2">Notes extrêmes</div>
            </div>
        </div>
        
        <!-- Répartition par sexe -->
        <div class="third-width">
            <div class="dashboard-card h-100">
                <div class="section-header">
                    <span><i class="fas fa-venus-mars me-1"></i> Répartition par sexe</span>
                </div>
                
                <div class="donut-chart" id="genderDonutContainer">
                    <canvas id="genderDonut"></canvas>
                    <div class="donut-center">
                        <div class="donut-percent">{{ $fillesCount ? round($fillesCount / ($totalEleves ?: 1) * 100) : '0' }}%</div>
                        <div class="donut-label">Filles</div>
                    </div>
                </div>
                
                <div class="d-flex justify-content-around mt-3">
                    <div class="text-center">
                        <div class="h5 mb-0">{{ $fillesCount ?? '0' }}</div>
                        <div class="small text-muted">Filles</div>
                    </div>
                    <div class="text-center">
                        <div class="h5 mb-0">{{ $garconsCount ?? '0' }}</div>
                        <div class="small text-muted">Garçons</div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Répartition des admis -->
        <div class="third-width">
            <div class="dashboard-card h-100">
                <div class="section-header">
                    <span><i class="fas fa-award me-1"></i> Élèves admis (≥10)</span>
                </div>
                
                <div class="donut-chart" id="successRateContainer">
                    <canvas id="successRateDonut"></canvas>
                    <div class="donut-center">
                        <div class="donut-percent">{{ $tauxReussite ?? '0' }}%</div>
                        <div class="donut-label">Taux</div>
                    </div>
                </div>
                
                <div class="d-flex justify-content-around mt-3">
                    <div class="text-center">
                        <div class="h5 mb-0">{{ $elevesAvecMoyenne ?? '0' }}</div>
                        <div class="small text-muted">Total admis</div>
                    </div>
                    <div class="text-center">
                        <div class="h5 mb-0">{{ $totalEleves - ($elevesAvecMoyenne ?? 0) }}</div>
                        <div class="small text-muted">Non admis</div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Répartition par sexe des admis -->
        <div class="third-width">
            <div class="dashboard-card h-100">
                <div class="section-header">
                    <span><i class="fas fa-user-graduate me-1"></i> Admis par sexe</span>
                </div>
                
                <div class="chart-container" style="height: 180px;">
                    <canvas id="admisParSexeChart"></canvas>
                </div>
                
                <div class="d-flex justify-content-around mt-2">
                    <div class="text-center">
                        <div class="h5 mb-0">{{ $fillesAvecMoyenne ?? '0' }}</div>
                        <div class="small text-muted">Filles ≥10</div>
                    </div>
                    <div class="text-center">
                        <div class="h5 mb-0">{{ $garconsAvecMoyenne ?? '0' }}</div>
                        <div class="small text-muted">Garçons ≥10</div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Statistiques par niveau -->
        <div class="half-width">
            <div class="dashboard-card h-100">
                <div class="section-header">
                    <span><i class="fas fa-layer-group me-1"></i> Performances par niveau</span>
                </div>
                
                <div class="table-container">
                    <table class="stats-table table-hover">
                        <thead>
                            <tr>
                                <th>Niveau</th>
                                <th class="text-center">Effectif</th>
                                <th class="text-center">Moyenne</th>
                                <th class="text-center">Taux Réussite</th>
                                <th class="text-center">Félicitations</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($statsNiveaux ?? [] as $niveau)
                            <tr>
                                <td>{{ $niveau['nom'] }}</td>
                                <td class="text-center">{{ $niveau['effectif'] }}</td>
                                <td class="text-center">{{ $niveau['moyenne'] }}</td>
                                <td class="text-center">{{ $niveau['taux_reussite'] }}%</td>
                                <td class="text-center">{{ $niveau['felicitations'] }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <!-- Graphique des moyennes par niveau -->
        <div class="half-width">
            <div class="dashboard-card h-100">
                <div class="section-header">
                    <span><i class="fas fa-chart-bar me-1"></i> Moyennes par niveau</span>
                </div>
                
                <div class="chart-container">
                    <canvas id="niveauxChart"></canvas>
                </div>
            </div>
        </div>
        
        <!-- Mentions et observations -->
        <div class="third-width">
            <div class="dashboard-card h-100">
                <div class="section-header">
                    <span><i class="fas fa-trophy me-1"></i> Mentions</span>
                </div>
                
                <div class="performance-grid">
                    <div class="indicator-card bg-primary-subtle">
                        <div class="indicator-value text-primary">{{ $felicitations ?? '0' }}</div>
                        <div class="indicator-label">Félicitations</div>
                    </div>
                    
                    <div class="indicator-card bg-info-subtle">
                        <div class="indicator-value text-info">{{ $encouragements ?? '0' }}</div>
                        <div class="indicator-label">Encouragements</div>
                    </div>
                    
                    <div class="indicator-card bg-success-subtle">
                        <div class="indicator-value text-success">{{ $tableauHonneur ?? '0' }}</div>
                        <div class="indicator-label">Tableau d'honneur</div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Observations -->
        <div class="third-width">
            <div class="dashboard-card h-100">
                <div class="section-header">
                    <span><i class="fas fa-comment me-1"></i> Observations</span>
                </div>
                
                <div class="performance-grid">
                    <div class="indicator-card bg-warning-subtle">
                        <div class="indicator-value text-warning">{{ $mieuxFaire ?? '0' }}</div>
                        <div class="indicator-label">Peut mieux faire</div>
                    </div>
                    
                    <div class="indicator-card bg-secondary-subtle">
                        <div class="indicator-value text-secondary">{{ $doitContinuer ?? '0' }}</div>
                        <div class="indicator-label">Doit continuer</div>
                    </div>
                    
                    <div class="indicator-card bg-danger-subtle">
                        <div class="indicator-value text-danger">{{ $risqueRedoubler ?? '0' }}</div>
                        <div class="indicator-label">Risque d'échec</div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Absences et retards -->
        <div class="third-width">
            <div class="dashboard-card h-100">
                <div class="section-header">
                    <span><i class="fas fa-clock me-1"></i> Absences & Retards</span>
                </div>
                
                <div class="chart-container">
                    <canvas id="absencesChart"></canvas>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Gestion des filtres dynamiques
    const filterType = document.getElementById('filter_type');
    const niveauFilter = document.querySelectorAll('.niveau-filter');
    const classeFilter = document.querySelectorAll('.classe-filter');
    const intervalFilter = document.querySelectorAll('.interval-filter');
    const niveauSelect = document.getElementById('niveau_id');
    const classeSelect = document.getElementById('classe_id');
    const minMoyenneInput = document.getElementById('min_moyenne');
    const maxMoyenneInput = document.getElementById('max_moyenne');
    
    filterType.addEventListener('change', function() {
        // Réinitialiser tous les filtres
        niveauFilter.forEach(el => el.style.display = 'none');
        classeFilter.forEach(el => el.style.display = 'none');
        intervalFilter.forEach(el => el.style.display = 'none');
        
        niveauSelect.disabled = true;
        classeSelect.disabled = true;
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
        } else if (this.value === 'interval') {
            intervalFilter.forEach(el => el.style.display = 'block');
            minMoyenneInput.disabled = false;
            maxMoyenneInput.disabled = false;
        }
    });
    
    // Donut Chart pour la répartition par sexe
    const genderCtx = document.getElementById('genderDonut').getContext('2d');
    new Chart(genderCtx, {
        type: 'doughnut',
        data: {
            labels: ['Filles', 'Garçons'],
            datasets: [{
                data: [
                    fillesCount, 
                    garconsCount
                ],
                backgroundColor: [
                    '#d63384',
                    '#0d6efd'
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
            responsive: true,
            maintainAspectRatio: false
        }
    });
    
    // Donut Chart pour le taux de réussite
    const successRateCtx = document.getElementById('successRateDonut').getContext('2d');
    new Chart(successRateCtx, {
        type: 'doughnut',
        data: {
            labels: ['Admis', 'Non-admis'],
            datasets: [{
                data: [
                    tauxReussite, 
                    100 - tauxReussite
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
            responsive: true,
            maintainAspectRatio: false
        }
    });
    
    // Graphique des admis par sexe
    const admisParSexeCtx = document.getElementById('admisParSexeChart').getContext('2d');
    new Chart(admisParSexeCtx, {
        type: 'bar',
        data: {
            labels: ['Filles', 'Garçons'],
            datasets: [{
                label: 'Admis (≥10)',
                data: [
                    fillesAvecMoyenne,
                    garconsAvecMoyenne
                ],
                backgroundColor: [
                    '#d63384',
                    '#0d6efd'
                ],
                borderWidth: 0
            }]
        },
        options: {
            indexAxis: 'y',
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                x: {
                    beginAtZero: true,
                    grid: {
                        display: false
                    }
                },
                y: {
                    grid: {
                        display: false
                    }
                }
            },
            responsive: true,
            maintainAspectRatio: false
        }
    });
    
    // Graphique des moyennes par niveau
    const niveauxCtx = document.getElementById('niveauxChart').getContext('2d');
    new Chart(niveauxCtx, {
        type: 'line',
        data: {
            labels: statsNiveauxCodes,
            datasets: [{
                label: 'Moyenne',
                data: statsNiveauxMoyennes,
                backgroundColor: 'rgba(13, 110, 253, 0.2)',
                borderColor: '#0d6efd',
                borderWidth: 2,
                tension: 0.3,
                fill: true,
                pointBackgroundColor: '#0d6efd',
                pointRadius: 4
            }, {
                label: 'Taux de réussite (%)',
                data: statsNiveauxTauxReussite,
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
            scales: {
                y: {
                    beginAtZero: false,
                    min: 8,
                    max: 16,
                    title: {
                        display: true,
                        text: 'Moyenne',
                        font: {
                            size: 10
                        }
                    },
                    ticks: {
                        font: {
                            size: 9
                        }
                    }
                },
                y1: {
                    position: 'right',
                    beginAtZero: true,
                    max: 100,
                    title: {
                        display: true,
                        text: 'Taux de réussite (%)',
                        font: {
                            size: 10
                        }
                    },
                    grid: {
                        drawOnChartArea: false
                    },
                    ticks: {
                        font: {
                            size: 9
                        }
                    }
                },
                x: {
                    grid: {
                        display: false
                    },
                    ticks: {
                        font: {
                            size: 9
                        }
                    }
                }
            },
            plugins: {
                legend: {
                    position: 'top',
                    labels: {
                        font: {
                            size: 10
                        }
                    }
                }
            }
        }
    });
    
    // Graphique des absences et retards
    const absencesCtx = document.getElementById('absencesChart').getContext('2d');
    new Chart(absencesCtx, {
        type: 'bar',
        data: {
            labels: classesData,
            datasets: [{
                label: 'Retards',
                data: retardsData,
                backgroundColor: 'rgba(13, 110, 253, 0.7)',
                borderColor: 'rgba(13, 110, 253, 1)',
                borderWidth: 1,
                borderRadius: 4,
                barPercentage: 0.6,
                categoryPercentage: 0.7
            }, {
                label: 'Absences',
                data: absencesData,
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
                    position: 'top',
                    labels: {
                        font: {
                            size: 10
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: false
                    },
                    ticks: {
                        font: {
                            size: 9
                        }
                    }
                },
                x: {
                    ticks: {
                        font: {
                            size: 8
                        },
                        maxRotation: 45,
                        minRotation: 45
                    }
                }
            }
        }
    });
    
    // Ajout d'event listeners pour charger les classes lorsqu'un niveau est sélectionné
    document.getElementById('niveau_id').addEventListener('change', function() {
        const niveauId = this.value;
        const classeSelect = document.getElementById('classe_id');
        const filterType = document.getElementById('filter_type').value;
        
        // Réinitialiser le select des classes
        classeSelect.innerHTML = '<option value="">Sélectionner une classe</option>';
        
        if (niveauId && (filterType === 'classe')) {
            // Activer le select des classes
            classeSelect.disabled = false;
            
            // Récupérer les classes du niveau via AJAX
            fetch(`/semestre1/classes-by-niveau/${niveauId}`)
                .then(response => response.json())
                .then(data => {
                    data.forEach(classe => {
                        const option = document.createElement('option');
                        option.value = classe.id;
                        option.textContent = classe.nom;
                        classeSelect.appendChild(option);
                    });
                    
                    // Si une classe était précédemment sélectionnée, tenter de la restaurer
                    const previousClasseId = classe_id;
                    if (previousClasseId) {
                        const option = classeSelect.querySelector(`option[value="${previousClasseId}"]`);
                        if (option) option.selected = true;
                    }
                })
                .catch(error => console.error('Erreur lors du chargement des classes:', error));
        } else {
            classeSelect.disabled = true;
        }
    });
});


</script>

@endsection