<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('criteria_scoring_parameters', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('scoring_rule_id');
            $table->string('param_key', 50);
            $table->json('param_value');

            $table->timestamps();

            // 1 rule hanya boleh punya 1 parameter dengan key yang sama
            $table->unique(
                ['scoring_rule_id', 'param_key'],
                'uniq_scoring_rule_param'
            );

            // FK ke scoring rule
            $table->foreign('scoring_rule_id')
                ->references('id')
                ->on('criteria_scoring_rules')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('criteria_scoring_parameters');
    }
};
