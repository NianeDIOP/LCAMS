@extends('layouts.app')

@section('title', 'Accueil')

@section('styles')
<style>
    /* Styles spécifiques à la page d'accueil */
    .hero-section {
        background: linear-gradient(120deg, #f5f7fa, #e4e9f2);
        padding: 3rem 0;
        margin-bottom: 2.5rem;
    }
    
    .hero-title {
        font-size: 2rem;
        font-weight: 700;
        color: #062c5e;
        margin-bottom: 1rem;
    }
    
    .hero-subtitle {
        font-size: 1.1rem;
        color: #4a5568;
        margin-bottom: 1.5rem;
        font-weight: 400;
    }
    
    .quick-access-card {
        height: 100%;
        border-radius: 12px;
        overflow: hidden;
        border: none;
        transition: transform 0.25s, box-shadow 0.25s;
    }
    
    .quick-access-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
    }
    
    .card-icon {
        width: 60px;
        height: 60px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 12px;
        background-color: rgba(0, 98, 204, 0.1);
        margin-bottom: 1.25rem;
        color: var(--primary);
        font-size: 1.5rem;
    }
    
    .feature-item {
        display: flex;
        align-items: flex-start;
        margin-bottom: 1.5rem;
    }
    
    .feature-icon {
        min-width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        background-color: rgba(0, 98, 204, 0.1);
        margin-right: 1rem;
        color: var(--primary);
        font-size: 1rem;
    }
    
    .feature-text h5 {
        font-size: 1rem;
        font-weight: 600;
        margin-bottom: 0.25rem;
    }
    
    .feature-text p {
        font-size: 0.875rem;
        color: #6c757d;
        margin-bottom: 0;
    }
    
    .section {
        padding: 2rem 0;
    }
    
    .section-header {
        text-align: center;
        margin-bottom: 2rem;
    }
    
    .section-title {
        font-size: 1.5rem;
        font-weight: 600;
        color: #062c5e;
        margin-bottom: 0.5rem;
    }
    
    .section-subtitle {
        font-size: 0.95rem;
        color: #6c757d;
    }
</style>
@endsection

@section('content')
<!-- Section Hero -->
<section class="hero-section">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-7">
                <h1 class="hero-title">Gérez et analysez les résultats scolaires en toute simplicité</h1>
                <p class="hero-subtitle">LCAMS est un logiciel de calcul et d'analyse des moyennes semestrielles conçu spécifiquement pour les établissements scolaires sénégalais.</p>
                <div class="d-flex flex-wrap gap-2">
                    <a href="#modules" class="btn btn-primary">
                        <i class="fas fa-rocket me-2"></i>Commencer maintenant
                    </a>
                    <a href="{{ route('parametres.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-cog me-2"></i>Configurer
                    </a>
                </div>
            </div>
            <div class="col-lg-5 d-none d-lg-block text-center">
                <img src="{{ asset('images/dashboard-preview.png') }}" alt="Aperçu" class="img-fluid rounded shadow" style="max-height: 300px;" onerror="this.src='https://via.placeholder.com/500x300?text=LCAMS'">
            </div>
        </div>
    </div>
</section>

