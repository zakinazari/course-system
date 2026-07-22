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
        Schema::create('employee_security_savings', function (Blueprint $table) {
            $table->id();

            // کارمند
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();

            // قرارداد (PermanentContract, TemporaryContract, ...)
            $table->morphs('contract');

            // پرداخت معاش (PermanentPayroll, TemporaryPayroll, ...)
            $table->nullableMorphs('payroll');

            $table->enum('type', [
                'deposit',      // کسر از معاش
                'refund',       // برگشت هنگام تصفیه
                'deduction',    // کسر بابت خسارت یا بدهی

            ]);

            $table->decimal('amount', 10, 2);

            $table->date('transaction_date');

            $table->text('note')->nullable();

            $table->foreignId('user_id')->constrained();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_security_savings');
    }
};
