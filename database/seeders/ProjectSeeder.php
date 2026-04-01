<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Project;
use App\Models\ProjectMember;
use App\Models\ProjectPhase;
use App\Models\ProjectTask;
use App\Models\User;
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{
    public function run(): void
    {
        $client1 = Client::where('company_name', 'Tech Solutions Brasil Ltda')->first();
        $client2 = Client::where('company_name', 'Inovação Digital S/A')->first();

        $pm1 = User::where('email', 'joao@projecthub.app')->first();
        $pm2 = User::where('email', 'ana@projecthub.app')->first();
        $dev1 = User::where('email', 'carlos@projecthub.app')->first();
        $dev2 = User::where('email', 'patricia@projecthub.app')->first();
        $designer = User::where('email', 'juliana@projecthub.app')->first();

        // Projeto 1 - Ativo
        $project1 = Project::create([
            'name' => 'Portal E-commerce Tech Solutions',
            'code' => 'PROJ-001',
            'client_id' => $client1->id,
            'project_manager_id' => $pm1->id,
            'description' => 'Desenvolvimento de plataforma completa de e-commerce com integração de pagamento e gestão de estoque',
            'status' => 'active',
            'priority' => 'high',
            'start_date' => now()->subMonths(2),
            'end_date' => now()->addMonths(2),
            'budget' => 120000.00,
            'progress_percent' => 45,
            'client_portal_enabled' => true,
            'color' => '#6366f1',
        ]);

        ProjectMember::create(['project_id' => $project1->id, 'user_id' => $pm1->id, 'role' => 'manager']);
        ProjectMember::create(['project_id' => $project1->id, 'user_id' => $dev1->id, 'role' => 'developer']);
        ProjectMember::create(['project_id' => $project1->id, 'user_id' => $dev2->id, 'role' => 'developer']);
        ProjectMember::create(['project_id' => $project1->id, 'user_id' => $designer->id, 'role' => 'designer']);

        $phase1 = ProjectPhase::create([
            'project_id' => $project1->id,
            'name' => 'Planejamento e Design',
            'description' => 'Definição de requisitos e criação do design',
            'start_date' => now()->subMonths(2),
            'end_date' => now()->subMonth(),
            'status' => 'completed',
            'sort_order' => 1,
        ]);

        $phase2 = ProjectPhase::create([
            'project_id' => $project1->id,
            'name' => 'Desenvolvimento Back-end',
            'description' => 'Desenvolvimento da API e integrações',
            'start_date' => now()->subMonth(),
            'end_date' => now()->addWeeks(2),
            'status' => 'in_progress',
            'sort_order' => 2,
        ]);

        ProjectTask::create([
            'project_id' => $project1->id,
            'phase_id' => $phase2->id,
            'title' => 'Implementar API de Produtos',
            'description' => 'Criar endpoints RESTful para gestão de produtos',
            'status' => 'in_progress',
            'priority' => 'high',
            'assigned_to' => $dev1->id,
            'due_date' => now()->addWeek(),
            'estimated_hours' => 40,
            'logged_hours' => 20,
        ]);

        ProjectTask::create([
            'project_id' => $project1->id,
            'phase_id' => $phase2->id,
            'title' => 'Integração Gateway de Pagamento',
            'description' => 'Integrar com Stripe e PagSeguro',
            'status' => 'todo',
            'priority' => 'high',
            'assigned_to' => $dev1->id,
            'due_date' => now()->addWeeks(2),
            'estimated_hours' => 30,
        ]);

        // Projeto 2 - Planejamento
        $project2 = Project::create([
            'name' => 'App Mobile Inovação Digital',
            'code' => 'PROJ-002',
            'client_id' => $client2->id,
            'project_manager_id' => $pm2->id,
            'description' => 'Aplicativo mobile iOS e Android para gestão empresarial',
            'status' => 'planning',
            'priority' => 'medium',
            'start_date' => now()->addWeek(),
            'end_date' => now()->addMonths(4),
            'budget' => 80000.00,
            'progress_percent' => 5,
            'client_portal_enabled' => true,
            'color' => '#10b981',
        ]);

        ProjectMember::create(['project_id' => $project2->id, 'user_id' => $pm2->id, 'role' => 'manager']);
        ProjectMember::create(['project_id' => $project2->id, 'user_id' => $dev2->id, 'role' => 'developer']);
        ProjectMember::create(['project_id' => $project2->id, 'user_id' => $designer->id, 'role' => 'designer']);

        ProjectPhase::create([
            'project_id' => $project2->id,
            'name' => 'Discovery',
            'description' => 'Levantamento de requisitos e análise',
            'start_date' => now()->addWeek(),
            'end_date' => now()->addWeeks(3),
            'status' => 'pending',
            'sort_order' => 1,
        ]);
    }
}
