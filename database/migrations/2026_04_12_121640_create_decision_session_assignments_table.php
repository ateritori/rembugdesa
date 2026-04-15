<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('decision_session_assignments', function (Blueprint $table) {
            $table->id();

            // FK utama
            $table->foreignId('decision_session_id')
                ->constrained()
                ->cascadeOnDelete();

            // user nullable (NULL = system)
            $table->foreignId('user_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            // role / capability
            $table->boolean('can_pairwise')->default(false);
            $table->boolean('can_evaluate')->default(false);

            // scope (parameter tertentu)
            $table->foreignId('criteria_id')
                ->nullable()
                ->constrained('criteria')
                ->nullOnDelete();

            $table->timestamps();

            // index penting (biar query cepat)
            $table->index(['decision_session_id']);
            $table->index(['user_id']);
            $table->index(['criteria_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('decision_session_assignments');
    }
};
