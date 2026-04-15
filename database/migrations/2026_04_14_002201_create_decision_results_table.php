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
        Schema::create('decision_results', function (Blueprint $table) {
            $table->id();

            // Relasi utama
            $table->foreignId('decision_session_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('alternative_id')
                ->constrained()
                ->cascadeOnDelete();

            // 🔥 Identitas metode
            $table->string('source_method', 30);        // SMART | SAW | SYSTEM | MULTI
            $table->string('aggregation_method', 30);   // DIRECT | BORDA | NESTED_BORDA
            $table->string('pipeline', 60);             // AHP_SMART_BORDA | AHP_SAW_BORDA | SMART | SAW
            $table->string('signature', 64);

            // 🔢 Hasil
            $table->double('score')->nullable();
            $table->integer('rank')->nullable();

            // 🧠 Metadata fleksibel
            $table->json('metadata')->nullable();

            $table->timestamps();

            // 🔒 Anti duplikat
            $table->unique('signature', 'decision_results_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('decision_results');
    }
};
