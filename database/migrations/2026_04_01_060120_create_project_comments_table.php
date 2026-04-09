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
        Schema::create('project_comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->nullableMorphs('commentable');
            $table->nullableMorphs('author');
            $table->text('content');
            $table->foreignId('parent_id')->nullable()->constrained('project_comments');
            $table->boolean('is_internal')->default(false);
            $table->timestamps();
            $table->softDeletes();

            $table->index('project_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_comments');
    }
};
