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
        Schema::create('employee_book_movements', function (Blueprint $table) {
            $table->id();

            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();

            $table->foreignId('book_inventory_id')->constrained('book_inventories')->cascadeOnDelete();
            $table->foreignId('book_id')->constrained('physical_books');
            $table->integer('quantity')->default(1);

            $table->enum('type', [
                'issued',
                'returned'
            ]);

            $table->date('movement_date');
            

            $table->text('note')->nullable();
            $table->foreignId('user_id')->constrained('users');

            
            $table->date('return_date')->nullable();
            $table->text('return_note')->nullable();
            $table->foreignId('returned_by')->nullable()->constrained('users');

            $table->timestamps();

            $table->index(['employee_id', 'book_inventory_id']);
            $table->index('type');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_book_movements');
    }
};
