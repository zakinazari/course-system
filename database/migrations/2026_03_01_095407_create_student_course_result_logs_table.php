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
        Schema::create('student_course_result_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_course_result_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users');

            // ستون‌های اصلی که تغییر می‌کنند
            $table->decimal('midterm_old', 5, 2)->nullable();
            $table->decimal('midterm_new', 5, 2)->nullable();
            
            $table->decimal('final_old', 5, 2)->nullable();
            $table->decimal('final_new', 5, 2)->nullable();
            
            $table->decimal('cognitive_old', 5, 2)->nullable();
            $table->decimal('cognitive_new', 5, 2)->nullable();
            
            $table->decimal('attendance_old', 5, 2)->nullable();
            $table->decimal('attendance_new', 5, 2)->nullable();

            $table->decimal('total_old', 5, 2)->nullable();
            $table->decimal('total_new', 5, 2)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_course_result_logs');
    }
};
