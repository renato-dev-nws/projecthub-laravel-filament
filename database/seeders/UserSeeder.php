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
                'job_titles' => ['CEO', 'Founder'],
                'skills' => ['Gestão', 'Estratégia', 'Negociação'],
                'department' => 'Executive',
                'city' => 'São Paulo - SP',
                'bio' => 'Responsável pela estratégia da operação e relacionamento com clientes-chave.',
                'is_active' => true,
            ]);
        }

        // Admin
        $admin = User::create([
            'name' => 'Maria Silva',
            'email' => 'maria@projecthub.app',
            'password' => Hash::make('password'),
            'position' => 'Operations Manager',
            'job_titles' => ['Operations Manager'],
            'skills' => ['Processos', 'KPIs', 'People Ops'],
            'department' => 'Operations',
            'phone' => '(11) 98765-4321',
            'city' => 'São Paulo - SP',
            'bio' => 'Coordena os processos internos e a qualidade das entregas.',
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
            'job_titles' => ['Project Manager', 'Scrum Master'],
            'skills' => ['Gestão Ágil', 'Planejamento', 'Comunicação'],
            'department' => 'Projects',
            'phone' => '(11) 98765-1111',
            'city' => 'Campinas - SP',
            'bio' => 'Gerencia projetos complexos com foco em previsibilidade e qualidade.',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);
        $pm1->assignRole('Project Manager');

        $pm2 = User::create([
            'name' => 'Ana Costa',
            'email' => 'ana@projecthub.app',
            'password' => Hash::make('password'),
            'position' => 'Project Manager',
            'job_titles' => ['Project Manager'],
            'skills' => ['Kanban', 'Gestão de Escopo', 'Stakeholders'],
            'department' => 'Projects',
            'phone' => '(11) 98765-2222',
            'city' => 'São Paulo - SP',
            'bio' => 'Conduz o ciclo de projetos do discovery ao go-live.',
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
            'job_titles' => ['Tech Lead', 'Full-Stack Developer'],
            'skills' => ['Laravel', 'Vue.js', 'PostgreSQL', 'Arquitetura'],
            'department' => 'Development',
            'phone' => '(11) 98765-3333',
            'city' => 'Santos - SP',
            'bio' => 'Atua em arquitetura e desenvolvimento de funcionalidades críticas.',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);
        $dev1->assignRole('Developer');

        $dev2 = User::create([
            'name' => 'Patricia Lima',
            'email' => 'patricia@projecthub.app',
            'password' => Hash::make('password'),
            'position' => 'Front-End Developer',
            'job_titles' => ['Front-End Developer'],
            'skills' => ['Tailwind', 'Alpine.js', 'UX'],
            'department' => 'Development',
            'phone' => '(11) 98765-4444',
            'city' => 'São Paulo - SP',
            'bio' => 'Especialista em interfaces e experiência do usuário.',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);
        $dev2->assignRole('Developer');

        $dev3 = User::create([
            'name' => 'Roberto Ferreira',
            'email' => 'roberto@projecthub.app',
            'password' => Hash::make('password'),
            'position' => 'Back-End Developer',
            'job_titles' => ['Back-End Developer'],
            'skills' => ['APIs', 'Laravel', 'Integrações'],
            'department' => 'Development',
            'phone' => '(11) 98765-5555',
            'city' => 'Guarulhos - SP',
            'bio' => 'Focado em regras de negócio e integrações externas.',
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
            'job_titles' => ['Product Designer', 'UI/UX Designer'],
            'skills' => ['Figma', 'Design System', 'Prototipação'],
            'department' => 'Design',
            'phone' => '(11) 98765-6666',
            'city' => 'São Bernardo do Campo - SP',
            'bio' => 'Desenha experiências digitais orientadas a resultado.',
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
            'job_titles' => ['Account Manager', 'Customer Success'],
            'skills' => ['Relacionamento', 'Vendas Consultivas', 'Retenção'],
            'department' => 'Sales',
            'phone' => '(11) 98765-7777',
            'city' => 'São Paulo - SP',
            'bio' => 'Ponto focal entre cliente e time de entrega.',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);
        $am->assignRole('Account Manager');

        // Financeiro
        $financial = User::create([
            'name' => 'Ricardo Nogueira',
            'email' => 'ricardo.financeiro@projecthub.app',
            'password' => Hash::make('password'),
            'position' => 'Financial Analyst',
            'job_titles' => ['Financial Analyst'],
            'skills' => ['Fluxo de Caixa', 'Conciliação', 'Relatórios'],
            'department' => 'Finance',
            'phone' => '(11) 98765-8888',
            'city' => 'São Paulo - SP',
            'bio' => 'Responsável por análises financeiras e acompanhamento de custos.',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);
        $financial->assignRole('Financial');
    }
}
