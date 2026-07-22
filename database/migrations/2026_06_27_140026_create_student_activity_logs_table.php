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
        Schema::create('student_activity_logs', function (Blueprint $table) {

            $table->id();

            $table->foreignId('category_id')->constrained('activity_categories');
            $table->foreignId('student_id')->constrained('students');

            $table->string('title')->nullable();
            $table->text('description')->nullable();

            $table->date('activity_date');

            $table->foreignId('created_by');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_activity_logs');
    }
};
