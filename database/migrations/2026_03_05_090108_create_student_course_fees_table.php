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
        Schema::create('student_course_fees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->nullable()->constrained('students')->nullOnDelete();
            $table->foreignId('course_id')->nullable()->constrained('courses')->nullOnDelete();
            $table->foreignId('discount_provider_id')->nullable()->constrained('discount_providers')->nullOnDelete();

            $table->enum('payment_type', ['full', 'installment']);

            $table->decimal('fee_amount', 10, 2);

            $table->enum('discount_type', ['percentage', 'fixed'])->nullable();
            $table->decimal('discount_value', 10, 2)->default(0);
            $table->text('discount_reason')->nullable();

            $table->decimal('total_amount', 10, 2);
            $table->decimal('paid_amount',10,2)->default(0);
            $table->decimal('remaining_amount',10,2)->default(0);

            $table->enum('status',['unpaid','partial','paid'])->default('unpaid');
            $table->foreignId('branch_id')->nullable()->constrained('branches')->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['student_id','course_id'], 'student_course_unique');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_course_fees');
    }
};
