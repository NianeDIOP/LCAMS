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
        Schema::create('classes', function (Blueprint $table) {
            $table->id();
            $table->string('nom'); // Par exemple: 6ème A, 5ème B, etc.
            $table->foreignId('niveau_id')->constrained()->onDelete('cascade');
            $table->string('annee_scolaire'); // Par exemple: 2024-2025
            $table->integer('effectif_total')->default(0);
            $table->integer('effectif_garcons')->default(0);
            $table->integer('effectif_filles')->default(0);
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('classes');
    }
};