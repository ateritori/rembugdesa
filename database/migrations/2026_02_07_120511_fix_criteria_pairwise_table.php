<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('criteria_pairwise', function (Blueprint $table) {
            $table->id();

            $table->foreignId('decision_session_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('dm_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->foreignId('criteria_id_1')
                ->constrained('criteria')
                ->cascadeOnDelete();

            $table->foreignId('criteria_id_2')
                ->constrained('criteria')
                ->cascadeOnDelete();

            $table->unsignedTinyInteger('value'); // 1–9

            $table->timestamps();

            // NAMA INDEX DIPENDEKKAN (PENTING)
            $table->unique(
                ['decision_session_id', 'dm_id', 'criteria_id_1', 'criteria_id_2'],
                'uq_pairwise_session_dm_criteria'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('criteria_pairwise');
    }
};
