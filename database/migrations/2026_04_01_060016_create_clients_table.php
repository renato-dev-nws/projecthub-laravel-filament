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
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->string('company_name');
            $table->string('trade_name')->nullable();
            $table->string('cnpj', 20)->nullable()->unique();
            $table->string('cpf', 14)->nullable()->unique();
            $table->enum('type', ['pessoa_juridica', 'pessoa_fisica'])->default('pessoa_juridica');
            $table->string('email')->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('website')->nullable();
            $table->string('industry')->nullable();
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('state', 2)->nullable();
            $table->string('zip_code', 10)->nullable();
            $table->string('country', 2)->default('BR');
            $table->enum('status', ['active', 'inactive', 'prospect'])->default('prospect');
            $table->text('notes')->nullable();
            $table->foreignId('account_manager_id')->nullable()->constrained('users');
            $table->timestamps();
            $table->softDeletes();

            $table->index('status');
            $table->index('account_manager_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
