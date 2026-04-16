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
        Schema::create('exam_attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->foreignId('exam_type_id')->constrained();
            $table->date('exam_date')->nullable();
            
            $table->enum('status', ['present','absent','excused'])->default('absent');
            $table->foreignId('recorded_by')->nullable()->constrained('users');
            $table->timestamps();

            $table->unique(['student_id','course_id','exam_type_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exam_attendances');
    }
};
