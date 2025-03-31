@extends('layouts.module')

@section('title', 'Gestion des données')

@section('sidebar')
<div class="nav-title">Administration</div>

<li class="nav-item">
    <a class="nav-link active" href="{{ route('data.management') }}">
        <span class="nav-icon"><i class="fas fa-database"></i></span>
        <span>Gestion des données</span>
    </a>
</li>

<li class="nav-item">
    <a class="nav-link" href="{{ route('parametres.index') }}">
        <span class="nav-icon"><i class="fas fa-cog"></i></span>
        <span>Paramètres</span>
    </a>
</li>

<div class="nav-title">Modules</div>

<li class="nav-item">
    <a class="nav-link" href="{{ route('semestre1.index') }}">
        <span class="nav-icon"><i class="fas fa-calendar-alt"></i></span>
        <span>Semestre 1</span>
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
    <i class="fas fa-database me-2"></i>Gestion des données
</h1>
<p class="page-subtitle">Gérez les données de votre application LCAMS et effectuez des opérations de maintenance.</p>

<!-- Statistiques des données -->
<div class="card mb-4">
    <div class="card-header header-primary">
        <h5 class="mb-0"><i class="fas fa-chart-pie me-2"></i>Statistiques des données</h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-3 mb-3">
                <div class="stats-card">
                    <div class="stats-icon stats-primary">
                        <i class="fas fa-user-graduate"></i>
                    </div>
                    <div class="stats-details">
                        <div class="stats-number">{{ $stats['eleves_count'] }}</div>
                        <div class="stats-text">Élèves</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="stats-card">
                    <div class="stats-icon stats-success">
                        <i class="fas fa-file-alt"></i>
                    </div>
                    <div class="stats-details">
                        <div class="stats-number">{{ $stats['notes_s1_count'] }}</div>
                        <div class="stats-text">Notes S1</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="stats-card">
                    <div class="stats-icon stats-warning">
                        <i class="fas fa-file-alt"></i>
                    </div>
                    <div class="stats-details">
                        <div class="stats-number">{{ $stats['notes_s2_count'] }}</div>
                        <div class="stats-text">Notes S2</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="stats-card">
                    <div class="stats-icon stats-danger">
                        <i class="fas fa-file-import"></i>
                    </div>
                    <div class="stats-details">
                        <div class="stats-number">{{ $stats['imports_count'] }}</div>
                        <div class="stats-text">Importations</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Opérations de suppression -->
<div class="row">
    <!-- Suppression par classe -->
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-header header-success">
                <h5 class="mb-0"><i class="fas fa-users me-2"></i>Supprimer les données d'une classe</h5>
            </div>
            <div class="card-body">
                <p>Supprime toutes les données associées à une classe spécifique.</p>
                
                <form action="{{ route('data.clear-class') }}" method="POST" id="clearClassForm">
                    @csrf
                    <div class="mb-3">
                        <label for="classe_id" class="form-label">Classe</label>
                        <select class="form-select" id="classe_id" name="classe_id" required>
                            <option value="">Sélectionnez une classe</option>
                            @foreach($classesWithData as $classe)
                                <option value="{{ $classe->id }}">{{ $classe->niveau }} - {{ $classe->libelle }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="confirm_class" name="confirm_class" value="yes" required>
                        <label class="form-check-label" for="confirm_class">
                            Je confirme vouloir supprimer les données de cette classe
                        </label>
                    </div>
                    
                    <button type="submit" class="btn btn-success" onclick="return confirm('Êtes-vous sûr de vouloir supprimer les données de cette classe ? Cette action est irréversible.')">
                        <i class="fas fa-trash-alt me-2"></i>Supprimer les données de la classe
                    </button>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Suppression par semestre -->
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-header header-warning">
                <h5 class="mb-0"><i class="fas fa-calendar-alt me-2"></i>Supprimer les données d'un semestre</h5>
            </div>
            <div class="card-body">
                <p>Supprime toutes les données associées à un semestre spécifique.</p>
                
                <form action="{{ route('data.clear-semester') }}" method="POST" id="clearSemesterForm">
                    @csrf
                    <div class="mb-3">
                        <label for="semestre" class="form-label">Semestre</label>
                        <select class="form-select" id="semestre" name="semestre" required>
                            <option value="">Sélectionnez un semestre</option>
                            <option value="1">Semestre 1</option>
                            <option value="2">Semestre 2</option>
                        </select>
                    </div>
                    
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="confirm_semestre" name="confirm_semestre" value="yes" required>
                        <label class="form-check-label" for="confirm_semestre">
                            Je confirme vouloir supprimer les données de ce semestre
                        </label>
                    </div>
                    
                    <button type="submit" class="btn btn-warning" onclick="return confirm('Êtes-vous sûr de vouloir supprimer les données de ce semestre ? Cette action est irréversible.')">
                        <i class="fas fa-trash-alt me-2"></i>Supprimer les données du semestre
                    </button>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Suppression complète -->
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-header header-danger">
                <h5 class="mb-0"><i class="fas fa-exclamation-triangle me-2"></i>Supprimer toutes les données</h5>
            </div>
            <div class="card-body">
                <p>Supprime toutes les données du système à l'exception des configurations de base. Cette action est irréversible.</p>
                
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-circle me-2"></i>Attention: Cette action supprimera toutes les données importées, les notes, les moyennes et les décisions finales. Seule la configuration du système sera conservée.
                </div>
                
                <form action="{{ route('data.clear-all') }}" method="POST" id="clearAllForm">
                    @csrf
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="confirm" name="confirm" value="yes" required>
                        <label class="form-check-label" for="confirm">
                            Je confirme vouloir supprimer toutes les données
                        </label>
                    </div>
                    
                    <button type="submit" class="btn btn-danger" onclick="return confirm('ATTENTION! Vous êtes sur le point de supprimer TOUTES les données du système. Cette action est IRRÉVERSIBLE. Êtes-vous absolument sûr de vouloir continuer?')">
                        <i class="fas fa-trash-alt me-2"></i>Supprimer toutes les données
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection