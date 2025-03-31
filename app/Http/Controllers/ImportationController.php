<?php

namespace App\Http\Controllers;

use App\Models\AnneeScolaire;
use App\Models\Niveau;
use App\Models\Classe;
use App\Models\Eleve;
use App\Models\Discipline;
use App\Models\NoteS1;
use App\Models\NoteS2;
use App\Models\MoyenneGeneraleS1;
use App\Models\MoyenneGeneraleS2;
use App\Models\ImportHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Carbon\Carbon;

class ImportationController extends Controller
{
    /**
     * Affiche le formulaire d'importation pour le semestre 1
     */
    public function indexS1()
    {
        $niveaux = Niveau::where('actif', true)->orderBy('libelle')->get();
        $anneeScolaireActive = AnneeScolaire::where('active', true)->first();
        
        if (!$anneeScolaireActive) {
            return redirect()->route('parametres.index')->with('error', 'Aucune année scolaire active. Veuillez en configurer une d\'abord.');
        }
        
        // Récupérer l'historique des importations pour le semestre 1
        $importHistory = ImportHistory::with(['niveau', 'classe'])
            ->where('semestre', 1)
            ->where('annee_scolaire_id', $anneeScolaireActive->id)
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('importation.semestre1', compact('niveaux', 'anneeScolaireActive', 'importHistory'));
    }
    
    /**
     * Affiche le formulaire d'importation pour le semestre 2
     */
    public function indexS2()
    {
        $niveaux = Niveau::where('actif', true)->orderBy('libelle')->get();
        $anneeScolaireActive = AnneeScolaire::where('active', true)->first();
        
        if (!$anneeScolaireActive) {
            return redirect()->route('parametres.index')->with('error', 'Aucune année scolaire active. Veuillez en configurer une d\'abord.');
        }
        
        // Récupérer l'historique des importations pour le semestre 2
        $importHistory = ImportHistory::with(['niveau', 'classe'])
            ->where('semestre', 2)
            ->where('annee_scolaire_id', $anneeScolaireActive->id)
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('importation.semestre2', compact('niveaux', 'anneeScolaireActive', 'importHistory'));
    }
    
    /**
     * Récupère les classes pour un niveau spécifique (AJAX)
     */
    public function getClasses($niveau_id)
    {
        $classes = Classe::where('niveau_id', $niveau_id)
                    ->where('actif', true)
                    ->orderBy('libelle')
                    ->get();
        
        return response()->json($classes);
    }
    
    /**
     * Détermine la décision du conseil en fonction de la moyenne
     * 
     * @param float|null $moyenne La moyenne de l'élève
     * @return string La décision correspondante
     */
    private function determinerDecisionConseil($moyenne) {
        if ($moyenne === null) {
            return '';
        }
        
        if ($moyenne >= 16) {
            return 'Travail excellent';
        } elseif ($moyenne >= 12) {
            return 'Satisfaisant doit continuer';
        } elseif ($moyenne >= 10) {
            return 'Peut Mieux Faire';
        } elseif ($moyenne >= 8) {
            return 'Insuffisant';
        } elseif ($moyenne >= 5) {
            return 'Risque de Redoubler';
        } else {
            return 'Risque l\'exclusion';
        }
    }

    /**
     * Détermine l'appréciation en fonction de la moyenne
     * 
     * @param float|null $moyenne La moyenne de l'élève
     * @return string L'appréciation correspondante
     */
    private function determinerAppreciation($moyenne) {
        if ($moyenne === null) {
            return '';
        }
        
        if ($moyenne >= 16) {
            return 'Félicitations';
        } elseif ($moyenne >= 14) {
            return 'Encouragements';
        } elseif ($moyenne >= 12) {
            return 'Tableau d\'honneur';
        } elseif ($moyenne >= 10) {
            return 'Passable';
        } elseif ($moyenne >= 8) {
            return 'Doit redoubler d\'effort';
        } elseif ($moyenne >= 5) {
            return 'Avertissement';
        } else {
            return 'Blâme';
        }
    }
    
