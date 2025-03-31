<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Artisan;
use App\Models\ImportHistory;
use App\Models\Eleve;
use App\Models\NoteS1;
use App\Models\NoteS2;
use App\Models\MoyenneGeneraleS1;
use App\Models\MoyenneGeneraleS2;
use App\Models\DecisionFinale;
use App\Models\Classe;
use App\Models\Niveau;
use App\Models\AnneeScolaire;

class DataManagementController extends Controller
{
    /**
     * Affiche la page de gestion des données
     */
    public function index()
    {
        // Récupérer les statistiques sur les données
        $stats = [
            'eleves_count' => Eleve::count(),
            'notes_s1_count' => NoteS1::count(),
            'notes_s2_count' => NoteS2::count(),
            'moy_s1_count' => MoyenneGeneraleS1::count(),
            'moy_s2_count' => MoyenneGeneraleS2::count(),
            'decisions_count' => DecisionFinale::count(),
            'imports_count' => ImportHistory::count(),
            'classes_count' => Classe::count(),
            'niveaux_count' => Niveau::count(),
            'annee_scolaires_count' => AnneeScolaire::count()
        ];
        
        // Récupérer la liste des classes avec données
        $classesWithData = DB::table('classes')
            ->select('classes.id', 'classes.libelle', 'niveaux.libelle as niveau')
            ->join('niveaux', 'classes.niveau_id', '=', 'niveaux.id')
            ->join('eleves', 'classes.id', '=', 'eleves.classe_id')
            ->groupBy('classes.id', 'classes.libelle', 'niveaux.libelle')
            ->get();
        
        return view('data-management.index', compact('stats', 'classesWithData'));
    }
    
    /**
     * Supprime toutes les données à l'exception des configurations
     */
    public function clearAllData(Request $request)
    {
        // Vérification de sécurité
        if (!$request->has('confirm') || $request->confirm !== 'yes') {
            return redirect()->back()->with('error', 'Confirmation requise pour supprimer toutes les données.');
        }
        
        try {
            // Désactiver temporairement les contraintes de clé étrangère
            DB::statement('PRAGMA foreign_keys = OFF');
            
            // Liste des tables à vider
            $tables = [
                'eleves', 'notes_s1', 'notes_s2', 'moyennes_generales_s1', 
                'moyennes_generales_s2', 'decisions_finales', 'import_history'
            ];
            
            foreach ($tables as $table) {
                if (Schema::hasTable($table)) {
                    DB::table($table)->truncate();
                }
            }
            
            // Réactiver les contraintes de clé étrangère
            DB::statement('PRAGMA foreign_keys = ON');
            
            return redirect()->route('data.management')->with('success', 'Toutes les données ont été supprimées avec succès, à l\'exception des configurations.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Erreur lors de la suppression des données: ' . $e->getMessage());
        }
    }
    
    /**
     * Supprime les données d'une classe spécifique
     */
    public function clearClassData(Request $request)
    {
        $request->validate([
            'classe_id' => 'required|exists:classes,id',
            'confirm_class' => 'required|in:yes'
        ]);
        
        $classe_id = $request->classe_id;
        $classe = Classe::find($classe_id);
        
        if (!$classe) {
            return redirect()->back()->with('error', 'Classe non trouvée.');
        }
        
        try {
            DB::beginTransaction();
            
            // Récupérer tous les élèves de cette classe
            $eleves = Eleve::where('classe_id', $classe_id)->get();
            $eleveIds = $eleves->pluck('id')->toArray();
            
            if (count($eleveIds) > 0) {
                // Supprimer les importations liées à cette classe
                ImportHistory::where('classe_id', $classe_id)->delete();
                
                // Supprimer les notes
                NoteS1::whereIn('eleve_id', $eleveIds)->delete();
                NoteS2::whereIn('eleve_id', $eleveIds)->delete();
                
                // Supprimer les moyennes générales
                MoyenneGeneraleS1::whereIn('eleve_id', $eleveIds)->delete();
                MoyenneGeneraleS2::whereIn('eleve_id', $eleveIds)->delete();
                
                // Supprimer les décisions finales
                DecisionFinale::whereIn('eleve_id', $eleveIds)->delete();
                
                // Supprimer les élèves
                Eleve::whereIn('id', $eleveIds)->delete();
            }
            
            DB::commit();
            
            return redirect()->route('data.management')->with('success', 'Les données de la classe ' . $classe->libelle . ' ont été supprimées avec succès.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Erreur lors de la suppression des données: ' . $e->getMessage());
        }
    }
    
    /**
     * Supprime les données d'un semestre spécifique
     */
    public function clearSemesterData(Request $request)
    {
        $request->validate([
            'semestre' => 'required|in:1,2',
            'confirm_semestre' => 'required|in:yes'
        ]);
        
        $semestre = $request->semestre;
        
        try {
            DB::beginTransaction();
            
            if ($semestre == 1) {
                // Supprimer les importations du semestre 1
                ImportHistory::where('semestre', 1)->delete();
                
                // Supprimer les notes du semestre 1
                NoteS1::truncate();
                
                // Supprimer les moyennes générales du semestre 1
                MoyenneGeneraleS1::truncate();
            } else {
                // Supprimer les importations du semestre 2
                ImportHistory::where('semestre', 2)->delete();
                
                // Supprimer les notes du semestre 2
                NoteS2::truncate();
                
                // Supprimer les moyennes générales du semestre 2
                MoyenneGeneraleS2::truncate();
                
                // Supprimer les décisions finales
                DecisionFinale::truncate();
            }
            
            DB::commit();
            
            return redirect()->route('data.management')->with('success', 'Les données du semestre ' . $semestre . ' ont été supprimées avec succès.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Erreur lors de la suppression des données: ' . $e->getMessage());
        }
    }
}