<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Lead;
use App\Models\LeadNote;
use App\Models\User;
use Illuminate\Database\Seeder;

class LeadSeeder extends Seeder
{
    public function run(): void
    {
        $am = User::where('email', 'fernando@projecthub.app')->first();
        $pm = User::where('email', 'joao@projecthub.app')->first();
        $client1 = Client::where('company_name', 'StartUp XYZ Ltda')->first();

        // Lead 1 - Em negociação
        $lead1 = Lead::create([
            'name' => 'Diego Carvalho',
            'company' => 'Finance Tech Ltda',
            'email' => 'diego@financetech.com',
            'phone' => '(11) 97777-1111',
            'source' => 'referral',
            'status' => 'negotiation',
            'priority' => 'high',
            'estimated_value' => 95000.00,
            'description' => 'Sistema de gestão financeira para fintech em crescimento. Precisam de um portal web + app mobile.',
            'expected_close_date' => now()->addWeeks(3),
            'assigned_to' => $am?->id,
        ]);

        LeadNote::create([
            'lead_id' => $lead1->id,
            'user_id' => $am?->id,
            'content' => 'Reunião realizada. Cliente muito interessado. Aguardando aprovação do board para fechar.',
        ]);

        LeadNote::create([
            'lead_id' => $lead1->id,
            'user_id' => $am?->id,
            'content' => 'Follow-up agendado para próxima semana.',
        ]);

        // Lead 2 - Proposta enviada
        $lead2 = Lead::create([
            'name' => 'Camila Rocha',
            'company' => 'EduTech Brasil S/A',
            'email' => 'camila@edutechbrasil.com',
            'phone' => '(21) 96666-2222',
            'source' => 'website',
            'status' => 'proposal_sent',
            'priority' => 'high',
            'estimated_value' => 45000.00,
            'description' => 'Plataforma de cursos online. Necessitam de CMS, área do aluno e relatórios.',
            'expected_close_date' => now()->addMonth(),
            'assigned_to' => $am?->id,
        ]);

        LeadNote::create([
            'lead_id' => $lead2->id,
            'user_id' => $am?->id,
            'content' => 'Proposta enviada. Aguardando retorno em 10 dias.',
        ]);

        // Lead 3 - Qualificado
        Lead::create([
            'name' => 'Henrique Nunes',
            'company' => 'Logística Express Ltda',
            'email' => 'henrique@logisticaexpress.com',
            'phone' => '(11) 95555-3333',
            'source' => 'social_media',
            'status' => 'qualified',
            'priority' => 'medium',
            'estimated_value' => 30000.00,
            'description' => 'Sistema de rastreamento de entregas. Integração com API dos Correios.',
            'expected_close_date' => now()->addMonths(2),
            'assigned_to' => $pm?->id,
        ]);

        // Lead 4 - Novo
        Lead::create([
            'name' => 'Fernanda Souza',
            'company' => 'Clínica Bem Estar',
            'email' => 'fernanda@clinicabemestar.com',
            'phone' => '(11) 94444-4444',
            'source' => 'event',
            'status' => 'new',
            'priority' => 'medium',
            'estimated_value' => 18000.00,
            'description' => 'Sistema de agendamento online e prontuário eletrônico.',
            'expected_close_date' => now()->addMonths(2),
            'assigned_to' => $am?->id,
        ]);

        // Lead 5 - Convertido (cliente StartUp XYZ)
        Lead::create([
            'name' => 'Bruno Lima',
            'company' => 'StartUp XYZ Ltda',
            'email' => 'bruno@startupxyz.com',
            'phone' => '(11) 93333-5555',
            'source' => 'cold_call',
            'status' => 'converted',
            'priority' => 'high',
            'estimated_value' => 60000.00,
            'description' => 'App mobile para gestão interna.',
            'expected_close_date' => now()->subWeeks(2),
            'converted_client_id' => $client1?->id,
            'converted_at' => now()->subWeek(),
            'assigned_to' => $am?->id,
        ]);

        // Lead 6 - Perdido
        Lead::create([
            'name' => 'Marcelo Castro',
            'company' => 'RetailMax Ltda',
            'email' => 'marcelo@retailmax.com',
            'phone' => '(11) 92222-6666',
            'source' => 'referral',
            'status' => 'lost',
            'priority' => 'low',
            'estimated_value' => 25000.00,
            'description' => 'E-commerce B2B. Foram com outra agência por preço.',
            'assigned_to' => $am?->id,
        ]);
    }
}
