@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Importation des données Excel</h5>
                    <a href="{{ route('admin.import.history') }}" class="btn btn-sm btn-outline-secondary">
                        <i class="fas fa-history"></i> Historique des importations
                    </a>
                </div>

                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger" role="alert">
                            {{ session('error') }}
                        </div>
                    @endif

                    <div class="row">
                        <div class="col-md-7">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h6 class="mb-0">Nouvelle importation</h6>
                                </div>
                                <div class="card-body">
                                    <form method="POST" action="{{ route('admin.import.store') }}" enctype="multipart/form-data">
                                        @csrf

                                        <div class="mb-3">
                                            <label for="grade_level_id" class="form-label">Niveau scolaire <span class="text-danger">*</span></label>
                                            <select id="grade_level_id" name="grade_level_id" class="form-select @error('grade_level_id') is-invalid @enderror" required>
                                                <option value="">Sélectionner un niveau</option>
                                                @foreach($gradeLevels as $gradeLevel)
                                                    <option value="{{ $gradeLevel->id }}" {{ old('grade_level_id') == $gradeLevel->id ? 'selected' : '' }}>
                                                        {{ $gradeLevel->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('grade_level_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="excel_file" class="form-label">Fichier Excel <span class="text-danger">*</span></label>
                                            <input type="file" id="excel_file" name="excel_file" class="form-control @error('excel_file') is-invalid @enderror" required>
                                            @error('excel_file')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <small class="form-text text-muted">
                                                Le fichier doit contenir obligatoirement les onglets "Moyennes eleves" et "Données détaillées".
                                                <br>Format accepté : .xlsx, .xls (max 10 Mo)
                                            </small>
                                        </div>

                                        <div class="alert alert-info">
                                            <h6 class="alert-heading">Instructions d'importation</h6>
                                            <p class="small mb-0">
                                                <strong>Format attendu :</strong>
                                                <ul class="mb-0">
                                                    <li>Onglet "Moyennes eleves" : Contient les moyennes générales des élèves</li>
                                                    <li>Onglet "Données détaillées" : Contient les notes par discipline</li>
                                                </ul>
                                                <a href="#" class="alert-link" data-bs-toggle="modal" data-bs-target="#formatHelpModal">
                                                    Voir le format requis en détail
                                                </a>
                                            </p>
                                        </div>

                                        <div class="d-grid gap-2">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fas fa-file-import"></i> Importer le fichier
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-5">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0">Dernières importations</h6>
                                </div>
                                <div class="card-body p-0">
                                    @if($importHistory->isEmpty())
                                        <div class="p-3 text-center text-muted">
                                            Aucune importation récente
                                        </div>
                                    @else
                                        <div class="table-responsive">
                                            <table class="table table-hover mb-0">
                                                <thead>
                                                    <tr>
                                                        <th>Date</th>
                                                        <th>Niveau</th>
                                                        <th>Statut</th>
                                                        <th></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($importHistory as $import)
                                                        <tr>
                                                            <td>{{ $import->created_at->format('d/m/Y H:i') }}</td>
                                                            <td>{{ $import->gradeLevel->name ?? 'N/A' }}</td>
                                                            <td>
                                                                @if($import->status === 'terminé')
                                                                    <span class="badge bg-success">Terminé</span>
                                                                @elseif($import->status === 'en_cours')
                                                                    <span class="badge bg-warning">En cours</span>
                                                                @else
                                                                    <span class="badge bg-danger">Échoué</span>
                                                                @endif
                                                            </td>
                                                            <td>
                                                                <a href="{{ route('admin.import.show', $import->id) }}" class="btn btn-sm btn-outline-secondary">
                                                                    <i class="fas fa-eye"></i>
                                                                </a>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal d'aide sur le format -->
<div class="modal fade" id="formatHelpModal" tabindex="-1" aria-labelledby="formatHelpModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="formatHelpModalLabel">Format requis pour l'importation</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <h6>Onglet "Moyennes eleves"</h6>
                <p>
                    Cet onglet doit contenir les informations de base des élèves et leur moyenne générale.
                    Les données commencent à la ligne 12 et doivent contenir les colonnes suivantes :
                </p>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Colonne</th>
                            <th>Description</th>
                            <th>Obligatoire</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>A</td>
                            <td>Matricule</td>
                            <td>Non</td>
                        </tr>
                        <tr>
                            <td>B</td>
                            <td>Nom</td>
                            <td>Oui</td>
                        </tr>
                        <tr>
                            <td>C</td>
                            <td>Prénom</td>
                            <td>Oui</td>
                        </tr>
                        <tr>
                            <td>D</td>
                            <td>Classe</td>
                            <td>Oui</td>
                        </tr>
                        <tr>
                            <td>E</td>
                            <td>Moyenne</td>
                            <td>Oui</td>
                        </tr>
                        <tr>
                            <td>F</td>
                            <td>Rang</td>
                            <td>Non</td>
                        </tr>
                        <tr>
                            <td>G</td>
                            <td>Appréciation</td>
                            <td>Non</td>
                        </tr>
                        <tr>
                            <td>H</td>
                            <td>Sexe (M/F)</td>
                            <td>Non</td>
                        </tr>
                    </tbody>
                </table>

                <h6 class="mt-4">Onglet "Données détaillées"</h6>
                <p>
                    Cet onglet doit contenir les notes des élèves par discipline.
                    La structure est plus complexe :
                </p>
                <ul>
                    <li>Ligne 7 : Noms des disciplines</li>
                    <li>Ligne 8 : Sous-colonnes (dont "Moy D" pour la moyenne disciplinaire)</li>
                    <li>À partir de la ligne 9 : Données des élèves</li>
                </ul>
                <p>Les colonnes A, B et C doivent contenir respectivement le matricule, nom et prénom de l'élève.</p>
                <p>
                    <img src="{{ asset('images/format_exemple.png') }}" class="img-fluid" alt="Exemple de format">
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
            </div>
        </div>
    </div>
</div>
@endsection