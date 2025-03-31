@extends('layouts.module')

@section('title', 'Prévisualisation des données')

@section('sidebar')
@if($import->semestre == 1)
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
        <a class="nav-link" href="{{ route('semestre1.rapports') }}">
            <span class="nav-icon"><i class="fas fa-file-alt"></i></span>
            <span>Rapports</span>
        </a>
    </li>
    
    <li class="nav-item">
        <a class="nav-link active" href="{{ route('importation.s1') }}">
            <span class="nav-icon"><i class="fas fa-file-import"></i></span>
            <span>Importation</span>
        </a>
    </li>
@else
    <div class="nav-title">Semestre 2</div>
    
    <li class="nav-item">
        <a class="nav-link" href="{{ route('semestre2.index') }}">
            <span class="nav-icon"><i class="fas fa-chart-pie"></i></span>
            <span>Vue d'ensemble</span>
        </a>
    </li>
    
    <li class="nav-item">
        <a class="nav-link" href="{{ route('semestre2.analyse-moyennes') }}">
            <span class="nav-icon"><i class="fas fa-chart-line"></i></span>
            <span>Analyse Moyennes</span>
        </a>
    </li>
    
    <li class="nav-item">
        <a class="nav-link" href="{{ route('semestre2.analyse-disciplines') }}">
            <span class="nav-icon"><i class="fas fa-chart-bar"></i></span>
            <span>Analyse Disciplines</span>
        </a>
    </li>
    
    <li class="nav-item">
        <a class="nav-link" href="{{ route('semestre2.rapports') }}">
            <span class="nav-icon"><i class="fas fa-file-alt"></i></span>
            <span>Rapports</span>
        </a>
    </li>
    
    <li class="nav-item">
        <a class="nav-link active" href="{{ route('importation.s2') }}">
            <span class="nav-icon"><i class="fas fa-file-import"></i></span>
            <span>Importation</span>
        </a>
    </li>
@endif

<div class="nav-title">Autres Modules</div>

<li class="nav-item">
    <a class="nav-link" href="{{ route('parametres.index') }}">
        <span class="nav-icon"><i class="fas fa-cog"></i></span>
        <span>Paramètres</span>
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
        <li class="breadcrumb-item"><a href="{{ $import->semestre == 1 ? route('importation.s1') : route('importation.s2') }}">Importation Semestre {{ $import->semestre }}</a></li>
        <li class="breadcrumb-item active" aria-current="page">Prévisualisation</li>
    </ol>
</nav>

<h1 class="page-title">
    <i class="fas fa-eye me-2"></i>Prévisualisation des données importées
</h1>

<div class="card mb-4">
    <div class="card-header header-primary">
        <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Informations sur l'importation</h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-3">
                <p><strong>Semestre:</strong> {{ $import->semestre }}</p>
            </div>
            <div class="col-md-3">
                <p><strong>Niveau:</strong> {{ $import->niveau->libelle }}</p>
            </div>
            <div class="col-md-3">
                <p><strong>Classe:</strong> {{ $import->classe->libelle }}</p>
            </div>
            <div class="col-md-3">
                <p><strong>Année scolaire:</strong> {{ $import->anneeScolaire->libelle }}</p>
            </div>
        </div>
        <div class="row">
            <div class="col-md-3">
                <p><strong>Fichier:</strong> {{ $import->fichier_original }}</p>
            </div>
            <div class="col-md-3">
            <p><strong>Date d'importation:</strong> {{ $import->created_at->format('d/m/Y H:i') }}</p>
            </div>
            <div class="col-md-3">
                <p><strong>Nombre d'élèves:</strong> {{ $import->nb_eleves }}</p>
            </div>
            <div class="col-md-3">
                <p><strong>Nombre de disciplines:</strong> {{ $import->nb_disciplines }}</p>
            </div>
        </div>
    </div>
</div>

