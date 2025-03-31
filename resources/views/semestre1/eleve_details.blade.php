@extends('layouts.module')

@section('title', 'Semestre 1 - Détail de l\'élève')

@section('sidebar')
<div class="nav-title">Semestre 1</div>

<li class="nav-item">
    <a class="nav-link" href="{{ route('semestre1.index') }}">
        <span class="nav-icon"><i class="fas fa-chart-pie"></i></span>
        <span>Vue d'ensemble</span>
    </a>
</li>

<li class="nav-item">
    <a class="nav-link" href="{{ route('semestre1.analyse-moyennes') }}">
        <span class="nav-icon"><i class="fas fa-chart-line"></i></span>
        <span>Analyse Moyennes</span>
    </a>
</li>

<li class="nav-item">
    <a class="nav-link" href="{{ route('semestre1.analyse-disciplines') }}">
        <span class="nav-icon"><i class="fas fa-chart-bar"></i></span>
        <span>Analyse Disciplines</span>
    </a>
</li>

<li class="nav-item">
    <a class="nav-link active" href="{{ route('semestre1.eleves') }}">
        <span class="nav-icon"><i class="fas fa-user-graduate"></i></span>
        <span>Liste des élèves</span>
    </a>
</li>

<li class="nav-item">
    <a class="nav-link" href="{{ route('semestre1.rapports') }}">
        <span class="nav-icon"><i class="fas fa-file-alt"></i></span>
        <span>Rapports</span>
    </a>
</li>

<li class="nav-item">
    <a class="nav-link" href="{{ route('importation.s1') }}">
        <span class="nav-icon"><i class="fas fa-file-import"></i></span>
        <span>Importation</span>
    </a>
</li>

<div class="nav-title">Autres Modules</div>

<li class="nav-item">
    <a class="nav-link" href="{{ route('parametres.index') }}">
        <span class="nav-icon"><i class="fas fa-cog"></i></span>
        <span>Paramètres</span>
    </a>
</li>

<li class="nav-item">
    <a class="nav-link" href="{{ route('semestre2.index') }}">
        <span class="nav-icon"><i class="fas fa-calendar-alt"></i></span>
        <span>Semestre 2</span>
    </a>
</li>

<li class="nav-item">
    <a class="nav-link" href="{{ route('general.index') }}">
        <span class="nav-icon"><i class="fas fa-clipboard-list"></i></span>
        <span>Général</span>
    </a>
</li>
@endsection

@section('content')
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('semestre1.eleves') }}">Liste des élèves</a></li>
        <li class="breadcrumb-item active" aria-current="page">Détails de l'élève</li>
    </ol>
</nav>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="page-title mb-0">
        <i class="fas fa-user-graduate me-2"></i>{{ $eleve->prenom }} {{ $eleve->nom }}
    </h1>
    <div>
        <a href="{{ route('semestre1.eleves') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Retour à la liste
        </a>
        <a href="#" class="btn btn-primary" id="print-btn">
            <i class="fas fa-print me-2"></i>Imprimer
        </a>
    </div>
</div>

