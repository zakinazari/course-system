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
          Schema::table('submissions', function (Blueprint $table) {
            $table->foreignId('main_axis_id')->constrained('main_axes')->cascadeOnDelete();
            $table->foreignId('sub_axis_id')->constrained('sub_axes')->cascadeOnDelete();
            $table->foreignId('accepted_abstract_id')->nullable()->constrained('accepted_abstracts')->nullOnDelete();
            $table->enum('upload_status', ['pending', 'processing', 'done', 'failed'])
            ->default('pending');

            $table->index('title_fa');             
            $table->index('title_en');
        });
    }

    public function down(): void
    {
        Schema::table('submissions', function (Blueprint $table) {
            $table->dropForeign(['main_axis_id']);
            $table->dropForeign(['sub_axis_id']);
            $table->dropForeign(['accepted_abstract_id']);

            $table->dropColumn(['main_axis_id', 'sub_axis_id', 'accepted_abstract_id','upload_status']);
        });
    }
};
