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
        Schema::create('web_page_files', function (Blueprint $table) {
            $table->id();
             $table->unsignedBigInteger('page_id');
            $table->string('file_path');
            $table->text('file_name')->nullable();
            $table->enum('file_type', ['pdf','docx','xlsx'])->nullable();// pdf, docx, etc
            $table->unsignedBigInteger('file_size')->nullable(); // bytes
            $table->timestamps();
            // Foreign key
            $table->foreign('page_id')
            ->references('id')->on('web_pages');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('web_page_files');
    }
};
