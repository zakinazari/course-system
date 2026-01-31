<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    
    public function up(): void
    {
        Schema::create('sub_axes', function (Blueprint $table) {
            $table->id();
            $table->string('title_fa');
            $table->string('title_en')->nullable();
            $table->foreignId('main_axis_id')->constrained('main_axes')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sub_axes');
    }
};
