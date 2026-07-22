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
        Schema::create('assets', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('branch_id')->constrained();

            $table->foreignId('section_id')->nullable()->constrained();

            $table->foreignId('asset_category_id')->constrained();

            $table->string('name'); // Laptop, Chair, etc.

            $table->unsignedInteger('asset_number');
            $table->string('code')->unique();

            $table->decimal('purchase_price', 15, 2);

            $table->integer('quantity')->default(1);

            $table->foreignId('unit_id')->nullable()->constrained();

            $table->date('purchase_date');

            $table->enum('status', [
                'warehouse',
                'assigned'
            ])->default('warehouse');
            
            $table->text('note')->nullable();

            $table->foreignId('user_id')->constrained();

        
            $table->timestamps();

            $table->index(['branch_id', 'purchase_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assets');
    }
};
