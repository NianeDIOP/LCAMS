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
        if (!Schema::hasColumn('moyennes_generales_s1', 'decision')) {
            Schema::table('moyennes_generales_s1', function (Blueprint $table) {
                $table->string('decision')->nullable()->after('rang');
            });
        }

        if (!Schema::hasColumn('moyennes_generales_s1', 'appreciation')) {
            Schema::table('moyennes_generales_s1', function (Blueprint $table) {
                $table->string('appreciation')->nullable()->after('decision');
            });
        }

        if (!Schema::hasColumn('moyennes_generales_s2', 'decision')) {
            Schema::table('moyennes_generales_s2', function (Blueprint $table) {
                $table->string('decision')->nullable()->after('rang');
            });
        }

        if (!Schema::hasColumn('moyennes_generales_s2', 'appreciation')) {
            Schema::table('moyennes_generales_s2', function (Blueprint $table) {
                $table->string('appreciation')->nullable()->after('decision');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('moyennes_generales_s1', function (Blueprint $table) {
            if (Schema::hasColumn('moyennes_generales_s1', 'decision')) {
                $table->dropColumn('decision');
            }
            if (Schema::hasColumn('moyennes_generales_s1', 'appreciation')) {
                $table->dropColumn('appreciation');
            }
        });

        Schema::table('moyennes_generales_s2', function (Blueprint $table) {
            if (Schema::hasColumn('moyennes_generales_s2', 'decision')) {
                $table->dropColumn('decision');
            }
            if (Schema::hasColumn('moyennes_generales_s2', 'appreciation')) {
                $table->dropColumn('appreciation');
            }
        });
    }
};