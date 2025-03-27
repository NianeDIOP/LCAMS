<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Semestre1Controller;
use App\Http\Controllers\Semestre2Controller;
use App\Http\Controllers\GeneralController;
use App\Http\Controllers\ParametresController;

// Page d'accueil
Route::get('/', [HomeController::class, 'index'])->name('home');

// Routes pour le Semestre 1
Route::prefix('semestre1')->group(function () {
    Route::get('/', [Semestre1Controller::class, 'index'])->name('semestre1.index');
    Route::get('/tableau-de-bord', [Semestre1Controller::class, 'dashboard'])->name('semestre1.dashboard');
    Route::get('/analyse-disciplines', [Semestre1Controller::class, 'analyseDisciples'])->name('semestre1.analyse');
    Route::get('/rapports', [Semestre1Controller::class, 'rapports'])->name('semestre1.rapports');
    Route::get('/base-moyennes', [Semestre1Controller::class, 'baseMoyennes'])->name('semestre1.base');
    Route::post('/importer', [Semestre1Controller::class, 'importer'])->name('semestre1.importer');
    Route::get('/base/view/{id}', [Semestre1Controller::class, 'viewImportedFile'])->name('semestre1.viewImportedFile');
    Route::delete('/base/delete/{id}', [Semestre1Controller::class, 'deleteImportedFile'])->name('semestre1.deleteImportedFile');
    Route::get('/base/view/{id}/export-pdf', [Semestre1Controller::class, 'exportPDF'])->name('semestre1.exportPDF');
    Route::get('/base/view/{id}/export-excel', [Semestre1Controller::class, 'exportExcel'])->name('semestre1.exportExcel');
    Route::get('/classes-by-niveau/{niveau_id}', [Semestre1Controller::class, 'getClassesByNiveau'])->name('semestre1.getClassesByNiveau');
    // Dans le groupe prefix('semestre1')
    Route::get('/base/view/{id}/detailed', [Semestre1Controller::class, 'viewDetailedData'])->name('semestre1.viewDetailedData');
});

// Routes pour le Semestre 2
Route::prefix('semestre2')->group(function () {
    Route::get('/', [Semestre2Controller::class, 'index'])->name('semestre2.index');
    Route::get('/tableau-de-bord', [Semestre2Controller::class, 'dashboard'])->name('semestre2.dashboard');
    Route::get('/analyse-disciplines', [Semestre2Controller::class, 'analyseDisciples'])->name('semestre2.analyse');
    Route::get('/rapports', [Semestre2Controller::class, 'rapports'])->name('semestre2.rapports');
    Route::get('/base-moyennes', [Semestre2Controller::class, 'baseMoyennes'])->name('semestre2.base');
    Route::post('/importer', [Semestre2Controller::class, 'importer'])->name('semestre2.importer');
});

// Routes pour la section Générale
Route::prefix('general')->group(function () {
    Route::get('/', [GeneralController::class, 'index'])->name('general.index');
    Route::get('/tableau-de-bord', [GeneralController::class, 'dashboard'])->name('general.dashboard');
    Route::get('/analyses', [GeneralController::class, 'analyses'])->name('general.analyses');
    Route::get('/rapports', [GeneralController::class, 'rapports'])->name('general.rapports');
});

// Routes pour les Paramètres
Route::prefix('parametres')->group(function () {
    Route::get('/', [ParametresController::class, 'index'])->name('parametres.index');
    Route::post('/etablissement', [ParametresController::class, 'saveEtablissement'])->name('parametres.etablissement');
    
    // Routes pour les niveaux
    Route::get('/niveaux', [ParametresController::class, 'niveaux'])->name('parametres.niveaux');
    Route::post('/niveaux', [ParametresController::class, 'saveNiveau'])->name('parametres.saveNiveau');
    Route::delete('/niveaux/delete/{id}', [ParametresController::class, 'deleteNiveau'])->name('parametres.deleteNiveau');
    
    // Routes pour les classes
    Route::get('/classes', [ParametresController::class, 'classes'])->name('parametres.classes');
    Route::post('/classes', [ParametresController::class, 'saveClasse'])->name('parametres.saveClasse');
    Route::delete('/classes/delete/{id}', [ParametresController::class, 'deleteClasse'])->name('parametres.deleteClasse');
    
    // Routes pour l'année scolaire
    Route::get('/annee-scolaire', [ParametresController::class, 'anneeScolaire'])->name('parametres.annee');
    Route::post('/annee-scolaire', [ParametresController::class, 'saveAnneeScolaire'])->name('parametres.saveAnneeScolaire');
});