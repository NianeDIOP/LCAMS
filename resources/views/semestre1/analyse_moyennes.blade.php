@extends('layouts.app')

@section('title', 'Semestre 1 - Analyse des moyennes')

@section('styles')
<link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet">
<style>
    .moyenne-card {
        border-radius: 10px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        padding: 15px;
        margin-bottom: 20px;
        text-align: center;
    }
    
    .moyenne-card .value {
        font-size: 2rem;
        font-weight: bold;
    }
    
    .moyenne-card .label {
        color: #7f8c8d;
        font-size: 0.9rem;
    }
    
    .rank-1 {
        border-left: 4px solid gold;
        background-color: rgba(255, 215, 0, 0.1);
    }
    
    .rank-2 {
        border-left: 4px solid silver;
        background-color: rgba(192, 192, 192, 0.1);
    }
    
    .rank-3 {
        border-left: 4px solid #cd7f32;
        background-color: rgba(205, 127, 50, 0.1);
    }
    
    .rank-other {
        border-left: 4px solid #3498db;
        background-color: rgba(52, 152, 219, 0.1);
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
                    <li class="breadcrumb-item active" aria-current="page">Analyse des moyennes</li>
                </ol>
            </nav>
            <h1><i class="fas fa-chart-line me-2"></i>Analyse des moyennes - Semestre 1</h1>
            <p class="text-muted">Analysez les moyennes générales des élèves pour le premier semestre.</p>
        </div>
    </div>
    
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0"><i class="fas fa-filter me-2"></i>Sélectionner une classe</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="niveau_id" class="form-label">Niveau</label>
                            <select class="form-select" id="niveau_id">
                                <option value="">Sélectionnez un niveau</option>
                                @foreach($niveaux as $niveau)
                                    <option value="{{ $niveau->id }}">{{ $niveau->libelle }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="classe_id" class="form-label">Classe</label>
                            <select class="form-select" id="classe_id" disabled>
                                <option value="">Sélectionnez d'abord un niveau</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i>Sélectionnez un niveau puis une classe pour afficher les résultats.
            </div>
        </div>
    </div>
    
    <div id="results-container" class="d-none">
        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-success text-white">
                        <h5 class="card-title mb-0"><i class="fas fa-chart-bar me-2"></i>Statistiques de la classe <span id="classe-name"></span></h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="moyenne-card">
                                    <div class="value" id="moyenne-classe">-</div>
                                    <div class="label">Moyenne de classe</div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="moyenne-card">
                                    <div class="value" id="effectif-classe">-</div>
                                    <div class="label">Effectif</div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="moyenne-card">
                                    <div class="value" id="taux-reussite">-</div>
                                    <div class="label">Taux de réussite</div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="moyenne-card">
                                    <div class="value" id="moyenne-max">-</div>
                                    <div class="label">Meilleure moyenne</div>
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
                        <h5 class="card-title mb-0"><i class="fas fa-list me-2"></i>Liste des élèves</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped" id="eleves-table">
                                <thead>
                                    <tr>
                                        <th>Rang</th>
                                        <th>Prénom</th>
                                        <th>Nom</th>
                                        <th>Sexe</th>
                                        <th>Moyenne</th>
                                    </tr>
                                </thead>
                                <tbody id="eleves-table-body">
                                    <!-- Données chargées dynamiquement -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-warning text-dark">
                        <h5 class="card-title mb-0"><i class="fas fa-trophy me-2"></i>Top 3 des élèves</h5>
                    </div>
                    <div class="card-body">
                        <div id="top-eleves-container">
                            <!-- Top élèves chargés dynamiquement -->
                        </div>
                    </div>
                </div>
                
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h5 class="card-title mb-0"><i class="fas fa-chart-pie me-2"></i>Répartition des moyennes</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="distribution-chart" width="100%" height="250"></canvas>
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
        let elevesTable;
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
        
        // Chargement des données quand une classe est sélectionnée
        $('#classe_id').change(function() {
            const classe_id = $(this).val();
            if (classe_id) {
                // Afficher un loader
                $('#results-container').addClass('d-none');
                $('body').append('<div id="loader" class="position-fixed top-0 start-0 w-100 h-100 d-flex justify-content-center align-items-center bg-white bg-opacity-75" style="z-index: 9999;"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Chargement...</span></div></div>');
                
                $.ajax({
                    url: '/semestre1/moyennes-classe/' + classe_id,
                    type: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        // Mettre à jour le nom de la classe
                        $('#classe-name').text(data.classe.libelle);
                        
                        // Traiter les données des élèves
                        const eleves = data.eleves;
                        
                        // Calculer les statistiques
                        let totalMoyenne = 0;
                        let nbEleves = 0;
                        let nbReussite = 0;
                        let moyenneMax = 0;
                        
                        // Trier les élèves par rang
                        eleves.sort((a, b) => {
                            const rangA = a.moyenne_generale_s1 ? a.moyenne_generale_s1.rang : 999;
                            const rangB = b.moyenne_generale_s1 ? b.moyenne_generale_s1.rang : 999;
                            return rangA - rangB;
                        });
                        
                        // Préparer les données pour le tableau
                        let tableData = '';
                        let topEleves = '';
                        
                        // Préparer les données pour le graphique
                        const distributionData = {
                            'moins_5': 0,
                            '5_10': 0,
                            '10_12': 0,
                            '12_14': 0,
                            '14_16': 0,
                            '16_plus': 0
                        };
                        
                        eleves.forEach(function(eleve, index) {
                            const moyenne = eleve.moyenne_generale_s1 ? eleve.moyenne_generale_s1.moyenne : '-';
                            const rang = eleve.moyenne_generale_s1 ? eleve.moyenne_generale_s1.rang : '-';
                            
                            // Calculs statistiques
                            if (moyenne !== '-' && moyenne !== null) {
                                totalMoyenne += parseFloat(moyenne);
                                nbEleves++;
                                
                                if (parseFloat(moyenne) >= 10) {
                                    nbReussite++;
                                }
                                
                                if (parseFloat(moyenne) > moyenneMax) {
                                    moyenneMax = parseFloat(moyenne);
                                }
                                
                                // Classement pour le graphique
                                if (moyenne < 5) {
                                    distributionData.moins_5++;
                                } else if (moyenne < 10) {
                                    distributionData['5_10']++;
                                } else if (moyenne < 12) {
                                    distributionData['10_12']++;
                                } else if (moyenne < 14) {
                                    distributionData['12_14']++;
                                } else if (moyenne < 16) {
                                    distributionData['14_16']++;
                                } else {
                                    distributionData['16_plus']++;
                                }
                            }
                            
                            // Ligne du tableau
                            tableData += `<tr>
                                <td>${rang}</td>
                                <td>${eleve.prenom}</td>
                                <td>${eleve.nom}</td>
                                <td>${eleve.sexe}</td>
                                <td class="${moyenne >= 10 ? 'text-success' : 'text-danger'} fw-bold">${moyenne}</td>
                            </tr>`;
                            
                            // Top 3 élèves
                            if (index < 3 && moyenne !== '-' && moyenne !== null) {
                                const rankClass = index === 0 ? 'rank-1' : (index === 1 ? 'rank-2' : 'rank-3');
                                const medal = index === 0 ? '🥇' : (index === 1 ? '🥈' : '🥉');
                                
                                topEleves += `<div class="moyenne-card ${rankClass} mb-2">
                                    <div class="d-flex align-items-center">
                                        <div class="me-3 fs-2">${medal}</div>
                                        <div class="text-start">
                                            <div class="fw-bold">${eleve.prenom} ${eleve.nom}</div>
                                            <div class="value">${moyenne}</div>
                                            <div class="label">Rang: ${rang}</div>
                                        </div>
                                    </div>
                                </div>`;
                            }
                        });
                        
                        // Mettre à jour les statistiques
                        const moyenneClasse = nbEleves > 0 ? (totalMoyenne / nbEleves).toFixed(2) : '-';
                        const tauxReussite = nbEleves > 0 ? ((nbReussite / nbEleves) * 100).toFixed(2) + '%' : '-';
                        
                        $('#moyenne-classe').text(moyenneClasse);
                        $('#effectif-classe').text(nbEleves);
                        $('#taux-reussite').text(tauxReussite);
                        $('#moyenne-max').text(moyenneMax > 0 ? moyenneMax.toFixed(2) : '-');
                        
                        // Mettre à jour le tableau
                        $('#eleves-table-body').html(tableData);
                        
                        // Initialiser DataTable si ce n'est pas déjà fait
                        if ($.fn.DataTable.isDataTable('#eleves-table')) {
                            elevesTable.destroy();
                        }
                        
                        elevesTable = $('#eleves-table').DataTable({
                            language: {
                                url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/fr-FR.json'
                            },
                            pageLength: 10,
                            order: [[0, 'asc']]
                        });
                        
                        // Mettre à jour le top des élèves
                        $('#top-eleves-container').html(topEleves);
                        
                        // Mettre à jour le graphique de distribution
                        if (distributionChart) {
                            distributionChart.destroy();
                        }
                        
                        const ctx = document.getElementById('distribution-chart').getContext('2d');
                        distributionChart = new Chart(ctx, {
                            type: 'pie',
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
                                    },
                                    tooltip: {
                                        callbacks: {
                                            label: function(context) {
                                                const value = context.raw;
                                                const total = distributionData.moins_5 + 
                                                    distributionData['5_10'] + 
                                                    distributionData['10_12'] + 
                                                    distributionData['12_14'] + 
                                                    distributionData['14_16'] + 
                                                    distributionData['16_plus'];
                                                const percentage = Math.round((value / total) * 100);
                                                return `${value} élèves (${percentage}%)`;
                                            }
                                        }
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
        });
    });
</script>
@endsection