@extends('layouts.module')

@section('title', 'Tableau de bord - Semestre 1')

@section('styles')
<style>
    /* Design système cohérent */
    .dashboard-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.03);
        transition: transform 0.2s, box-shadow 0.2s;
        height: 100%;
        overflow: hidden;
    }

    .dashboard-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.08);
    }

    .card-header {
        background: #f8f9fa;
        padding: 1.25rem;
        border-bottom: 1px solid #e9ecef;
    }

    .card-title {
        font-size: 1.1rem;
        font-weight: 600;
        margin: 0;
        display: flex;
        align-items: center;
    }

    .card-title i {
        margin-right: 0.75rem;
        font-size: 1.2em;
        color: #0062cc;
    }

    .stat-value {
        font-size: 2rem;
        font-weight: 700;
        color: #2c3e50;
        line-height: 1.2;
    }

    .stat-diff {
        font-size: 0.85rem;
        display: flex;
        align-items: center;
    }

    .stat-diff i {
        margin-right: 0.25rem;
    }

    .chart-container {
        padding: 1.25rem;
        position: relative;
        min-height: 300px;
    }

    .filter-bar {
        background: white;
        border-radius: 12px;
        padding: 1.25rem;
        margin-bottom: 1.5rem;
    }

    .filter-label {
        font-size: 0.85rem;
        font-weight: 500;
        margin-bottom: 0.5rem;
        color: #6c757d;
    }

    .grid-divider {
        border-right: 1px solid #e9ecef;
    }

    @media (max-width: 768px) {
        .grid-divider {
            border-right: none;
            border-bottom: 1px solid #e9ecef;
            margin-bottom: 1rem;
            padding-bottom: 1rem;
        }
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <!-- En-tête -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex align-items-center justify-content-between">
                <h1 class="page-title mb-0">
                    <i class="fas fa-tachometer-alt me-2"></i>Tableau de bord S1
                </h1>
                <div class="d-flex align-items-center">
                    <span class="badge bg-light text-dark me-2">
                        <i class="fas fa-calendar-alt me-2"></i>
                        @if($etablissement)
                            {{ $etablissement->annee_scolaire }}
                        @else
                            Non défini
                        @endif
                    </span>
                    <span class="badge bg-primary">
                        <i class="fas fa-database me-2"></i>{{ $fileCount }} fichiers
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtres -->
    <div class="filter-bar">
        <form action="{{ route('semestre1.dashboard') }}" method="GET">
            <div class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="filter-label">Niveau</label>
                    <select name="niveau_id" class="form-select form-select-sm">
                        @foreach($niveaux as $niveau)
                        <option value="{{ $niveau->id }}" {{ request('niveau_id') == $niveau->id ? 'selected' : '' }}>
                            {{ $niveau->nom }}
                        </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="col-md-3">
                    <label class="filter-label">Classe</label>
                    <select name="classe_id" class="form-select form-select-sm">
                        @foreach($classes as $classe)
                        <option value="{{ $classe->id }}" {{ request('classe_id') == $classe->id ? 'selected' : '' }}>
                            {{ $classe->nom }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4">
                    <div class="row g-2">
                        <div class="col">
                            <label class="filter-label">Plage de moyennes</label>
                            <div class="input-group input-group-sm">
                                <input type="number" name="min_moyenne" class="form-control" 
                                    placeholder="Min" value="{{ request('min_moyenne') }}">
                                <span class="input-group-text">-</span>
                                <input type="number" name="max_moyenne" class="form-control" 
                                    placeholder="Max" value="{{ request('max_moyenne') }}">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-2 d-flex">
                    <button type="submit" class="btn btn-sm btn-primary w-100 me-2">
                        <i class="fas fa-filter me-1"></i>Appliquer
                    </button>
                    <a href="{{ route('semestre1.dashboard') }}" class="btn btn-sm btn-light w-100">
                        <i class="fas fa-undo me-1"></i>
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- Statistiques principales -->
    <div class="row mb-4 g-4">
        <div class="col-md-3">
            <div class="dashboard-card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-users"></i>Élèves
                    </h3>
                </div>
                <div class="chart-container text-center">
                    <div class="stat-value">{{ $totalEleves }}</div>
                    <div class="text-muted small mt-2">
                        <span class="text-success">+{{ $elevesAvecMoyenne }} au-dessus de 10</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="dashboard-card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-percent"></i>Réussite
                    </h3>
                </div>
                <div class="chart-container text-center">
                    <div class="stat-value">{{ $tauxReussite }}%</div>
                    <div class="text-muted small mt-2">
                        Moyenne : {{ number_format($noteMoyenne, 2) }}
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="dashboard-card h-100">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-chart-bar"></i>Performance par niveau
                    </h3>
                </div>
                <div class="chart-container">
                    <canvas id="performanceChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Visualisations -->
    <div class="row g-4">
        <div class="col-md-4">
            <div class="dashboard-card h-100">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-venus-mars"></i>Répartition par sexe
                    </h3>
                </div>
                <div class="chart-container">
                    <canvas id="genderChart"></canvas>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="dashboard-card h-100">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-list-ol"></i>Classement par niveau
                    </h3>
                </div>
                <div class="chart-container">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Niveau</th>
                                    <th>Moyenne</th>
                                    <th>Élèves</th>
                                    <th>Réussite</th>
                                    <th>Détail</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($statsNiveaux as $niveau)
                                <tr>
                                    <td>{{ $niveau['nom'] }}</td>
                                    <td class="fw-bold">{{ $niveau['moyenne'] }}</td>
                                    <td>{{ $niveau['effectif'] }}</td>
                                    <td>{{ $niveau['effectif'] }}</td>
                                        <td>
                                            <div class="progress" style="height: 20px;">
                                                <div class="progress-bar bg-success" 
                                                    style="width: {{ $niveau['taux_reussite'] }}%">
                                                    {{ $niveau['taux_reussite'] }}%
                                                </div>
                                            </div>
                                        </td>
                                    <td>
                                        <a href="#" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-search"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Configuration commune
    const chartConfig = {
        maintainAspectRatio: false,
        plugins: {
            legend: { position: 'bottom' },
            tooltip: {
                backgroundColor: '#172b4d',
                titleFont: { size: 14 },
                bodyFont: { size: 12 },
                padding: 12
            }
        }
    };

    // Graphique de performance
    new Chart(document.getElementById('performanceChart'), {
        type: 'bar',
        data: {
            labels: @json(array_column($statsNiveaux, 'nom')),
            datasets: [{
                label: 'Moyenne',
                data: @json(array_column($statsNiveaux, 'moyenne')),
                backgroundColor: 'rgba(0, 98, 204, 0.8)',
                borderWidth: 0,
                borderRadius: 8
            }]
        },
        options: {
            ...chartConfig,
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: '#e9ecef' }
                },
                x: {
                    grid: { display: false }
                }
            }
        }
    });

    // Graphique genre
    new Chart(document.getElementById('genderChart'), {
        type: 'doughnut',
        data: {
            labels: ['Filles', 'Garçons'],
            datasets: [{
                data: [{{ $fillesCount }}, {{ $garconsCount }}],
                backgroundColor: ['#ff6384', '#36a2eb'],
                hoverOffset: 10
            }]
        },
        options: {
            ...chartConfig,
            cutout: '70%',
            plugins: {
                legend: { position: 'right' }
            }
        }
    });
</script>
@endsection