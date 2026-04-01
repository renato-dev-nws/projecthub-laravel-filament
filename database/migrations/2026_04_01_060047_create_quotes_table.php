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
        Schema::create('quotes', function (Blueprint $table) {
            $table->id();
            $table->string('number')->unique();
            $table->foreignId('client_id')->constrained();
            $table->foreignId('lead_id')->nullable()->constrained();
            $table->foreignId('created_by')->constrained('users');
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('status', [
                'draft', 'sent', 'viewed', 'approved', 'rejected', 'expired'
            ])->default('draft');
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('discount_percent', 5, 2)->default(0);
            $table->decimal('discount_value', 15, 2)->default(0);
            $table->decimal('tax_percent', 5, 2)->default(0);
            $table->decimal('tax_value', 15, 2)->default(0);
            $table->decimal('total', 15, 2)->default(0);
            $table->string('currency', 3)->default('BRL');
            $table->date('valid_until')->nullable();
            $table->text('terms_conditions')->nullable();
            $table->text('internal_notes')->nullable();
            $table->string('signed_token')->nullable()->unique();
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('viewed_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('status');
            $table->index('client_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quotes');
    }
};
