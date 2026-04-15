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
        Schema::create('evaluation_aggregations', function (Blueprint $table) {
            $table->id();

            $table->foreignId('decision_session_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('alternative_id')->constrained()->cascadeOnDelete();

            $table->string('method'); // smart, saw, wp
            $table->double('score', 15, 6);

            $table->timestamps();

            // 🔥 penting
            $table->unique([
                'decision_session_id',
                'user_id',
                'alternative_id',
                'method'
            ], 'unique_eval_agg');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('evaluation_aggregations');
    }
};
