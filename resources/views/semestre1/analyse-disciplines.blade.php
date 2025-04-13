@extends('semestre1.layout')

@section('title', 'Analyse des Disciplines - Semestre 1')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-filter me-1"></i> Filtres
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
                            <label for="discipline" class="form-label">Discipline</label>
                            <select id="discipline" class="form-select">
                                <option value="">Toutes les disciplines</option>
                                <!-- Les options de discipline seront chargées dynamiquement -->
                            </select>
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-search me-2"></i> Filtrer
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Performance par discipline -->
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header">
                    <i class="fas fa-chart-bar me-1"></i> Performance par Discipline
                </div>
                <div class="card-body">
                    <div class="placeholder-content">
                        <!-- Contenu qui sera affiché dynamiquement -->
                        <div class="text-center py-5">
                            <i class="fas fa-chart-bar fa-3x text-muted mb-3"></i>
                            <p class="text-muted">Sélectionnez des filtres pour afficher les performances par discipline</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Comparaison entre disciplines -->
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header">
                    <i class="fas fa-balance-scale me-1"></i> Comparaison entre Disciplines
                </div>
                <div class="card-body">
                    <div class="placeholder-content">
                        <!-- Contenu qui sera affiché dynamiquement -->
                        <div class="text-center py-5">
                            <i class="fas fa-balance-scale fa-3x text-muted mb-3"></i>
                            <p class="text-muted">Sélectionnez des filtres pour afficher la comparaison entre disciplines</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tableau des résultats par discipline -->
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div><i class="fas fa-table me-1"></i> Résultats par Discipline</div>
                    <div>
                        <button class="btn btn-sm btn-outline-secondary me-2">
                            <i class="fas fa-print"></i> Imprimer
                        </button>
                        <button class="btn btn-sm btn-outline-success">
                            <i class="fas fa-file-excel"></i> Exporter Excel
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>Discipline</th>
                                    <th>Moyenne Générale</th>
                                    <th>Minimum</th>
                                    <th>Maximum</th>
                                    <th>≥ 10/20</th>
                                    <th>< 10/20</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Les données des disciplines seront chargées dynamiquement -->
                                <tr>
                                    <td colspan="7" class="text-center py-4">
                                        <i class="fas fa-info-circle me-2"></i> Sélectionnez des filtres pour afficher les résultats par discipline
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Détail par élève (section cachée par défaut) -->
    <div class="row mt-4" id="detailEleves" style="display: none;">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div><i class="fas fa-user-graduate me-1"></i> Détail par Élève - <span id="disciplineTitle">Discipline</span></div>
                    <button class="btn btn-sm btn-outline-secondary" id="btnCloseDetail">
                        <i class="fas fa-times"></i> Fermer
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>N°</th>
                                    <th>Nom</th>
                                    <th>Prénom</th>
                                    <th>Classe</th>
                                    <th>Note</th>
                                    <th>Rang</th>
                                    <th>Appréciation</th>
                                </tr>
                            </thead>
                            <tbody id="detailElevesBody">
                                <!-- Les données des élèves seront chargées dynamiquement -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection