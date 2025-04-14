<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Suppression de la table import_histories si elle existe
        if (Schema::hasTable('import_histories')) {
            Schema::dropIfExists('import_histories');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Cette migration supprime définitivement la table, il n'y a pas de retour en arrière possible
        // Pour récupérer la table, il faudrait recréer la migration create_import_histories_table
    }
};
