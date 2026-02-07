<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('alternatives', function (Blueprint $table) {
            $table->id();

            $table->foreignId('decision_session_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('code', 10); // A1, A2, dst
            $table->string('name');

            $table->unsignedInteger('order')->default(0);
            $table->boolean('is_active')->default(true);

            $table->softDeletes();
            $table->timestamps();

            // kode alternatif harus unik per sesi
            $table->unique(['decision_session_id', 'code']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alternatives');
    }
};
