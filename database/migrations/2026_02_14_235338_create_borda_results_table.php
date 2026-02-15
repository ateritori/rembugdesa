<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('borda_results', function (Blueprint $table) {
            $table->id();

            $table->foreignId('decision_session_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('alternative_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->unsignedInteger('borda_score');
            $table->unsignedInteger('final_rank');

            $table->timestamps();

            $table->unique([
                'decision_session_id',
                'alternative_id'
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('borda_results');
    }
};
