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
        Schema::create('time_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained();
            $table->foreignId('task_id')->nullable()->constrained('project_tasks');
            $table->foreignId('user_id')->constrained();
            $table->text('description')->nullable();
            $table->decimal('hours', 5, 2);
            $table->date('logged_date');
            $table->boolean('is_billable')->default(true);
            $table->timestamps();

            $table->index('project_id');
            $table->index('user_id');
            $table->index('logged_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('time_logs');
    }
};
