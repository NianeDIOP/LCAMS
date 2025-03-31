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
use App\Models\DecisionFinale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GeneralController extends Controller
{
    /**
     * Affiche la page d'accueil du module général
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
        
        // Comparaison des moyennes S1 et S2 par classe
        $moyennesComparees = DB::table('classes')
            ->select('classes.libelle as classe')
            ->selectRaw('(SELECT AVG(mgs1.moyenne) FROM moyennes_generales_s1 as mgs1 
                          JOIN eleves as e1 ON mgs1.eleve_id = e1.id 
                          WHERE e1.classe_id = classes.id AND mgs1.annee_scolaire_id = ?) as moyenne_s1', 
                          [$anneeScolaireActive->id])
            ->selectRaw('(SELECT AVG(mgs2.moyenne) FROM moyennes_generales_s2 as mgs2 
                          JOIN eleves as e2 ON mgs2.eleve_id = e2.id 
                          WHERE e2.classe_id = classes.id AND mgs2.annee_scolaire_id = ?) as moyenne_s2', 
                          [$anneeScolaireActive->id])
            ->join('eleves', 'classes.id', '=', 'eleves.classe_id')
            ->where('eleves.annee_scolaire_id', $anneeScolaireActive->id)
            ->groupBy('classes.id', 'classes.libelle')
            ->get();
            
        // Statistiques d'évolution
        $evolutionStats = [
            'amelioration' => 0,
            'stable' => 0,
            'regression' => 0
        ];
        
        $eleves = Eleve::where('annee_scolaire_id', $anneeScolaireActive->id)->get();
        foreach ($eleves as $eleve) {
            $moyenneS1 = $eleve->moyenneGeneraleS1->moyenne ?? 0;
            $moyenneS2 = $eleve->moyenneGeneraleS2->moyenne ?? 0;
            
            if ($moyenneS1 > 0 && $moyenneS2 > 0) {
                $diff = $moyenneS2 - $moyenneS1;
                
                if ($diff > 0.5) {
                    $evolutionStats['amelioration']++;
                } elseif ($diff < -0.5) {
                    $evolutionStats['regression']++;
                } else {
                    $evolutionStats['stable']++;
                }
            }
        }
        
        return view('general.index', compact(
            'anneeScolaireActive', 
            'totalEleves', 
            'totalClasses', 
            'moyennesComparees', 
            'evolutionStats'
        ));
    }
    
    /**
     * Affiche l'analyse des moyennes comparées
     */
    public function analyseMoyennes()
    {
        $anneeScolaireActive = AnneeScolaire::where('active', true)->first();
        $niveaux = Niveau::where('actif', true)->orderBy('libelle')->get();
        
        return view('general.analyse_moyennes', compact('anneeScolaireActive', 'niveaux'));
    }
    
    /**
     * Récupère les données des moyennes comparées pour une classe spécifique
     */
    public function getMoyennesClasse($classe_id)
    {
        $anneeScolaireActive = AnneeScolaire::where('active', true)->first();
        $classe = Classe::findOrFail($classe_id);
        
        $eleves = Eleve::with(['moyenneGeneraleS1', 'moyenneGeneraleS2'])
            ->where('classe_id', $classe_id)
            ->where('annee_scolaire_id', $anneeScolaireActive->id)
            ->get();
            
        $elevesData = $eleves->map(function($eleve) {
            $moyenneS1 = $eleve->moyenneGeneraleS1->moyenne ?? null;
            $moyenneS2 = $eleve->moyenneGeneraleS2->moyenne ?? null;
            
            // Calculer la moyenne annuelle (si les deux semestres sont disponibles)
            $moyenneAnnuelle = null;
            if ($moyenneS1 !== null && $moyenneS2 !== null) {
                $moyenneAnnuelle = ($moyenneS1 + $moyenneS2) / 2;
            }
            
            // Calculer l'évolution
            $evolution = null;
            if ($moyenneS1 !== null && $moyenneS2 !== null) {
                $evolution = $moyenneS2 - $moyenneS1;
            }
            
            return [
                'id' => $eleve->id,
                'ien' => $eleve->ien,
                'prenom' => $eleve->prenom,
                'nom' => $eleve->nom,
                'sexe' => $eleve->sexe,
                'moyenne_s1' => $moyenneS1,
                'rang_s1' => $eleve->moyenneGeneraleS1->rang ?? null,
                'moyenne_s2' => $moyenneS2,
                'rang_s2' => $eleve->moyenneGeneraleS2->rang ?? null,
                'moyenne_annuelle' => $moyenneAnnuelle,
                'evolution' => $evolution
            ];
        });
            
        return response()->json([
            'classe' => $classe,
            'eleves' => $elevesData
        ]);
    }
    
    /**
     * Affiche l'analyse des disciplines comparées
     */
    public function analyseDisciplines()
    {
        $anneeScolaireActive = AnneeScolaire::where('active', true)->first();
        $niveaux = Niveau::where('actif', true)->orderBy('libelle')->get();
        $disciplines = Discipline::where('type', 'principale')->orderBy('libelle')->get();
        
        return view('general.analyse_disciplines', compact('anneeScolaireActive', 'niveaux', 'disciplines'));
    }
    
    /**
     * Récupère les données des disciplines comparées pour une classe spécifique
     */
    public function getDisciplinesClasse(Request $request)
    {
        $anneeScolaireActive = AnneeScolaire::where('active', true)->first();
        $classe_id = $request->classe_id;
        $discipline_id = $request->discipline_id;
        
        $discipline = Discipline::findOrFail($discipline_id);
        $classe = Classe::findOrFail($classe_id);
        
        // Récupérer les élèves de la classe
        $eleves = Eleve::where('classe_id', $classe_id)
                      ->where('annee_scolaire_id', $anneeScolaireActive->id)
                      ->get();
        
        $resultats = [];
        
        foreach ($eleves as $eleve) {
            $noteS1 = NoteS1::where('eleve_id', $eleve->id)
                           ->where('discipline_id', $discipline_id)
                           ->where('annee_scolaire_id', $anneeScolaireActive->id)
                           ->first();
                           
            $noteS2 = NoteS2::where('eleve_id', $eleve->id)
                           ->where('discipline_id', $discipline_id)
                           ->where('annee_scolaire_id', $anneeScolaireActive->id)
                           ->first();
                           
            $moyenneS1 = $noteS1->moy_d ?? null;
            $moyenneS2 = $noteS2->moy_d ?? null;
            
            // Calculer la moyenne annuelle (si les deux semestres sont disponibles)
            $moyenneAnnuelle = null;
            if ($moyenneS1 !== null && $moyenneS2 !== null) {
                $moyenneAnnuelle = ($moyenneS1 + $moyenneS2) / 2;
            }
            
            // Calculer l'évolution
            $evolution = null;
            if ($moyenneS1 !== null && $moyenneS2 !== null) {
                $evolution = $moyenneS2 - $moyenneS1;
            }
            
            $resultats[] = [
                'eleve_id' => $eleve->id,
                'prenom' => $eleve->prenom,
                'nom' => $eleve->nom,
                'sexe' => $eleve->sexe,
                'moyenne_s1' => $moyenneS1,
                'moyenne_s2' => $moyenneS2,
                'moyenne_annuelle' => $moyenneAnnuelle,
                'evolution' => $evolution
            ];
        }
        
        // Statistiques
        $statS1 = $this->calculateStats($classe_id, $discipline_id, 1, $anneeScolaireActive->id);
        $statS2 = $this->calculateStats($classe_id, $discipline_id, 2, $anneeScolaireActive->id);
        
        // Évolution
        $evolution = [
            'moyenne' => ($statS2->moyenne ?? 0) - ($statS1->moyenne ?? 0),
            'success_rate' => ($statS2->success_rate ?? 0) - ($statS1->success_rate ?? 0)
        ];
        
        return response()->json([
            'classe' => $classe,
            'discipline' => $discipline,
            'resultats' => $resultats,
            'statistiques' => [
                's1' => $statS1,
                's2' => $statS2,
                'evolution' => $evolution
            ]
        ]);
    }
    
    /**
     * Calcule les statistiques pour une discipline et une classe
     */
    private function calculateStats($classe_id, $discipline_id, $semestre, $annee_scolaire_id)
    {
        $table = 'notes_s'.$semestre;
        
        $stats = DB::table($table)
            ->join('eleves', $table.'.eleve_id', '=', 'eleves.id')
            ->select(
                DB::raw('AVG('.$table.'.moy_d) as moyenne'),
                DB::raw('MIN('.$table.'.moy_d) as min'),
                DB::raw('MAX('.$table.'.moy_d) as max'),
                DB::raw('COUNT(CASE WHEN '.$table.'.moy_d >= 10 THEN 1 END) as success_count'),
                DB::raw('COUNT(*) as total_count')
            )
            ->where('eleves.classe_id', $classe_id)
            ->where($table.'.discipline_id', $discipline_id)
            ->where($table.'.annee_scolaire_id', $annee_scolaire_id)
            ->first();
            
        if ($stats->total_count > 0) {
            $stats->success_rate = ($stats->success_count / $stats->total_count) * 100;
        } else {
            $stats->success_rate = 0;
        }
        
        return $stats;
    }
    
    /**
     * Affiche la page des décisions finales
     */
    public function decisions()
    {
        $anneeScolaireActive = AnneeScolaire::where('active', true)->first();
        $niveaux = Niveau::where('actif', true)->orderBy('libelle')->get();
        
        return view('general.decisions', compact('anneeScolaireActive', 'niveaux'));
    }
    
    /**
     * Récupère les données pour les décisions finales d'une classe
     */
    public function getDecisionsClasse($classe_id)
    {
        $anneeScolaireActive = AnneeScolaire::where('active', true)->first();
        $classe = Classe::findOrFail($classe_id);
        
        $eleves = Eleve::with(['moyenneGeneraleS1', 'moyenneGeneraleS2', 'decisionFinale'])
            ->where('classe_id', $classe_id)
            ->where('annee_scolaire_id', $anneeScolaireActive->id)
            ->get();
            
        $elevesData = $eleves->map(function($eleve) {
            $moyenneS1 = $eleve->moyenneGeneraleS1->moyenne ?? 0;
            $moyenneS2 = $eleve->moyenneGeneraleS2->moyenne ?? 0;
            
            // Calculer la moyenne annuelle (si les deux semestres sont disponibles)
            $moyenneAnnuelle = 0;
            if ($moyenneS1 > 0 || $moyenneS2 > 0) {
                if ($moyenneS1 > 0 && $moyenneS2 > 0) {
                    $moyenneAnnuelle = ($moyenneS1 + $moyenneS2) / 2;
                } else if ($moyenneS1 > 0) {
                    $moyenneAnnuelle = $moyenneS1;
                } else {
                    $moyenneAnnuelle = $moyenneS2;
                }
            }
            
            return [
                'id' => $eleve->id,
                'ien' => $eleve->ien,
                'prenom' => $eleve->prenom,
                'nom' => $eleve->nom,
                'sexe' => $eleve->sexe,
                'moyenne_s1' => $moyenneS1,
                'moyenne_s2' => $moyenneS2,
                'moyenne_annuelle' => $moyenneAnnuelle,
                'decision' => $eleve->decisionFinale->decision ?? '',
                'observation' => $eleve->decisionFinale->observation ?? ''
            ];
        });
            
        return response()->json([
            'classe' => $classe,
            'eleves' => $elevesData
        ]);
    }
    
    /**
     * Enregistre les décisions finales pour une classe
     */
    public function saveDecisions(Request $request)
    {
        $anneeScolaireActive = AnneeScolaire::where('active', true)->first();
        
        $decisions = $request->decisions;
        
        try {
            DB::beginTransaction();
            
            foreach ($decisions as $decision) {
                $eleve_id = $decision['eleve_id'];
                $moyenneAnnuelle = $decision['moyenne_annuelle'];
                $decisionValue = $decision['decision'];
                $observation = $decision['observation'] ?? '';
                
                DecisionFinale::updateOrCreate(
                    [
                        'eleve_id' => $eleve_id,
                        'annee_scolaire_id' => $anneeScolaireActive->id
                    ],
                    [
                        'decision' => $decisionValue,
                        'moyenne_annuelle' => $moyenneAnnuelle,
                        'observation' => $observation
                    ]
                );
            }
            
            DB::commit();
            return response()->json(['message' => 'Décisions enregistrées avec succès']);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Erreur lors de l\'enregistrement des décisions: ' . $e->getMessage()], 500);
        }
    }
    
    /**
     * Affiche la page des rapports
     */
    public function rapports()
    {
        $anneeScolaireActive = AnneeScolaire::where('active', true)->first();
        $niveaux = Niveau::where('actif', true)->orderBy('libelle')->get();
        
        return view('general.rapports', compact('anneeScolaireActive', 'niveaux'));
    }
    
    /**
     * Génère un rapport PDF pour une classe spécifique
     */
    public function genererRapportClasse($classe_id)
    {
        // Cette fonction sera implémentée plus tard
        return response()->json(['message' => 'Fonctionnalité en cours de développement']);
    }
}