<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('criteria_scoring_rules', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('decision_session_id')->nullable();
            $table->unsignedBigInteger('criteria_id');

            $table->enum('input_type', ['ordinal', 'numeric']);
            $table->enum('preference_type', ['linear', 'concave', 'convex']);
            $table->enum('utility_mode', ['system', 'custom'])->default('system');

            $table->decimal('curve_param', 5, 3)->nullable();

            $table->timestamps();

            $table->foreign('criteria_id')
                ->references('id')->on('criteria')
                ->onDelete('cascade');

            $table->foreign('decision_session_id')
                ->references('id')->on('decision_sessions')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('criteria_scoring_rules');
    }
};
