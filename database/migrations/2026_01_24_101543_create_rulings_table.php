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

        Schema::create('rulings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('gazette_id')
                ->constrained('gazettes')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->string('title_fa');
            $table->string('title_en');
            $table->string('ruling_number')->unique();
            $table->string('ruling_date', 10);
            $table->enum('type', ['Decree', 'Order']);
            $table->timestamps();

            $table->index(['gazette_id', 'type', 'ruling_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rulings');
    }
};
