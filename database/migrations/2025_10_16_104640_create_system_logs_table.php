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
        Schema::create('system_logs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('user_id');
            $table->integer('st_id')->nullable();
            $table->integer('s_id')->nullable();
            $table->text('section')->nullable();
            $table->tinyInteger('type_id');
            $table->timestamps();


            $table->index('user_id');
            $table->index('st_id');
            $table->index('s_id');
            $table->index('type_id');
            $table->index('created_at');

            $table->index(['user_id', 'created_at'], 'idx_user_created_at');
            $table->index('st_id');
            $table->index('s_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('system_logs');
    }
};
