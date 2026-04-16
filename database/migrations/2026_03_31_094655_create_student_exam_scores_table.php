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
        Schema::create('student_exam_scores', function (Blueprint $table) {
            $table->id();

            $table->foreignId('student_id')->constrained();

            $table->foreignId('course_id')->constrained();

            $table->foreignId('exam_type_id')->constrained();
            // نمره
            $table->decimal('score', 5, 2)->nullable();

            // اختیاری: برای audit
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();

            $table->timestamps();

            $table->unique(['student_id', 'course_id', 'exam_type_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_exam_scores');
    }
};
