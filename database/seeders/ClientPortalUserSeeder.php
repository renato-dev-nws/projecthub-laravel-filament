<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\ClientPortalUser;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class ClientPortalUserSeeder extends Seeder
{
    public function run(): void
    {
        $portalUsers = [
            [
                'client_email'  => 'contato@techsolutions.com.br',
                'name'          => 'Ricardo Mendes',
                'email'         => 'ricardo@techsolutions.com.br',
            ],
            [
                'client_email'  => 'contato@inovacaodigital.com',
                'name'          => 'Beatriz Santos',
                'email'         => 'beatriz@inovacaodigital.com',
            ],
            [
                'client_email'  => 'paulo@consultoria.com',
                'name'          => 'Paulo Henrique',
                'email'         => 'paulo@consultoria.com',
            ],
        ];

        foreach ($portalUsers as $data) {
            $client = Client::where('email', $data['client_email'])->first();

            if (! $client) {
                continue;
            }

            ClientPortalUser::updateOrCreate(
                ['email' => $data['email']],
                [
                    'client_id'          => $client->id,
                    'name'               => $data['name'],
                    'password'           => Hash::make('password'),
                    'is_active'          => true,
                ]
            );
        }
    }
}
