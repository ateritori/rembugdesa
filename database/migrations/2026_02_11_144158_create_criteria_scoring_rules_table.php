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
        Schema::create('criteria_scoring_rules', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('decision_session_id')->nullable()->index('criteria_scoring_rules_decision_session_id_foreign');
            $table->unsignedBigInteger('criteria_id')->unique('unique_criteria_rule');
            $table->enum('input_type', ['scale', 'numeric']);
            $table->enum('preference_type', ['linear', 'concave', 'convex']);
            $table->enum('utility_mode', ['system', 'custom'])->default('system');
            $table->decimal('curve_param', 5, 3)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('criteria_scoring_rules');
    }
};
