<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ClearDatabaseExceptConfig extends Command
{
    protected $signature = 'db:clear-except-config';
    protected $description = 'Vide toutes les tables sauf la table configurations';

    public function handle()
    {
        // Désactiver les contraintes de clé étrangère pour pouvoir vider les tables
        DB::statement('PRAGMA foreign_keys = OFF');

        // Récupérer toutes les tables
        $tables = DB::select("SELECT name FROM sqlite_master WHERE type='table'");

        $tablesCount = 0;
        $rowsCount = 0;

        foreach ($tables as $table) {
            $tableName = $table->name;
            
            // Ignorer les tables système de SQLite et la table configurations
            if ($tableName !== 'configurations' && $tableName !== 'sqlite_sequence' && $tableName !== 'migrations') {
                // Compter le nombre de lignes avant la suppression
                $count = DB::table($tableName)->count();
                $rowsCount += $count;
                
                // Vider la table
                DB::table($tableName)->truncate();
                
                $this->info("Table '{$tableName}' vidée. ({$count} lignes supprimées)");
                $tablesCount++;
            }
        }

        // Réactiver les contraintes de clé étrangère
        DB::statement('PRAGMA foreign_keys = ON');

        $this->info("Opération terminée! {$tablesCount} tables vidées, {$rowsCount} lignes supprimées au total.");
        $this->info("La table 'configurations' a été préservée.");
        
        return 0;
    }
}