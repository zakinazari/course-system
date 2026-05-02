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
        Schema::create('employee_salary_advance_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_salary_advance_id')
            ->constrained('employee_salary_advances')->cascadeOnDelete()->name('esa_payments_adv_id_fk');;

            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();

            $table->foreignId('month_id')->constrained('months');

            $table->integer('year');

            $table->decimal('amount', 10, 2);
            $table->date('payment_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_salary_advance_payments');
    }
};
