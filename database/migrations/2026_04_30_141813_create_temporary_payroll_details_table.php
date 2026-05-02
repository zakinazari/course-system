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
        Schema::create('temporary_payroll_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('temporary_payroll_id')->constrained()->cascadeOnDelete();

            $table->foreignId('employee_id')->constrained();

            $table->foreignId('book_id')->constrained();

            //  Snapshot fields
            $table->decimal('amount_snapshot', 10, 2);
            $table->integer('total_days_snapshot');
            $table->decimal('daily_rate_snapshot', 10, 2);
            $table->integer('attendance_count')->default(0);

            $table->decimal('total_salary', 10, 2)->default(0);

            $table->timestamps();

            $table->index(['temporary_payroll_id', 'employee_id'], 'tpd_payroll_emp_idx');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('temporary_payroll_details');
    }
};
