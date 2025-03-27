@extends('layouts.module')

@section('title', 'Troisième onglet - Semestre 1')

@section('module-title')
    <i class="fas fa-calendar-alt me-2"></i> Semestre 1
@endsection

@section('page-title', 'Troisième onglet')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('semestre1.index') }}">Semestre 1</a></li>
    <li class="breadcrumb-item"><a href="{{ route('semestre1.base') }}">Base des moyennes</a></li>
    <li class="breadcrumb-item"><a href="{{ route('semestre1.viewImportedFile', $file->id) }}">Visualisation</a></li>
    <li class="breadcrumb-item active">Troisième onglet</li>
@endsection

@section('sidebar-menu')
    <li>
        <a href="{{ route('semestre1.index') }}" class="{{ request()->routeIs('semestre1.index') ? 'active' : '' }}">
            <span class="icon"><i class="fas fa-home"></i></span> Vue d'ensemble
        </a>
    </li>
    <li>
        <a href="{{ route('semestre1.dashboard') }}" class="{{ request()->routeIs('semestre1.dashboard') ? 'active' : '' }}">
            <span class="icon"><i class="fas fa-tachometer-alt"></i></span> Tableau de bord
        </a>
    </li>
    <li>
        <a href="{{ route('semestre1.analyse') }}" class="{{ request()->routeIs('semestre1.analyse') ? 'active' : '' }}">
            <span class="icon"><i class="fas fa-chart-line"></i></span> Analyse des disciplines
        </a>
    </li>
    <li>
        <a href="{{ route('semestre1.rapports') }}" class="{{ request()->routeIs('semestre1.rapports') ? 'active' : '' }}">
            <span class="icon"><i class="fas fa-file-alt"></i></span> Génération des rapports
        </a>
    </li>
    <li>
        <a href="{{ route('semestre1.base') }}" class="{{ request()->routeIs('semestre1.base') ? 'active' : '' }}">
            <span class="icon"><i class="fas fa-database"></i></span> Base des moyennes
        </a>
    </li>
@endsection

@section('styles')
<style>
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
</style>
@endsection

@section('content')
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <i class="fas fa-info-circle me-2"></i> Troisième onglet: {{ $sheetName }} - {{ $file->nom_fichier }}
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
    </div>
    
    <div class="card">
        <div class="card-header">
            <i class="fas fa-table me-2"></i> Données du troisième onglet ({{ count($rawData) }} lignes, {{ count($modifiedColumns) }} colonnes)
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