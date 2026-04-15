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
        Schema::create('system_rankings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('decision_session_id')->constrained()->cascadeOnDelete();
            $table->foreignId('alternative_id')->constrained()->cascadeOnDelete();

            $table->decimal('score', 10, 6);
            $table->unsignedInteger('rank');

            $table->timestamps();

            $table->unique(['decision_session_id', 'alternative_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('system_rankings');
    }
};
