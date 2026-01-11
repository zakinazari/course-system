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
        Schema::create('submission_authors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('country_id')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users');
            $table->foreignId('type_id')->nullable();
            $table->foreignId('submission_id')->constrained('submissions')->onDelete('cascade');
            $table->integer('education_degree_id')->nullable();
            $table->integer('academic_rank_id')->nullable();
            $table->integer('province_id')->nullable();
            $table->string('given_name_fa')->nullable();
            $table->string('given_name_en')->nullable();
            $table->string('family_name_fa')->nullable();
            $table->string('family_name_en')->nullable();

            $table->string('email')->nullable();
            $table->string('phone_no',16)->nullable();
            $table->string('affiliation_fa')->nullable();
            $table->string('affiliation_en')->nullable();
            $table->string('city_fa')->nullable();
            $table->string('city_en')->nullable();
            $table->string('department_fa')->nullable();
            $table->string('department_en')->nullable();
            $table->string('preferred_research_area_fa')->nullable();
            $table->string('preferred_research_area_en')->nullable();
            
            $table->unsignedInteger('order_index')->default(1);
            $table->timestamps(); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('submission_authors');
    }
};
