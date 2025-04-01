<?php

namespace App\Http\Controllers;

use App\Models\AnneeScolaire;
use App\Models\Niveau;
use App\Models\Classe;
use App\Models\Eleve;
use App\Models\Discipline;
use App\Models\NoteS1;
use App\Models\MoyenneGeneraleS1;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf ;
use App\Models\DonneeDetaillee;


class Semestre1Controller extends Controller
{
    /**
     * Affiche la page d'accueil du semestre 1
     */
    public function index()
    {
        $anneeScolaireActive = AnneeScolaire::where('active', true)->first();
        if (!$anneeScolaireActive) {
            return redirect()->route('parametres.index')->with('error', 'Aucune année scolaire active. Veuillez en configurer une d\'abord.');
        }
        
        // Obtenir les statistiques générales
        $totalEleves = Eleve::where('annee_scolaire_id', $anneeScolaireActive->id)->count();
        $totalClasses = Classe::whereHas('eleves', function($query) use ($anneeScolaireActive) {
            $query->where('annee_scolaire_id', $anneeScolaireActive->id);
        })->count();
        
        // Distribution des moyennes par tranches
        $distribution = [
            'moins_5' => MoyenneGeneraleS1::where('annee_scolaire_id', $anneeScolaireActive->id)
                ->where('moyenne', '<', 5)->count(),
            '5_10' => MoyenneGeneraleS1::where('annee_scolaire_id', $anneeScolaireActive->id)
                ->whereBetween('moyenne', [5, 9.99])->count(),
            '10_12' => MoyenneGeneraleS1::where('annee_scolaire_id', $anneeScolaireActive->id)
                ->whereBetween('moyenne', [10, 11.99])->count(),
            '12_14' => MoyenneGeneraleS1::where('annee_scolaire_id', $anneeScolaireActive->id)
                ->whereBetween('moyenne', [12, 13.99])->count(),
            '14_16' => MoyenneGeneraleS1::where('annee_scolaire_id', $anneeScolaireActive->id)
                ->whereBetween('moyenne', [14, 15.99])->count(),
            '16_plus' => MoyenneGeneraleS1::where('annee_scolaire_id', $anneeScolaireActive->id)
                ->where('moyenne', '>=', 16)->count(),
        ];
        
        // Moyennes par classe
        $moyennesParClasse = DB::table('moyennes_generales_s1')
            ->join('eleves', 'moyennes_generales_s1.eleve_id', '=', 'eleves.id')
            ->join('classes', 'eleves.classe_id', '=', 'classes.id')
            ->select('classes.libelle as classe', DB::raw('AVG(moyennes_generales_s1.moyenne) as moyenne'))
            ->where('moyennes_generales_s1.annee_scolaire_id', $anneeScolaireActive->id)
            ->groupBy('classes.id', 'classes.libelle')
            ->get();
            
        // Répartition par sexe
        $repartitionSexe = DB::table('eleves')
            ->select('sexe', DB::raw('count(*) as total'))
            ->where('annee_scolaire_id', $anneeScolaireActive->id)
            ->groupBy('sexe')
            ->get();
            
        return view('semestre1.index', compact(
            'anneeScolaireActive', 
            'totalEleves', 
            'totalClasses', 
            'distribution', 
            'moyennesParClasse', 
            'repartitionSexe'
        ));
    }
    
    /**
     * Affiche l'analyse des moyennes
     */
    public function analyseMoyennes()
    {
        $anneeScolaireActive = AnneeScolaire::where('active', true)->first();
        $niveaux = Niveau::where('actif', true)->orderBy('libelle')->get();
        
        return view('semestre1.analyse_moyennes', compact('anneeScolaireActive', 'niveaux'));
    }
    
    /**
     * Récupère les données des moyennes pour une classe spécifique
     */
    public function getMoyennesClasse($classe_id)
    {
        $anneeScolaireActive = AnneeScolaire::where('active', true)->first();
        $classe = Classe::findOrFail($classe_id);
        
        $eleves = Eleve::with(['moyenneGeneraleS1'])
            ->where('classe_id', $classe_id)
            ->where('annee_scolaire_id', $anneeScolaireActive->id)
            ->get();
            
        return response()->json([
            'classe' => $classe,
            'eleves' => $eleves
        ]);
    }
    
