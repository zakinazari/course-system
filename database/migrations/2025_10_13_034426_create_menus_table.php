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
        Schema::create('menus', function (Blueprint $table) {
            $table->id();
            $table->string('name_fa', length: 128)->nullable();
            $table->string('name_en', length: 255);
            $table->string('url',255)->nullable();
            $table->string('icon',255)->nullable();
            $table->tinyInteger('category')->comment('1=no operation, 2=has operation');
            $table->tinyInteger('status')->comment('1=activ, 2=inactive');
            $table->mediumInteger('order')->nullable();
            $table->mediumInteger('parent_id')->nullable();
            $table->mediumInteger('grand_parent_id')->nullable();
            $table->smallInteger('type_id');
            $table->smallInteger('section_id');
            $table->timestamps();
        });
    }
    
    public function down(): void
    {
        Schema::dropIfExists('menus');
    }
};
