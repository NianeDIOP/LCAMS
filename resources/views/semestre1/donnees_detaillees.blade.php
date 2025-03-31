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
            <a href="{{ route('semestre1.donnees-detaillees.edit', request()->query()) }}" class="btn btn-sm btn-success me-2">
                <i class="fas fa-edit me-1"></i>Éditer les données
            </a>
            <a href="#" class="btn btn-sm btn-danger" onclick="alert('Fonctionnalité d\'export en cours de développement')">
                <i class="fas fa-file-pdf me-1"></i>Exporter en PDF
            </a>
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
                                $note = $notesByEleveAndDiscipline[$eleve->id][$discipline->id] ?? null;
                            @endphp
                            
                            <td class="text-center">{{ $note && $note->moy_dd !== null ? number_format($note->moy_dd, 2) : '-' }}</td>
                            <td class="text-center">{{ $note && $note->comp_d !== null ? number_format($note->comp_d, 2) : '-' }}</td>
                            <td class="text-center bg-light {{ $note && $note->moy_d >= 10 ? 'text-success' : 'text-danger' }} fw-bold">
                                {{ $note && $note->moy_d !== null ? number_format($note->moy_d, 2) : '-' }}
                            </td>
                            <td class="text-center">{{ $note && $note->rang_d ? $note->rang_d : '-' }}</td>
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
            {{ $eleves->links('pagination::bootstrap-5') }}
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
    }
    
    /* Ajuster la largeur des colonnes */
    .table th {
        white-space: nowrap;
        padding: 0.5rem;
    }
    
    .table td {
        padding: 0.5rem;
    }
    
    /* Figer les 3 premières colonnes */
    th:nth-child(1), td:nth-child(1),
    th:nth-child(2), td:nth-child(2),
    th:nth-child(3), td:nth-child(3) {
        position: sticky;
        left: 0;
        background-color: white;
        z-index: 1;
    }
    
    th:nth-child(1), td:nth-child(1) { left: 0; }
    th:nth-child(2), td:nth-child(2) { left: 60px; }  /* Ajuster selon la largeur de la première colonne */
    th:nth-child(3), td:nth-child(3) { left: 160px; } /* Ajuster selon la largeur des colonnes précédentes */
    
    tr:nth-child(odd) td:nth-child(1),
    tr:nth-child(odd) td:nth-child(2),
    tr:nth-child(odd) td:nth-child(3) {
        background-color: #f2f2f2;
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
    });
</script>
@endsection