    /**
     * Affiche l'analyse des disciplines
     */
    public function analyseDisciplines()
    {
        $anneeScolaireActive = AnneeScolaire::where('active', true)->first();
        $niveaux = Niveau::where('actif', true)->orderBy('libelle')->get();
        $disciplines = Discipline::where('type', 'principale')->orderBy('libelle')->get();
        
        return view('semestre1.analyse_disciplines', compact('anneeScolaireActive', 'niveaux', 'disciplines'));
    }
    
    /**
     * Récupère les données des disciplines pour une classe spécifique
     */
    public function getDisciplinesClasse(Request $request)
    {
        $anneeScolaireActive = AnneeScolaire::where('active', true)->first();
        $classe_id = $request->classe_id;
        $discipline_id = $request->discipline_id;
        
        $discipline = Discipline::findOrFail($discipline_id);
        $classe = Classe::findOrFail($classe_id);
        
        // Récupérer les moyennes par discipline
        $notes = DB::table('notes_s1')
            ->join('eleves', 'notes_s1.eleve_id', '=', 'eleves.id')
            ->join('disciplines', 'notes_s1.discipline_id', '=', 'disciplines.id')
            ->select(
                'eleves.id',
                'eleves.prenom',
                'eleves.nom',
                'eleves.sexe',
                'notes_s1.moy_dd',
                'notes_s1.comp_d',
                'notes_s1.moy_d',
                'notes_s1.rang_d'
            )
            ->where('eleves.classe_id', $classe_id)
            ->where('notes_s1.discipline_id', $discipline_id)
            ->where('notes_s1.annee_scolaire_id', $anneeScolaireActive->id)
            ->orderBy('eleves.nom')
            ->orderBy('eleves.prenom')
            ->get();
        
        // Statistiques
        $statistics = DB::table('notes_s1')
            ->join('eleves', 'notes_s1.eleve_id', '=', 'eleves.id')
            ->select(
                DB::raw('AVG(notes_s1.moy_d) as moyenne'),
                DB::raw('MIN(notes_s1.moy_d) as min'),
                DB::raw('MAX(notes_s1.moy_d) as max'),
                DB::raw('COUNT(CASE WHEN notes_s1.moy_d >= 10 THEN 1 END) as success_count'),
                DB::raw('COUNT(*) as total_count')
            )
            ->where('eleves.classe_id', $classe_id)
            ->where('notes_s1.discipline_id', $discipline_id)
            ->where('notes_s1.annee_scolaire_id', $anneeScolaireActive->id)
            ->first();
            
        // Statistiques par sexe
        $statisticsBySex = DB::table('notes_s1')
            ->join('eleves', 'notes_s1.eleve_id', '=', 'eleves.id')
            ->select(
                'eleves.sexe',
                DB::raw('AVG(notes_s1.moy_d) as moyenne'),
                DB::raw('COUNT(CASE WHEN notes_s1.moy_d >= 10 THEN 1 END) as success_count'),
                DB::raw('COUNT(*) as total_count')
            )
            ->where('eleves.classe_id', $classe_id)
            ->where('notes_s1.discipline_id', $discipline_id)
            ->where('notes_s1.annee_scolaire_id', $anneeScolaireActive->id)
            ->groupBy('eleves.sexe')
            ->get();
            
        return response()->json([
            'classe' => $classe,
            'discipline' => $discipline,
            'notes' => $notes,
            'statistics' => $statistics,
            'statisticsBySex' => $statisticsBySex
        ]);
    }
    
    /**
     * Affiche la page des rapports
     */
    public function rapports()
    {
        $anneeScolaireActive = AnneeScolaire::where('active', true)->first();
        $niveaux = Niveau::where('actif', true)->orderBy('libelle')->get();
        
        return view('semestre1.rapports', compact('anneeScolaireActive', 'niveaux'));
    }
    
