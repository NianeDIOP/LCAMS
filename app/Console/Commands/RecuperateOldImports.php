<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Eleve;
use App\Models\AnneeScolaire;
use App\Models\ImportHistory;
use App\Models\NoteS1;
use App\Models\NoteS2;
use App\Models\Discipline;
use Illuminate\Support\Facades\DB;

class RecuperateOldImports extends Command
{
    protected $signature = 'imports:recuperate';
    protected $description = 'Récupère les anciennes importations non enregistrées dans la table import_history';

    public function handle()
    {
        $this->info('Récupération des anciennes importations...');
        
        $anneeScolaire = AnneeScolaire::where('active', true)->first();
        
        if (!$anneeScolaire) {
            $this->error('Aucune année scolaire active trouvée.');
            return 1;
        }
        
        // Récupérer toutes les classes avec des élèves pour le semestre 1
        $classesS1 = DB::table('eleves')
                    ->join('classes', 'eleves.classe_id', '=', 'classes.id')
                    ->join('moyennes_generales_s1', 'eleves.id', '=', 'moyennes_generales_s1.eleve_id')
                    ->select('classes.id', 'classes.libelle', 'classes.niveau_id', DB::raw('COUNT(DISTINCT eleves.id) as nb_eleves'))
                    ->where('eleves.annee_scolaire_id', $anneeScolaire->id)
                    ->groupBy('classes.id', 'classes.libelle', 'classes.niveau_id')
                    ->get();
        
        $this->info('Classes avec données Semestre 1 trouvées: ' . $classesS1->count());
        
        foreach ($classesS1 as $classe) {
            // Vérifier si déjà importé
            $existingImport = ImportHistory::where('classe_id', $classe->id)
                                        ->where('semestre', 1)
                                        ->where('annee_scolaire_id', $anneeScolaire->id)
                                        ->first();
            
            if (!$existingImport) {
                // Compter les disciplines
                $nb_disciplines = Discipline::whereHas('notesS1', function($query) use ($classe, $anneeScolaire) {
                    $query->whereHas('eleve', function($q) use ($classe, $anneeScolaire) {
                        $q->where('classe_id', $classe->id)
                          ->where('annee_scolaire_id', $anneeScolaire->id);
                    });
                })->count();
                
                // Créer l'entrée d'historique
                ImportHistory::create([
                    'niveau_id' => $classe->niveau_id,
                    'classe_id' => $classe->id,
                    'annee_scolaire_id' => $anneeScolaire->id,
                    'fichier_original' => 'Importation antérieure - ' . $classe->libelle,
                    'semestre' => 1,
                    'nb_eleves' => $classe->nb_eleves,
                    'nb_disciplines' => $nb_disciplines,
                    'statut' => 'complet',
                    'resume' => [
                        'nb_eleves' => $classe->nb_eleves,
                        'nb_disciplines' => $nb_disciplines,
                        'date_import' => 'Antérieure à l\'historique'
                    ],
                    'created_at' => now()->subHours(rand(1, 100)) // Pour différencier les dates
                ]);
                
                $this->info('Importation S1 récupérée pour la classe: ' . $classe->libelle);
            }
        }
        
        // Récupérer toutes les classes avec des élèves pour le semestre 2
        $classesS2 = DB::table('eleves')
                    ->join('classes', 'eleves.classe_id', '=', 'classes.id')
                    ->join('moyennes_generales_s2', 'eleves.id', '=', 'moyennes_generales_s2.eleve_id')
                    ->select('classes.id', 'classes.libelle', 'classes.niveau_id', DB::raw('COUNT(DISTINCT eleves.id) as nb_eleves'))
                    ->where('eleves.annee_scolaire_id', $anneeScolaire->id)
                    ->groupBy('classes.id', 'classes.libelle', 'classes.niveau_id')
                    ->get();
        
        $this->info('Classes avec données Semestre 2 trouvées: ' . $classesS2->count());
        
        foreach ($classesS2 as $classe) {
            // Vérifier si déjà importé
            $existingImport = ImportHistory::where('classe_id', $classe->id)
                                        ->where('semestre', 2)
                                        ->where('annee_scolaire_id', $anneeScolaire->id)
                                        ->first();
            
            if (!$existingImport) {
                // Compter les disciplines
                $nb_disciplines = Discipline::whereHas('notesS2', function($query) use ($classe, $anneeScolaire) {
                    $query->whereHas('eleve', function($q) use ($classe, $anneeScolaire) {
                        $q->where('classe_id', $classe->id)
                          ->where('annee_scolaire_id', $anneeScolaire->id);
                    });
                })->count();
                
                // Créer l'entrée d'historique
                ImportHistory::create([
                    'niveau_id' => $classe->niveau_id,
                    'classe_id' => $classe->id,
                    'annee_scolaire_id' => $anneeScolaire->id,
                    'fichier_original' => 'Importation antérieure - ' . $classe->libelle,
                    'semestre' => 2,
                    'nb_eleves' => $classe->nb_eleves,
                    'nb_disciplines' => $nb_disciplines,
                    'statut' => 'complet',
                    'resume' => [
                        'nb_eleves' => $classe->nb_eleves,
                        'nb_disciplines' => $nb_disciplines,
                        'date_import' => 'Antérieure à l\'historique'
                    ],
                    'created_at' => now()->subHours(rand(1, 100)) // Pour différencier les dates
                ]);
                
                $this->info('Importation S2 récupérée pour la classe: ' . $classe->libelle);
            }
        }
        
        $this->info('Récupération terminée.');
        
        return 0;
    }
}