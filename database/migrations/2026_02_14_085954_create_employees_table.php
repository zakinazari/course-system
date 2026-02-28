<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    
    public function up(): void
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('employee_number');
            $table->string('employee_code')->unique();
            $table->string('name');
            $table->string('last_name')->nullable();
            $table->string('father_name')->nullable();
            $table->string('phone_no', 16)->nullable();
            $table->string('email')->nullable();
            $table->string('tazkira_no',32)->unique()->nullable();
            $table->text('address')->nullable();
            $table->enum('status', [
                'new', 
                'active',
                'inactive',
            ])->default('new');

            $table->date('hire_date');
            $table->foreignId('branch_id')->nullable()->constrained('branches')->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();

            $table->unique(['branch_id', 'employee_number']);

            $table->index(['employee_code']);
            $table->index(['name']);
            $table->index(['name','father_name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
