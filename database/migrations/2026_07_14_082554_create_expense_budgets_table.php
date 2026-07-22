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
        Schema::create('expense_budgets', function (Blueprint $table) {
            $table->id();

            $table->foreignId('expense_category_id')->constrained()->cascadeOnDelete();

            $table->foreignId('branch_id')->constrained()->cascadeOnDelete();

            $table->foreignId('section_id')->nullable()->constrained()->nullOnDelete();

            $table->decimal('amount', 15, 2);

            $table->date('effective_from');

            $table->date('effective_to')->nullable();

            $table->text('note')->nullable();

            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();

            $table->timestamps();

            $table->index(
                ['expense_category_id', 'branch_id', 'section_id', 'effective_from'],
                'exp_budget_lookup'
            );
        });
    }

    /**
    * Reverse the migrations.
    */
    public function down(): void
    {
        Schema::dropIfExists('expense_budgets');
    }
};
