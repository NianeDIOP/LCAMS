@extends('semestre1.layout')

@section('title', 'Rapports - Semestre 1')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-filter me-1"></i> Filtres pour le rapport
                </div>
                <div class="card-body">
                    <form action="#" method="GET" class="row g-3">
                        <div class="col-md-3">
                            <label for="niveau" class="form-label">Niveau</label>
                            <select id="niveau" class="form-select">
                                <option value="">Tous les niveaux</option>
                                <!-- Les options de niveau seront chargées dynamiquement -->
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="classe" class="form-label">Classe</label>
                            <select id="classe" class="form-select">
                                <option value="">Toutes les classes</option>
                                <!-- Les options de classe seront chargées dynamiquement -->
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="typeRapport" class="form-label">Type de rapport</label>
                            <select id="typeRapport" class="form-select">
                                <option value="synthese">Synthèse générale</option>
                                <option value="classement">Classement par mérite</option>
                                <option value="decisions">Décisions du conseil</option>
                                <option value="statistiques">Statistiques détaillées</option>
                            </select>
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-search me-2"></i> Générer le rapport
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <!-- Types de rapports disponibles -->
        <div class="col-md-12">
            <div class="card-group">
                <!-- Synthèse générale -->
                <div class="card">
                    <div class="card-body text-center">
                        <i class="fas fa-chart-pie fa-3x text-primary mb-3"></i>
                        <h5 class="card-title">Synthèse générale</h5>
                        <p class="card-text small">Vue d'ensemble des performances avec tableaux et graphiques</p>
                        <button class="btn btn-outline-primary btn-sm" data-type="synthese">
                            <i class="fas fa-file-alt me-1"></i> Générer
                        </button>
                    </div>
                </div>
                
                <!-- Classement -->
                <div class="card">
                    <div class="card-body text-center">
                        <i class="fas fa-medal fa-3x text-success mb-3"></i>
                        <h5 class="card-title">Classement par mérite</h5>
                        <p class="card-text small">Liste des élèves classés par ordre de mérite</p>
                        <button class="btn btn-outline-success btn-sm" data-type="classement">
                            <i class="fas fa-file-alt me-1"></i> Générer
                        </button>
                    </div>
                </div>
                
                <!-- Décisions du conseil -->
                <div class="card">
                    <div class="card-body text-center">
                        <i class="fas fa-gavel fa-3x text-warning mb-3"></i>
                        <h5 class="card-title">Décisions du conseil</h5>
                        <p class="card-text small">Rapport sur les décisions prises lors du conseil de classe</p>
                        <button class="btn btn-outline-warning btn-sm" data-type="decisions">
                            <i class="fas fa-file-alt me-1"></i> Générer
                        </button>
                    </div>
                </div>
                
                <!-- Statistiques détaillées -->
                <div class="card">
                    <div class="card-body text-center">
                        <i class="fas fa-chart-bar fa-3x text-danger mb-3"></i>
                        <h5 class="card-title">Statistiques détaillées</h5>
                        <p class="card-text small">Analyses statistiques approfondies par classe et par sexe</p>
                        <button class="btn btn-outline-danger btn-sm" data-type="statistiques">
                            <i class="fas fa-file-alt me-1"></i> Générer
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Prévisualisation du rapport -->
    <div class="row" id="rapportPreview">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div><i class="fas fa-file-alt me-1"></i> Prévisualisation du rapport</div>
                    <div>
                        <button class="btn btn-sm btn-outline-secondary me-2">
                            <i class="fas fa-print"></i> Imprimer
                        </button>
                        <button class="btn btn-sm btn-outline-success me-2">
                            <i class="fas fa-file-excel"></i> Exporter Excel
                        </button>
                        <button class="btn btn-sm btn-outline-danger">
                            <i class="fas fa-file-pdf"></i> Exporter PDF
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Zone de prévisualisation du rapport -->
                    <div class="report-container">
                        <div class="text-center py-5">
                            <i class="fas fa-file-alt fa-3x text-muted mb-3"></i>
                            <p class="text-muted">Sélectionnez des filtres et cliquez sur "Générer le rapport" pour afficher un aperçu ici</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Historique des rapports générés -->
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-history me-1"></i> Rapports récemment générés
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Type de rapport</th>
                                    <th>Niveau/Classe</th>
                                    <th>Généré par</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Données des rapports seront chargées dynamiquement -->
                                <tr>
                                    <td colspan="5" class="text-center py-4">
                                        <i class="fas fa-info-circle me-2"></i> Aucun rapport généré récemment
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection