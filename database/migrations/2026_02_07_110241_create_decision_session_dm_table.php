<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('decision_session_dm', function (Blueprint $table) {
            $table->id();

            $table->foreignId('decision_session_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->timestamps();

            // satu DM tidak boleh di-assign dua kali ke sesi yang sama
            $table->unique(['decision_session_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('decision_session_dm');
    }
};
