<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('evaluation_results', function (Blueprint $table) {
            $table->id();

            $table->foreignId('decision_session_id')->constrained()->cascadeOnDelete();
            $table->foreignId('alternative_id')->constrained()->cascadeOnDelete();
            $table->foreignId('criteria_id')->constrained('criteria')->cascadeOnDelete();

            $table->enum('method', [
                'smart',
                'saw',
                'weighted_product',
                'topsis'
            ]);

            $table->decimal('evaluation_score', 12, 6);
            $table->decimal('weighted_score', 12, 6);

            $table->timestamps();

            // Prevent duplicate calculation
            $table->unique([
                'decision_session_id',
                'alternative_id',
                'criteria_id',
                'method'
            ], 'uniq_eval_result');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('evaluation_results');
    }
};
