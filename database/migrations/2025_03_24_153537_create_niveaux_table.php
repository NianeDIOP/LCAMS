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
        Schema::create('niveaux', function (Blueprint $table) {
            $table->id();
            $table->string('code'); // Par exemple: 6eme, 5eme, etc.
            $table->string('nom');  // Par exemple: Sixième, Cinquième, etc.
            $table->string('cycle'); // Par exemple: Collège, Lycée
            $table->integer('ordre'); // Pour trier les niveaux dans l'ordre correct
            $table->boolean('actif')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('niveaux');
    }
};