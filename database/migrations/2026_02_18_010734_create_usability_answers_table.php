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
        Schema::create('usability_answers', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('usability_response_id');
            $table->unsignedBigInteger('usability_question_id');

            // Nilai skala Likert SUS (1–5)
            $table->unsignedTinyInteger('value')
                ->comment('Likert scale 1–5');

            $table->timestamps();

            // Index untuk agregasi
            $table->index('usability_question_id');

            // Foreign key explicit (hindari collision)
            $table->foreign(
                'usability_response_id',
                'fk_usability_answers_response'
            )->references('id')
                ->on('usability_responses')
                ->onDelete('cascade');

            $table->foreign(
                'usability_question_id',
                'fk_usability_answers_question'
            )->references('id')
                ->on('usability_questions')
                ->onDelete('cascade');

            // Satu jawaban per pertanyaan dalam satu response
            $table->unique(
                ['usability_response_id', 'usability_question_id'],
                'uq_usability_answer_once'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('usability_answers');
    }
};