<!-- Section Modules -->
<section class="section" id="modules">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">Modules principaux</h2>
            <p class="section-subtitle">Accédez rapidement aux différentes fonctionnalités</p>
        </div>
        
        <div class="row g-4">
            <!-- Semestre 1 -->
            <div class="col-md-4">
                <div class="card quick-access-card h-100">
                    <div class="card-body p-4">
                        <div class="card-icon">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                        <h3 class="card-title h5 mb-2">Semestre 1</h3>
                        <p class="card-text mb-4 small text-muted">
                            Importez, analysez et visualisez les données du premier semestre pour un suivi précis des performances.
                        </p>
                        <a href="{{ route('semestre1.index') }}" class="btn btn-sm btn-primary d-block">
                            Accéder au module <i class="fas fa-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Semestre 2 -->
            <div class="col-md-4">
                <div class="card quick-access-card h-100">
                    <div class="card-body p-4">
                        <div class="card-icon" style="background-color: rgba(40, 167, 69, 0.1); color: #28a745;">
                            <i class="fas fa-calendar-check"></i>
                        </div>
                        <h3 class="card-title h5 mb-2">Semestre 2</h3>
                        <p class="card-text mb-4 small text-muted">
                            Gérez les données du deuxième semestre et évaluez l'évolution des résultats des élèves.
                        </p>
                        <a href="{{ route('semestre2.index') }}" class="btn btn-sm btn-success d-block">
                            Accéder au module <i class="fas fa-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Général -->
            <div class="col-md-4">
                <div class="card quick-access-card h-100">
                    <div class="card-body p-4">
                        <div class="card-icon" style="background-color: rgba(23, 162, 184, 0.1); color: #17a2b8;">
                            <i class="fas fa-chart-pie"></i>
                        </div>
                        <h3 class="card-title h5 mb-2">Général</h3>
                        <p class="card-text mb-4 small text-muted">
                            Obtenez une vue d'ensemble complète des performances sur l'année scolaire entière.
                        </p>
                        <a href="{{ route('general.index') }}" class="btn btn-sm btn-info d-block text-white">
                            Accéder au module <i class="fas fa-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Section Fonctionnalités -->
<section class="section bg-light py-5">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">Fonctionnalités clés</h2>
            <p class="section-subtitle">Une solution complète pour votre établissement</p>
        </div>
        
        <div class="row">
            <div class="col-lg-6">
                <div class="feature-item">
                    <div class="feature-icon">
                        <i class="fas fa-file-import"></i>
                    </div>
                    <div class="feature-text">
                        <h5>Importation simplifiée</h5>
                        <p>Importez facilement les fichiers Excel générés par la plateforme PLANETE.</p>
                    </div>
                </div>
                
                <div class="feature-item">
                    <div class="feature-icon">
                        <i class="fas fa-chart-bar"></i>
                    </div>
                    <div class="feature-text">
                        <h5>Analyses avancées</h5>
                        <p>Analysez les performances par niveau, classe, sexe et discipline.</p>
                    </div>
                </div>
                
                <div class="feature-item">
                    <div class="feature-icon">
                        <i class="fas fa-file-pdf"></i>
                    </div>
                    <div class="feature-text">
                        <h5>Génération de rapports</h5>
                        <p>Créez des rapports détaillés et personnalisés par catégorie.</p>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-6">
                <div class="feature-item">
                    <div class="feature-icon">
                        <i class="fas fa-laptop"></i>
                    </div>
                    <div class="feature-text">
                        <h5>Application portable</h5>
                        <p>Utilisez l'application sans installation complexe, même sur clé USB.</p>
                    </div>
                </div>
                
                <div class="feature-item">
                    <div class="feature-icon">
                        <i class="fas fa-wifi-slash"></i>
                    </div>
                    <div class="feature-text">
                        <h5>Mode hors ligne</h5>
                        <p>Travaillez sans connexion internet, en toute autonomie.</p>
                    </div>
                </div>
                
                <div class="feature-item">
                    <div class="feature-icon">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <div class="feature-text">
                        <h5>Données sécurisées</h5>
                        <p>Vos données restent confidentielles et stockées localement.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Bannière "Pour commencer" -->
<section class="section">
    <div class="container">
        <div class="card border-0 bg-primary text-white">
            <div class="card-body p-4">
                <div class="row align-items-center">
                    <div class="col-lg-8">
                        <h3 class="h4 mb-2">Prêt à commencer ?</h3>
                        <p class="mb-lg-0">Configurez d'abord les paramètres de votre établissement pour tirer le meilleur parti de LCAMS.</p>
                    </div>
                    <div class="col-lg-4 text-lg-end mt-3 mt-lg-0">
                        <a href="{{ route('parametres.index') }}" class="btn btn-light">
                            <i class="fas fa-cog me-2"></i>Configurer maintenant
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection