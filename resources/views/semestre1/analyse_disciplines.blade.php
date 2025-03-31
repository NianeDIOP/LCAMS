@extends('layouts.app')

@section('title', 'Semestre 1 - Analyse des disciplines')

@section('styles')
<link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet">
<style>
    .stats-box {
        border-radius: 10px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        padding: 15px;
        margin-bottom: 20px;
        text-align: center;
    }
    
    .stats-box .value {
        font-size: 1.8rem;
        font-weight: bold;
    }
    
    .stats-box .label {
        color: #7f8c8d;
        font-size: 0.9rem;
    }
    
    .stats-primary {
        border-left: 4px solid #3498db;
        background-color: rgba(52, 152, 219, 0.1);
    }
    
    .stats-success {
        border-left: 4px solid #2ecc71;
        background-color: rgba(46, 204, 113, 0.1);
    }
    
    .stats-warning {
        border-left: 4px solid #f39c12;
        background-color: rgba(243, 156, 18, 0.1);
    }
    
    .stats-danger {
        border-left: 4px solid #e74c3c;
        background-color: rgba(231, 76, 60, 0.1);
    }
</style>
@endsection

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('semestre1.index') }}">Semestre 1</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Analyse des disciplines</li>
                </ol>
            </nav>
            <h1><i class="fas fa-chart-bar me-2"></i>Analyse des disciplines - Semestre 1</h1>
            <p class="text-muted">Analysez les performances par discipline pour le premier semestre.</p>
        </div>
    </div>
    
    <div class="row mb-4">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0"><i class="fas fa-filter me-2"></i>Sélectionner une classe et une discipline</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label for="niveau_id" class="form-label">Niveau</label>
                            <select class="form-select" id="niveau_id">
                                <option value="">Sélectionnez un niveau</option>
                                @foreach($niveaux as $niveau)
                                    <option value="{{ $niveau->id }}">{{ $niveau->libelle }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="classe_id" class="form-label">Classe</label>
                            <select class="form-select" id="classe_id" disabled>
                                <option value="">Sélectionnez d'abord un niveau</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="discipline_id" class="form-label">Discipline</label>
                            <select class="form-select" id="discipline_id">
                                <option value="">Sélectionnez une discipline</option>
                                @foreach($disciplines as $discipline)
                                    <option value="{{ $discipline->id }}">{{ $discipline->libelle }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i>Sélectionnez un niveau, une classe et une discipline pour afficher les résultats.
            </div>
        </div>
    </div>
    
    <div id="results-container" class="d-none">
        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-success text-white">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-chart-line me-2"></i>
                            Résultats en <span id="discipline-name"></span> pour la classe <span id="classe-name"></span>
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="stats-box stats-primary">
                                    <div class="value" id="moyenne-discipline">-</div>
                                    <div class="label">Moyenne de la classe</div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="stats-box stats-success">
                                    <div class="value" id="taux-reussite">-</div>
                                    <div class="label">Taux de réussite</div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="stats-box stats-warning">
                                    <div class="value" id="moyenne-min">-</div>
                                    <div class="label">Note minimale</div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="stats-box stats-danger">
                                    <div class="value" id="moyenne-max">-</div>
                                    <div class="label">Note maximale</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-8">
                <div class="card shadow-sm">
                    <div class="card-header bg-info text-white">
                        <h5 class="card-title mb-0"><i class="fas fa-list me-2"></i>Résultats des élèves</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped" id="notes-table">
                                <thead>
                                    <tr>
                                        <th>Prénom</th>
                                        <th>Nom</th>
                                        <th>Moy DD</th>
                                        <th>Comp D</th>
                                        <th>Moy D</th>
                                        <th>Rang</th>
                                    </tr>
                                </thead>
                                <tbody id="notes-table-body">
                                    <!-- Données chargées dynamiquement -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="card-title mb-0"><i class="fas fa-venus-mars me-2"></i>Statistiques par sexe</h5>
                    </div>
                    <div class="card-body">
                        <div id="stats-by-sex-container">
                            <!-- Stats par sexe chargées dynamiquement -->
                        </div>
                    </div>
                </div>
                
                <div class="card shadow-sm">
                    <div class="card-header bg-warning text-dark">
                        <h5 class="card-title mb-0"><i class="fas fa-chart-pie me-2"></i>Répartition des notes</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="notes-distribution-chart" width="100%" height="250"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        let notesTable;
        let distributionChart;
        
        // Chargement dynamique des classes quand un niveau est sélectionné
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
        
        // Chargement des données quand une classe et une discipline sont sélectionnées
        $('#classe_id, #discipline_id').change(function() {
            loadDisciplineData();
        });
        
        function loadDisciplineData() {
            const classe_id = $('#classe_id').val();
            const discipline_id = $('#discipline_id').val();
            
            if (classe_id && discipline_id) {
                // Afficher un loader
                $('#results-container').addClass('d-none');
                $('body').append('<div id="loader" class="position-fixed top-0 start-0 w-100 h-100 d-flex justify-content-center align-items-center bg-white bg-opacity-75" style="z-index: 9999;"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Chargement...</span></div></div>');
                
                $.ajax({
                    url: '/semestre1/disciplines-classe',
                    type: 'GET',
                    data: {
                        classe_id: classe_id,
                        discipline_id: discipline_id
                    },
                    dataType: 'json',
                    success: function(data) {
                        // Mettre à jour les noms
                        $('#classe-name').text(data.classe.libelle);
                        $('#discipline-name').text(data.discipline.libelle);
                        
                        // Traiter les statistiques
                        const statistics = data.statistics;
                        const statsByGender = data.statisticsBySex;
                        
                        $('#moyenne-discipline').text(statistics.moyenne ? parseFloat(statistics.moyenne).toFixed(2) : '-');
                        
                        const successRate = statistics.total_count > 0 
                            ? ((statistics.success_count / statistics.total_count) * 100).toFixed(2) + '%' 
                            : '-';
                        $('#taux-reussite').text(successRate);
                        
                        $('#moyenne-min').text(statistics.min ? parseFloat(statistics.min).toFixed(2) : '-');
                        $('#moyenne-max').text(statistics.max ? parseFloat(statistics.max).toFixed(2) : '-');
                        
                        // Traiter les données des notes
                        const notes = data.notes;
                        let tableData = '';
                        
                        // Préparer les données pour le graphique
                        const distributionData = {
                            'moins_5': 0,
                            '5_10': 0,
                            '10_12': 0,
                            '12_14': 0,
                            '14_16': 0,
                            '16_plus': 0
                        };
                        
                        notes.forEach(function(note) {
                            const moyD = note.moy_d !== null ? parseFloat(note.moy_d).toFixed(2) : '-';
                            const moyDD = note.moy_dd !== null ? parseFloat(note.moy_dd).toFixed(2) : '-';
                            const compD = note.comp_d !== null ? parseFloat(note.comp_d).toFixed(2) : '-';
                            
                            // Ligne du tableau
                            tableData += `<tr>
                                <td>${note.prenom}</td>
                                <td>${note.nom}</td>
                                <td>${moyDD}</td>
                                <td>${compD}</td>
                                <td class="${moyD >= 10 ? 'text-success' : 'text-danger'} fw-bold">${moyD}</td>
                                <td>${note.rang_d}</td>
                            </tr>`;
                            
                            // Classement pour le graphique
                            if (moyD !== '-' && moyD !== null) {
                                const noteValue = parseFloat(moyD);
                                if (noteValue < 5) {
                                    distributionData.moins_5++;
                                } else if (noteValue < 10) {
                                    distributionData['5_10']++;
                                } else if (noteValue < 12) {
                                    distributionData['10_12']++;
                                } else if (noteValue < 14) {
                                    distributionData['12_14']++;
                                } else if (noteValue < 16) {
                                    distributionData['14_16']++;
                                } else {
                                    distributionData['16_plus']++;
                                }
                            }
                        });
                        
                        // Mettre à jour le tableau
                        $('#notes-table-body').html(tableData);
                        
                        // Initialiser DataTable si ce n'est pas déjà fait
                        if ($.fn.DataTable.isDataTable('#notes-table')) {
                            notesTable.destroy();
                        }
                        
                        notesTable = $('#notes-table').DataTable({
                            language: {
                                url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/fr-FR.json'
                            },
                            pageLength: 10,
                            order: [[5, 'asc']]
                        });
                        
                        // Mettre à jour les statistiques par sexe
                        let statsBySexHTML = '';
                        
                        statsByGender.forEach(function(stat) {
                            const sexe = stat.sexe === 'M' ? 'Garçons' : (stat.sexe === 'F' ? 'Filles' : 'Non défini');
                            const sexeIcon = stat.sexe === 'M' ? 'fa-mars' : (stat.sexe === 'F' ? 'fa-venus' : 'fa-question');
                            const colorClass = stat.sexe === 'M' ? 'primary' : (stat.sexe === 'F' ? 'danger' : 'secondary');
                            
                            const moyenne = stat.moyenne ? parseFloat(stat.moyenne).toFixed(2) : '-';
                            const successRate = stat.total_count > 0 
                                ? ((stat.success_count / stat.total_count) * 100).toFixed(2) + '%' 
                                : '-';
                            
                            statsBySexHTML += `
                            <div class="card mb-3">
                                <div class="card-body">
                                    <h5 class="card-title text-${colorClass}">
                                        <i class="fas ${sexeIcon} me-2"></i>${sexe}
                                    </h5>
                                    <div class="row">
                                        <div class="col-6">
                                            <div class="stats-box stats-${colorClass}">
                                                <div class="value">${moyenne}</div>
                                                <div class="label">Moyenne</div>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="stats-box stats-${colorClass}">
                                                <div class="value">${successRate}</div>
                                                <div class="label">Taux de réussite</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>`;
                        });
                        
                        $('#stats-by-sex-container').html(statsBySexHTML);
                        
                        // Mettre à jour le graphique de distribution
                        if (distributionChart) {
                            distributionChart.destroy();
                        }
                        
                        const ctx = document.getElementById('notes-distribution-chart').getContext('2d');
                        distributionChart = new Chart(ctx, {
                            type: 'doughnut',
                            data: {
                                labels: ['< 5', '5 à 10', '10 à 12', '12 à 14', '14 à 16', '≥ 16'],
                                datasets: [{
                                    data: [
                                        distributionData.moins_5,
                                        distributionData['5_10'],
                                        distributionData['10_12'],
                                        distributionData['12_14'],
                                        distributionData['14_16'],
                                        distributionData['16_plus']
                                    ],
                                    backgroundColor: [
                                        'rgba(231, 76, 60, 0.7)',
                                        'rgba(243, 156, 18, 0.7)',
                                        'rgba(46, 204, 113, 0.7)',
                                        'rgba(52, 152, 219, 0.7)',
                                        'rgba(155, 89, 182, 0.7)',
                                        'rgba(52, 73, 94, 0.7)'
                                    ]
                                }]
                            },
                            options: {
                                responsive: true,
                                plugins: {
                                    legend: {
                                        position: 'right',
                                    }
                                }
                            }
                        });
                        
                        // Afficher les résultats
                        $('#results-container').removeClass('d-none');
                        $('#loader').remove();
                    },
                    error: function(xhr, status, error) {
                        $('#loader').remove();
                        alert('Erreur lors du chargement des données: ' + error);
                    }
                });
            } else {
                $('#results-container').addClass('d-none');
            }
        }
    });
</script>
@endsection