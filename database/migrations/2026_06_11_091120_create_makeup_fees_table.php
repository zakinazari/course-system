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
        Schema::create('makeup_fees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained();

            $table->foreignId('course_id')->constrained();

            $table->foreignId('makeup_setting_id')->nullable()->constrained()->nullOnDelete();

            $table->decimal('amount', 10, 2);

            $table->text('note')->nullable();

            $table->date('payment_date')->nullable();

            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('makeup_fees');
    }
};
