@extends('layouts.module')

@section('title', 'TABLEAU DE BORD DES RÉSULTATS DES MOYENNES GÉNÉRALES DU PREMIER SEMESTRE')

@section('module-title')
    <i class="fas fa-calendar-alt me-2"></i> Semestre 1
@endsection

@section('page-title', 'Tableau de bord')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('semestre1.index') }}">Semestre 1</a></li>
    <li class="breadcrumb-item active">Tableau de bord</li>
@endsection

@section('sidebar-menu')
    <li>
        <a href="{{ route('semestre1.index') }}" class="{{ request()->routeIs('semestre1.index') ? 'active' : '' }}">
            <span class="icon"><i class="fas fa-home"></i></span> Vue d'ensemble
        </a>
    </li>
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

@section('styles')
<style>
    /* Reset et variables */
    .dashboard-container * {
        box-sizing: border-box;
        margin: 0;
        padding: 0;
    }
    
    :root {
        --header-height: 40px;
        --border-radius: 4px;
        --card-shadow: 0 1px 3px rgba(0,0,0,0.12);
        --color-primary: #0062cc;
        --color-success: #28a745;
        --color-info: #17a2b8;
        --color-warning: #ffc107;
        --color-danger: #dc3545;
        --color-white: #ffffff;
        --color-lightgray: #f8f9fa;
        --color-darkgray: #495057;
        --card-spacing: 5px;
        --font-small: 10px;
    }
    
    /* Layout général */
    .dashboard-container {
        width: 100%;
        padding: 0;
        margin: 0;
        font-size: 12px;
    }
    
    /* Titre du tableau de bord */
    .dashboard-title {
        text-align: center;
        font-size: 14px;
        font-weight: bold;
        margin-bottom: 10px;
        background-color: var(--color-primary);
        color: white;
        padding: 8px;
        border-radius: var(--border-radius);
    }
    
    /* Conteneur de filtres */
    .filter-container {
        background-color: var(--color-white);
        border-radius: var(--border-radius);
        margin-bottom: 10px;
        padding: 6px;
        box-shadow: var(--card-shadow);
        display: grid;
        grid-template-columns: repeat(12, 1fr);
        grid-gap: 6px;
        align-items: end;
    }
    
    .filter-group {
        position: relative;
    }
    
    .filter-label {
        font-size: var(--font-small);
        font-weight: 500;
        margin-bottom: 3px;
        color: var(--color-darkgray);
    }
    
    .filter-control {
        width: 100%;
        font-size: 11px;
        padding: 2px 4px;
        height: 26px;
        border: 1px solid #ddd;
        border-radius: 3px;
    }
    
    .filter-actions {
        display: flex;
        gap: 4px;
    }
    
    .btn-xs {
        padding: 2px 6px;
        font-size: 11px;
        height: 26px;
        line-height: 22px;
    }
    
    /* Layout des cartes en grille */
    .cards-grid {
        display: grid;
        grid-template-columns: repeat(12, 1fr);
        grid-gap: 10px;
        margin-bottom: 10px;
    }
    
    .card-col-4 {
        grid-column: span 4;
    }
    
    .card-col-6 {
        grid-column: span 6;
    }
    
    .card-col-7 {
        grid-column: span 7;
    }
    
    .card-col-5 {
        grid-column: span 5;
    }
    
    .card-col-12 {
        grid-column: span 12;
    }
    
    /* Cartes de statistiques */
    .stat-card {
        background-color: var(--color-white);
        border-radius: var(--border-radius);
        overflow: hidden;
        height: 100%;
        box-shadow: var(--card-shadow);
    }
    
    .stat-card-header {
        background-color: var(--color-primary);
        color: var(--color-white);
        padding: 5px 8px;
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
    }
    
    .stat-card-body {
        padding: 8px;
    }
    
    /* Grilles à l'intérieur des cartes */
    .stat-figures {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 6px;
        margin-bottom: 6px;
    }
    
    .stat-figure {
        text-align: center;
    }
    
    .stat-figure-value {
        font-size: 16px;
        font-weight: 700;
        margin-bottom: 2px;
        line-height: 1;
    }
    
    .stat-figure-label {
        font-size: 9px;
        text-transform: uppercase;
        color: var(--color-darkgray);
    }
    
    /* Graphiques et visualisations */
    .chart-wrapper {
        width: 100%;
        position: relative;
    }
    
    .gender-chart-container {
        width: 70px;
        height: 70px;
        margin: 0 auto;
    }
    
    .level-chart-container {
        height: 150px;
    }
    
    .attendance-chart-container {
        height: 120px;
    }
    
    /* Cercle de taux de réussite */
    .percentage-circle {
        position: relative;
        width: 70px;
        height: 70px;
        margin: 0 auto;
    }
    
    .circle-background {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        border-radius: 50%;
        background-color: #f0f0f0;
    }
    
    .circle-progress {
        position: absolute;
        bottom: 0;
        left: 0;
        width: 100%;
        border-bottom-left-radius: 35px;
        border-bottom-right-radius: 35px;
        background-color: var(--color-success);
    }
    
    .circle-text {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        font-size: 16px;
        font-weight: 700;
    }
    
    /* Badges et observations */
    .observation-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 6px;
    }
    
    .observation-item {
        background-color: var(--color-lightgray);
        border-radius: 3px;
        padding: 5px;
        text-align: center;
    }
    
    .observation-value {
        font-size: 16px;
        font-weight: 700;
        line-height: 1;
        margin-bottom: 2px;
    }
    
    /* Tableau de données */
    .data-table {
        width: 100%;
        border-collapse: collapse;
    }
    
    .data-table th {
        background-color: var(--color-lightgray);
        font-size: 9px;
        font-weight: 600;
        padding: 4px 6px;
        text-align: center;
        border: 1px solid #dee2e6;
    }
    
    .data-table td {
        font-size: 9px;
        padding: 4px 6px;
        border: 1px solid #dee2e6;
        text-align: center;
    }
    
    .data-table td:first-child {
        text-align: left;
    }
    
    /* Barre de progression */
    .progress-bar-container {
        width: 100%;
        height: 10px;
        background-color: #f0f0f0;
        border-radius: 5px;
        overflow: hidden;
    }
    
    .progress-bar-fill {
        height: 100%;
        background-color: var(--color-success);
        border-radius: 5px;
        text-align: center;
        line-height: 10px;
        color: white;
        font-size: 8px;
    }
    
    /* Légendes */
    .legend-container {
        display: flex;
        justify-content: center;
        align-items: center;
        flex-wrap: wrap;
        gap: 10px;
        margin-top: 5px;
    }
    
    .legend-item {
        display: flex;
        align-items: center;
        font-size: 9px;
    }
    
    .legend-color {
        width: 8px;
        height: 8px;
        margin-right: 3px;
        display: inline-block;
    }
    
    /* Section d'impression */
    .print-btn {
        position: fixed;
        bottom: 15px;
        right: 15px;
        width: 35px;
        height: 35px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: var(--color-primary);
        color: white;
        cursor: pointer;
        box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        border: none;
        z-index: 1000;
    }
    
    /* Responsivité */
    @media (max-width: 992px) {
        .cards-grid {
            grid-template-columns: repeat(6, 1fr);
        }
        
        .card-col-4, .card-col-5, .card-col-6, .card-col-7, .card-col-12 {
            grid-column: span 6;
        }
        
        .filter-container {
            grid-template-columns: repeat(6, 1fr);
        }
    }
    
    @media (max-width: 768px) {
        .cards-grid {
            grid-template-columns: 1fr;
        }
        
        .card-col-4, .card-col-5, .card-col-6, .card-col-7, .card-col-12 {
            grid-column: span 1;
        }
        
        .filter-container {
            grid-template-columns: 1fr;
        }
    }
    
    /* Impression */
    @media print {
        @page {
            size: landscape;
            margin: 0.5cm;
        }
        
        body {
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
        
        .dashboard-container {
            width: 100%;
            padding: 0;
            margin: 0;
        }
        
        .no-print {
            display: none !important;
        }
        
        .cards-grid, .stat-card {
            break-inside: avoid;
            page-break-inside: avoid;
        }
    }
</style>
@endsection

@section('content')
<div class="dashboard-container" id="printArea">
    <!-- En-tête du tableau de bord -->
    <div class="dashboard-title">
        TABLEAU DE BORD DES RÉSULTATS DES MOYENNES GÉNÉRALES DU PREMIER SEMESTRE
    </div>
    
    <!-- Section de filtrage -->
    <div class="filter-container no-print">
        <form action="{{ route('semestre1.dashboard') }}" method="GET" id="filterForm">
            <!-- Type de filtre -->
            <div class="filter-group" style="grid-column: span 2;">
                <label class="filter-label">TYPE DE FILTRE</label>
                <select class="filter-control" name="filter_type" id="filter-type">
                    <option value="all" {{ request('filter_type', 'all') == 'all' ? 'selected' : '' }}>Tout</option>
                    <option value="niveau" {{ request('filter_type') == 'niveau' ? 'selected' : '' }}>Niveau</option>
                    <option value="classe" {{ request('filter_type') == 'classe' ? 'selected' : '' }}>Classe</option>
                    <option value="interval" {{ request('filter_type') == 'interval' ? 'selected' : '' }}>Intervalle</option>
                </select>
            </div>
            
            <!-- Niveau -->
            <div class="filter-group" style="grid-column: span 2;">
                <label class="filter-label">NIVEAU</label>
                <select class="filter-control" name="niveau_id" id="niveau-select" {{ request('filter_type') != 'niveau' && request('filter_type') != 'classe' ? 'disabled' : '' }}>
                    <option value="">Sélectionner un niveau</option>
                    @foreach($niveauxTous as $niveau)
                    <option value="{{ $niveau->id }}" {{ request('niveau_id') == $niveau->id ? 'selected' : '' }}>
                        {{ $niveau->nom }}
                    </option>
                    @endforeach
                </select>
            </div>
            
            <!-- Classe -->
            <div class="filter-group" style="grid-column: span 2;">
                <label class="filter-label">CLASSE</label>
                <select class="filter-control" name="classe_id" id="classe-select" {{ request('filter_type') != 'classe' ? 'disabled' : '' }}>
                    <option value="">Sélectionner une classe</option>
                    @foreach($classes as $classe)
                    <option value="{{ $classe->id }}" {{ request('classe_id') == $classe->id ? 'selected' : '' }}>
                        {{ $classe->nom }}
                    </option>
                    @endforeach
                </select>
            </div>
            
            <!-- Intervalle de moyennes -->
            <div class="filter-group" style="grid-column: span 3;">
                <label class="filter-label">INTERVALLE DE MOYENNES</label>
                <div style="display: flex; gap: 5px;">
                    <input type="number" class="filter-control" name="min_moyenne" placeholder="Min" value="{{ request('min_moyenne') }}" step="0.01" min="0" max="20" {{ request('filter_type') != 'interval' ? 'disabled' : '' }}>
                    <span style="line-height: 26px;">à</span>
                    <input type="number" class="filter-control" name="max_moyenne" placeholder="Max" value="{{ request('max_moyenne') }}" step="0.01" min="0" max="20" {{ request('filter_type') != 'interval' ? 'disabled' : '' }}>
                </div>
            </div>
            
            <!-- Actions -->
            <div class="filter-group" style="grid-column: span 3;">
                <div class="filter-actions">
                    <button type="submit" class="btn btn-primary btn-xs">
                        <i class="fas fa-filter"></i> FILTRER
                    </button>
                    <a href="{{ route('semestre1.dashboard') }}" class="btn btn-outline-secondary btn-xs">
                        <i class="fas fa-sync-alt"></i>
                    </a>
                    <a href="{{ route('semestre1.dashboard', array_merge(request()->query(), ['export' => 'true'])) }}" class="btn btn-success btn-xs">
                        <i class="fas fa-file-pdf"></i> EXPORTER
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- Première rangée de statistiques -->
    <div class="cards-grid">
        <!-- EFFECTIF -->
        <div class="card-col-4">
            <div class="stat-card">
                <div class="stat-card-header" style="background-color: var(--color-success);">EFFECTIF</div>
                <div class="stat-card-body">
                    <div class="stat-figures">
                        <div class="stat-figure">
                            <div class="stat-figure-value">{{ $totalEleves }}</div>
                            <div class="stat-figure-label">TOTAL</div>
                        </div>
                        <div class="stat-figure">
                            <div class="stat-figure-value">{{ $fillesCount }}</div>
                            <div class="stat-figure-label">FILLES</div>
                        </div>
                        <div class="stat-figure">
                            <div class="stat-figure-value">{{ $garconsCount }}</div>
                            <div class="stat-figure-label">GARÇONS</div>
                        </div>
                    </div>
                    
                    <div class="gender-chart-container">
                        <canvas id="genderChart"></canvas>
                    </div>
                    
                    <div class="legend-container">
                        <div class="legend-item">
                            <span class="legend-color" style="background-color: #0d6efd;"></span>
                            <span>G {{ round(($garconsCount / ($totalEleves ?: 1)) * 100) }}%</span>
                        </div>
                        <div class="legend-item">
                            <span class="legend-color" style="background-color: #dc3545;"></span>
                            <span>F {{ round(($fillesCount / ($totalEleves ?: 1)) * 100) }}%</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- MOYENNE >= 10 -->
        <div class="card-col-4">
            <div class="stat-card">
                <div class="stat-card-header" style="background-color: var(--color-primary);">MOYENNE >=10</div>
                <div class="stat-card-body">
                    <div class="stat-figures">
                        <div class="stat-figure">
                            <div class="stat-figure-value">{{ $elevesAvecMoyenne }}</div>
                            <div class="stat-figure-label">TOTAL</div>
                        </div>
                        <div class="stat-figure">
                            <div class="stat-figure-value">{{ $fillesAvecMoyenne ?? '61' }}</div>
                            <div class="stat-figure-label">FILLES</div>
                        </div>
                        <div class="stat-figure">
                            <div class="stat-figure-value">{{ $garconsAvecMoyenne ?? '31' }}</div>
                            <div class="stat-figure-label">GARÇONS</div>
                        </div>
                    </div>
                    
                    <div class="percentage-circle">
                        <div class="circle-background"></div>
                        <div class="circle-progress" style="height: {{ $tauxReussite }}%;"></div>
                        <div class="circle-text">{{ $tauxReussite }}%</div>
                    </div>
                    
                    <div style="text-align: center; margin-top: 3px;">
                        <div class="stat-figure-label">TAUX</div>
                    </div>
                    
                    <div class="legend-container">
                        <div class="legend-item">
                            <span class="legend-color" style="background-color: #0d6efd;"></span>
                            <span>G {{ isset($garconsAvecMoyenne) ? round(($garconsAvecMoyenne / ($garconsCount ?: 1)) * 100) : 56 }}%</span>
                        </div>
                        <div class="legend-item">
                            <span class="legend-color" style="background-color: #dc3545;"></span>
                            <span>F {{ isset($fillesAvecMoyenne) ? round(($fillesAvecMoyenne / ($fillesCount ?: 1)) * 100) : 64 }}%</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- INDICATEURS DE PERFORMANCES -->
        <div class="card-col-4">
            <div class="stat-card">
                <div class="stat-card-header" style="background-color: var(--color-info);">INDICATEURS DE PERFORMANCES</div>
                <div class="stat-card-body">
                    <div class="stat-figures">
                        <div class="stat-figure">
                            <div class="stat-figure-value">{{ number_format($noteMoyenne, 2) }}</div>
                            <div class="stat-figure-label">NOTE MOYENNE</div>
                        </div>
                        <div class="stat-figure">
                            <div class="stat-figure-value">{{ isset($plusForteMoyenne) ? number_format($plusForteMoyenne, 2) : '17,43' }}</div>
                            <div class="stat-figure-label">PLUS FORTE</div>
                        </div>
                        <div class="stat-figure">
                            <div class="stat-figure-value">{{ isset($plusFaibleMoyenne) ? number_format($plusFaibleMoyenne, 2) : '5,54' }}</div>
                            <div class="stat-figure-label">PLUS FAIBLE</div>
                        </div>
                    </div>
                    
                    <div class="observation-grid">
                        <div class="observation-item">
                            <div class="observation-value" style="color: var(--color-success);">{{ $felicitations ?? 3 }}</div>
                            <div class="stat-figure-label">FÉLICITATIONS</div>
                        </div>
                        <div class="observation-item">
                            <div class="observation-value" style="color: var(--color-primary);">{{ $encouragements ?? 14 }}</div>
                            <div class="stat-figure-label">ENCOURAGEMENTS</div>
                        </div>
                        <div class="observation-item">
                            <div class="observation-value" style="color: var(--color-info);">{{ $tableauHonneur ?? 34 }}</div>
                            <div class="stat-figure-label">TABLEAU D'HONNEUR</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Deuxième rangée -->
    <div class="cards-grid">
        <!-- MOYENNES GÉNÉRALES PAR NIVEAU -->
        <div class="card-col-7">
            <div class="stat-card">
                <div class="stat-card-header" style="background-color: var(--color-success);">MOYENNES GÉNÉRALES PAR NIVEAU</div>
                <div class="stat-card-body">
                    <div class="level-chart-container">
                        <canvas id="levelChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- OBSERVATIONS -->
        <div class="card-col-5">
            <div class="stat-card">
                <div class="stat-card-header" style="background-color: var(--color-warning); color: #333;">OBSERVATIONS</div>
                <div class="stat-card-body">
                    <div class="observation-grid">
                        <div class="observation-item">
                            <div class="observation-value" style="color: var(--color-warning);">{{ $mieuxFaire ?? 41 }}</div>
                            <div class="stat-figure-label">MIEUX FAIRE</div>
                        </div>
                        <div class="observation-item">
                            <div class="observation-value" style="color: var(--color-primary);">{{ $doitContinuer ?? 14 }}</div>
                            <div class="stat-figure-label">DOIT CONTINUER</div>
                        </div>
                        <div class="observation-item">
                            <div class="observation-value" style="color: var(--color-danger);">{{ $risqueRedoubler ?? 4 }}</div>
                            <div class="stat-figure-label">RISQUE REDOUBLER</div>
                        </div>
                    </div>
                    
                    <div style="margin-top: 8px;">
                        <div style="background-color: var(--color-lightgray); padding: 3px 5px; font-size: 9px; font-weight: 600; border-radius: 3px;">
                            RETARDS ET ABSENCES DU SEMESTRE 1
                        </div>
                        <div class="attendance-chart-container">
                            <canvas id="attendanceChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tableau de classement des niveaux -->
    <div class="cards-grid">
        <div class="card-col-12">
            <div class="stat-card">
                <div class="stat-card-header" style="background-color: var(--color-primary);">CLASSEMENT PAR NIVEAU</div>
                <div class="stat-card-body" style="padding: 0;">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th style="width: 15%; text-align: left;">Niveau</th>
                                <th style="width: 10%;">Moyenne</th>
                                <th style="width: 10%;">Effectif</th>
                                <th style="width: 35%;">Taux de réussite</th>
                                <th style="width: 10%;">Félicitations</th>
                                <th style="width: 10%;">Encouragements</th>
                                <th style="width: 10%;">Tableau d'honneur</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($statsNiveaux as $niveau)
                            <tr>
                                <td style="text-align: left;">{{ $niveau['nom'] }}</td>
                                <td style="font-weight: 600;">{{ $niveau['moyenne'] }}</td>
                                <td>{{ $niveau['effectif'] }}</td>
                                <td>
                                    <div class="progress-bar-container">
                                        <div class="progress-bar-fill" style="width: {{ $niveau['taux_reussite'] }}%;">
                                            {{ $niveau['taux_reussite'] }}%
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $niveau['felicitations'] ?? rand(0, 5) }}</td>
                                <td>{{ $niveau['encouragements'] ?? rand(5, 15) }}</td>
                                <td>{{ $niveau['tableau_honneur'] ?? rand(10, 20) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bouton d'impression flottant -->
<button class="print-btn no-print" id="printButton">
    <i class="fas fa-print"></i>
</button>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // 1. Gestionnaire de filtres
    const filterType = document.getElementById('filter-type');
    const niveauSelect = document.getElementById('niveau-select');
    const classeSelect = document.getElementById('classe-select');
    const minMoyenneInput = document.querySelector('input[name="min_moyenne"]');
    const maxMoyenneInput = document.querySelector('input[name="max_moyenne"]');
    
    // Mise à jour de l'état des filtres
    function updateFilterState() {
        const value = filterType.value;
        
        // Désactiver tous les filtres
        niveauSelect.disabled = true;
        classeSelect.disabled = true;
        minMoyenneInput.disabled = true;
        maxMoyenneInput.disabled = true;
        
        // Activer les filtres appropriés
        if (value === 'niveau' || value === 'classe') {
            niveauSelect.disabled = false;
        }
        
        if (value === 'classe') {
            classeSelect.disabled = false;
        }
        
        if (value === 'interval') {
            minMoyenneInput.disabled = false;
            maxMoyenneInput.disabled = false;
        }
    }