<div class="row">
    <!-- Informations personnelles -->
    <div class="col-md-4 mb-4">
        <div class="card h-100">
            <div class="card-header header-primary">
                <i class="fas fa-id-card me-2"></i>Informations personnelles
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-center mb-4">
                    <div class="avatar-placeholder bg-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 120px; height: 120px;">
                        <i class="fas {{ $eleve->sexe == 'F' ? 'fa-female' : 'fa-male' }} fa-3x text-white"></i>
                    </div>
                </div>
                
                <table class="table table-borderless">
                    <tr>
                        <th>IEN:</th>
                        <td>{{ $eleve->ien }}</td>
                    </tr>
                    <tr>
                        <th>Nom:</th>
                        <td>{{ $eleve->nom }}</td>
                    </tr>
                    <tr>
                        <th>Prénom:</th>
                        <td>{{ $eleve->prenom }}</td>
                    </tr>
                    <tr>
                        <th>Sexe:</th>
                        <td>
                            @if($eleve->sexe == 'M')
                                <span class="badge bg-primary"><i class="fas fa-mars"></i> Masculin</span>
                            @elseif($eleve->sexe == 'F')
                                <span class="badge bg-danger"><i class="fas fa-venus"></i> Féminin</span>
                            @else
                                <span class="badge bg-secondary">Non défini</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>Date de naissance:</th>
                        <td>{{ $eleve->date_naissance ? $eleve->date_naissance->format('d/m/Y') : 'Non renseignée' }}</td>
                    </tr>
                    <tr>
                        <th>Lieu de naissance:</th>
                        <td>{{ $eleve->lieu_naissance ?: 'Non renseigné' }}</td>
                    </tr>
                    <tr>
                        <th>Classe:</th>
                        <td>{{ $eleve->classe->libelle }}</td>
                    </tr>
                    <tr>
                        <th>Niveau:</th>
                        <td>{{ $eleve->classe->niveau->libelle }}</td>
                    </tr>
                    <tr>
                        <th>Année scolaire:</th>
                        <td>{{ $anneeScolaireActive->libelle }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    
    <!-- Résultats scolaires -->
    <div class="col-md-8 mb-4">
        <div class="card h-100">
            <div class="card-header header-success">
                <i class="fas fa-chart-line me-2"></i>Résultats du semestre 1
            </div>
            <div class="card-body">
                @if($eleve->moyenneGeneraleS1)
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="stats-card">
                                <div class="stats-icon stats-primary">
                                    <i class="fas fa-calculator"></i>
                                </div>
                                <div class="stats-details">
                                    <div class="stats-number {{ $eleve->moyenneGeneraleS1->moyenne >= 10 ? 'text-success' : 'text-danger' }}">
                                        {{ number_format($eleve->moyenneGeneraleS1->moyenne, 2) }}
                                    </div>
                                    <div class="stats-text">Moyenne générale</div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="stats-card">
                                <div class="stats-icon stats-success">
                                    <i class="fas fa-sort-numeric-down"></i>
                                </div>
                                <div class="stats-details">
                                    <div class="stats-number">{{ $eleve->moyenneGeneraleS1->rang }}</div>
                                    <div class="stats-text">Rang</div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="stats-card">
                                <div class="stats-icon stats-warning">
                                    <i class="fas fa-star"></i>
                                </div>
                                <div class="stats-details">
                                    <div class="stats-number">
                                        @if($eleve->moyenneGeneraleS1->appreciation == 'Félicitations')
                                            <span class="badge bg-success">{{ $eleve->moyenneGeneraleS1->appreciation }}</span>
                                        @elseif($eleve->moyenneGeneraleS1->appreciation == 'Encouragements')
                                            <span class="badge bg-info">{{ $eleve->moyenneGeneraleS1->appreciation }}</span>
                                        @elseif($eleve->moyenneGeneraleS1->appreciation == 'Tableau d\'honneur')
                                            <span class="badge bg-primary">{{ $eleve->moyenneGeneraleS1->appreciation }}</span>
                                        @elseif($eleve->moyenneGeneraleS1->appreciation == 'Passable')
                                            <span class="badge bg-warning">{{ $eleve->moyenneGeneraleS1->appreciation }}</span>
                                        @elseif(in_array($eleve->moyenneGeneraleS1->appreciation, ['Doit redoubler d\'effort', 'Avertissement', 'Blâme']))
                                            <span class="badge bg-danger">{{ $eleve->moyenneGeneraleS1->appreciation }}</span>
                                        @else
                                            <span class="badge bg-secondary">{{ $eleve->moyenneGeneraleS1->appreciation }}</span>
                                        @endif
                                    </div>
                                    <div class="stats-text">Appréciation</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="stats-card">
                                <div class="stats-icon stats-danger">
                                    <i class="fas fa-clock"></i>
                                </div>
                                <div class="stats-details">
                                    <div class="stats-number">{{ $eleve->moyenneGeneraleS1->retard ?: '0' }}</div>
                                    <div class="stats-text">Retards</div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="stats-card">
                                <div class="stats-icon stats-danger">
                                    <i class="fas fa-calendar-times"></i>
                                </div>
                                <div class="stats-details">
                                    <div class="stats-number">{{ $eleve->moyenneGeneraleS1->absence ?: '0' }}</div>
                                    <div class="stats-text">Absences</div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="stats-card">
                                <div class="stats-icon stats-warning">
                                    <i class="fas fa-exclamation-triangle"></i>
                                </div>
                                <div class="stats-details">
                                    <div class="stats-number">{{ $eleve->moyenneGeneraleS1->conseil_discipline ?: '0' }}</div>
                                    <div class="stats-text">Conseil de discipline</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    @if($eleve->moyenneGeneraleS1->decision)
                    <div class="alert alert-info">
                        <h5 class="alert-heading"><i class="fas fa-comment-alt me-2"></i>Décision du conseil</h5>
                        <p class="mb-0">{{ $eleve->moyenneGeneraleS1->decision }}</p>
                    </div>
                    @endif
                    
                    @if($eleve->moyenneGeneraleS1->observation)
                    <div class="alert alert-secondary">
                        <h5 class="alert-heading"><i class="fas fa-comment me-2"></i>Observations</h5>
                        <p class="mb-0">{{ $eleve->moyenneGeneraleS1->observation }}</p>
                    </div>
                    @endif
                @else
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>Aucune information disponible pour cet élève au semestre 1.
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Détails des notes par discipline -->
<div class="card">
    <div class="card-header header-info">
        <i class="fas fa-table me-2"></i>Notes par discipline - Semestre 1
    </div>
    <div class="card-body">
        @if(count($eleve->notesS1) > 0)
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Discipline</th>
                            <th>Type</th>
                            <th>Moyenne devoir</th>
                            <th>Compo</th>
                            <th>Moyenne discipline</th>
                            <th>Rang</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($eleve->notesS1->sortBy('discipline.libelle') as $note)
                            <tr>
                                <td>{{ $note->discipline->libelle }}</td>
                                <td>
                                    @if($note->discipline->type == 'principale')
                                        <span class="badge bg-primary">Principale</span>
                                    @elseif($note->discipline->type == 'sous-discipline')
                                        <span class="badge bg-info">Sous-discipline</span>
                                    @endif
                                </td>
                                <td>{{ $note->moy_dd !== null ? number_format($note->moy_dd, 2) : '-' }}</td>
                                <td>{{ $note->comp_d !== null ? number_format($note->comp_d, 2) : '-' }}</td>
                                <td class="{{ $note->moy_d >= 10 ? 'text-success' : 'text-danger' }} fw-bold">
                                    {{ $note->moy_d !== null ? number_format($note->moy_d, 2) : '-' }}
                                </td>
                                <td>{{ $note->rang_d ?: '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle me-2"></i>Aucune note disponible pour cet élève au semestre 1.
            </div>
        @endif
    </div>
</div>

<!-- Graphique des notes -->
<div class="card mt-4">
    <div class="card-header header-warning">
        <i class="fas fa-chart-bar me-2"></i>Graphique des notes par discipline
    </div>
    <div class="card-body">
        @if(count($eleve->notesS1) > 0)
            <canvas id="notesChart" width="400" height="200"></canvas>
        @else
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle me-2"></i>Aucune donnée disponible pour générer le graphique.
            </div>
        @endif
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Gestion de l'impression
        $('#print-btn').click(function(e) {
            e.preventDefault();
            window.print();
        });
        
        @if(count($eleve->notesS1) > 0)
        // Créer le graphique des notes
        const ctx = document.getElementById('notesChart').getContext('2d');
        
        // Préparer les données
        const disciplines = [];
        const moyennes = [];
        const backgroundColors = [];
        
        @foreach($eleve->notesS1->sortBy('discipline.libelle') as $note)
            @if($note->discipline->type == 'principale' && $note->moy_d !== null)
                disciplines.push("{{ $note->discipline->libelle }}");
                moyennes.push({{ $note->moy_d }});
                backgroundColors.push({{ $note->moy_d >= 10 ? "'rgba(46, 204, 113, 0.7)'" : "'rgba(231, 76, 60, 0.7)'" }});
            @endif
        @endforeach
        
        const notesChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: disciplines,
                datasets: [{
                    label: 'Notes par discipline',
                    data: moyennes,
                    backgroundColor: backgroundColors,
                    borderColor: backgroundColors.map(color => color.replace('0.7', '1')),
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 20,
                        ticks: {
                            stepSize: 2
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });
        @endif
    });
</script>
@endsection