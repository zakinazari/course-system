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
        Schema::create('student_book_fees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained();
            $table->foreignId('branch_id')->constrained();
            $table->foreignId('physical_book_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('price',10,2);
            $table->date('payment_date')->nullable();
            $table->text('notes')->nullable();
            $table->enum('type', ['automatic', 'manual'])->default('automatic');
            $table->enum('status', ['paid', 'requested_exemption','rejected_exemption','accepted_exemption'])->default('paid');
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('processed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index('type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_book_fees');
    }
};
