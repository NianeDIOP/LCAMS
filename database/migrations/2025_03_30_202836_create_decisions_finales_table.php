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
    Schema::create('decisions_finales', function (Blueprint $table) {
        $table->id();
        $table->foreignId('eleve_id')->constrained('eleves')->onDelete('cascade');
        $table->string('decision')->nullable();
        $table->float('moyenne_annuelle', 5, 2)->nullable();
        $table->integer('rang_annuel')->nullable();
        $table->foreignId('annee_scolaire_id')->constrained('annee_scolaires')->onDelete('cascade');
        $table->text('observation')->nullable();
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
        Schema::dropIfExists('decisions_finales');
    }
};
