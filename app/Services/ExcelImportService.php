<?php

namespace App\Services;

use App\Models\Student;
use App\Models\Classroom;
use App\Models\GradeLevel;
use App\Models\Semester1Average;
use App\Models\ImportHistory;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Exception as SpreadsheetException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;
use Illuminate\Support\Facades\Storage;

class ExcelImportService
{
    /**
     * Erreurs d'importation spécifiques
     */
    const ERROR_INVALID_FILE = 1000;
    const ERROR_MISSING_SHEETS = 1001;
    const ERROR_INVALID_STRUCTURE = 1002;
    const ERROR_NO_DATA = 1003;
    const ERROR_NO_CLASSROOMS = 1004;
    
    /**
     * Importe les données de l'onglet "Moyennes eleves" d'un fichier Excel
     * 
     * @param string $filePath Chemin vers le fichier Excel téléchargé
     * @param int $gradeLevelId ID du niveau scolaire concerné
     * @param int $userId ID de l'utilisateur qui réalise l'importation
     * @param int|null $classroomId ID de la classe spécifique à importer (optionnel)
     * @return array Statistiques sur l'importation
     */
    public function importCompleteExcelFile(string $filePath, int $gradeLevelId, int $userId = null, int $classroomId = null)
    {
        try {
            // Vérification du fichier Excel avant tout traitement
            $this->validateExcelFile($filePath);
            
            // Enregistrer une copie du fichier pour référence future
            $storagePath = $this->storeFile($filePath, $gradeLevelId);

            // Chargement du fichier Excel
            $inputFileType = IOFactory::identify($filePath);
            $reader = IOFactory::createReader($inputFileType);
            $reader->setLoadSheetsOnly(['Moyennes eleves']);
            $spreadsheet = $reader->load($filePath);
            
            // Vérifier que le fichier contient bien l'onglet nécessaire
            $sheetNames = $spreadsheet->getSheetNames();
            if (!in_array('Moyennes eleves', $sheetNames)) {
                throw new Exception('Le fichier Excel doit contenir l\'onglet "Moyennes eleves".', self::ERROR_MISSING_SHEETS);
            }

            // Récupération des classes du niveau concerné
            $classrooms = Classroom::where('grade_level_id', $gradeLevelId)
                ->where('active', true);

            // Si une classe spécifique est demandée, filtrer uniquement pour cette classe
            if ($classroomId) {
                $classrooms = $classrooms->where('id', $classroomId);
            }
            
            // Obtenir le tableau des classes
            $classrooms = $classrooms->pluck('id', 'name')->toArray();

            if (empty($classrooms)) {
                throw new Exception('Aucune classe active trouvée pour ce niveau. Veuillez d\'abord configurer les classes dans les paramètres.', self::ERROR_NO_CLASSROOMS);
            }

            // Démarrer une transaction pour assurer l'intégrité des données
            DB::beginTransaction();

            // Créer un enregistrement d'historique pour cette importation
            $importHistory = ImportHistory::create([
                'grade_level_id' => $gradeLevelId,
                'user_id' => $userId,
                'file_path' => $storagePath,
                'status' => 'en_cours',
                'details' => json_encode([
                    'début' => now()->toDateTimeString(),
                    'niveau' => GradeLevel::find($gradeLevelId)->name,
                    'classe_specifique' => $classroomId ? Classroom::find($classroomId)->name : null
                ])
            ]);

            // Importer les moyennes générales (onglet "Moyennes eleves")
            $averageStats = $this->processAveragesSheet($spreadsheet, $classrooms, $gradeLevelId);

            // Mettre à jour l'historique avec les résultats
            $importHistory->update([
                'status' => 'terminé',
                'details' => json_encode([
                    'début' => json_decode($importHistory->details)->début,
                    'fin' => now()->toDateTimeString(),
                    'niveau' => GradeLevel::find($gradeLevelId)->name,
                    'classe_specifique' => $classroomId ? Classroom::find($classroomId)->name : null,
                    'statistiques' => [
                        'total_students' => $averageStats['imported'] + $averageStats['updated'],
                        'new_students' => $averageStats['imported'],
                        'updated_students' => $averageStats['updated'],
                        'errors' => $averageStats['errors']
                    ]
                ])
            ]);

            // Valider la transaction
            DB::commit();

            return [
                'status' => 'success',
                'stats' => [
                    'total_students' => $averageStats['imported'] + $averageStats['updated'],
                    'new_students' => $averageStats['imported'],
                    'updated_students' => $averageStats['updated'],
                    'errors' => $averageStats['errors']
                ],
                'import_id' => $importHistory->id
            ];

        } catch (Exception $e) {
            // Rollback de la transaction en cas d'erreur
            if (DB::transactionLevel() > 0) {
                DB::rollBack();
            }
            
            // Enregistrer l'erreur dans l'historique si l'enregistrement existe
            if (isset($importHistory)) {
                $importHistory->update([
                    'status' => 'échoué',
                    'details' => json_encode([
                        'début' => json_decode($importHistory->details)->début,
                        'fin' => now()->toDateTimeString(),
                        'erreur' => $e->getMessage(),
                        'code_erreur' => $e->getCode(),
                        'trace' => $e->getTraceAsString()
                    ])
                ]);
            }
            
            // Enregistrer l'erreur dans les logs pour le débogage
            Log::error('Erreur lors de l\'importation du fichier Excel', [
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'trace' => $e->getTraceAsString(),
                'file_path' => $filePath,
                'grade_level_id' => $gradeLevelId
            ]);
            
            // Définir un message d'erreur convivial en fonction du code d'erreur
            $errorMessage = $this->getFriendlyErrorMessage($e);
            
            return [
                'status' => 'error',
                'message' => $errorMessage,
                'error_code' => $e->getCode(),
                'import_id' => $importHistory->id ?? null
            ];
        }
    }

