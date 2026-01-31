<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   
    public function up(): void
    {
        Schema::create('ruling_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ruling_id')
                ->constrained('rulings')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->string('file_name');      
            $table->string('file_path');   
            $table->string('file_type')->default('pdf');
            $table->integer('file_size')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ruling_files');
    }
};
