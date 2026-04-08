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
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('title')->nullable();
            $table->string('email')->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('company')->nullable();
            $table->string('website')->nullable();
            $table->string('referral_url')->nullable();
            $table->unsignedBigInteger('lead_source_id')->nullable()->index();
            $table->string('source')->nullable();
            $table->enum('status', [
                'new', 'contacted', 'qualified', 'proposal_sent',
                'negotiation', 'converted', 'lost'
            ])->default('new');
            $table->enum('priority', ['low', 'medium', 'high'])->default('medium');
            $table->decimal('estimated_value', 15, 2)->nullable();
            $table->text('description')->nullable();
            $table->date('expected_close_date')->nullable();
            $table->foreignId('assigned_to')->nullable()->constrained('users');
            $table->foreignId('converted_client_id')->nullable()->constrained('clients');
            $table->timestamp('converted_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('status');
            $table->index('assigned_to');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leads');
    }
};
