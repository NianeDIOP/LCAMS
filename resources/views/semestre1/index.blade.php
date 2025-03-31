@extends('layouts.module')

@section('title', 'Semestre 1 - Tableau de bord')

@section('sidebar')
<div class="nav-title">Semestre 1</div>

<li class="nav-item">
    <a class="nav-link {{ request()->routeIs('semestre1.index') ? 'active' : '' }}" href="{{ route('semestre1.index') }}">
        <span class="nav-icon"><i class="fas fa-chart-pie"></i></span>
        <span>Vue d'ensemble</span>
    </a>
</li>

<li class="nav-item">
    <a class="nav-link {{ request()->routeIs('semestre1.analyse-moyennes') ? 'active' : '' }}" href="{{ route('semestre1.analyse-moyennes') }}">
        <span class="nav-icon"><i class="fas fa-chart-line"></i></span>
        <span>Analyse Moyennes</span>
    </a>
</li>

<li class="nav-item">
    <a class="nav-link {{ request()->routeIs('semestre1.analyse-disciplines') ? 'active' : '' }}" href="{{ route('semestre1.analyse-disciplines') }}">
        <span class="nav-icon"><i class="fas fa-chart-bar"></i></span>
        <span>Analyse Disciplines</span>
    </a>
</li>

<li class="nav-item">
    <a class="nav-link {{ request()->routeIs('semestre1.rapports') ? 'active' : '' }}" href="{{ route('semestre1.rapports') }}">
        <span class="nav-icon"><i class="fas fa-file-alt"></i></span>
        <span>Rapports</span>
    </a>
</li>

<li class="nav-item">
    <a class="nav-link {{ request()->routeIs('importation.s1') ? 'active' : '' }}" href="{{ route('importation.s1') }}">
        <span class="nav-icon"><i class="fas fa-file-import"></i></span>
        <span>Importation</span>
    </a>
</li>

<div class="nav-title">Autres Modules</div>

<li class="nav-item">
    <a class="nav-link" href="{{ route('parametres.index') }}">
        <span class="nav-icon"><i class="fas fa-cog"></i></span>
        <span>Paramètres</span>
    </a>
</li>

<li class="nav-item">
    <a class="nav-link" href="{{ route('semestre2.index') }}">
        <span class="nav-icon"><i class="fas fa-calendar-alt"></i></span>
        <span>Semestre 2</span>
    </a>
</li>

<li class="nav-item">
    <a class="nav-link" href="{{ route('general.index') }}">
        <span class="nav-icon"><i class="fas fa-clipboard-list"></i></span>
        <span>Général</span>
    </a>
</li>
@endsection

@section('content')
<h1 class="page-title">
    <i class="fas fa-chart-pie me-2"></i>Tableau de bord - Semestre 1
</h1>
<p class="page-subtitle">Vue d'ensemble des performances du premier semestre pour l'année scolaire {{ $anneeScolaireActive->libelle }}.</p>

