<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ParametresController;
use App\Http\Controllers\Semestre1Controller;
use App\Http\Controllers\Semestre2Controller;
use App\Http\Controllers\GeneralController;
use App\Http\Controllers\ImportationController;
use App\Http\Controllers\DataManagementController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Page d'accueil
Route::get('/', [HomeController::class, 'index'])->name('home');

// Routes pour les paramètres
Route::get('/parametres', [ParametresController::class, 'index'])->name('parametres.index');
Route::post('/parametres/configuration', [ParametresController::class, 'saveConfiguration'])->name('parametres.configuration');
Route::post('/parametres/annee-scolaire', [ParametresController::class, 'saveAnneeScolaire'])->name('parametres.annee-scolaire');
Route::put('/parametres/annee-scolaire/{id}', [ParametresController::class, 'updateAnneeScolaire'])->name('parametres.update-annee-scolaire');
Route::post('/parametres/niveau', [ParametresController::class, 'saveNiveau'])->name('parametres.niveau');
Route::put('/parametres/niveau/{id}', [ParametresController::class, 'updateNiveau'])->name('parametres.update-niveau');
Route::get('/parametres/classes/{niveau_id}', [ParametresController::class, 'getClasses'])->name('parametres.classes');
Route::post('/parametres/classe', [ParametresController::class, 'saveClasse'])->name('parametres.classe');
Route::put('/parametres/classe/{id}', [ParametresController::class, 'updateClasse'])->name('parametres.update-classe');

// Routes pour le Semestre 1
Route::get('/semestre1', [Semestre1Controller::class, 'index'])->name('semestre1.index');
Route::get('/semestre1/analyse-moyennes', [Semestre1Controller::class, 'analyseMoyennes'])->name('semestre1.analyse-moyennes');
Route::get('/semestre1/moyennes-classe/{classe_id}', [Semestre1Controller::class, 'getMoyennesClasse'])->name('semestre1.moyennes-classe');
Route::get('/semestre1/analyse-disciplines', [Semestre1Controller::class, 'analyseDisciplines'])->name('semestre1.analyse-disciplines');
Route::get('/semestre1/disciplines-classe', [Semestre1Controller::class, 'getDisciplinesClasse'])->name('semestre1.disciplines-classe');
Route::get('/semestre1/rapports', [Semestre1Controller::class, 'rapports'])->name('semestre1.rapports');
Route::get('/semestre1/rapport-classe/{classe_id}', [Semestre1Controller::class, 'genererRapportClasse'])->name('semestre1.rapport-classe');

// Routes pour l'importation du Semestre 1
Route::get('/importation/semestre1', [ImportationController::class, 'indexS1'])->name('importation.s1');
Route::post('/importation/semestre1', [ImportationController::class, 'importerS1'])->name('importation.importer-s1');

// Routes pour le Semestre 2
Route::get('/semestre2', [Semestre2Controller::class, 'index'])->name('semestre2.index');
Route::get('/semestre2/analyse-moyennes', [Semestre2Controller::class, 'analyseMoyennes'])->name('semestre2.analyse-moyennes');
Route::get('/semestre2/moyennes-classe/{classe_id}', [Semestre2Controller::class, 'getMoyennesClasse'])->name('semestre2.moyennes-classe');
Route::get('/semestre2/analyse-disciplines', [Semestre2Controller::class, 'analyseDisciplines'])->name('semestre2.analyse-disciplines');
Route::get('/semestre2/disciplines-classe', [Semestre2Controller::class, 'getDisciplinesClasse'])->name('semestre2.disciplines-classe');
Route::get('/semestre2/rapports', [Semestre2Controller::class, 'rapports'])->name('semestre2.rapports');
Route::get('/semestre2/rapport-classe/{classe_id}', [Semestre2Controller::class, 'genererRapportClasse'])->name('semestre2.rapport-classe');

// Routes pour l'importation du Semestre 2
Route::get('/importation/semestre2', [ImportationController::class, 'indexS2'])->name('importation.s2');
Route::post('/importation/semestre2', [ImportationController::class, 'importerS2'])->name('importation.importer-s2');
Route::get('/importation/preview/{id}', [ImportationController::class, 'showImport'])->name('importation.show');

// Routes pour le module Général
Route::get('/general', [GeneralController::class, 'index'])->name('general.index');
Route::get('/general/analyse-moyennes', [GeneralController::class, 'analyseMoyennes'])->name('general.analyse-moyennes');
Route::get('/general/moyennes-classe/{classe_id}', [GeneralController::class, 'getMoyennesClasse'])->name('general.moyennes-classe');
Route::get('/general/analyse-disciplines', [GeneralController::class, 'analyseDisciplines'])->name('general.analyse-disciplines');
Route::get('/general/disciplines-classe', [GeneralController::class, 'getDisciplinesClasse'])->name('general.disciplines-classe');
Route::get('/general/decisions', [GeneralController::class, 'decisions'])->name('general.decisions');
Route::get('/general/decisions-classe/{classe_id}', [GeneralController::class, 'getDecisionsClasse'])->name('general.decisions-classe');
Route::post('/general/decisions', [GeneralController::class, 'saveDecisions'])->name('general.save-decisions');
Route::get('/general/rapports', [GeneralController::class, 'rapports'])->name('general.rapports');
Route::get('/general/rapport-classe/{classe_id}', [GeneralController::class, 'genererRapportClasse'])->name('general.rapport-classe');

// Route AJAX pour récupérer les classes d'un niveau
Route::get('/api/niveaux/{niveau_id}/classes', [ImportationController::class, 'getClasses'])->name('api.classes');

// Routes pour la gestion des données
Route::get('/data-management', [DataManagementController::class, 'index'])->name('data.management');
Route::post('/data-management/clear-all', [DataManagementController::class, 'clearAllData'])->name('data.clear-all');
Route::post('/data-management/clear-class', [DataManagementController::class, 'clearClassData'])->name('data.clear-class');
Route::post('/data-management/clear-semester', [DataManagementController::class, 'clearSemesterData'])->name('data.clear-semester');