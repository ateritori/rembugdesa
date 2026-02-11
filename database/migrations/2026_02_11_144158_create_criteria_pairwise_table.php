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
        Schema::create('criteria_pairwise', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('decision_session_id');
            $table->unsignedBigInteger('dm_id')->index('criteria_pairwise_dm_id_foreign');
            $table->unsignedBigInteger('criteria_id_1')->index('criteria_pairwise_criteria_id_1_foreign');
            $table->unsignedBigInteger('criteria_id_2')->index('criteria_pairwise_criteria_id_2_foreign');
            $table->unsignedTinyInteger('value');
            $table->string('direction', 10);
            $table->timestamps();

            $table->unique(['decision_session_id', 'dm_id', 'criteria_id_1', 'criteria_id_2'], 'uq_pairwise_session_dm_criteria');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('criteria_pairwise');
    }
};
