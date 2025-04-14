<?php

namespace App\Services;

use App\Models\Classroom;
use App\Models\Semester1Average;
use App\Models\Semester1SubjectMark;
use App\Models\Student;
use App\Models\Subject;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use DateTime;
use Exception;

class ExcelImportService
{
    /**
     * Prévisualise les données d'un fichier Excel sans les importer
     *
     * @param string $filePath Chemin vers le fichier Excel
     * @return array Données prévisualisées
     */
    public function preview(string $filePath): array
    {
        try {
            // Optimisation : Utiliser un reader avec des options spécifiques pour la prévisualisation
            $reader = new Xlsx();
            $reader->setReadDataOnly(true); // Ne pas charger les formats, styles, etc.
            $reader->setLoadSheetsOnly(['Moyennes eleves']); // Ne charger que l'onglet requis pour la prévisualisation
            
            $spreadsheet = $reader->load($filePath);
            $studentSheet = $spreadsheet->getActiveSheet();
            
            $students = [];
            $highestRow = min($studentSheet->getHighestRow(), 30); // Limiter à 30 lignes pour la prévisualisation
            $subjects = [];
            
            // Récupérer quelques étudiants pour prévisualisation
            for ($row = 12; $row <= $highestRow; $row++) {
                $matricule = $studentSheet->getCell('A' . $row)->getValue();
                
                // Ignorer les lignes vides
                if (empty($matricule)) {
                    continue;
                }
                
                $prenom = $studentSheet->getCell('B' . $row)->getValue();
                $nom = $studentSheet->getCell('C' . $row)->getValue();
                $sexe = $studentSheet->getCell('D' . $row)->getValue();
                $moyenne = floatval($studentSheet->getCell('J' . $row)->getValue());
                $rang = intval($studentSheet->getCell('K' . $row)->getValue());
                $decision = $studentSheet->getCell('L' . $row)->getValue();
                
                $students[] = [
                    'matricule' => $matricule,
                    'nom' => $nom,
                    'prenom' => $prenom,
                    'sexe' => $sexe,
                    'moyenne' => $moyenne,
                    'rang' => $rang,
                    'decision' => $decision,
                ];
            }
            
            // Charger brièvement la deuxième feuille pour récupérer les noms des matières
            $reader->setLoadSheetsOnly(['Données détaillées']);
            $detailsSpreadsheet = $reader->load($filePath);
            $detailsSheet = $detailsSpreadsheet->getActiveSheet();
            
            // Récupérer les noms des matières (ligne 8)
            $columnIndex = 3;
            $highestColumnIndex = min(
                \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($detailsSheet->getHighestColumn()),
                20 // Limiter le nombre de colonnes pour la prévisualisation
            );
            
            while ($columnIndex <= $highestColumnIndex) {
                $subjectName = $detailsSheet->getCellByColumnAndRow($columnIndex, 8)->getValue();
                if (!empty($subjectName) && !in_array($subjectName, $subjects)) {
                    $subjects[] = $subjectName;
                    // Sauter 4 colonnes (Moy DD, Comp D, Moy D, Rang D)
                    $columnIndex += 4;
                } else {
                    $columnIndex++;
                }
            }
            
            return [
                'success' => true,
                'total_students' => count($students),
                'students' => $students,
                'subjects' => $subjects
            ];
        } catch (Exception $e) {
            Log::error("Erreur lors de la prévisualisation du fichier Excel: " . $e->getMessage());
            
            return [
                'success' => false,
                'message' => "Erreur lors de l'analyse du fichier Excel: " . $e->getMessage(),
            ];
        }
    }

    /**
     * Prévisualise les données d'un fichier Excel et stocke le fichier temporairement
     *
     * @param string $filePath Chemin vers le fichier Excel
     * @param string $sessionId Identifiant de session pour stocker le fichier
     * @return array Données prévisualisées et chemin du fichier
     */
    public function previewAndStore(string $filePath, string $sessionId): array
    {
        try {
            // Stocker le fichier avec un nom basé sur la session pour pouvoir le retrouver
            $storedPath = storage_path('app/temp/excel_' . $sessionId . '.xlsx');
            
            // Créer le répertoire s'il n'existe pas
            if (!file_exists(dirname($storedPath))) {
                mkdir(dirname($storedPath), 0755, true);
            }
            
            // Copier le fichier vers le nouvel emplacement
            copy($filePath, $storedPath);
            
            // Obtenir la prévisualisation
            $previewData = $this->preview($filePath);
            
            if (!$previewData['success']) {
                // Si la prévisualisation échoue, supprimer le fichier stocké
                @unlink($storedPath);
                return $previewData;
            }
            
            // Ajouter le chemin du fichier stocké aux données de prévisualisation
            $previewData['stored_path'] = $storedPath;
            
            return $previewData;
        } catch (Exception $e) {
            Log::error("Erreur lors du stockage et de la prévisualisation du fichier Excel: " . $e->getMessage());
            
            return [
                'success' => false,
                'message' => "Erreur lors de l'analyse du fichier Excel: " . $e->getMessage(),
            ];
        }
    }
    
