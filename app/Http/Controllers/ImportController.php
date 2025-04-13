<?php

namespace App\Http\Controllers;

use App\Models\GradeLevel;
use App\Models\Classroom;
use App\Models\ImportHistory;
use App\Services\ExcelImportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ImportController extends Controller
{
    protected $importService;

    public function __construct(ExcelImportService $importService)
    {
        $this->importService = $importService;
        $this->middleware('auth');
    }

    /**
     * Affiche la page d'importation
     */
    public function index()
    {
        $gradeLevels = GradeLevel::where('active', true)->orderBy('order')->get();
        $importHistory = ImportHistory::with('gradeLevel', 'user')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('admin.import.index', compact('gradeLevels', 'importHistory'));
    }

    /**
     * Traite l'importation du fichier Excel
     */
    public function store(Request $request)
    {
        // Validation des données
        $validator = Validator::make($request->all(), [
            'excel_file' => 'required|file|mimes:xlsx,xls|max:10240',
            'grade_level_id' => 'required|exists:grade_levels,id',
        ], [
            'excel_file.required' => 'Veuillez sélectionner un fichier Excel à importer.',
            'excel_file.file' => 'Le fichier sélectionné n\'est pas valide.',
            'excel_file.mimes' => 'Le fichier doit être au format Excel (.xlsx ou .xls).',
            'excel_file.max' => 'La taille du fichier ne doit pas dépasser 10 Mo.',
            'grade_level_id.required' => 'Veuillez sélectionner un niveau scolaire.',
            'grade_level_id.exists' => 'Le niveau scolaire sélectionné n\'existe pas.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Récupération du fichier uploadé
        $file = $request->file('excel_file');
        $userId = Auth::id();

        // Importation du fichier
        $result = $this->importService->importCompleteExcelFile(
            $file->getPathname(),
            $request->grade_level_id,
            $userId
        );

        if ($result['status'] === 'success') {
            return redirect()->route('admin.import.show', $result['import_id'])
                ->with('success', 'Le fichier a été importé avec succès.');
        } else {
            return redirect()->back()
                ->with('error', 'Une erreur est survenue lors de l\'importation : ' . $result['message'])
                ->withInput();
        }
    }

    /**
     * Affiche les détails d'une importation
     */
    public function show($id)
    {
        $import = ImportHistory::with('gradeLevel', 'user')->findOrFail($id);
        $details = json_decode($import->details);

        return view('admin.import.show', compact('import', 'details'));
    }

    /**
     * Affiche l'historique des importations
     */
    public function history()
    {
        $imports = ImportHistory::with('gradeLevel', 'user')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.import.history', compact('imports'));
    }

    /**
     * Supprime une importation et ses données associées
     */
    public function destroy($id)
    {
        $import = ImportHistory::findOrFail($id);
        
        // Vérifier si l'importation est en cours
        if ($import->status === 'en_cours') {
            return redirect()->back()
                ->with('error', 'Impossible de supprimer une importation en cours.');
        }
        
        // Récupérer le niveau et ses classes
        $gradeLevelId = $import->grade_level_id;
        $classroomIds = Classroom::where('grade_level_id', $gradeLevelId)->pluck('id')->toArray();
        
        // Supprimer les données associées à ce niveau (à adapter selon vos besoins)
        // Cette opération doit être effectuée avec prudence car elle supprime des données
        // Vous devriez implémenter une logique de vérification supplémentaire

        try {
            \DB::beginTransaction();
            
            // Supprimer le fichier importé
            if (Storage::disk('local')->exists($import->file_path)) {
                Storage::disk('local')->delete($import->file_path);
            }
            
            // Supprimer l'enregistrement d'importation
            $import->delete();
            
            \DB::commit();
            
            return redirect()->route('admin.import.history')
                ->with('success', 'L\'importation a été supprimée avec succès.');
        } catch (\Exception $e) {
            \DB::rollBack();
            
            return redirect()->back()
                ->with('error', 'Une erreur est survenue lors de la suppression : ' . $e->getMessage());
        }
    }
}