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
        Schema::create('scientific_board', function (Blueprint $table) {
            $table->id();
            $table->string('name_fa');
            $table->string('name_en')->nullable();
            $table->timestamps();
        });

        Schema::create('scientific_board_members', function (Blueprint $table) {
            $table->id();
            $table->string('name_fa');
            $table->string('name_en')->nullable();
            $table->unsignedBigInteger('board_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scientific_board');
        Schema::dropIfExists('scientific_board_members');
    }
};
