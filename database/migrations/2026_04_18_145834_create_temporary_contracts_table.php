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
        Schema::create('temporary_contracts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('position_id')->constrained('positions');
            $table->foreignId('employee_id')->constrained('employees');
            $table->foreignId('branch_id')->constrained('branches');
            
            $table->decimal('taxi_fare', 10, 2)->default(0);
            $table->decimal('food_deduction', 10, 2)->default(0);
            $table->decimal('credit_card', 10, 2)->default(0);

            $table->date('start_date');
            
            $table->date('end_date')->nullable();

            $table->enum('status', ['active', 'inactive', 'ended'])->default('active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('temporary_contracts');
    }
};
