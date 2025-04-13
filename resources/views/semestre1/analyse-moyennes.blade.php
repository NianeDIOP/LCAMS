@extends('semestre1.layout')

@section('title', 'Analyse des Moyennes - Semestre 1')

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
                            <label for="sexe" class="form-label">Sexe</label>
                            <select id="sexe" class="form-select">
                                <option value="">Tous</option>
                                <option value="M">Masculin</option>
                                <option value="F">Féminin</option>
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
        <!-- Synthèse générale -->
        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <div class="card-header">
                    <i class="fas fa-chart-pie me-1"></i> Synthèse Générale
                </div>
                <div class="card-body">
                    <div class="placeholder-content">
                        <!-- Contenu qui sera affiché dynamiquement -->
                        <div class="text-center py-5">
                            <i class="fas fa-chart-pie fa-3x text-muted mb-3"></i>
                            <p class="text-muted">Sélectionnez des filtres pour afficher la synthèse des moyennes</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Répartition par groupe -->
        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <div class="card-header">
                    <i class="fas fa-users me-1"></i> Répartition par Groupe
                </div>
                <div class="card-body">
                    <div class="placeholder-content">
                        <!-- Contenu qui sera affiché dynamiquement -->
                        <div class="text-center py-5">
                            <i class="fas fa-users fa-3x text-muted mb-3"></i>
                            <p class="text-muted">Sélectionnez des filtres pour afficher la répartition par groupe</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Progression -->
        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <div class="card-header">
                    <i class="fas fa-chart-line me-1"></i> Répartition par Intervalle
                </div>
                <div class="card-body">
                    <div class="placeholder-content">
                        <!-- Contenu qui sera affiché dynamiquement -->
                        <div class="text-center py-5">
                            <i class="fas fa-chart-line fa-3x text-muted mb-3"></i>
                            <p class="text-muted">Sélectionnez des filtres pour afficher la répartition par intervalle</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tableau des élèves -->
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div><i class="fas fa-table me-1"></i> Liste des Élèves</div>
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
                                    <th>N°</th>
                                    <th>Nom</th>
                                    <th>Prénom</th>
                                    <th>Classe</th>
                                    <th>Sexe</th>
                                    <th>Moyenne</th>
                                    <th>Rang</th>
                                    <th>Appréciation</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Les données des élèves seront chargées dynamiquement -->
                                <tr>
                                    <td colspan="9" class="text-center py-4">
                                        <i class="fas fa-info-circle me-2"></i> Sélectionnez des filtres pour afficher la liste des élèves
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