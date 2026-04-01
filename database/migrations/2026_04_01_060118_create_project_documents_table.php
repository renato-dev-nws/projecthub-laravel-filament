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
        Schema::create('project_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->foreignId('created_by')->constrained('users');
            $table->string('title');
            $table->string('slug');
            $table->longText('content')->nullable();
            $table->enum('type', ['markdown', 'file', 'link'])->default('markdown');
            $table->string('file_path')->nullable();
            $table->string('external_url')->nullable();
            $table->boolean('is_public')->default(false);
            $table->enum('visibility', ['team', 'client', 'public'])->default('team');
            $table->string('category')->nullable();
            $table->integer('version')->default(1);
            $table->json('version_history')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->index('project_id');
            $table->index('is_public');
            $table->unique(['project_id', 'slug']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_documents');
    }
};
