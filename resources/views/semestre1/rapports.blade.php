@extends('layouts.sidebar')

@section('title', 'Génération des rapports - Semestre 1')

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
    Génération des rapports - Semestre 1
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('semestre1.index') }}">Semestre 1</a></li>
    <li class="breadcrumb-item active">Génération des rapports</li>
@endsection

@section('main-content')
    <!-- Votre contenu ici -->
    <div class="alert alert-info">
        <i class="fas fa-info-circle me-2"></i> Fonctionnalité en cours de développement. Revenez bientôt !
    </div>
@endsection