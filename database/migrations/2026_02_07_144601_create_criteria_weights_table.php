<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('criteria_weights', function (Blueprint $table) {
            $table->id();

            $table->foreignId('decision_session_id')
                ->constrained('decision_sessions')
                ->cascadeOnDelete();

            $table->foreignId('dm_id')
                ->constrained('users')
                ->cascadeOnDelete();

            // bobot kriteria hasil AHP (key = criteria_id)
            $table->json('weights');

            // Consistency Ratio
            $table->decimal('cr', 6, 4);

            $table->timestamps();

            // satu DM hanya boleh 1 set bobot per sesi
            $table->unique(['decision_session_id', 'dm_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('criteria_weights');
    }
};