<!-- Navigation par onglets -->
<ul class="nav nav-tabs mb-4" id="dataTabs" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active" id="moyennes-tab" data-bs-toggle="tab" data-bs-target="#moyennes" type="button" role="tab" aria-controls="moyennes" aria-selected="true">
            <i class="fas fa-users me-2"></i>Moyennes élèves
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="details-tab" data-bs-toggle="tab" data-bs-target="#details" type="button" role="tab" aria-controls="details" aria-selected="false">
            <i class="fas fa-table me-2"></i>Données détaillées
        </button>
    </li>
</ul>

<!-- Contenu des onglets -->
<div class="tab-content" id="dataTabsContent">
    <!-- Onglet Moyennes élèves -->
    <div class="tab-pane fade show active" id="moyennes" role="tabpanel" aria-labelledby="moyennes-tab">
        <div class="card">
            <div class="card-header header-success">
                <h5 class="mb-0"><i class="fas fa-list me-2"></i>Onglet "Moyennes élèves"</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover datatable">
                        <thead>
                            <tr>
                                @foreach($moyennesHeaders as $header)
                                <th>{{ $header }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($moyennesData as $row)
                                @if(!empty($row[0])) <!-- Ignorer les lignes vides -->
                                <tr>
                                    @foreach($row as $cell)
                                    <td>{{ $cell }}</td>
                                    @endforeach
                                </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Onglet Données détaillées -->
<div class="tab-pane fade" id="details" role="tabpanel" aria-labelledby="details-tab">
    <div class="card">
        <div class="card-header header-warning">
            <h5 class="mb-0"><i class="fas fa-table me-2"></i>Onglet "Données détaillées" (Moyennes par discipline)</h5>
        </div>
        <div class="card-body">
            <ul class="nav nav-tabs mb-3" id="detailsTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="simplified-tab" data-bs-toggle="tab" data-bs-target="#simplified" type="button" role="tab">
                        Vue simplifiée (Moyennes par discipline)
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="complete-tab" data-bs-toggle="tab" data-bs-target="#complete" type="button" role="tab">
                        Données complètes
                    </button>
                </li>
            </ul>
            
            <div class="tab-content" id="detailsTabsContent">
                <!-- Vue simplifiée -->
                <div class="tab-pane fade show active" id="simplified" role="tabpanel">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover datatable-simplified">
                            <thead>
                                <tr>
                                    @foreach($simplifiedHeaders as $header)
                                    <th>{{ $header }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($simplifiedData as $row)
                                <tr>
                                    @foreach($simplifiedHeaders as $header)
                                    <td>{{ $row[$header] ?? '' }}</td>
                                    @endforeach
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <!-- Données complètes -->
                <div class="tab-pane fade" id="complete" role="tabpanel">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>Cet onglet contient toutes les données brutes. Faites défiler horizontalement pour voir toutes les colonnes.
                    </div>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover datatable-details">
                            <thead>
                                <tr>
                                    @foreach($detailsHeaders as $header)
                                    <th>{{ $header }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($detailsData as $row)
                                    @if(!empty($row[0])) <!-- Ignorer les lignes vides -->
                                    <tr>
                                        @foreach($row as $cell)
                                        <td>{{ $cell }}</td>
                                        @endforeach
                                    </tr>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>

<div class="text-end mt-4">
    <a href="{{ $import->semestre == 1 ? route('importation.s1') : route('importation.s2') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left me-2"></i>Retour
    </a>
    
    <a href="{{ route('importation.convert-preview', $import->id) }}" class="btn btn-warning text-white me-2">
        <i class="fas fa-database me-2"></i>Convertir et enregistrer en base de données
    </a>
 
    
    <a href="{{ $import->semestre == 1 ? route('semestre1.index') : route('semestre2.index') }}" class="btn btn-primary">
        <i class="fas fa-chart-pie me-2"></i>Voir le tableau de bord
    </a>
</div>
@endsection

@section('styles')
<style>
    .datatable-details {
        width: 100% !important;
    }
</style>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Initialiser DataTables avec options optimisées
        $('.datatable-simplified').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/fr-FR.json'
            },
            pageLength: 15,
            scrollX: true
        });
        // Pour les données détaillées qui peuvent avoir beaucoup de colonnes
        $('.datatable-details').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/fr-FR.json'
            },
            pageLength: 15,
            scrollX: true,
            responsive: false
        });
    });
</script>
@endsection