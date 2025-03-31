<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('donnees_detaillees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('eleve_id')->constrained()->onDelete('cascade');
            $table->foreignId('discipline_id')->constrained()->onDelete('cascade');
            $table->float('valeur')->nullable();
            $table->string('type'); // 'moy_dd', 'comp_d', 'moy_d', 'rang_d'
            $table->foreignId('annee_scolaire_id')->constrained()->onDelete('cascade');
            $table->integer('semestre');
            $table->timestamps();
            
            // Index pour accélérer les requêtes
            $table->index(['eleve_id', 'discipline_id', 'type', 'semestre']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('donnees_detaillees');
    }
};