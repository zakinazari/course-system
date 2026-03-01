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
        Schema::create('student_course_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();

            $table->decimal('midterm', 5, 2)->nullable();
            $table->decimal('final', 5, 2)->nullable();
            $table->decimal('cognitive', 5, 2)->nullable();
            $table->decimal('attendance', 5, 2)->nullable();
            $table->decimal('total', 5, 2)->nullable(); 

            $table->boolean('is_finalized')->default(false); 
            $table->foreignId('user_id')->nullable()->constrained('users');
            $table->timestamps();

            $table->unique(['student_id', 'course_id']);
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_course_results');
    }
};
