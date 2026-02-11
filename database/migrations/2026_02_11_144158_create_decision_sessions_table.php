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
        Schema::create('decision_sessions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->year('year');
            $table->enum('status', ['draft', 'active', 'criteria', 'alternatives', 'closed'])->default('draft');
            $table->unsignedBigInteger('created_by')->index('decision_sessions_created_by_foreign');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('decision_sessions');
    }
};
