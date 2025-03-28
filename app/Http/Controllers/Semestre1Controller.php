<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Barryvdh\DomPDF\Facade\Pdf;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use App\Models\Niveau;
use App\Models\Classe;

class Semestre1Controller extends Controller
{
    /**
     * Affiche la page principale du semestre 1
     */
   /**
 * Affiche la page principale du semestre 1 avec des statistiques
 */
public function index()
{
    // Récupérer les statistiques des fichiers importés
    $fileCount = DB::table('imported_files')
        ->where('semestre', 1)
        ->count();
    
    // Récupérer le nombre d'élèves (distinct) dans les fichiers importés
    $studentCount = DB::table('excel_data')
        ->join('imported_files', 'excel_data.file_id', '=', 'imported_files.id')
        ->where('imported_files.semestre', 1)
        ->distinct()
        ->count(DB::raw('JSON_EXTRACT(excel_data.data, "$[0]")'));  // IEN est généralement à l'indice 0
    
    // Récupérer les classes actives pour le semestre 1
    $classCount = DB::table('classes')
        ->where('active', 1)
        ->count();
    
    // Calculer la moyenne générale à partir de tous les fichiers importés
    $avgQuery = DB::table('excel_data')
        ->join('imported_files', 'excel_data.file_id', '=', 'imported_files.id')
        ->where('imported_files.semestre', 1)
        ->select(DB::raw('AVG(CAST(REPLACE(JSON_EXTRACT(data, "$[9]"), ",", ".") AS DECIMAL(10,2))) as average_grade'));
    
    $avgResult = $avgQuery->first();
    $averageGrade = $avgResult ? round($avgResult->average_grade, 2) : 0;
    
    // Récupérer les derniers fichiers importés
    $recentFiles = DB::table('imported_files AS f')
        ->leftJoin('niveaux AS n', 'f.niveau_id', '=', 'n.id')
        ->leftJoin('classes AS c', 'f.classe_id', '=', 'c.id')
        ->select('f.*', 'n.nom AS niveau_nom', 'c.nom AS classe_nom')
        ->where('f.semestre', 1)
        ->orderBy('f.created_at', 'desc')
        ->limit(5)
        ->get();
    
    // Calculer les statistiques de performance
    $performanceStats = [
        'excellent' => 0,   // >= 16
        'good' => 0,        // >= 14 et < 16
        'average' => 0,     // >= 10 et < 14
        'poor' => 0,        // < 10
    ];
    
    // Si nous avons des fichiers importés, calculons les statistiques de performance
    if ($fileCount > 0) {
        $excellentCount = DB::table('excel_data')
            ->join('imported_files', 'excel_data.file_id', '=', 'imported_files.id')
            ->where('imported_files.semestre', 1)
            ->whereRaw('CAST(REPLACE(JSON_EXTRACT(data, "$[9]"), ",", ".") AS DECIMAL(10,2)) >= 16')
            ->count();
        
        $goodCount = DB::table('excel_data')
            ->join('imported_files', 'excel_data.file_id', '=', 'imported_files.id')
            ->where('imported_files.semestre', 1)
            ->whereRaw('CAST(REPLACE(JSON_EXTRACT(data, "$[9]"), ",", ".") AS DECIMAL(10,2)) >= 14')
            ->whereRaw('CAST(REPLACE(JSON_EXTRACT(data, "$[9]"), ",", ".") AS DECIMAL(10,2)) < 16')
            ->count();
        
        $averageCount = DB::table('excel_data')
            ->join('imported_files', 'excel_data.file_id', '=', 'imported_files.id')
            ->where('imported_files.semestre', 1)
            ->whereRaw('CAST(REPLACE(JSON_EXTRACT(data, "$[9]"), ",", ".") AS DECIMAL(10,2)) >= 10')
            ->whereRaw('CAST(REPLACE(JSON_EXTRACT(data, "$[9]"), ",", ".") AS DECIMAL(10,2)) < 14')
            ->count();
        
        $poorCount = DB::table('excel_data')
            ->join('imported_files', 'excel_data.file_id', '=', 'imported_files.id')
            ->where('imported_files.semestre', 1)
            ->whereRaw('CAST(REPLACE(JSON_EXTRACT(data, "$[9]"), ",", ".") AS DECIMAL(10,2)) < 10')
            ->count();
        
        $totalCount = $excellentCount + $goodCount + $averageCount + $poorCount;
        
        if ($totalCount > 0) {
            $performanceStats = [
                'excellent' => round($excellentCount / $totalCount * 100),
                'good' => round($goodCount / $totalCount * 100),
                'average' => round($averageCount / $totalCount * 100),
                'poor' => round($poorCount / $totalCount * 100),
            ];
        }
    }
    
    // Obtenir la répartition par sexe
    $genderStats = [
        'male' => 0,
        'female' => 0,
    ];
    
    if ($fileCount > 0) {
        $maleCount = DB::table('excel_data')
            ->join('imported_files', 'excel_data.file_id', '=', 'imported_files.id')
            ->where('imported_files.semestre', 1)
            ->whereRaw('JSON_EXTRACT(data, "$[3]") = "H" OR JSON_EXTRACT(data, "$[3]") = "h"')
            ->count();
        
        $femaleCount = DB::table('excel_data')
            ->join('imported_files', 'excel_data.file_id', '=', 'imported_files.id')
            ->where('imported_files.semestre', 1)
            ->whereRaw('JSON_EXTRACT(data, "$[3]") = "F" OR JSON_EXTRACT(data, "$[3]") = "f"')
            ->count();
        
        $totalGender = $maleCount + $femaleCount;
        
        if ($totalGender > 0) {
            $genderStats = [
                'male' => round($maleCount / $totalGender * 100),
                'female' => round($femaleCount / $totalGender * 100),
            ];
        }
    }
    
    return view('semestre1.index', compact(
        'fileCount',
        'studentCount',
        'classCount',
        'averageGrade',
        'recentFiles',
        'performanceStats',
        'genderStats'
    ));
}

