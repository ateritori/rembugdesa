<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('smart_results_dm', function (Blueprint $table) {
            $table->id();

            $table->foreignId('decision_session_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('dm_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->foreignId('alternative_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->decimal('smart_score', 10, 6);
            $table->unsignedInteger('rank_dm');

            $table->timestamps();

            $table->unique([
                'decision_session_id',
                'dm_id',
                'alternative_id'
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('smart_results_dm');
    }
};