    /**
     * Importe les données du fichier Excel pour le semestre 1
     */
    public function importerS1(Request $request)
    {
        $request->validate([
            'fichier' => 'required|file|mimes:xlsx,xls',
            'classe_id' => 'required|exists:classes,id',
        ]);
        
        $classe = Classe::findOrFail($request->classe_id);
        $niveau = Niveau::findOrFail($classe->niveau_id);
        $anneeScolaireActive = AnneeScolaire::where('active', true)->first();
        
        if (!$anneeScolaireActive) {
            return redirect()->back()->with('error', 'Aucune année scolaire active.');
        }
        
        try {
            // Stocker le fichier original
            $fichier = $request->file('fichier');
            $fichierOriginal = $fichier->getClientOriginalName();
            $fichierStocke = $fichier->store('imports/s1');
            
            // Charger le fichier Excel
            $spreadsheet = IOFactory::load($fichier->getPathname());
            
            // Créer un enregistrement d'importation
            $importHistory = new ImportHistory([
                'niveau_id' => $niveau->id,
                'classe_id' => $classe->id,
                'annee_scolaire_id' => $anneeScolaireActive->id,
                'fichier_original' => $fichierOriginal,
                'fichier_stocke' => $fichierStocke,
                'semestre' => 1,
                'statut' => 'en_cours'
            ]);
            $importHistory->save();

            // Commencez une transaction
            DB::beginTransaction();
            
            // Traiter l'onglet "Moyennes eleves"
            $worksheet = $spreadsheet->getSheet(0); // Premier onglet
            $data = $worksheet->toArray();
            
            // Vérifiez si le format est correct
            if (!isset($data[0][0]) || $data[0][0] != 'IEN') {
                $importHistory->update(['statut' => 'erreur_format']);
                return redirect()->back()->with('error', 'Format de fichier incorrect. L\'entête de la première colonne doit être "IEN".');
            }
            
            $elevesCount = 0;
            // Traiter les données des élèves
            for ($i = 1; $i < count($data); $i++) {
                if (empty($data[$i][0])) continue; // Ignorer les lignes vides
                
                // Vérifiez si l'élève existe déjà
                $eleve = Eleve::where('ien', $data[$i][0])
                            ->where('annee_scolaire_id', $anneeScolaireActive->id)
                            ->first();
                            
                if (!$eleve) {
                    // Créer un nouvel élève
                    $eleve = new Eleve();
                    $eleve->ien = $data[$i][0];
                    $eleve->prenom = $data[$i][1] ?? '';
                    $eleve->nom = $data[$i][2] ?? '';
                    
                    // Conversion du sexe: H (Homme) vers M (Masculin)
                    $sexe = $data[$i][3] ?? '';
                    if ($sexe === 'H') {
                        $sexe = 'M';
                    } elseif ($sexe === 'F') {
                        // F reste F, pas besoin de conversion
                        $sexe = 'F';
                    } else {
                        // Pour les autres valeurs, utiliser M par défaut
                        $sexe = 'M';
                    }
                    $eleve->sexe = $sexe;
                    
                    // Correction pour la date de naissance
                    $dateValue = $data[$i][4] ?? null;
                    if (!empty($dateValue)) {
                        if (is_numeric($dateValue)) {
                            $eleve->date_naissance = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($dateValue)->format('Y-m-d');
                        } else {
                            // Tentative de conversion de chaîne en date
                            try {
                                $eleve->date_naissance = Carbon::parse($dateValue)->format('Y-m-d');
                            } catch (\Exception $e) {
                                $eleve->date_naissance = null;
                            }
                        }
                    } else {
                        $eleve->date_naissance = null;
                    }
                    
                    $eleve->lieu_naissance = $data[$i][5] ?? '';
                    $eleve->classe_id = $classe->id;
                    $eleve->annee_scolaire_id = $anneeScolaireActive->id;
                    $eleve->save();
                    
                    $elevesCount++;
                }
                
                // Récupérer les valeurs des données
                $moyenne = isset($data[$i][9]) && is_numeric($data[$i][9]) ? $data[$i][9] : null;
                $rang = isset($data[$i][10]) && is_numeric($data[$i][10]) ? $data[$i][10] : null;
                $decision = isset($data[$i][11]) && !empty($data[$i][11]) ? $data[$i][11] : $this->determinerDecisionConseil($moyenne);
                $appreciation = isset($data[$i][12]) && !empty($data[$i][12]) ? $data[$i][12] : $this->determinerAppreciation($moyenne);
                $observation = isset($data[$i][13]) ? $data[$i][13] : '';

                // Créer ou mettre à jour la moyenne générale
                $moyenneGenerale = MoyenneGeneraleS1::updateOrCreate(
                    [
                        'eleve_id' => $eleve->id,
                        'annee_scolaire_id' => $anneeScolaireActive->id
                    ],
                    [
                        'retard' => $data[$i][6] ?? '',
                        'absence' => $data[$i][7] ?? '',
                        'conseil_discipline' => $data[$i][8] ?? '',
                        'moyenne' => $moyenne,
                        'rang' => $rang,
                        'decision' => $decision,
                        'appreciation' => $appreciation,
                        'observation' => $observation
                    ]
                );
            }
            
            // Traiter l'onglet "Données détaillées"
            $worksheet = $spreadsheet->getSheet(1); // Deuxième onglet
            $data = $worksheet->toArray();
            
            // Identifier les colonnes de disciplines
            $headers = $data[0];
            $disciplinesMap = [];
            $currentDiscipline = null;
            $disciplinesCount = 0;
            
            // Parcourir les en-têtes pour identifier les disciplines
            for ($j = 3; $j < count($headers); $j++) {
                if (!empty($headers[$j]) && $headers[$j] != 'Moy DD' && $headers[$j] != 'Comp D' && $headers[$j] != 'Moy D' && $headers[$j] != 'Rang D') {
                    // Nouvelle discipline principale ou sous-discipline
                    $disciplinesCount++;
                    
                    if (strpos($headers[$j], '[') !== false) {
                        // Sous-discipline
                        $parts = explode('[', $headers[$j]);
                        $disciplinePrincipale = trim($parts[0]);
                        $sousDiscipline = trim(str_replace(']', '', $parts[1]));
                        
                        // Vérifier si la discipline principale existe
                        $disciplineParent = Discipline::where('libelle', $disciplinePrincipale)
                                                   ->where('type', 'principale')
                                                   ->first();
                                                   
                        if (!$disciplineParent) {
                            $disciplineParent = new Discipline();
                            $disciplineParent->libelle = $disciplinePrincipale;
                            $disciplineParent->type = 'principale';
                            $disciplineParent->save();
                        }
                        
                        // Créer ou trouver la sous-discipline
                        $discipline = Discipline::where('libelle', $sousDiscipline)
                                              ->where('discipline_parent_id', $disciplineParent->id)
                                              ->first();
                                              
                        if (!$discipline) {
                            $discipline = new Discipline();
                            $discipline->libelle = $sousDiscipline;
                            $discipline->type = 'sous-discipline';
                            $discipline->discipline_parent_id = $disciplineParent->id;
                            $discipline->save();
                        }
                        
                        $currentDiscipline = $discipline->id;
                    } else {
                        // Discipline principale
                        $discipline = Discipline::where('libelle', $headers[$j])
                                              ->where('type', 'principale')
                                              ->first();
                                              
                        if (!$discipline) {
                            $discipline = new Discipline();
                            $discipline->libelle = $headers[$j];
                            $discipline->type = 'principale';
                            $discipline->save();
                        }
                        
                        $currentDiscipline = $discipline->id;
                    }
                }
                
                $columnType = '';
                if (!empty($headers[$j])) {
                    if ($headers[$j] == 'Moy DD') $columnType = 'moy_dd';
                    else if ($headers[$j] == 'Comp D') $columnType = 'comp_d';
                    else if ($headers[$j] == 'Moy D') $columnType = 'moy_d';
                    else if ($headers[$j] == 'Rang D') $columnType = 'rang_d';
                }
                
                if ($columnType && $currentDiscipline) {
                    $disciplinesMap[$j] = [
                        'discipline_id' => $currentDiscipline,
                        'type' => $columnType
                    ];
                }
            }
            
            // Traiter les notes des élèves
            for ($i = 1; $i < count($data); $i++) {
                if (empty($data[$i][0])) continue; // Ignorer les lignes vides
                
                $eleve = Eleve::where('ien', $data[$i][0])
                            ->where('annee_scolaire_id', $anneeScolaireActive->id)
                            ->first();
                            
                if (!$eleve) continue; // Élève non trouvé
                
                $notesParDiscipline = [];
                
                // Parcourir les colonnes pour trouver les notes
                foreach ($disciplinesMap as $colIndex => $mapInfo) {
                    $discipline_id = $mapInfo['discipline_id'];
                    $type = $mapInfo['type'];
                    $valeur = $data[$i][$colIndex] ?? null;
                    
                    if (!isset($notesParDiscipline[$discipline_id])) {
                        $notesParDiscipline[$discipline_id] = [
                            'moy_dd' => null,
                            'comp_d' => null,
                            'moy_d' => null,
                            'rang_d' => null
                        ];
                    }
                    
                    $notesParDiscipline[$discipline_id][$type] = $valeur;
                }
                
                // Enregistrer ou mettre à jour les notes pour chaque discipline
                foreach ($notesParDiscipline as $discipline_id => $notes) {
                    NoteS1::updateOrCreate(
                        [
                            'eleve_id' => $eleve->id,
                            'discipline_id' => $discipline_id,
                            'annee_scolaire_id' => $anneeScolaireActive->id
                        ],
                        [
                            'moy_dd' => $notes['moy_dd'],
                            'comp_d' => $notes['comp_d'],
                            'moy_d' => $notes['moy_d'],
                            'rang_d' => $notes['rang_d']
                        ]
                    );
                }
            }
            
            // Mettre à jour l'historique d'importation
            $importHistory->update([
                'nb_eleves' => $elevesCount,
                'nb_disciplines' => $disciplinesCount,
                'statut' => 'complet',
                'resume' => [
                    'nb_eleves' => $elevesCount,
                    'nb_disciplines' => $disciplinesCount,
                    'date_import' => now()->format('d/m/Y H:i:s')
                ]
            ]);
            
            DB::commit();
            return redirect()->route('importation.s1')->with('success', 'Données importées avec succès pour le semestre 1.');
            
        } catch (\Exception $e) {
            DB::rollBack();
            if (isset($importHistory)) {
                $importHistory->update(['statut' => 'erreur']);
            }
            Log::error('Erreur lors de l\'importation: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Erreur lors de l\'importation: ' . $e->getMessage());
        }
    }
    
