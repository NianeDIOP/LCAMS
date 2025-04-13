@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-11">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Détails de l'importation</h5>
                    <div>
                        <a href="{{ route('semestre1.importation') }}" class="btn btn-sm btn-outline-secondary me-2">
                            <i class="fas fa-arrow-left"></i> Retour aux importations
                        </a>
                        <a href="{{ route('semestre1.index') }}" class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-home"></i> Semestre 1
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <!-- Informations générales de l'importation -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card h-100">
                                <div class="card-header">
                                    <h6 class="mb-0">Informations générales</h6>
                                </div>
                                <div class="card-body">
                                    <table class="table table-sm">
                                        <tr>
                                            <th style="width: 40%">Date d'importation :</th>
                                            <td>{{ $import->created_at->format('d/m/Y à H:i') }}</td>
                                        </tr>
                                        <tr>
                                            <th>Niveau scolaire :</th>
                                            <td>{{ $import->gradeLevel->name ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Utilisateur :</th>
                                            <td>{{ $import->user->name ?? 'Système' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Statut :</th>
                                            <td>
                                                @if($import->status === 'terminé')
                                                    <span class="badge bg-success">Terminé</span>
                                                @elseif($import->status === 'en_cours')
                                                    <span class="badge bg-warning">En cours</span>
                                                @else
                                                    <span class="badge bg-danger">Échoué</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Durée :</th>
                                            <td>
                                                @if(isset($details->début) && isset($details->fin))
                                                    @php
                                                        $debut = \Carbon\Carbon::parse($details->début);
                                                        $fin = \Carbon\Carbon::parse($details->fin);
                                                        $duree = $debut->diffInSeconds($fin);
                                                        $minutes = floor($duree / 60);
                                                        $seconds = $duree % 60;
                                                    @endphp
                                                    {{ $minutes }}m {{ $seconds }}s
                                                @else
                                                    N/A
                                                @endif
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card h-100">
                                <div class="card-header">
                                    <h6 class="mb-0">Récapitulatif</h6>
                                </div>
                                <div class="card-body">
                                    @if($import->status === 'terminé' && isset($details->statistiques))
                                        <table class="table table-sm">
                                            <tr>
                                                <th style="width: 60%">Nombre total d'élèves :</th>
                                                <td class="text-right"><strong>{{ $details->statistiques->total_students }}</strong></td>
                                            </tr>
                                            <tr>
                                                <th>- Nouveaux élèves :</th>
                                                <td class="text-right">{{ $details->statistiques->new_students }}</td>
                                            </tr>
                                            <tr>
                                                <th>- Élèves mis à jour :</th>
                                                <td class="text-right">{{ $details->statistiques->updated_students }}</td>
                                            </tr>
                                            @if(isset($details->statistiques->total_subjects))
                                                <tr>
                                                    <th>Nombre de disciplines :</th>
                                                    <td class="text-right">{{ $details->statistiques->total_subjects }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Nombre total de notes :</th>
                                                    <td class="text-right">{{ $details->statistiques->total_marks }}</td>
                                                </tr>
                                            @endif
                                            <tr>
                                                <th>Nombre d'erreurs :</th>
                                                <td class="text-right">
                                                    @if($details->statistiques->errors > 0)
                                                        <span class="text-danger">{{ $details->statistiques->errors }}</span>
                                                    @else
                                                        0
                                                    @endif
                                                </td>
                                            </tr>
                                        </table>

                                        <!-- Boutons d'actions -->
                                        <div class="mt-3">
                                            <a href="#donnees-importees" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-table"></i> Voir les données importées
                                            </a>
                                        </div>
                                    @elseif($import->status === 'échoué' && isset($details->erreur))
                                        <div class="alert alert-danger">
                                            <h6>Erreur lors de l'importation</h6>
                                            <p class="mb-0">{{ $details->erreur }}</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Détail par discipline (si disponible) -->
                    @if($import->status === 'terminé' && isset($details->statistiques->subjects) && count($details->statistiques->subjects) > 0)
                        <div class="card mb-4">
                            <div class="card-header">
                                <h6 class="mb-0">Détail par discipline</h6>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-sm table-hover mb-0">
                                        <thead>
                                            <tr>
                                                <th>Discipline</th>
                                                <th class="text-center">Notes importées</th>
                                                <th class="text-center">Notes mises à jour</th>
                                                <th class="text-center">Erreurs</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($details->statistiques->subjects as $subject)
                                                <tr>
                                                    <td>{{ $subject->name }}</td>
                                                    <td class="text-center">{{ $subject->imported }}</td>
                                                    <td class="text-center">{{ $subject->updated }}</td>
                                                    <td class="text-center">
                                                        @if($subject->errors > 0)
                                                            <span class="text-danger">{{ $subject->errors }}</span>
                                                        @else
                                                            0
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Tableau des données importées -->
                    @if($import->status === 'terminé')
                        <div class="card" id="donnees-importees">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h6 class="mb-0">Données importées</h6>
                                <div>
                                    <div class="input-group input-group-sm">
                                        <input type="text" id="searchInput" class="form-control" placeholder="Rechercher...">
                                        <button class="btn btn-outline-secondary" type="button" id="filterButton">
                                            <i class="fas fa-filter"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body p-0">
                                @if($studentsData && count($studentsData) > 0)
                                    <div class="table-responsive">
                                        <table class="table table-hover table-striped" id="importedDataTable">
                                            <thead>
                                                <tr>
                                                    <th>Matricule</th>
                                                    <th>Nom</th>
                                                    <th>Prénom</th>
                                                    <th>Classe</th>
                                                    <th>Sexe</th>
                                                    <th class="text-center">Moyenne</th>
                                                    <th class="text-center">Rang</th>
                                                    <th>Appréciation</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($studentsData as $student)
                                                    <tr>
                                                        <td>{{ $student->matricule }}</td>
                                                        <td>{{ $student->nom }}</td>
                                                        <td>{{ $student->prenom }}</td>
                                                        <td>{{ $student->classroom->name ?? 'N/A' }}</td>
                                                        <td>{{ $student->sexe ?? '-' }}</td>
                                                        <td class="text-center">{{ $student->semester1Average->moyenne ?? '-' }}</td>
                                                        <td class="text-center">{{ $student->semester1Average->rang ?? '-' }}</td>
                                                        <td>{{ Str::limit($student->semester1Average->appreciation ?? '-', 30) }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="p-3">
                                        {{ $studentsData->links() }}
                                    </div>
                                @else
                                    <div class="alert alert-info m-3">
                                        <i class="fas fa-info-circle"></i> Aucune donnée disponible pour cette importation.
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif

                    <!-- Boutons d'action -->
                    <div class="mt-4">
                        <div class="d-flex justify-content-between">
                            <div>
                                <a href="{{ route('semestre1.importation') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Retour
                                </a>
                            </div>
                            @if($import->status === 'terminé')
                                <div>
                                    <form method="POST" action="{{ route('semestre1.delete-import', $import->id) }}" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette importation ? Cette action est irréversible.');" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger">
                                            <i class="fas fa-trash"></i> Supprimer cette importation
                                        </button>
                                    </form>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // Recherche dans le tableau
    $("#searchInput").on("keyup", function() {
        var value = $(this).val().toLowerCase();
        $("#importedDataTable tbody tr").filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
        });
    });
});
</script>
@endpush
@endsection