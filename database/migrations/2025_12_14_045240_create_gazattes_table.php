<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {

        Schema::create('gazettes', function (Blueprint $table) {
            $table->id();
            $table->string('title_fa');
            $table->string('title_en')->nullable();
            $table->string('gazette_number')->nullable();
            $table->date('publish_date')->nullable();
            $table->boolean('status')->default(true);
            $table->timestamps();
        });


        /*
        |--------------------------------------------------------------------------
        | Gazette files table (one-to-many)
        |--------------------------------------------------------------------------
        */
        Schema::create('gazette_files', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('gazette_id');
            $table->string('file_path');
            $table->text('file_name')->nullable();
            $table->enum('file_type', ['pdf','docx','xlsx'])->nullable();// pdf, docx, etc
            $table->unsignedBigInteger('file_size')->nullable(); // bytes
            $table->timestamps();
            // Foreign key
            $table->foreign('gazette_id')
            ->references('id')->on('gazettes')
            ->onDelete('cascade');
        });
    }


    /**
    * Reverse the migrations.
    */
    public function down(): void
    {
        Schema::dropIfExists('gazette_files');
        Schema::dropIfExists('gazettes');
    }
};
