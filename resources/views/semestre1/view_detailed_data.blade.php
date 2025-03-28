<!-- resources/views/semestre1/view_detailed_data.blade.php -->
@extends('layouts.module')

@section('title', 'Données détaillées - Semestre 1')

@section('module-icon')
<i class="fas fa-calendar-alt me-2"></i>
@endsection

@section('module-title', 'Semestre 1')

@section('page-title', 'Données détaillées')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('semestre1.index') }}">Semestre 1</a></li>
<li class="breadcrumb-item"><a href="{{ route('semestre1.base') }}">Base des moyennes</a></li>
<li class="breadcrumb-item"><a href="{{ route('semestre1.viewImportedFile', $file->id) }}">Visualisation</a></li>
<li class="breadcrumb-item active">Données détaillées</li>
@endsection

@section('sidebar-menu')
<li>
    <a href="{{ route('semestre1.index') }}" class="nav-link ps-3 py-2 {{ request()->routeIs('semestre1.index') ? 'active bg-light fw-bold' : '' }}">
        <i class="fas fa-home me-2"></i> Vue d'ensemble
    </a>
</li>
<li>
    <a href="{{ route('semestre1.dashboard') }}" class="nav-link ps-3 py-2 {{ request()->routeIs('semestre1.dashboard') ? 'active bg-light fw-bold' : '' }}">
        <i class="fas fa-tachometer-alt me-2"></i> Tableau de bord
    </a>
</li>
<li>
    <a href="{{ route('semestre1.analyse') }}" class="nav-link ps-3 py-2 {{ request()->routeIs('semestre1.analyse') ? 'active bg-light fw-bold' : '' }}">
        <i class="fas fa-chart-line me-2"></i> Analyse des disciplines
    </a>
</li>
<li>
    <a href="{{ route('semestre1.rapports') }}" class="nav-link ps-3 py-2 {{ request()->routeIs('semestre1.rapports') ? 'active bg-light fw-bold' : '' }}">
        <i class="fas fa-file-alt me-2"></i> Génération des rapports
    </a>
</li>
<li>
    <a href="{{ route('semestre1.base') }}" class="nav-link ps-3 py-2 {{ request()->routeIs('semestre1.base') ? 'active bg-light fw-bold' : '' }}">
        <i class="fas fa-database me-2"></i> Base des moyennes
    </a>
</li>
@endsection

@section('styles')
<style>
    .data-card {
        background-color: white;
        border-radius: 0.5rem;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        margin-bottom: 1.5rem;
    }
    
    .card-header {
        padding: 1rem 1.25rem;
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        font-weight: 600;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .data-table {
        font-size: 0.75rem;
        width: auto;
    }
    
    .data-table td {
        padding: 2px 4px;
        white-space: nowrap;
        border: 1px solid #dee2e6;
    }
    
    .data-table-wrapper {
        overflow-x: auto;
        max-height: 700px;
        overflow-y: auto;
    }

    /* Style pour mettre en évidence les en-têtes */
    .data-table tr:first-child td,
    .data-table tr:nth-child(2) td {
        font-weight: bold;
        background-color: #f8f9fa;
    }

    /* Colonnes alternées pour faciliter la lecture */
    .data-table td:nth-child(even) {
        background-color: rgba(0,0,0,0.02);
    }
</style>
@endsection

@section('module-content')
    <div class="data-card mb-4">
        <div class="card-header">
            <div>
                <i class="fas fa-info-circle me-2"></i> Informations sur le fichier
            </div>
            <div>
                <a href="{{ route('semestre1.viewImportedFile', $file->id) }}" class="btn btn-sm btn-primary me-2">
                    <i class="fas fa-table me-1"></i> Vue principale
                </a>
                <a href="{{ route('semestre1.base') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Retour
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Nom du fichier :</strong> {{ $file->nom_fichier }}</p>
                    <p><strong>Type :</strong> 
                        @if($file->type == 'statistiques')
                            <span class="badge bg-primary">Statistiques</span>
                        @elseif($file->type == 'moyennes')
                            <span class="badge bg-success">Moyennes</span>
                        @elseif($file->type == 'evaluations')
                            <span class="badge bg-info">Évaluations</span>
                        @else
                            <span class="badge bg-secondary">{{ $file->type }}</span>
                        @endif
                    </p>
                </div>
                <div class="col-md-6">
                    <p><strong>Onglet :</strong> {{ $sheetName }}</p>
                    <p><strong>Lignes :</strong> {{ count($rawData) }} | <strong>Colonnes :</strong> {{ count($modifiedColumns) }}</p>
                </div>
            </div>
        </div>
    </div>
    
    <div class="data-card">
        <div class="card-header">
            <i class="fas fa-table me-2"></i> Données détaillées
        </div>
        <div class="card-body p-0">
            <div class="data-table-wrapper">
                <table class="table table-sm table-bordered mb-0 data-table">
                    <tbody>
                        @foreach($rawData as $rowIndex => $row)
                            <tr>
                                @foreach($modifiedColumns as $col)
                                    <td>{{ $row[$col] ?? '' }}</td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection