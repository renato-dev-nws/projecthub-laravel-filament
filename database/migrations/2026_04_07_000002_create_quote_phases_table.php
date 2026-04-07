<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quote_phases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quote_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->integer('estimated_days')->nullable();
            $table->date('deadline_date')->nullable();
            $table->integer('sort_order')->default(0);
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->timestamps();

            $table->index('quote_id');
        });

        Schema::table('quote_items', function (Blueprint $table) {
            $table->foreign('quote_phase_id')->references('id')->on('quote_phases')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('quote_items', function (Blueprint $table) {
            $table->dropForeign(['quote_phase_id']);
        });

        Schema::dropIfExists('quote_phases');
    }
};
