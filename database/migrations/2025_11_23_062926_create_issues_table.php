<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    
    public function up(): void
    {
        Schema::create('issues', function (Blueprint $table) {
            $table->id();
            $table->string('volume')->nullable();       
            $table->string('number')->nullable();        
            $table->string('title_fa')->nullable();      
            $table->string('title_en')->nullable();      
            $table->string('cover_image')->nullable();
            $table->enum('status', ['published', 'unpublished'])->default('unpublished');      
            $table->date('published_at')->nullable();  
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('issues');
    }
};
