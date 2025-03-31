@extends('layouts.module')

@section('title', 'Semestre 1 - Données détaillées')

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
    <a class="nav-link" href="{{ route('semestre1.eleves') }}">
        <span class="nav-icon"><i class="fas fa-user-graduate"></i></span>
        <span>Liste des élèves</span>
    </a>
</li>

<li class="nav-item">
    <a class="nav-link active" href="{{ route('semestre1.donnees-detaillees') }}">
        <span class="nav-icon"><i class="fas fa-table"></i></span>
        <span>Données détaillées</span>
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
<h1 class="page-title">
    <i class="fas fa-table me-2"></i>Données détaillées - Semestre 1
</h1>
<p class="page-subtitle">Consultez les notes détaillées par discipline pour le premier semestre de l'année scolaire {{ $anneeScolaireActive->libelle }}.</p>

<!-- Panneau de débogage temporaire -->
@if(count($eleves) > 0)
<div class="card mb-3">
    <div class="card-header bg-warning text-white d-flex justify-content-between align-items-center">
        <div>
            <i class="fas fa-bug me-2"></i>Informations de débogage (temporaire)
        </div>
        <button class="btn btn-sm btn-light" onclick="this.parentElement.parentElement.style.display='none'">
            <i class="fas fa-times"></i>
        </button>
    </div>
    <div class="card-body">
        <h6>Informations générales:</h6>
        <ul>
            <li>Nombre d'élèves: {{ $eleves->count() }}</li>
            <li>Nombre de disciplines: {{ $disciplines->count() }}</li>
            <li>Disciplines trouvées: {{ implode(', ', $disciplines->pluck('libelle')->toArray()) }}</li>
        </ul>
        
        @if($eleves->count() > 0)
            <h6>Premier élève ({{ $eleves->first()->prenom }} {{ $eleves->first()->nom }}):</h6>
            <ul>
                @php
                    $premierEleve = $eleves->first();
                    $notesEleve = $notes[$premierEleve->id] ?? [];
                @endphp
                <li>ID: {{ $premierEleve->id }}</li>
                <li>Nombre de notes: {{ count($notesEleve) }}</li>
                
                @if(count($notesEleve) > 0)
                    <li>Disciplines avec notes:
                        <ul>
                            @foreach($notesEleve as $disciplineId => $note)
                                @php
                                    $discipline = $disciplines->firstWhere('id', $disciplineId);
                                    $disciplineName = $discipline ? $discipline->libelle : "Discipline inconnue (ID: $disciplineId)";
                                @endphp
                                <li>
                                    {{ $disciplineName }}: 
                                    Moy DD={{ $note->moy_dd !== null ? number_format($note->moy_dd, 2) : 'N/A' }}, 
                                    Comp D={{ $note->comp_d !== null ? number_format($note->comp_d, 2) : 'N/A' }}, 
                                    Moy D={{ $note->moy_d !== null ? number_format($note->moy_d, 2) : 'N/A' }}, 
                                    Rang D={{ $note->rang_d ?? 'N/A' }}
                                </li>
                            @endforeach
                        </ul>
                    </li>
                @else
                    <li>Aucune note trouvée pour cet élève!</li>
                @endif
            </ul>
        @endif
    </div>
</div>
@endif

