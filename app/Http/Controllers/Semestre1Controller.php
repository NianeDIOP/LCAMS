<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Barryvdh\DomPDF\Facade\Pdf;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use App\Models\Niveau;
use App\Models\Classe;

class Semestre1Controller extends Controller
{
    /**
     * Affiche la page principale du semestre 1
     */
    public function index()
    {
        return view('semestre1.index');
    }

    /**
     * Affiche le tableau de bord du semestre 1
     */
    public function dashboard()
    {
        return view('semestre1.dashboard');
    }

    /**
     * Affiche la page d'analyse des disciplines
     */
    public function analyseDisciples()
    {
        return view('semestre1.analyse');
    }

    /**
     * Affiche la page des rapports
     */
    public function rapports()
    {
        return view('semestre1.rapports');
    }

    /**
     * Affiche la page de base des moyennes
     */
    public function baseMoyennes()
    {
        // Récupérer les fichiers importés
        $importedFiles = DB::table('imported_files AS f')
            ->leftJoin('niveaux AS n', 'f.niveau_id', '=', 'n.id')
            ->leftJoin('classes AS c', 'f.classe_id', '=', 'c.id')
            ->select('f.*', 'n.nom AS niveau_nom', 'c.nom AS classe_nom')
            ->where('f.semestre', 1)
            ->orderBy('f.created_at', 'desc')
            ->get();
            
        // Récupérer tous les niveaux
        $niveaux = Niveau::where('actif', 1)
            ->orWhereNull('actif')
            ->orderBy('ordre')
            ->get();
        
        return view('semestre1.base', compact('importedFiles', 'niveaux'));
    }

    /**
     * Récupère les classes disponibles pour un niveau donné
     */
    public function getClassesByNiveau($niveau_id)
    {
        $classes = Classe::where('niveau_id', $niveau_id)
            ->where(function($query) {
                $query->where('active', 1)
                    ->orWhereNull('active');
            })
            ->orderBy('nom')
            ->get();
        
        return response()->json($classes);
    }

