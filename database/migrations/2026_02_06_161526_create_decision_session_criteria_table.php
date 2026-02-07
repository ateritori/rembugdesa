<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('decision_session_criteria', function (Blueprint $table) {
            $table->id();
            $table->foreignId('decision_session_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->foreignId('criteria_id')
                ->constrained('criteria')
                ->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['decision_session_id', 'criteria_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('decision_session_criteria');
    }
};
