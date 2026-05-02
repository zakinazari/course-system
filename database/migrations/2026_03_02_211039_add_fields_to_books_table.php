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
        Schema::table('books', function (Blueprint $table) {

            $table->integer('fee')->nullable();
            $table->unsignedSmallInteger('pass_mark')->nullable();
            $table->unsignedSmallInteger('makeup_mark')->nullable();
            $table->unsignedSmallInteger('total_teaching_days');
            $table->unsignedSmallInteger('min_capacity')->nullable();
            $table->unsignedSmallInteger('max_capacity')->nullable();
            $table->integer('exam_fine_amount')->nullable();
            $table->unsignedSmallInteger('level_number')->nullable();
            $table->unsignedSmallInteger('drop_days')->nullable();

            $table->unique(['program_id', 'level_number'], 'books_program_level_unique');
        });
    }

    public function down(): void
    {
        Schema::table('books', function (Blueprint $table) {
            
            $table->dropUnique('books_program_level_unique');

       
            $table->dropColumn([
                'fee', 
                'pass_mark',
                'total_teaching_days',
                'min_capacity',
                'max_capacity',
                'exam_fine_amount',
                'level_number',
                'drop_days'
            ]);
        });
    }
};
