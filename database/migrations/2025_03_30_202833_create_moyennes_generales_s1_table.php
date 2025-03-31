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
    Schema::create('moyennes_generales_s1', function (Blueprint $table) {
        $table->id();
        $table->foreignId('eleve_id')->constrained('eleves')->onDelete('cascade');
        $table->float('moyenne', 5, 2)->nullable();
        $table->integer('rang')->nullable();
        $table->string('retard')->nullable();
        $table->string('absence')->nullable();
        $table->string('conseil_discipline')->nullable();
        $table->text('appreciation')->nullable();
        $table->text('observation')->nullable();
        $table->foreignId('annee_scolaire_id')->constrained('annee_scolaires')->onDelete('cascade');
        $table->timestamps();
        
        // Unique constraint pour éviter les doublons
        $table->unique(['eleve_id', 'annee_scolaire_id']);
    });
}
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('moyennes_generales_s1');
    }
};
