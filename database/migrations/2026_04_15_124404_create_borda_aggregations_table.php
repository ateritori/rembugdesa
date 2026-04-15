<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('borda_aggregations', function (Blueprint $table) {
            $table->id();

            $table->foreignId('decision_session_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->enum('method', ['SMART', 'SAW']);

            $table->enum('level', ['group', 'system', 'final']);

            $table->enum('source', [
                'partisipatif',
                'strategis',
                'system',
                'final'
            ]);

            $table->foreignId('alternative_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->double('borda_score');

            $table->integer('rank');

            $table->timestamps();

            $table->unique([
                'decision_session_id',
                'method',
                'level',
                'source',
                'alternative_id'
            ], 'borda_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('borda_aggregations');
    }
};
