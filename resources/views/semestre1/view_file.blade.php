@extends('layouts.module')

@section('title', 'Visualisation du fichier - Semestre 1')

@section('module-title')
    <i class="fas fa-calendar-alt me-2"></i> Semestre 1
@endsection

@section('page-title', 'Visualisation des données')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('semestre1.index') }}">Semestre 1</a></li>
    <li class="breadcrumb-item"><a href="{{ route('semestre1.base') }}">Base des moyennes</a></li>
    <li class="breadcrumb-item active">Visualisation</li>
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
    /* Styles pour les options de filtrage */
    .filter-card {
        background-color: white;
        border-radius: 0.5rem;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        margin-bottom: 1.5rem;
    }
    
    .file-info-card {
        background-color: white;
        border-radius: 0.5rem;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        margin-bottom: 1.5rem;
    }
    
    .data-table-card {
        background-color: white;
        border-radius: 0.5rem;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    }
    
    .card-header {
        padding: 1rem 1.25rem;
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        font-weight: 600;
    }
    
    .file-icon {
        font-size: 2rem;
        color: #0062cc;
        margin-right: 1rem;
    }
    
    .file-details {
        display: flex;
        align-items: center;
    }
    
    .file-title {
        font-size: 1.1rem;
        font-weight: 600;
        margin-bottom: 0.25rem;
    }
    
    .file-meta {
        font-size: 0.85rem;
        color: #6c757d;
    }
    
    .data-table {
        font-size: 0.85rem;
    }
    
    .data-table th, .data-table td {
        padding: 0.5rem 0.75rem;
        white-space: nowrap;
    }
    
    .data-table-wrapper {
        overflow-x: auto;
    }
    
    .pagination-info {
        font-size: 0.85rem;
        color: #6c757d;
    }
    
    .badge {
        font-size: 0.75rem;
        padding: 0.25em 0.5em;
    }
    
    .filter-form {
        display: flex;
        flex-wrap: wrap;
        gap: 0.75rem;
        align-items: flex-end;
    }
    
    .filter-group {
        flex: 1;
        min-width: 150px;
    }
    
    .sort-icon {
        display: inline-block;
        width: 0.8rem;
        text-align: center;
    }
    
    .sortable {
        cursor: pointer;
    }
    
    .sortable:hover {
        background-color: rgba(0, 0, 0, 0.05);
    }
    
    .sortable.active {
        background-color: rgba(0, 0, 0, 0.03);
    }
    
    .stat-box {
        background-color: #f8f9fa;
        border-radius: 0.5rem;
        padding: 1rem;
        text-align: center;
    }
    
    .stat-value {
        font-size: 1.25rem;
        font-weight: 600;
        margin-bottom: 0.25rem;
    }
    
    .stat-label {
        font-size: 0.8rem;
        color: #6c757d;
    }
    
    .action-buttons {
        display: flex;
        gap: 0.5rem;
    }
</style>
@endsection

