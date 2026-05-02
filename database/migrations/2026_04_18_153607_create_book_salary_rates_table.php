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
        Schema::create('book_salary_rates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('book_id')->constrained('books');

            $table->foreignId('temporary_contract_id')->constrained('temporary_contracts')->cascadeOnDelete();

            $table->decimal('amount', 10, 2);

            $table->timestamps();

            $table->unique(['book_id', 'temporary_contract_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('book_salary_rates');
    }
};
