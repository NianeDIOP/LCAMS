@extends('layouts.module')

@section('title', 'Semestre 1 - Liste des élèves')

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
<h1 class="page-title">
    <i class="fas fa-user-graduate me-2"></i>Liste des élèves - Semestre 1
</h1>
<p class="page-subtitle">Consultez les informations détaillées des élèves pour le premier semestre de l'année scolaire {{ $anneeScolaireActive->libelle }}.</p>

<!-- Filtres -->
<div class="card mb-3">
    <div class="card-header header-primary d-flex justify-content-between align-items-center py-2">
        <div>
            <i class="fas fa-filter me-2"></i>Filtres
        </div>
        <div>
            <button class="btn btn-sm btn-light" type="button" data-bs-toggle="collapse" data-bs-target="#filterCollapse" aria-expanded="false">
                <i class="fas fa-chevron-down"></i>
            </button>
        </div>
    </div>
    <div class="card-body p-3 collapse" id="filterCollapse">
        <form action="{{ route('semestre1.eleves') }}" method="GET" id="filter-form">
            <div class="row g-2 mb-2">
                <div class="col-md-3">
                    <label for="niveau_id" class="form-label small">Niveau</label>
                    <select class="form-select form-select-sm" id="niveau_id" name="niveau_id">
                        <option value="">Tous les niveaux</option>
                        @foreach($niveaux as $niveau)
                            <option value="{{ $niveau->id }}" {{ $niveau_id == $niveau->id ? 'selected' : '' }}>{{ $niveau->libelle }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="classe_id" class="form-label small">Classe</label>
                    <select class="form-select form-select-sm" id="classe_id" name="classe_id" {{ count($classes) ? '' : 'disabled' }}>
                        <option value="">Toutes les classes</option>
                        @foreach($classes as $classe)
                            <option value="{{ $classe->id }}" {{ $classe_id == $classe->id ? 'selected' : '' }}>{{ $classe->libelle }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="sexe" class="form-label small">Sexe</label>
                    <select class="form-select form-select-sm" id="sexe" name="sexe">
                        <option value="">Tous</option>
                        <option value="M" {{ request('sexe') == 'M' ? 'selected' : '' }}>Masculin</option>
                        <option value="F" {{ request('sexe') == 'F' ? 'selected' : '' }}>Féminin</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="sort_by" class="form-label small">Trier par</label>
                    <select class="form-select form-select-sm" id="sort_by" name="sort_by">
                        <option value="nom" {{ request('sort_by') == 'nom' ? 'selected' : '' }}>Nom</option>
                        <option value="moyenne_desc" {{ request('sort_by') == 'moyenne_desc' ? 'selected' : '' }}>Moyenne (décroissante)</option>
                        <option value="moyenne_asc" {{ request('sort_by') == 'moyenne_asc' ? 'selected' : '' }}>Moyenne (croissante)</option>
                        <option value="rang" {{ request('sort_by') == 'rang' ? 'selected' : '' }}>Rang</option>
                    </select>
                </div>
            </div>
            
            <div class="row g-2">
                <div class="col-md-3">
                    <label class="form-label small">Moyenne</label>
                    <div class="input-group input-group-sm">
                        <input type="number" class="form-control form-control-sm" id="moyenne_min" name="moyenne_min" 
                               min="0" max="20" step="0.01" value="{{ request('moyenne_min') }}" placeholder="Min">
                        <span class="input-group-text">à</span>
                        <input type="number" class="form-control form-control-sm" id="moyenne_max" name="moyenne_max" 
                               min="0" max="20" step="0.01" value="{{ request('moyenne_max') }}" placeholder="Max">
                    </div>
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <div class="form-check form-check-inline mb-0">
                        <input class="form-check-input" type="checkbox" id="admis_only" name="admis_only" value="1" 
                               {{ request('admis_only') ? 'checked' : '' }}>
                        <label class="form-check-label small" for="admis_only">Élèves ayant la moyenne</label>
                    </div>
                </div>
                <div class="col-md-5 d-flex justify-content-end align-items-end">
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

<!-- Liste des élèves -->
<div class="card">
    <div class="card-header header-success">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <i class="fas fa-list me-2"></i>Résultats ({{ $eleves->total() }} élèves)
            </div>
            <!-- Dans la section des boutons d'action (généralement dans l'en-tête de la carte) -->
                <div class="btn-group">
                    <a href="{{ route('semestre1.eleves.export.pdf', request()->query()) }}" class="btn btn-sm btn-danger" target="_blank">
                        <i class="fas fa-file-pdf me-1"></i>Exporter en PDF
                    </a>
                    <a href="{{ route('semestre1.disciplines-notes', request()->query()) }}" class="btn btn-sm btn-info text-white">
                        <i class="fas fa-table me-1"></i>Voir notes par discipline
                    </a>
                </div>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>IEN</th>
                        <th>Nom</th>
                        <th>Prénom</th>
                        <th>Sexe</th>
                        <th>Classe</th>
                        <th>Moyenne</th>
                        <th>Rang</th>
                        <th>Appréciation</th>
                        <th>Décision conseil</th>
                        <th>Retard</th>
                        <th>Absence</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($eleves as $eleve)
                    <tr>
                        <td>{{ $eleve->ien }}</td>
                        <td>{{ $eleve->nom }}</td>
                        <td>{{ $eleve->prenom }}</td>
                        <td>
                            @if($eleve->sexe == 'M')
                                <span class="badge bg-primary"><i class="fas fa-mars"></i> M</span>
                            @elseif($eleve->sexe == 'F')
                                <span class="badge bg-danger"><i class="fas fa-venus"></i> F</span>
                            @else
                                <span class="badge bg-secondary">-</span>
                            @endif
                        </td>
                        <td>{{ $eleve->classe->libelle }}</td>
                        <td class="{{ $eleve->moyenneGeneraleS1 && $eleve->moyenneGeneraleS1->moyenne >= 10 ? 'text-success' : 'text-danger' }} fw-bold">
                            {{ $eleve->moyenneGeneraleS1 ? number_format($eleve->moyenneGeneraleS1->moyenne, 2) : '-' }}
                        </td>
                        <td>{{ $eleve->moyenneGeneraleS1 ? $eleve->moyenneGeneraleS1->rang : '-' }}</td>
                        <td>
                            @if($eleve->moyenneGeneraleS1)
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
                            @else
                                -
                            @endif
                        </td>
                        <td>
                            @if($eleve->moyenneGeneraleS1)
                                @php
                                    $moyenne = $eleve->moyenneGeneraleS1->moyenne;
                                    $decision = '';
                                    
                                    if ($moyenne >= 16) {
                                        $decision = 'Travail excellent';
                                        $badgeClass = 'bg-success';
                                    } elseif ($moyenne >= 12) {
                                        $decision = 'Satisfaisant doit continuer';
                                        $badgeClass = 'bg-info';
                                    } elseif ($moyenne >= 10) {
                                        $decision = 'Peut Mieux Faire';
                                        $badgeClass = 'bg-warning';
                                    } elseif ($moyenne >= 8) {
                                        $decision = 'Insuffisant';
                                        $badgeClass = 'bg-danger';
                                    } elseif ($moyenne >= 5) {
                                        $decision = 'Risque de Redoubler';
                                        $badgeClass = 'bg-danger';
                                    } else {
                                        $decision = 'Risque l\'exclusion';
                                        $badgeClass = 'bg-danger';
                                    }
                                @endphp
                                <span class="badge {{ $badgeClass }}">{{ $decision }}</span>
                            @else
                                <span class="badge bg-secondary">Non définie</span>
                            @endif
                        </td>
                        <td>{{ $eleve->moyenneGeneraleS1 ? $eleve->moyenneGeneraleS1->retard : '-' }}</td>
                        <td>{{ $eleve->moyenneGeneraleS1 ? $eleve->moyenneGeneraleS1->absence : '-' }}</td>
                        <td>
                            <a href="{{ route('semestre1.eleve-details', $eleve->id) }}" class="btn btn-sm btn-info text-white">
                                <i class="fas fa-eye"></i> Détails
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="11" class="text-center">Aucun élève trouvé pour les critères sélectionnés.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="d-flex justify-content-center mt-4">
            {{ $eleves->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>
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
        
        // Fonctionnalités d'export (à implémenter ultérieurement)
        $('#export-csv, #export-excel, #export-pdf').click(function() {
            alert('Fonctionnalité d\'export en cours de développement.');
        });
        
        // Gérer le bouton de réinitialisation
        $('button[type="reset"]').click(function(e) {
            e.preventDefault();
            // Réinitialiser les valeurs des champs
            $('#niveau_id').val('');
            $('#classe_id').val('').prop('disabled', true);
            $('#sexe').val('');
            $('#moyenne_min').val('');
            $('#moyenne_max').val('');
            $('#sort_by').val('nom');
            $('#admis_only').prop('checked', false);
            
            // Soumettre le formulaire avec les valeurs réinitialisées
            $('#filter-form').submit();
        });
        
        // Mettre des limites logiques pour les moyennes min et max
        $('#moyenne_min').change(function() {
            const min = parseFloat($(this).val());
            const max = parseFloat($('#moyenne_max').val());
            
            if (min && max && min > max) {
                $('#moyenne_max').val(min);
            }
        });
        
        $('#moyenne_max').change(function() {
            const max = parseFloat($(this).val());
            const min = parseFloat($('#moyenne_min').val());
            
            if (min && max && max < min) {
                $('#moyenne_min').val(max);
            }
        });
        
        // Activer le tri automatique au changement
        $('#sort_by').change(function() {
            $('#filter-form').submit();
        });
    });
</script>
@endsection