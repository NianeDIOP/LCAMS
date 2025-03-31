<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateImportHistoryTable extends Migration
{
    public function up()
    {
        Schema::create('import_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('niveau_id')->constrained('niveaux');
            $table->foreignId('classe_id')->constrained('classes');
            $table->foreignId('annee_scolaire_id')->constrained('annee_scolaires');
            $table->string('fichier_original');
            $table->string('fichier_stocke')->nullable();
            $table->integer('semestre')->comment('1 ou 2');
            $table->integer('nb_eleves')->default(0);
            $table->integer('nb_disciplines')->default(0);
            $table->json('resume')->nullable();
            $table->string('statut')->default('complet');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('import_history');
    }
}