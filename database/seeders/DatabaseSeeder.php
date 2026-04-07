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
        // Criar Super Admin
        User::updateOrCreate(
            ['email' => 'admin@projecthub.app'],
            [
                'name' => 'Admin',
                'password' => \Illuminate\Support\Facades\Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        // Roles e Permissions
        $this->call(RoleAndPermissionSeeder::class);

        // Users da equipe
        $this->call(UserSeeder::class);

        // Serviços
        $this->call(ServiceSeeder::class);

        // Clientes
        $this->call(ClientSeeder::class);

        // Usuários do portal do cliente
        $this->call(ClientPortalUserSeeder::class);

        // Projetos
        $this->call(ProjectSeeder::class);

        // Leads
        $this->call(LeadSeeder::class);

        // Propostas
        $this->call(QuoteSeeder::class);
    }
}