    /**
     * Génère un rapport PDF pour une classe spécifique
     */
    public function genererRapportClasse($classe_id)
    {
        // Cette fonction sera implémentée plus tard
        return response()->json(['message' => 'Fonctionnalité en cours de développement']);
    }

    /**
     * Affiche la liste des élèves avec leurs informations pour le semestre 1
     */
    public function eleves(Request $request)
    {
        $anneeScolaireActive = AnneeScolaire::where('active', true)->first();
        if (!$anneeScolaireActive) {
            return redirect()->route('parametres.index')->with('error', 'Aucune année scolaire active. Veuillez en configurer une d\'abord.');
        }
        
        $niveaux = Niveau::where('actif', true)->orderBy('libelle')->get();
        $classes = collect();
        
        // Récupérer les paramètres de filtrage
        $niveau_id = $request->query('niveau_id');
        $classe_id = $request->query('classe_id');
        $sexe = $request->query('sexe');
        $moyenne_min = $request->query('moyenne_min');
        $moyenne_max = $request->query('moyenne_max');
        $admis_only = $request->query('admis_only');
        $sort_by = $request->query('sort_by', 'nom'); // Par défaut, trier par nom
        
        if ($niveau_id) {
            $classes = Classe::where('niveau_id', $niveau_id)
                        ->where('actif', true)
                        ->orderBy('libelle')
                        ->get();
        }
        
        // Initialiser la requête des élèves avec les relations nécessaires
        $elevesQuery = Eleve::with(['moyenneGeneraleS1', 'classe.niveau'])
                        ->where('eleves.annee_scolaire_id', $anneeScolaireActive->id);
        
        // Appliquer les filtres
        if ($classe_id) {
            $elevesQuery->where('eleves.classe_id', $classe_id);
        } elseif ($niveau_id) {
            $elevesQuery->whereHas('classe', function($query) use ($niveau_id) {
                $query->where('niveau_id', $niveau_id);
            });
        }
        
        if ($sexe) {
            $elevesQuery->where('eleves.sexe', $sexe);
        }
        
        // Filtrer par moyenne
        if ($moyenne_min !== null || $moyenne_max !== null || $admis_only) {
            $elevesQuery->whereHas('moyenneGeneraleS1', function($query) use ($moyenne_min, $moyenne_max, $admis_only, $anneeScolaireActive) {
                $query->where('moyennes_generales_s1.annee_scolaire_id', $anneeScolaireActive->id);
                
                if ($moyenne_min !== null) {
                    $query->where('moyennes_generales_s1.moyenne', '>=', $moyenne_min);
                }
                
                if ($moyenne_max !== null) {
                    $query->where('moyennes_generales_s1.moyenne', '<=', $moyenne_max);
                }
                
                if ($admis_only) {
                    $query->where('moyennes_generales_s1.moyenne', '>=', 10);
                }
            });
        }
        
        // Appliquer le tri
        switch ($sort_by) {
            case 'moyenne_desc':
                $elevesQuery->join('moyennes_generales_s1', function($join) use ($anneeScolaireActive) {
                        $join->on('eleves.id', '=', 'moyennes_generales_s1.eleve_id')
                             ->where('moyennes_generales_s1.annee_scolaire_id', '=', $anneeScolaireActive->id);
                    })
                    ->select('eleves.*')
                    ->orderByDesc('moyennes_generales_s1.moyenne');
                break;
            case 'moyenne_asc':
                $elevesQuery->join('moyennes_generales_s1', function($join) use ($anneeScolaireActive) {
                        $join->on('eleves.id', '=', 'moyennes_generales_s1.eleve_id')
                             ->where('moyennes_generales_s1.annee_scolaire_id', '=', $anneeScolaireActive->id);
                    })
                    ->select('eleves.*')
                    ->orderBy('moyennes_generales_s1.moyenne');
                break;
            case 'rang':
                $elevesQuery->join('moyennes_generales_s1', function($join) use ($anneeScolaireActive) {
                        $join->on('eleves.id', '=', 'moyennes_generales_s1.eleve_id')
                             ->where('moyennes_generales_s1.annee_scolaire_id', '=', $anneeScolaireActive->id);
                    })
                    ->select('eleves.*')
                    ->orderBy('moyennes_generales_s1.rang');
                break;
            default: // Cas 'nom' ou autre
                $elevesQuery->orderBy('eleves.classe_id')
                    ->orderBy('eleves.nom')
                    ->orderBy('eleves.prenom');
                break;
        }
        
        // Récupérer les élèves avec pagination
        $eleves = $elevesQuery->paginate(20)->withQueryString();
        
        return view('semestre1.eleves', compact(
            'anneeScolaireActive', 
            'niveaux', 
            'classes', 
            'eleves', 
            'niveau_id', 
            'classe_id'
        ));
    }