<!-- Filtres -->
<div class="card mb-3">
    <div class="card-header header-primary d-flex justify-content-between align-items-center py-2">
        <div>
            <i class="fas fa-filter me-2"></i>Filtres
        </div>
        <div>
            <button class="btn btn-sm btn-light" type="button" data-bs-toggle="collapse" data-bs-target="#filterCollapse" aria-expanded="true">
                <i class="fas fa-chevron-down"></i>
            </button>
        </div>
    </div>
    <div class="card-body p-3 collapse show" id="filterCollapse">
        <form action="{{ route('semestre1.donnees-detaillees') }}" method="GET" id="filter-form">
            <div class="row g-2 mb-2">
                <div class="col-md-4">
                    <label for="niveau_id" class="form-label small">Niveau</label>
                    <select class="form-select form-select-sm" id="niveau_id" name="niveau_id">
                        <option value="">Tous les niveaux</option>
                        @foreach($niveaux as $niveau)
                            <option value="{{ $niveau->id }}" {{ $niveau_id == $niveau->id ? 'selected' : '' }}>{{ $niveau->libelle }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="classe_id" class="form-label small">Classe</label>
                    <select class="form-select form-select-sm" id="classe_id" name="classe_id" {{ count($classes) ? '' : 'disabled' }}>
                        <option value="">Toutes les classes</option>
                        @foreach($classes as $classe)
                            <option value="{{ $classe->id }}" {{ $classe_id == $classe->id ? 'selected' : '' }}>{{ $classe->libelle }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="sort_by" class="form-label small">Trier par</label>
                    <select class="form-select form-select-sm" id="sort_by" name="sort_by">
                        <option value="nom" {{ request('sort_by') == 'nom' ? 'selected' : '' }}>Nom</option>
                        <option value="moyenne_desc" {{ request('sort_by') == 'moyenne_desc' ? 'selected' : '' }}>Moyenne (décroissante)</option>
                        <option value="moyenne_asc" {{ request('sort_by') == 'moyenne_asc' ? 'selected' : '' }}>Moyenne (croissante)</option>
                    </select>
                </div>
            </div>
            
            <div class="row g-2">
                <div class="col-md-12 d-flex justify-content-end">
                    <button type="reset" class="btn btn-sm btn-secondary me-1">
                        <i class="fas fa-undo me-1"></i>Réinitialiser
                    </button>
                    <button type="submit" class="btn btn-sm btn-primary">
                        <i class="fas fa-search me-1"></i>Filtrer
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Données détaillées -->
<div class="card">
    <div class="card-header header-success d-flex justify-content-between align-items-center">
        <div>
            <i class="fas fa-table me-2"></i>Notes détaillées par discipline ({{ $eleves->total() }} élèves)
        </div>
        <div>
            <button type="button" class="btn btn-sm btn-danger" id="export-pdf-btn">
                <i class="fas fa-file-pdf me-1"></i>Exporter en PDF
            </button>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-bordered table-sm m-0">
                <thead>
                    <tr>
                        <th class="text-center align-middle" rowspan="2">IEN</th>
                        <th class="text-center align-middle" rowspan="2">Prénom</th>
                        <th class="text-center align-middle" rowspan="2">Nom</th>
                        
                        @foreach($disciplines as $discipline)
                            <th class="text-center" colspan="4">{{ $discipline->libelle }}</th>
                        @endforeach
                    </tr>
                    <tr>
                        @foreach($disciplines as $discipline)
                            <th class="text-center">Moy DD</th>
                            <th class="text-center">Comp D</th>
                            <th class="text-center bg-light">Moy D</th>
                            <th class="text-center">Rang D</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @forelse($eleves as $eleve)
                    <tr>
                        <td>{{ $eleve->ien }}</td>
                        <td>{{ $eleve->prenom }}</td>
                        <td>{{ $eleve->nom }}</td>
                        
                        @foreach($disciplines as $discipline)
                            @php
                                // Vérification explicite pour récupérer les notes de l'élève pour cette discipline
                                $note = null;
                                if (isset($notes[$eleve->id]) && isset($notes[$eleve->id][$discipline->id])) {
                                    $note = $notes[$eleve->id][$discipline->id];
                                }
                                
                                // Formattage des valeurs pour affichage
                                $moyDD = $note && $note->moy_dd !== null ? number_format($note->moy_dd, 2) : '-';
                                $compD = $note && $note->comp_d !== null ? number_format($note->comp_d, 2) : '-';
                                $moyD = $note && $note->moy_d !== null ? number_format($note->moy_d, 2) : '-';
                                $rangD = $note && $note->rang_d ? $note->rang_d : '-';
                                
                                // Classe pour la coloration des moyennes
                                $textClass = $note && $note->moy_d !== null ? ($note->moy_d >= 10 ? 'text-success' : 'text-danger') : 'text-secondary';
                            @endphp
                            
                            <td class="text-center">{{ $moyDD }}</td>
                            <td class="text-center">{{ $compD }}</td>
                            <td class="text-center bg-light {{ $textClass }} fw-bold">{{ $moyD }}</td>
                            <td class="text-center">{{ $rangD }}</td>
                        @endforeach
                    </tr>
                    @empty
                    <tr>
                        <td colspan="{{ 3 + (count($disciplines) * 4) }}" class="text-center">Aucun élève trouvé pour les critères sélectionnés.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="d-flex justify-content-center p-3">
            {{ $eleves->links() }}
        </div>
    </div>
</div>

<!-- Légende -->
<div class="card mt-3">
    <div class="card-header header-info">
        <i class="fas fa-info-circle me-2"></i>Légende
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-3">
                <p><strong>Moy DD:</strong> Moyenne des devoirs et interrogations</p>
            </div>
            <div class="col-md-3">
                <p><strong>Comp D:</strong> Note de composition</p>
            </div>
            <div class="col-md-3">
                <p><strong>Moy D:</strong> Moyenne de la discipline</p>
            </div>
            <div class="col-md-3">
                <p><strong>Rang D:</strong> Rang de l'élève dans la discipline</p>
            </div>
        </div>
        <div class="alert alert-info">
            <p class="mb-0"><i class="fas fa-lightbulb me-2"></i>Conseil: Utilisez les filtres pour affiner les résultats et voir les notes par classe.</p>
        </div>
    </div>
</div>

<!-- Modal d'exportation -->
<div class="modal fade" id="exportModal" tabindex="-1" aria-labelledby="exportModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exportModalLabel">Exporter les données</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Préparation de l'exportation en PDF...</p>
                <div class="progress">
                    <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%"></div>
                </div>
                <p class="text-muted mt-3">Cette fonctionnalité est en cours de développement. Le fichier PDF sera disponible prochainement.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    .table-responsive {
        overflow-x: auto;
        max-width: 100%;
    }
    
    /* Style pour mettre en évidence la colonne Moy D */
    .bg-light {
        background-color: rgba(0, 123, 255, 0.1) !important;
    }
    
    /* Styles supplémentaires pour la table */
    .table-bordered th,
    .table-bordered td {
        border: 1px solid #dee2e6;
        font-size: 0.9rem;
    }
    
    /* Ajuster la largeur des colonnes */
    .table th {
        white-space: nowrap;
        padding: 0.5rem;
    }
    
    .table td {
        padding: 0.4rem;
    }
    
    /* Figer les 3 premières colonnes */
    .table th:nth-child(1), .table td:nth-child(1),
    .table th:nth-child(2), .table td:nth-child(2),
    .table th:nth-child(3), .table td:nth-child(3) {
        position: sticky;
        left: 0;
        background-color: white;
        z-index: 1;
    }
    
    .table th:nth-child(1), .table td:nth-child(1) { left: 0; }
    .table th:nth-child(2), .table td:nth-child(2) { left: 5rem; }  /* Ajuster selon la largeur de la première colonne */
    .table th:nth-child(3), .table td:nth-child(3) { left: 12rem; } /* Ajuster selon la largeur des colonnes précédentes */
    
    tr:nth-child(odd) td:nth-child(1),
    tr:nth-child(odd) td:nth-child(2),
    tr:nth-child(odd) td:nth-child(3) {
        background-color: #f8f9fa;
    }
    
    /* Pour les colonnes fixées des entêtes */
    thead tr:first-child th:nth-child(1),
    thead tr:first-child th:nth-child(2),
    thead tr:first-child th:nth-child(3) {
        background-color: #fff;
        z-index: 2;
    }
    
    thead tr:nth-child(2) th:nth-child(1),
    thead tr:nth-child(2) th:nth-child(2),
    thead tr:nth-child(2) th:nth-child(3) {
        background-color: #fff;
        z-index: 2;
    }
</style>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Gestion du changement de niveau pour filtrer les classes
        $('#niveau_id').change(function() {
            const niveau_id = $(this).val();
            if (niveau_id) {
                $.ajax({
                    url: '/api/niveaux/' + niveau_id + '/classes',
                    type: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        let options = '<option value="">Toutes les classes</option>';
                        $.each(data, function(key, classe) {
                            options += '<option value="' + classe.id + '">' + classe.libelle + '</option>';
                        });
                        $('#classe_id').html(options).prop('disabled', false);
                    },
                    error: function() {
                        alert('Erreur lors du chargement des classes');
                    }
                });
            } else {
                $('#classe_id').html('<option value="">Toutes les classes</option>').prop('disabled', true);
            }
        });
        
        // Gérer le bouton de réinitialisation
        $('button[type="reset"]').click(function(e) {
            e.preventDefault();
            // Réinitialiser les valeurs des champs
            $('#niveau_id').val('');
            $('#classe_id').val('').prop('disabled', true);
            $('#sort_by').val('nom');
            
            // Soumettre le formulaire avec les valeurs réinitialisées
            $('#filter-form').submit();
        });
        
        // Activer le tri automatique au changement
        $('#sort_by').change(function() {
            $('#filter-form').submit();
        });
        
        // Gestion de l'exportation en PDF
        $('#export-pdf-btn').click(function() {
            $('#exportModal').modal('show');
            
            // Simuler un chargement
            setTimeout(function() {
                $('#exportModal').modal('hide');
                alert('La fonctionnalité d\'exportation en PDF est en cours de développement.');
            }, 2000);
        });
    });
</script>
@endsection