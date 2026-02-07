<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('criteria', function (Blueprint $table) {
            $table->id();

            $table->foreignId('decision_session_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('name');

            $table->enum('type', ['benefit', 'cost']);

            $table->boolean('is_active')->default(true);

            $table->integer('order')->default(0);

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('criteria');
    }
};