<!-- Stats Cards -->
<div class="row">
    <div class="col-md-3">
        <div class="stats-card">
            <div class="stats-icon stats-primary">
                <i class="fas fa-user-graduate"></i>
            </div>
            <div class="stats-details">
                <div class="stats-number">{{ $totalEleves }}</div>
                <div class="stats-text">Élèves</div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="stats-card">
            <div class="stats-icon stats-success">
                <i class="fas fa-users"></i>
            </div>
            <div class="stats-details">
                <div class="stats-number">{{ $totalClasses }}</div>
                <div class="stats-text">Classes</div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="stats-card">
            <div class="stats-icon stats-warning">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stats-details">
                @php
                    $successCount = isset($distribution['10_plus']) ? $distribution['10_plus'] : 
                        (($distribution['10_12'] ?? 0) + ($distribution['12_14'] ?? 0) + 
                         ($distribution['14_16'] ?? 0) + ($distribution['16_plus'] ?? 0));
                @endphp
                <div class="stats-number">{{ $successCount }}</div>
                <div class="stats-text">Élèves ≥ 10/20</div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="stats-card">
            <div class="stats-icon stats-danger">
                <i class="fas fa-times-circle"></i>
            </div>
            <div class="stats-details">
                @php
                    $failCount = ($distribution['moins_5'] ?? 0) + ($distribution['5_10'] ?? 0);
                @endphp
                <div class="stats-number">{{ $failCount }}</div>
                <div class="stats-text">Élèves < 10/20</div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Distribution des moyennes -->
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header header-primary">
                <i class="fas fa-chart-bar me-2"></i>Distribution des moyennes
            </div>
            <div class="card-body">
                <canvas id="distributionChart" width="400" height="300"></canvas>
            </div>
        </div>
    </div>
    
    <!-- Moyennes par classe -->
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header header-success">
                <i class="fas fa-chart-line me-2"></i>Moyennes par classe
            </div>
            <div class="card-body">
                <canvas id="moyennesClasseChart" width="400" height="300"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Répartition par sexe -->
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header header-warning">
                <i class="fas fa-venus-mars me-2"></i>Répartition par sexe
            </div>
            <div class="card-body">
                <canvas id="repartitionSexeChart" width="400" height="300"></canvas>
            </div>
        </div>
    </div>
    
    <!-- Actions rapides -->
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header header-primary">
                <i class="fas fa-bolt me-2"></i>Actions rapides
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <a href="{{ route('importation.s1') }}" class="btn btn-primary w-100 py-3">
                            <i class="fas fa-file-import fa-2x mb-2 d-block mx-auto"></i>
                            Importer des données
                        </a>
                    </div>
                    <div class="col-md-6 mb-3">
                        <a href="{{ route('semestre1.analyse-moyennes') }}" class="btn btn-success w-100 py-3">
                            <i class="fas fa-chart-line fa-2x mb-2 d-block mx-auto"></i>
                            Analyser les moyennes
                        </a>
                    </div>
                    <div class="col-md-6 mb-3">
                        <a href="{{ route('semestre1.analyse-disciplines') }}" class="btn btn-warning text-white w-100 py-3">
                            <i class="fas fa-chart-bar fa-2x mb-2 d-block mx-auto"></i>
                            Analyser les disciplines
                        </a>
                    </div>
                    <div class="col-md-6 mb-3">
                        <a href="{{ route('semestre1.rapports') }}" class="btn btn-info text-white w-100 py-3">
                            <i class="fas fa-file-pdf fa-2x mb-2 d-block mx-auto"></i>
                            Générer des rapports
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Chart 1: Distribution des moyennes
        const ctxDistribution = document.getElementById('distributionChart').getContext('2d');
        const distributionChart = new Chart(ctxDistribution, {
            type: 'bar',
            data: {
                labels: ['Moins de 5', '5 à 10', '10 à 12', '12 à 14', '14 à 16', '16 et plus'],
                datasets: [{
                    label: 'Nombre d\'élèves',
                    data: [
                        {{ $distribution['moins_5'] ?? 0 }},
                        {{ $distribution['5_10'] ?? 0 }},
                        {{ $distribution['10_12'] ?? 0 }},
                        {{ $distribution['12_14'] ?? 0 }},
                        {{ $distribution['14_16'] ?? 0 }},
                        {{ $distribution['16_plus'] ?? 0 }}
                    ],
                    backgroundColor: [
                        'rgba(231, 76, 60, 0.7)',
                        'rgba(243, 156, 18, 0.7)',
                        'rgba(46, 204, 113, 0.7)',
                        'rgba(52, 152, 219, 0.7)',
                        'rgba(155, 89, 182, 0.7)',
                        'rgba(52, 73, 94, 0.7)'
                    ],
                    borderColor: [
                        'rgba(231, 76, 60, 1)',
                        'rgba(243, 156, 18, 1)',
                        'rgba(46, 204, 113, 1)',
                        'rgba(52, 152, 219, 1)',
                        'rgba(155, 89, 182, 1)',
                        'rgba(52, 73, 94, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const value = context.raw;
                                const total = {{ $totalEleves ?? 0 }};
                                const percentage = total > 0 ? Math.round((value / total) * 100) : 0;
                                return `${value} élèves (${percentage}%)`;
                            }
                        }
                    }
                }
            }
        });
        
        // Chart 2: Moyennes par classe
        const ctxMoyennes = document.getElementById('moyennesClasseChart').getContext('2d');
        const moyennesData = {
            labels: [
                @foreach($moyennesParClasse as $moyenneClasse)
                    '{{ $moyenneClasse->classe }}',
                @endforeach
            ],
            datasets: [{
                label: 'Moyenne de classe',
                data: [
                    @foreach($moyennesParClasse as $moyenneClasse)
                        {{ number_format($moyenneClasse->moyenne, 2) }},
                    @endforeach
                ],
                backgroundColor: 'rgba(46, 204, 113, 0.2)',
                borderColor: 'rgba(46, 204, 113, 1)',
                borderWidth: 2,
                fill: true,
                tension: 0.1
            }]
        };
        
        const moyennesClasseChart = new Chart(ctxMoyennes, {
            type: 'bar',
            data: moyennesData,
            options: {
                scales: {
                    y: {
                        beginAtZero: false,
                        min: 0,
                        max: 20,
                        ticks: {
                            stepSize: 2
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });
        
        // Chart 3: Répartition par sexe
        const ctxSexe = document.getElementById('repartitionSexeChart').getContext('2d');
        
        const sexeData = [];
        const sexeLabels = [];
        const sexeColors = [];
        
        @foreach($repartitionSexe as $rep)
            sexeLabels.push('{{ $rep->sexe == "M" ? "Garçons" : ($rep->sexe == "F" ? "Filles" : "Non défini") }}');
            sexeData.push({{ $rep->total }});
            sexeColors.push('{{ $rep->sexe == "M" ? "rgba(52, 152, 219, 0.7)" : ($rep->sexe == "F" ? "rgba(255, 105, 180, 0.7)" : "rgba(149, 165, 166, 0.7)") }}');
        @endforeach
        
        const sexeChart = new Chart(ctxSexe, {
            type: 'doughnut',
            data: {
                labels: sexeLabels,
                datasets: [{
                    data: sexeData,
                    backgroundColor: sexeColors,
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'right',
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const value = context.raw;
                                const total = sexeData.reduce((a, b) => a + b, 0);
                                const percentage = total > 0 ? Math.round((value / total) * 100) : 0;
                                return `${value} élèves (${percentage}%)`;
                            }
                        }
                    }
                }
            }
        });
    });
</script>
@endsection