@extends('layouts.module')

@section('title', 'Semestre 1')

@section('module-title')
    <i class="fas fa-calendar-alt me-2"></i> Semestre 1
@endsection

@section('page-title', 'Vue d\'ensemble')

@section('breadcrumb')
    <li class="breadcrumb-item active">Semestre 1</li>
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
    /* Styles spécifiques à la page d'accueil du Semestre 1 */
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
    
    .action-card {
        background-color: white;
        border-radius: 0.5rem;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        padding: 1.5rem;
        height: 100%;
        transition: transform 0.2s, box-shadow 0.2s;
    }
    
    .action-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    }
    
    .action-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 40px;
        height: 40px;
        border-radius: 10px;
        margin-bottom: 1rem;
        font-size: 1.1rem;
    }
    
    .action-title {
        font-size: 1.1rem;
        font-weight: 600;
        margin-bottom: 0.75rem;
    }
    
    .action-description {
        font-size: 0.85rem;
        color: #6c757d;
        margin-bottom: 1.25rem;
    }
    
    .section-title {
        font-size: 1.25rem;
        font-weight: 600;
        margin-bottom: 1.25rem;
        color: #343a40;
    }
    
    .guide-box {
        background-color: #e9f2ff;
        border-radius: 0.5rem;
        padding: 1.5rem;
        margin-top: 1.5rem;
    }
    
    .guide-title {
        font-size: 1.1rem;
        font-weight: 600;
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
    }
    
    .guide-title i {
        margin-right: 0.5rem;
        color: var(--primary);
    }
    
    .guide-list {
        padding-left: 1.5rem;
        margin-bottom: 0;
    }
    
    .guide-list li {
        margin-bottom: 0.5rem;
        font-size: 0.9rem;
    }
    
    .guide-list li:last-child {
        margin-bottom: 0;
    }

    /* Styles pour les nouveaux éléments */
    .performance-bar {
        height: 10px;
        background-color: #f0f0f0;
        border-radius: 5px;
        margin-bottom: 15px;
        overflow: hidden;
        display: flex;
    }

    .segment-excellent {
        background-color: #28a745;
        height: 100%;
    }

    .segment-good {
        background-color: #17a2b8;
        height: 100%;
    }

    .segment-average {
        background-color: #ffc107;
        height: 100%;
    }

    .segment-poor {
        background-color: #dc3545;
        height: 100%;
    }

    .gender-donut {
        width: 120px;
        height: 120px;
        margin: 0 auto;
        position: relative;
        border-radius: 50%;
        background: conic-gradient(
            #17a2b8 0% {{ $genderStats['male'] }}%, 
            #dc3545 {{ $genderStats['male'] }}% 100%
        );
    }

    .gender-donut::before {
        content: "";
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 80px;
        height: 80px;
        background: #fff;
        border-radius: 50%;
    }

    .recent-files-table {
        font-size: 0.85rem;
    }

    .recent-files-table th, .recent-files-table td {
        padding: 0.5rem 0.75rem;
    }

    .dash-section {
        margin-bottom: 2rem;
    }

    .grid-container {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 1.5rem;
    }

    .data-card {
        background-color: white;
        border-radius: 0.5rem;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        padding: 1.5rem;
        height: 100%;
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

    .legend-item {
        display: flex;
        align-items: center;
        margin-bottom: 0.5rem;
        font-size: 0.85rem;
    }

    .legend-color {
        width: 12px;
        height: 12px;
        margin-right: 0.5rem;
        border-radius: 2px;
    }
</style>
@endsection

@section('content')
    <!-- Statistiques -->
    <div class="row mb-4">
        <!-- Élèves -->
        <div class="col-6 col-md-3 mb-3">
            <div class="stats-card">
                <div class="stats-icon bg-primary-subtle text-primary">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stats-value text-primary">{{ $studentCount }}</div>
                <div class="stats-label">Élèves</div>
            </div>
        </div>
        
        <!-- Classes -->
        <div class="col-6 col-md-3 mb-3">
            <div class="stats-card">
                <div class="stats-icon bg-success-subtle text-success">
                    <i class="fas fa-chalkboard-teacher"></i>
                </div>
                <div class="stats-value text-success">{{ $classCount }}</div>
                <div class="stats-label">Classes</div>
            </div>
        </div>
        
        <!-- Fichiers -->
        <div class="col-6 col-md-3 mb-3">
            <div class="stats-card">
                <div class="stats-icon bg-warning-subtle text-warning">
                    <i class="fas fa-file-excel"></i>
                </div>
                <div class="stats-value text-warning">{{ $fileCount }}</div>
                <div class="stats-label">Fichiers importés</div>
            </div>
        </div>
        
        <!-- Moyenne -->
        <div class="col-6 col-md-3 mb-3">
            <div class="stats-card">
                <div class="stats-icon bg-info-subtle text-info">
                    <i class="fas fa-chart-bar"></i>
                </div>
                <div class="stats-value text-info">{{ $averageGrade }}</div>
                <div class="stats-label">Moyenne générale</div>
            </div>
        </div>
    </div>
    
    <!-- Tableau de bord détaillé -->
    <div class="dash-section">
        <h2 class="section-title">Tableau de bord</h2>
        
        <div class="grid-container">
            <!-- Répartition par niveau de performance -->
            <div class="data-card">
                <h3 class="data-card-title">
                    <i class="fas fa-chart-pie"></i> Performance des élèves
                </h3>
                
                <div class="performance-bar">
                    <div class="segment-excellent" style="width: {{ $performanceStats['excellent'] }}%"></div>
                    <div class="segment-good" style="width: {{ $performanceStats['good'] }}%"></div>
                    <div class="segment-average" style="width: {{ $performanceStats['average'] }}%"></div>
                    <div class="segment-poor" style="width: {{ $performanceStats['poor'] }}%"></div>
                </div>
                
                <div class="legend">
                    <div class="legend-item">
                        <div class="legend-color" style="background-color: #28a745;"></div>
                        <span>Excellent ≥ 16 ({{ $performanceStats['excellent'] }}%)</span>
                    </div>
                    <div class="legend-item">
                        <div class="legend-color" style="background-color: #17a2b8;"></div>
                        <span>Bien 14-15.99 ({{ $performanceStats['good'] }}%)</span>
                    </div>
                    <div class="legend-item">
                        <div class="legend-color" style="background-color: #ffc107;"></div>
                        <span>Moyen 10-13.99 ({{ $performanceStats['average'] }}%)</span>
                    </div>
                    <div class="legend-item">
                        <div class="legend-color" style="background-color: #dc3545;"></div>
                        <span>Insuffisant < 10 ({{ $performanceStats['poor'] }}%)</span>
                    </div>
                </div>
            </div>
            
            <!-- Répartition par sexe -->
            <div class="data-card">
                <h3 class="data-card-title">
                    <i class="fas fa-venus-mars"></i> Répartition par sexe
                </h3>
                
                <div class="gender-donut"></div>
                
                <div class="legend mt-3 text-center">
                    <div class="legend-item justify-content-center">
                        <div class="legend-color" style="background-color: #17a2b8;"></div>
                        <span>Garçons ({{ $genderStats['male'] }}%)</span>
                    </div>
                    <div class="legend-item justify-content-center">
                        <div class="legend-color" style="background-color: #dc3545;"></div>
                        <span>Filles ({{ $genderStats['female'] }}%)</span>
                    </div>
                </div>
            </div>
            
            <!-- Fichiers récemment importés -->
            <div class="data-card" style="grid-column: span 2;">
                <h3 class="data-card-title">
                    <i class="fas fa-history"></i> Fichiers récemment importés
                </h3>
                
                @if(count($recentFiles) > 0)
                    <div class="table-responsive">
                        <table class="table table-sm table-hover recent-files-table">
                            <thead class="table-light">
                                <tr>
                                    <th>Nom du fichier</th>
                                    <th>Niveau</th>
                                    <th>Classe</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentFiles as $file)
                                    <tr>
                                        <td>{{ $file->nom_fichier }}</td>
                                        <td>{{ $file->niveau_nom ?? 'Non spécifié' }}</td>
                                        <td>{{ $file->classe_nom ?? 'Non spécifié' }}</td>
                                        <td>{{ \Carbon\Carbon::parse($file->created_at)->format('d/m/Y H:i') }}</td>
                                        <td>
                                            <a href="{{ route('semestre1.viewImportedFile', $file->id) }}" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i> Aucun fichier n'a encore été importé.
                    </div>
                @endif
                
                <div class="mt-3 text-end">
                    <a href="{{ route('semestre1.base') }}" class="btn btn-sm btn-primary">
                        <i class="fas fa-database me-1"></i> Gérer tous les fichiers
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Actions principales -->
    <div class="dash-section">
        <h2 class="section-title">Actions principales</h2>
        
        <div class="row mb-4">
            <!-- Base des moyennes -->
            <div class="col-md-6 mb-4">
                <div class="action-card">
                    <div class="action-icon bg-primary-subtle text-primary">
                        <i class="fas fa-database"></i>
                    </div>
                    <h3 class="action-title">Base des moyennes</h3>
                    <p class="action-description">Importez les fichiers Excel générés depuis PLANETE pour commencer l'analyse des données.</p>
                    <a href="{{ route('semestre1.base') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-file-import me-1"></i> Importer des données
                    </a>
                </div>
            </div>
            
            <!-- Analyse des disciplines -->
            <div class="col-md-6 mb-4">
                <div class="action-card">
                    <div class="action-icon bg-success-subtle text-success">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <h3 class="action-title">Analyse des disciplines</h3>
                    <p class="action-description">Visualisez les statistiques détaillées par discipline et par classe.</p>
                    <a href="{{ route('semestre1.analyse') }}" class="btn btn-success btn-sm">
                        <i class="fas fa-chart-line me-1"></i> Analyser les résultats
                    </a>
                </div>
            </div>
            
            <!-- Tableau de bord complet -->
            <div class="col-md-6 mb-4">
                <div class="action-card">
                    <div class="action-icon bg-info-subtle text-info">
                        <i class="fas fa-tachometer-alt"></i>
                    </div>
                    <h3 class="action-title">Tableau de bord complet</h3>
                    <p class="action-description">Consultez le tableau de bord détaillé pour une analyse complète des performances.</p>
                    <a href="{{ route('semestre1.dashboard') }}" class="btn btn-info btn-sm text-white">
                        <i class="fas fa-tachometer-alt me-1"></i> Voir le tableau de bord
                    </a>
                </div>
            </div>
            
            <!-- Rapports -->
            <div class="col-md-6 mb-4">
                <div class="action-card">
                    <div class="action-icon bg-warning-subtle text-warning">
                        <i class="fas fa-file-alt"></i>
                    </div>
                    <h3 class="action-title">Rapports</h3>
                    <p class="action-description">Générez des rapports détaillés par classe, niveau ou discipline.</p>
                    <a href="{{ route('semestre1.rapports') }}" class="btn btn-warning btn-sm">
                        <i class="fas fa-file-pdf me-1"></i> Générer des rapports
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Guide d'utilisation -->
    @if($fileCount == 0)
        <div class="guide-box">
            <h3 class="guide-title">
                <i class="fas fa-info-circle"></i> Comment commencer ?
            </h3>
            <p>Pour analyser les données du Semestre 1, suivez ces étapes :</p>
            <ol class="guide-list">
                <li>Accédez à la <strong>Base des moyennes</strong> pour importer vos fichiers Excel depuis PLANETE</li>
                <li>Consultez le <strong>Tableau de bord</strong> pour visualiser les statistiques générales</li>
                <li>Utilisez l'<strong>Analyse des disciplines</strong> pour des informations détaillées</li>
                <li>Générez des <strong>Rapports</strong> personnalisés selon vos besoins</li>
            </ol>
        </div>
    @endif
@endsection