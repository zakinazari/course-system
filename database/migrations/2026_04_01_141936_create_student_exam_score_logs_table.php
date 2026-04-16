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
        Schema::create('student_exam_score_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_exam_score_id')->nullable();

            $table->unsignedBigInteger('student_id');
            $table->unsignedBigInteger('course_id');
            $table->unsignedBigInteger('exam_type_id');

            $table->decimal('score_old', 5, 2)->nullable();
            $table->decimal('score_new', 5, 2)->nullable();

            $table->foreignId('user_id')->nullable()->constrained('users');

            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_exam_score_logs');
    }
};
