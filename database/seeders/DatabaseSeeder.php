<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Criar Super Admin antes dos seeders
        \App\Models\User::updateOrCreate(
            ['email' => 'admin@projecthub.app'],
            [
                'name'              => 'Admin',
                'password'          => \Illuminate\Support\Facades\Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        $this->call([
            RoleAndPermissionSeeder::class,
            UserSeeder::class,
            ClientSeeder::class,
            ClientPortalUserSeeder::class,
            LeadSeeder::class,
            ServiceCategorySeeder::class,
            ServiceSeeder::class,
            ServicePricingTierSeeder::class,
            BankSeeder::class,
            FinancialCategorySeeder::class,
            SupplierCategorySeeder::class,
            SupplierSeeder::class,
            ContractClauseSeeder::class,
            QuoteSeeder::class,
            ProjectSeeder::class,
        ]);
    }
}
