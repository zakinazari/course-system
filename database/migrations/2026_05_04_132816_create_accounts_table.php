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
        Schema::create('accounts', function (Blueprint $table) {

            $table->id();

            $table->foreignId('branch_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');

            $table->enum('type', [
                'central',
                'branch'
            ])->default('branch');

            $table->enum('category', [
                'treasury',
                'cash',
                'bank',
                'other'
            ])->default('treasury');

            $table->boolean('is_default')->default(false);

            $table->decimal('balance', 15, 2)->default(0);

            $table->timestamps();

            // index
            $table->index(['branch_id', 'type']);
            $table->index(['branch_id', 'category']);

            $table->unique(
                ['branch_id', 'category', 'is_default'],
                'accounts_branch_category_default_unique'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accounts');
    }
};
