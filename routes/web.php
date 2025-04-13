<?php

use App\Http\Controllers\ImportController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\Semestre1Controller;
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

// Routes pour le système d'importation Excel (administration)
Route::prefix('admin/import')->name('admin.import.')->group(function () {
    Route::get('/', [ImportController::class, 'index'])->name('index');
    Route::post('/', [ImportController::class, 'store'])->name('store');
    Route::get('/history', [ImportController::class, 'history'])->name('history');
    Route::get('/{id}', [ImportController::class, 'show'])->name('show');
    Route::delete('/{id}', [ImportController::class, 'destroy'])->name('destroy');
});

// Routes pour le Semestre 1
Route::prefix('semestre1')->group(function () {
    // Page par défaut du semestre 1 - redirige vers l'analyse des moyennes
    Route::get('/', function () {
        return redirect()->route('semestre1.analyse-moyennes');
    });
    
    Route::get('/analyse-moyennes', [Semestre1Controller::class, 'analyseMoyennes'])->name('semestre1.analyse-moyennes');
    Route::get('/analyse-disciplines', [Semestre1Controller::class, 'analyseDisciplines'])->name('semestre1.analyse-disciplines');
    
    Route::get('/importation', [Semestre1Controller::class, 'importation'])->name('semestre1.importation');
    Route::post('/importation/moyennes', [Semestre1Controller::class, 'importMoyennes'])->name('semestre1.import-moyennes');
    Route::post('/importation/discipline', [Semestre1Controller::class, 'importDiscipline'])->name('semestre1.import-discipline');
    Route::get('/importation/{id}', [Semestre1Controller::class, 'showImport'])->name('semestre1.show-import');
    Route::delete('/importation/{id}', [Semestre1Controller::class, 'deleteImport'])->name('semestre1.delete-import');
    
    Route::get('/rapport', [Semestre1Controller::class, 'rapport'])->name('semestre1.rapport');
});
