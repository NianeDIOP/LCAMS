@extends('layouts.app')

@section('title', 'Vue d\'ensemble - Semestre 1')

@section('styles')
<style>
    /* Styles de base identiques au dashboard */
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
    
    /* Panel latéral - Identique au tableau de bord */
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

    /* Nouveaux styles pour la vue d'ensemble améliorée */
    .welcome-banner {
        background: linear-gradient(135deg, #0062cc, #0056b3);
        border-radius: 0.5rem;
        color: white;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }
    
    .banner-content {
        max-width: 800px;
    }
    
    .banner-title {
        font-size: 1.8rem;
        font-weight: 700;
        margin-bottom: 0.75rem;
    }
    
    .banner-subtitle {
        font-size: 1rem;
        opacity: 0.9;
        margin-bottom: 1.5rem;
    }
    
    .banner-actions .btn {
        padding: 0.5rem 1.2rem;
        font-weight: 500;
    }
    
    .stat-card {
        background-color: white;
        border-radius: 0.5rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        height: 100%;
        transition: transform 0.2s, box-shadow 0.2s;
        overflow: hidden;
        display: flex;
        flex-direction: column;
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
        flex: 1;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }
    
    .stat-card-value {
        font-size: 2rem;
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
    
    .status-indicator {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        display: inline-block;
        margin-right: 6px;
    }
    
    .status-green {
        background-color: #28a745;
    }
    
    .status-orange {
        background-color: #fd7e14;
    }
    
    .status-blue {
        background-color: #007bff;
    }
    
    .section {
        margin-bottom: 2rem;
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
    
    .quick-action-card {
        background-color: white;
        border-radius: 0.5rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        transition: transform 0.2s, box-shadow 0.2s;
        padding: 1.5rem;
        display: flex;
        align-items: center;
        height: 100%;
    }
    
    .quick-action-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }
    
    .quick-action-icon {
        background-color: rgba(0, 98, 204, 0.1);
        width: 45px;
        height: 45px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        color: var(--primary);
        margin-right: 1rem;
    }
    
    .quick-action-content {
        flex: 1;
    }
    
    .quick-action-title {
        font-size: 0.9rem;
        font-weight: 600;
        margin-bottom: 0.25rem;
    }
    
    .quick-action-description {
        font-size: 0.8rem;
        color: #6c757d;
        margin-bottom: 0;
    }
    
    .recent-activity-item {
        padding: 0.75rem 0;
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        display: flex;
        align-items: flex-start;
    }
    
    .recent-activity-item:last-child {
        border-bottom: none;
    }
    
    .activity-icon {
        width: 30px;
        height: 30px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.8rem;
        margin-right: 0.75rem;
        flex-shrink: 0;
        margin-top: 0.2rem;
    }
    
    .activity-icon-blue {
        background-color: rgba(0, 98, 204, 0.1);
        color: var(--primary);
    }
    
    .activity-icon-green {
        background-color: rgba(40, 167, 69, 0.1);
        color: var(--success);
    }
    
    .activity-icon-orange {
        background-color: rgba(253, 126, 20, 0.1);
        color: #fd7e14;
    }
    
    .activity-content {
        flex: 1;
    }
    
    .activity-title {
        font-size: 0.875rem;
        font-weight: 500;
        margin-bottom: 0.25rem;
    }
    
    .activity-timestamp {
        font-size: 0.75rem;
        color: #6c757d;
    }
    
    .performance-overview {
        padding: 1.25rem;
        background-color: white;
        border-radius: 0.5rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }
    
    .performance-header {
        margin-bottom: 1.25rem;
    }
    
    .performance-title {
        font-size: 1rem;
        font-weight: 600;
        margin-bottom: 0.25rem;
    }
    
    .performance-bar {
        height: 12px;
        background-color: #f0f0f0;
        border-radius: 6px;
        margin-bottom: 15px;
        overflow: hidden;
        display: flex;
    }
    
    .performance-segment {
        height: 100%;
    }
    
    .segment-excellent {
        background-color: #28a745;
    }
    
    .segment-good {
        background-color: #17a2b8;
    }
    
    .segment-average {
        background-color: #ffc107;
    }
    
    .segment-poor {
        background-color: #dc3545;
    }
    
    .performance-legend {
        display: flex;
        flex-wrap: wrap;
        font-size: 0.75rem;
    }
    
    .legend-item {
        display: flex;
        align-items: center;
        margin-right: 1rem;
        margin-bottom: 0.5rem;
    }
    
    .legend-color {
        width: 12px;
        height: 12px;
        border-radius: 2px;
        margin-right: 4px;
    }
    
    .chart-container {
        position: relative;
        height: 200px;
    }
    
    .gender-distribution {
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 1.5rem;
    }
    
    .gender-icon {
        font-size: 1.25rem;
        margin-bottom: 0.25rem;
    }
    
    .gender-value {
        font-size: 1.25rem;
        font-weight: 600;
        margin-bottom: 0;
    }
    
    .gender-label {
        font-size: 0.75rem;
        color: #6c757d;
    }
    
    .gender-donut {
        width: 120px;
        height: 120px;
        margin: 0 auto;
        position: relative;
        border-radius: 50%;
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
    
    /* Styles pour la section Progrès et Suivi */
    .timeline {
        position: relative;
        padding-left: 30px;
    }
    
    .timeline::before {
        content: '';
        position: absolute;
        top: 0;
        left: 15px;
        height: 100%;
        width: 2px;
        background-color: #e9ecef;
    }
    
    .timeline-event {
        position: relative;
        margin-bottom: 1.5rem;
    }
    
    .timeline-event:last-child {
        margin-bottom: 0;
    }
    
    .timeline-dot {
        position: absolute;
        left: -23px;
        top: 5px;
        width: 12px;
        height: 12px;
        border-radius: 50%;
    }
    
    .timeline-dot-active {
        background-color: #28a745;
    }
    
    .timeline-dot-upcoming {
        background-color: #adb5bd;
    }
    
    .timeline-dot-current {
        background-color: #007bff;
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
        <!-- Barre latérale - Identique au tableau de bord -->
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
                <!-- Bannière d'accueil -->
                <div class="welcome-banner">
                    <div class="banner-content">
                        <h1 class="banner-title">Bienvenue dans le module Semestre 1</h1>
                        <p class="banner-subtitle">Consultez et analysez les résultats du premier semestre de l'année scolaire {{ isset($etablissement) ? $etablissement->annee_scolaire : '2024-2025' }}</p>
                        <div class="banner-actions">
                            <a href="{{ route('semestre1.dashboard') }}" class="btn btn-light me-2">
                                <i class="fas fa-tachometer-alt me-2"></i> Tableau de bord
                            </a>
                            <a href="{{ route('semestre1.base') }}" class="btn btn-outline-light">
                                <i class="fas fa-database me-2"></i> Importer des données
                            </a>
                        </div>
                    </div>
                </div>
                
                <!-- Statistiques rapides -->
                <div class="section">
                    <h2 class="section-title">
                        <i class="fas fa-chart-pie"></i> Aperçu des résultats
                    </h2>
                    
                    <div class="row">
                        <!-- Élèves -->
                        <div class="col-md-3 mb-3">
                            <div class="stat-card">
                                <div class="stat-card-header">
                                    Effectif Total
                                </div>
                                <div class="stat-card-body">
                                    <div class="stat-card-value text-primary">{{ $studentCount }}</div>
                                    <div class="stat-card-label">Élèves enregistrés</div>
                                </div>
                                <div class="stat-card-footer text-center">
                                    <span class="status-indicator status-green"></span> Données complètes
                                </div>
                            </div>
                        </div>
                        
                        <!-- Classes -->
                        <div class="col-md-3 mb-3">
                            <div class="stat-card">
                                <div class="stat-card-header">
                                    Classes Actives
                                </div>
                                <div class="stat-card-body">
                                    <div class="stat-card-value text-success">{{ $classCount }}</div>
                                    <div class="stat-card-label">Classes configurées</div>
                                </div>
                                <div class="stat-card-footer text-center">
                                    <span class="status-indicator status-blue"></span> Paramètres à jour
                                </div>
                            </div>
                        </div>
                        
                        <!-- Fichiers -->
                        <div class="col-md-3 mb-3">
                            <div class="stat-card">
                                <div class="stat-card-header">
                                    Fichiers Importés
                                </div>
                                <div class="stat-card-body">
                                    <div class="stat-card-value text-warning">{{ $fileCount }}</div>
                                    <div class="stat-card-label">Fichiers Excel</div>
                                </div>
                                <div class="stat-card-footer text-center">
                                    @if($fileCount > 0)
                                        <span class="status-indicator status-green"></span> Données disponibles
                                    @else
                                        <span class="status-indicator status-orange"></span> Import nécessaire
                                    @endif
                                </div>
                            </div>
                        </div>
                        
                        <!-- Moyenne générale -->
                        <div class="col-md-3 mb-3">
                            <div class="stat-card">
                                <div class="stat-card-header">
                                    Résultats
                                </div>
                                <div class="stat-card-body">
                                    <div class="stat-card-value text-info">{{ number_format($averageGrade, 2) }}</div>
                                    <div class="stat-card-label">Moyenne générale</div>
                                </div>
                                <div class="stat-card-footer text-center">
                                    <small>
                                        @if($averageGrade >= 12)
                                            <span class="text-success">Très bons résultats</span>
                                        @elseif($averageGrade >= 10)
                                            <span class="text-info">Résultats satisfaisants</span>
                                        @else
                                            <span class="text-warning">Résultats à améliorer</span>
                                        @endif
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Performance et répartition -->
                <div class="row mb-4">
                    <!-- Distribution des performances -->
                    <div class="col-md-7 mb-3">
                        <div class="performance-overview">
                            <div class="performance-header">
                                <h3 class="performance-title">Distribution des performances</h3>
                                <small class="text-muted">Répartition des élèves selon leur niveau de résultats</small>
                            </div>
                            
                            <div class="performance-bar">
                                <div class="performance-segment segment-excellent" style="width: {{ $performanceStats['excellent'] }}%"></div>
                                <div class="performance-segment segment-good" style="width: {{ $performanceStats['good'] }}%"></div>
                                <div class="performance-segment segment-average" style="width: {{ $performanceStats['average'] }}%"></div>
                                <div class="performance-segment segment-poor" style="width: {{ $performanceStats['poor'] }}%"></div>
                            </div>
                            
                            <div class="performance-legend">
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
                            
                            @if($fileCount > 0)
                            <div class="mt-3 pt-3 border-top">
                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="text-muted">Taux de réussite global (≥ 10)</small>
                                    <strong>{{ $performanceStats['excellent'] + $performanceStats['good'] + $performanceStats['average'] }}%</strong>
                                </div>
                                <div class="progress mt-1" style="height: 6px;">
                                    <div class="progress-bar bg-success" role="progressbar" style="width: {{ $performanceStats['excellent'] + $performanceStats['good'] + $performanceStats['average'] }}%" aria-valuenow="{{ $performanceStats['excellent'] + $performanceStats['good'] + $performanceStats['average'] }}" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                    
                    <!-- Répartition par sexe -->
                    <div class="col-md-5 mb-3">
                        <div class="performance-overview h-100">
                            <div class="performance-header">
                                <h3 class="performance-title">Répartition par sexe</h3>
                                <small class="text-muted">Distribution des élèves selon le genre</small>
                            </div>
                            
                            <div class="gender-distribution">
                                <div class="gender-donut" style="background: conic-gradient(#17a2b8 0% {{ $genderStats['male'] }}%, #dc3545 {{ $genderStats['male'] }}% 100%)"></div>
                            </div>
                            
                            <div class="d-flex justify-content-center mt-2">
                                <div class="text-center me-4">
                                    <div class="gender-icon text-primary">
                                        <i class="fas fa-male"></i>
                                    </div>
                                    <div class="gender-value">{{ $genderStats['male'] }}%</div>
                                    <div class="gender-label">Garçons</div>
                                </div>
                                
                                <div class="text-center">
                                    <div class="gender-icon" style="color: #dc3545;">
                                        <i class="fas fa-female"></i>
                                    </div>
                                    <div class="gender-value">{{ $genderStats['female'] }}%</div>
                                    <div class="gender-label">Filles</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Actions rapides et fichiers récents -->
                <div class="row">
                    <!-- Actions rapides -->
                    <div class="col-md-6 mb-4">
                        <h2 class="section-title">
                            <i class="fas fa-bolt"></i> Actions rapides
                        </h2>
                        
                        <div class="row g-3">
                            <div class="col-md-6">
                                <a href="{{ route('semestre1.base') }}" class="quick-action-card text-decoration-none text-dark">
                                    <div class="quick-action-icon">
                                        <i class="fas fa-file-import"></i>
                                    </div>
                                    <div class="quick-action-content">
                                        <div class="quick-action-title">Importer des données</div>
                                        <div class="quick-action-description">Ajoutez des fichiers Excel</div>
                                    </div>
                                </a>
                            </div>
                            
                            <div class="col-md-6">
                                <a href="{{ route('semestre1.dashboard') }}" class="quick-action-card text-decoration-none text-dark">
                                    <div class="quick-action-icon" style="background-color: rgba(40, 167, 69, 0.1); color: #28a745;">
                                        <i class="fas fa-tachometer-alt"></i>
                                    </div>
                                    <div class="quick-action-content">
                                        <div class="quick-action-title">Tableau de bord</div>
                                        <div class="quick-action-description">Consultez les statistiques</div>
                                    </div>
                                </a>
                            </div>
                            
                            <div class="col-md-6">
                                <a href="{{ route('semestre1.analyse') }}" class="quick-action-card text-decoration-none text-dark">
                                    <div class="quick-action-icon" style="background-color: rgba(23, 162, 184, 0.1); color: #17a2b8;">
                                        <i class="fas fa-chart-line"></i>
                                    </div>
                                    <div class="quick-action-content">
                                        <div class="quick-action-title">Analyse détaillée</div>
                                        <div class="quick-action-description">Explorez les disciplines</div>
                                    </div>
                                </a>
                            </div>
                            
                            <div class="col-md-6">
                                <a href="{{ route('semestre1.rapports') }}" class="quick-action-card text-decoration-none text-dark">
                                    <div class="quick-action-icon" style="background-color: rgba(253, 126, 20, 0.1); color: #fd7e14;">
                                        <i class="fas fa-file-pdf"></i>
                                    </div>
                                    <div class="quick-action-content">
                                        <div class="quick-action-title">Rapports</div>
                                        <div class="quick-action-description">Générez des documents</div>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Fichiers récemment importés -->
                    <div class="col-md-6 mb-4">
                        <h2 class="section-title">
                            <i class="fas fa-history"></i> Activité récente
                        </h2>
                        
                        <div class="performance-overview">
                            @if(count($recentFiles) > 0)
                                @foreach($recentFiles as $file)
                                    <div class="recent-activity-item">
                                        <div class="activity-icon activity-icon-blue">
                                            <i class="fas fa-file-excel"></i>
                                        </div>
                                        <div class="activity-content">
                                            <div class="activity-title">{{ $file->nom_fichier }}</div>
                                            <div class="activity-timestamp d-flex justify-content-between">
                                                <span>{{ \Carbon\Carbon::parse($file->created_at)->format('d/m/Y H:i') }}</span>
                                                <span>
                                                    {{ $file->niveau_nom ?? 'N/A' }} - {{ $file->classe_nom ?? 'N/A' }}
                                                    <a href="{{ route('semestre1.viewImportedFile', $file->id) }}" class="ms-2 text-primary">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                                
                                <div class="text-center mt-3">
                                    <a href="{{ route('semestre1.base') }}" class="btn btn-sm btn-outline-primary">
                                        Voir tous les fichiers <i class="fas fa-arrow-right ms-1"></i>
                                    </a>
                                </div>
                            @else
                                <div class="text-center py-4">
                                    <div class="mb-3">
                                        <i class="fas fa-folder-open fa-3x text-muted"></i>
                                    </div>
                                    <h5>Aucun fichier importé</h5>
                                    <p class="text-muted mb-3">Commencez par importer des fichiers pour analyser les données</p>
                                    <a href="{{ route('semestre1.base') }}" class="btn btn-primary">
                                        <i class="fas fa-file-import me-2"></i> Importer des fichiers
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                
                <!-- Module d'aide -->
                @if($fileCount == 0)
                <div class="section">
                    <div class="card shadow-sm">
                        <div class="card-header bg-light">
                            <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i> Guide de démarrage</h5>
                        </div>
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-md-8">
                                    <h4 class="h5 mb-3">Comment commencer avec le module Semestre 1 ?</h4>
                                    <p>Suivez ces étapes pour configurer et utiliser efficacement ce module :</p>
                                    
                                    <ol class="mb-0">
                                        <li class="mb-2">
                                            <strong>Importez vos données</strong> : Commencez par importer vos fichiers Excel générés depuis PLANETE pour alimenter le système.
                                        </li>
                                        <li class="mb-2">
                                            <strong>Consultez le tableau de bord</strong> : Visualisez les statistiques générales pour obtenir une vue d'ensemble des performances.
                                        </li>
                                        <li class="mb-2">
                                            <strong>Analysez les résultats par discipline</strong> : Utilisez l'outil d'analyse pour comprendre les forces et faiblesses par matière.
                                        </li>
                                        <li>
                                            <strong>Générez des rapports</strong> : Créez des documents PDF personnalisés pour vos réunions et présentations.
                                        </li>
                                    </ol>
                                </div>
                                <div class="col-md-4 text-center d-none d-md-block">
                                    <i class="fas fa-tasks fa-5x text-muted mb-3"></i>
                                    <div>
                                        <a href="{{ route('semestre1.base') }}" class="btn btn-primary">
                                            <i class="fas fa-file-import me-2"></i> Commencer maintenant
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @else
                <!-- Aperçu des résultats avancés (quand des données sont disponibles) -->
                <div class="section">
                    <h2 class="section-title">
                        <i class="fas fa-chart-bar"></i> Aperçu des disciplines
                    </h2>
                    
                    <div class="alert alert-info">
                        <div class="d-flex">
                            <div class="me-3">
                                <i class="fas fa-lightbulb fa-2x"></i>
                            </div>
                            <div>
                                <h5 class="alert-heading">Analyse complète disponible</h5>
                                <p class="mb-0">Explorez l'analyse détaillée pour obtenir des informations approfondies sur les performances par discipline, les comparaisons entre classes et niveaux, et bien plus encore.</p>
                                <div class="mt-3">
                                    <a href="{{ route('semestre1.analyse') }}" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-chart-line me-2"></i> Voir l'analyse complète
                                    </a>
                                    <a href="{{ route('semestre1.dashboard') }}" class="btn btn-sm btn-outline-dark ms-2">
                                        <i class="fas fa-tachometer-alt me-2"></i> Consulter le tableau de bord
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
                
                <!-- Progrès et suivi -->
                <div class="section">
                    <h2 class="section-title">
                        <i class="fas fa-tasks"></i> Progrès et suivi
                    </h2>
                    
                    <div class="row">
                        <!-- Checklist de progression -->
                        <div class="col-md-6 mb-3">
                            <div class="performance-overview">
                                <div class="performance-header">
                                    <h3 class="performance-title">Avancement du module</h3>
                                    <small class="text-muted">Progression de la configuration et de l'analyse</small>
                                </div>
                                
                                <div class="list-group list-group-flush mt-3">
                                    <div class="list-group-item px-0 py-2 d-flex justify-content-between align-items-center">
                                        <div>
                                            <i class="fas {{ isset($etablissement) ? 'fa-check-circle text-success' : 'fa-circle text-muted' }} me-2"></i>
                                            Configuration de l'établissement
                                        </div>
                                        <div>
                                            @if(isset($etablissement))
                                                <span class="badge bg-success">Complété</span>
                                            @else
                                                <a href="{{ route('parametres.index') }}" class="badge bg-secondary text-decoration-none">À faire</a>
                                            @endif
                                        </div>
                                    </div>
                                    
                                    <div class="list-group-item px-0 py-2 d-flex justify-content-between align-items-center">
                                        <div>
                                            <i class="fas {{ $classCount > 0 ? 'fa-check-circle text-success' : 'fa-circle text-muted' }} me-2"></i>
                                            Configuration des classes
                                        </div>
                                        <div>
                                            @if($classCount > 0)
                                                <span class="badge bg-success">Complété</span>
                                            @else
                                                <a href="{{ route('parametres.classes') }}" class="badge bg-secondary text-decoration-none">À faire</a>
                                            @endif
                                        </div>
                                    </div>
                                    
                                    <div class="list-group-item px-0 py-2 d-flex justify-content-between align-items-center">
                                        <div>
                                            <i class="fas {{ $fileCount > 0 ? 'fa-check-circle text-success' : 'fa-circle text-muted' }} me-2"></i>
                                            Importation des fichiers
                                        </div>
                                        <div>
                                            @if($fileCount > 0)
                                                <span class="badge bg-success">Complété</span>
                                            @else
                                                <a href="{{ route('semestre1.base') }}" class="badge bg-warning text-dark text-decoration-none">À faire</a>
                                            @endif
                                        </div>
                                    </div>
                                    
                                    <div class="list-group-item px-0 py-2 d-flex justify-content-between align-items-center">
                                        <div>
                                            <i class="fas fa-circle text-muted me-2"></i>
                                            Analyse des disciplines
                                        </div>
                                        <div>
                                            <a href="{{ route('semestre1.analyse') }}" class="badge bg-primary text-decoration-none">Explorer</a>
                                        </div>
                                    </div>
                                    
                                    <div class="list-group-item px-0 py-2 d-flex justify-content-between align-items-center">
                                        <div>
                                            <i class="fas fa-circle text-muted me-2"></i>
                                            Génération de rapports
                                        </div>
                                        <div>
                                            <a href="{{ route('semestre1.rapports') }}" class="badge bg-primary text-decoration-none">Explorer</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Calendrier -->
                        <div class="col-md-6 mb-3">
                            <div class="performance-overview h-100">
                                <div class="performance-header">
                                    <h3 class="performance-title">Calendrier académique</h3>
                                    <small class="text-muted">Progression de l'année scolaire</small>
                                </div>
                                
                                <div class="timeline mt-3">
                                    <!-- Étapes du calendrier -->
                                    <div class="timeline-event">
                                        <div class="timeline-dot timeline-dot-active"></div>
                                        <h6 class="mb-1">Début de l'année scolaire</h6>
                                        <small class="text-muted">Octobre 2024</small>
                                    </div>
                                    
                                    <div class="timeline-event">
                                        <div class="timeline-dot timeline-dot-current"></div>
                                        <h6 class="mb-1">Semestre 1 en cours</h6>
                                        <small class="text-muted">Octobre 2024 - Février 2025</small>
                                    </div>
                                    
                                    <div class="timeline-event">
                                        <div class="timeline-dot timeline-dot-upcoming"></div>
                                        <h6 class="mb-1">Début du Semestre 2</h6>
                                        <small class="text-muted">Mars 2025</small>
                                    </div>
                                    
                                    <div class="timeline-event">
                                        <div class="timeline-dot timeline-dot-upcoming"></div>
                                        <h6 class="mb-1">Fin de l'année scolaire</h6>
                                        <small class="text-muted">Juillet 2025</small>
                                    </div>
                                </div>
                                
                                <div class="text-center mt-3 pt-3 border-top">
                                    <a href="{{ route('parametres.annee') }}" class="btn btn-sm btn-outline-secondary">
                                        <i class="fas fa-cog me-1"></i> Configurer l'année scolaire
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Aperçu des rapports disponibles -->
                <div class="section">
                    <h2 class="section-title">
                        <i class="fas fa-file-alt"></i> Rapports disponibles
                    </h2>
                    
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="performance-overview h-100">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="activity-icon activity-icon-blue me-3">
                                        <i class="fas fa-file-pdf"></i>
                                    </div>
                                    <div>
                                        <h5 class="mb-0">Bulletin de moyenne</h5>
                                        <small class="text-muted">Résultats par élève</small>
                                    </div>
                                </div>
                                <p class="small text-muted mb-3">Générez des bulletins individuels avec les moyennes par discipline et les commentaires.</p>
                                <a href="{{ route('semestre1.rapports') }}" class="btn btn-sm btn-outline-primary d-block">
                                    <i class="fas fa-file-export me-1"></i> Générer
                                </a>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="performance-overview h-100">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="activity-icon activity-icon-green me-3">
                                        <i class="fas fa-chart-pie"></i>
                                    </div>
                                    <div>
                                        <h5 class="mb-0">Rapport de classe</h5>
                                        <small class="text-muted">Statistiques par classe</small>
                                    </div>
                                </div>
                                <p class="small text-muted mb-3">Analysez les performances globales par classe avec des graphiques comparatifs.</p>
                                <a href="{{ route('semestre1.rapports') }}" class="btn btn-sm btn-outline-success d-block">
                                    <i class="fas fa-file-export me-1"></i> Générer
                                </a>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="performance-overview h-100">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="activity-icon activity-icon-orange me-3">
                                        <i class="fas fa-table"></i>
                                    </div>
                                    <div>
                                        <h5 class="mb-0">Tableau récapitulatif</h5>
                                        <small class="text-muted">Vue d'ensemble des résultats</small>
                                    </div>
                                </div>
                                <p class="small text-muted mb-3">Obtenez un tableau récapitulatif des résultats de tous les élèves par classe.</p>
                                <a href="{{ route('semestre1.rapports') }}" class="btn btn-sm btn-outline-warning d-block">
                                    <i class="fas fa-file-export me-1"></i> Générer
                                </a>
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
        // Animation des statistiques à l'affichage
        const statValues = document.querySelectorAll('.stat-card-value');
        
        statValues.forEach(stat => {
            const finalValue = stat.textContent;
            stat.textContent = '0';
            
            setTimeout(() => {
                animateValue(stat, 0, parseFloat(finalValue), 1500);
            }, 300);
        });
        
        // Fonction pour animer les valeurs numériques
        function animateValue(element, start, end, duration) {
            // Pour éviter les divisions par zéro ou les cas où start et end sont identiques
            if (start === end) {
                element.textContent = Number.isInteger(end) ? end : end.toFixed(2);
                return;
            }
            
            let current = start;
            const range = Math.abs(end - start);
            const step = Math.abs(range / duration * 10); // Augmenter le pas pour accélérer l'animation
            const increment = end > start ? step : -step;
            
            const timer = setInterval(() => {
                current += increment;
                
                // Vérifier si nous avons dépassé la valeur finale
                if ((increment > 0 && current >= end) || (increment < 0 && current <= end)) {
                    clearInterval(timer);
                    current = end; // Assurer que la valeur finale est exacte
                }
                
                // Si la valeur est un entier
                if (Number.isInteger(end)) {
                    element.textContent = Math.round(current);
                } else {
                    // Si c'est un nombre décimal (comme pour les moyennes)
                    element.textContent = current.toFixed(2);
                }
            }, 50);
        }
    });
</script>
@endsection