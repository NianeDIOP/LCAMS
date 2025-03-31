@extends('layouts.module')

@section('title', 'Semestre 1 - Édition des données détaillées')

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
    <i class="fas fa-edit me-2"></i>Édition des données détaillées - Semestre 1
</h1>
<p class="page-subtitle">Modifiez les notes détaillées par discipline pour le premier semestre de l'année scolaire {{ $anneeScolaireActive->libelle }}.</p>

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
        <form action="{{ route('semestre1.donnees-detaillees.edit') }}" method="GET" id="filter-form">
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
                <div class="col-md-4 d-flex justify-content-end align-items-end">
                    <button type="submit" class="btn btn-sm btn-primary">
                        <i class="fas fa-search me-1"></i>Filtrer
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

@if(!$classe_id)
    <div class="alert alert-info">
        <i class="fas fa-info-circle me-2"></i>Veuillez sélectionner une classe pour éditer les données détaillées.
    </div>
@else
    <!-- Instructions pour copier-coller -->
    <div class="alert alert-info">
        <h5><i class="fas fa-clipboard me-2"></i>Copier-coller depuis Excel</h5>
        <p>Vous pouvez copier une colonne depuis Excel et coller les valeurs dans les champs correspondants.</p>
        <p><strong>Astuce :</strong> Utilisez la touche Tab pour naviguer facilement entre les champs.</p>
    </div>

    <!-- Sélecteur de discipline pour affichage simplifié -->
    <div class="card mb-3">
        <div class="card-header header-warning">
            <i class="fas fa-table me-2"></i>Sélectionner une discipline à éditer
        </div>
        <div class="card-body">
            <select class="form-select" id="discipline-selector">
                <option value="">Toutes les disciplines</option>
                @foreach($disciplines as $discipline)
                    <option value="{{ $discipline->id }}">{{ $discipline->libelle }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <!-- Formulaire d'édition des données -->
    <form action="{{ route('semestre1.donnees-detaillees.store') }}" method="POST" id="edit-form">
        @csrf
        <input type="hidden" name="classe_id" value="{{ $classe_id }}">
        <input type="hidden" name="semestre" value="1">
        
        <div class="card">
            <div class="card-header header-success d-flex justify-content-between align-items-center">
                <div>
                    <i class="fas fa-edit me-2"></i>Édition des notes ({{ count($eleves) }} élèves)
                </div>
                <div>
                    <button type="submit" class="btn btn-sm btn-success">
                        <i class="fas fa-save me-1"></i>Enregistrer les modifications
                    </button>
                    <a href="{{ route('semestre1.donnees-detaillees', request()->query()) }}" class="btn btn-sm btn-secondary ms-2">
                        <i class="fas fa-times me-1"></i>Annuler
                    </a>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-bordered table-sm m-0" id="edit-table">
                        <thead>
                            <tr>
                                <th class="text-center align-middle" rowspan="2">N°</th>
                                <th class="text-center align-middle" rowspan="2">IEN</th>
                                <th class="text-center align-middle" rowspan="2">Prénom</th>
                                <th class="text-center align-middle" rowspan="2">Nom</th>
                                
                                @foreach($disciplines as $discipline)
                                    <th class="text-center discipline-col discipline-{{ $discipline->id }}" colspan="4">{{ $discipline->libelle }}</th>
                                @endforeach
                            </tr>
                            <tr>
                                @foreach($disciplines as $discipline)
                                    <th class="text-center discipline-col discipline-{{ $discipline->id }}">Moy DD</th>
                                    <th class="text-center discipline-col discipline-{{ $discipline->id }}">Comp D</th>
                                    <th class="text-center bg-light discipline-col discipline-{{ $discipline->id }}">Moy D</th>
                                    <th class="text-center discipline-col discipline-{{ $discipline->id }}">Rang D</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($eleves as $index => $eleve)
                            <tr>
                                <td class="text-center">{{ $index + 1 }}</td>
                                <td>{{ $eleve->ien }}</td>
                                <td>{{ $eleve->prenom }}</td>
                                <td>{{ $eleve->nom }}</td>
                                
                                @foreach($disciplines as $discipline)
                                    @php
                                        $donnee_moy_dd = $donneesExistantes[$eleve->id][$discipline->id]['moy_dd'] ?? null;
                                        $donnee_comp_d = $donneesExistantes[$eleve->id][$discipline->id]['comp_d'] ?? null;
                                        $donnee_moy_d = $donneesExistantes[$eleve->id][$discipline->id]['moy_d'] ?? null;
                                        $donnee_rang_d = $donneesExistantes[$eleve->id][$discipline->id]['rang_d'] ?? null;
                                    @endphp
                                    
                                    <td class="discipline-col discipline-{{ $discipline->id }}">
                                        <input type="hidden" name="data[{{ $index }}][eleve_id]" value="{{ $eleve->id }}">
                                        <input type="text" class="form-control form-control-sm note-input moy-dd" 
                                               name="data[{{ $index }}][disciplines][{{ $discipline->id }}][moy_dd]" 
                                               value="{{ $donnee_moy_dd }}"
                                               data-eleve-index="{{ $index }}"
                                               data-discipline-id="{{ $discipline->id }}"
                                               data-type="moy_dd">
                                    </td>
                                    <td class="discipline-col discipline-{{ $discipline->id }}">
                                        <input type="text" class="form-control form-control-sm note-input comp-d" 
                                               name="data[{{ $index }}][disciplines][{{ $discipline->id }}][comp_d]" 
                                               value="{{ $donnee_comp_d }}"
                                               data-eleve-index="{{ $index }}"
                                               data-discipline-id="{{ $discipline->id }}"
                                               data-type="comp_d">
                                    </td>
                                    <td class="discipline-col discipline-{{ $discipline->id }} bg-light">
                                        <input type="text" class="form-control form-control-sm note-input moy-d" 
                                               name="data[{{ $index }}][disciplines][{{ $discipline->id }}][moy_d]" 
                                               value="{{ $donnee_moy_d }}"
                                               data-eleve-index="{{ $index }}"
                                               data-discipline-id="{{ $discipline->id }}"
                                               data-type="moy_d">
                                    </td>
                                    <td class="discipline-col discipline-{{ $discipline->id }}">
                                        <input type="text" class="form-control form-control-sm note-input rang-d" 
                                               name="data[{{ $index }}][disciplines][{{ $discipline->id }}][rang_d]" 
                                               value="{{ $donnee_rang_d }}"
                                               data-eleve-index="{{ $index }}"
                                               data-discipline-id="{{ $discipline->id }}"
                                               data-type="rang_d">
                                    </td>
                                @endforeach
                            </tr>
                            @empty
                            <tr>
                                <td colspan="{{ 4 + (count($disciplines) * 4) }}" class="text-center">Aucun élève trouvé pour cette classe.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-save me-1"></i>Enregistrer les modifications
                </button>
                <a href="{{ route('semestre1.donnees-detaillees', request()->query()) }}" class="btn btn-secondary ms-2">
                    <i class="fas fa-times me-1"></i>Annuler
                </a>
            </div>
        </div>
    </form>
@endif
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
    
    /* Figer les 4 premières colonnes */
    .table th:nth-child(-n+4), 
    .table td:nth-child(-n+4) {
        position: sticky;
        left: 0;
        background-color: white;
        z-index: 1;
    }
    
    .table th:nth-child(1), .table td:nth-child(1) { left: 0; }
    .table th:nth-child(2), .table td:nth-child(2) { left: 3rem; }
    .table th:nth-child(3), .table td:nth-child(3) { left: 8rem; }
    .table th:nth-child(4), .table td:nth-child(4) { left: 15rem; }
    
    tr:nth-child(odd) td:nth-child(-n+4) {
        background-color: #f8f9fa;
    }
    
    /* Pour les colonnes fixées des entêtes */
    thead tr:first-child th:nth-child(-n+4),
    thead tr:nth-child(2) th:nth-child(-n+4) {
        background-color: #fff;
        z-index: 2;
    }
    
    /* Style des inputs */
    .note-input {
        width: 60px;
        text-align: center;
        padding: 0.25rem;
    }
    
    .note-input:focus {
        background-color: #f0f8ff;
    }
    
    /* Mettre en évidence les cellules modifiées */
    .modified {
        background-color: #fffacd !important; /* Light yellow */
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
                        let options = '<option value="">Sélectionnez une classe</option>';
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
                $('#classe_id').html('<option value="">Sélectionnez d\'abord un niveau</option>').prop('disabled', true);
            }
        });
        
        // Filtrer par discipline
        $('#discipline-selector').change(function() {
            const disciplineId = $(this).val();
            
            if (disciplineId) {
                // Masquer toutes les colonnes de discipline
                $('.discipline-col').hide();
                // Afficher uniquement les colonnes de la discipline sélectionnée
                $('.discipline-' + disciplineId).show();
            } else {
                // Afficher toutes les colonnes
                $('.discipline-col').show();
            }
        });
        
        // Calcul automatique de la moyenne discipline
        $(document).on('input', '.moy-dd, .comp-d', function() {
            const $this = $(this);
            const eleveIndex = $this.data('eleve-index');
            const disciplineId = $this.data('discipline-id');
            const type = $this.data('type');
            
            // Trouver les autres champs pour cet élève et cette discipline
            const $moyDD = $('input[data-eleve-index="' + eleveIndex + '"][data-discipline-id="' + disciplineId + '"][data-type="moy_dd"]');
            const $compD = $('input[data-eleve-index="' + eleveIndex + '"][data-discipline-id="' + disciplineId + '"][data-type="comp_d"]');
            const $moyD = $('input[data-eleve-index="' + eleveIndex + '"][data-discipline-id="' + disciplineId + '"][data-type="moy_d"]');
            
            // Récupérer les valeurs
            const moyDD = parseFloat($moyDD.val()) || 0;
            const compD = parseFloat($compD.val()) || 0;
            
            // Calculer la moyenne (50% devoir, 50% composition)
            if (moyDD > 0 || compD > 0) {
                let moyD;
                if (moyDD > 0 && compD > 0) {
                    moyD = (moyDD + compD) / 2;
                } else if (moyDD > 0) {
                    moyD = moyDD;
                } else {
                    moyD = compD;
                }
                
                // Mettre à jour le champ moyD avec 2 décimales
                $moyD.val(moyD.toFixed(2)).addClass('modified');
            }
        });
        
        // Marquer les cellules modifiées
        $(document).on('input', '.note-input', function() {
            $(this).addClass('modified');
        });
        
        // Formater les nombres avec la virgule comme séparateur décimal (pour le français)
        function formatDecimal(value) {
            if (!value) return '';
            return value.toString().replace('.', ',');
        }
        
        function parseDecimal(value) {
            if (!value) return 0;
            return parseFloat(value.toString().replace(',', '.')) || 0;
        }
        
        // Convertir tous les champs au format décimal français lors de la soumission
        $('#edit-form').on('submit', function() {
            $('.note-input').each(function() {
                const $this = $(this);
                const value = $this.val();
                
                if (value) {
                    // Convertir les virgules en points pour le stockage
                    $this.val(parseDecimal(value));
                }
            });
            
            return true;
        });
        
        // Formater les champs au chargement
        $('.note-input').each(function() {
            const $this = $(this);
            const value = $this.val();
            
            if (value) {
                $this.val(formatDecimal(value));
            }
        });
        
        // Validation des saisies
        $('.note-input').on('input', function() {
            const $this = $(this);
            const type = $this.data('type');
            let value = $this.val().replace(',', '.');
            
            // Supprimer tout caractère non numérique sauf le point
            value = value.replace(/[^\d.]/g, '');
            
            // Limiter à une seule décimale
            const parts = value.split('.');
            if (parts.length > 1) {
                value = parts[0] + '.' + parts[1].substring(0, 2);
            }
            
            // Limiter les valeurs selon le type
            if (type === 'moy_dd' || type === 'comp_d' || type === 'moy_d') {
                // Notes: entre 0 et 20
                value = Math.min(20, parseFloat(value) || 0);
            } else if (type === 'rang_d') {
                // Rang: entiers positifs uniquement
                value = value.split('.')[0];
                value = Math.max(1, parseInt(value) || 1);
            }
            
            // Remettre la valeur formatée
            $this.val(value.toString().replace('.', ','));
        });
    });
</script>
@endsection