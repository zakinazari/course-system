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
        Schema::create('exam_fines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained();
            $table->foreignId('course_id')->constrained();
            $table->foreignId('exam_type_id')->constrained();
            $table->decimal('amount', 10, 2);
            $table->enum('status', ['unpaid','paid','waived'])->default('unpaid');
            $table->text('reason')->nullable(); 
            $table->date('exam_date')->nullable();
            $table->date('payment_date')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exam_fines');
    }
};
