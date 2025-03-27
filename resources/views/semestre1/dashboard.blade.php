@extends('layouts.sidebar')

@section('title', 'Tableau de bord - Semestre 1')

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
    Tableau de bord - Semestre 1
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('semestre1.index') }}">Semestre 1</a></li>
    <li class="breadcrumb-item active">Tableau de bord</li>
@endsection

@section('styles')
<style>
    .dashboard-header {
        background-color: #0d6efd;
        color: white;
        padding: 10px 20px;
        border-radius: 5px 5px 0 0;
        font-weight: bold;
        text-align: center;
        margin-bottom: 0;
        text-transform: uppercase;
        letter-spacing: 1px;
    }
    
    .dashboard-card {
        background-color: white;
        border-radius: 5px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        margin-bottom: 20px;
        overflow: hidden;
    }
    
    .dashboard-title {
        background-color: #198754;
        color: white;
        padding: 10px;
        text-align: center;
        text-transform: uppercase;
        font-size: 14px;
        font-weight: bold;
        margin: 0;
    }
    
    .stats-row {
        display: flex;
        justify-content: space-around;
        padding: 15px 0;
        background-color: #f0f9ff;
    }
    
    .stats-item {
        text-align: center;
    }
    
    .stats-value {
        font-size: 24px;
        font-weight: bold;
        color: #0d6efd;
    }
    
    .stats-label {
        font-size: 12px;
        color: #6c757d;
        text-transform: uppercase;
    }
    
    .donut-chart {
        position: relative;
        width: 150px;
        height: 150px;
        margin: 0 auto;
    }
    
    .donut-chart-text {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        font-size: 20px;
        font-weight: bold;
        color: #0d6efd;
    }
    
    .chart-legend {
        display: flex;
        justify-content: center;
        margin-top: 15px;
    }
    
    .legend-item {
        display: flex;
        align-items: center;
        margin: 0 10px;
        font-size: 12px;
    }
    
    .legend-color {
        width: 12px;
        height: 12px;
        margin-right: 5px;
        border-radius: 2px;
    }
    
    .indicator-row {
        display: flex;
        justify-content: space-around;
        padding: 20px 0;
        text-align: center;
    }
    
    .indicator-value {
        font-size: 28px;
        font-weight: bold;
        margin-bottom: 5px;
    }
    
    .indicator-label {
        font-size: 12px;
        color: #6c757d;
        text-transform: uppercase;
    }
    
    .observation-card {
        text-align: center;
        padding: 10px;
    }
    
    .observation-value {
        font-size: 24px;
        font-weight: bold;
        color: #0d6efd;
    }
    
    .observation-label {
        font-size: 12px;
        color: #6c757d;
    }
    
    .line-chart-container {
        width: 100%;
        height: 200px;
        padding: 20px;
    }
    
    .telecharger-btn {
        position: absolute;
        right: 15px;
        top: 15px;
        z-index: 10;
    }
    
    .retards-absences-chart {
        width: 100%;
        height: 150px;
        padding: 20px;
    }

    .bar-chart {
        display: flex;
        height: 150px;
        align-items: flex-end;
        justify-content: space-around;
        padding: 0 20px 20px;
    }
    
    .bar {
        width: 30px;
        background-color: #0d6efd;
        border-radius: 3px 3px 0 0;
        position: relative;
    }
    
    .bar-label {
        position: absolute;
        bottom: -25px;
        left: 50%;
        transform: translateX(-50%);
        font-size: 12px;
        white-space: nowrap;
    }
    
    .bar-value {
        position: absolute;
        top: -20px;
        left: 50%;
        transform: translateX(-50%);
        font-size: 12px;
        font-weight: bold;
    }
    
    .retard-bar {
        display: inline-block;
        width: 10px;
        background-color: #28a745;
        margin-right: 5px;
    }
    
    .class-stats {
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
        padding: 0 15px 15px;
    }
    
    .class-stat-item {
        width: calc(50% - 10px);
        margin-bottom: 10px;
        position: relative;
    }
    
    .class-name {
        font-size: 12px;
        margin-bottom: 5px;
    }
    
    .class-bars {
        display: flex;
        height: 30px;
    }
    
    .retard-count, .absence-count {
        position: absolute;
        top: 0;
        font-size: 11px;
        font-weight: bold;
    }
    
    .retard-count {
        right: 50%;
        margin-right: 5px;
    }
    
    .absence-count {
        right: 0;
    }
</style>
@endsection