    /**
     * Importe les données du fichier Excel pour le semestre 2
     */
    public function importerS2(Request $request)
    {
        $request->validate([
            'fichier' => 'required|file|mimes:xlsx,xls',
            'classe_id' => 'required|exists:classes,id',
        ]);
        
        $classe = Classe::findOrFail($request->classe_id);
        $niveau = Niveau::findOrFail($classe->niveau_id);
        $anneeScolaireActive = AnneeScolaire::where('active', true)->first();
        
        if (!$anneeScolaireActive) {
            return redirect()->back()->with('error', 'Aucune année scolaire active.');
        }
        
        try {
            // Stocker le fichier original
            $fichier = $request->file('fichier');
            $fichierOriginal = $fichier->getClientOriginalName();
            $fichierStocke = $fichier->store('imports/s2');
            
            // Créer un enregistrement d'importation
            $importHistory = new ImportHistory([
                'niveau_id' => $niveau->id,
                'classe_id' => $classe->id,
                'annee_scolaire_id' => $anneeScolaireActive->id,
                'fichier_original' => $fichierOriginal,
                'fichier_stocke' => $fichierStocke,
                'semestre' => 2,
                'statut' => 'en_cours'
            ]);
            $importHistory->save();
            
            // Commencez une transaction
            DB::beginTransaction();
            
            $spreadsheet = IOFactory::load($fichier->getPathname());
            
            // Traiter l'onglet "Moyennes eleves"
            $worksheet = $spreadsheet->getSheet(0); // Premier onglet
            $data = $worksheet->toArray();
            
            // Vérifiez si le format est correct
            if (!isset($data[0][0]) || $data[0][0] != 'IEN') {
                $importHistory->update(['statut' => 'erreur_format']);
                return redirect()->back()->with('error', 'Format de fichier incorrect. L\'entête de la première colonne doit être "IEN".');
            }
            
            $elevesCount = 0;
            // Traiter les données des élèves
            for ($i = 1; $i < count($data); $i++) {
                if (empty($data[$i][0])) continue; // Ignorer les lignes vides
                
                // Vérifiez si l'élève existe déjà
                $eleve = Eleve::where('ien', $data[$i][0])
                            ->where('annee_scolaire_id', $anneeScolaireActive->id)
                            ->first();
                            
                if (!$eleve) {
                    // Créer un nouvel élève
                    $eleve = new Eleve();
                    $eleve->ien = $data[$i][0];
                    $eleve->prenom = $data[$i][1] ?? '';
                    $eleve->nom = $data[$i][2] ?? '';
                    
                    // Conversion du sexe: H (Homme) vers M (Masculin)
                    $sexe = $data[$i][3] ?? '';
                    if ($sexe === 'H') {
                        $sexe = 'M';
                    } elseif ($sexe === 'F') {
                        // F reste F, pas besoin de conversion
                        $sexe = 'F';
                    } else {
                        // Pour les autres valeurs, utiliser M par défaut
                        $sexe = 'M';
                    }
                    $eleve->sexe = $sexe;
                    
                    // Correction pour la date de naissance
                    $dateValue = $data[$i][4] ?? null;
                    if (!empty($dateValue)) {
                        if (is_numeric($dateValue)) {
                            $eleve->date_naissance = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($dateValue)->format('Y-m-d');
                        } else {
                            // Tentative de conversion de chaîne en date
                            try {
                                $eleve->date_naissance = Carbon::parse($dateValue)->format('Y-m-d');
                            } catch (\Exception $e) {
                                $eleve->date_naissance = null;
                            }
                        }
                    } else {
                        $eleve->date_naissance = null;
                    }
                    
                    $eleve->lieu_naissance = $data[$i][5] ?? '';
                    $eleve->classe_id = $classe->id;
                    $eleve->annee_scolaire_id = $anneeScolaireActive->id;
                    $eleve->save();
                    
                    $elevesCount++;
                }
                
                // Récupérer les valeurs des données
                $moyenne = isset($data[$i][9]) && is_numeric($data[$i][9]) ? $data[$i][9] : null;
                $rang = isset($data[$i][10]) && is_numeric($data[$i][10]) ? $data[$i][10] : null;
                $decision = isset($data[$i][11]) && !empty($data[$i][11]) ? $data[$i][11] : $this->determinerDecisionConseil($moyenne);
                $appreciation = isset($data[$i][12]) && !empty($data[$i][12]) ? $data[$i][12] : $this->determinerAppreciation($moyenne);
                $observation = isset($data[$i][13]) ? $data[$i][13] : '';

                // Créer ou mettre à jour la moyenne générale
                $moyenneGenerale = MoyenneGeneraleS2::updateOrCreate(
                    [
                        'eleve_id' => $eleve->id,
                        'annee_scolaire_id' => $anneeScolaireActive->id
                    ],
                    [
                        'retard' => $data[$i][6] ?? '',
                        'absence' => $data[$i][7] ?? '',
                        'conseil_discipline' => $data[$i][8] ?? '',
                        'moyenne' => $moyenne,
                        'rang' => $rang,
                        'decision' => $decision,
                        'appreciation' => $appreciation,
                        'observation' => $observation
                    ]
                );
            }
            
            // Traiter l'onglet "Données détaillées"
            $worksheet = $spreadsheet->getSheet(1); // Deuxième onglet
            $data = $worksheet->toArray();
            
            // Identifier les colonnes de disciplines
            $headers = $data[0];
            $disciplinesMap = [];
            $currentDiscipline = null;
            $disciplinesCount = 0;
            
            // Parcourir les en-têtes pour identifier les disciplines
            for ($j = 3; $j < count($headers); $j++) {
                if (!empty($headers[$j]) && $headers[$j] != 'Moy DD' && $headers[$j] != 'Comp D' && $headers[$j] != 'Moy D' && $headers[$j] != 'Rang D') {
                    // Nouvelle discipline principale ou sous-discipline
                    $disciplinesCount++;
                    
                    if (strpos($headers[$j], '[') !== false) {
                        // Sous-discipline
                        $parts = explode('[', $headers[$j]);
                        $disciplinePrincipale = trim($parts[0]);
                        $sousDiscipline = trim(str_replace(']', '', $parts[1]));
                        
                        // Vérifier si la discipline principale existe
                        $disciplineParent = Discipline::where('libelle', $disciplinePrincipale)
                                                   ->where('type', 'principale')
                                                   ->first();
                                                   
                        if (!$disciplineParent) {
                            $disciplineParent = new Discipline();
                            $disciplineParent->libelle = $disciplinePrincipale;
                            $disciplineParent->type = 'principale';
                            $disciplineParent->save();
                        }
                        
                        // Créer ou trouver la sous-discipline
                        $discipline = Discipline::where('libelle', $sousDiscipline)
                                              ->where('discipline_parent_id', $disciplineParent->id)
                                              ->first();
                                              
                        if (!$discipline) {
                            $discipline = new Discipline();
                            $discipline->libelle = $sousDiscipline;
                            $discipline->type = 'sous-discipline';
                            $discipline->discipline_parent_id = $disciplineParent->id;
                            $discipline->save();
                        }
                        
                        $currentDiscipline = $discipline->id;
                    } else {
                        // Discipline principale
                        $discipline = Discipline::where('libelle', $headers[$j])
                                              ->where('type', 'principale')
                                              ->first();
                                              
                        if (!$discipline) {
                            $discipline = new Discipline();
                            $discipline->libelle = $headers[$j];
                            $discipline->type = 'principale';
                            $discipline->save();
                        }
                        
                        $currentDiscipline = $discipline->id;
                    }
                }
                
                $columnType = '';
                if (!empty($headers[$j])) {
                    if ($headers[$j] == 'Moy DD') $columnType = 'moy_dd';
                    else if ($headers[$j] == 'Comp D') $columnType = 'comp_d';
                    else if ($headers[$j] == 'Moy D') $columnType = 'moy_d';
                    else if ($headers[$j] == 'Rang D') $columnType = 'rang_d';
                }
                
                if ($columnType && $currentDiscipline) {
                    $disciplinesMap[$j] = [
                        'discipline_id' => $currentDiscipline,
                        'type' => $columnType
                    ];
                }
            }
            
            // Traiter les notes des élèves
            for ($i = 1; $i < count($data); $i++) {
                if (empty($data[$i][0])) continue; // Ignorer les lignes vides
                
                $eleve = Eleve::where('ien', $data[$i][0])
                            ->where('annee_scolaire_id', $anneeScolaireActive->id)
                            ->first();
                            
                if (!$eleve) continue; // Élève non trouvé
                
                $notesParDiscipline = [];
                
                // Parcourir les colonnes pour trouver les notes
                foreach ($disciplinesMap as $colIndex => $mapInfo) {
                    $discipline_id = $mapInfo['discipline_id'];
                    $type = $mapInfo['type'];
                    $valeur = $data[$i][$colIndex] ?? null;
                    
                    if (!isset($notesParDiscipline[$discipline_id])) {
                        $notesParDiscipline[$discipline_id] = [
                            'moy_dd' => null,
                            'comp_d' => null,
                            'moy_d' => null,
                            'rang_d' => null
                        ];
                    }
                    
                    $notesParDiscipline[$discipline_id][$type] = $valeur;
                }
                
                // Enregistrer ou mettre à jour les notes pour chaque discipline
                foreach ($notesParDiscipline as $discipline_id => $notes) {
                    NoteS2::updateOrCreate(
                        [
                            'eleve_id' => $eleve->id,
                            'discipline_id' => $discipline_id,
                            'annee_scolaire_id' => $anneeScolaireActive->id
                        ],
                        [
                            'moy_dd' => $notes['moy_dd'],
                            'comp_d' => $notes['comp_d'],
                            'moy_d' => $notes['moy_d'],
                            'rang_d' => $notes['rang_d']
                        ]
                    );
                }
            }
            
            // Mettre à jour l'historique d'importation
            $importHistory->update([
                'nb_eleves' => $elevesCount,
                'nb_disciplines' => $disciplinesCount,
                'statut' => 'complet',
                'resume' => [
                    'nb_eleves' => $elevesCount,
                    'nb_disciplines' => $disciplinesCount,
                    'date_import' => now()->format('d/m/Y H:i:s')
                ]
            ]);
            
            DB::commit();
            return redirect()->route('importation.s2')->with('success', 'Données importées avec succès pour le semestre 2.');
            
        } catch (\Exception $e) {
            DB::rollBack();
            if (isset($importHistory)) {
                $importHistory->update(['statut' => 'erreur']);
            }
            Log::error('Erreur lors de l\'importation: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Erreur lors de l\'importation: ' . $e->getMessage());
        }
    }
    