    /**
 * Affiche les notes des élèves par discipline
 */
/**
 * Affiche les notes des élèves par discipline
 */
public function disciplinesNotes(Request $request)
{
    $anneeScolaireActive = AnneeScolaire::where('active', true)->first();
    if (!$anneeScolaireActive) {
        return redirect()->route('parametres.index')->with('error', 'Aucune année scolaire active. Veuillez en configurer une d\'abord.');
    }
    
    $niveaux = Niveau::where('actif', true)->orderBy('libelle')->get();
    $classes = collect();
    
    // Récupérer les paramètres de filtrage
    $niveau_id = $request->query('niveau_id');
    $classe_id = $request->query('classe_id');
    $sort_by = $request->query('sort_by', 'nom');
    
    if ($niveau_id) {
        $classes = Classe::where('niveau_id', $niveau_id)
                    ->where('actif', true)
                    ->orderBy('libelle')
                    ->get();
    }
    
    // Initialiser la requête des élèves
    $elevesQuery = Eleve::where('annee_scolaire_id', $anneeScolaireActive->id);
    
    // Appliquer les filtres
    if ($classe_id) {
        $elevesQuery->where('classe_id', $classe_id);
    } elseif ($niveau_id) {
        $elevesQuery->whereHas('classe', function($query) use ($niveau_id) {
            $query->where('niveau_id', $niveau_id);
        });
    }
    
    // Appliquer le tri
    switch ($sort_by) {
        case 'moyenne_desc':
            $elevesQuery->join('moyennes_generales_s1', function($join) use ($anneeScolaireActive) {
                    $join->on('eleves.id', '=', 'moyennes_generales_s1.eleve_id')
                         ->where('moyennes_generales_s1.annee_scolaire_id', '=', $anneeScolaireActive->id);
                })
                ->select('eleves.*')
                ->orderByDesc('moyennes_generales_s1.moyenne');
            break;
        case 'moyenne_asc':
            $elevesQuery->join('moyennes_generales_s1', function($join) use ($anneeScolaireActive) {
                    $join->on('eleves.id', '=', 'moyennes_generales_s1.eleve_id')
                         ->where('moyennes_generales_s1.annee_scolaire_id', '=', $anneeScolaireActive->id);
                })
                ->select('eleves.*')
                ->orderBy('moyennes_generales_s1.moyenne');
            break;
        default:
            $elevesQuery->orderBy('nom')
                ->orderBy('prenom');
            break;
    }
    
    // Récupérer les élèves avec pagination
    $eleves = $elevesQuery->paginate(20)->withQueryString();
    
    // Récupérer les disciplines pour les élèves de la classe sélectionnée
    // En utilisant les notes existantes pour déterminer les disciplines disponibles
    $disciplineIds = [];
    if ($classe_id) {
        $disciplineIds = NoteS1::whereHas('eleve', function($query) use ($classe_id, $anneeScolaireActive) {
                    $query->where('classe_id', $classe_id)
                          ->where('annee_scolaire_id', $anneeScolaireActive->id);
                })
                ->where('annee_scolaire_id', $anneeScolaireActive->id)
                ->distinct('discipline_id')
                ->pluck('discipline_id')
                ->toArray();
    } else {
        // Si pas de classe spécifique, prendre toutes les disciplines utilisées
        $disciplineIds = NoteS1::where('annee_scolaire_id', $anneeScolaireActive->id)
                ->distinct('discipline_id')
                ->pluck('discipline_id')
                ->toArray();
    }
    
    // Récupérer les disciplines avec leurs détails
    $disciplines = Discipline::whereIn('id', $disciplineIds)
                 ->orderBy('libelle')
                 ->get();
    
    // Récupérer toutes les notes pour les élèves de la page actuelle
    $eleveIds = $eleves->pluck('id')->toArray();
    $notes = NoteS1::whereIn('eleve_id', $eleveIds)
             ->whereIn('discipline_id', $disciplineIds)
             ->where('annee_scolaire_id', $anneeScolaireActive->id)
             ->get();
    
    // Organiser les notes par élève et discipline
    $notesByEleve = [];
    foreach ($notes as $note) {
        if (!isset($notesByEleve[$note->eleve_id])) {
            $notesByEleve[$note->eleve_id] = [];
        }
        
        $notesByEleve[$note->eleve_id][$note->discipline_id] = $note;
    }
    
    return view('semestre1.disciplines_notes', compact(
        'anneeScolaireActive',
        'niveaux',
        'classes',
        'eleves',
        'disciplines',
        'notesByEleve',
        'niveau_id',
        'classe_id'
    ));
}
    
