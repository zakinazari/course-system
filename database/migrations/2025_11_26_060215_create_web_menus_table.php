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
        Schema::create('web_menus', function (Blueprint $table) {
            $table->id();
            $table->string('name_fa');
            $table->string('name_en');
            $table->tinyInteger('status')->comment('1=activ, 2=inactive');
            $table->mediumInteger('order')->nullable();
            $table->mediumInteger('parent_id')->nullable();
            $table->mediumInteger('grand_parent_id')->nullable();
            $table->smallInteger('type_id');
            $table->mediumInteger('page_id')->nullable();
            $table->string('slug',length: 64)->unique()->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('web_menus');
    }
};
