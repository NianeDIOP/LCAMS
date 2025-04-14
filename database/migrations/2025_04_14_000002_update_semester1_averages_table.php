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
        Schema::table('semester1_averages', function (Blueprint $table) {
            // Ajout des champs pour la décision et les observations
            $table->string('decision')->nullable()->after('rang');
            $table->text('observations')->nullable()->after('appreciation');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('semester1_averages', function (Blueprint $table) {
            $table->dropColumn('decision');
            $table->dropColumn('observations');
        });
    }
};