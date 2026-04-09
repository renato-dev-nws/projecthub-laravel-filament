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
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique()->nullable();
            $table->string('code')->unique();
            $table->string('github_url')->nullable();
            $table->foreignId('client_id')->constrained();
            $table->foreignId('quote_id')->nullable()->constrained();
            $table->foreignId('project_manager_id')->constrained('users');
            $table->text('description')->nullable();
            $table->enum('status', [
                'planning', 'active', 'on_hold', 'completed', 'cancelled'
            ])->default('planning');
            $table->enum('priority', ['low', 'medium', 'high', 'critical'])->default('medium');
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->integer('estimated_hours')->nullable();
            $table->integer('logged_hours')->default(0);
            $table->decimal('budget', 15, 2)->nullable();
            $table->decimal('spent', 15, 2)->default(0);
            $table->integer('progress_percent')->default(0);
            $table->json('settings')->nullable();
            $table->boolean('client_portal_enabled')->default(true);
            $table->boolean('client_can_comment')->default(true);
            $table->string('color', 7)->default('#6366f1');
            $table->timestamps();
            $table->softDeletes();

            $table->index('status');
            $table->index('client_id');
            $table->index('project_manager_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
