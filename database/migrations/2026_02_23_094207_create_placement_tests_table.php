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
        Schema::create('placement_tests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();

            $table->foreignId('program_id')->nullable()->constrained('programs')->nullOnDelete();
            $table->foreignId('book_id')->nullable()->constrained('books')->nullOnDelete();
            $table->foreignId('shift_id')->nullable()->constrained('shifts')->nullOnDelete();

            $table->date('test_date');
            
            $table->date('expire_date');

            $table->decimal('fee_amount', 10, 2)->default(0);
            $table->boolean('is_paid')->default(false);

            $table->text('notes')->nullable();

            $table->enum('status', ['pending','enrolled'])->default('pending');

            $table->timestamps();

            $table->index('status');
            $table->unique([
                'student_id',
                'branch_id',
                'program_id',
                'book_id',
                'shift_id',
            ], 'student_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('placement_tests');
    }
};
