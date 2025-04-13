<?php

namespace App\Http\Controllers;

use App\Models\Classroom;
use App\Models\GradeLevel;
use App\Models\ImportHistory;
use App\Models\Student;
use App\Services\ExcelImportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ImportController extends Controller
{
    protected $importService;
    
    public function __construct(ExcelImportService $importService)
    {
        $this->importService = $importService;
    }

    /**
     * Affiche la page d'importation Excel
     */
    public function index()
    {
        $gradeLevels = GradeLevel::where('active', true)->orderBy('order')->get();
        $importHistory = ImportHistory::with(['user', 'gradeLevel'])
                                     ->latest()
                                     ->limit(10)
                                     ->get();

        return view('admin.import', compact('gradeLevels', 'importHistory'));
    }

    /**
     * Récupère les classes pour un niveau scolaire donné
     */
    public function getClassrooms(Request $request)
    {
        $gradeLevelId = $request->input('grade_level_id');
        $classrooms = Classroom::where('grade_level_id', $gradeLevelId)
                             ->where('active', true)
                             ->orderBy('name')
                             ->get();
                             
        return response()->json($classrooms);
    }

    /**
     * Traite l'importation d'un fichier Excel
     */
    public function importExcel(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'excel_file' => 'required|file|mimes:xlsx,xls|max:10240',
            'grade_level_id' => 'required|exists:grade_levels,id',
            'classroom_id' => 'nullable|exists:classrooms,id',
            'import_method' => 'required|in:standard,advanced',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation échouée',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            // Création d'un enregistrement d'importation
            $importHistory = new ImportHistory();
            $importHistory->user_id = Auth::id();
            $importHistory->grade_level_id = $request->grade_level_id;
            $importHistory->classroom_id = $request->classroom_id;
            $importHistory->file_name = $request->file('excel_file')->getClientOriginalName();
            $importHistory->status = 'en_cours';
            $importHistory->method = $request->import_method;
            $importHistory->save();

            // Traitement de l'importation
            $filePath = $request->file('excel_file')->path();
            $useAdvancedMethod = ($request->import_method === 'advanced');

            $result = $this->importService->import(
                $filePath, 
                $request->grade_level_id, 
                $request->classroom_id,
                $useAdvancedMethod
            );

            // Mise à jour de l'historique d'importation
            $importHistory->status = 'terminé';
            $importHistory->results = json_encode([
                'total_students' => $result['total_students'] ?? 0,
                'new_students' => $result['new_students'] ?? 0,
                'updated_students' => $result['updated_students'] ?? 0,
                'subjects_imported' => $result['subjects_imported'] ?? 0,
                'errors' => $result['errors'] ?? [],
            ]);
            $importHistory->save();

            return response()->json([
                'message' => 'Importation réussie',
                'stats' => [
                    'total_students' => $result['total_students'] ?? 0,
                    'new_students' => $result['new_students'] ?? 0,
                    'updated_students' => $result['updated_students'] ?? 0,
                    'subjects_imported' => $result['subjects_imported'] ?? 0,
                    'errors' => count($result['errors'] ?? []),
                ],
                'import_id' => $importHistory->id,
            ]);

        } catch (\Exception $e) {
            // Enregistrement de l'erreur
            Log::error('Erreur lors de l\'importation : ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);
            
            // Mise à jour de l'historique d'importation en cas d'échec
            if (isset($importHistory)) {
                $importHistory->status = 'échoué';
                $importHistory->results = json_encode([
                    'error' => $e->getMessage(),
                    'error_code' => $e->getCode(),
                ]);
                $importHistory->save();
            }

            return response()->json([
                'message' => 'Erreur lors de l\'importation : ' . $e->getMessage(),
                'error_code' => $e->getCode(),
            ], 500);
        }
    }

    /**
     * Affiche les détails d'une importation
     */
    public function showImportDetails($id)
    {
        $import = ImportHistory::with(['user', 'gradeLevel', 'classroom'])->findOrFail($id);
        $results = json_decode($import->results, true) ?? [];
        
        // Si l'importation a réussi, rechercher les étudiants concernés
        $students = collect();
        if ($import->status === 'terminé' && isset($results['total_students'])) {
            $query = Student::with(['classroom', 'semester1Average'])
                         ->where('grade_level_id', $import->grade_level_id);
                         
            if ($import->classroom_id) {
                $query->where('classroom_id', $import->classroom_id);
            }
            
            $students = $query->orderBy('last_name')->get();
        }
        
        return view('admin.import_details', compact('import', 'results', 'students'));
    }

    /**
     * Supprime une importation et éventuellement les données associées
     */
    public function deleteImport(Request $request, $id)
    {
        try {
            $import = ImportHistory::findOrFail($id);
            
            // Option pour supprimer également les données importées
            $deleteAssociatedData = $request->input('delete_data', false);
            
            if ($deleteAssociatedData) {
                // Supprimer les notes et moyennes associées à cette importation
                // Cette logique dépendra de votre modèle de données
                // ...
            }
            
            // Supprimer l'enregistrement d'historique
            $import->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Importation supprimée avec succès'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Erreur lors de la suppression de l\'importation : ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la suppression de l\'importation'
            ], 500);
        }
    }
}