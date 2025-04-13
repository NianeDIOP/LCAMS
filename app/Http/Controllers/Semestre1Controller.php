<?php

namespace App\Http\Controllers;

use App\Models\GradeLevel;
use App\Models\ImportHistory;
use App\Services\ExcelImportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Routing\Controller as BaseController;

class Semestre1Controller extends BaseController
{
    protected $importService;

    public function __construct(ExcelImportService $importService)
    {
        $this->importService = $importService;
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
     * Affiche la page d'importation des données du semestre 1
     */
    public function importation()
    {
        $gradeLevels = GradeLevel::where('active', true)->orderBy('order')->get();
        $importHistory = ImportHistory::with('gradeLevel')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('semestre1.importation', compact('gradeLevels', 'importHistory'));
    }

    /**
     * Traite l'importation des moyennes générales du semestre 1
     */
    public function importMoyennes(Request $request)
    {
        // Validation des données
        $validator = Validator::make($request->all(), [
            'excel_file' => 'required|file|mimes:xlsx,xls|max:10240',
            'grade_level_id' => 'required|exists:grade_levels,id',
            'classroom_id' => 'nullable|exists:classrooms,id',
        ], [
            'excel_file.required' => 'Veuillez sélectionner un fichier Excel à importer.',
            'excel_file.file' => 'Le fichier sélectionné n\'est pas valide.',
            'excel_file.mimes' => 'Le fichier doit être au format Excel (.xlsx ou .xls).',
            'excel_file.max' => 'La taille du fichier ne doit pas dépasser 10 Mo.',
            'grade_level_id.required' => 'Veuillez sélectionner un niveau scolaire.',
            'grade_level_id.exists' => 'Le niveau scolaire sélectionné n\'existe pas.',
            'classroom_id.exists' => 'La classe sélectionnée n\'existe pas.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Récupération du fichier uploadé
        $file = $request->file('excel_file');
        
        // Importation du fichier
        $result = $this->importService->importCompleteExcelFile(
            $file->getPathname(),
            $request->grade_level_id,
            null, // ID utilisateur (null car pas d'authentification)
            $request->classroom_id // ID de la classe (optionnel)
        );

        if ($result['status'] === 'success') {
            return redirect()->route('semestre1.show-import', $result['import_id'])
                ->with('success', 'Le fichier a été importé avec succès. Consultez les détails ci-dessous.');
        } else {
            return redirect()->back()
                ->with('error', 'Une erreur est survenue lors de l\'importation : ' . $result['message'])
                ->withInput();
        }
    }

    /**
     * Traite l'importation des notes par discipline du semestre 1
     */
    public function importDiscipline(Request $request)
    {
        // Cette méthode pourrait être utilisée si vous souhaitez séparer l'importation des moyennes générales
        // et l'importation des notes par discipline
        return redirect()->route('semestre1.importation')
            ->with('info', 'Cette fonctionnalité est intégrée dans l\'importation des moyennes.');
    }

    /**
     * Affiche les détails d'une importation
     */
    public function showImport($id)
    {
        $import = ImportHistory::with('gradeLevel')->findOrFail($id);
        $details = json_decode($import->details);

        // Récupérer les étudiants importés pour cet import (basé sur le grade_level_id)
        $studentsData = null;
        if ($import->status === 'terminé' && $import->grade_level_id) {
            // Récupérer les élèves du niveau avec leurs relations
            $studentsData = \App\Models\Student::with(['classroom', 'semester1Average'])
                ->whereHas('classroom', function ($query) use ($import) {
                    $query->where('grade_level_id', $import->grade_level_id);
                })
                ->orderBy('classroom_id')
                ->orderBy('nom')
                ->orderBy('prenom')
                ->paginate(50); // Pagination pour ne pas surcharger la page
        }

        return view('semestre1.importation-details', compact('import', 'details', 'studentsData'));
    }

    /**
     * Supprime une importation
     */
    public function deleteImport($id)
    {
        $import = ImportHistory::findOrFail($id);
        
        // Utiliser le service pour effectuer la suppression
        $result = $this->importService->deleteImportData($id);
        
        if ($result === true) {
            return redirect()->route('semestre1.importation')
                ->with('success', 'L\'importation a été supprimée avec succès.');
        } else {
            return redirect()->back()
                ->with('error', $result);
        }
    }

    /**
     * Affiche la page des rapports du semestre 1
     */
    public function rapport()
    {
        return view('semestre1.rapport');
    }
}