    /**
     * Affiche le tableau de bord du semestre 1
     */
   /**
 * Affiche le tableau de bord du semestre 1
 */
/**
 * Affiche le tableau de bord du semestre 1
 */
/**
 * Affiche le tableau de bord du semestre 1 avec données filtrées
 */
/**
 * Affiche le tableau de bord du semestre 1 avec données filtrées
 */
public function dashboard(Request $request)
{
    // 1. Récupération des paramètres de filtrage
    $niveau_id = $request->input('niveau_id');
    $classe_id = $request->input('classe_id');
    $sexe = $request->input('sexe');
    $min_moyenne = $request->input('min_moyenne');
    $max_moyenne = $request->input('max_moyenne');

    // 2. Configuration de l'établissement
    $etablissement = DB::table('etablissements')->first();

    // 3. Statistiques fichiers
    $fileCount = DB::table('imported_files')
        ->where('semestre', 1)
        ->count();

    // 4. Requête de base pour les élèves
    $query = DB::table('excel_data')
        ->join('imported_files', 'excel_data.file_id', '=', 'imported_files.id')
        ->where('imported_files.semestre', 1);

    // 5. Application des filtres
    if ($niveau_id) {
        $query->where('imported_files.niveau_id', $niveau_id);
    }

    if ($classe_id) {
        $query->where('imported_files.classe_id', $classe_id);
    }

    if ($sexe) {
        if ($sexe == 'F') {
            $query->whereRaw('JSON_EXTRACT(data, "$[3]") IN ("F", "f")');
        } else if ($sexe == 'G') {
            $query->whereRaw('JSON_EXTRACT(data, "$[3]") IN ("H", "h", "M", "m", "G", "g")');
        }
    }

    if ($min_moyenne !== null) {
        $query->whereRaw('CAST(REPLACE(JSON_EXTRACT(data, "$[9]"), ",", ".") AS DECIMAL(10,2)) >= ?', [$min_moyenne]);
    }

    if ($max_moyenne !== null) {
        $query->whereRaw('CAST(REPLACE(JSON_EXTRACT(data, "$[9]"), ",", ".") AS DECIMAL(10,2)) <= ?', [$max_moyenne]);
    }

    // 6. Calcul des statistiques élèves basées sur les filtres appliqués
    $totalEleves = $query->clone()->distinct()->count(DB::raw('JSON_EXTRACT(data, "$[0]")'));
    
    $fillesCount = $query->clone()
        ->whereRaw('JSON_EXTRACT(data, "$[3]") IN ("F", "f")')
        ->distinct()
        ->count(DB::raw('JSON_EXTRACT(data, "$[0]")'));
    
    $garconsCount = $query->clone()
        ->whereRaw('JSON_EXTRACT(data, "$[3]") IN ("H", "h", "M", "m", "G", "g")')
        ->distinct()
        ->count(DB::raw('JSON_EXTRACT(data, "$[0]")'));

    // Elèves avec moyenne >= 10
    $elevesAvecMoyenne = $query->clone()
        ->whereRaw('CAST(REPLACE(JSON_EXTRACT(data, "$[9]"), ",", ".") AS DECIMAL(10,2)) >= 10')
        ->distinct()
        ->count(DB::raw('JSON_EXTRACT(data, "$[0]")'));
    
    // Elèves filles avec moyenne >= 10
    $fillesAvecMoyenne = $query->clone()
        ->whereRaw('JSON_EXTRACT(data, "$[3]") IN ("F", "f")')
        ->whereRaw('CAST(REPLACE(JSON_EXTRACT(data, "$[9]"), ",", ".") AS DECIMAL(10,2)) >= 10')
        ->distinct()
        ->count(DB::raw('JSON_EXTRACT(data, "$[0]")'));
    
    // Elèves garçons avec moyenne >= 10
    $garconsAvecMoyenne = $query->clone()
        ->whereRaw('JSON_EXTRACT(data, "$[3]") IN ("H", "h", "M", "m", "G", "g")')
        ->whereRaw('CAST(REPLACE(JSON_EXTRACT(data, "$[9]"), ",", ".") AS DECIMAL(10,2)) >= 10')
        ->distinct()
        ->count(DB::raw('JSON_EXTRACT(data, "$[0]")'));

    $tauxReussite = $totalEleves > 0 
        ? round(($elevesAvecMoyenne / $totalEleves) * 100)
        : 0;

    // Moyennes
    $noteMoyenneResult = $query->clone()
        ->whereRaw('JSON_EXTRACT(data, "$[9]") IS NOT NULL')
        ->select(DB::raw('AVG(CAST(REPLACE(JSON_EXTRACT(data, "$[9]"), ",", ".") AS DECIMAL(10,2))) as moyenne'))
        ->first();
    $noteMoyenne = $noteMoyenneResult ? $noteMoyenneResult->moyenne : 0;
    
    // Meilleure moyenne
    $plusForteMoyenneResult = $query->clone()
        ->whereRaw('JSON_EXTRACT(data, "$[9]") IS NOT NULL')
        ->select(DB::raw('MAX(CAST(REPLACE(JSON_EXTRACT(data, "$[9]"), ",", ".") AS DECIMAL(10,2))) as max_moyenne'))
        ->first();
    $plusForteMoyenne = $plusForteMoyenneResult ? $plusForteMoyenneResult->max_moyenne : 0;
    
    // Plus faible moyenne
    $plusFaibleMoyenneResult = $query->clone()
        ->whereRaw('JSON_EXTRACT(data, "$[9]") IS NOT NULL')
        ->select(DB::raw('MIN(CAST(REPLACE(JSON_EXTRACT(data, "$[9]"), ",", ".") AS DECIMAL(10,2))) as min_moyenne'))
        ->first();
    $plusFaibleMoyenne = $plusFaibleMoyenneResult ? $plusFaibleMoyenneResult->min_moyenne : 0;
    
    // Mentions
    $felicitations = $query->clone()
        ->whereRaw('JSON_EXTRACT(data, "$[12]") LIKE "%élicitation%"')
        ->distinct()
        ->count(DB::raw('JSON_EXTRACT(data, "$[0]")'));
    
    $encouragements = $query->clone()
        ->whereRaw('JSON_EXTRACT(data, "$[12]") LIKE "%ncouragement%"')
        ->distinct()
        ->count(DB::raw('JSON_EXTRACT(data, "$[0]")'));
    
    $tableauHonneur = $query->clone()
        ->whereRaw('JSON_EXTRACT(data, "$[12]") LIKE "%honneur%"')
        ->distinct()
        ->count(DB::raw('JSON_EXTRACT(data, "$[0]")'));
    
    // Observations
    $mieuxFaire = $query->clone()
        ->whereRaw('JSON_EXTRACT(data, "$[12]") LIKE "%ieux faire%" OR JSON_EXTRACT(data, "$[12]") LIKE "%assable%"')
        ->distinct()
        ->count(DB::raw('JSON_EXTRACT(data, "$[0]")'));
    
    $doitContinuer = $query->clone()
        ->whereRaw('JSON_EXTRACT(data, "$[12]") LIKE "%ontinuer%"')
        ->distinct()
        ->count(DB::raw('JSON_EXTRACT(data, "$[0]")'));
    
    $risqueRedoubler = $query->clone()
        ->whereRaw('JSON_EXTRACT(data, "$[12]") LIKE "%edoubler%" OR JSON_EXTRACT(data, "$[11]") LIKE "%edoubler%"')
        ->distinct()
        ->count(DB::raw('JSON_EXTRACT(data, "$[0]")'));
        
    // 7. Statistiques détaillées par niveau (adaptées aux filtres aussi)
    $niveauxQuery = DB::table('imported_files as f')
        ->join('niveaux as n', 'f.niveau_id', '=', 'n.id')
        ->where('f.semestre', 1);
        
    // Appliquer les filtres de niveau si spécifiés
    if ($niveau_id) {
        $niveauxQuery->where('f.niveau_id', $niveau_id);
    }
    
    $niveaux = $niveauxQuery->select('n.id', 'n.nom', 'n.code')
        ->distinct()
        ->get();

    $statsNiveaux = [];
    foreach ($niveaux as $niveau) {
        // Construction de la requête de base pour ce niveau
        $niveauQuery = DB::table('excel_data')
            ->join('imported_files', 'excel_data.file_id', '=', 'imported_files.id')
            ->where('imported_files.semestre', 1)
            ->where('imported_files.niveau_id', $niveau->id);
        
        // Appliquer les filtres restants
        if ($classe_id) {
            $niveauQuery->where('imported_files.classe_id', $classe_id);
        }
        
        if ($sexe) {
            if ($sexe == 'F') {
                $niveauQuery->whereRaw('JSON_EXTRACT(data, "$[3]") IN ("F", "f")');
            } else if ($sexe == 'G') {
                $niveauQuery->whereRaw('JSON_EXTRACT(data, "$[3]") IN ("H", "h", "M", "m", "G", "g")');
            }
        }
        
        if ($min_moyenne !== null) {
            $niveauQuery->whereRaw('CAST(REPLACE(JSON_EXTRACT(data, "$[9]"), ",", ".") AS DECIMAL(10,2)) >= ?', [$min_moyenne]);
        }
        
        if ($max_moyenne !== null) {
            $niveauQuery->whereRaw('CAST(REPLACE(JSON_EXTRACT(data, "$[9]"), ",", ".") AS DECIMAL(10,2)) <= ?', [$max_moyenne]);
        }

        // Calcul de l'effectif
        $effectif = $niveauQuery->clone()
            ->distinct()
            ->count(DB::raw('JSON_EXTRACT(data, "$[0]")'));

        // Calcul de la moyenne
        $moyenneNiveauResult = $niveauQuery->clone()
            ->whereRaw('JSON_EXTRACT(data, "$[9]") IS NOT NULL')
            ->select(DB::raw('AVG(CAST(REPLACE(JSON_EXTRACT(data, "$[9]"), ",", ".") AS DECIMAL(10,2))) as moyenne_niveau'))
            ->first();
        $moyenneNiveau = $moyenneNiveauResult ? $moyenneNiveauResult->moyenne_niveau : 0;

        // Calcul du taux de réussite
        $reussiteCount = $niveauQuery->clone()
            ->whereRaw('CAST(REPLACE(JSON_EXTRACT(data, "$[9]"), ",", ".") AS DECIMAL(10,2)) >= 10')
            ->distinct()
            ->count(DB::raw('JSON_EXTRACT(data, "$[0]")'));

        $tauxReussiteNiveau = $effectif > 0 
            ? round(($reussiteCount / $effectif) * 100)
            : 0;

        // Calcul des félicitations par niveau
        $felicitationsNiveau = $niveauQuery->clone()
            ->whereRaw('JSON_EXTRACT(data, "$[12]") LIKE "%élicitation%"')
            ->distinct()
            ->count(DB::raw('JSON_EXTRACT(data, "$[0]")'));

        // Calcul des encouragements par niveau
        $encouragementsNiveau = $niveauQuery->clone()
            ->whereRaw('JSON_EXTRACT(data, "$[12]") LIKE "%ncouragement%"')
            ->distinct()
            ->count(DB::raw('JSON_EXTRACT(data, "$[0]")'));

        // Calcul du tableau d'honneur par niveau
        $tableauHonneurNiveau = $niveauQuery->clone()
            ->whereRaw('JSON_EXTRACT(data, "$[12]") LIKE "%honneur%"')
            ->distinct()
            ->count(DB::raw('JSON_EXTRACT(data, "$[0]")'));

        $statsNiveaux[] = [
            'id' => $niveau->id,
            'nom' => $niveau->nom,
            'code' => $niveau->code,
            'moyenne' => $moyenneNiveau ? number_format($moyenneNiveau, 2) : '0.00',
            'effectif' => $effectif,
            'taux_reussite' => $tauxReussiteNiveau,
            'felicitations' => $felicitationsNiveau,
            'encouragements' => $encouragementsNiveau,
            'tableau_honneur' => $tableauHonneurNiveau
        ];
    }

    // 8. Récupération des valeurs pour les graphiques d'absences et retards (adaptées aux filtres)
    // Récupérer jusqu'à 14 classes pour le graphique, selon les filtres appliqués
    $classesQuery = DB::table('classes')
        ->where('active', 1);
    
    if ($niveau_id) {
        $classesQuery->where('niveau_id', $niveau_id);
    }
    
    if ($classe_id) {
        $classesQuery->where('id', $classe_id);
    }
    
    $classesData = $classesQuery->orderBy('nom')
        ->limit(14)
        ->pluck('nom')
        ->toArray();

    // Initialiser les tableaux pour stocker les retards et absences
    $retardsData = [];
    $absencesData = [];

    // Pour chaque classe, récupérer les retards et absences
    foreach ($classesData as $classeNom) {
        // Trouver l'ID de la classe à partir de son nom
        $classeObj = DB::table('classes')->where('nom', $classeNom)->first();
        
        if ($classeObj) {
            // Créer une requête pour cette classe spécifique
            $classeSpecificQuery = $query->clone()->where('imported_files.classe_id', $classeObj->id);
            
            // Calcul des retards (colonne 6, index 6 dans le JSON)
            $retardsSum = $classeSpecificQuery->clone()
                ->whereRaw('JSON_EXTRACT(data, "$[6]") IS NOT NULL')
                ->selectRaw('SUM(CAST(REPLACE(JSON_EXTRACT(data, "$[6]"), ",", ".") AS UNSIGNED)) as total_retards')
                ->first();
            
            // Calcul des absences (colonne 7, index 7 dans le JSON)
            $absencesSum = $classeSpecificQuery->clone()
                ->whereRaw('JSON_EXTRACT(data, "$[7]") IS NOT NULL')
                ->selectRaw('SUM(CAST(REPLACE(JSON_EXTRACT(data, "$[7]"), ",", ".") AS UNSIGNED)) as total_absences')
                ->first();
            
            // Ajouter les valeurs aux tableaux (0 si NULL)
            $retardsData[] = ($retardsSum && isset($retardsSum->total_retards)) ? (int)$retardsSum->total_retards : 0;
            $absencesData[] = ($absencesSum && isset($absencesSum->total_absences)) ? (int)$absencesSum->total_absences : 0;
        } else {
            // Si la classe n'est pas trouvée, ajouter des zéros
            $retardsData[] = 0;
            $absencesData[] = 0;
        }
    }

    // Assurez-vous que les tableaux sont non vides pour éviter des erreurs JavaScript
    if (empty($classesData)) {
        $classesData = ['Aucune classe'];
        $retardsData = [0];
        $absencesData = [0];
    }
    
    // Calcul des statistiques de performance selon les filtres
    $performanceStats = [
        'excellent' => 0,   // >= 16
        'good' => 0,        // >= 14 et < 16
        'average' => 0,     // >= 10 et < 14
        'poor' => 0,        // < 10
    ];
    
    // Si nous avons des élèves, calculons les statistiques de performance
    if ($totalEleves > 0) {
        $excellentCount = $query->clone()
            ->whereRaw('CAST(REPLACE(JSON_EXTRACT(data, "$[9]"), ",", ".") AS DECIMAL(10,2)) >= 16')
            ->count();
        
        $goodCount = $query->clone()
            ->whereRaw('CAST(REPLACE(JSON_EXTRACT(data, "$[9]"), ",", ".") AS DECIMAL(10,2)) >= 14')
            ->whereRaw('CAST(REPLACE(JSON_EXTRACT(data, "$[9]"), ",", ".") AS DECIMAL(10,2)) < 16')
            ->count();
        
        $averageCount = $query->clone()
            ->whereRaw('CAST(REPLACE(JSON_EXTRACT(data, "$[9]"), ",", ".") AS DECIMAL(10,2)) >= 10')
            ->whereRaw('CAST(REPLACE(JSON_EXTRACT(data, "$[9]"), ",", ".") AS DECIMAL(10,2)) < 14')
            ->count();
        
        $poorCount = $query->clone()
            ->whereRaw('CAST(REPLACE(JSON_EXTRACT(data, "$[9]"), ",", ".") AS DECIMAL(10,2)) < 10')
            ->count();
        
        $performanceStats = [
            'excellent' => $totalEleves > 0 ? round(($excellentCount / $totalEleves) * 100) : 0,
            'good' => $totalEleves > 0 ? round(($goodCount / $totalEleves) * 100) : 0,
            'average' => $totalEleves > 0 ? round(($averageCount / $totalEleves) * 100) : 0,
            'poor' => $totalEleves > 0 ? round(($poorCount / $totalEleves) * 100) : 0,
        ];
    }

    // 9. Récupération des filtres pour le formulaire
    $niveauxTous = Niveau::orderBy('ordre')->get();
    $classes = DB::table('classes');
    
    if ($niveau_id) {
        $classes->where('niveau_id', $niveau_id);
    }
    
    $classes = $classes->where('active', 1)->orderBy('nom')->get();

    // 10. Vérifier si une demande d'exportation est faite
    if ($request->has('export')) {
        return $this->exportDashboardAsImage($request, compact(
            'totalEleves',
            'fillesCount',
            'garconsCount',
            'elevesAvecMoyenne',
            'fillesAvecMoyenne',
            'garconsAvecMoyenne',
            'tauxReussite',
            'noteMoyenne',
            'plusForteMoyenne',
            'plusFaibleMoyenne',
            'felicitations',
            'encouragements',
            'tableauHonneur',
            'mieuxFaire',
            'doitContinuer',
            'risqueRedoubler',
            'statsNiveaux',
            'niveauxTous',
            'niveaux',
            'classes',
            'etablissement',
            'fileCount',
            'classesData',
            'retardsData',
            'absencesData',
            'performanceStats'
        ));
    }

    // 11. Retour de la vue avec toutes les variables
    return view('semestre1.dashboard', compact(
        'totalEleves',
        'fillesCount',
        'garconsCount',
        'elevesAvecMoyenne',
        'fillesAvecMoyenne',
        'garconsAvecMoyenne',
        'tauxReussite',
        'noteMoyenne',
        'plusForteMoyenne',
        'plusFaibleMoyenne',
        'felicitations',
        'encouragements',
        'tableauHonneur',
        'mieuxFaire',
        'doitContinuer',
        'risqueRedoubler',
        'statsNiveaux',
        'niveauxTous',
        'niveaux',
        'classes',
        'etablissement',
        'fileCount',
        'classesData',
        'retardsData',
        'absencesData',
        'performanceStats',
        'niveau_id',
        'classe_id',
        'sexe',
        'min_moyenne',
        'max_moyenne'
    ));
}
/**
 * Exporte le tableau de bord sous forme d'image après filtrage
 * Cette méthode utilise une capture d'écran côté serveur via une bibliothèque de capture comme Browsershot
 */

