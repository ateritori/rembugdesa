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
        Schema::create('decision_session_dm', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('decision_session_id');
            $table->unsignedBigInteger('user_id')->index('decision_session_dm_user_id_foreign');
            $table->timestamps();

            $table->unique(['decision_session_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('decision_session_dm');
    }
};
