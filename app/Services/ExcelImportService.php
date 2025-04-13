<?php

namespace App\Services;

use App\Models\Student;
use App\Models\Subject;
use App\Models\Classroom;
use App\Models\GradeLevel;
use App\Models\Semester1Average;
use App\Models\Semester1SubjectMark;
use App\Models\ImportHistory;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Exception as SpreadsheetException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ExcelImportService
{
    /**
     * Erreurs d'importation spécifiques
     */
    const ERROR_INVALID_FILE = 'FORMAT_INVALIDE';
    const ERROR_MISSING_SHEETS = 'ONGLETS_MANQUANTS';
    const ERROR_INVALID_STRUCTURE = 'STRUCTURE_INVALIDE';
    const ERROR_NO_DATA = 'AUCUNE_DONNEE';
    const ERROR_NO_CLASSROOMS = 'AUCUNE_CLASSE';
    
    /**
     * Importe les données d'un fichier Excel complet (moyennes et disciplines)
     * 
     * @param string $filePath Chemin vers le fichier Excel téléchargé
     * @param int $gradeLevelId ID du niveau scolaire concerné
     * @param int $userId ID de l'utilisateur qui réalise l'importation
     * @return array Statistiques sur l'importation
     */
    public function importCompleteExcelFile(string $filePath, int $gradeLevelId, int $userId = null)
    {
        try {
            // Vérification du fichier Excel avant tout traitement
            $this->validateExcelFile($filePath);
            
            // Enregistrer une copie du fichier pour référence future
            $storagePath = $this->storeFile($filePath, $gradeLevelId);

            // Chargement du fichier Excel
            $spreadsheet = IOFactory::load($filePath);
            
            // Vérifier que le fichier contient bien les deux onglets nécessaires
            $sheetNames = $spreadsheet->getSheetNames();
            if (!in_array('Moyennes eleves', $sheetNames) || !in_array('Données détaillées', $sheetNames)) {
                throw new Exception('Le fichier Excel doit contenir les onglets "Moyennes eleves" et "Données détaillées".', self::ERROR_MISSING_SHEETS);
            }

            // Récupération des classes du niveau concerné
            $classrooms = Classroom::where('grade_level_id', $gradeLevelId)
                ->where('active', true)
                ->pluck('id', 'name')
                ->toArray();

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
                    'niveau' => GradeLevel::find($gradeLevelId)->name
                ])
            ]);

            // 1. Importer les moyennes générales (premier onglet)
            $averageStats = $this->processAveragesSheet($spreadsheet, $classrooms, $gradeLevelId);

            // 2. Importer les notes des disciplines (deuxième onglet)
            $subjectStats = $this->processSubjectsSheet($spreadsheet, $classrooms, $gradeLevelId);

            // Fusionner les statistiques
            $stats = [
                'total_students' => $averageStats['imported'] + $averageStats['updated'],
                'new_students' => $averageStats['imported'],
                'updated_students' => $averageStats['updated'],
                'total_subjects' => count($subjectStats['subjects']),
                'total_marks' => array_sum(array_map(function($s) { return $s['imported'] + $s['updated']; }, $subjectStats['subjects'])),
                'subjects' => $subjectStats['subjects'],
                'errors' => $averageStats['errors'] + $subjectStats['errors']
            ];

            // Mettre à jour l'historique avec les résultats
            $importHistory->update([
                'status' => 'terminé',
                'details' => json_encode([
                    'début' => json_decode($importHistory->details)->début,
                    'fin' => now()->toDateTimeString(),
                    'niveau' => GradeLevel::find($gradeLevelId)->name,
                    'statistiques' => $stats
                ])
            ]);

            // Valider la transaction
            DB::commit();

            return [
                'status' => 'success',
                'stats' => $stats,
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
        if (!file_exists($filePath)) {
            throw new Exception('Le fichier Excel n\'existe pas', self::ERROR_INVALID_FILE);
        }

        $fileExtension = pathinfo($filePath, PATHINFO_EXTENSION);
        if (!in_array($fileExtension, ['xlsx', 'xls'])) {
            throw new Exception('Le fichier doit être au format Excel (.xlsx ou .xls)', self::ERROR_INVALID_FILE);
        }

        try {
            // Essayer de charger le fichier pour s'assurer qu'il est valide
            $reader = IOFactory::createReader(ucfirst($fileExtension));
            $spreadsheet = $reader->load($filePath);
            
            // Vérifier la présence des onglets nécessaires
            $sheetNames = $spreadsheet->getSheetNames();
            if (!in_array('Moyennes eleves', $sheetNames) || !in_array('Données détaillées', $sheetNames)) {
                throw new Exception('Le fichier Excel doit contenir les onglets "Moyennes eleves" et "Données détaillées"', self::ERROR_MISSING_SHEETS);
            }
            
            return true;
        } catch (SpreadsheetException $e) {
            throw new Exception('Le fichier Excel est corrompu ou dans un format non pris en charge: ' . $e->getMessage(), self::ERROR_INVALID_FILE);
        }
    }

    /**
     * Valide la structure de l'onglet des moyennes
     * 
     * @param \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet
     * @throws Exception Si la structure est invalide
     * @return bool
     */
    private function validateAveragesSheet($sheet)
    {
        // Vérifier que la feuille contient des données
        if ($sheet->getHighestDataRow() < 12) {
            throw new Exception('L\'onglet "Moyennes eleves" est vide ou ne contient pas de données à partir de la ligne 12', self::ERROR_NO_DATA);
        }

        // Vérifier les en-têtes (à adapter selon le format exact attendu)
        $requiredColumns = [
            'A' => 'Matricule',
            'B' => 'Nom',
            'C' => 'Prénom',
            'D' => 'Classe',
            'E' => 'Moyenne'
        ];

        foreach ($requiredColumns as $col => $expectedHeader) {
            $actualHeader = trim($sheet->getCell($col . '11')->getValue());
            if (empty($actualHeader) || strpos($actualHeader, $expectedHeader) === false) {
                throw new Exception("Structure invalide pour l'onglet \"Moyennes eleves\". La colonne $col devrait contenir \"$expectedHeader\"", self::ERROR_INVALID_STRUCTURE);
            }
        }

        return true;
    }

    /**
     * Valide la structure de l'onglet des disciplines
     * 
     * @param \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet
     * @throws Exception Si la structure est invalide
     * @return bool
     */
    private function validateSubjectsSheet($sheet)
    {
        // Vérifier que la feuille contient des données
        if ($sheet->getHighestDataRow() < 9) {
            throw new Exception('L\'onglet "Données détaillées" est vide ou ne contient pas de données à partir de la ligne 9', self::ERROR_NO_DATA);
        }

        // Vérifier les en-têtes de base (colonnes A, B, C)
        $requiredColumns = [
            'A' => 'Matricule',
            'B' => 'Nom',
            'C' => 'Prénom'
        ];

        foreach ($requiredColumns as $col => $expectedHeader) {
            $actualHeader = trim($sheet->getCell($col . '8')->getValue());
            if (empty($actualHeader) || strpos($actualHeader, $expectedHeader) === false) {
                throw new Exception("Structure invalide pour l'onglet \"Données détaillées\". La colonne $col devrait contenir \"$expectedHeader\"", self::ERROR_INVALID_STRUCTURE);
            }
        }

        // Vérifier qu'il y a au moins une colonne "Moy D" pour une discipline
        $highestColumn = $sheet->getHighestDataColumn();
        $columnIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($highestColumn);
        $foundMoyD = false;

        for ($col = 4; $col <= $columnIndex; $col++) {
            $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col);
            $header = trim($sheet->getCell($colLetter . '8')->getValue());
            
            if ($header === 'Moy D') {
                $foundMoyD = true;
                break;
            }
        }

        if (!$foundMoyD) {
            throw new Exception("L'onglet \"Données détaillées\" ne contient aucune colonne \"Moy D\" pour les moyennes de discipline", self::ERROR_INVALID_STRUCTURE);
        }

        return true;
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
                return 'Le fichier Excel doit contenir les onglets "Moyennes eleves" et "Données détaillées".';
            
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
     * Traite l'onglet "Moyennes eleves" du fichier Excel
     * 
     * @param \PhpOffice\PhpSpreadsheet\Spreadsheet $spreadsheet
     * @param array $classrooms Tableau des classes disponibles
     * @param int $gradeLevelId ID du niveau scolaire
     * @return array Statistiques d'importation
     */
    private function processAveragesSheet($spreadsheet, array $classrooms, int $gradeLevelId)
    {
        // Récupérer l'onglet "Moyennes eleves"
        $worksheet = $spreadsheet->getSheetByName('Moyennes eleves');
        
        $stats = [
            'total' => 0,
            'imported' => 0,
            'updated' => 0,
            'errors' => 0
        ];

        // Démarrer à la ligne 12 (après en-têtes)
        $startRow = 12;
        $highestRow = $worksheet->getHighestDataRow();
        
        for ($row = $startRow; $row <= $highestRow; $row++) {
            $stats['total']++;
            
            // Lire les données de la ligne
            $matricule = trim($worksheet->getCell('A' . $row)->getValue());
            $nom = trim($worksheet->getCell('B' . $row)->getValue());
            $prenom = trim($worksheet->getCell('C' . $row)->getValue());
            $classe = trim($worksheet->getCell('D' . $row)->getValue());
            $moyenne = $worksheet->getCell('E' . $row)->getValue();
            $rang = $worksheet->getCell('F' . $row)->getValue();
            $appreciation = $worksheet->getCell('G' . $row)->getValue() ?? '';
            $sexe = $worksheet->getCell('H' . $row)->getValue() ?? null;
            
            // Si les données essentielles sont manquantes, ignorer cette ligne
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
                
                if (!empty($sexe) && in_array(strtoupper($sexe), ['M', 'F'])) {
                    $student->sexe = strtoupper($sexe);
                }
                
                $student->save();

                // Enregistrer la moyenne générale
                Semester1Average::updateOrCreate(
                    ['student_id' => $student->id],
                    [
                        'moyenne' => $moyenne,
                        'rang' => $rang,
                        'appreciation' => $appreciation
                    ]
                );

                // Mettre à jour les statistiques
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

        // Recalcul des rangs par classe si nécessaire
        $this->recalculateAverageRanks($gradeLevelId);

        return $stats;
    }

    /**
     * Traite l'onglet "Données détaillées" du fichier Excel pour extraire les moyennes par discipline
     * 
     * @param \PhpOffice\PhpSpreadsheet\Spreadsheet $spreadsheet
     * @param array $classrooms Tableau des classes disponibles
     * @param int $gradeLevelId ID du niveau scolaire
     * @return array Statistiques d'importation
     */
    private function processSubjectsSheet($spreadsheet, array $classrooms, int $gradeLevelId)
    {
        // Récupérer l'onglet "Données détaillées"
        $worksheet = $spreadsheet->getSheetByName('Données détaillées');
        
        $stats = [
            'total' => 0,
            'errors' => 0,
            'subjects' => []
        ];

        // Démarrer à la ligne 9 (après en-têtes)
        $startRow = 9;
        $highestRow = $worksheet->getHighestDataRow();
        $highestColumn = $worksheet->getHighestDataColumn();
        $columnIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($highestColumn);

        // 1. Analyser l'en-tête pour trouver les disciplines et les colonnes "Moy D"
        $disciplines = [];
        $moyDColumns = [];
        
        // Récupérer les disciplines (ligne 7) et leurs sous-colonnes (ligne 8)
        $currentDiscipline = null;
        
        for ($col = 4; $col <= $columnIndex; $col++) { // Commencer à partir de D (colonne 4)
            $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col);
            
            // Récupérer le nom de la discipline sur la ligne 7
            $disciplineName = trim($worksheet->getCell($colLetter . '7')->getValue());
            
            // Si la discipline n'est pas vide, la mémoriser
            if (!empty($disciplineName) && !str_contains($disciplineName, 'Unnamed')) {
                $currentDiscipline = $disciplineName;
            }
            
            // Vérifier si c'est une colonne "Moy D" sur la ligne 8
            $subCol = trim($worksheet->getCell($colLetter . '8')->getValue());
            
            if ($subCol === 'Moy D' && !empty($currentDiscipline)) {
                $moyDColumns[$colLetter] = $currentDiscipline;
                
                // Créer ou récupérer la discipline dans la base de données
                $subject = Subject::firstOrCreate(
                    ['nom' => $currentDiscipline],
                    ['active' => true]
                );
                
                $disciplines[$colLetter] = [
                    'name' => $currentDiscipline,
                    'id' => $subject->id,
                    'imported' => 0,
                    'updated' => 0,
                    'errors' => 0
                ];
            }
        }
        
        if (empty($disciplines)) {
            throw new Exception('Aucune discipline avec colonne "Moy D" n\'a été trouvée dans le fichier.', self::ERROR_INVALID_STRUCTURE);
        }

        // 2. Pour chaque ligne, récupérer les infos de l'élève et les notes par discipline
        for ($row = $startRow; $row <= $highestRow; $row++) {
            $stats['total']++;
            
            // Informations de l'élève
            $matricule = trim($worksheet->getCell('A' . $row)->getValue());
            $nom = trim($worksheet->getCell('B' . $row)->getValue());
            $prenom = trim($worksheet->getCell('C' . $row)->getValue());
            
            // Si les infos essentielles sont manquantes, ignorer cette ligne
            if (empty($nom) || empty($prenom)) {
                $stats['errors']++;
                Log::info("Ligne {$row} ignorée dans l'onglet Données détaillées: données élève manquantes", [
                    'nom' => $nom,
                    'prenom' => $prenom
                ]);
                continue;
            }
            
            // Trouver l'élève dans la base de données
            $student = Student::where('nom', $nom)
                              ->where('prenom', $prenom)
                              ->whereIn('classroom_id', array_values($classrooms))
                              ->first();
            
            if (!$student) {
                $stats['errors']++;
                Log::info("Élève non trouvé: {$nom} {$prenom} (ligne {$row})", [
                    'classrooms' => array_values($classrooms)
                ]);
                continue;
            }
            
            // Traiter chaque discipline (colonne "Moy D")
            foreach ($disciplines as $colLetter => $discipline) {
                $note = $worksheet->getCell($colLetter . $row)->getValue();
                
                // Si la note est vide ou non numérique, ignorer cette cellule
                if (!is_numeric($note)) {
                    $disciplines[$colLetter]['errors']++;
                    continue;
                }
                
                try {
                    // Enregistrer la note pour cette discipline
                    $mark = Semester1SubjectMark::updateOrCreate(
                        [
                            'student_id' => $student->id,
                            'subject_id' => $discipline['id']
                        ],
                        [
                            'note' => $note
                        ]
                    );
                    
                    // Mettre à jour les statistiques pour cette discipline
                    if ($mark->wasRecentlyCreated) {
                        $disciplines[$colLetter]['imported']++;
                    } else {
                        $disciplines[$colLetter]['updated']++;
                    }
                } catch (Exception $e) {
                    $disciplines[$colLetter]['errors']++;
                    Log::warning("Erreur lors du traitement de la note de {$nom} {$prenom} pour {$discipline['name']}: " . $e->getMessage(), [
                        'exception' => $e
                    ]);
                }
            }
        }

        // 3. Calculer les rangs pour chaque discipline
        foreach ($disciplines as $discipline) {
            $this->calculateSubjectRanks($discipline['id'], array_values($classrooms));
        }

        $stats['subjects'] = array_values($disciplines);
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
                } else {
                    $sameRankCount++;
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
     * Calcule les rangs des élèves pour une discipline donnée
     *
     * @param int $subjectId ID de la discipline
     * @param array $classroomIds IDs des classes concernées
     * @return void
     */
    private function calculateSubjectRanks(int $subjectId, array $classroomIds)
    {
        // Pour chaque classe, calculer le rang des élèves
        foreach ($classroomIds as $classroomId) {
            $students = Student::where('classroom_id', $classroomId)->pluck('id')->toArray();
            
            if (empty($students)) {
                continue;
            }
            
            // Récupérer les notes et les trier par ordre décroissant
            $marks = Semester1SubjectMark::whereIn('student_id', $students)
                ->where('subject_id', $subjectId)
                ->orderBy('note', 'desc')
                ->get();
                
            $currentRank = 1;
            $currentNote = null;
            $sameRankCount = 0;
            
            foreach ($marks as $index => $mark) {
                // Si la note est différente de la précédente, mettre à jour le rang
                if ($currentNote !== $mark->note) {
                    $currentNote = $mark->note;
                    $currentRank = $index + 1;
                    $sameRankCount = 0;
                } else {
                    $sameRankCount++;
                }
                
                $mark->rang = $currentRank;
                $mark->save();
            }
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
}