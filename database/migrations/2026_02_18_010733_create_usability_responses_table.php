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
        Schema::create('usability_responses', function (Blueprint $table) {
            $table->id();

            $table->foreignId('usability_instrument_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            // Role diambil dari sistem role (Spatie), disimpan sebagai snapshot
            $table->string('role', 50)->nullable()->index();

            // Nullable untuk evaluasi global (tanpa sesi keputusan)
            $table->foreignId('decision_session_id')
                ->nullable()
                ->constrained('decision_sessions')
                ->nullOnDelete()
                ->index();

            // Skor SUS (0–100), disimpan sebagai cache hasil
            $table->decimal('total_score', 5, 2)->nullable();

            $table->timestamps();

            // Satu user hanya boleh mengisi sekali per instrumen per sesi
            $table->unique(
                ['usability_instrument_id', 'user_id', 'decision_session_id'],
                'uq_usability_response_once'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('usability_responses');
    }
};
