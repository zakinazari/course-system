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
        Schema::create('submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('submitter_id')->constrained('users')->onDelete('cascade');
            $table->string('title_fa')->nullable();
            $table->string('title_en')->nullable();
            $table->text('abstract_fa')->nullable();
            $table->text('abstract_en')->nullable();
            $table->text('comments_to_editor_fa')->nullable();
            $table->text('comments_to_editor_en')->nullable();
            $table->enum('status', [
                'submitted','screening','under_review','revision_required','accepted','rejected','published'
            ])->default('submitted');

            $table->unsignedInteger('round')->default(1);
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->foreignId('issue_id')->nullable()->constrained('issues')->nullOnDelete();
            $table->timestamp('last_activity_at')->nullable();
            $table->unsignedBigInteger('views')->default(0);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('submissions');
    }
};
