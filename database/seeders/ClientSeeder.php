<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\ClientContact;
use App\Models\ClientPortalUser;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class ClientSeeder extends Seeder
{
    public function run(): void
    {
        $accountManager = User::where('email', 'fernando@projecthub.app')->first();

        // Cliente 1 - Pessoa Jurídica
        $client1 = Client::create([
            'company_name' => 'Tech Solutions Brasil Ltda',
            'trade_name' => 'Tech Solutions',
            'cnpj' => '12.345.678/0001-90',
            'type' => 'pessoa_juridica',
            'email' => 'contato@techsolutions.com.br',
            'phone' => '(11) 3000-1000',
            'website' => 'https://techsolutions.com.br',
            'address' => 'Av. Paulista, 1000',
            'city' => 'São Paulo',
            'state' => 'SP',
            'postal_code' => '01310-100',
            'country' => 'Brasil',
            'status' => 'active',
            'account_manager_id' => $accountManager?->id,
            'contract_start_date' => now()->subMonths(6),
            'billing_email' => 'financeiro@techsolutions.com.br',
        ]);

        ClientContact::create([
            'client_id' => $client1->id,
            'name' => 'Ricardo Mendes',
            'email' => 'ricardo@techsolutions.com.br',
            'phone' => '(11) 99999-1111',
            'position' => 'CTO',
            'is_primary' => true,
            'can_access_portal' => true,
        ]);

        ClientPortalUser::create([
            'client_id' => $client1->id,
            'name' => 'Ricardo Mendes',
            'email' => 'ricardo@techsolutions.com.br',
            'password' => Hash::make('password'),
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        // Cliente 2 - Pessoa Jurídica
        $client2 = Client::create([
            'company_name' => 'Inovação Digital S/A',
            'trade_name' => 'Inovação Digital',
            'cnpj' => '98.765.432/0001-10',
            'type' => 'pessoa_juridica',
            'email' => 'contato@inovacaodigital.com',
            'phone' => '(21) 3200-2000',
            'website' => 'https://inovacaodigital.com',
            'address' => 'Rua das Flores, 500',
            'city' => 'Rio de Janeiro',
            'state' => 'RJ',
            'postal_code' => '20000-000',
            'country' => 'Brasil',
            'status' => 'active',
            'account_manager_id' => $accountManager?->id,
            'contract_start_date' => now()->subMonths(3),
            'billing_email' => 'financeiro@inovacaodigital.com',
        ]);

        ClientContact::create([
            'client_id' => $client2->id,
            'name' => 'Beatriz Santos',
            'email' => 'beatriz@inovacaodigital.com',
            'phone' => '(21) 99999-2222',
            'position' => 'Diretora de TI',
            'is_primary' => true,
            'can_access_portal' => true,
        ]);

        ClientPortalUser::create([
            'client_id' => $client2->id,
            'name' => 'Beatriz Santos',
            'email' => 'beatriz@inovacaodigital.com',
            'password' => Hash::make('password'),
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        // Cliente 3 - Pessoa Física
        $client3 = Client::create([
            'company_name' => 'Paulo Henrique Consultoria',
            'trade_name' => null,
            'cpf' => '123.456.789-00',
            'type' => 'pessoa_fisica',
            'email' => 'paulo@consultoria.com',
            'phone' => '(11) 98888-3333',
            'website' => null,
            'address' => 'Rua das Acácias, 123',
            'city' => 'São Paulo',
            'state' => 'SP',
            'postal_code' => '05000-000',
            'country' => 'Brasil',
            'status' => 'active',
            'account_manager_id' => $accountManager?->id,
            'contract_start_date' => now()->subMonth(),
            'billing_email' => 'paulo@consultoria.com',
        ]);

        ClientContact::create([
            'client_id' => $client3->id,
            'name' => 'Paulo Henrique',
            'email' => 'paulo@consultoria.com',
            'phone' => '(11) 98888-3333',
            'position' => 'Proprietário',
            'is_primary' => true,
            'can_access_portal' => true,
        ]);

        ClientPortalUser::create([
            'client_id' => $client3->id,
            'name' => 'Paulo Henrique',
            'email' => 'paulo@consultoria.com',
            'password' => Hash::make('password'),
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        // Cliente 4 - Prospect
        Client::create([
            'company_name' => 'StartUp XYZ Ltda',
            'trade_name' => 'StartUp XYZ',
            'cnpj' => '11.222.333/0001-44',
            'type' => 'pessoa_juridica',
            'email' => 'contato@startupxyz.com',
            'phone' => '(11) 97777-4444',
            'website' => 'https://startupxyz.com',
            'address' => 'Rua da Inovação, 100',
            'city' => 'São Paulo',
            'state' => 'SP',
            'postal_code' => '04000-000',
            'country' => 'Brasil',
            'status' => 'prospect',
            'account_manager_id' => $accountManager?->id,
        ]);
    }
}