@section('content')
    <!-- Informations sur le fichier -->
    <div class="file-info-card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <i class="fas fa-info-circle me-2"></i> Informations sur le fichier
            </div>
            <!-- Dans la section des boutons d'action, ajoutez ce bouton -->
            <div class="action-buttons">
                <a href="{{ route('semestre1.viewDetailedData', $file->id) }}" class="btn btn-sm btn-info">
                    <i class="fas fa-table me-1"></i> Données détaillées
                </a>
                
                <a href="{{ route('semestre1.exportPDF', ['id' => $file->id] + request()->query()) }}" class="btn btn-sm btn-danger">
                    <i class="fas fa-file-pdf me-1"></i> Exporter en PDF
                </a>
                <a href="{{ route('semestre1.exportExcel', ['id' => $file->id] + request()->query()) }}" class="btn btn-sm btn-success">
                    <i class="fas fa-file-excel me-1"></i> Exporter en Excel
                </a>
                <a href="{{ route('semestre1.base') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Retour
                </a>
            </div>
        </div>
        <div class="card-body p-3">
            <div class="file-details">
                <div class="file-icon">
                    <i class="fas fa-file-excel"></i>
                </div>
                <div>
                    <h4 class="file-title">{{ $file->nom_fichier }}</h4>
                    <div class="file-meta">
                        <span class="me-3">
                            <i class="fas fa-calendar-alt me-1"></i> Importé le {{ \Carbon\Carbon::parse($file->created_at)->format('d/m/Y à H:i') }}
                        </span>
                        <span class="me-3">
                            <i class="fas fa-list me-1"></i> {{ $file->nombre_lignes }} lignes
                        </span>
                        <span>
                            <i class="fas fa-tag me-1"></i> 
                            @if($file->type == 'statistiques')
                                <span class="badge bg-primary">Statistiques</span>
                            @elseif($file->type == 'moyennes')
                                <span class="badge bg-success">Moyennes</span>
                            @elseif($file->type == 'evaluations')
                                <span class="badge bg-info">Évaluations</span>
                            @else
                                <span class="badge bg-secondary">{{ $file->type }}</span>
                            @endif
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Options de filtrage -->
    <div class="filter-card">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-filter me-2"></i> Filtrer et trier les données</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('semestre1.viewImportedFile', $file->id) }}" method="GET" class="filter-form">
                <div class="filter-group">
                    <label for="search" class="form-label">Recherche</label>
                    <input type="text" class="form-control" id="search" name="search" placeholder="Nom, prénom ou IEN" value="{{ $searchTerm ?? '' }}">
                </div>
                
                <div class="filter-group">
                    <label for="min_moy" class="form-label">Moyenne min</label>
                    <input type="number" class="form-control" id="min_moy" name="min_moy" step="0.01" placeholder="0" value="{{ $minMoy ?? '' }}">
                </div>
                
                <div class="filter-group">
                    <label for="max_moy" class="form-label">Moyenne max</label>
                    <input type="number" class="form-control" id="max_moy" name="max_moy" step="0.01" placeholder="20" value="{{ $maxMoy ?? '' }}">
                </div>
                
                <div class="filter-group">
                    <label for="sort" class="form-label">Trier par</label>
                    <select class="form-select" id="sort" name="sort">
                        <option value="moy" {{ ($sortField ?? '') == 'moy' ? 'selected' : '' }}>Moyenne</option>
                        <option value="rang" {{ ($sortField ?? '') == 'rang' ? 'selected' : '' }}>Rang</option>
                        <option value="nom" {{ ($sortField ?? '') == 'nom' ? 'selected' : '' }}>Nom</option>
                    </select>
                </div>
                
                <div class="filter-group">
                    <label for="direction" class="form-label">Ordre</label>
                    <select class="form-select" id="direction" name="direction">
                        <option value="desc" {{ ($sortDirection ?? '') == 'desc' ? 'selected' : '' }}>Décroissant</option>
                        <option value="asc" {{ ($sortDirection ?? '') == 'asc' ? 'selected' : '' }}>Croissant</option>
                    </select>
                </div>
                
                <div class="filter-group" style="flex-basis: 150px;">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search me-1"></i> Filtrer
                    </button>
                </div>
                
                <div class="filter-group" style="flex-basis: 150px;">
                    <a href="{{ route('semestre1.viewImportedFile', $file->id) }}" class="btn btn-outline-secondary w-100">
                        <i class="fas fa-redo me-1"></i> Réinitialiser
                    </a>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Statistiques -->
    @if(isset($stats) && $stats['count'] > 0)
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="stat-box">
                <div class="stat-value">{{ $stats['count'] }}</div>
                <div class="stat-label">Élèves</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-box">
                <div class="stat-value">{{ number_format($stats['moyenne'], 2) }}</div>
                <div class="stat-label">Moyenne générale</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-box">
                <div class="stat-value">{{ number_format($stats['min'], 2) }}</div>
                <div class="stat-label">Note minimale</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-box">
                <div class="stat-value">{{ number_format($stats['max'], 2) }}</div>
                <div class="stat-label">Note maximale</div>
            </div>
        </div>
    </div>
    @endif
    
    <!-- Données du fichier -->
    <div class="data-table-card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <i class="fas fa-table me-2"></i> 
                Données 
                @if(isset($searchTerm) || isset($minMoy) || isset($maxMoy))
                    <span class="badge bg-info">Filtrées</span>
                @endif
            </div>
            <div>
                <span class="text-muted small">{{ isset($data) ? count($data) : 0 }} enregistrements</span>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="data-table-wrapper">
                <table class="table table-hover table-sm mb-0 data-table">
                    <thead class="table-light">
                        <tr>
                            <th>Ligne</th>
                            @foreach($headers as $index => $header)
                                <th class="{{ in_array($index, [9, 10]) ? 'sortable ' . (($sortField == ($index == 9 ? 'moy' : ($index == 10 ? 'rang' : ''))) ? 'active' : '') : '' }}">
                                    {{ $header }}
                                    @if($index == 9 && ($sortField ?? '') == 'moy')
                                        <span class="sort-icon">
                                            @if(($sortDirection ?? '') == 'asc') ↑ @else ↓ @endif
                                        </span>
                                    @endif
                                    @if($index == 10 && ($sortField ?? '') == 'rang')
                                        <span class="sort-icon">
                                            @if(($sortDirection ?? '') == 'asc') ↑ @else ↓ @endif
                                        </span>
                                    @endif
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @if(isset($data) && count($data) > 0)
                            @foreach($data as $row)
                                <tr>
                                    <td><strong>{{ $row['row_number'] }}</strong></td>
                                    @foreach($row['data'] as $cell)
                                        <td>{{ $cell }}</td>
                                    @endforeach
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="{{ count($headers) + 1 }}" class="text-center py-3">
                                    Aucune donnée trouvée
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Gestion des clics sur les en-têtes triables
        const sortableHeaders = document.querySelectorAll('th.sortable');
        sortableHeaders.forEach(header => {
            header.addEventListener('click', function() {
                // Déterminer le champ de tri
                const headerIndex = Array.from(header.parentNode.children).indexOf(header);
                let sortField = 'moy';
                
                if (headerIndex === 11) {
                    sortField = 'rang';
                } else if (headerIndex === 10) {
                    sortField = 'moy';
                }
                
                // Déterminer la direction
                let direction = 'desc';
                if (header.classList.contains('active') && 
                    document.querySelector('.sort-icon').textContent.trim() === '↓') {
                    direction = 'asc';
                }
                
                // Rediriger vers la même page avec les paramètres de tri
                const currentUrl = new URL(window.location.href);
                currentUrl.searchParams.set('sort', sortField);
                currentUrl.searchParams.set('direction', direction);
                window.location.href = currentUrl.toString();
            });
        });
    });
</script>
@endsection