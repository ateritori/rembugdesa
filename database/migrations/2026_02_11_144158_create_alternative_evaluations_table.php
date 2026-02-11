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
        Schema::create('alternative_evaluations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('decision_session_id');
            $table->unsignedBigInteger('dm_id')->index('alternative_evaluations_dm_id_foreign');
            $table->unsignedBigInteger('alternative_id')->index('alternative_evaluations_alternative_id_foreign');
            $table->unsignedBigInteger('criteria_id')->index('alternative_evaluations_criteria_id_foreign');
            $table->decimal('raw_value', 15, 5);
            $table->decimal('utility_value', 8, 6);
            $table->timestamps();

            $table->unique(['decision_session_id', 'dm_id', 'alternative_id', 'criteria_id'], 'uniq_dm_alt_criteria_session');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alternative_evaluations');
    }
};
