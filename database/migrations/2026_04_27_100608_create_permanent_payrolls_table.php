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
        Schema::create('permanent_payrolls', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees');
            $table->foreignId('branch_id')->constrained('branches');
            $table->foreignId('permanent_contract_id')->constrained('permanent_contracts');
            $table->foreignId('month_id')->constrained('months');
            $table->integer('year');

            $table->decimal('gross_salary', 10, 2)->default(0);
            $table->decimal('total_present_days', 10, 2)->default(0);
            $table->decimal('over_time_hours', 10, 2)->default(0);
            $table->decimal('over_time_amount', 10, 2)->default(0);

            $table->decimal('taxi_fare', 10, 2)->default(0);
            $table->decimal('credit_card', 10, 2)->default(0);
            $table->decimal('total_allowances', 10, 2)->default(0);

            $table->decimal('tax', 10, 2)->default(0);
            $table->decimal('advance_deduction', 10, 2)->default(0);
            $table->decimal('total_deductions', 12, 2)->default(0);
            
            $table->decimal('net_salary', 12, 2)->default(0);
    
            $table->date('payment_date')->nullable();
            $table->foreignId('paid_by')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('status', ['pending', 'paid'])->default('pending');

            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permanent_payrolls');
    }
};
