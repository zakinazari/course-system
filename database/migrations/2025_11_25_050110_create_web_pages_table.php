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
        Schema::create('web_pages', function (Blueprint $table) {
            $table->id();    
            $table->string('name_fa')->nullable();      
            $table->string('name_en')->nullable();      
            $table->string('title_fa')->nullable();      
            $table->string('title_en')->nullable();      
            $table->text('content_fa')->nullable();      
            $table->text('content_en')->nullable();      
            $table->string('cover_image')->nullable();     
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('web_pages');
    }
};
