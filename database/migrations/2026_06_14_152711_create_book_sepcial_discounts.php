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
        Schema::create('book_special_discounts', function (Blueprint $table) {
            $table->id();

            $table->foreignId('book_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->enum('type', [
                'failed',
                'makeup',
                're_study',
                'dropped',
                'other'
            ]);

            // مقدار تخفیف
            $table->decimal('amount', 10, 2);

            // مدت اعتبار (روز)
            $table->integer('duration_days')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('book_sepcial_discounts');
    }
};
