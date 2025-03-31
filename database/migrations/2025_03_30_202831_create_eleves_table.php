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
    Schema::create('eleves', function (Blueprint $table) {
        $table->id();
        $table->string('ien')->unique(); // Identifiant Élève National
        $table->string('prenom');
        $table->string('nom');
        $table->enum('sexe', ['M', 'F', 'H'])->nullable();
        $table->date('date_naissance')->nullable();
        $table->string('lieu_naissance')->nullable();
        $table->foreignId('classe_id')->constrained('classes')->onDelete('cascade');
        $table->foreignId('annee_scolaire_id')->constrained('annee_scolaires')->onDelete('cascade');
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('eleves');
    }
};
