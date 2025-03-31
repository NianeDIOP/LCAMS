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
    Schema::create('notes_s2', function (Blueprint $table) {
        $table->id();
        $table->foreignId('eleve_id')->constrained('eleves')->onDelete('cascade');
        $table->foreignId('discipline_id')->constrained('disciplines')->onDelete('cascade');
        $table->float('moy_dd', 5, 2)->nullable(); // Moyenne Devoir
        $table->float('comp_d', 5, 2)->nullable(); // Composition
        $table->float('moy_d', 5, 2)->nullable(); // Moyenne Discipline
        $table->integer('rang_d')->nullable(); // Rang Discipline
        $table->foreignId('annee_scolaire_id')->constrained('annee_scolaires')->onDelete('cascade');
        $table->timestamps();
        
        // Unique constraint pour éviter les doublons
        $table->unique(['eleve_id', 'discipline_id', 'annee_scolaire_id']);
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notes_s2');
    }
};
