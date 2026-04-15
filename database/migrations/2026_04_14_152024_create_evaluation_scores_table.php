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
        Schema::create('evaluation_scores', function (Blueprint $table) {
            $table->id();

            $table->foreignId('decision_session_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('user_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->foreignId('criteria_id')
                ->constrained('criteria')
                ->cascadeOnDelete();

            $table->foreignId('alternative_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->decimal('value', 12, 4);

            $table->enum('source', ['human', 'system']);

            $table->timestamps();

            // Index penting untuk performa
            $table->index(['decision_session_id', 'criteria_id']);
            $table->index(['decision_session_id', 'alternative_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('evaluation_scores');
    }
};
