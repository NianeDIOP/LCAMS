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
    Schema::create('annee_scolaires', function (Blueprint $table) {
        $table->id();
        $table->string('libelle');
        $table->date('date_debut')->nullable();
        $table->date('date_fin')->nullable();
        $table->boolean('active')->default(false);
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('annee_scolaires');
    }
};
