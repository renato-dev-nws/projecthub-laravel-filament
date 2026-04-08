<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_pricing_tiers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')->constrained()->cascadeOnDelete();
            $table->decimal('min_hours', 10, 2)->default(0);
            $table->decimal('max_hours', 10, 2)->nullable();
            $table->decimal('price_per_hour', 15, 2)->default(0);
            $table->string('label', 100)->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->index('service_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_pricing_tiers');
    }
};
