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
        Schema::create('accepted_abstracts', function (Blueprint $table) {
            $table->id();
            $table->string('title_fa');
            $table->string('title_en')->nullable();
            $table->string('author_name')->nullable();
            $table->string('author_last_name')->nullable();
            $table->timestamps();

           
            $table->index('title_fa');             
            $table->index('title_en');          
            $table->index('author_name');          
            $table->index('author_last_name');          
            $table->index(['author_name','author_last_name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accepted_abstracts');
    }
};
