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
    Schema::create('disciplines', function (Blueprint $table) {
        $table->id();
        $table->string('libelle');
        $table->string('code')->nullable();
        $table->float('coefficient', 4, 2)->default(1.0);
        $table->string('type')->default('principale'); // principale ou sous-discipline
        $table->foreignId('discipline_parent_id')->nullable()->constrained('disciplines')->onDelete('cascade');
        $table->boolean('actif')->default(true);
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('disciplines');
    }
};
