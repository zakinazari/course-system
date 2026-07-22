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
        Schema::create('expenses', function (Blueprint $table) {

            $table->id();
            $table->foreignId('shop_id')->nullable()->constrained();
            $table->foreignId('branch_id')->constrained();
            $table->foreignId('section_id')->constrained();
            $table->foreignId('employee_id')->nullable()->constrained();
            $table->foreignId('expense_category_id')->constrained();
            $table->string('name');
            $table->decimal('unit_price', 15, 2); 
            $table->decimal('quantity', 15, 2)->default(1);        
            $table->decimal('total_amount', 15, 2);  
            $table->foreignId('unit_id')->constrained();

            $table->text('note')->nullable();

            $table->date('expense_date');

            $table->foreignId('user_id')->constrained();

            $table->timestamps();

            $table->index(['branch_id', 'expense_date']);
        });
    }
    
    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
