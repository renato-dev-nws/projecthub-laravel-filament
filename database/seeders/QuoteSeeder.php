<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Lead;
use App\Models\Quote;
use App\Models\QuoteItem;
use App\Models\Service;
use App\Models\User;
use Illuminate\Database\Seeder;

class QuoteSeeder extends Seeder
{
    public function run(): void
    {
        $am = User::where('email', 'fernando@projecthub.app')->first();
        $client1 = Client::where('company_name', 'Tech Solutions Brasil Ltda')->first();
        $client2 = Client::where('company_name', 'Inovação Digital S/A')->first();
        $lead = Lead::where('company', 'Finance Tech Ltda')->first();

        $devWeb = Service::where('code', 'BACKEND-LARAVEL')->firstOrFail();
        $designUx = Service::where('code', 'CRIACAO-LAYOUT')->firstOrFail();
        $devApi = Service::where('code', 'API-LARAVEL')->firstOrFail();
        $devMob = Service::where('code', 'FRONTEND-REACT')->firstOrFail();
        $devOps = Service::where('code', 'CONFIG-INFRA')->firstOrFail();

        // Quote 1 - Aprovada
        $quote1 = Quote::create([
            'client_id' => $client1->id,
            'number' => 'ORC-2026-001',
            'title' => 'Portal E-commerce Tech Solutions',
            'description' => 'Desenvolvimento completo de plataforma e-commerce.',
            'status' => 'approved',
            'subtotal' => 110000.00,
            'discount_percent' => 8.33,
            'discount_value' => 10000.00,
            'tax_percent' => 0,
            'tax_value' => 0,
            'total' => 100000.00,
            'currency' => 'BRL',
            'valid_until' => now()->addMonths(2),
            'terms_conditions' => "Pagamento em 3 parcelas:\n- 30% na assinatura\n- 40% na entrega do back-end\n- 30% no lançamento",
            'created_by' => $am?->id,
            'approved_at' => now()->subMonths(2),
            'sent_at' => now()->subMonths(2)->subWeek(),
            'viewed_at' => now()->subMonths(2)->subDays(5),
        ]);

        QuoteItem::create(['quote_id' => $quote1->id, 'service_id' => $devWeb->id, 'description' => 'Desenvolvimento Front-end + Back-end', 'quantity' => 400, 'unit_price' => 90.00, 'sort_order' => 1]);
        QuoteItem::create(['quote_id' => $quote1->id, 'service_id' => $designUx->id, 'description' => 'UI/UX Design completo', 'quantity' => 100, 'unit_price' => 120.00, 'sort_order' => 2]);
        QuoteItem::create(['quote_id' => $quote1->id, 'service_id' => $devApi->id, 'description' => 'API e Integrações', 'quantity' => 150, 'unit_price' => 90.00, 'sort_order' => 3]);
        QuoteItem::create(['quote_id' => $quote1->id, 'service_id' => $devOps->id, 'description' => 'Setup DevOps + Deploy', 'quantity' => 80, 'unit_price' => 100.00, 'sort_order' => 4]);

        // Quote 2 - Visualizada
        $quote2 = Quote::create([
            'client_id' => $client2->id,
            'number' => 'ORC-2026-002',
            'title' => 'App Mobile Inovação Digital',
            'description' => 'Desenvolvimento de aplicativo mobile iOS e Android.',
            'status' => 'viewed',
            'subtotal' => 78000.00,
            'discount_percent' => 0,
            'discount_value' => 0,
            'tax_percent' => 0,
            'tax_value' => 0,
            'total' => 78000.00,
            'currency' => 'BRL',
            'valid_until' => now()->addMonth(),
            'terms_conditions' => "Pagamento em 4 parcelas mensais iguais.",
            'created_by' => $am?->id,
            'sent_at' => now()->subWeek(),
            'viewed_at' => now()->subDays(3),
        ]);

        QuoteItem::create(['quote_id' => $quote2->id, 'service_id' => $devMob->id, 'description' => 'App iOS + Android (React Native)', 'quantity' => 380, 'unit_price' => 90.00, 'sort_order' => 1]);
        QuoteItem::create(['quote_id' => $quote2->id, 'service_id' => $designUx->id, 'description' => 'Design das telas mobile', 'quantity' => 80, 'unit_price' => 120.00, 'sort_order' => 2]);

        // Quote 3 - Rascunho para lead
        $quote3 = Quote::create([
            'client_id' => null,
            'lead_id' => $lead?->id,
            'number' => 'ORC-2026-003',
            'title' => 'Sistema Finance Tech',
            'description' => 'Portal web + App mobile para gestão financeira.',
            'status' => 'draft',
            'subtotal' => 94800.00,
            'discount_percent' => 5,
            'discount_value' => 4740.00,
            'tax_percent' => 0,
            'tax_value' => 0,
            'total' => 90060.00,
            'currency' => 'BRL',
            'valid_until' => now()->addMonths(2),
            'created_by' => $am?->id,
        ]);

        QuoteItem::create(['quote_id' => $quote3->id, 'service_id' => $devWeb->id, 'description' => 'Portal Web', 'quantity' => 300, 'unit_price' => 90.00, 'sort_order' => 1]);
        QuoteItem::create(['quote_id' => $quote3->id, 'service_id' => $devMob->id, 'description' => 'App Mobile', 'quantity' => 250, 'unit_price' => 90.00, 'sort_order' => 2]);
        QuoteItem::create(['quote_id' => $quote3->id, 'service_id' => $designUx->id, 'description' => 'Design', 'quantity' => 40, 'unit_price' => 120.00, 'sort_order' => 3]);
    }
}
