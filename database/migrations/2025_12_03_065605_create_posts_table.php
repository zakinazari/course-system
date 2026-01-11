<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    
    public function up(): void
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->string('title_fa');
            $table->string('title_en');
        
            $table->text('content_fa')->nullable();
            $table->text('content_en')->nullable();
            
            $table->string('image')->nullable();
        
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
        
            $table->enum('status', ['draft', 'published'])->default('draft');
            
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
