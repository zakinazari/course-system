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
        Schema::create('asset_movements', function (Blueprint $table) {
            $table->id();

            $table->foreignId('asset_id')->constrained();
            $table->foreignId('employee_id')->constrained();
            $table->foreignId('section_id')->constrained();
            $table->foreignId('branch_id')->constrained();

            $table->enum('type', [
                'assigned',   
                'returned', 
                'transfer'
            ]);

            $table->date('movement_date');

            $table->text('note')->nullable();

            $table->foreignId('user_id')->constrained();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asset_movements');
    }
};
