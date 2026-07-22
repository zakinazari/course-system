<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('student_number');
            $table->string('student_code')->unique();

            $table->string('name');
            $table->string('name_fa')->nullable();
            $table->string('name_pa')->nullable();

            $table->string('last_name')->nullable();
            $table->string('last_name_fa')->nullable();
            $table->string('last_name_pa')->nullable();

            $table->string('father_name')->nullable();
            $table->string('father_name_fa')->nullable();
            $table->string('father_name_pa')->nullable();

            $table->date('date_of_birth')->nullable();

            $table->string('phone_no', 16)->nullable();
            $table->string('whats_app', 16)->nullable();
            $table->string('father_whats_app', 16)->nullable();
            $table->string('tazkira_no',32)->nullable();

            $table->text('address')->nullable();
            $table->timestamp('registration_date');
            $table->enum('status', [
                'new', 
                'active', 
                'suspended', 
                'inactive', 
                'graduated', 
            ])->default('new');
            $table->foreignId('branch_id')->nullable()->constrained('branches')->nullOnDelete();
            $table->foreignId('gender_id')->default(1)->constrained('genders')->defacults(1);
            $table->foreignId('occupation_id')->nullable()->constrained();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();


            $table->timestamps();

            $table->unique(['branch_id', 'student_number']);
            $table->index(['student_code']);
            $table->index(['name']);
            $table->index(['name','father_name']);
            $table->index(['status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
