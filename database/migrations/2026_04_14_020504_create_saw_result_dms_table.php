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
        Schema::create('saw_result_dm', function (Blueprint $table) {
            $table->id();
            $table->foreignId('decision_session_id')->constrained()->cascadeOnDelete();
            $table->foreignId('dm_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('alternative_id')->constrained()->cascadeOnDelete();

            $table->double('saw_score');
            $table->integer('rank_dm');

            $table->timestamps();

            $table->unique(['decision_session_id', 'dm_id', 'alternative_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('saw_result_dms');
    }
};
