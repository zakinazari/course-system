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
        Schema::create('notification_category_role', function (Blueprint $table) {
            
            $table->id();

            $table->foreignId('notification_category_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('role_id')
                ->constrained('access_roles')
                ->cascadeOnDelete();

            $table->unique(
                [
                    'notification_category_id',
                    'role_id'
                ],
                'notification_category_role_unique'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notification_category_role');
    }
};
