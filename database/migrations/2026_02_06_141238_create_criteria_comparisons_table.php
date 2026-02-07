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
        Schema::create('criteria_comparisons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('criteria_1_id')->constrained('criteria')->cascadeOnDelete();
            $table->foreignId('criteria_2_id')->constrained('criteria')->cascadeOnDelete();
            $table->integer('value'); // -9 s.d +9 (skala Saaty)
            $table->timestamps();

            $table->unique(
                ['user_id', 'criteria_1_id', 'criteria_2_id'],
                'uq_user_criteria_pair'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('criteria_comparisons');
    }
};
