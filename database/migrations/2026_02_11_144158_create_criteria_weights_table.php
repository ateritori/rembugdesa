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
        Schema::create('criteria_weights', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('decision_session_id');
            $table->unsignedBigInteger('dm_id')->nullable()->index('criteria_weights_dm_id_foreign');
            $table->json('weights');
            $table->decimal('cr', 6, 4)->nullable();
            $table->timestamps();

            $table->unique(['decision_session_id', 'dm_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('criteria_weights');
    }
};
