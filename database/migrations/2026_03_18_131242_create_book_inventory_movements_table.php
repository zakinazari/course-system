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
        Schema::create('book_inventory_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('book_inventory_id')->constrained('book_inventories')->cascadeOnDelete();
            $table->integer('quantity_change'); 
            $table->integer('balance_after'); 
            $table->decimal('unit_price', 10, 2)->nullable();
            $table->enum('type', ['purchase', 'sale', 'transfer', 'return']);
            $table->text('note')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete(); 
            $table->timestamps();

            $table->index('type');
            $table->index('book_inventory_id');
            $table->index(['book_inventory_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('book_inventory_movements');
    }
};
