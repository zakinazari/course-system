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
        Schema::table('courses', function (Blueprint $table) {
            $table->string('course_code')->unique()->nullable()->after('name');
            $table->integer('min_capacity')->nullable()->after('total_teaching_days');
            $table->integer('max_capacity')->nullable();
            $table->string('image')->after('status')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->dropUnique(['course_code']); 
            $table->dropColumn('course_code');  
            $table->dropColumn('min_capacity');  
            $table->dropColumn('max_capacity');  
            $table->dropColumn('image');  
        });
    }
};
