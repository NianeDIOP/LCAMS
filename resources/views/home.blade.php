@extends('layouts.landing')

@section('title', 'Accueil')

@section('content')
<!-- Hero Section -->
<section class="hero-section">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h1 class="hero-title">LCAMS - Analyse de données scolaires simplifiée</h1>
                <p class="hero-subtitle">Importez, analysez et interprétez facilement les données d'évaluation générées par la plateforme PLANETE.</p>
                <div class="d-flex gap-3">
                    <a href="{{ route('parametres.index') }}" class="btn btn-light btn-lg">Commencer</a>
                    <a href="{{ route('importation.s1') }}" class="btn btn-outline-light btn-lg">Importer des données</a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Features Section -->
<section class="container">
    <div class="row mb-5">
        <div class="col-lg-12 text-center">
            <h2>Fonctionnalités principales</h2>
            <p class="text-muted">Découvrez comment LCAMS peut vous aider à analyser vos données scolaires</p>
        </div>
    </div>
    
    <div class="row g-4">
        <div class="col-lg-3 col-md-6">
            <div class="feature-card">
                <div class="feature-icon icon-primary">
                    <i class="fas fa-file-import"></i>
                </div>
                <h3 class="feature-title">Importation facile</h3>
                <p class="feature-desc">Importez directement vos fichiers Excel générés par PLANETE sans manipulation complexe.</p>
                <a href="{{ route('importation.s1') }}" class="btn btn-primary">Importer</a>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6">
            <div class="feature-card">
                <div class="feature-icon icon-success">
                    <i class="fas fa-chart-line"></i>
                </div>
                <h3 class="feature-title">Analyse des moyennes</h3>
                <p class="feature-desc">Visualisez et analysez les performances globales par classe, niveau et sexe.</p>
                <a href="{{ route('semestre1.analyse-moyennes') }}" class="btn btn-success">Analyser</a>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6">
            <div class="feature-card">
                <div class="feature-icon icon-warning">
                    <i class="fas fa-chart-bar"></i>
                </div>
                <h3 class="feature-title">Analyse par discipline</h3>
                <p class="feature-desc">Évaluez les performances par matière pour identifier forces et faiblesses.</p>
                <a href="{{ route('semestre1.analyse-disciplines') }}" class="btn btn-warning">Découvrir</a>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6">
            <div class="feature-card">
                <div class="feature-icon icon-info">
                    <i class="fas fa-file-pdf"></i>
                </div>
                <h3 class="feature-title">Rapports détaillés</h3>
                <p class="feature-desc">Générez des rapports personnalisés pour vos analyses et présentations.</p>
                <a href="{{ route('semestre1.rapports') }}" class="btn btn-info">Générer</a>
            </div>
        </div>
    </div>
</section>

<!-- Steps Section -->
<section class="container mt-5">
    <div class="row mb-4">
        <div class="col-lg-12 text-center">
            <h2>Comment ça marche</h2>
            <p class="text-muted">Suivez ces étapes simples pour tirer le meilleur parti de LCAMS</p>
        </div>
    </div>
    
    <div class="row g-4">
        <div class="col-md-3">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 60px; height: 60px;">
                        <h3 class="mb-0">1</h3>
                    </div>
                    <h5>Configuration</h5>
                    <p class="text-muted">Configurez les informations de base dans les paramètres</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="rounded-circle bg-success text-white d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 60px; height: 60px;">
                        <h3 class="mb-0">2</h3>
                    </div>
                    <h5>Importation</h5>
                    <p class="text-muted">Importez vos fichiers Excel par semestre</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="rounded-circle bg-warning text-white d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 60px; height: 60px;">
                        <h3 class="mb-0">3</h3>
                    </div>
                    <h5>Analyse</h5>
                    <p class="text-muted">Explorez les analyses et visualisations</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="rounded-circle bg-info text-white d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 60px; height: 60px;">
                        <h3 class="mb-0">4</h3>
                    </div>
                    <h5>Rapports</h5>
                    <p class="text-muted">Générez des rapports détaillés pour vos besoins</p>
                </div>
            </div>
        </div>
    </div>
</section>

@if(isset($configuration) && $configuration)
<!-- School Info Section -->
<section class="container mt-5">
    <div class="card border-0 shadow">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-2 text-center">
                    @if($configuration->logo_path)
                        <img src="{{ asset('storage/' . $configuration->logo_path) }}" alt="Logo" class="img-fluid" style="max-width: 120px;">
                    @else
                        <div class="bg-light rounded d-flex justify-content-center align-items-center" style="width: 120px; height: 120px; margin: 0 auto;">
                            <i class="fas fa-school fa-3x text-secondary"></i>
                        </div>
                    @endif
                </div>
                <div class="col-md-10">
                    <h4>{{ $configuration->nom_etablissement }}</h4>
                    <div class="row">
                        <div class="col-md-4">
                            <p class="mb-1"><i class="fas fa-map-marker-alt me-2 text-primary"></i>{{ $configuration->adresse ?: 'Adresse non définie' }}</p>
                        </div>
                        <div class="col-md-4">
                            <p class="mb-1"><i class="fas fa-phone me-2 text-primary"></i>{{ $configuration->telephone ?: 'Téléphone non défini' }}</p>
                        </div>
                        <div class="col-md-4">
                            <p class="mb-1"><i class="fas fa-university me-2 text-primary"></i>{{ $configuration->inspection_academie ?: 'Inspection d\'académie non définie' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endif
@endsection