    /**
     * Affiche le détail d'une importation (prévisualisation)
     */
    public function showImport($id)
    {
        $import = ImportHistory::with(['niveau', 'classe', 'anneeScolaire'])->findOrFail($id);
        
        try {
            $filePath = Storage::path($import->fichier_stocke);
            $spreadsheet = IOFactory::load($filePath);
            
            // Récupérer l'onglet "Moyennes eleves"
            $moyennesSheet = $spreadsheet->getSheet(0)->toArray();
            $moyennesHeaders = $moyennesSheet[0];
            $moyennesData = array_slice($moyennesSheet, 1);
            
            // Récupérer l'onglet "Données détaillées"
            $detailsSheet = $spreadsheet->getSheet(1)->toArray();
            $detailsHeaders = $detailsSheet[0];
            $detailsData = array_slice($detailsSheet, 1);
            
            // Structurer les disciplines
            $disciplineColumns = [];
            $currentDiscipline = null;
            
            // Parcourir les en-têtes pour identifier les disciplines
            for ($j = 3; $j < count($detailsHeaders); $j++) {
                if (!empty($detailsHeaders[$j]) && $detailsHeaders[$j] != 'Moy DD' && $detailsHeaders[$j] != 'Comp D' && $detailsHeaders[$j] != 'Moy D' && $detailsHeaders[$j] != 'Rang D') {
                    $currentDiscipline = $detailsHeaders[$j];
                    $disciplineColumns[$currentDiscipline] = [];
                }
                
                if (!empty($detailsHeaders[$j]) && in_array($detailsHeaders[$j], ['Moy DD', 'Comp D', 'Moy D', 'Rang D']) && $currentDiscipline) {
                    $disciplineColumns[$currentDiscipline][$detailsHeaders[$j]] = $j;
                }
            }
            
            // Extraire seulement les données qui nous intéressent (Moy D pour chaque discipline)
            $simplifiedData = [];
            
            foreach ($detailsData as $row) {
                if (empty($row[0])) continue; // Ignorer les lignes vides
                
                $eleveDonnees = [
                    'IEN' => $row[0],
                    'Prenom' => $row[1],
                    'Nom' => $row[2]
                ];
                
                foreach ($disciplineColumns as $discipline => $columns) {
                    if (isset($columns['Moy D'])) {
                        $colIndex = $columns['Moy D'];
                        $eleveDonnees[$discipline] = $row[$colIndex] ?? '';
                    }
                }
                
                $simplifiedData[] = $eleveDonnees;
            }
            
            // Créer des en-têtes simplifiés
            $simplifiedHeaders = ['IEN', 'Prenom', 'Nom'];
            foreach ($disciplineColumns as $discipline => $columns) {
                $simplifiedHeaders[] = $discipline;
            }
            
            return view('importation.preview', compact('import', 'moyennesHeaders', 'moyennesData', 'detailsHeaders', 'detailsData', 'simplifiedHeaders', 'simplifiedData'));
        } catch (\Exception $e) {
            Log::error('Erreur lors de la prévisualisation: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Erreur lors de la prévisualisation: ' . $e->getMessage());
        }
    }
}