<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   
    public function up(): void
    {
        Schema::create('employee_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->onDelete('cascade'); 
                
            $table->string('file_type')->default('photo')->index();
           
            // اطلاعات فایل
            $table->string('file_name');
            $table->string('file_path');
            $table->string('thumbnail_path')->nullable();

            $table->string('mime_type')->nullable();
            $table->unsignedBigInteger('file_size')->nullable();

            $table->string('disk')->default('public');

            $table->timestamp('uploaded_at')->useCurrent();

            $table->index(['employee_id', 'file_type']);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_files');
    }
};
