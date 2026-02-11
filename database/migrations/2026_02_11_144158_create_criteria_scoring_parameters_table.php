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
        Schema::create('criteria_scoring_parameters', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('scoring_rule_id');
            $table->string('param_key', 50);
            $table->json('param_value');
            $table->timestamps();

            $table->unique(['scoring_rule_id', 'param_key'], 'uniq_scoring_rule_param');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('criteria_scoring_parameters');
    }
};