    /**
     * Valide un fichier Excel avant importation
     * 
     * @param string $filePath Chemin vers le fichier
     * @throws Exception Si le fichier est invalide
     * @return bool
     */
    private function validateExcelFile(string $filePath)
    {
        // Vérifications de base du fichier
        if (!file_exists($filePath)) {
            throw new Exception('Le fichier Excel n\'existe pas', self::ERROR_INVALID_FILE);
        }

        if (filesize($filePath) <= 0) {
            throw new Exception('Le fichier Excel est vide', self::ERROR_INVALID_FILE);
        }

        $fileExtension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
        if (!in_array($fileExtension, ['xlsx', 'xls'])) {
            throw new Exception('Le fichier doit être au format Excel (.xlsx ou .xls)', self::ERROR_INVALID_FILE);
        }

        try {
            // Déterminer le type de fichier de manière plus robuste
            $inputFileType = IOFactory::identify($filePath);
            
            // Créer le lecteur approprié
            $reader = IOFactory::createReader($inputFileType);
            $reader->setReadDataOnly(true);
            
            // Vérifier si le fichier contient l'onglet requis
            $worksheetData = $reader->listWorksheetInfo($filePath);
            $hasRequiredSheet = false;
            
            foreach ($worksheetData as $worksheet) {
                if ($worksheet['worksheetName'] === 'Moyennes eleves') {
                    $hasRequiredSheet = true;
                    
                    // Vérifier que la feuille contient suffisamment de lignes
                    if ($worksheet['totalRows'] < 12) {
                        throw new Exception('L\'onglet "Moyennes eleves" ne contient pas suffisamment de données (moins de 12 lignes)', self::ERROR_NO_DATA);
                    }
                    
                    // Vérifier que la feuille contient suffisamment de colonnes (au moins jusqu'à H)
                    if ($worksheet['totalColumns'] < 8) {
                        throw new Exception('L\'onglet "Moyennes eleves" ne contient pas suffisamment de colonnes (au moins 8 colonnes requises)', self::ERROR_INVALID_STRUCTURE);
                    }
                    
                    break;
                }
            }
            
            if (!$hasRequiredSheet) {
                throw new Exception('Le fichier Excel doit contenir l\'onglet "Moyennes eleves"', self::ERROR_MISSING_SHEETS);
            }
            
            // Maintenant chargeons l'onglet pour vérifier la structure des en-têtes
            $reader->setLoadSheetsOnly(['Moyennes eleves']);
            $spreadsheet = $reader->load($filePath);
            $worksheet = $spreadsheet->getActiveSheet();
            
            // Vérifier les en-têtes à la ligne 12 (colonnes A à H)
            $headerRow = 12;
            $expectedHeaders = [
                'A' => 'Matricule',
                'B' => 'Nom',
                'C' => 'Prénom',
                'D' => 'Classe',
                'E' => 'Moyenne',
                'F' => 'Rang'
            ];
            
            foreach ($expectedHeaders as $col => $expectedHeader) {
                $cellValue = $worksheet->getCell($col . $headerRow)->getValue();
                
                // Vérification plus souple: on vérifie si le nom de la colonne est contenu dans la valeur
                if (empty($cellValue) || stripos($cellValue, $expectedHeader) === false) {
                    throw new Exception("Structure incorrecte: l'en-tête '{$expectedHeader}' est introuvable dans la colonne {$col}", self::ERROR_INVALID_STRUCTURE);
                }
            }
            
            return true;
            
        } catch (SpreadsheetException $e) {
            // Log détaillé pour le débogage
            Log::error('Erreur PhpSpreadsheet lors de la validation du fichier Excel', [
                'path' => $filePath,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Message d'erreur spécifique pour l'utilisateur
            throw new Exception('Le fichier Excel ne peut pas être ouvert ou est corrompu. Message technique: ' . $e->getMessage(), self::ERROR_INVALID_FILE);
        } catch (Exception $e) {
            // Si c'est une exception que nous avons lancée nous-mêmes, la propager
            if ($e->getCode() >= self::ERROR_INVALID_FILE && $e->getCode() <= self::ERROR_NO_CLASSROOMS) {
                throw $e;
            }
            
            // Sinon, logger et lancer une exception générique
            Log::error('Erreur générique lors de la validation du fichier Excel', [
                'path' => $filePath,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            throw new Exception('Erreur lors de la validation du fichier Excel: ' . $e->getMessage(), self::ERROR_INVALID_FILE);
        }
    }

    /**
     * Traite l'onglet "Moyennes eleves" du fichier Excel
     * 
     * @param Spreadsheet $spreadsheet Instance du fichier Excel
     * @param array $classrooms Tableau des classes disponibles
     * @param int $gradeLevelId ID du niveau scolaire
     * @return array Statistiques d'importation
     */
    private function processAveragesSheet($spreadsheet, array $classrooms, int $gradeLevelId)
    {
        // Récupérer l'onglet "Moyennes eleves"
        $worksheet = $spreadsheet->getSheetByName('Moyennes eleves');
        
        // Initialiser les statistiques
        $stats = [
            'total' => 0,
            'imported' => 0,
            'updated' => 0,
            'errors' => 0
        ];

        // Trouver la ligne des en-têtes (ligne 12) et la dernière ligne de données
        $headerRow = 12;
        $highestRow = $worksheet->getHighestDataRow();
        
        // Définir les colonnes selon l'image Moyennes eleves.PNG
        $matriculeCol = 'A';
        $nomCol = 'B';
        $prenomCol = 'C';
        $classeCol = 'D';
        $moyenneCol = 'E';
        $rangCol = 'F';
        $appreciationCol = 'G';
        $sexeCol = 'H';
        
        // Vérifier que les colonnes attendues correspondent bien aux en-têtes
        $headers = [
            $matriculeCol => 'Matricule',
            $nomCol => 'Nom',
            $prenomCol => 'Prénom',
            $classeCol => 'Classe',
            $moyenneCol => 'Moyenne',
            $rangCol => 'Rang'
        ];
        
        foreach ($headers as $col => $expectedHeader) {
            $actualHeader = trim($worksheet->getCell($col . $headerRow)->getValue());
            if (empty($actualHeader) || !str_contains(strtolower($actualHeader), strtolower($expectedHeader))) {
                throw new Exception("Erreur de structure dans l'onglet Moyennes eleves : en-tête '$expectedHeader' non trouvé dans la colonne $col", self::ERROR_INVALID_STRUCTURE);
            }
        }
        
        // Parcourir les lignes de données (à partir de la ligne 13)
        for ($row = $headerRow + 1; $row <= $highestRow; $row++) {
            $stats['total']++;
            
            // Lire les données de l'élève
            $matricule = trim($worksheet->getCell($matriculeCol . $row)->getValue());
            $nom = trim($worksheet->getCell($nomCol . $row)->getValue());
            $prenom = trim($worksheet->getCell($prenomCol . $row)->getValue());
            $classe = trim($worksheet->getCell($classeCol . $row)->getValue());
            $moyenne = $worksheet->getCell($moyenneCol . $row)->getValue();
            $rang = $worksheet->getCell($rangCol . $row)->getValue();
            $appreciation = $worksheet->getCell($appreciationCol . $row)->getValue() ?? '';
            $sexe = $worksheet->getCell($sexeCol . $row)->getValue() ?? '';
            
            // Ignorer les lignes sans données essentielles
            if (empty($nom) || empty($prenom) || empty($classe) || !is_numeric($moyenne)) {
                $stats['errors']++;
                Log::info("Ligne {$row} ignorée: données manquantes ou invalides", [
                    'nom' => $nom,
                    'prenom' => $prenom,
                    'classe' => $classe,
                    'moyenne' => $moyenne
                ]);
                continue;
            }

            try {
                // Trouver ou créer la classe si elle n'existe pas
                $classroomId = $this->findOrCreateClassroom($classe, $classrooms, $gradeLevelId);
                
                // Trouver ou créer l'élève
                $student = Student::firstOrNew([
                    'nom' => $nom,
                    'prenom' => $prenom,
                    'classroom_id' => $classroomId
                ]);

                // Mettre à jour les données de l'élève
                $isNewStudent = !$student->exists;
                
                if (!$student->exists || empty($student->matricule)) {
                    $student->matricule = $matricule ?: $this->generateStudentId($nom, $prenom);
                }
                
                if (!empty($sexe)) {
                    // Standardiser le sexe à M ou F
                    if (in_array(strtoupper(substr($sexe, 0, 1)), ['M', 'G', 'H'])) {
                        $student->sexe = 'M';
                    } elseif (in_array(strtoupper(substr($sexe, 0, 1)), ['F'])) {
                        $student->sexe = 'F';
                    }
                }
                
                $student->save();

                // Enregistrer la moyenne générale
                Semester1Average::updateOrCreate(
                    ['student_id' => $student->id],
                    [
                        'moyenne' => (float) $moyenne,
                        'rang' => $rang ?: null,
                        'appreciation' => (string) $appreciation
                    ]
                );

                if ($isNewStudent) {
                    $stats['imported']++;
                } else {
                    $stats['updated']++;
                }
                
            } catch (Exception $e) {
                $stats['errors']++;
                Log::warning("Erreur lors du traitement de la ligne {$row} de l'onglet Moyennes: " . $e->getMessage(), [
                    'exception' => $e,
                    'nom' => $nom,
                    'prenom' => $prenom,
                    'classe' => $classe
                ]);
            }
        }

        // Recalcul des rangs par classe
        $this->recalculateAverageRanks($gradeLevelId);

        return $stats;
    }

    /**
     * Recalcule les rangs des élèves pour les moyennes générales
     *
     * @param int $gradeLevelId ID du niveau scolaire
     * @return void
     */
    private function recalculateAverageRanks(int $gradeLevelId)
    {
        // Récupérer toutes les classes de ce niveau
        $classroomIds = Classroom::where('grade_level_id', $gradeLevelId)
            ->where('active', true)
            ->pluck('id')
            ->toArray();

        // Pour chaque classe, calculer les rangs
        foreach ($classroomIds as $classroomId) {
            $students = Student::where('classroom_id', $classroomId)->pluck('id')->toArray();
            
            if (empty($students)) {
                continue;
            }
            
            // Récupérer les moyennes et les trier par ordre décroissant
            $averages = Semester1Average::whereIn('student_id', $students)
                ->orderBy('moyenne', 'desc')
                ->get();
                
            $currentRank = 1;
            $currentMoyenne = null;
            $sameRankCount = 0;
            
            foreach ($averages as $index => $average) {
                // Si la moyenne est différente de la précédente, mettre à jour le rang
                if ($currentMoyenne !== $average->moyenne) {
                    $currentMoyenne = $average->moyenne;
                    $currentRank = $index + 1;
                    $sameRankCount = 0;
                }
                
                $average->rang = $currentRank;
                $average->save();
            }
        }
    }

    /**
     * Trouve ou crée une classe et met à jour le tableau des classes
     * 
     * @param string $className Nom de la classe
     * @param array &$classrooms Référence au tableau des classes
     * @param int $gradeLevelId ID du niveau scolaire
     * @return int L'ID de la classe
     */
    private function findOrCreateClassroom(string $className, array &$classrooms, int $gradeLevelId)
    {
        if (isset($classrooms[$className])) {
            return $classrooms[$className];
        }

        // Vérifier si la classe existe déjà mais est inactive
        $existingClassroom = Classroom::where('name', $className)
            ->where('grade_level_id', $gradeLevelId)
            ->first();

        if ($existingClassroom) {
            // Réactiver la classe si elle était inactive
            if (!$existingClassroom->active) {
                $existingClassroom->active = true;
                $existingClassroom->save();
            }
            $classrooms[$className] = $existingClassroom->id;
            return $existingClassroom->id;
        }

        // Créer la classe si elle n'existe pas
        $classroom = Classroom::create([
            'name' => $className,
            'grade_level_id' => $gradeLevelId,
            'active' => true
        ]);

        // Mettre à jour le tableau des classes
        $classrooms[$className] = $classroom->id;
        
        return $classroom->id;
    }

    /**
     * Génère un ID d'élève unique si le matricule n'est pas fourni
     *
     * @param string $nom Nom de l'élève
     * @param string $prenom Prénom de l'élève
     * @return string Matricule généré
     */
    private function generateStudentId(string $nom, string $prenom): string
    {
        $prefix = strtoupper(substr($nom, 0, 2) . substr($prenom, 0, 1));
        $uniqueId = $prefix . date('Y') . mt_rand(1000, 9999);
        
        // Vérifier si ce matricule existe déjà
        while (Student::where('matricule', $uniqueId)->exists()) {
            $uniqueId = $prefix . date('Y') . mt_rand(1000, 9999);
        }
        
        return $uniqueId;
    }

    /**
     * Retourne un message d'erreur convivial en fonction du code d'erreur
     * 
     * @param Exception $e Exception levée
     * @return string Message d'erreur convivial
     */
    private function getFriendlyErrorMessage(Exception $e)
    {
        switch ($e->getCode()) {
            case self::ERROR_INVALID_FILE:
                return 'Le fichier Excel sélectionné est invalide ou corrompu. Veuillez vérifier qu\'il s\'agit bien d\'un fichier Excel (.xlsx ou .xls).';
            
            case self::ERROR_MISSING_SHEETS:
                return 'Le fichier Excel doit contenir l\'onglet "Moyennes eleves".';
            
            case self::ERROR_INVALID_STRUCTURE:
                return 'La structure du fichier Excel ne correspond pas au format attendu. ' . $e->getMessage();
            
            case self::ERROR_NO_DATA:
                return 'Le fichier Excel ne contient pas de données à importer. ' . $e->getMessage();
            
            case self::ERROR_NO_CLASSROOMS:
                return 'Aucune classe active n\'a été trouvée pour le niveau scolaire sélectionné. Veuillez d\'abord configurer les classes dans les paramètres.';
            
            default:
                return 'Une erreur inattendue est survenue lors de l\'importation: ' . $e->getMessage();
        }
    }

    /**
     * Stocke une copie du fichier importé pour référence future
     *
     * @param string $filePath Chemin du fichier temporaire
     * @param int $gradeLevelId ID du niveau scolaire
     * @return string Le chemin de stockage du fichier
     */
    private function storeFile(string $filePath, int $gradeLevelId)
    {
        $gradeLevel = GradeLevel::find($gradeLevelId);
        $timestamp = now()->format('Ymd_His');
        $filename = "import_{$gradeLevel->name}_{$timestamp}.xlsx";
        
        // Déplacer le fichier vers le répertoire de stockage
        $storagePath = "imports/{$filename}";
        Storage::disk('local')->put($storagePath, file_get_contents($filePath));
        
        return $storagePath;
    }

    /**
     * Supprime les données liées à une importation spécifique
     * 
     * @param int $importId ID de l'importation
     * @return bool|string True si succès, message d'erreur si échec
     */
    public function deleteImportData(int $importId)
    {
        try {
            $import = ImportHistory::findOrFail($importId);
            
            // Vérifier si l'importation est en cours
            if ($import->status === 'en_cours') {
                return 'Impossible de supprimer une importation en cours.';
            }
            
            DB::beginTransaction();
            
            // Supprimer le fichier importé
            if (Storage::disk('local')->exists($import->file_path)) {
                Storage::disk('local')->delete($import->file_path);
            }
            
            // Supprimer l'enregistrement d'importation
            $import->delete();
            
            DB::commit();
            return true;
            
        } catch (Exception $e) {
            if (DB::transactionLevel() > 0) {
                DB::rollBack();
            }
            
            Log::error('Erreur lors de la suppression de l\'importation', [
                'import_id' => $importId,
                'error' => $e->getMessage()
            ]);
            
            return 'Une erreur est survenue lors de la suppression: ' . $e->getMessage();
        }
    }

    /**
     * Fonction avancée pour charger et nettoyer un fichier Excel
     * Inspirée d'un script Python utilisant Pandas
     * 
     * @param string $filePath Chemin vers le fichier Excel
     * @return array Tableau contenant les dataframes nettoyés
     * @throws Exception En cas d'erreur de traitement
     */
    public function chargerEtNettoyer(string $filePath)
    {
        try {
            // Vérifications de base du fichier
            if (!file_exists($filePath)) {
                throw new Exception('Le fichier Excel n\'existe pas', self::ERROR_INVALID_FILE);
            }
            
            if (filesize($filePath) <= 0) {
                throw new Exception('Le fichier Excel est vide', self::ERROR_INVALID_FILE);
            }
            
            $fileExtension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
            if (!in_array($fileExtension, ['xlsx', 'xls'])) {
                throw new Exception('Le fichier doit être au format Excel (.xlsx ou .xls)', self::ERROR_INVALID_FILE);
            }

            // Créer un reader pour le fichier Excel
            $inputFileType = IOFactory::identify($filePath);
            $reader = IOFactory::createReader($inputFileType);
            $reader->setReadDataOnly(true);
            
            // Vérifier la présence des onglets nécessaires
            $worksheetInfo = $reader->listWorksheetInfo($filePath);
            $worksheetNames = array_column($worksheetInfo, 'worksheetName');
            
            if (!in_array('Moyennes eleves', $worksheetNames)) {
                throw new Exception('Le fichier doit contenir l\'onglet "Moyennes eleves"', self::ERROR_MISSING_SHEETS);
            }
            
            if (!in_array('Données détaillées', $worksheetNames)) {
                throw new Exception('Le fichier doit contenir l\'onglet "Données détaillées"', self::ERROR_MISSING_SHEETS);
            }
            
            // 1. Lecture de l'onglet "Moyennes eleves" (ignorer les 11 premières lignes)
            $reader->setLoadSheetsOnly(['Moyennes eleves']);
            $spreadsheet = $reader->load($filePath);
            $worksheetMoyennes = $spreadsheet->getActiveSheet();
            
            // Récupérer les données à partir de la ligne 12 (index 11 en 0-based)
            $dataArray = $worksheetMoyennes->rangeToArray(
                'A12:' . $worksheetMoyennes->getHighestColumn() . $worksheetMoyennes->getHighestRow(),
                null, true, true, true
            );
            
            // Récupérer les entêtes
            $headerRow = 12;
            $headers = [];
            $highestColumn = $worksheetMoyennes->getHighestColumn();
            for ($col = 'A'; $col <= $highestColumn; $col++) {
                $headers[$col] = $worksheetMoyennes->getCell($col . $headerRow)->getValue();
            }
            
            // Tableau pour stocker les données nettoyées
            $moyennesEleves = [
                'headers' => $headers,
                'data' => []
            ];
            
            // Ajouter toutes les lignes avec des données (à partir de la ligne 13)
            foreach ($dataArray as $rowIndex => $row) {
                if ($rowIndex <= $headerRow) continue; // Sauter les en-têtes
                
                // Vérifier que la ligne contient des données pertinentes
                if (!empty(trim($row['B'])) && !empty(trim($row['C']))) {
                    $moyennesEleves['data'][] = $row;
                }
            }
            
            // 2. Lecture de l'onglet "Données détaillées" (ignorer les 8 premières lignes)
            $reader = IOFactory::createReader($inputFileType);
            $reader->setReadDataOnly(true);
            $reader->setLoadSheetsOnly(['Données détaillées']);
            $spreadsheetDetail = $reader->load($filePath);
            $worksheetDetail = $spreadsheetDetail->getActiveSheet();
            
            // Récupérer les données à partir de la ligne 9 (index 8 en 0-based)
            $dataArrayDetail = $worksheetDetail->rangeToArray(
                'A9:' . $worksheetDetail->getHighestColumn() . $worksheetDetail->getHighestRow(),
                null, true, true, true
            );
            
            // Récupérer l'en-tête niveau 1 (disciplines)
            $headerRow1 = 9;
            $disciplines = [];
            $highestColumn = $worksheetDetail->getHighestColumn();
            for ($col = 'A'; $col <= $highestColumn; $col++) {
                $cellValue = $worksheetDetail->getCell($col . $headerRow1)->getValue();
                $disciplines[$col] = $cellValue;
                
                // Si le nom est vide ou "Unnamed", utiliser le précédent (si disponible)
                if (empty($cellValue) || strpos($cellValue, 'Unnamed') !== false) {
                    if ($col > 'A') {
                        $prevCol = chr(ord($col) - 1);
                        $disciplines[$col] = $disciplines[$prevCol];
                    }
                }
            }
            
            // Récupérer l'en-tête niveau 2 (sous-colonnes)
            $headerRow2 = 10;
            $sousColonnes = [];
            for ($col = 'A'; $col <= $highestColumn; $col++) {
                $sousColonnes[$col] = $worksheetDetail->getCell($col . $headerRow2)->getValue();
            }
            
            // Identifier les colonnes qui contiennent "Moy D"
            $colonnesMoyD = [];
            $nomsMoyD = [];
            foreach ($sousColonnes as $col => $value) {
                if ($value == 'Moy D') {
                    $colonnesMoyD[] = $col;
                    $nomsMoyD[$col] = $disciplines[$col];
                }
            }
            
            // Préparer le tableau final
            $donnéesDétaillées = [
                'headers' => [
                    'disciplines' => $disciplines,
                    'sous_colonnes' => $sousColonnes
                ],
                'info_eleves' => [],
                'moyennes_disciplines' => [],
                'moy_d_headers' => $nomsMoyD
            ];
            
            // Traiter les données
            foreach ($dataArrayDetail as $rowIndex => $row) {
                if ($rowIndex <= $headerRow2) continue; // Sauter les en-têtes
                
                // Vérifier que la ligne contient des données pertinentes
                if (!empty(trim($row['B']))) {
                    // Info élèves (3 premières colonnes)
                    $infoEleve = [
                        'A' => $row['A'] ?? '', // Matricule
                        'B' => $row['B'] ?? '', // Nom
                        'C' => $row['C'] ?? ''  // Prénom
                    ];
                    
                    // Extraire uniquement les moyennes par discipline
                    $moyennesDisciplines = [];
                    foreach ($colonnesMoyD as $col) {
                        $moyennesDisciplines[$nomsMoyD[$col]] = $row[$col] ?? null;
                    }
                    
                    $donnéesDétaillées['info_eleves'][] = $infoEleve;
                    $donnéesDétaillées['moyennes_disciplines'][] = $moyennesDisciplines;
                }
            }
            
            // Retourner les données nettoyées
            return [
                'moyennes_eleves' => $moyennesEleves,
                'données_détaillées' => $donnéesDétaillées
            ];
            
        } catch (SpreadsheetException $e) {
            // Log détaillé pour le débogage
            Log::error('Erreur PhpSpreadsheet lors du traitement du fichier Excel', [
                'path' => $filePath,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Message d'erreur spécifique pour l'utilisateur
            throw new Exception('Le fichier Excel ne peut pas être ouvert ou est corrompu. Message technique: ' . $e->getMessage(), self::ERROR_INVALID_FILE);
        } catch (Exception $e) {
            // Si c'est une exception que nous avons lancée nous-mêmes, la propager
            if ($e->getCode() >= self::ERROR_INVALID_FILE && $e->getCode() <= self::ERROR_NO_CLASSROOMS) {
                throw $e;
            }
            
            // Sinon, logger et lancer une exception générique
            Log::error('Erreur lors du traitement du fichier Excel', [
                'path' => $filePath,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            throw new Exception('Erreur lors du traitement du fichier Excel: ' . $e->getMessage(), self::ERROR_INVALID_FILE);
        }
    }
    
    /**
     * Importer le fichier Excel avec la nouvelle méthode de traitement
     * 
     * @param string $filePath Chemin vers le fichier Excel
     * @param int $gradeLevelId ID du niveau scolaire
     * @param int $userId ID de l'utilisateur
     * @param int|null $classroomId ID de classe spécifique (optionnel)
     * @return array Résultat de l'importation
     */
    public function importExcelAvecTraitement(string $filePath, int $gradeLevelId, int $userId = null, int $classroomId = null)
    {
        try {
            // Utiliser la nouvelle méthode pour charger et nettoyer le fichier Excel
            $données = $this->chargerEtNettoyer($filePath);
            
            // Enregistrer une copie du fichier pour référence future
            $storagePath = $this->storeFile($filePath, $gradeLevelId);
            
            // Récupération des classes du niveau concerné
            $classrooms = Classroom::where('grade_level_id', $gradeLevelId)
                ->where('active', true);
                
            // Si une classe spécifique est demandée, filtrer uniquement pour cette classe
            if ($classroomId) {
                $classrooms = $classrooms->where('id', $classroomId);
            }
            
            // Obtenir le tableau des classes
            $classrooms = $classrooms->pluck('id', 'name')->toArray();
            
            if (empty($classrooms)) {
                throw new Exception('Aucune classe active trouvée pour ce niveau. Veuillez d\'abord configurer les classes dans les paramètres.', self::ERROR_NO_CLASSROOMS);
            }
            
            // Démarrer une transaction pour assurer l'intégrité des données
            DB::beginTransaction();
            
            // Créer un enregistrement d'historique pour cette importation
            $importHistory = ImportHistory::create([
                'grade_level_id' => $gradeLevelId,
                'user_id' => $userId,
                'file_path' => $storagePath,
                'status' => 'en_cours',
                'details' => json_encode([
                    'début' => now()->toDateTimeString(),
                    'niveau' => GradeLevel::find($gradeLevelId)->name,
                    'classe_specifique' => $classroomId ? Classroom::find($classroomId)->name : null
                ])
            ]);
            
            // Traiter les données nettoyées pour les moyennes générales
            $stats = $this->processCleanedData($données, $classrooms, $gradeLevelId);
            
            // Mettre à jour l'historique avec les résultats
            $importHistory->update([
                'status' => 'terminé',
                'details' => json_encode([
                    'début' => json_decode($importHistory->details)->début,
                    'fin' => now()->toDateTimeString(),
                    'niveau' => GradeLevel::find($gradeLevelId)->name,
                    'classe_specifique' => $classroomId ? Classroom::find($classroomId)->name : null,
                    'statistiques' => [
                        'total_students' => $stats['imported'] + $stats['updated'],
                        'new_students' => $stats['imported'],
                        'updated_students' => $stats['updated'],
                        'errors' => $stats['errors'],
                        'subjects_imported' => $stats['subjects_imported'] ?? 0
                    ]
                ])
            ]);
            
            // Valider la transaction
            DB::commit();
            
            return [
                'status' => 'success',
                'stats' => [
                    'total_students' => $stats['imported'] + $stats['updated'],
                    'new_students' => $stats['imported'],
                    'updated_students' => $stats['updated'],
                    'errors' => $stats['errors'],
                    'subjects_imported' => $stats['subjects_imported'] ?? 0
                ],
                'import_id' => $importHistory->id
            ];
            
        } catch (Exception $e) {
            // Rollback de la transaction en cas d'erreur
            if (DB::transactionLevel() > 0) {
                DB::rollBack();
            }
            
            // Enregistrer l'erreur dans l'historique si l'enregistrement existe
            if (isset($importHistory)) {
                $importHistory->update([
                    'status' => 'échoué',
                    'details' => json_encode([
                        'début' => json_decode($importHistory->details)->début,
                        'fin' => now()->toDateTimeString(),
                        'erreur' => $e->getMessage(),
                        'code_erreur' => $e->getCode(),
                        'trace' => $e->getTraceAsString()
                    ])
                ]);
            }
            
            // Enregistrer l'erreur dans les logs pour le débogage
            Log::error('Erreur lors de l\'importation du fichier Excel avec traitement', [
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'trace' => $e->getTraceAsString(),
                'file_path' => $filePath,
                'grade_level_id' => $gradeLevelId
            ]);
            
            // Définir un message d'erreur convivial en fonction du code d'erreur
            $errorMessage = $this->getFriendlyErrorMessage($e);
            
            return [
                'status' => 'error',
                'message' => $errorMessage,
                'error_code' => $e->getCode(),
                'import_id' => $importHistory->id ?? null
            ];
        }
    }
    
    /**
     * Traite les données nettoyées pour les importer dans la base de données
     * 
     * @param array $données Données nettoyées du fichier Excel
     * @param array $classrooms Tableau des classes disponibles
     * @param int $gradeLevelId ID du niveau scolaire
     * @return array Statistiques d'importation
     */
    private function processCleanedData(array $données, array $classrooms, int $gradeLevelId)
    {
        // Initialiser les statistiques
        $stats = [
            'total' => 0,
            'imported' => 0,
            'updated' => 0,
            'errors' => 0,
            'subjects_imported' => 0
        ];
        
        // Traiter les moyennes générales
        $moyennesEleves = $données['moyennes_eleves'];
        $headers = $moyennesEleves['headers'];
        
        // Identifier les indices des colonnes importantes
        $matriculeCol = 'A';
        $nomCol = 'B';
        $prenomCol = 'C';
        $classeCol = 'D';
        $moyenneCol = 'E';
        $rangCol = 'F';
        $appreciationCol = 'G';
        $sexeCol = 'H';
        
        // Parcourir toutes les lignes de données
        foreach ($moyennesEleves['data'] as $row) {
            $stats['total']++;
            
            // Extraire les données de l'élève
            $matricule = trim($row[$matriculeCol] ?? '');
            $nom = trim($row[$nomCol] ?? '');
            $prenom = trim($row[$prenomCol] ?? '');
            $classe = trim($row[$classeCol] ?? '');
            $moyenne = $row[$moyenneCol] ?? null;
            $rang = $row[$rangCol] ?? null;
            $appreciation = $row[$appreciationCol] ?? '';
            $sexe = $row[$sexeCol] ?? '';
            
            // Ignorer les lignes sans données essentielles
            if (empty($nom) || empty($prenom) || empty($classe) || !is_numeric($moyenne)) {
                $stats['errors']++;
                Log::info("Ligne ignorée: données manquantes ou invalides", [
                    'nom' => $nom,
                    'prenom' => $prenom,
                    'classe' => $classe,
                    'moyenne' => $moyenne
                ]);
                continue;
            }
            
            try {
                // Trouver ou créer la classe si elle n'existe pas
                $classroomId = $this->findOrCreateClassroom($classe, $classrooms, $gradeLevelId);
                
                // Trouver ou créer l'élève
                $student = Student::firstOrNew([
                    'nom' => $nom,
                    'prenom' => $prenom,
                    'classroom_id' => $classroomId
                ]);
                
                // Mettre à jour les données de l'élève
                $isNewStudent = !$student->exists;
                
                if (!$student->exists || empty($student->matricule)) {
                    $student->matricule = $matricule ?: $this->generateStudentId($nom, $prenom);
                }
                
                if (!empty($sexe)) {
                    // Standardiser le sexe à M ou F
                    if (in_array(strtoupper(substr($sexe, 0, 1)), ['M', 'G', 'H'])) {
                        $student->sexe = 'M';
                    } elseif (in_array(strtoupper(substr($sexe, 0, 1)), ['F'])) {
                        $student->sexe = 'F';
                    }
                }
                
                $student->save();
                
                // Enregistrer la moyenne générale
                Semester1Average::updateOrCreate(
                    ['student_id' => $student->id],
                    [
                        'moyenne' => (float) $moyenne,
                        'rang' => $rang ?: null,
                        'appreciation' => (string) $appreciation
                    ]
                );
                
                // Si on a des données détaillées, traiter les moyennes par matière
                if (isset($données['données_détaillées']) && !empty($données['données_détaillées'])) {
                    $this->importSubjectMarks($student->id, $données['données_détaillées'], $nom, $prenom, $stats);
                }
                
                if ($isNewStudent) {
                    $stats['imported']++;
                } else {
                    $stats['updated']++;
                }
                
            } catch (Exception $e) {
                $stats['errors']++;
                Log::warning("Erreur lors du traitement d'un élève: " . $e->getMessage(), [
                    'exception' => $e,
                    'nom' => $nom,
                    'prenom' => $prenom,
                    'classe' => $classe
                ]);
            }
        }
        
        // Recalcul des rangs par classe
        $this->recalculateAverageRanks($gradeLevelId);
        
        return $stats;
    }
    
    /**
     * Importer les moyennes par matière pour un élève
     * 
     * @param int $studentId ID de l'élève
     * @param array $donnéesDétaillées Données des moyennes par matière
     * @param string $nom Nom de l'élève (pour débogage)
     * @param string $prenom Prénom de l'élève (pour débogage)
     * @param array &$stats Référence aux statistiques d'importation
     */
    private function importSubjectMarks(int $studentId, array $donnéesDétaillées, string $nom, string $prenom, array &$stats)
    {
        // Rechercher l'élève dans les données détaillées (par nom et prénom)
        $elèveTrouvé = false;
        $indexElève = null;
        
        foreach ($donnéesDétaillées['info_eleves'] as $index => $infoEleve) {
            if (trim($infoEleve['B']) == $nom && trim($infoEleve['C']) == $prenom) {
                $elèveTrouvé = true;
                $indexElève = $index;
                break;
            }
        }
        
        if (!$elèveTrouvé) {
            // L'élève n'a pas été trouvé dans les données détaillées
            return;
        }
        
        // Récupérer les moyennes par discipline pour cet élève
        $moyennesDisciplines = $donnéesDétaillées['moyennes_disciplines'][$indexElève];
        
        // Pour chaque discipline, créer ou mettre à jour les notes
        foreach ($moyennesDisciplines as $discipline => $moyenne) {
            if (!is_numeric($moyenne)) {
                continue; // Ignorer les valeurs non numériques
            }
            
            // Trouver ou créer la matière
            $subject = Subject::firstOrCreate(
                ['name' => $discipline],
                ['description' => 'Matière importée automatiquement']
            );
            
            // Enregistrer la note pour cette matière
            Semester1SubjectMark::updateOrCreate(
                [
                    'student_id' => $studentId,
                    'subject_id' => $subject->id
                ],
                [
                    'mark' => (float) $moyenne,
                    'coefficient' => 1, // Coefficient par défaut
                    'comment' => null
                ]
            );
            
            $stats['subjects_imported']++;
        }
    }
}