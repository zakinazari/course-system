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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('branch_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('section_id')->constrained();
            $table->foreignId('account_id')->constrained();
            $table->enum('type', ['income', 'expense']);
            $table->decimal('amount', 15, 2);

            $table->string('category'); 
            // course_fee, book_sale, expense, correction, etc.

            // ارتباط با سیستم
            $table->string('source_type')->nullable(); 
            $table->unsignedBigInteger('source_id')->nullable();

            $table->date('transaction_date');
            $table->enum('action', ['create', 'update','delete']);
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->text('note')->nullable();
            $table->timestamps();

            // برای performance
            $table->index(['branch_id', 'transaction_date']);
            $table->index(['source_type', 'source_id']);
            $table->index(['category', 'source_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
