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
        Schema::table('criteria_pairwise', function (Blueprint $table) {
            $table->string('direction', 10)
                ->after('value');
        });
    }

    public function down(): void
    {
        Schema::table('criteria_pairwise', function (Blueprint $table) {
            $table->dropColumn('direction');
        });
    }
};
