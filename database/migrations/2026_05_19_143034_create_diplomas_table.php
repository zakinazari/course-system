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
        Schema::create('diplomas', function (Blueprint $table) {
            $table->id();

            $table->foreignId('student_id')->constrained();

            $table->foreignId('branch_id');

            $table->string('serial_number')->unique();

            $table->uuid('verification_code')->unique();

            $table->date('graduated_at')->nullable();

            $table->decimal('average', 5, 1)->nullable(); 

            $table->boolean('is_revoked')->default(false);

            $table->timestamp('printed_at')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('diplomas');
    }
};
