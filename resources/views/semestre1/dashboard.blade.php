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
    /* Styles généraux */
    .dashboard-container {
        margin-bottom: 20px;
    }
    
    .dashboard-header {
        background-color: #004d40;
        color: white;
        padding: 10px 15px;
        font-weight: bold;
        text-align: center;
        border-radius: 5px 5px 0 0;
        text-transform: uppercase;
        margin-bottom: 0;
        letter-spacing: 1px;
    }
    
    .dashboard-subheader {
        background-color: #00796b;
        color: white;
        padding: 6px 15px;
        font-weight: bold;
        text-align: center;
        font-size: 0.9rem;
        text-transform: uppercase;
        margin-bottom: 0;
        letter-spacing: 0.5px;
    }
    
    .data-section {
        background-color: #f2f9f9;
        margin-bottom: 20px;
        border-radius: 0 0 5px 5px;
        overflow: hidden;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }
    
    .filter-container {
        background-color: #fff;
        padding: 15px;
        margin-bottom: 20px;
        border-radius: 5px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }
    
    .filter-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 10px;
    }
    
    .filter-title {
        font-weight: bold;
        color: #004d40;
        display: flex;
        align-items: center;
    }
    
    .filter-title i {
        margin-right: 5px;
    }
    
    .filter-form {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }
    
    .filter-group {
        flex: 1;
        min-width: 160px;
    }
    
    /* Cartes de données */
    .stat-card {
        background-color: white;
        padding: 15px;
        border-radius: 0;
    }
    
    .stat-category {
        background-color: #00a67c;
        color: white;
        text-align: center;
        padding: 8px;
        font-weight: bold;
        text-transform: uppercase;
        margin: 0;
        letter-spacing: 0.5px;
    }
    
    .stat-value {
        font-size: 2.5rem;
        font-weight: bold;
        text-align: center;
        margin: 10px 0;
    }
    
    .stat-label {
        font-size: 0.9rem;
        text-align: center;
        text-transform: uppercase;
        color: #666;
        font-weight: 600;
        border-bottom: 1px solid #e0e0e0;
        padding-bottom: 5px;
    }
    
    .stat-number {
        font-size: 1.8rem;
        font-weight: bold;
        text-align: center;
        color: #212121;
    }
    
    /* Donut chart */
    .donut-container {
        width: 150px;
        height: 150px;
        margin: 0 auto;
        position: relative;
    }
    
    .donut-center {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        text-align: center;
        width: 80px;
        height: 80px;
        background: white;
        border-radius: 50%;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }
    
    .donut-percent {
        font-size: 1.8rem;
        font-weight: bold;
        color: #f39c12;
    }
    
    .donut-label {
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
    }
    
    .gender-stats {
        display: flex;
        justify-content: space-around;
        margin-top: 10px;
    }
    
    .gender-stat {
        text-align: center;
    }
    
    .gender-label {
        font-size: 0.85rem;
        font-weight: 600;
    }
    
    .gender-percent {
        font-size: 1.2rem;
        font-weight: bold;
    }
    
    /* Performances */
    .performance-box {
        display: flex;
        justify-content: space-between;
        padding: 15px;
        text-align: center;
    }
    
    .performance-item {
        flex: 1;
    }
    
    .performance-label {
        font-size: 0.85rem;
        text-transform: uppercase;
        color: #666;
        font-weight: 600;
        margin-bottom: 5px;
    }
    
    .performance-value {
        font-size: 1.5rem;
        font-weight: bold;
    }
    
    /* Graphique */
    .chart-container {
        width: 100%;
        height: 240px;
        padding: 15px;
        background-color: white;
    }
    
    /* Tableau de données */
    .data-table-container {
        margin: 0;
        background-color: white;
        overflow: hidden;
    }
    
    .data-table {
        width: 100%;
        margin-bottom: 0;
        font-size: 0.85rem;
    }
    
    .data-table th {
        background-color: #e0f2f1;
        color: #004d40;
        padding: 10px;
        font-weight: 600;
        text-align: center;
    }
    
    .data-table td {
        padding: 8px 10px;
        text-align: center;
        border-bottom: 1px solid #e0e0e0;
    }
    
    /* Indicateurs */
    .observations-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 0;
    }
    
    .observations-item {
        padding: 10px;
        text-align: center;
        background-color: white;
    }
    
    .observations-label {
        font-size: 0.85rem;
        text-transform: uppercase;
        color: #666;
        font-weight: 600;
        margin-bottom: 5px;
    }
    
    .observations-value {
        font-size: 1.8rem;
        font-weight: bold;
    }
    
    /* Bouton de téléchargement */
    .telecharger-btn {
        position: absolute;
        right: 15px;
        top: 10px;
        z-index: 10;
    }
    
    /* Séparateur vertical */
    .separator {
        width: 30px;
        background-color: #e0f2f1;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        color: #004d40;
        font-weight: bold;
        writing-mode: vertical-rl;
        text-orientation: mixed;
        transform: rotate(180deg);
        text-transform: uppercase;
        letter-spacing: 1px;
        font-size: 0.9rem;
    }
    
    .arrow-down {
        width: 0; 
        height: 0; 
        border-left: 15px solid transparent;
        border-right: 15px solid transparent;
        border-top: 15px solid #004d40;
        margin: 0 auto;
    }
    
    /* Adaptations pour mobile */
    @media (max-width: 768px) {
        .filter-group {
            flex: 100%;
        }
        
        .performance-box {
            flex-direction: column;
            gap: 10px;
        }
        
        .observations-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }
