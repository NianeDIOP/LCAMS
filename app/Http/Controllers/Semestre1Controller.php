<?php

namespace App\Http\Controllers;

use App\Models\Classroom;
use App\Services\ExcelImportService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class Semestre1Controller extends BaseController
{
    public function __construct()
    {
        // Pas besoin de middleware d'authentification
    }

    /**
     * Affiche la page d'analyse des moyennes du semestre 1
     */
    public function analyseMoyennes()
    {
        return view('semestre1.analyse-moyennes');
    }

    /**
     * Affiche la page d'analyse des disciplines du semestre 1
     */
    public function analyseDisciplines()
    {
        return view('semestre1.analyse-disciplines');
    }

    /**
     * Affiche la page des rapports du semestre 1
     */
    public function rapport()
    {
        return view('semestre1.rapport');
    }
    
    /**
     * Affiche la page d'accueil de l'importation du semestre 1 (étape 1: sélection classe/niveau)
     */
    public function importation()
    {
        return view('semestre1.importation.accueil');
    }
    
    /**
     * Affiche la page d'importation du fichier Excel (étape 2)
     */
    public function importExcel()
    {
        return view('semestre1.importation.import-excel');
    }
    
    /**
     * Traite la redirection après prévisualisation pour afficher la page de visualisation des données
     * Cette méthode reçoit l'ID du fichier et l'ID de la classe depuis la redirection
     */
    public function visualizeExcelData(Request $request)
    {
        $fileId = $request->query('file_id');
        $classroomId = $request->query('classroom_id');
        
        if (!$fileId || !$classroomId) {
            return redirect()->route('semestre1.importation')
                ->with('error', 'Données manquantes pour la visualisation. Veuillez recommencer l\'importation.');
        }
        
        // Stocker ces informations dans la session pour les récupérer dans la vue de visualisation
        session(['excel_file_id' => $fileId, 'classroom_id' => $classroomId]);
        
        // Rediriger vers la vue de visualisation
        return $this->visualisationDonnees();
    }
    
    /**
     * Affiche la page de visualisation des données importées (étape 3)
     */
    public function visualisationDonnees()
    {
        return view('semestre1.importation.visualisation');
    }
    
    /**
     * Affiche la page de validation des données dans la base (étape 4)
     */
    public function validationDonnees()
    {
        return view('semestre1.importation.validation');
    }
    
    /**
     * Importe un fichier Excel dans une classe spécifique
     */
    public function importExcelFile(Request $request, ExcelImportService $importService)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls',
            'classroom_id' => 'required|exists:classrooms,id',
        ]);
        
        try {
            // Récupérer la classe sélectionnée
            $classroom = Classroom::findOrFail($request->classroom_id);
            
            // Stocker temporairement le fichier
            $filePath = $request->file('file')->store('temp');
            $fullPath = Storage::path($filePath);
            
            // Importer les données
            $importResults = $importService->import($fullPath, $classroom->id);
            
            // Supprimer le fichier temporaire
            Storage::delete($filePath);
            
            if (!$importResults['success']) {
                return response()->json([
                    'success' => false,
                    'message' => $importResults['message'],
                ], Response::HTTP_BAD_REQUEST);
            }
            
            return response()->json([
                'success' => true,
                'data' => [
                    'students_count' => $importResults['students_count'],
                    'subject_marks_count' => $importResults['subject_marks_count'],
                    'classroom_name' => $importResults['classroom_name'],
                ],
                'message' => 'Importation réussie',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'importation : ' . $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    
    /**
     * Récupère un aperçu des données du fichier Excel
     */
    public function previewExcelFile(Request $request, ExcelImportService $importService)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls',
        ]);
        
        try {
            // Stocker temporairement le fichier
            $filePath = $request->file('file')->store('temp');
            $fullPath = Storage::path($filePath);
            
            // Générer un ID de session unique pour ce fichier
            $sessionId = session()->getId() . '_' . time();
            
            // Prévisualiser et stocker le fichier pour une utilisation ultérieure
            $previewResult = $importService->previewAndStore($fullPath, $sessionId);
            
            // Supprimer le fichier temporaire initial
            Storage::delete($filePath);
            
            if (!$previewResult['success']) {
                return response()->json([
                    'success' => false,
                    'message' => $previewResult['message'],
                ], Response::HTTP_BAD_REQUEST);
            }
            
            // Stocker l'ID de session dans la session pour pouvoir récupérer le fichier à l'étape suivante
            session(['excel_import_session_id' => $sessionId]);
            
            return response()->json([
                'success' => true,
                'data' => $previewResult['data'],
                'message' => 'Aperçu du fichier généré',
                'session_id' => $sessionId, // Envoyer l'ID de session au frontend
            ]);
        } catch (\Exception $e) {
            // Supprimer le fichier temporaire en cas d'erreur
            if (isset($filePath)) {
                Storage::delete($filePath);
            }
            
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la génération de l\'aperçu : ' . $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Importe un fichier Excel déjà prévisualisé à partir d'une session
     */
    public function importExcelFromSession(Request $request, ExcelImportService $importService)
    {
        $request->validate([
            'session_id' => 'required|string',
            'classroom_id' => 'required|exists:classrooms,id',
        ]);
        
        try {
            // Récupérer le fichier stocké à partir de l'ID de session
            $sessionId = $request->input('session_id');
            $filePath = $importService->getStoredFile($sessionId);
            
            if (!$filePath) {
                return response()->json([
                    'success' => false,
                    'message' => "Le fichier temporaire n'a pas été trouvé. Veuillez réimporter votre fichier.",
                ], Response::HTTP_BAD_REQUEST);
            }
            
            // Récupérer la classe sélectionnée
            $classroom = Classroom::findOrFail($request->classroom_id);
            
            // Importer les données
            $importResults = $importService->import($filePath, $classroom->id);
            
            // Supprimer le fichier temporaire après importation
            $importService->removeStoredFile($sessionId);
            
            if (!$importResults['success']) {
                return response()->json([
                    'success' => false,
                    'message' => $importResults['message'],
                ], Response::HTTP_BAD_REQUEST);
            }
            
            return response()->json([
                'success' => true,
                'data' => [
                    'students_count' => $importResults['students_count'] ?? 0,
                    'subject_marks_count' => $importResults['subject_marks_count'] ?? 0,
                    'classroom_name' => $importResults['classroom_name'] ?? $classroom->name,
                ],
                'message' => 'Importation réussie',
            ]);
        } catch (\Exception $e) {
            // En cas d'erreur, on essaie de supprimer le fichier si l'ID de session est disponible
            if (isset($sessionId)) {
                $importService->removeStoredFile($sessionId);
            }
            
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'importation : ' . $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Importe un fichier Excel directement à partir du formulaire d'importation
     * et retourne un ID de fichier pour la visualisation
     */
    public function importFromSession(Request $request, ExcelImportService $importService)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls',
            'classroom_id' => 'required|exists:classrooms,id',
        ]);
        
        try {
            // Stocker temporairement le fichier
            $filePath = $request->file('file')->store('temp');
            $fullPath = Storage::path($filePath);
            
            // Générer un ID unique pour ce fichier
            $fileId = uniqid('file_', true);
            
            // Stocker le fichier avec l'ID unique
            $storedPath = $importService->storeFile($fullPath, $fileId);
            
            // Récupérer la classe sélectionnée
            $classroom = Classroom::findOrFail($request->classroom_id);
            
            // Stocker les informations de la classe dans la session
            session(['import_classroom_id' => $classroom->id]);
            session(['import_classroom_name' => $classroom->name]);
            session(['import_file_id' => $fileId]);
            
            // Supprimer le fichier temporaire initial
            Storage::delete($filePath);
            
            // Retourner un JSON avec l'ID du fichier pour la redirection
            return response()->json([
                'success' => true,
                'file_id' => $fileId,
                'message' => 'Fichier prêt pour la visualisation',
            ]);
        } catch (\Exception $e) {
            // Supprimer le fichier temporaire en cas d'erreur
            if (isset($filePath)) {
                Storage::delete($filePath);
            }
            
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la préparation du fichier : ' . $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}