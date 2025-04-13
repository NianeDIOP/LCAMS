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

        // Trouver les lignes de début et de fin des données
        $highestRow = $worksheet->getHighestDataRow();
        $highestColumn = $worksheet->getHighestDataColumn();
        
        // Trouver la ligne d'en-tête qui contient "Matricule", "Nom", "Prénom", etc.
        $headerRow = null;
        $matriculeCol = null;
        $nomCol = null;
        $prenomCol = null;
        $classeCol = null;
        $moyenneCol = null;
        $rangCol = null;
        $appreciationCol = null;
        $sexeCol = null;

        // Chercher la ligne d'en-tête 
        for ($row = 1; $row <= 20; $row++) { // Limité aux 20 premières lignes
            $foundMatricule = false;
            $foundNom = false;
            $foundPrenom = false;
            $foundClasse = false;
            $foundMoyenne = false;
            
            for ($col = 'A'; $col <= $highestColumn; $col++) {
                $cellValue = trim($worksheet->getCell($col . $row)->getValue());
                
                if (stripos($cellValue, 'Matricule') !== false) {
                    $matriculeCol = $col;
                    $foundMatricule = true;
                } elseif (strcasecmp($cellValue, 'Nom') === 0) {
                    $nomCol = $col;
                    $foundNom = true;
                } elseif (strcasecmp($cellValue, 'Prénom') === 0) {
                    $prenomCol = $col;
                    $foundPrenom = true;
                } elseif (strcasecmp($cellValue, 'Classe') === 0) {
                    $classeCol = $col;
                    $foundClasse = true;
                } elseif (strcasecmp($cellValue, 'Moyenne') === 0) {
                    $moyenneCol = $col;
                    $foundMoyenne = true;
                } elseif (strcasecmp($cellValue, 'Rang') === 0) {
                    $rangCol = $col;
                } elseif (stripos($cellValue, 'Appréciation') !== false) {
                    $appreciationCol = $col;
                } elseif (in_array(strtoupper($cellValue), ['SEXE', 'GENRE'])) {
                    $sexeCol = $col;
                }
            }
            
            // Si toutes les colonnes essentielles sont trouvées
            if ($foundMatricule && $foundNom && $foundPrenom && $foundClasse && $foundMoyenne) {
                $headerRow = $row;
                break;
            }
        }
        
        if ($headerRow === null) {
            throw new Exception("La structure de l'onglet 'Moyennes eleves' ne correspond pas au format attendu. Impossible de trouver l'en-tête.", self::ERROR_INVALID_STRUCTURE);
        }
        
        // Commencer à traiter les données à partir de la ligne suivant l'en-tête
        $startRow = $headerRow + 1;
        
        // Parcourir les lignes de données
        for ($row = $startRow; $row <= $highestRow; $row++) {
            $stats['total']++;
            
            // Lire les données de l'élève
            $matricule = $matriculeCol ? trim($worksheet->getCell($matriculeCol . $row)->getValue()) : '';
            $nom = $nomCol ? trim($worksheet->getCell($nomCol . $row)->getValue()) : '';
            $prenom = $prenomCol ? trim($worksheet->getCell($prenomCol . $row)->getValue()) : '';
            $classe = $classeCol ? trim($worksheet->getCell($classeCol . $row)->getValue()) : '';
            $moyenne = $moyenneCol ? $worksheet->getCell($moyenneCol . $row)->getValue() : null;
            $rang = $rangCol ? $worksheet->getCell($rangCol . $row)->getValue() : null;
            $appreciation = $appreciationCol ? $worksheet->getCell($appreciationCol . $row)->getValue() : '';
            $sexe = $sexeCol ? trim($worksheet->getCell($sexeCol . $row)->getValue()) : null;
            
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
     * Traite l'onglet "Données détaillées" du fichier Excel pour extraire les moyennes par discipline
     * 
     * @param Spreadsheet $spreadsheet Instance du fichier Excel
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

        // Trouver les lignes pour les en-têtes et les noms de disciplines
        $headerRow = null;
        $disciplineRow = null;
        $matriculeCol = null;
        $nomCol = null;
        $prenomCol = null;
        $classeCol = null;
        
        // Chercher les lignes d'en-tête importantes
        for ($row = 1; $row <= 20; $row++) { // Limité aux 20 premières lignes
            $foundMatricule = false;
            $foundNom = false;
            $foundPrenom = false;
            $foundMoyD = false;
            
            // Vérifier si c'est une ligne d'en-tête avec "Matricule", "Nom", "Prénom"
            $highestColumn = $worksheet->getHighestDataColumn();
            for ($col = 'A'; $col <= $highestColumn; $col++) {
                $cellValue = trim($worksheet->getCell($col . $row)->getValue());
                
                if (stripos($cellValue, 'Matricule') !== false) {
                    $matriculeCol = $col;
                    $foundMatricule = true;
                } elseif (strcasecmp($cellValue, 'Nom') === 0) {
                    $nomCol = $col;
                    $foundNom = true;
                } elseif (strcasecmp($cellValue, 'Prénom') === 0) {
                    $prenomCol = $col;
                    $foundPrenom = true;
                } elseif (strcasecmp($cellValue, 'Classe') === 0) {
                    $classeCol = $col;
                } elseif ($cellValue === 'Moy D') {
                    $foundMoyD = true;
                }
            }
            
            // Si on trouve la ligne des en-têtes de colonnes (Matricule, Nom, Prénom)
            if ($foundMatricule && $foundNom && $foundPrenom) {
                $headerRow = $row;
            }
            
            // Si on trouve au moins une colonne "Moy D", c'est potentiellement la ligne des sous-colonnes
            if ($foundMoyD && $headerRow !== null) {
                $disciplineRow = $row - 1; // La ligne des noms de disciplines est juste au-dessus
                break;
            }
        }
        
        if ($headerRow === null || $disciplineRow === null) {
            throw new Exception("La structure de l'onglet 'Données détaillées' ne correspond pas au format attendu. Impossible de trouver les en-têtes.", self::ERROR_INVALID_STRUCTURE);
        }
        
        // Analyser les disciplines et leurs colonnes "Moy D"
        $disciplines = [];
        $moyDColumns = [];
        $highestColumn = $worksheet->getHighestDataColumn();
        
        // Parcourir les colonnes pour trouver les disciplines et les "Moy D"
        $currentDiscipline = null;
        
        for ($col = 'A'; $col <= $highestColumn; $col++) {
            // Récupérer le nom de la discipline (ligne au-dessus des en-têtes)
            $disciplineName = trim($worksheet->getCell($col . $disciplineRow)->getValue());
            
            // Si on a un nom de discipline non vide
            if (!empty($disciplineName) && !str_contains($disciplineName, 'Unnamed')) {
                $currentDiscipline = $disciplineName;
            }
            
            // Vérifier si c'est une colonne "Moy D"
            $subColHeader = trim($worksheet->getCell($col . $headerRow)->getValue());
            
            if ($subColHeader === 'Moy D' && !empty($currentDiscipline)) {
                // Créer/récupérer la discipline dans la BDD
                $subject = Subject::firstOrCreate(
                    ['nom' => $currentDiscipline],
                    ['active' => true]
                );
                
                $moyDColumns[$col] = [
                    'name' => $currentDiscipline,
                    'id' => $subject->id
                ];
                
                $disciplines[$subject->id] = [
                    'name' => $currentDiscipline,
                    'id' => $subject->id,
                    'imported' => 0,
                    'updated' => 0,
                    'errors' => 0
                ];
            }
        }
        
        if (empty($disciplines)) {
            throw new Exception('Aucune discipline avec colonne "Moy D" n\'a été trouvée dans l\'onglet "Données détaillées".', self::ERROR_INVALID_STRUCTURE);
        }
        
        // Commencer à traiter les données à partir de la ligne suivant l'en-tête
        $startRow = $headerRow + 1;
        $highestRow = $worksheet->getHighestDataRow();
        
        // Parcourir les lignes de données pour traiter les notes
        for ($row = $startRow; $row <= $highestRow; $row++) {
            $stats['total']++;
            
            // Lire les informations de l'élève
            $matricule = $matriculeCol ? trim($worksheet->getCell($matriculeCol . $row)->getValue()) : '';
            $nom = $nomCol ? trim($worksheet->getCell($nomCol . $row)->getValue()) : '';
            $prenom = $prenomCol ? trim($worksheet->getCell($prenomCol . $row)->getValue()) : '';
            $classe = $classeCol ? trim($worksheet->getCell($classeCol . $row)->getValue()) : '';
            
            // Ignorer les lignes sans données essentielles
            if (empty($nom) || empty($prenom)) {
                $stats['errors']++;
                Log::info("Ligne {$row} ignorée dans l'onglet Données détaillées: données élève manquantes", [
                    'nom' => $nom,
                    'prenom' => $prenom
                ]);
                continue;
            }
            
            // Trouver l'élève
            $student = null;
            
            // Si on a le matricule, chercher d'abord par matricule
            if (!empty($matricule)) {
                $student = Student::where('matricule', $matricule)->first();
            }
            
            // Sinon chercher par nom, prénom et classe
            if (!$student) {
                $classroomQuery = Student::where('nom', $nom)->where('prenom', $prenom);
                
                if (!empty($classe) && isset($classrooms[$classe])) {
                    $classroomQuery->where('classroom_id', $classrooms[$classe]);
                } else {
                    $classroomQuery->whereIn('classroom_id', array_values($classrooms));
                }
                
                $student = $classroomQuery->first();
            }
            
            // Si on ne trouve pas l'élève, passer à la ligne suivante
            if (!$student) {
                $stats['errors']++;
                Log::info("Élève non trouvé dans l'onglet Données détaillées: {$nom} {$prenom}", [
                    'matricule' => $matricule,
                    'classe' => $classe
                ]);
                continue;
            }
            
            // Traiter chaque discipline pour cet élève
            foreach ($moyDColumns as $colLetter => $discipline) {
                $note = $worksheet->getCell($colLetter . $row)->getValue();
                
                // Ignorer les notes non numériques
                if (!is_numeric($note)) {
                    continue;
                }
                
                try {
                    // Mettre à jour ou créer la note pour cette discipline
                    $mark = Semester1SubjectMark::updateOrCreate(
                        [
                            'student_id' => $student->id,
                            'subject_id' => $discipline['id']
                        ],
                        [
                            'note' => (float) $note
                        ]
                    );
                    
                    // Mettre à jour les statistiques
                    if ($mark->wasRecentlyCreated) {
                        $disciplines[$discipline['id']]['imported']++;
                    } else {
                        $disciplines[$discipline['id']]['updated']++;
                    }
                } catch (Exception $e) {
                    $disciplines[$discipline['id']]['errors']++;
                    Log::warning("Erreur lors du traitement de la note de {$nom} {$prenom} pour {$discipline['name']}: " . $e->getMessage(), [
                        'exception' => $e
                    ]);
                }
            }
        }
        
        // Calculer les rangs pour chaque discipline
        foreach ($disciplines as $disciplineId => $stats) {
            $this->calculateSubjectRanks($disciplineId, array_values($classrooms));
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