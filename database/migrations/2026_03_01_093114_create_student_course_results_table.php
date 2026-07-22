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
            $table->foreignId('student_id')->constrained();
            $table->foreignId('course_id')->constrained();

            $table->decimal('total', 5, 1)->nullable(); 
            $table->enum('status', ['passed', 'makeup', 'failed','in_progress'])->nullable();
            $table->integer('pass_mark_snapshot')->nullable();
            $table->integer('makeup_mark_snapshot')->nullable();
            $table->boolean('is_finalized')->default(false); 
            $table->timestamp('finalized_at')->nullable();
            $table->foreignId('finalized_by')->nullable()->constrained('users')->nullOnDelete();
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
