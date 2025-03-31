@extends('layouts.module')

@section('title', 'Importation Semestre 1')

@section('sidebar')
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

<div class="nav-title">Autres Modules</div>

<li class="nav-item">
    <a class="nav-link" href="{{ route('parametres.index') }}">
        <span class="nav-icon"><i class="fas fa-cog"></i></span>
        <span>Paramètres</span>
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
    <i class="fas fa-file-import me-2"></i>Importation des données - Semestre 1
</h1>
<p class="page-subtitle">Importez les fichiers Excel générés par PLANETE pour le premier semestre.</p>

<div class="row mb-4">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header header-primary">
                <i class="fas fa-upload me-2"></i>Importer un fichier
            </div>
            <div class="card-body">
                <form action="{{ route('importation.importer-s1') }}" method="POST" enctype="multipart/form-data" id="importForm">
                    @csrf
                    
                    <div class="mb-3">
                        <label for="niveau_id" class="form-label">Niveau <span class="text-danger">*</span></label>
                        <select class="form-select" id="niveau_id" name="niveau_id" required>
                            <option value="">Sélectionnez un niveau</option>
                            @foreach($niveaux as $niveau)
                                <option value="{{ $niveau->id }}">{{ $niveau->libelle }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="classe_id" class="form-label">Classe <span class="text-danger">*</span></label>
                        <select class="form-select" id="classe_id" name="classe_id" required disabled>
                            <option value="">Sélectionnez d'abord un niveau</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="fichier" class="form-label">Fichier Excel PLANETE <span class="text-danger">*</span></label>
                        <input type="file" class="form-control" id="fichier" name="fichier" accept=".xlsx,.xls" required>
                        <div class="form-text">Format attendu: fichier Excel avec deux onglets (Moyennes eleves, Données détaillées)</div>
                    </div>
                    
                    <div class="alert alert-info">
                        <strong>Année scolaire active:</strong> {{ $anneeScolaireActive->libelle }}
                    </div>
                    
                    <button type="submit" class="btn btn-primary" id="submitBtn">
                        <i class="fas fa-upload me-2"></i>Importer
                    </button>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header header-info">
                <i class="fas fa-info-circle me-2"></i>Instructions
            </div>
            <div class="card-body">
                <ol>
                    <li>Téléchargez le fichier Excel depuis la plateforme PLANETE</li>
                    <li>Assurez-vous que le fichier contient les deux onglets requis:
                        <ul>
                            <li><strong>Moyennes eleves:</strong> Liste des élèves avec leurs informations et moyennes générales</li>
                            <li><strong>Données détaillées:</strong> Notes détaillées par discipline</li>
                        </ul>
                    </li>
                    <li>Sélectionnez le niveau et la classe concernés</li>
                    <li>Sélectionnez le fichier à importer</li>
                    <li>Cliquez sur le bouton "Importer"</li>
                </ol>
                
                <div class="alert alert-warning">
                    <strong>Important:</strong> L'importation peut prendre quelques instants selon la taille du fichier. Ne fermez pas la page pendant le processus.
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Historique des importations -->
<div class="card">
    <div class="card-header header-success">
        <i class="fas fa-history me-2"></i>Historique des importations - Semestre 1
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped datatable">
                <thead>
                    <tr>
                        <th>Niveau</th>
                        <th>Classe</th>
                        <th>Fichier</th>
                        <th>Date</th>
                        <th>Nb élèves</th>
                        <th>Nb disciplines</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($importHistory as $import)
                    <tr>
                        <td>{{ $import->niveau->libelle }}</td>
                        <td>{{ $import->classe->libelle }}</td>
                        <td>{{ $import->fichier_original }}</td>
                        <td>{{ $import->created_at->format('d/m/Y H:i') }}</td>
                        <td>{{ $import->nb_eleves }}</td>
                        <td>{{ $import->nb_disciplines }}</td>
                        <td>
                            @if($import->statut == 'complet')
                                <span class="badge bg-success">Complet</span>
                            @elseif($import->statut == 'en_cours')
                                <span class="badge bg-warning">En cours</span>
                            @elseif($import->statut == 'erreur')
                                <span class="badge bg-danger">Erreur</span>
                            @elseif($import->statut == 'erreur_format')
                                <span class="badge bg-danger">Erreur de format</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('importation.show', $import->id) }}" class="btn btn-sm btn-info text-white">
                                <i class="fas fa-eye"></i> Voir
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center">Aucune importation enregistrée</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Loader pour l'importation -->
<div class="progress d-none" id="progressContainer">
    <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%"></div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
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
        
        // Afficher la barre de progression lors de la soumission
        $('#importForm').submit(function() {
            $('#submitBtn').prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Importation en cours...');
            $('#progressContainer').removeClass('d-none');
        });
        
        // Initialiser DataTable
        $('.datatable').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/fr-FR.json'
            },
            order: [[3, 'desc']], // Trier par date décroissante
            pageLength: 10
        });
    });
</script>
@endsection