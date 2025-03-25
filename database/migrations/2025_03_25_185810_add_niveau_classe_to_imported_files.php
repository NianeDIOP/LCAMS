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
        Schema::table('imported_files', function (Blueprint $table) {
            $table->unsignedBigInteger('niveau_id')->nullable()->after('type');
            $table->unsignedBigInteger('classe_id')->nullable()->after('niveau_id');
            
            $table->foreign('niveau_id')->references('id')->on('niveaux')->onDelete('set null');
            $table->foreign('classe_id')->references('id')->on('classes')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('imported_files', function (Blueprint $table) {
            $table->dropForeign(['niveau_id']);
            $table->dropForeign(['classe_id']);
            $table->dropColumn(['niveau_id', 'classe_id']);
        });
    }
};