@section('main-content')
    <div class="position-relative mb-4">
        <h2 class="dashboard-header">TABLEAU DE BORD DES RESULTATS DES MOYENNES GENERALES DU PREMIER SEMESTRE</h2>
        <a href="#" class="btn btn-sm btn-secondary telecharger-btn">
            <i class="fas fa-download me-1"></i> FILTRER
        </a>
    </div>
    
    <div class="row">
        <!-- Première colonne -->
        <div class="col-md-4">
            <!-- Carte des effectifs -->
            <div class="dashboard-card">
                <h3 class="dashboard-title">EFFECTIF</h3>
                <div class="stats-row">
                    <div class="stats-item">
                        <div class="stats-value">{{ $totalEleves }}</div>
                        <div class="stats-label">TOTAL</div>
                    </div>
                    <div class="stats-item">
                        <div class="stats-value">{{ $fillesCount }}</div>
                        <div class="stats-label">FILLES</div>
                    </div>
                    <div class="stats-item">
                        <div class="stats-value">{{ $garconsCount }}</div>
                        <div class="stats-label">GARÇONS</div>
                    </div>
                </div>
                
                <!-- Graphique en anneau -->
                <div style="padding: 15px;">
                    <div class="donut-chart">
                        <svg viewBox="0 0 36 36" class="circular-chart">
                            <path class="circle-bg" d="M18 2.0845
                                a 15.9155 15.9155 0 0 1 0 31.831
                                a 15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="#eeeeee" stroke-width="4" />
                            <path class="circle" d="M18 2.0845
                                a 15.9155 15.9155 0 0 1 0 31.831
                                a 15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="#28a745" stroke-width="4" stroke-dasharray="{{ $pourcentageFilles }}, 100" />
                            <text x="18" y="20.35" class="percentage">{{ $pourcentageFilles }}%</text>
                        </svg>
                        <div class="donut-chart-text"></div>
                    </div>
                    <div class="chart-legend">
                        <div class="legend-item">
                            <div class="legend-color" style="background-color: #28a745;"></div>
                            <span>FILLES {{ $pourcentageFilles }}%</span>
                        </div>
                        <div class="legend-item">
                            <div class="legend-color" style="background-color: #b3b3b3;"></div>
                            <span>GARÇONS {{ $pourcentageGarcons }}%</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Graphique des moyennes par niveau -->
            <div class="dashboard-card">
                <h3 class="dashboard-title">MOYENNES GENERALES PAR NIVEAU</h3>
                <div class="line-chart-container">
                    <canvas id="moyennesParNiveau"></canvas>
                </div>
            </div>
        </div>
        
        <!-- Deuxième colonne -->
        <div class="col-md-4">
            <!-- Carte des élèves avec moyenne -->
            <div class="dashboard-card">
                <h3 class="dashboard-title">MOYENNE >=10</h3>
                <div class="stats-row">
                    <div class="stats-item">
                        <div class="stats-value">{{ $elevesAvecMoyenne }}</div>
                        <div class="stats-label">TOTAL</div>
                    </div>
                    <div class="stats-item">
                        <div class="stats-value">{{ $fillesAvecMoyenne }}</div>
                        <div class="stats-label">FILLES</div>
                    </div>
                    <div class="stats-item">
                        <div class="stats-value">{{ $garconsAvecMoyenne }}</div>
                        <div class="stats-label">GARÇONS</div>
                    </div>
                </div>
                
                <!-- Graphique de taux de réussite -->
                <div style="padding: 15px;">
                    <div style="text-align: center; margin-bottom: 10px;">
                        <div style="font-size: 24px; font-weight: bold; color: #ffc107;">{{ $tauxReussite }}%</div>
                        <div style="font-size: 12px; color: #6c757d;">TAUX</div>
                    </div>
                    <div style="display: flex; justify-content: space-around;">
                        <div style="text-align: center;">
                            <div style="font-size: 18px; font-weight: bold; color: #17a2b8;">{{ $tauxReussiteFilles }}%</div>
                            <div style="font-size: 12px; color: #6c757d;">% FILLES</div>
                        </div>
                        <div style="text-align: center;">
                            <div style="font-size: 18px; font-weight: bold; color: #17a2b8;">{{ $tauxReussiteGarcons }}%</div>
                            <div style="font-size: 12px; color: #6c757d;">% GARÇONS</div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Carte des observations -->
            <div class="dashboard-card">
                <h3 class="dashboard-title">OBSERVATIONS</h3>
                <div class="row p-3">
                    <div class="col-4">
                        <div class="observation-card">
                            <div class="observation-value">{{ $mieuxFaireCount }}</div>
                            <div class="observation-label">MIEUX FAIRE</div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="observation-card">
                            <div class="observation-value">{{ $doitContinuerCount }}</div>
                            <div class="observation-label">DOIT CONTINUER</div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="observation-card">
                            <div class="observation-value">{{ $risqueRedoublerCount }}</div>
                            <div class="observation-label">RISQUE DE REDOUBLER</div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Carte des retards et absences -->
            <div class="dashboard-card">
                <h3 class="dashboard-title">RETARDS ET ABSENCES DU SEMESTRE 1</h3>
                <div class="class-stats">
                    @foreach($statsClasses as $classStat)
                        <div class="class-stat-item">
                            <div class="class-name">{{ $classStat['nom'] }}</div>
                            <div class="class-bars">
                                <div class="retard-bar" style="height: 100%; width: {{ min(100, $classStat['retards']) }}%;"></div>
                                <div class="absence-bar" style="height: 100%; width: {{ min(100, $classStat['absences']) }}%;"></div>
                            </div>
                            <div class="retard-count">{{ $classStat['retards'] }}</div>
                            <div class="absence-count">{{ $classStat['absences'] }}</div>
                        </div>
                    @endforeach
                </div>
                <div class="chart-legend m-3">
                    <div class="legend-item">
                        <div class="legend-color" style="background-color: #28a745;"></div>
                        <span>Nombre RETARD</span>
                    </div>
                    <div class="legend-item">
                        <div class="legend-color" style="background-color: #dc3545;"></div>
                        <span>Nombre ABSENCE</span>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Troisième colonne -->
        <div class="col-md-4">
            <!-- Carte des indicateurs de performance -->
            <div class="dashboard-card">
                <h3 class="dashboard-title">INDICATEURS DE PERFORMANCES</h3>
                <div class="indicator-row">
                    <div>
                        <div class="indicator-value">{{ $noteMoyenne }}</div>
                        <div class="indicator-label">NOTE MOYENNE</div>
                    </div>
                    <div>
                        <div class="indicator-value">{{ $notePlusForte }}</div>
                        <div class="indicator-label">PLUS FORTE MOYENNE</div>
                    </div>
                    <div>
                        <div class="indicator-value">{{ $notePlusFaible }}</div>
                        <div class="indicator-label">PLUS FAIBLE MOYENNE</div>
                    </div>
                </div>
                <div class="indicator-row">
                    <div>
                        <div class="indicator-value">{{ $felicitationsCount }}</div>
                        <div class="indicator-label">FÉLICITATIONS</div>
                    </div>
                    <div>
                        <div class="indicator-value">{{ $encouragementsCount }}</div>
                        <div class="indicator-label">ENCOURAGEMENTS</div>
                    </div>
                    <div>
                        <div class="indicator-value">{{ $tableauHonneurCount }}</div>
                        <div class="indicator-label">TABLEAU D'HONNEUR</div>
                    </div>
                </div>
            </div>
            
            <!-- Autres statistiques ou graphiques peuvent être ajoutés ici -->
            <div class="dashboard-card">
                <h3 class="dashboard-title">STATISTIQUES DES CLASSES</h3>
                <div style="padding: 15px;">
                    <!-- Statistiques supplémentaires ici -->
                    <table class="table table-sm table-striped">
                        <thead>
                            <tr>
                                <th>Classe</th>
                                <th class="text-center">Élèves</th>
                                <th class="text-center">Moyenne</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($statsClasses as $index => $classStat)
                                @if($index < 8) {{-- Limiter à 8 classes pour éviter une liste trop longue --}}
                                    <tr>
                                        <td>{{ $classStat['nom'] }}</td>
                                        <td class="text-center">
                                            @php
                                                $classeCount = DB::table('excel_data')
                                                    ->join('imported_files', 'excel_data.file_id', '=', 'imported_files.id')
                                                    ->where('imported_files.semestre', 1)
                                                    ->where('imported_files.classe_id', $classStat['id'])
                                                    ->distinct()
                                                    ->count(DB::raw('JSON_EXTRACT(data, "$[0]")'));
                                            @endphp
                                            {{ $classeCount }}
                                        </td>
                                        <td class="text-center">
                                            @php
                                                $classeMoyenne = DB::table('excel_data')
                                                    ->join('imported_files', 'excel_data.file_id', '=', 'imported_files.id')
                                                    ->where('imported_files.semestre', 1)
                                                    ->where('imported_files.classe_id', $classStat['id'])
                                                    ->whereRaw('JSON_EXTRACT(data, "$[9]") IS NOT NULL')
                                                    ->select(DB::raw('AVG(CAST(REPLACE(JSON_EXTRACT(data, "$[9]"), ",", ".") AS DECIMAL(10,2))) as moyenne_classe'))
                                                    ->first();
                                                
                                                $moyenne = $classeMoyenne ? number_format($classeMoyenne->moyenne_classe, 2) : '0.00';
                                            @endphp
                                            {{ $moyenne }}
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Graphique des moyennes par niveau
        const ctx = document.getElementById('moyennesParNiveau').getContext('2d');
        
        // Données pour le graphique
        const labels = [
            @foreach($statsNiveaux as $niveau)
                '{{ $niveau['code'] }}',
            @endforeach
        ];
        
        const data = [
            @foreach($statsNiveaux as $niveau)
                {{ $niveau['moyenne'] }},
            @endforeach
        ];
        
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Moyenne par niveau',
                    data: data,
                    fill: false,
                    borderColor: '#28a745',
                    tension: 0.1,
                    pointBackgroundColor: '#28a745',
                    pointRadius: 5
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: false,
                        min: Math.max(0, Math.min(...data) - 2),
                        max: Math.max(...data) + 2
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });
    });
</script>
@endsection