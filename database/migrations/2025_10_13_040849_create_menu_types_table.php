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
        Schema::create('menu_types', function (Blueprint $table) {
            $table->id();
            $table->string('type_name_fa',128);
            $table->string('type_name_en',128);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('menu_types');
    }
};
