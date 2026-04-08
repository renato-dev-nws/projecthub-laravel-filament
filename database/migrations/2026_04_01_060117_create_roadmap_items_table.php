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
        Schema::create('roadmap_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->foreignId('phase_id')->nullable()->constrained('project_phases');
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('type', ['milestone', 'deliverable', 'review', 'launch'])->default('milestone');
            $table->enum('status', ['planned', 'in_progress', 'completed', 'delayed'])->default('planned');
            $table->date('planned_date');
            $table->date('actual_date')->nullable();
            $table->boolean('is_public')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->index('project_id');
            $table->index('is_public');
        });

        Schema::table('project_tasks', function (Blueprint $table) {
            $table->foreign('roadmap_item_id')->references('id')->on('roadmap_items')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('project_tasks', function (Blueprint $table) {
            $table->dropForeign(['roadmap_item_id']);
        });

        Schema::dropIfExists('roadmap_items');
    }
};
