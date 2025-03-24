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
            $table->string('code'); // Par exemple: MATH, FR, etc.
            $table->string('nom');  // Par exemple: Mathématiques, Français, etc.
            $table->string('groupe')->nullable(); // Par exemple: Scientifique, Littéraire, etc.
            $table->decimal('coefficient', 4, 2)->default(1.00);
            $table->boolean('active')->default(true);
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