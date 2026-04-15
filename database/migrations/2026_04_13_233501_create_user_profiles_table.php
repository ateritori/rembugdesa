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
        Schema::create('user_profiles', function (Blueprint $table) {
            $table->id();

            // Relasi ke users
            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            // Identitas
            $table->string('nama_lengkap')->nullable();
            $table->text('alamat')->nullable();

            // Wilayah
            $table->string('dusun')->nullable();
            $table->string('rt')->nullable();
            $table->string('rw')->nullable();

            // Kelembagaan
            $table->string('unsur')->nullable();
            $table->string('jabatan')->nullable();
            $table->string('instansi')->nullable();

            // Kategori DM (untuk Nested Borda)
            $table->enum('kategori_dm', [
                'strategis',
                'partisipatif',
                'teknokratis'
            ])->default('partisipatif');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_profiles');
    }
};
