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

@section('main-content')
    <!-- Message d'alerte si aucune donnée n'est disponible -->
    <div class="alert alert-info mb-4">
        <i class="fas fa-info-circle me-2"></i> Importez des données dans la section "Base des moyennes" pour visualiser les statistiques du Semestre 1.
    </div>
    
    <!-- Statistiques générales -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-chart-pie me-2"></i> Statistiques générales</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 text-center mb-3">
                            <h6 class="text-muted">Moyenne générale</h6>
                            <div class="display-4 fw-bold text-primary">-</div>
                        </div>
                        <div class="col-md-3 text-center mb-3">
                            <h6 class="text-muted">Taux de réussite</h6>
                            <div class="display-4 fw-bold text-success">-</div>
                        </div>
                        <div class="col-md-3 text-center mb-3">
                            <h6 class="text-muted">Mentions</h6>
                            <div class="display-4 fw-bold text-warning">-</div>
                        </div>
                        <div class="col-md-3 text-center mb-3">
                            <h6 class="text-muted">Élèves évalués</h6>
                            <div class="display-4 fw-bold text-info">-</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Résultats par niveau -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-school me-2"></i> Résultats par niveau</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-light">
                        Aucune donnée disponible pour le moment.
                    </div>
                    <!-- Un graphique sera affiché ici lorsque des données seront disponibles -->
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-venus-mars me-2"></i> Résultats par genre</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-light">
                        Aucune donnée disponible pour le moment.
                    </div>
                    <!-- Un graphique sera affiché ici lorsque des données seront disponibles -->
                </div>
            </div>
        </div>
    </div>
    
    <!-- Top 5 des classes et disciplines -->
    <div class="row">
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0"><i class="fas fa-trophy me-2"></i> Top 5 des classes</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-light">
                        Aucune donnée disponible pour le moment.
                    </div>
                    <!-- Un tableau sera affiché ici lorsque des données seront disponibles -->
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0"><i class="fas fa-book me-2"></i> Top 5 des disciplines</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-light">
                        Aucune donnée disponible pour le moment.
                    </div>
                    <!-- Un tableau sera affiché ici lorsque des données seront disponibles -->
                </div>
            </div>
        </div>
    </div>
@endsection