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
}