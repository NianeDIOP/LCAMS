<?php

use App\Http\Controllers\SettingsController;
use App\Http\Controllers\Semestre1Controller;
use App\Models\Classroom;
use App\Models\GradeLevel;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Routes pour les paramètres
Route::prefix('settings')->group(function () {
    // Paramètres généraux
    Route::get('/', [SettingsController::class, 'index'])->name('settings.index');
    Route::post('/general', [SettingsController::class, 'saveGeneralSettings'])->name('settings.save_general');
    
    // Niveaux scolaires
    Route::get('/grade-levels', [SettingsController::class, 'gradeLevels'])->name('settings.grade_levels');
    Route::post('/grade-levels', [SettingsController::class, 'saveGradeLevel'])->name('settings.save_grade_level');
    Route::put('/grade-levels/{id}', [SettingsController::class, 'updateGradeLevel'])->name('settings.update_grade_level');
    Route::delete('/grade-levels/{id}', [SettingsController::class, 'deleteGradeLevel'])->name('settings.delete_grade_level');
    
    // Classes
    Route::get('/grade-levels/{id}/classrooms', [SettingsController::class, 'classrooms'])->name('settings.classrooms');
    Route::post('/grade-levels/{id}/classrooms', [SettingsController::class, 'saveClassroom'])->name('settings.save_classroom');
    Route::put('/classrooms/{id}', [SettingsController::class, 'updateClassroom'])->name('settings.update_classroom');
    Route::delete('/classrooms/{id}', [SettingsController::class, 'deleteClassroom'])->name('settings.delete_classroom');
});

// Routes pour le Semestre 1
Route::prefix('semestre1')->group(function () {
    // Page par défaut du semestre 1 - redirige vers l'analyse des moyennes
    Route::get('/', function () {
        return redirect()->route('semestre1.analyse-moyennes');
    })->name('semestre1.index');
    
    Route::get('/analyse-moyennes', [Semestre1Controller::class, 'analyseMoyennes'])->name('semestre1.analyse-moyennes');
    Route::get('/analyse-disciplines', [Semestre1Controller::class, 'analyseDisciplines'])->name('semestre1.analyse-disciplines');
    
    Route::get('/rapport', [Semestre1Controller::class, 'rapport'])->name('semestre1.rapport');
    
    // Routes pour l'importation des données (étapes 1-4)
    Route::get('/importation', [Semestre1Controller::class, 'importation'])->name('semestre1.importation');
    Route::get('/importation/excel', [Semestre1Controller::class, 'importExcel'])->name('semestre1.importation.excel');
    Route::get('/importation/visualisation', [Semestre1Controller::class, 'visualisationDonnees'])->name('semestre1.importation.visualisation');
    // Alias route for compatibility with JS code
    Route::get('/importation/visualize', [Semestre1Controller::class, 'visualisationDonnees'])->name('semestre1.importation.visualize');
    Route::get('/importation/validation', [Semestre1Controller::class, 'validationDonnees'])->name('semestre1.importation.validation');
    
    // Routes API pour l'importation des fichiers Excel
    Route::post('/importation/preview', [Semestre1Controller::class, 'previewExcelFile'])->name('semestre1.importation.preview');
    Route::post('/importation/import', [Semestre1Controller::class, 'importExcelFile'])->name('semestre1.importation.import');
    Route::post('/importation/import-from-session', [Semestre1Controller::class, 'importFromSession'])->name('semestre1.importation.importFromSession');
});

// API Routes
Route::prefix('api')->group(function() {
    // API pour récupérer les niveaux scolaires actifs
    Route::get('/grade-levels', function() {
        return GradeLevel::where('active', true)
                        ->orderBy('display_order')
                        ->get();
    });
    
    // API pour récupérer les classes par niveau scolaire
    Route::get('/classrooms/{gradeId}', function($gradeId) {
        return Classroom::where('grade_level_id', $gradeId)
                        ->where('active', true)
                        ->orderBy('name')
                        ->get();
    });
});