    /**
     * Affiche la page d'analyse des disciplines
     */
    /**
 * Affiche la page d'analyse des disciplines du semestre 1
 * avec une extraction et analyse améliorée des données disciplinaires
 * 

 */
/**
 * Affiche la page d'analyse des disciplines du semestre 1
 * Extraction et analyse des moyennes (Moy D) par discipline
 */
public function analyseDisciples(Request $request)
{
    // 1. Récupération des paramètres de filtrage
    $niveau_id = $request->input('niveau_id');
    $classe_id = $request->input('classe_id');
    $discipline = $request->input('discipline');
    $sexe = $request->input('sexe');
    $min_moyenne = $request->input('min_moyenne');
    $max_moyenne = $request->input('max_moyenne');

    // 2. Récupérer tous les niveaux et classes pour les filtres
    $niveaux = DB::table('niveaux')->orderBy('ordre')->get();
    $classes = [];
    
    if ($niveau_id) {
        $classes = DB::table('classes')
            ->where('niveau_id', $niveau_id)
            ->where('active', 1)
            ->orderBy('nom')
            ->get();
    }

    // 3. Récupérer les fichiers importés selon les filtres
    $filesQuery = DB::table('imported_files')
        ->where('semestre', 1)
        ->orderBy('created_at', 'desc');
    
    if ($niveau_id) {
        $filesQuery->where('niveau_id', $niveau_id);
    }
    
    if ($classe_id) {
        $filesQuery->where('classe_id', $classe_id);
    }
    
    $importedFiles = $filesQuery->get();

    // 4. Trouver toutes les disciplines disponibles et leurs données
    $allDisciplines = [];
    $disciplinesData = [];

    foreach ($importedFiles as $file) {
        try {
            $filePath = Storage::path($file->chemin);
            if (!file_exists($filePath)) {
                continue; // Fichier manquant, passer au suivant
            }
            
            $spreadsheet = IOFactory::load($filePath);
            
            // Vérifier si le 3ème onglet (disciplines) existe 
            if ($spreadsheet->getSheetCount() < 3) {
                continue; // Pas assez d'onglets, passer au suivant
            }
            
            // Récupérer le 3ème onglet (index 2) qui contient les matières/disciplines
            $worksheet = $spreadsheet->getSheet(2);
            $highestColumn = $worksheet->getHighestColumn();
            $highestRow = $worksheet->getHighestRow();
            
            // Analyser les en-têtes pour trouver les disciplines et les colonnes Moy D
            $headers = [];
            $disciplineNames = [];
            $moyDColumns = [];
            
            for ($col = 'A'; $col <= $highestColumn; $col++) {
                $headerValue = $worksheet->getCell($col . '1')->getValue();
                $moyDValue = $worksheet->getCell($col . '2')->getValue();
                
                $headers[$col] = $headerValue;
                
                // Identifier les disciplines et leurs colonnes Moy D
                if (!empty($headerValue) && $headerValue != 'IEN' && $headerValue != 'Prenom' && $headerValue != 'Nom' && $headerValue != 'Sexe' && 
                    $headerValue != 'Moy.' && $headerValue != 'Rang' && $headerValue != 'Appréciations') {
                    
                    // Si on trouve une colonne Moy D
                    if ($moyDValue === 'Moy D') {
                        // Recherche de la discipline associée
                        $disciplineName = $headerValue;
                        
                        if (!isset($allDisciplines[$disciplineName])) {
                            $allDisciplines[$disciplineName] = 0;
                        }
                        
                        $disciplineNames[$col] = $disciplineName;
                        $moyDColumns[$col] = $col;
                    }
                }
            }
            
            // Récupérer les infos des élèves depuis le premier onglet pour avoir le sexe
            $firstSheet = $spreadsheet->getSheet(0);
            $studentsInfo = [];
            
            for ($row = 2; $row <= $firstSheet->getHighestRow(); $row++) {
                $ien = $firstSheet->getCell('A' . $row)->getValue();
                if (!empty($ien)) {
                    $sexeValue = $firstSheet->getCell('D' . $row)->getValue();
                    // Normaliser le sexe
                    if ($sexeValue) {
                        $sexeValue = strtoupper($sexeValue);
                        if (in_array($sexeValue, ['M', 'G'])) {
                            $sexeValue = 'H';
                        }
                    }
                    
                    $studentsInfo[$ien] = [
                        'prenom' => $firstSheet->getCell('B' . $row)->getValue(),
                        'nom' => $firstSheet->getCell('C' . $row)->getValue(),
                        'sexe' => $sexeValue
                    ];
                }
            }
            
            // Extraire les moyennes par discipline pour chaque élève
            for ($row = 3; $row <= $highestRow; $row++) {
                $ien = $worksheet->getCell('A' . $row)->getValue();
                if (empty($ien) || !isset($studentsInfo[$ien])) continue;
                
                // Filtrer par sexe si demandé
                if ($sexe && $studentsInfo[$ien]['sexe'] != $sexe) continue;
                
                // Parcourir les colonnes Moy D identifiées
                foreach ($moyDColumns as $col) {
                    $disciplineName = $disciplineNames[$col];
                    $note = $worksheet->getCell($col . $row)->getValue();
                    
                    // Vérifier que c'est une note valide et appliquer les filtres de min/max
                    if (is_numeric(str_replace(',', '.', $note))) {
                        $note = floatval(str_replace(',', '.', $note));
                        
                        if (($min_moyenne !== null && $note < floatval($min_moyenne)) || 
                            ($max_moyenne !== null && $note > floatval($max_moyenne))) {
                            continue;
                        }
                        
                        // Incrémenter le compteur de la discipline
                        $allDisciplines[$disciplineName]++;
                        
                        // Si on n'analyse pas une discipline spécifique ou si c'est la discipline recherchée
                        if (!$discipline || $disciplineName == $discipline) {
                            // Initialiser l'entrée si nécessaire
                            if (!isset($disciplinesData[$disciplineName])) {
                                $disciplinesData[$disciplineName] = [
                                    'nom' => $disciplineName,
                                    'notes' => [],
                                    'notes_filles' => [],
                                    'notes_garcons' => [],
                                    'nb_eleves' => 0,
                                    'nb_filles' => 0,
                                    'nb_garcons' => 0,
                                    'classes' => [],
                                    'niveaux' => [],
                                    'eleves' => []
                                ];
                            }
                            
                            // Ajouter la note
                            $disciplinesData[$disciplineName]['notes'][] = $note;
                            $disciplinesData[$disciplineName]['nb_eleves']++;
                            
                            // Ajouter les infos de l'élève
                            $disciplinesData[$disciplineName]['eleves'][] = [
                                'ien' => $ien,
                                'prenom' => $studentsInfo[$ien]['prenom'],
                                'nom' => $studentsInfo[$ien]['nom'],
                                'sexe' => $studentsInfo[$ien]['sexe'],
                                'note' => $note
                            ];
                            
                            // Ajouter par sexe
                            if ($studentsInfo[$ien]['sexe'] == 'F') {
                                $disciplinesData[$disciplineName]['notes_filles'][] = $note;
                                $disciplinesData[$disciplineName]['nb_filles']++;
                            } else if (in_array($studentsInfo[$ien]['sexe'], ['H', 'M', 'G'])) {
                                $disciplinesData[$disciplineName]['notes_garcons'][] = $note;
                                $disciplinesData[$disciplineName]['nb_garcons']++;
                            }
                            
                            // Ajouter aux classes et niveaux
                            $classeId = $file->classe_id;
                            $niveauId = $file->niveau_id;
                            
                            if (!isset($disciplinesData[$disciplineName]['classes'][$classeId])) {
                                $classeInfo = DB::table('classes')->find($classeId);
                                $disciplinesData[$disciplineName]['classes'][$classeId] = [
                                    'id' => $classeId,
                                    'nom' => $classeInfo ? $classeInfo->nom : 'Classe ' . $classeId,
                                    'notes' => [],
                                    'count_eleves' => 0,
                                    'moyenne' => 0
                                ];
                            }
                            
                            $disciplinesData[$disciplineName]['classes'][$classeId]['notes'][] = $note;
                            $disciplinesData[$disciplineName]['classes'][$classeId]['count_eleves']++;
                            
                            if (!isset($disciplinesData[$disciplineName]['niveaux'][$niveauId])) {
                                $niveauInfo = DB::table('niveaux')->find($niveauId);
                                $disciplinesData[$disciplineName]['niveaux'][$niveauId] = [
                                    'id' => $niveauId,
                                    'nom' => $niveauInfo ? $niveauInfo->nom : 'Niveau ' . $niveauId,
                                    'code' => $niveauInfo ? $niveauInfo->code : 'N/A',
                                    'notes' => [],
                                    'count_eleves' => 0,
                                    'moyenne' => 0
                                ];
                            }
                            
                            $disciplinesData[$disciplineName]['niveaux'][$niveauId]['notes'][] = $note;
                            $disciplinesData[$disciplineName]['niveaux'][$niveauId]['count_eleves']++;
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            \Log::error('Erreur lors de l\'analyse du fichier ' . ($file->nom_fichier ?? 'inconnu') . ': ' . $e->getMessage());
            continue;
        }
    }

    // 5. Calculer les moyennes pour les classes et niveaux
    foreach ($disciplinesData as $discName => &$discData) {
        foreach ($discData['classes'] as &$classeData) {
            if (!empty($classeData['notes'])) {
                $classeData['moyenne'] = array_sum($classeData['notes']) / count($classeData['notes']);
            }
        }
        
        foreach ($discData['niveaux'] as &$niveauData) {
            if (!empty($niveauData['notes'])) {
                $niveauData['moyenne'] = array_sum($niveauData['notes']) / count($niveauData['notes']);
            }
        }
    }

    // 6. Préparer les détails de la discipline sélectionnée
    $disciplineDetails = null;
    
    if ($discipline && isset($disciplinesData[$discipline])) {
        $discData = $disciplinesData[$discipline];
        
        // Calculer les moyennes générales et par sexe
        $notes = $discData['notes'];
        $notesFilles = $discData['notes_filles'];
        $notesGarcons = $discData['notes_garcons'];
        
        $moyenneGenerale = !empty($notes) ? array_sum($notes) / count($notes) : 0;
        $moyenneFilles = !empty($notesFilles) ? array_sum($notesFilles) / count($notesFilles) : 0;
        $moyenneGarcons = !empty($notesGarcons) ? array_sum($notesGarcons) / count($notesGarcons) : 0;
        
        // Répartition par catégorie
        $excellent = count(array_filter($notes, function($n) { return $n >= 16; }));
        $bien = count(array_filter($notes, function($n) { return $n >= 14 && $n < 16; }));
        $assezBien = count(array_filter($notes, function($n) { return $n >= 12 && $n < 14; }));
        $passable = count(array_filter($notes, function($n) { return $n >= 10 && $n < 12; }));
        $insuffisant = count(array_filter($notes, function($n) { return $n < 10; }));
        
        $totalNotes = count($notes);
        $distribution = [
            'excellent' => $totalNotes > 0 ? round(($excellent / $totalNotes) * 100) : 0,
            'bien' => $totalNotes > 0 ? round(($bien / $totalNotes) * 100) : 0,
            'assez_bien' => $totalNotes > 0 ? round(($assezBien / $totalNotes) * 100) : 0,
            'passable' => $totalNotes > 0 ? round(($passable / $totalNotes) * 100) : 0,
            'insuffisant' => $totalNotes > 0 ? round(($insuffisant / $totalNotes) * 100) : 0
        ];
        
        $countReussite = $excellent + $bien + $assezBien + $passable;
        $countReussiteFilles = count(array_filter($notesFilles, function($n) { return $n >= 10; }));
        $countReussiteGarcons = count(array_filter($notesGarcons, function($n) { return $n >= 10; }));
        
        // Stats complémentaires: min, max, écart-type
        $minNote = !empty($notes) ? min($notes) : 0;
        $maxNote = !empty($notes) ? max($notes) : 0;
        
        // Calcul de l'écart type
        $ecartType = 0;
        if (count($notes) > 1) {
            $sumSqDiff = 0;
            foreach ($notes as $note) {
                $sumSqDiff += pow($note - $moyenneGenerale, 2);
            }
            $ecartType = sqrt($sumSqDiff / count($notes));
        }
        
        $disciplineDetails = [
            'nom' => $discipline,
            'nb_eleves' => count($notes),
            'nb_filles' => count($notesFilles),
            'nb_garcons' => count($notesGarcons),
            'moyenne_generale' => $moyenneGenerale,
            'moyenne_filles' => $moyenneFilles,
            'moyenne_garcons' => $moyenneGarcons,
            'distribution' => $distribution,
            'reussite' => $totalNotes > 0 ? round(($countReussite / $totalNotes) * 100) : 0,
            'reussite_filles' => count($notesFilles) > 0 ? round(($countReussiteFilles / count($notesFilles)) * 100) : 0,
            'reussite_garcons' => count($notesGarcons) > 0 ? round(($countReussiteGarcons / count($notesGarcons)) * 100) : 0,
            'min_note' => $minNote,
            'max_note' => $maxNote,
            'ecart_type' => round($ecartType, 2),
            'classes' => $discData['classes'],
            'niveaux' => $discData['niveaux'],
            'eleves' => $discData['eleves']
        ];
    }

    // 7. Récupérer la moyenne générale globale pour comparaison
    $moyenneGeneraleQuery = DB::table('excel_data')
        ->join('imported_files', 'excel_data.file_id', '=', 'imported_files.id')
        ->where('imported_files.semestre', 1)
        ->whereRaw('JSON_VALID(data)')
        ->select(DB::raw('AVG(CAST(REPLACE(JSON_EXTRACT(data, "$[9]"), ",", ".") AS DECIMAL(10,2))) as moyenne_generale'));
    
    if ($niveau_id) {
        $moyenneGeneraleQuery->where('imported_files.niveau_id', $niveau_id);
    }
    
    if ($classe_id) {
        $moyenneGeneraleQuery->where('imported_files.classe_id', $classe_id);
    }
    
    if ($sexe) {
        if ($sexe == 'F') {
            $moyenneGeneraleQuery->whereRaw('JSON_EXTRACT(data, "$[3]") IN ("F", "f")');
        } else {
            $moyenneGeneraleQuery->whereRaw('JSON_EXTRACT(data, "$[3]") IN ("H", "h", "M", "m", "G", "g")');
        }
    }
    
    $globalStats = $moyenneGeneraleQuery->first();

    // 8. Retourner la vue avec les données
    return view('semestre1.analyse', compact(
        'niveaux',
        'classes',
        'importedFiles',
        'allDisciplines',
        'discipline',
        'disciplineDetails',
        'globalStats',
        'niveau_id',
        'classe_id',
        'sexe',
        'min_moyenne',
        'max_moyenne'
    ));
}

    /**
     * Affiche la page des rapports
     */
    public function rapports()
    {
        return view('semestre1.rapports');
    }

    /**
     * Affiche la page de base des moyennes
     */
    public function baseMoyennes()
    {
        // Récupérer les fichiers importés
        $importedFiles = DB::table('imported_files AS f')
            ->leftJoin('niveaux AS n', 'f.niveau_id', '=', 'n.id')
            ->leftJoin('classes AS c', 'f.classe_id', '=', 'c.id')
            ->select('f.*', 'n.nom AS niveau_nom', 'c.nom AS classe_nom')
            ->where('f.semestre', 1)
            ->orderBy('f.created_at', 'desc')
            ->get();
            
        // Récupérer tous les niveaux
        $niveaux = Niveau::where('actif', 1)
            ->orWhereNull('actif')
            ->orderBy('ordre')
            ->get();
        
        return view('semestre1.base', compact('importedFiles', 'niveaux'));
    }

    /**
     * Récupère les classes disponibles pour un niveau donné
     */
    public function getClassesByNiveau($niveau_id)
    {
        $classes = Classe::where('niveau_id', $niveau_id)
            ->where(function($query) {
                $query->where('active', 1)
                    ->orWhereNull('active');
            })
            ->orderBy('nom')
            ->get();
        
        return response()->json($classes);
    }

    /**
     * Importe un fichier Excel
     */
    /**
 * Importe un fichier Excel
 */
/**
 * Importe un fichier Excel
 */
public function importer(Request $request)
{
    // Valider le fichier et les données
    $request->validate([
        'excel_file' => 'required|file|mimes:xlsx,xls',
        'niveau_id' => 'required|exists:niveaux,id',
        'classe_id' => 'required|exists:classes,id',
    ]);
    
    try {
        $file = $request->file('excel_file');
        $fileName = time() . '_' . $file->getClientOriginalName();
        
        // Assurez-vous que le dossier de stockage existe
        Storage::makeDirectory('imports');
        
        // Sauvegarder le fichier dans le stockage
        $path = $file->storeAs('imports', $fileName);
        
        // Utiliser Storage::path pour obtenir le chemin absolu correct du système de fichiers
        $fullPath = Storage::path($path);
        
        // Charger le fichier Excel avec PhpSpreadsheet
        $spreadsheet = IOFactory::load($fullPath);
        
        // Sélectionner spécifiquement le premier onglet "Moyennes eleves"
        $worksheet = $spreadsheet->getSheet(0); // Index 0 = premier onglet
        
        // Obtenir le titre du fichier (pour l'affichage)
        $fileTitle = $file->getClientOriginalName();
        
        // Extraire les en-têtes (ligne 1)
        $headers = [];
        $highestColumn = 'N'; // Limiter aux colonnes de A à N
        for ($col = 'A'; $col <= $highestColumn; $col++) {
            $headers[] = $worksheet->getCell($col . '1')->getValue();
        }
        
        // Extraire les données à partir de la ligne 2 (après les en-têtes)
        $data = [];
        $highestRow = $worksheet->getHighestRow();
        $validRowCount = 0;
        
        for ($row = 2; $row <= $highestRow; $row++) {
            $rowData = [];
            $isEmpty = true;
            
            for ($col = 'A'; $col <= $highestColumn; $col++) {
                $cellValue = $worksheet->getCell($col . $row)->getValue();
                
                // Normalisation de la valeur de Sexe (colonne D, index 3)
                if ($col === 'D' && ($cellValue === 'M' || $cellValue === 'm')) {
                    $cellValue = 'H';
                }
                
                $rowData[] = $cellValue;
                
                // Vérifier si la cellule a une valeur (uniquement pour les colonnes A à K)
                if ($col <= 'K' && $cellValue !== null && trim((string)$cellValue) !== '') {
                    $isEmpty = false;
                }
            }
            
            // N'ajouter que les lignes non vides
            if (!$isEmpty) {
                // Vérifier et remplir la décision du conseil si nécessaire (colonne L / index 11)
                if (empty($rowData[11])) {
                    $moyenne = floatval(str_replace(',', '.', $rowData[9] ?? 0));
                    $rowData[11] = $this->getDecisionConseil($moyenne);
                }
                
                // Vérifier et remplir l'appréciation si nécessaire (colonne M / index 12)
                if (empty($rowData[12])) {
                    $moyenne = floatval(str_replace(',', '.', $rowData[9] ?? 0));
                    $rowData[12] = $this->getAppreciation($moyenne);
                }
                
                $data[] = $rowData;
                $validRowCount++;
            }
            
            // Limiter à 100 lignes maximum
            if ($validRowCount >= 100) {
                break;
            }
        }
        
        // Créer une entrée dans la table des fichiers importés
        $fileId = DB::table('imported_files')->insertGetId([
            'nom_fichier' => $fileTitle,
            'chemin' => $path,
            'semestre' => 1,
            'type' => $request->input('type_fichier', 'statistiques'),
            'niveau_id' => $request->input('niveau_id'),
            'classe_id' => $request->input('classe_id'),
            'nombre_lignes' => count($data), // Le nombre de lignes de données, sans compter l'en-tête
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        
        // Enregistrer les en-têtes
        DB::table('excel_headers')->insert([
            'file_id' => $fileId,
            'headers' => json_encode($headers),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        
        // Stocker les données dans la table excel_data
        foreach ($data as $rowIndex => $row) {
            DB::table('excel_data')->insert([
                'file_id' => $fileId,
                'row_number' => $rowIndex + 2, // La ligne originale dans le fichier (commence à 2)
                'data' => json_encode($row),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        
        return redirect()->route('semestre1.base')
            ->with('success', 'Fichier importé avec succès. ' . count($data) . ' lignes traitées.');
            
    } catch (\Exception $e) {
        return redirect()->route('semestre1.base')
            ->with('error', 'Erreur lors de l\'importation: ' . $e->getMessage());
    }
}


/**
 * Affiche toutes les données détaillées d'un fichier importé
 */
/**
 * Affiche les données brutes du troisième onglet d'un fichier importé
 */

/**
 * Affiche le contenu du troisième onglet d'un fichier importé avec ajout de la colonne Sexe du premier onglet
 */
public function viewDetailedData($id, Request $request)
{
    // Récupérer les infos du fichier
    $file = DB::table('imported_files')->find($id);
    
    if (!$file) {
        return redirect()->route('semestre1.base')
            ->with('error', 'Fichier non trouvé.');
    }
    
    try {
        // Récupérer le chemin du fichier
        $filePath = Storage::path($file->chemin);
        
        // Charger le fichier Excel
        $spreadsheet = IOFactory::load($filePath);
        
        // Vérifier si le troisième onglet existe
        $sheetCount = $spreadsheet->getSheetCount();
        if ($sheetCount < 3) {
            return redirect()->route('semestre1.viewImportedFile', $id)
                ->with('error', 'Le fichier ne contient pas de troisième onglet.');
        }
        
        // Extraire d'abord les données sur le sexe depuis le premier onglet
        $firstSheet = $spreadsheet->getSheet(0);
        $genderData = [];
        
        // Déterminer la colonne qui contient le sexe (normalement colonne D)
        $genderColumn = 'D';
        
        // Vérifier l'en-tête pour confirmer que c'est bien la colonne Sexe
        $sexeHeaderValue = $firstSheet->getCell($genderColumn . '1')->getValue();
        if (strtolower($sexeHeaderValue) != 'sexe') {
            // Si ce n'est pas "Sexe", chercher la bonne colonne
            for ($col = 'A'; $col <= 'G'; $col++) {
                $headerValue = $firstSheet->getCell($col . '1')->getValue();
                if (strtolower($headerValue) == 'sexe') {
                    $genderColumn = $col;
                    break;
                }
            }
        }
        
        // Maintenant que nous avons la colonne du sexe, extraire les données à partir de la ligne 2
        // La colonne IEN servira de clé pour faire la correspondance
        $highestRow = $firstSheet->getHighestRow();
        for ($row = 2; $row <= $highestRow; $row++) {
            $ien = $firstSheet->getCell('A' . $row)->getValue();
            $gender = $firstSheet->getCell($genderColumn . $row)->getValue();
            if (!empty($ien)) {
                $genderData[$ien] = $gender;
            }
        }
        
        // Maintenant, traiter le troisième onglet
        $worksheet = $spreadsheet->getSheet(2);
        $sheetName = $worksheet->getTitle();
        
        // Récupérer les dimensions
        $highestRow = $worksheet->getHighestRow();
        $highestColumnLetter = $worksheet->getHighestColumn();
        $highestColumnIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($highestColumnLetter);
        
        // Générer la liste des colonnes
        $allColumns = [];
        for ($colIndex = 1; $colIndex <= $highestColumnIndex; $colIndex++) {
            $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex);
            $allColumns[] = $colLetter;
        }
        
        // Lire toutes les données brutes
        $rawData = [];
        for ($row = 1; $row <= $highestRow; $row++) {
            $rowData = [];
            
            // Extraire les données des colonnes existantes
            foreach ($allColumns as $col) {
                $cell = $worksheet->getCell($col . $row);
                $value = $cell->getValue();
                
                // Gestion des cellules fusionnées
                foreach ($worksheet->getMergeCells() as $mergeCell) {
                    if ($cell->isInRange($mergeCell)) {
                        $mergeRange = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::splitRange($mergeCell);
                        $firstCell = $mergeRange[0][0];
                        $value = $worksheet->getCell($firstCell)->getValue();
                        break;
                    }
                }
                
                $rowData[$col] = $value;
            }
            
            // Pour les lignes de données (à partir de la ligne 3), ajouter la colonne Sexe après la colonne Nom
            if ($row >= 3) {
                $ien = $rowData['A'] ?? '';
                $sexe = $genderData[$ien] ?? '';
                
                // Créer un nouveau tableau avec la colonne Sexe après Nom
                $newRowData = [];
                $sexeInserted = false;
                
                foreach ($rowData as $col => $value) {
                    $newRowData[$col] = $value;
                    
                    // Après la colonne C (Nom), insérer la colonne Sexe
                    if ($col === 'C' && !$sexeInserted) {
                        $newRowData['SEXE'] = $sexe;
                        $sexeInserted = true;
                    }
                }
                
                $rowData = $newRowData;
            } else if ($row === 1) {
                // Pour la première ligne d'en-tête (entêtes principaux)
                $newRowData = [];
                $sexeInserted = false;
                
                foreach ($rowData as $col => $value) {
                    $newRowData[$col] = $value;
                    
                    if ($col === 'C' && !$sexeInserted) {
                        $newRowData['SEXE'] = 'Sexe';
                        $sexeInserted = true;
                    }
                }
                
                $rowData = $newRowData;
            } else if ($row === 2) {
                // Pour la deuxième ligne (sous-en-têtes)
                $newRowData = [];
                $sexeInserted = false;
                
                foreach ($rowData as $col => $value) {
                    $newRowData[$col] = $value;
                    
                    if ($col === 'C' && !$sexeInserted) {
                        $newRowData['SEXE'] = '';  // Cellule vide pour la sous-entête
                        $sexeInserted = true;
                    }
                }
                
                $rowData = $newRowData;
            }
            
            $rawData[] = $rowData;
        }
        
        // Ajouter SEXE à la liste des colonnes après la colonne C
        $modifiedColumns = [];
        foreach ($allColumns as $index => $col) {
            $modifiedColumns[] = $col;
            if ($col === 'C') {
                $modifiedColumns[] = 'SEXE';
            }
        }
        
        return view('semestre1.view_detailed_data', compact('file', 'rawData', 'modifiedColumns', 'sheetName'));
        
    } catch (\Exception $e) {
        return redirect()->route('semestre1.viewImportedFile', $id)
            ->with('error', 'Erreur lors de la lecture du troisième onglet: ' . $e->getMessage() . ' Ligne: ' . $e->getLine());
    }
}
    /**
     * Affiche les données d'un fichier importé avec filtres
     */
    public function viewImportedFile($id, Request $request)
    {
        // Récupérer les infos du fichier
        $file = DB::table('imported_files')->find($id);
        
        if (!$file) {
            return redirect()->route('semestre1.base')
                ->with('error', 'Fichier non trouvé.');
        }
        
        // Récupérer les en-têtes
        $headerRecord = DB::table('excel_headers')->where('file_id', $id)->first();
        $headers = $headerRecord ? json_decode($headerRecord->headers) : [];
        
        // Récupérer les données
        $rawData = DB::table('excel_data')
            ->where('file_id', $id)
            ->orderBy('row_number')
            ->get()
            ->map(function($item) {
                return [
                    'id' => $item->id,
                    'row_number' => $item->row_number,
                    'data' => json_decode($item->data)
                ];
            });
        
        // Extraction de toutes les données pour manipulation
        $dataArray = [];
        foreach ($rawData as $row) {
            $dataArray[] = [
                'id' => $row['id'],
                'row_number' => $row['row_number'],
                'data' => $row['data']
            ];
        }
        
        // Paramètres de filtrage
        $sortField = $request->input('sort', 'moy'); // Par défaut, tri par moyenne
        $sortDirection = $request->input('direction', 'desc'); // Par défaut, descendant (meilleur à pire)
        $searchTerm = $request->input('search', '');
        $minMoy = $request->input('min_moy', '');
        $maxMoy = $request->input('max_moy', '');
        
        // Filtrage par recherche (nom, prénom ou IEN)
        if (!empty($searchTerm)) {
            $dataArray = array_filter($dataArray, function($row) use ($searchTerm) {
                $ien = strtolower($row['data'][0] ?? ''); // Colonne A: IEN
                $prenom = strtolower($row['data'][1] ?? ''); // Colonne B: Prénom
                $nom = strtolower($row['data'][2] ?? ''); // Colonne C: Nom
                
                $searchLower = strtolower($searchTerm);
                
                return strpos($ien, $searchLower) !== false || 
                       strpos($prenom, $searchLower) !== false || 
                       strpos($nom, $searchLower) !== false;
            });
        }
        
        // Filtrage par plage de moyenne
        if (!empty($minMoy)) {
            $dataArray = array_filter($dataArray, function($row) use ($minMoy) {
                $moy = floatval(str_replace(',', '.', $row['data'][9] ?? 0)); // Colonne J: Moy
                return $moy >= floatval($minMoy);
            });
        }
        
        if (!empty($maxMoy)) {
            $dataArray = array_filter($dataArray, function($row) use ($maxMoy) {
                $moy = floatval(str_replace(',', '.', $row['data'][9] ?? 0)); // Colonne J: Moy
                return $moy <= floatval($maxMoy);
            });
        }
        
        // Tri des données
        if ($sortField === 'moy') {
            usort($dataArray, function($a, $b) use ($sortDirection) {
                $moyA = floatval(str_replace(',', '.', $a['data'][9] ?? 0));
                $moyB = floatval(str_replace(',', '.', $b['data'][9] ?? 0));
                
                if ($sortDirection === 'asc') {
                    return $moyA <=> $moyB;
                } else {
                    return $moyB <=> $moyA;
                }
            });
        } elseif ($sortField === 'rang') {
            usort($dataArray, function($a, $b) use ($sortDirection) {
                $rangA = intval($a['data'][10] ?? 0);
                $rangB = intval($b['data'][10] ?? 0);
                
                if ($sortDirection === 'asc') {
                    return $rangA <=> $rangB;
                } else {
                    return $rangB <=> $rangA;
                }
            });
        } elseif ($sortField === 'nom') {
            usort($dataArray, function($a, $b) use ($sortDirection) {
                $nomA = strtolower($a['data'][2] ?? '');
                $nomB = strtolower($b['data'][2] ?? '');
                
                if ($sortDirection === 'asc') {
                    return strcmp($nomA, $nomB);
                } else {
                    return strcmp($nomB, $nomA);
                }
            });
        }
        
        // Statistiques
        $stats = [
            'count' => count($dataArray),
            'moyenne' => 0,
            'min' => 0,
            'max' => 0
        ];
        
        if (count($dataArray) > 0) {
            $moyennes = array_map(function($row) {
                return floatval(str_replace(',', '.', $row['data'][9] ?? 0));
            }, $dataArray);
            
            $stats['moyenne'] = array_sum($moyennes) / count($moyennes);
            $stats['min'] = min($moyennes);
            $stats['max'] = max($moyennes);
        }
        
        // Préparer les données filtrées pour la vue
        $data = array_values($dataArray);
        
        return view('semestre1.view_file', compact('file', 'data', 'headers', 'sortField', 
            'sortDirection', 'searchTerm', 'minMoy', 'maxMoy', 'stats'));
    }

    /**
     * Exporte les données filtrées en PDF
     */
    public function exportPDF($id, Request $request)
    {
        // Récupérer les infos du fichier avec niveau et classe
        $file = DB::table('imported_files AS f')
            ->leftJoin('niveaux AS n', 'f.niveau_id', '=', 'n.id')
            ->leftJoin('classes AS c', 'f.classe_id', '=', 'c.id')
            ->select('f.*', 'n.nom AS niveau_nom', 'c.nom AS classe_nom')
            ->where('f.id', $id)
            ->first();
        
        if (!$file) {
            return redirect()->route('semestre1.base')
                ->with('error', 'Fichier non trouvé.');
        }
        
        // Récupérer les en-têtes
        $headerRecord = DB::table('excel_headers')->where('file_id', $id)->first();
        $headers = $headerRecord ? json_decode($headerRecord->headers) : [];
        
        // Récupérer les données
        $rawData = DB::table('excel_data')
            ->where('file_id', $id)
            ->orderBy('row_number')
            ->get()
            ->map(function($item) {
                return [
                    'id' => $item->id,
                    'row_number' => $item->row_number,
                    'data' => json_decode($item->data)
                ];
            });
        
        // Extraction de toutes les données pour manipulation
        $dataArray = [];
        foreach ($rawData as $row) {
            $dataArray[] = [
                'id' => $row['id'],
                'row_number' => $row['row_number'],
                'data' => $row['data']
            ];
        }
        
        // Paramètres de filtrage (repris de viewImportedFile)
        $sortField = $request->input('sort', 'moy');
        $sortDirection = $request->input('direction', 'desc');
        $searchTerm = $request->input('search', '');
        $minMoy = $request->input('min_moy', '');
        $maxMoy = $request->input('max_moy', '');
        
        // Filtrage par recherche (nom, prénom ou IEN)
        if (!empty($searchTerm)) {
            $dataArray = array_filter($dataArray, function($row) use ($searchTerm) {
                $ien = strtolower($row['data'][0] ?? '');
                $prenom = strtolower($row['data'][1] ?? '');
                $nom = strtolower($row['data'][2] ?? '');
                
                $searchLower = strtolower($searchTerm);
                
                return strpos($ien, $searchLower) !== false || 
                       strpos($prenom, $searchLower) !== false || 
                       strpos($nom, $searchLower) !== false;
            });
        }
        
        // Filtrage par plage de moyenne
        if (!empty($minMoy)) {
            $dataArray = array_filter($dataArray, function($row) use ($minMoy) {
                $moy = floatval(str_replace(',', '.', $row['data'][9] ?? 0));
                return $moy >= floatval($minMoy);
            });
        }
        
        if (!empty($maxMoy)) {
            $dataArray = array_filter($dataArray, function($row) use ($maxMoy) {
                $moy = floatval(str_replace(',', '.', $row['data'][9] ?? 0));
                return $moy <= floatval($maxMoy);
            });
        }
        
        // Tri des données
        if ($sortField === 'moy') {
            usort($dataArray, function($a, $b) use ($sortDirection) {
                $moyA = floatval(str_replace(',', '.', $a['data'][9] ?? 0));
                $moyB = floatval(str_replace(',', '.', $b['data'][9] ?? 0));
                
                if ($sortDirection === 'asc') {
                    return $moyA <=> $moyB;
                } else {
                    return $moyB <=> $moyA;
                }
            });
        } elseif ($sortField === 'rang') {
            usort($dataArray, function($a, $b) use ($sortDirection) {
                $rangA = intval($a['data'][10] ?? 0);
                $rangB = intval($b['data'][10] ?? 0);
                
                if ($sortDirection === 'asc') {
                    return $rangA <=> $rangB;
                } else {
                    return $rangB <=> $rangA;
                }
            });
        } elseif ($sortField === 'nom') {
            usort($dataArray, function($a, $b) use ($sortDirection) {
                $nomA = strtolower($a['data'][2] ?? '');
                $nomB = strtolower($b['data'][2] ?? '');
                
                if ($sortDirection === 'asc') {
                    return strcmp($nomA, $nomB);
                } else {
                    return strcmp($nomB, $nomA);
                }
            });
        }
        
        // Préparer les données filtrées pour le PDF
        $data = array_values($dataArray);
        
        // Récupérer les informations de l'établissement
        $etablissement = DB::table('etablissements')->first();
        if ($etablissement) {
            $file->nom_etablissement = $etablissement->nom;
            $file->adresse_etablissement = $etablissement->adresse;
            $file->annee_scolaire = $etablissement->annee_scolaire;
            $file->academie = $etablissement->academie;
            $file->ief = $etablissement->ief;
        }
        
        // Générer le PDF avec les options de mise en page correctes
        $pdf = PDF::loadView('semestre1.export_pdf', compact('data', 'headers', 'file', 'searchTerm', 'minMoy', 'maxMoy'));
        $pdf->setPaper('a4', 'portrait'); // Format portrait comme demandé
        $pdf->setOptions([
            'dpi' => 150,
            'defaultFont' => 'times',
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true
        ]);
    
        return $pdf->download('Liste_Moyennes_S1_' . date('YmdHis') . '.pdf');
    }

    /**
     * Exporte les données filtrées en Excel
     */
    public function exportExcel($id, Request $request)
    {
        // Récupérer les infos du fichier
        $file = DB::table('imported_files')->find($id);
        
        if (!$file) {
            return redirect()->route('semestre1.base')
                ->with('error', 'Fichier non trouvé.');
        }
        
        // Récupérer les en-têtes
        $headerRecord = DB::table('excel_headers')->where('file_id', $id)->first();
        $headers = $headerRecord ? json_decode($headerRecord->headers) : [];
        
        // Récupérer les données
        $rawData = DB::table('excel_data')
            ->where('file_id', $id)
            ->orderBy('row_number')
            ->get()
            ->map(function($item) {
                return [
                    'id' => $item->id,
                    'row_number' => $item->row_number,
                    'data' => json_decode($item->data)
                ];
            });
        
        // Extraction de toutes les données pour manipulation
        $dataArray = [];
        foreach ($rawData as $row) {
            $dataArray[] = [
                'id' => $row['id'],
                'row_number' => $row['row_number'],
                'data' => $row['data']
            ];
        }
        
        // Paramètres de filtrage (repris de viewImportedFile)
        $sortField = $request->input('sort', 'moy');
        $sortDirection = $request->input('direction', 'desc');
        $searchTerm = $request->input('search', '');
        $minMoy = $request->input('min_moy', '');
        $maxMoy = $request->input('max_moy', '');
        
        // Filtrage par recherche (nom, prénom ou IEN)
        if (!empty($searchTerm)) {
            $dataArray = array_filter($dataArray, function($row) use ($searchTerm) {
                $ien = strtolower($row['data'][0] ?? '');
                $prenom = strtolower($row['data'][1] ?? '');
                $nom = strtolower($row['data'][2] ?? '');
                
                $searchLower = strtolower($searchTerm);
                
                return strpos($ien, $searchLower) !== false || 
                       strpos($prenom, $searchLower) !== false || 
                       strpos($nom, $searchLower) !== false;
            });
        }
        
        // Filtrage par plage de moyenne
        if (!empty($minMoy)) {
            $dataArray = array_filter($dataArray, function($row) use ($minMoy) {
                $moy = floatval(str_replace(',', '.', $row['data'][9] ?? 0));
                return $moy >= floatval($minMoy);
            });
        }
        
        if (!empty($maxMoy)) {
            $dataArray = array_filter($dataArray, function($row) use ($maxMoy) {
                $moy = floatval(str_replace(',', '.', $row['data'][9] ?? 0));
                return $moy <= floatval($maxMoy);
            });
        }
        
        // Tri des données
        if ($sortField === 'moy') {
            usort($dataArray, function($a, $b) use ($sortDirection) {
                $moyA = floatval(str_replace(',', '.', $a['data'][9] ?? 0));
                $moyB = floatval(str_replace(',', '.', $b['data'][9] ?? 0));
                
                if ($sortDirection === 'asc') {
                    return $moyA <=> $moyB;
                } else {
                    return $moyB <=> $moyA;
                }
            });
        } elseif ($sortField === 'rang') {
            usort($dataArray, function($a, $b) use ($sortDirection) {
                $rangA = intval($a['data'][10] ?? 0);
                $rangB = intval($b['data'][10] ?? 0);
                
                if ($sortDirection === 'asc') {
                    return $rangA <=> $rangB;
                } else {
                    return $rangB <=> $rangA;
                }
            });
        } elseif ($sortField === 'nom') {
            usort($dataArray, function($a, $b) use ($sortDirection) {
                $nomA = strtolower($a['data'][2] ?? '');
                $nomB = strtolower($b['data'][2] ?? '');
                
                if ($sortDirection === 'asc') {
                    return strcmp($nomA, $nomB);
                } else {
                    return strcmp($nomB, $nomA);
                }
            });
        }
        
        // Préparer les données filtrées pour l'Excel
        $data = array_values($dataArray);
        
        // Créer un nouveau fichier Excel
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Ajouter les en-têtes spécifiés
        $exportHeaders = ['IEN', 'Prenom', 'Nom', 'Sexe', 'Date naissance', 'Lieu naissance', 
                          'Retard', 'Absence', 'C. D.', 'Moy', 'Rang', 'Appréciation'];
        
        // Utiliser setCellValue au lieu de setCellValueByColumnAndRow
        $columns = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L'];
        foreach ($columns as $index => $column) {
            $sheet->setCellValue($column . '1', $exportHeaders[$index]);
        }
        
        // Ajouter les données
        $rowIndex = 2;
        foreach ($data as $row) {
            $colIndices = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 12]; // Indices correspondant aux colonnes spécifiées
            
            foreach ($columns as $index => $column) {
                $colIdx = $colIndices[$index];
                $sheet->setCellValue($column . $rowIndex, $row['data'][$colIdx] ?? '');
            }
            
            $rowIndex++;
        }
        
        // Générer le fichier Excel
        $writer = new Xlsx($spreadsheet);
        $fileName = 'export_' . $file->nom_fichier . '.xlsx';
        $tempFile = tempnam(sys_get_temp_dir(), 'excel');
        $writer->save($tempFile);
        
        return response()->download($tempFile, $fileName)->deleteFileAfterSend(true);
    }

    /**
     * Supprime un fichier importé
     */
    public function deleteImportedFile($id)
    {
        try {
            // Récupérer les infos du fichier
            $file = DB::table('imported_files')->find($id);
            
            if (!$file) {
                return redirect()->route('semestre1.base')
                    ->with('error', 'Fichier non trouvé.');
            }
            
            // Supprimer le fichier physique
            Storage::delete($file->chemin);
            
            // Supprimer les en-têtes associés
            DB::table('excel_headers')->where('file_id', $id)->delete();
            
            // Supprimer les données associées
            DB::table('excel_data')->where('file_id', $id)->delete();
            
            // Supprimer l'entrée dans la table des fichiers
            DB::table('imported_files')->delete($id);
            
            return redirect()->route('semestre1.base')
                ->with('success', 'Fichier supprimé avec succès.');
                
        } catch (\Exception $e) {
            return redirect()->route('semestre1.base')
                ->with('error', 'Erreur lors de la suppression: ' . $e->getMessage());
        }
    }

    /**
     * Détermine la décision du conseil en fonction de la moyenne
     */
    private function getDecisionConseil($moyenne)
    {
        // Convertir en nombre flottant pour s'assurer que la comparaison fonctionne correctement
        $moyenne = floatval(str_replace(',', '.', $moyenne));
        
        if ($moyenne < 3) {
            return "Risque l'exclusion";
        } elseif ($moyenne < 6) {
            return "Risque de Redoubler";
        } elseif ($moyenne < 10) {
            return "Insuffisant";
        } elseif ($moyenne < 14) {
            return "Peut Mieux Faire";
        } elseif ($moyenne < 16) {
            return "Satisfaisant doit continuer";
        } else {
            return "Travail excellent";
        }
    }

    /**
     * Détermine l'appréciation en fonction de la moyenne
     */
    private function getAppreciation($moyenne)
    {
        // Convertir en nombre flottant pour s'assurer que la comparaison fonctionne correctement
        $moyenne = floatval(str_replace(',', '.', $moyenne));
        
        if ($moyenne < 3) {
            return "Blâme";
        } elseif ($moyenne < 6) {
            return "Avertissement";
        } elseif ($moyenne < 8) {
            return "Doit redoubler d'effort";
        } elseif ($moyenne < 12) {
            return "Passable";
        } elseif ($moyenne < 14) {
            return "Tableau d'honneur";
        } elseif ($moyenne < 16) {
            return "Encouragements";
        } else {
            return "Félicitations";
        }
    }
}