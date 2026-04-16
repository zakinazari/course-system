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
        Schema::create('teacher_attendances', function (Blueprint $table) {
            $table->id();

            $table->foreignId('course_id')->constrained();

            $table->foreignId('teacher_id')->constrained('employees');

            $table->enum('status', ['present', 'absent', 'late']);

            $table->date('attendance_date');
            $table->foreignId('recorded_by')->nullable()->constrained('users');

            $table->text('note')->nullable();
            // برای یونت درسی است 
            $table->integer('unit_number')->nullable();
            $table->enum('lesson_status', ['ongoing','finished'])->default('finished');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teacher_attendances');
    }
};
