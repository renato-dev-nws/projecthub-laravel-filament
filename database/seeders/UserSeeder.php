<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Super Admin (já existe)
        $superAdmin = User::where('email', 'admin@projecthub.app')->first();
        if ($superAdmin) {
            $superAdmin->assignRole('Super Admin');
            $superAdmin->update([
                'position' => 'CEO',
                'department' => 'Executive',
                'is_active' => true,
            ]);
        }

        // Admin
        $admin = User::create([
            'name' => 'Maria Silva',
            'email' => 'maria@projecthub.app',
            'password' => Hash::make('password'),
            'position' => 'Operations Manager',
            'department' => 'Operations',
            'phone' => '(11) 98765-4321',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);
        $admin->assignRole('Admin');

        // Project Managers
        $pm1 = User::create([
            'name' => 'João Santos',
            'email' => 'joao@projecthub.app',
            'password' => Hash::make('password'),
            'position' => 'Senior Project Manager',
            'department' => 'Projects',
            'phone' => '(11) 98765-1111',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);
        $pm1->assignRole('Project Manager');

        $pm2 = User::create([
            'name' => 'Ana Costa',
            'email' => 'ana@projecthub.app',
            'password' => Hash::make('password'),
            'position' => 'Project Manager',
            'department' => 'Projects',
            'phone' => '(11) 98765-2222',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);
        $pm2->assignRole('Project Manager');

        // Developers
        $dev1 = User::create([
            'name' => 'Carlos Oliveira',
            'email' => 'carlos@projecthub.app',
            'password' => Hash::make('password'),
            'position' => 'Senior Full-Stack Developer',
            'department' => 'Development',
            'phone' => '(11) 98765-3333',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);
        $dev1->assignRole('Developer');

        $dev2 = User::create([
            'name' => 'Patricia Lima',
            'email' => 'patricia@projecthub.app',
            'password' => Hash::make('password'),
            'position' => 'Front-End Developer',
            'department' => 'Development',
            'phone' => '(11) 98765-4444',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);
        $dev2->assignRole('Developer');

        $dev3 = User::create([
            'name' => 'Roberto Ferreira',
            'email' => 'roberto@projecthub.app',
            'password' => Hash::make('password'),
            'position' => 'Back-End Developer',
            'department' => 'Development',
            'phone' => '(11) 98765-5555',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);
        $dev3->assignRole('Developer');

        // Designers
        $designer1 = User::create([
            'name' => 'Juliana Martins',
            'email' => 'juliana@projecthub.app',
            'password' => Hash::make('password'),
            'position' => 'UI/UX Designer',
            'department' => 'Design',
            'phone' => '(11) 98765-6666',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);
        $designer1->assignRole('Designer');

        // Account Manager
        $am = User::create([
            'name' => 'Fernando Alves',
            'email' => 'fernando@projecthub.app',
            'password' => Hash::make('password'),
            'position' => 'Account Manager',
            'department' => 'Sales',
            'phone' => '(11) 98765-7777',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);
        $am->assignRole('Account Manager');
    }
}
