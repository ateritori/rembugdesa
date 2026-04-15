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
        Schema::create('alternatives', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('decision_session_id');
            $table->string('code', 10);
            $table->string('name');
            $table->unsignedInteger('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->unsignedBigInteger('rab')->nullable();
            $table->unsignedInteger('coverage')->nullable();
            $table->unsignedInteger('beneficiaries')->nullable();
            // Relasi ke sektor (criteria level 1)
            $table->foreignId('criteria_id')->nullable()->constrained('criteria')->nullOnDelete();
            $table->softDeletes();
            $table->timestamps();

            $table->unique(['decision_session_id', 'code']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alternatives');
    }
};
