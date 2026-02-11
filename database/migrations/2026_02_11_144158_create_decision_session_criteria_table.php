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
        Schema::create('decision_session_criteria', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('decision_session_id');
            $table->unsignedBigInteger('criteria_id')->index('decision_session_criteria_criteria_id_foreign');
            $table->timestamps();

            $table->unique(['decision_session_id', 'criteria_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('decision_session_criteria');
    }
};