    /**
     * Récupère un fichier Excel stocké temporairement
     *
     * @param string $sessionId Identifiant de session du fichier
     * @return string|null Chemin du fichier ou null s'il n'existe pas
     */
    public function getStoredFile(string $sessionId): ?string
    {
        $storedPath = storage_path('app/temp/excel_' . $sessionId . '.xlsx');
        
        if (file_exists($storedPath)) {
            return $storedPath;
        }
        
        return null;
    }
    
    /**
     * Supprime un fichier Excel stocké temporairement
     *
     * @param string $sessionId Identifiant de session du fichier
     * @return bool Succès de la suppression
     */
    public function removeStoredFile(string $sessionId): bool
    {
        $storedPath = storage_path('app/temp/excel_' . $sessionId . '.xlsx');
        
        if (file_exists($storedPath)) {
            return @unlink($storedPath);
        }
        
        return true;
    }

    /**
     * Importe les données d'un fichier Excel
     *
     * @param string $filePath Chemin vers le fichier Excel
     * @param int $classroomId ID de la classe dans laquelle importer les données
     * @return array Statistiques sur l'importation
     */
    public function import(string $filePath, int $classroomId): array
    {
        try {
            // Optimisation: Utiliser un reader avec des options spécifiques
            $reader = IOFactory::createReader('Xlsx');
            $reader->setReadDataOnly(true); // Ignore les styles, images, etc.
            
            $spreadsheet = $reader->load($filePath);
            $classroom = Classroom::findOrFail($classroomId);
            
            // Commencer une transaction pour garantir l'intégrité des données
            DB::beginTransaction();
            
            // 1. Traiter l'onglet "Moyennes eleves"
            $studentSheet = $spreadsheet->getSheetByName('Moyennes eleves');
            if (!$studentSheet) {
                throw new Exception("L'onglet 'Moyennes eleves' est introuvable dans le fichier Excel.");
            }
            
            $students = $this->importStudentAverages($studentSheet, $classroomId);
            
            // 2. Traiter l'onglet "Données détaillées"
            $detailsSheet = $spreadsheet->getSheetByName('Données détaillées');
            if (!$detailsSheet) {
                throw new Exception("L'onglet 'Données détaillées' est introuvable dans le fichier Excel.");
            }
            
            // Optimisation : traiter les matières et les notes en une seule passe
            $result = $this->importSubjectMarks($detailsSheet, $students);
            
            // Valider et confirmer la transaction
            DB::commit();
            
            return [
                'success' => true,
                'students_count' => $students->count(),
                'subjects_count' => $result['subjects_count'],
                'marks_count' => $result['marks_count'],
                'classroom_name' => $classroom->name,
            ];
        } catch (Exception $e) {
            // En cas d'erreur, annuler la transaction
            DB::rollBack();
            
            Log::error("Erreur lors de l'importation du fichier Excel: " . $e->getMessage());
            
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }
    
    /**
     * Importe les données de l'onglet "Moyennes eleves"
     *
     * @param \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet
     * @param int $classroomId
     * @return Collection Collection des étudiants importés avec leurs IDs
     */
    private function importStudentAverages($sheet, int $classroomId): Collection
    {
        $students = collect();
        $highestRow = $sheet->getHighestRow();
        $studentsData = [];
        $averagesData = [];
        
        // Optimisation : collecte des données en une seule passe, puis insertion par lots
        for ($row = 12; $row <= $highestRow; $row++) {
            $matricule = $sheet->getCell('A' . $row)->getValue();
            
            // Ignorer les lignes vides
            if (empty($matricule)) {
                continue;
            }
            
            $prenom = $sheet->getCell('B' . $row)->getValue();
            $nom = $sheet->getCell('C' . $row)->getValue();
            $sexe = $sheet->getCell('D' . $row)->getValue();
            $dateNaissanceValue = $sheet->getCell('E' . $row)->getValue();
            $lieu = $sheet->getCell('F' . $row)->getValue();
            $retards = intval($sheet->getCell('G' . $row)->getValue());
            $absences = intval($sheet->getCell('H' . $row)->getValue());
            $convocationsDiscipline = intval($sheet->getCell('I' . $row)->getValue());
            $moyenne = floatval($sheet->getCell('J' . $row)->getValue());
            $rang = intval($sheet->getCell('K' . $row)->getValue());
            $decision = $sheet->getCell('L' . $row)->getValue();
            $appreciation = $sheet->getCell('M' . $row)->getValue();
            $observations = $sheet->getCell('N' . $row)->getValue();
            
            // Convertir la date de naissance du format Excel
            $dateNaissance = null;
            if ($dateNaissanceValue) {
                try {
                    if (is_numeric($dateNaissanceValue)) {
                        // Format Excel date (nombre)
                        $dateNaissance = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($dateNaissanceValue)->format('Y-m-d');
                    } else {
                        // Format texte (peut être "JJ/MM/AAAA")
                        $dateObj = DateTime::createFromFormat('d/m/Y', $dateNaissanceValue);
                        if ($dateObj) {
                            $dateNaissance = $dateObj->format('Y-m-d');
                        }
                    }
                } catch (Exception $e) {
                    Log::warning("Impossible de convertir la date de naissance pour l'étudiant $matricule: " . $e->getMessage());
                }
            }
            
            // Créer ou mettre à jour l'étudiant
            $student = Student::updateOrCreate(
                ['matricule' => $matricule, 'classroom_id' => $classroomId],
                [
                    'nom' => $nom,
                    'prenom' => $prenom,
                    'sexe' => $sexe,
                    'date_naissance' => $dateNaissance,
                    'lieu_naissance' => $lieu,
                    'retards' => $retards,
                    'absences' => $absences,
                    'convocations_discipline' => $convocationsDiscipline,
                ]
            );
            
            // Créer ou mettre à jour la moyenne
            Semester1Average::updateOrCreate(
                ['student_id' => $student->id],
                [
                    'moyenne' => $moyenne,
                    'rang' => $rang,
                    'decision' => $decision,
                    'appreciation' => $appreciation,
                    'observations' => $observations,
                ]
            );
            
            $students->push($student);
        }
        
        return $students;
    }
    
    /**
     * Importe les données de l'onglet "Données détaillées"
     *
     * @param \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet
     * @param Collection $students Collection d'étudiants avec leurs IDs
     * @return array Statistiques sur les notes et matières importées
     */
    private function importSubjectMarks($sheet, Collection $students): array
    {
        $marksCount = 0;
        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();
        
        // Identifier les colonnes des matières (ligne d'en-tête)
        // La structure est complexe avec 4 colonnes par matière
        $subjects = [];
        $columnIndex = 3; // Commencer après les colonnes d'identification (IEN, Nom)
        
        // Obtenir le nombre de colonnes (convert letter to number)
        $highestColumnIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($highestColumn);
        
        // Optimisation : Préparer un cache de correspondance matricule -> ID étudiant
        $studentIdsByMatricule = [];
        foreach ($students as $student) {
            $studentIdsByMatricule[$student->matricule] = $student->id;
        }
        
        // Parcourir les colonnes pour trouver les noms de matières
        while ($columnIndex <= $highestColumnIndex) {
            $subjectName = $sheet->getCellByColumnAndRow($columnIndex, 8)->getValue();
            
            if (!empty($subjectName)) {
                // Pour chaque matière, il y a 4 colonnes (Moy DD, Comp D, Moy D, Rang D)
                // On n'a besoin que de la sous-colonne "Moy D" (3ème colonne de chaque groupe)
                $moyDColumnIndex = $columnIndex + 2; // +2 car on commence à la première colonne de la matière
                
                // Créer ou récupérer la matière
                $subject = Subject::firstOrCreate(['nom' => $subjectName]);
                $subjects[$moyDColumnIndex] = $subject->id;
                
                // Avancer de 4 colonnes pour la prochaine matière
                $columnIndex += 4;
            } else {
                // Si la cellule est vide, passer à la colonne suivante
                $columnIndex++;
            }
        }
        
        // Optimisation : préparer des lots d'insertions pour les notes
        $batchMarks = [];
        $batchSize = 100;
        
        // Parcourir les lignes d'étudiants (à partir de la ligne 9)
        for ($row = 9; $row <= $highestRow; $row++) {
            $matricule = $sheet->getCellByColumnAndRow(1, $row)->getValue(); // Colonne A = IEN
            
            // Ignorer les lignes vides
            if (empty($matricule)) {
                continue;
            }
            
            // Vérifier si l'étudiant existe dans notre cache
            if (!isset($studentIdsByMatricule[$matricule])) {
                Log::warning("Étudiant avec matricule $matricule non trouvé dans l'onglet 'Données détaillées'");
                continue;
            }
            
            $studentId = $studentIdsByMatricule[$matricule];
            
            // Pour chaque matière, extraire la note "Moy D"
            foreach ($subjects as $columnIndex => $subjectId) {
                $note = $sheet->getCellByColumnAndRow($columnIndex, $row)->getValue();
                
                // Vérifier si la note est valide
                if (is_numeric($note)) {
                    // Ajouter la note au lot
                    Semester1SubjectMark::updateOrCreate(
                        ['student_id' => $studentId, 'subject_id' => $subjectId],
                        ['note' => floatval($note)]
                    );
                    
                    $marksCount++;
                }
            }
        }
        
        return [
            'marks_count' => $marksCount,
            'subjects_count' => count($subjects),
        ];
    }
}