@extends('layouts.app')

@section('title', 'Gestion de l\'année scolaire')

@section('styles')
<style>
    /* Styles spécifiques à la page de l'année scolaire */
    .page-header {
        background: linear-gradient(120deg, #f5f7fa, #e4e9f2);
        padding: 1.5rem 0;
        margin-bottom: 1.75rem;
    }
    
    .tab-header {
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        margin-bottom: 1.5rem;
    }
    
    .nav-tabs {
        border-bottom: none;
    }
    
    .nav-tabs .nav-item {
        margin-bottom: 0;
    }
    
    .nav-tabs .nav-link {
        border: none;
        font-size: 0.875rem;
        font-weight: 500;
        padding: 0.75rem 1rem;
        color: var(--secondary);
        border-bottom: 2px solid transparent;
        border-radius: 0;
        transition: all 0.2s;
    }
    
    .nav-tabs .nav-link:hover:not(.active) {
        color: var(--dark);
        border-color: rgba(0, 0, 0, 0.1);
    }
    
    .nav-tabs .nav-link.active {
        color: var(--primary);
        border-bottom: 2px solid var(--primary);
        background-color: transparent;
    }
    
    .current-year {
        font-size: 2.5rem;
        font-weight: 700;
        color: #0062cc;
        text-align: center;
        margin-bottom: 1.5rem;
    }
    
    .form-control, .form-select {
        font-size: 0.875rem;
        border-radius: 0.375rem;
        padding: 0.5rem 0.75rem;
        border-color: #dce1e6;
    }
    
    .form-select-lg {
        height: auto;
        padding-top: 0.75rem;
        padding-bottom: 0.75rem;
        font-size: 1.1rem;
    }
    
    .guide-card {
        font-size: 0.875rem;
    }
    
    .guide-card h6 {
        font-size: 0.95rem;
        font-weight: 600;
    }
    
    .guide-icon {
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 6px;
        margin-right: 0.75rem;
    }
    
    .alert-warning {
        border-left: 4px solid #ffc107;
        background-color: #fff8e1;
        color: #856404;
    }
</style>
@endsection

@section('content')
<!-- En-tête de page -->
<div class="page-header">
    <div class="container">
        <h1 class="page-title">Paramètres</h1>
        <p class="page-subtitle text-muted">Gestion de l'année scolaire</p>
    </div>
</div>

<div class="container">
    <!-- Messages de notification -->
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <!-- Onglets de navigation -->
    <div class="tab-header">
        <ul class="nav nav-tabs" id="parametresTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <a class="nav-link" href="{{ route('parametres.index') }}">
                    <i class="fas fa-school me-2"></i>Établissement
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link" href="{{ route('parametres.niveaux') }}">
                    <i class="fas fa-layer-group me-2"></i>Niveaux
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link" href="{{ route('parametres.classes') }}">
                    <i class="fas fa-chalkboard me-2"></i>Classes
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link active" href="{{ route('parametres.annee') }}">
                    <i class="fas fa-calendar-alt me-2"></i>Année scolaire
                </a>
            </li>
        </ul>
    </div>

    <!-- Contenu -->
    <div class="row">
        <!-- Configuration de l'année scolaire -->
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h5 class="mb-0">Année scolaire en cours</h5>
                </div>
                <div class="card-body p-4">
                    <div class="current-year mb-4">
                        {{ $etablissement->annee_scolaire ?? '2024-2025' }}
                    </div>
                    
                    <form action="{{ route('parametres.saveAnneeScolaire') }}" method="POST">
                        @csrf
                        
                        <div class="mb-4">
                            <label for="annee_scolaire" class="form-label">Changer l'année scolaire</label>
                            <select class="form-select form-select-lg mb-3" id="annee_scolaire" name="annee_scolaire">
                                @foreach($anneesScolaires as $annee)
                                    <option value="{{ $annee }}" {{ (isset($etablissement) && $etablissement->annee_scolaire == $annee) ? 'selected' : '' }}>
                                        {{ $annee }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="form-text">Sélectionnez l'année scolaire active pour l'établissement.</div>
                        </div>
                        
                        <div class="alert alert-warning mb-4">
                            <div class="d-flex">
                                <div class="me-3">
                                    <i class="fas fa-exclamation-triangle fa-2x"></i>
                                </div>
                                <div>
                                    <strong>Attention :</strong>
                                    <p class="mb-0">La modification de l'année scolaire affectera toutes les nouvelles données importées et les classes créées. Cette action n'affectera pas les données déjà importées.</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i> Mettre à jour l'année scolaire
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Guide d'utilisation -->
        <div class="col-lg-4">
            <div class="card shadow-sm guide-card">
                <div class="card-header">
                    <h5 class="mb-0">Guide d'utilisation</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-start mb-3">
                        <div class="guide-icon bg-primary-subtle text-primary">
                            <i class="fas fa-info"></i>
                        </div>
                        <div>
                            <h6>À propos de l'année scolaire</h6>
                            <p class="text-muted small mb-0">L'année scolaire est utilisée pour organiser les données par période académique et associer les classes à une année spécifique.</p>
                        </div>
                    </div>
                    
                    <div class="d-flex align-items-start mb-3">
                        <div class="guide-icon bg-warning-subtle text-warning">
                            <i class="fas fa-lightbulb"></i>
                        </div>
                        <div>
                            <h6>Conseils</h6>
                            <ul class="text-muted small mb-0 ps-3">
                                <li>Mettez à jour l'année scolaire au début de chaque nouvelle année académique</li>
                                <li>Les classes et données sont associées à l'année scolaire configurée au moment de leur création</li>
                                <li>Vous pouvez consulter les données des années précédentes même après avoir changé l'année en cours</li>
                            </ul>
                        </div>
                    </div>
                    
                    <div class="d-flex align-items-start">
                        <div class="guide-icon bg-danger-subtle text-danger">
                        <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        <div>
                            <h6>Important</h6>
                            <p class="text-muted small mb-0">Assurez-vous d'avoir exporté et sauvegardé toutes les données importantes avant de changer d'année scolaire.</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card shadow-sm mt-4">
                <div class="card-header">
                    <h5 class="mb-0">Calendrier académique</h5>
                </div>
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-2">
                        <span class="small">Début des cours</span>
                        <span class="small text-muted">Octobre</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-2">
                        <span class="small">Fin du 1er semestre</span>
                        <span class="small text-muted">Février</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-2">
                        <span class="small">Début du 2ème semestre</span>
                        <span class="small text-muted">Mars</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="small">Fin des cours</span>
                        <span class="small text-muted">Juillet</span>
                    </div>
                </div>
                <div class="card-footer bg-light p-2">
                    <div class="text-center">
                        <a href="#" class="btn btn-sm btn-outline-secondary disabled">
                            <i class="fas fa-calendar-day me-1"></i>Configurer le calendrier
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
    document.addEventListener('DOMContentLoaded', function() {
        // Animation lors du changement d'année
        const anneeSelect = document.getElementById('annee_scolaire');
        const currentYear = document.querySelector('.current-year');
        
        anneeSelect.addEventListener('change', function() {
            currentYear.classList.add('text-primary');
            setTimeout(() => {
                currentYear.textContent = this.value;
                currentYear.classList.remove('text-primary');
                currentYear.classList.add('text-success');
                
                setTimeout(() => {
                    currentYear.classList.remove('text-success');
                    currentYear.classList.add('text-primary');
                }, 1000);
            }, 200);
        });
    });
</script>
@endsection