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
        Schema::create('physical_books', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('book_id')->nullable()->constrained('books')->nullOnDelete();
            $table->integer('price')->nullable();
            $table->integer('minimum_stock')->default(10);
            $table->timestamps();
        });
    }

/**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('physical_books');
    }
};
