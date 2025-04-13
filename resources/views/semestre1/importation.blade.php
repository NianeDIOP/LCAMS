@extends('semestre1.layout')

@section('title', 'Importation de données')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Importation des données du semestre 1</h5>
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

                @if (session('info'))
                    <div class="alert alert-info" role="alert">
                        {{ session('info') }}
                    </div>
                @endif

                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="mb-0">Nouvelle importation</h6>
                            </div>
                            <div class="card-body">
                                <form method="POST" action="{{ route('semestre1.import-moyennes') }}" enctype="multipart/form-data">
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
                                        <small class="form-text text-muted">Le niveau scolaire auquel seront associées les données importées.</small>
                                    </div>

                                    <div class="mb-3">
                                        <label for="excel_file" class="form-label">Fichier Excel <span class="text-danger">*</span></label>
                                        <input type="file" id="excel_file" name="excel_file" class="form-control @error('excel_file') is-invalid @enderror" required>
                                        @error('excel_file')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="form-text text-muted">
                                            Le fichier doit contenir les onglets "Moyennes eleves" et "Données détaillées".<br>
                                            Format accepté : .xlsx, .xls (max 10 Mo)
                                        </small>
                                    </div>

                                    <div class="alert alert-info">
                                        <h6 class="alert-heading">Format du fichier Excel</h6>
                                        <p class="small mb-0">
                                            Le fichier doit contenir deux onglets :
                                            <ul class="mb-0">
                                                <li><strong>Moyennes eleves</strong> : Moyennes générales des élèves</li>
                                                <li><strong>Données détaillées</strong> : Notes par discipline</li>
                                            </ul>
                                            <button type="button" class="btn btn-link p-0" data-bs-toggle="modal" data-bs-target="#formatModal">
                                                Voir le format requis en détail
                                            </button>
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
                    <div class="col-md-6">
                        <div class="card h-100">
                            <div class="card-header">
                                <h6 class="mb-0">Instructions d'importation</h6>
                            </div>
                            <div class="card-body">
                                <div class="mb-4">
                                    <h6 class="fw-bold">Préparation du fichier Excel</h6>
                                    <p>Le fichier d'import doit suivre un format spécifique :</p>
                                    <ol>
                                        <li>Créer un fichier Excel avec deux onglets nommés <code>Moyennes eleves</code> et <code>Données détaillées</code></li>
                                        <li>Dans l'onglet <code>Moyennes eleves</code>, préparez les données selon le format demandé</li>
                                        <li>Dans l'onglet <code>Données détaillées</code>, préparez les notes par matière selon le format demandé</li>
                                    </ol>
                                </div>
                                
                                <div class="mb-4">
                                    <h6 class="fw-bold">Processus d'importation</h6>
                                    <ol>
                                        <li>Sélectionnez le niveau scolaire concerné</li>
                                        <li>Choisissez votre fichier Excel préparé</li>
                                        <li>Cliquez sur "Importer le fichier"</li>
                                        <li>Le système validera les données et les importera</li>
                                        <li>Un rapport d'importation vous sera présenté</li>
                                    </ol>
                                </div>

                                <div class="alert alert-warning mb-0">
                                    <i class="fas fa-exclamation-triangle"></i> <strong>Important :</strong> L'importation peut modifier ou ajouter des données. Assurez-vous d'avoir préparé correctement votre fichier avant l'importation.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0">Historique des importations récentes</h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Niveau</th>
                                        <th>Statut</th>
                                        <th>Élèves importés</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($importHistory as $import)
                                        @php
                                            $details = json_decode($import->details);
                                            $stats = $details->statistiques ?? null;
                                        @endphp
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
                                                @if($import->status === 'terminé' && $stats)
                                                    {{ $stats->total_students ?? 0 }}
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm" role="group">
                                                    <a href="{{ route('semestre1.show-import', $import->id) }}" class="btn btn-outline-primary">
                                                        <i class="fas fa-eye"></i> Détails
                                                    </a>
                                                    @if($import->status !== 'en_cours')
                                                        <form method="POST" action="{{ route('semestre1.delete-import', $import->id) }}" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette importation ?');">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-outline-danger">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </form>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center py-3">Aucune importation récente</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal pour le format du fichier -->
<div class="modal fade" id="formatModal" tabindex="-1" aria-labelledby="formatModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="formatModalLabel">Format du fichier Excel requis</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <h6>Onglet "Moyennes eleves"</h6>
                <p>Cet onglet doit contenir les informations de base des élèves et leur moyenne générale.</p>
                <p>Les données commencent à la ligne 12 (les lignes 1-11 sont réservées aux en-têtes) et doivent contenir les colonnes suivantes:</p>
                
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

                <hr>

                <h6 class="mt-4">Onglet "Données détaillées"</h6>
                <p>Cet onglet doit contenir les notes des élèves par discipline.</p>
                <p>Structure:</p>
                <ul>
                    <li>Ligne 7 : Noms des disciplines</li>
                    <li>Ligne 8 : Sous-colonnes (dont "Moy D" pour la moyenne disciplinaire)</li>
                    <li>À partir de la ligne 9 : Données des élèves</li>
                </ul>
                <p>Les colonnes A, B et C doivent contenir respectivement le matricule, nom et prénom de l'élève.</p>
                
                <div class="text-center mt-3">
                    <img src="{{ asset('images/format_exemple.png') }}" class="img-fluid border" alt="Exemple de format" style="max-width: 100%;">
                    <p class="text-muted mt-2">Exemple de format de l'onglet "Données détaillées"</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
            </div>
        </div>
    </div>
</div>
@endsection