</style>
@endsection
@section('module-content')
    <!-- En-tête du tableau de bord -->
    <div class="dashboard-container">
        <h4 class="dashboard-header">TABLEAU DE BORD DES RESULTATS DES MOYENNES GENERALES DU PREMIER SEMESTRE</h4>
        <h5 class="dashboard-subheader">SEMESTRE 1 
            @if($filterType == 'niveau' && isset($niveau_id) && isset($niveauxTous))
                {{ $niveauxTous->firstWhere('id', $niveau_id)->nom ?? '' }}
            @elseif($filterType == 'classe' && isset($classe_id) && isset($classes))
                {{ $classes->firstWhere('id', $classe_id)->nom ?? '' }}
            @endif
        </h5>
    </div>
    
    <!-- Section de filtres -->
    <div class="filter-container">
        <div class="filter-header">
            <div class="filter-title">
                <i class="fas fa-filter me-2"></i> FILTRER
            </div>
            <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="collapse" data-bs-target="#filterCollapse" aria-expanded="false" aria-controls="filterCollapse">
                <i class="fas fa-chevron-down"></i>
            </button>
        </div>
        
        <div class="collapse show" id="filterCollapse">
            <form id="dashboardFilterForm" action="{{ route('semestre1.dashboard') }}" method="GET" class="filter-form">
                <div class="filter-group">
                    <label for="filter_type" class="form-label">Type de filtre</label>
                    <select class="form-select form-select-sm" id="filter_type" name="filter_type">
                        <option value="all" {{ ($filterType ?? 'all') == 'all' ? 'selected' : '' }}>Tous</option>
                        <option value="niveau" {{ ($filterType ?? '') == 'niveau' ? 'selected' : '' }}>Niveau</option>
                        <option value="classe" {{ ($filterType ?? '') == 'classe' ? 'selected' : '' }}>Classe</option>
                        <option value="sexe" {{ ($filterType ?? '') == 'sexe' ? 'selected' : '' }}>Sexe</option>
                        <option value="interval" {{ ($filterType ?? '') == 'interval' ? 'selected' : '' }}>Intervalle</option>
                    </select>
                </div>
                
                <div class="filter-group niveau-filter" style="{{ ($filterType ?? '') != 'niveau' && ($filterType ?? '') != 'classe' ? 'display:none' : '' }}">
                    <label for="niveau_id" class="form-label">Niveau</label>
                    <select class="form-select form-select-sm" id="niveau_id" name="niveau_id" {{ ($filterType ?? '') != 'niveau' && ($filterType ?? '') != 'classe' ? 'disabled' : '' }}>
                        <option value="">Sélectionner un niveau</option>
                        @foreach($niveauxTous ?? [] as $niveau)
                            <option value="{{ $niveau->id }}" {{ ($niveau_id ?? '') == $niveau->id ? 'selected' : '' }}>{{ $niveau->nom }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="filter-group classe-filter" style="{{ ($filterType ?? '') != 'classe' ? 'display:none' : '' }}">
                    <label for="classe_id" class="form-label">Classe</label>
                    <select class="form-select form-select-sm" id="classe_id" name="classe_id" {{ ($filterType ?? '') != 'classe' ? 'disabled' : '' }}>
                        <option value="">Sélectionner une classe</option>
                        @foreach($classes ?? [] as $classe)
                            <option value="{{ $classe->id }}" {{ ($classe_id ?? '') == $classe->id ? 'selected' : '' }}>{{ $classe->nom }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="filter-group sexe-filter" style="{{ ($filterType ?? '') != 'sexe' ? 'display:none' : '' }}">
                    <label for="sexe" class="form-label">Sexe</label>
                    <select class="form-select form-select-sm" id="sexe" name="sexe" {{ ($filterType ?? '') != 'sexe' ? 'disabled' : '' }}>
                        <option value="">Tous</option>
                        <option value="F" {{ (request('sexe') ?? '') == 'F' ? 'selected' : '' }}>Filles</option>
                        <option value="G" {{ (request('sexe') ?? '') == 'G' ? 'selected' : '' }}>Garçons</option>
                    </select>
                </div>
                
                <div class="filter-group interval-filter" style="{{ ($filterType ?? '') != 'interval' ? 'display:none' : '' }}">
                    <label for="min_moyenne" class="form-label">Moyenne min.</label>
                    <input type="number" class="form-control form-control-sm" id="min_moyenne" name="min_moyenne" step="0.01" min="0" max="20" value="{{ $min_moyenne ?? '' }}" {{ ($filterType ?? '') != 'interval' ? 'disabled' : '' }}>
                </div>
                
                <div class="filter-group interval-filter" style="{{ ($filterType ?? '') != 'interval' ? 'display:none' : '' }}">
                    <label for="max_moyenne" class="form-label">Moyenne max.</label>
                    <input type="number" class="form-control form-control-sm" id="max_moyenne" name="max_moyenne" step="0.01" min="0" max="20" value="{{ $max_moyenne ?? '' }}" {{ ($filterType ?? '') != 'interval' ? 'disabled' : '' }}>
                </div>
                
                <div class="filter-group" style="display: flex; gap: 5px;">
                    <button type="submit" class="btn btn-sm btn-primary flex-grow-1">
                        <i class="fas fa-search me-1"></i> Appliquer
                    </button>
                    <a href="{{ route('semestre1.dashboard') }}" class="btn btn-sm btn-outline-secondary">
                        <i class="fas fa-undo"></i>
                    </a>
                    <button type="submit" name="export" value="1" class="btn btn-sm btn-success">
                        <i class="fas fa-file-export"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>
    <!-- Contenu principal -->
    <div class="row g-3">
        <!-- Première rangée -->
        <div class="col-12">
            <div class="row g-0">
                <!-- Section Effectifs -->
                <div class="col-md-4">
                    <h5 class="stat-category">EFFECTIF</h5>
                    <div class="stat-card">
                        <div class="row">
                            <div class="col-4">
                                <div class="stat-label">TOTAL</div>
                                <div class="stat-number">{{ $totalEleves ?? '0' }}</div>
                            </div>
                            <div class="col-4">
                                <div class="stat-label">FILLES</div>
                                <div class="stat-number">{{ $fillesCount ?? '0' }}</div>
                            </div>
                            <div class="col-4">
                                <div class="stat-label">GARÇONS</div>
                                <div class="stat-number">{{ $garconsCount ?? '0' }}</div>
                            </div>
                        </div>
                        
                        <div class="donut-container mt-3">
                            <canvas id="genderDonut"></canvas>
                        </div>
                        
                        <div class="gender-stats">
                            <div class="gender-stat">
                                <div class="gender-label">GARÇONS</div>
                                <div class="gender-percent text-primary">{{ $totalEleves ? round(($garconsCount / $totalEleves) * 100) : '0' }}%</div>
                            </div>
                            <div class="gender-stat">
                                <div class="gender-label">FILLES</div>
                                <div class="gender-percent text-danger">{{ $totalEleves ? round(($fillesCount / $totalEleves) * 100) : '0' }}%</div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Section Moyenne >= 10 -->
                <div class="col-md-4">
                    <h5 class="stat-category">MOYENNE >=10</h5>
                    <div class="stat-card">
                        <div class="row">
                            <div class="col-4">
                                <div class="stat-label">TOTAL</div>
                                <div class="stat-number">{{ $elevesAvecMoyenne ?? '0' }}</div>
                            </div>
                            <div class="col-4">
                                <div class="stat-label">FILLES</div>
                                <div class="stat-number">{{ $fillesAvecMoyenne ?? '0' }}</div>
                            </div>
                            <div class="col-4">
                                <div class="stat-label">GARÇONS</div>
                                <div class="stat-number">{{ $garconsAvecMoyenne ?? '0' }}</div>
                            </div>
                        </div>
                        
                        <div class="donut-container mt-3">
                            <canvas id="successRateDonut"></canvas>
                            <div class="donut-center">
                                <div class="donut-percent">{{ $tauxReussite ?? '0' }}%</div>
                                <div class="donut-label">TAUX</div>
                            </div>
                        </div>
                        
                        <div class="gender-stats">
                            <div class="gender-stat">
                                <div class="gender-label">% FILLES</div>
                                <div class="gender-percent text-danger">
                                    {{ $fillesCount ? round(($fillesAvecMoyenne / $fillesCount) * 100) : '0' }}%
                                </div>
                            </div>
                            <div class="gender-stat">
                                <div class="gender-label">% GARÇONS</div>
                                <div class="gender-percent text-primary">
                                    {{ $garconsCount ? round(($garconsAvecMoyenne / $garconsCount) * 100) : '0' }}%
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Séparateur -->
                <div class="separator">
                    <span>TELECHARGER</span>
                    <div class="mt-2">
                        <i class="fas fa-arrow-down"></i>
                    </div>
                </div>
                
                <!-- Section Indicateurs de performances -->
                <div class="col">
                    <h5 class="stat-category">INDICATEURS DE PERFORMANCES</h5>
                    <div class="stat-card">
                        <div class="performance-box">
                            <div class="performance-item">
                                <div class="performance-label">NOTE MOYENNE</div>
                                <div class="performance-value text-primary">{{ number_format(($noteMoyenne ?? 0), 2) }}</div>
                            </div>
                            <div class="performance-item">
                                <div class="performance-label">PLUS FORTE MOYENNE</div>
                                <div class="performance-value text-success">{{ number_format(($plusForteMoyenne ?? 0), 2) }}</div>
                            </div>
                            <div class="performance-item">
                                <div class="performance-label">PLUS FAIBLE MOYENNE</div>
                                <div class="performance-value text-danger">{{ number_format(($plusFaibleMoyenne ?? 0), 2) }}</div>
                            </div>
                        </div>
                        
                        <div class="performance-box pt-0">
                            <div class="performance-item">
                                <div class="performance-label">FÉLICITATIONS</div>
                                <div class="performance-value text-dark">{{ $felicitations ?? '0' }}</div>
                            </div>
                            <div class="performance-item">
                                <div class="performance-label">ENCOURAGEMENTS</div>
                                <div class="performance-value text-dark">{{ $encouragements ?? '0' }}</div>
                            </div>
                            <div class="performance-item">
                                <div class="performance-label">TABLEAU D'HONNEUR</div>
                                <div class="performance-value text-dark">{{ $tableauHonneur ?? '0' }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Deuxième rangée -->
        <div class="col-12">
            <div class="row g-0">
                <!-- Moyennes par niveau -->
                <div class="col-md-6">
                    <h5 class="stat-category">MOYENNES GÉNÉRALES PAR NIVEAU</h5>
                    <div class="chart-container">
                        <canvas id="niveauxChart"></canvas>
                    </div>
                </div>
                
                <!-- Observations -->
                <div class="col-md-6">
                    <div class="row g-0">
                        <div class="col-12">
                            <h5 class="stat-category">OBSERVATIONS</h5>
                            <div class="observations-grid">
                                <div class="observations-item">
                                    <div class="observations-label">MIEUX FAIRE</div>
                                    <div class="observations-value">{{ $mieuxFaire ?? '0' }}</div>
                                </div>
                                <div class="observations-item">
                                    <div class="observations-label">DOIT CONTINUER</div>
                                    <div class="observations-value">{{ $doitContinuer ?? '0' }}</div>
                                </div>
                                <div class="observations-item">
                                    <div class="observations-label">RISQUE DE REDOUBLER</div>
                                    <div class="observations-value">{{ $risqueRedoubler ?? '0' }}</div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-12 mt-3">
                            <h5 class="stat-category">RETARDS ET ABSENCES DU SEMESTRE 1</h5>
                            <div class="chart-container" style="height: 170px;">
                                <canvas id="absencesChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Troisième rangée - Tableau des niveaux -->
        <div class="col-12">
            <h5 class="stat-category">STATISTIQUES PAR NIVEAU</h5>
            <div class="data-table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th width="20%">NIVEAU</th>
                            <th width="10%">EFFECTIF</th>
                            <th width="12%">MOYENNE</th>
                            <th width="18%">TAUX RÉUSSITE</th>
                            <th width="10%">FÉLICITATIONS</th>
                            <th width="10%">ENCOURAGEMENTS</th>
                            <th width="10%">TABLEAU D'HONNEUR</th>
                            <th width="10%">RISQUE D'ÉCHEC</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($statsNiveaux ?? [] as $niveau)
                        <tr>
                            <td><strong>{{ $niveau['code'] }}</strong> - {{ $niveau['nom'] }}</td>
                            <td>{{ $niveau['effectif'] }}</td>
                            <td><strong class="{{ floatval($niveau['moyenne']) >= 10 ? 'text-success' : 'text-danger' }}">{{ $niveau['moyenne'] }}</strong></td>
                            <td>
                                <div class="progress" style="height: 6px; margin-top: 5px;">
                                    <div class="progress-bar {{ $niveau['taux_reussite'] >= 70 ? 'bg-success' : ($niveau['taux_reussite'] >= 50 ? 'bg-warning' : 'bg-danger') }}" style="width: {{ $niveau['taux_reussite'] }}%"></div>
                                </div>
                                <span class="small">{{ $niveau['taux_reussite'] }}%</span>
                            </td>
                            <td>{{ $niveau['felicitations'] }}</td>
                            <td>{{ $niveau['encouragements'] }}</td>
                            <td>{{ $niveau['tableau_honneur'] }}</td>
                            <td>{{ $niveau['effectif'] - ($niveau['felicitations'] + $niveau['encouragements'] + $niveau['tableau_honneur']) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
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
    const sexeFilter = document.querySelectorAll('.sexe-filter');
    const intervalFilter = document.querySelectorAll('.interval-filter');
    const niveauSelect = document.getElementById('niveau_id');
    const classeSelect = document.getElementById('classe_id');
    const sexeSelect = document.getElementById('sexe');
    const minMoyenneInput = document.getElementById('min_moyenne');
    const maxMoyenneInput = document.getElementById('max_moyenne');
    
    // Initialiser l'état des filtres au chargement de la page
    if (filterType.value === 'niveau' || filterType.value === 'classe') {
        niveauFilter.forEach(el => el.style.display = 'block');
        niveauSelect.disabled = false;
        
        if (filterType.value === 'classe') {
            classeFilter.forEach(el => el.style.display = 'block');
            classeSelect.disabled = false;
        }
    } else if (filterType.value === 'sexe') {
        sexeFilter.forEach(el => el.style.display = 'block');
        sexeSelect.disabled = false;
    } else if (filterType.value === 'interval') {
        intervalFilter.forEach(el => el.style.display = 'block');
        minMoyenneInput.disabled = false;
        maxMoyenneInput.disabled = false;
    }
    
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
        maxMoyenneInput.disabled = false;
        
        // Activer les filtres nécessaires
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
    
    // Préparation des données pour les graphiques
    const statsNiveauxCodes = @json(array_column($statsNiveaux ?? [], 'code') ?? []);
    const statsNiveauxMoyennes = @json(array_map(function($n) { return floatval($n['moyenne']); }, $statsNiveaux ?? []) ?? []);
    const statsNiveauxTauxReussite = @json(array_column($statsNiveaux ?? [], 'taux_reussite') ?? []);
    
    const fillesCount = {{ $fillesCount ?? 0 }};
    const garconsCount = {{ $garconsCount ?? 0 }};
    
    const fillesAvecMoyenne = {{ $fillesAvecMoyenne ?? 0 }};
    const garconsAvecMoyenne = {{ $garconsAvecMoyenne ?? 0 }};
    
    const tauxReussite = {{ $tauxReussite ?? 0 }};
    
    const classesData = @json($classesData ?? []);
    const retardsData = @json($retardsData ?? []);
    const absencesData = @json($absencesData ?? []);
    
    // Donut Chart pour la répartition par sexe
    const genderCtx = document.getElementById('genderDonut').getContext('2d');
    new Chart(genderCtx, {
        type: 'doughnut',
        data: {
            labels: ['Filles', 'Garçons'],
            datasets: [{
                data: [fillesCount, garconsCount],
                backgroundColor: ['#e83e8c', '#007bff'],
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
                    callbacks: {
                        label: function(context) {
                            const value = context.raw;
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = Math.round((value / total) * 100);
                            return `${context.label}: ${value} (${percentage}%)`;
                        }
                    }
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
            labels: ['Réussite', 'Échec'],
            datasets: [{
                data: [tauxReussite, 100 - tauxReussite],
                backgroundColor: ['#f39c12', '#e0e0e0'],
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
                    enabled: false
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
                backgroundColor: 'rgba(0, 166, 124, 0.2)',
                borderColor: '#00a67c',
                borderWidth: 2,
                tension: 0.4,
                fill: true,
                pointBackgroundColor: '#00a67c',
                pointRadius: 6,
                pointHoverRadius: 8
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: false,
                    min: Math.max(0, Math.min(...statsNiveauxMoyennes) - 2),
                    max: Math.max(...statsNiveauxMoyennes) + 2,
                    ticks: {
                        font: {
                            weight: 'bold'
                        }
                    },
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)'
                    }
                },
                x: {
                    grid: {
                        display: false
                    },
                    ticks: {
                        font: {
                            weight: 'bold'
                        }
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: '#004d40',
                    titleFont: {
                        size: 16,
                        weight: 'bold'
                    },
                    bodyFont: {
                        size: 14
                    },
                    padding: 12,
                    displayColors: false,
                    callbacks: {
                        title: function(tooltipItems) {
                            return 'NIVEAU ' + tooltipItems[0].label;
                        },
                        label: function(context) {
                            return 'Moyenne: ' + context.parsed.y.toFixed(2);
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
                backgroundColor: '#4caf50',
                borderColor: '#388e3c',
                borderWidth: 1,
                borderRadius: 4,
                barPercentage: 0.6,
                categoryPercentage: 0.7
            }, {
                label: 'Absences',
                data: absencesData,
                backgroundColor: '#f44336',
                borderColor: '#d32f2f',
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
                        boxWidth: 12,
                        font: {
                            size: 10,
                            weight: 'bold'
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)'
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
                    const previousClasseId = "{{ $classe_id ?? '' }}";
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