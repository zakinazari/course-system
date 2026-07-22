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
        Schema::create('makeup_settings', function (Blueprint $table) {

            $table->id();
              
            $table->string('name')->nullable();

            $table->decimal('fee_amount', 10, 2);

            $table->integer('exam_valid_days')->default(10);
            $table->integer('fee_valid_days')->default(10);

            $table->boolean('status')->default(true);

            $table->text('note')->nullable();

            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('makeup_settings');
    }
};
