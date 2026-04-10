<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->json('job_titles')->nullable()->after('position');
            $table->json('skills')->nullable()->after('job_titles');
            $table->text('bio')->nullable()->after('department');
            $table->string('city', 120)->nullable()->after('phone');
            $table->string('linkedin_url')->nullable()->after('avatar_url');
            $table->string('github_url')->nullable()->after('linkedin_url');
            $table->string('portfolio_url')->nullable()->after('github_url');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'job_titles',
                'skills',
                'bio',
                'city',
                'linkedin_url',
                'github_url',
                'portfolio_url',
            ]);
        });
    }
};