    /**
     * Affiche les détails d'un élève spécifique pour le semestre 1
     */
    public function eleveDetails($id)
    {
        $anneeScolaireActive = AnneeScolaire::where('active', true)->first();
        
        $eleve = Eleve::with([
                'moyenneGeneraleS1',
                'notesS1.discipline',
                'classe.niveau'
            ])
            ->where('id', $id)
            ->where('annee_scolaire_id', $anneeScolaireActive->id)
            ->firstOrFail();
        
        return view('semestre1.eleve_details', compact('anneeScolaireActive', 'eleve'));
    }
    
    /**
     * Exporte la liste des élèves en PDF
     */
    public function exportPdf(Request $request)
    {
        $anneeScolaireActive = AnneeScolaire::where('active', true)->first();
        if (!$anneeScolaireActive) {
            return redirect()->route('parametres.index')->with('error', 'Aucune année scolaire active. Veuillez en configurer une d\'abord.');
        }
        
        // Récupérer les paramètres de filtrage
        $niveau_id = $request->query('niveau_id');
        $classe_id = $request->query('classe_id');
        $sexe = $request->query('sexe');
        $moyenne_min = $request->query('moyenne_min');
        $moyenne_max = $request->query('moyenne_max');
        $admis_only = $request->query('admis_only');
        $sort_by = $request->query('sort_by', 'nom');
        
        // Préparer les informations sur les filtres pour le PDF
        $filtres = [];
        
        if ($niveau_id) {
            $niveau = Niveau::find($niveau_id);
            $filtres['Niveau'] = $niveau ? $niveau->libelle : '';
        }
        
        if ($classe_id) {
            $classe = Classe::find($classe_id);
            $filtres['Classe'] = $classe ? $classe->libelle : '';
        }
        
        if ($sexe) {
            $filtres['Sexe'] = $sexe == 'M' ? 'Masculin' : 'Féminin';
        }
        
        if ($moyenne_min !== null) {
            $filtres['Moyenne min'] = $moyenne_min;
        }
        
        if ($moyenne_max !== null) {
            $filtres['Moyenne max'] = $moyenne_max;
        }
        
        if ($admis_only) {
            $filtres['Admis uniquement'] = 'Oui';
        }
        
        switch ($sort_by) {
            case 'moyenne_desc':
                $filtres['Tri'] = 'Moyenne (décroissante)';
                break;
            case 'moyenne_asc':
                $filtres['Tri'] = 'Moyenne (croissante)';
                break;
            case 'rang':
                $filtres['Tri'] = 'Rang';
                break;
            default:
                $filtres['Tri'] = 'Nom';
                break;
        }
        
        // Initialiser la requête des élèves avec les relations nécessaires
        $elevesQuery = Eleve::with(['moyenneGeneraleS1', 'classe.niveau'])
                      ->where('eleves.annee_scolaire_id', $anneeScolaireActive->id);
        
        // Appliquer les filtres
        if ($classe_id) {
            $elevesQuery->where('eleves.classe_id', $classe_id);
        } elseif ($niveau_id) {
            $elevesQuery->whereHas('classe', function($query) use ($niveau_id) {
                $query->where('niveau_id', $niveau_id);
            });
        }
        
        if ($sexe) {
            $elevesQuery->where('eleves.sexe', $sexe);
        }
        
        // Filtrer par moyenne
        if ($moyenne_min !== null || $moyenne_max !== null || $admis_only) {
            $elevesQuery->whereHas('moyenneGeneraleS1', function($query) use ($moyenne_min, $moyenne_max, $admis_only, $anneeScolaireActive) {
                $query->where('moyennes_generales_s1.annee_scolaire_id', $anneeScolaireActive->id);
                
                if ($moyenne_min !== null) {
                    $query->where('moyennes_generales_s1.moyenne', '>=', $moyenne_min);
                }
                
                if ($moyenne_max !== null) {
                    $query->where('moyennes_generales_s1.moyenne', '<=', $moyenne_max);
                }
                
                if ($admis_only) {
                    $query->where('moyennes_generales_s1.moyenne', '>=', 10);
                }
            });
        }
        
        // Appliquer le tri
        switch ($sort_by) {
            case 'moyenne_desc':
                $elevesQuery->join('moyennes_generales_s1', function($join) use ($anneeScolaireActive) {
                        $join->on('eleves.id', '=', 'moyennes_generales_s1.eleve_id')
                             ->where('moyennes_generales_s1.annee_scolaire_id', '=', $anneeScolaireActive->id);
                    })
                    ->select('eleves.*')
                    ->orderByDesc('moyennes_generales_s1.moyenne');
                break;
            case 'moyenne_asc':
                $elevesQuery->join('moyennes_generales_s1', function($join) use ($anneeScolaireActive) {
                        $join->on('eleves.id', '=', 'moyennes_generales_s1.eleve_id')
                             ->where('moyennes_generales_s1.annee_scolaire_id', '=', $anneeScolaireActive->id);
                    })
                    ->select('eleves.*')
                    ->orderBy('moyennes_generales_s1.moyenne');
                break;
            case 'rang':
                $elevesQuery->join('moyennes_generales_s1', function($join) use ($anneeScolaireActive) {
                        $join->on('eleves.id', '=', 'moyennes_generales_s1.eleve_id')
                             ->where('moyennes_generales_s1.annee_scolaire_id', '=', $anneeScolaireActive->id);
                    })
                    ->select('eleves.*')
                    ->orderBy('moyennes_generales_s1.rang');
                break;
            default: // Cas 'nom' ou autre
                $elevesQuery->orderBy('eleves.classe_id')
                    ->orderBy('eleves.nom')
                    ->orderBy('eleves.prenom');
                break;
        }
        
        // Récupérer tous les élèves pour le PDF
        $eleves = $elevesQuery->get();
        
        // Calculer le nombre total de pages (approximatif, environ 25 élèves par page)
        $totalPages = ceil($eleves->count() / 25);
        
        // Configurer le PDF
        $pdf = Pdf::loadView('semestre1.eleves_pdf', [
            'eleves' => $eleves,
            'anneeScolaireActive' => $anneeScolaireActive,
            'dateExport' => now()->format('d/m/Y H:i'),
            'filtres' => $filtres,
            'page' => 1,
            'totalPages' => $totalPages
        ]);
        
        // Options du PDF
        $pdf->setPaper('a4', 'landscape');
        $pdf->setOption('margin-bottom', 20);
        
        // Nom du fichier
        $fileName = 'eleves_semestre1_' . date('Y-m-d_His') . '.pdf';
        
        // Télécharger le PDF
        return $pdf->download($fileName);
    }
}