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
        Schema::create('employee_leaves', function (Blueprint $table) {

            $table->id();

            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();

            $table->morphs('contract');

            $table->foreignId('leave_type_id')->constrained();

            $table->date('start_date');

            $table->date('end_date');

            $table->decimal('days',4,1);

            $table->enum('status',[
                'pending',
                'approved',
                'rejected',
                'cancelled'
            ])->default('pending');

            $table->text('reason')->nullable();

            $table->text('note')->nullable();

            $table->foreignId('approved_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('approved_at')->nullable();

            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_leaves');
    }
};
