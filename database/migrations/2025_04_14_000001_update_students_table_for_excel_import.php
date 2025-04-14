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
        Schema::table('students', function (Blueprint $table) {
            // La migration existante contient déjà nom, prenom, matricule, classroom_id, sexe
            $table->string('lieu_naissance')->nullable()->after('sexe');
            $table->date('date_naissance')->nullable()->after('sexe');
            $table->integer('retards')->nullable()->after('lieu_naissance');
            $table->integer('absences')->nullable()->after('retards');
            $table->integer('convocations_discipline')->nullable()->after('absences');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn('date_naissance');
            $table->dropColumn('lieu_naissance');
            $table->dropColumn('retards');
            $table->dropColumn('absences');
            $table->dropColumn('convocations_discipline');
        });
    }
};