@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Détails de l'importation</h5>
                    <div>
                        <a href="{{ route('admin.import.index') }}" class="btn btn-sm btn-outline-secondary me-2">
                            <i class="fas fa-arrow-left"></i> Retour
                        </a>
                        <a href="{{ route('admin.import.history') }}" class="btn btn-sm btn-outline-secondary">
                            <i class="fas fa-history"></i> Historique
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6>Informations générales</h6>
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
                        <div class="col-md-6">
                            @if($import->status === 'terminé' && isset($details->statistiques))
                                <h6>Récapitulatif de l'importation</h6>
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
                                    <tr>
                                        <th>Nombre de disciplines :</th>
                                        <td class="text-right">{{ $details->statistiques->total_subjects }}</td>
                                    </tr>
                                    <tr>
                                        <th>Nombre total de notes :</th>
                                        <td class="text-right">{{ $details->statistiques->total_marks }}</td>
                                    </tr>
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
                            @elseif($import->status === 'échoué' && isset($details->erreur))
                                <div class="alert alert-danger">
                                    <h6>Erreur lors de l'importation</h6>
                                    <p class="mb-0">{{ $details->erreur }}</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    @if($import->status === 'terminé' && isset($details->statistiques->subjects) && count($details->statistiques->subjects) > 0)
                        <hr>
                        <h6>Détail par discipline</h6>
                        <div class="table-responsive">
                            <table class="table table-sm table-hover">
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
                    @endif

                    @if($import->status === 'terminé')
                        <div class="mt-4 d-flex justify-content-between">
                            <form method="POST" action="{{ route('admin.import.destroy', $import->id) }}" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette importation ? Cette action est irréversible.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger">
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
@endsection