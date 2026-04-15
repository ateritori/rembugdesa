<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('dm_scores', function (Blueprint $table) {
            $table->id();

            $table->foreignId('decision_session_id')->constrained()->cascadeOnDelete();
            $table->foreignId('dm_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('alternative_id')->constrained()->cascadeOnDelete();

            $table->enum('method', ['saw', 'smart']);
            $table->decimal('score', 12, 6);

            $table->timestamps();

            $table->unique([
                'decision_session_id',
                'dm_id',
                'alternative_id',
                'method'
            ], 'unique_dm_score');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dm_scores');
    }
};
