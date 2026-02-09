<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('alternative_evaluations', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('decision_session_id');
            $table->unsignedBigInteger('dm_id');
            $table->unsignedBigInteger('alternative_id');
            $table->unsignedBigInteger('criteria_id');

            // Nilai mentah dari DM (ordinal atau numeric)
            $table->decimal('raw_value', 15, 5);

            // Nilai utilitas hasil sistem (0–1)
            $table->decimal('utility_value', 8, 6);

            $table->timestamps();

            /* ================= INDEX & CONSTRAINT ================= */

            // Satu DM hanya boleh satu penilaian per alternatif–kriteria–sesi
            $table->unique(
                ['decision_session_id', 'dm_id', 'alternative_id', 'criteria_id'],
                'uniq_dm_alt_criteria_session'
            );

            $table->foreign('decision_session_id')
                ->references('id')->on('decision_sessions')
                ->onDelete('cascade');

            $table->foreign('dm_id')
                ->references('id')->on('users')
                ->onDelete('cascade');

            $table->foreign('alternative_id')
                ->references('id')->on('alternatives')
                ->onDelete('cascade');

            $table->foreign('criteria_id')
                ->references('id')->on('criteria')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alternative_evaluations');
    }
};