    /**
     * Importe un fichier Excel
     */
    /**
 * Importe un fichier Excel
 */
public function importer(Request $request)
{
    // Valider le fichier et les données
    $request->validate([
        'excel_file' => 'required|file|mimes:xlsx,xls',
        'niveau_id' => 'required|exists:niveaux,id',
        'classe_id' => 'required|exists:classes,id',
    ]);
    
    try {
        $file = $request->file('excel_file');
        $fileName = time() . '_' . $file->getClientOriginalName();
        
        // Assurez-vous que le dossier de stockage existe
        Storage::makeDirectory('imports');
        
        // Sauvegarder le fichier dans le stockage
        $path = $file->storeAs('imports', $fileName);
        
        // Utiliser Storage::path pour obtenir le chemin absolu correct du système de fichiers
        $fullPath = Storage::path($path);
        
        // Charger le fichier Excel avec PhpSpreadsheet
        $spreadsheet = IOFactory::load($fullPath);
        
        // Sélectionner spécifiquement le premier onglet "Moyennes eleves"
        $worksheet = $spreadsheet->getSheet(0); // Index 0 = premier onglet
        
        // Obtenir le titre du fichier (pour l'affichage)
        $fileTitle = $file->getClientOriginalName();
        
        // Extraire les en-têtes (ligne 1)
        $headers = [];
        $highestColumn = 'N'; // Limiter aux colonnes de A à N
        for ($col = 'A'; $col <= $highestColumn; $col++) {
            $headers[] = $worksheet->getCell($col . '1')->getValue();
        }
        
        // Extraire les données à partir de la ligne 2 (après les en-têtes)
        $data = [];
        $highestRow = $worksheet->getHighestRow();
        $validRowCount = 0;
        
        for ($row = 2; $row <= $highestRow; $row++) {
            $rowData = [];
            $isEmpty = true;
            
            for ($col = 'A'; $col <= $highestColumn; $col++) {
                $cellValue = $worksheet->getCell($col . $row)->getValue();
                $rowData[] = $cellValue;
                
                // Vérifier si la cellule a une valeur (uniquement pour les colonnes A à K)
                if ($col <= 'K' && $cellValue !== null && trim((string)$cellValue) !== '') {
                    $isEmpty = false;
                }
            }
            
            // N'ajouter que les lignes non vides
            if (!$isEmpty) {
                // Vérifier et remplir la décision du conseil si nécessaire (colonne L / index 11)
                if (empty($rowData[11])) {
                    $moyenne = floatval(str_replace(',', '.', $rowData[9] ?? 0));
                    $rowData[11] = $this->getDecisionConseil($moyenne);
                }
                
                // Vérifier et remplir l'appréciation si nécessaire (colonne M / index 12)
                if (empty($rowData[12])) {
                    $moyenne = floatval(str_replace(',', '.', $rowData[9] ?? 0));
                    $rowData[12] = $this->getAppreciation($moyenne);
                }
                
                $data[] = $rowData;
                $validRowCount++;
            }
            
            // Limiter à 100 lignes maximum
            if ($validRowCount >= 100) {
                break;
            }
        }
        
        // Créer une entrée dans la table des fichiers importés
        $fileId = DB::table('imported_files')->insertGetId([
            'nom_fichier' => $fileTitle,
            'chemin' => $path,
            'semestre' => 1,
            'type' => $request->input('type_fichier', 'statistiques'),
            'niveau_id' => $request->input('niveau_id'),
            'classe_id' => $request->input('classe_id'),
            'nombre_lignes' => count($data), // Le nombre de lignes de données, sans compter l'en-tête
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        
        // Enregistrer les en-têtes
        DB::table('excel_headers')->insert([
            'file_id' => $fileId,
            'headers' => json_encode($headers),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        
        // Stocker les données dans la table excel_data
        foreach ($data as $rowIndex => $row) {
            DB::table('excel_data')->insert([
                'file_id' => $fileId,
                'row_number' => $rowIndex + 2, // La ligne originale dans le fichier (commence à 2)
                'data' => json_encode($row),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        
        return redirect()->route('semestre1.base')
            ->with('success', 'Fichier importé avec succès. ' . count($data) . ' lignes traitées.');
            
    } catch (\Exception $e) {
        return redirect()->route('semestre1.base')
            ->with('error', 'Erreur lors de l\'importation: ' . $e->getMessage());
    }
}

    /**
     * Affiche les données d'un fichier importé avec filtres
     */
    public function viewImportedFile($id, Request $request)
    {
        // Récupérer les infos du fichier
        $file = DB::table('imported_files')->find($id);
        
        if (!$file) {
            return redirect()->route('semestre1.base')
                ->with('error', 'Fichier non trouvé.');
        }
        
        // Récupérer les en-têtes
        $headerRecord = DB::table('excel_headers')->where('file_id', $id)->first();
        $headers = $headerRecord ? json_decode($headerRecord->headers) : [];
        
        // Récupérer les données
        $rawData = DB::table('excel_data')
            ->where('file_id', $id)
            ->orderBy('row_number')
            ->get()
            ->map(function($item) {
                return [
                    'id' => $item->id,
                    'row_number' => $item->row_number,
                    'data' => json_decode($item->data)
                ];
            });
        
        // Extraction de toutes les données pour manipulation
        $dataArray = [];
        foreach ($rawData as $row) {
            $dataArray[] = [
                'id' => $row['id'],
                'row_number' => $row['row_number'],
                'data' => $row['data']
            ];
        }
        
        // Paramètres de filtrage
        $sortField = $request->input('sort', 'moy'); // Par défaut, tri par moyenne
        $sortDirection = $request->input('direction', 'desc'); // Par défaut, descendant (meilleur à pire)
        $searchTerm = $request->input('search', '');
        $minMoy = $request->input('min_moy', '');
        $maxMoy = $request->input('max_moy', '');
        
        // Filtrage par recherche (nom, prénom ou IEN)
        if (!empty($searchTerm)) {
            $dataArray = array_filter($dataArray, function($row) use ($searchTerm) {
                $ien = strtolower($row['data'][0] ?? ''); // Colonne A: IEN
                $prenom = strtolower($row['data'][1] ?? ''); // Colonne B: Prénom
                $nom = strtolower($row['data'][2] ?? ''); // Colonne C: Nom
                
                $searchLower = strtolower($searchTerm);
                
                return strpos($ien, $searchLower) !== false || 
                       strpos($prenom, $searchLower) !== false || 
                       strpos($nom, $searchLower) !== false;
            });
        }
        
        // Filtrage par plage de moyenne
        if (!empty($minMoy)) {
            $dataArray = array_filter($dataArray, function($row) use ($minMoy) {
                $moy = floatval(str_replace(',', '.', $row['data'][9] ?? 0)); // Colonne J: Moy
                return $moy >= floatval($minMoy);
            });
        }
        
        if (!empty($maxMoy)) {
            $dataArray = array_filter($dataArray, function($row) use ($maxMoy) {
                $moy = floatval(str_replace(',', '.', $row['data'][9] ?? 0)); // Colonne J: Moy
                return $moy <= floatval($maxMoy);
            });
        }
        
        // Tri des données
        if ($sortField === 'moy') {
            usort($dataArray, function($a, $b) use ($sortDirection) {
                $moyA = floatval(str_replace(',', '.', $a['data'][9] ?? 0));
                $moyB = floatval(str_replace(',', '.', $b['data'][9] ?? 0));
                
                if ($sortDirection === 'asc') {
                    return $moyA <=> $moyB;
                } else {
                    return $moyB <=> $moyA;
                }
            });
        } elseif ($sortField === 'rang') {
            usort($dataArray, function($a, $b) use ($sortDirection) {
                $rangA = intval($a['data'][10] ?? 0);
                $rangB = intval($b['data'][10] ?? 0);
                
                if ($sortDirection === 'asc') {
                    return $rangA <=> $rangB;
                } else {
                    return $rangB <=> $rangA;
                }
            });
        } elseif ($sortField === 'nom') {
            usort($dataArray, function($a, $b) use ($sortDirection) {
                $nomA = strtolower($a['data'][2] ?? '');
                $nomB = strtolower($b['data'][2] ?? '');
                
                if ($sortDirection === 'asc') {
                    return strcmp($nomA, $nomB);
                } else {
                    return strcmp($nomB, $nomA);
                }
            });
        }
        
        // Statistiques
        $stats = [
            'count' => count($dataArray),
            'moyenne' => 0,
            'min' => 0,
            'max' => 0
        ];
        
        if (count($dataArray) > 0) {
            $moyennes = array_map(function($row) {
                return floatval(str_replace(',', '.', $row['data'][9] ?? 0));
            }, $dataArray);
            
            $stats['moyenne'] = array_sum($moyennes) / count($moyennes);
            $stats['min'] = min($moyennes);
            $stats['max'] = max($moyennes);
        }
        
        // Préparer les données filtrées pour la vue
        $data = array_values($dataArray);
        
        return view('semestre1.view_file', compact('file', 'data', 'headers', 'sortField', 
            'sortDirection', 'searchTerm', 'minMoy', 'maxMoy', 'stats'));
    }

    /**
     * Exporte les données filtrées en PDF
     */
    public function exportPDF($id, Request $request)
    {
        // Récupérer les infos du fichier avec niveau et classe
        $file = DB::table('imported_files AS f')
            ->leftJoin('niveaux AS n', 'f.niveau_id', '=', 'n.id')
            ->leftJoin('classes AS c', 'f.classe_id', '=', 'c.id')
            ->select('f.*', 'n.nom AS niveau_nom', 'c.nom AS classe_nom')
            ->where('f.id', $id)
            ->first();
        
        if (!$file) {
            return redirect()->route('semestre1.base')
                ->with('error', 'Fichier non trouvé.');
        }
        
        // Récupérer les en-têtes
        $headerRecord = DB::table('excel_headers')->where('file_id', $id)->first();
        $headers = $headerRecord ? json_decode($headerRecord->headers) : [];
        
        // Récupérer les données
        $rawData = DB::table('excel_data')
            ->where('file_id', $id)
            ->orderBy('row_number')
            ->get()
            ->map(function($item) {
                return [
                    'id' => $item->id,
                    'row_number' => $item->row_number,
                    'data' => json_decode($item->data)
                ];
            });
        
        // Extraction de toutes les données pour manipulation
        $dataArray = [];
        foreach ($rawData as $row) {
            $dataArray[] = [
                'id' => $row['id'],
                'row_number' => $row['row_number'],
                'data' => $row['data']
            ];
        }
        
        // Paramètres de filtrage (repris de viewImportedFile)
        $sortField = $request->input('sort', 'moy');
        $sortDirection = $request->input('direction', 'desc');
        $searchTerm = $request->input('search', '');
        $minMoy = $request->input('min_moy', '');
        $maxMoy = $request->input('max_moy', '');
        
        // Filtrage par recherche (nom, prénom ou IEN)
        if (!empty($searchTerm)) {
            $dataArray = array_filter($dataArray, function($row) use ($searchTerm) {
                $ien = strtolower($row['data'][0] ?? '');
                $prenom = strtolower($row['data'][1] ?? '');
                $nom = strtolower($row['data'][2] ?? '');
                
                $searchLower = strtolower($searchTerm);
                
                return strpos($ien, $searchLower) !== false || 
                       strpos($prenom, $searchLower) !== false || 
                       strpos($nom, $searchLower) !== false;
            });
        }
        
        // Filtrage par plage de moyenne
        if (!empty($minMoy)) {
            $dataArray = array_filter($dataArray, function($row) use ($minMoy) {
                $moy = floatval(str_replace(',', '.', $row['data'][9] ?? 0));
                return $moy >= floatval($minMoy);
            });
        }
        
        if (!empty($maxMoy)) {
            $dataArray = array_filter($dataArray, function($row) use ($maxMoy) {
                $moy = floatval(str_replace(',', '.', $row['data'][9] ?? 0));
                return $moy <= floatval($maxMoy);
            });
        }
        
        // Tri des données
        if ($sortField === 'moy') {
            usort($dataArray, function($a, $b) use ($sortDirection) {
                $moyA = floatval(str_replace(',', '.', $a['data'][9] ?? 0));
                $moyB = floatval(str_replace(',', '.', $b['data'][9] ?? 0));
                
                if ($sortDirection === 'asc') {
                    return $moyA <=> $moyB;
                } else {
                    return $moyB <=> $moyA;
                }
            });
        } elseif ($sortField === 'rang') {
            usort($dataArray, function($a, $b) use ($sortDirection) {
                $rangA = intval($a['data'][10] ?? 0);
                $rangB = intval($b['data'][10] ?? 0);
                
                if ($sortDirection === 'asc') {
                    return $rangA <=> $rangB;
                } else {
                    return $rangB <=> $rangA;
                }
            });
        } elseif ($sortField === 'nom') {
            usort($dataArray, function($a, $b) use ($sortDirection) {
                $nomA = strtolower($a['data'][2] ?? '');
                $nomB = strtolower($b['data'][2] ?? '');
                
                if ($sortDirection === 'asc') {
                    return strcmp($nomA, $nomB);
                } else {
                    return strcmp($nomB, $nomA);
                }
            });
        }
        
        // Préparer les données filtrées pour le PDF
        $data = array_values($dataArray);
        
        // Récupérer les informations de l'établissement
        $etablissement = DB::table('etablissements')->first();
        if ($etablissement) {
            $file->nom_etablissement = $etablissement->nom;
            $file->adresse_etablissement = $etablissement->adresse;
            $file->annee_scolaire = $etablissement->annee_scolaire;
            $file->academie = $etablissement->academie;
            $file->ief = $etablissement->ief;
        }
        
        // Générer le PDF avec les options de mise en page correctes
        $pdf = PDF::loadView('semestre1.export_pdf', compact('data', 'headers', 'file', 'searchTerm', 'minMoy', 'maxMoy'));
        $pdf->setPaper('a4', 'portrait'); // Format portrait comme demandé
        $pdf->setOptions([
            'dpi' => 150,
            'defaultFont' => 'times',
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true
        ]);
    
        return $pdf->download('Liste_Moyennes_S1_' . date('YmdHis') . '.pdf');
    }

    /**
     * Exporte les données filtrées en Excel
     */
    public function exportExcel($id, Request $request)
    {
        // Récupérer les infos du fichier
        $file = DB::table('imported_files')->find($id);
        
        if (!$file) {
            return redirect()->route('semestre1.base')
                ->with('error', 'Fichier non trouvé.');
        }
        
        // Récupérer les en-têtes
        $headerRecord = DB::table('excel_headers')->where('file_id', $id)->first();
        $headers = $headerRecord ? json_decode($headerRecord->headers) : [];
        
        // Récupérer les données
        $rawData = DB::table('excel_data')
            ->where('file_id', $id)
            ->orderBy('row_number')
            ->get()
            ->map(function($item) {
                return [
                    'id' => $item->id,
                    'row_number' => $item->row_number,
                    'data' => json_decode($item->data)
                ];
            });
        
        // Extraction de toutes les données pour manipulation
        $dataArray = [];
        foreach ($rawData as $row) {
            $dataArray[] = [
                'id' => $row['id'],
                'row_number' => $row['row_number'],
                'data' => $row['data']
            ];
        }
        
        // Paramètres de filtrage (repris de viewImportedFile)
        $sortField = $request->input('sort', 'moy');
        $sortDirection = $request->input('direction', 'desc');
        $searchTerm = $request->input('search', '');
        $minMoy = $request->input('min_moy', '');
        $maxMoy = $request->input('max_moy', '');
        
        // Filtrage par recherche (nom, prénom ou IEN)
        if (!empty($searchTerm)) {
            $dataArray = array_filter($dataArray, function($row) use ($searchTerm) {
                $ien = strtolower($row['data'][0] ?? '');
                $prenom = strtolower($row['data'][1] ?? '');
                $nom = strtolower($row['data'][2] ?? '');
                
                $searchLower = strtolower($searchTerm);
                
                return strpos($ien, $searchLower) !== false || 
                       strpos($prenom, $searchLower) !== false || 
                       strpos($nom, $searchLower) !== false;
            });
        }
        
        // Filtrage par plage de moyenne
        if (!empty($minMoy)) {
            $dataArray = array_filter($dataArray, function($row) use ($minMoy) {
                $moy = floatval(str_replace(',', '.', $row['data'][9] ?? 0));
                return $moy >= floatval($minMoy);
            });
        }
        
        if (!empty($maxMoy)) {
            $dataArray = array_filter($dataArray, function($row) use ($maxMoy) {
                $moy = floatval(str_replace(',', '.', $row['data'][9] ?? 0));
                return $moy <= floatval($maxMoy);
            });
        }
        
        // Tri des données
        if ($sortField === 'moy') {
            usort($dataArray, function($a, $b) use ($sortDirection) {
                $moyA = floatval(str_replace(',', '.', $a['data'][9] ?? 0));
                $moyB = floatval(str_replace(',', '.', $b['data'][9] ?? 0));
                
                if ($sortDirection === 'asc') {
                    return $moyA <=> $moyB;
                } else {
                    return $moyB <=> $moyA;
                }
            });
        } elseif ($sortField === 'rang') {
            usort($dataArray, function($a, $b) use ($sortDirection) {
                $rangA = intval($a['data'][10] ?? 0);
                $rangB = intval($b['data'][10] ?? 0);
                
                if ($sortDirection === 'asc') {
                    return $rangA <=> $rangB;
                } else {
                    return $rangB <=> $rangA;
                }
            });
        } elseif ($sortField === 'nom') {
            usort($dataArray, function($a, $b) use ($sortDirection) {
                $nomA = strtolower($a['data'][2] ?? '');
                $nomB = strtolower($b['data'][2] ?? '');
                
                if ($sortDirection === 'asc') {
                    return strcmp($nomA, $nomB);
                } else {
                    return strcmp($nomB, $nomA);
                }
            });
        }
        
        // Préparer les données filtrées pour l'Excel
        $data = array_values($dataArray);
        
        // Créer un nouveau fichier Excel
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Ajouter les en-têtes spécifiés
        $exportHeaders = ['IEN', 'Prenom', 'Nom', 'Sexe', 'Date naissance', 'Lieu naissance', 
                          'Retard', 'Absence', 'C. D.', 'Moy', 'Rang', 'Appréciation'];
        
        // Utiliser setCellValue au lieu de setCellValueByColumnAndRow
        $columns = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L'];
        foreach ($columns as $index => $column) {
            $sheet->setCellValue($column . '1', $exportHeaders[$index]);
        }
        
        // Ajouter les données
        $rowIndex = 2;
        foreach ($data as $row) {
            $colIndices = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 12]; // Indices correspondant aux colonnes spécifiées
            
            foreach ($columns as $index => $column) {
                $colIdx = $colIndices[$index];
                $sheet->setCellValue($column . $rowIndex, $row['data'][$colIdx] ?? '');
            }
            
            $rowIndex++;
        }
        
        // Générer le fichier Excel
        $writer = new Xlsx($spreadsheet);
        $fileName = 'export_' . $file->nom_fichier . '.xlsx';
        $tempFile = tempnam(sys_get_temp_dir(), 'excel');
        $writer->save($tempFile);
        
        return response()->download($tempFile, $fileName)->deleteFileAfterSend(true);
    }

    /**
     * Supprime un fichier importé
     */
    public function deleteImportedFile($id)
    {
        try {
            // Récupérer les infos du fichier
            $file = DB::table('imported_files')->find($id);
            
            if (!$file) {
                return redirect()->route('semestre1.base')
                    ->with('error', 'Fichier non trouvé.');
            }
            
            // Supprimer le fichier physique
            Storage::delete($file->chemin);
            
            // Supprimer les en-têtes associés
            DB::table('excel_headers')->where('file_id', $id)->delete();
            
            // Supprimer les données associées
            DB::table('excel_data')->where('file_id', $id)->delete();
            
            // Supprimer l'entrée dans la table des fichiers
            DB::table('imported_files')->delete($id);
            
            return redirect()->route('semestre1.base')
                ->with('success', 'Fichier supprimé avec succès.');
                
        } catch (\Exception $e) {
            return redirect()->route('semestre1.base')
                ->with('error', 'Erreur lors de la suppression: ' . $e->getMessage());
        }
    }

    /**
     * Détermine la décision du conseil en fonction de la moyenne
     */
    private function getDecisionConseil($moyenne)
    {
        // Convertir en nombre flottant pour s'assurer que la comparaison fonctionne correctement
        $moyenne = floatval(str_replace(',', '.', $moyenne));
        
        if ($moyenne < 3) {
            return "Risque l'exclusion";
        } elseif ($moyenne < 6) {
            return "Risque de Redoubler";
        } elseif ($moyenne < 10) {
            return "Insuffisant";
        } elseif ($moyenne < 14) {
            return "Peut Mieux Faire";
        } elseif ($moyenne < 16) {
            return "Satisfaisant doit continuer";
        } else {
            return "Travail excellent";
        }
    }

    /**
     * Détermine l'appréciation en fonction de la moyenne
     */
    private function getAppreciation($moyenne)
    {
        // Convertir en nombre flottant pour s'assurer que la comparaison fonctionne correctement
        $moyenne = floatval(str_replace(',', '.', $moyenne));
        
        if ($moyenne < 3) {
            return "Blâme";
        } elseif ($moyenne < 6) {
            return "Avertissement";
        } elseif ($moyenne < 8) {
            return "Doit redoubler d'effort";
        } elseif ($moyenne < 12) {
            return "Passable";
        } elseif ($moyenne < 14) {
            return "Tableau d'honneur";
        } elseif ($moyenne < 16) {
            return "Encouragements";
        } else {
            return "Félicitations";
        }
    }
}