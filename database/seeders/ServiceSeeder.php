<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        $services = [
            [
                'name' => 'Desenvolvimento Web',
                'code' => 'DEV-WEB',
                'description' => 'Desenvolvimento de websites e aplicações web responsivas',
                'unit_type' => 'hora',
                'default_price' => 150.00,
                'is_active' => true,
            ],
            [
                'name' => 'Desenvolvimento Mobile',
                'code' => 'DEV-MOB',
                'description' => 'Desenvolvimento de aplicativos mobile para iOS e Android',
                'unit_type' => 'hora',
                'default_price' => 180.00,
                'is_active' => true,
            ],
            [
                'name' => 'UI/UX Design',
                'code' => 'DESIGN-UX',
                'description' => 'Design de interfaces e experiência do usuário',
                'unit_type' => 'hora',
                'default_price' => 120.00,
                'is_active' => true,
            ],
            [
                'name' => 'Consultoria Técnica',
                'code' => 'CONS-TEC',
                'description' => 'Consultoria técnica especializada em arquitetura e boas práticas',
                'unit_type' => 'hora',
                'default_price' => 200.00,
                'is_active' => true,
            ],
            [
                'name' => 'API Development',
                'code' => 'DEV-API',
                'description' => 'Desenvolvimento de APIs RESTful e GraphQL',
                'unit_type' => 'hora',
                'default_price' => 160.00,
                'is_active' => true,
            ],
            [
                'name' => 'DevOps',
                'code' => 'DEVOPS',
                'description' => 'Configuração de CI/CD, deploy e infraestrutura',
                'unit_type' => 'hora',
                'default_price' => 170.00,
                'is_active' => true,
            ],
            [
                'name' => 'Manutenção Mensal',
                'code' => 'MANUT-MES',
                'description' => 'Pacote de manutenção e suporte mensal',
                'unit_type' => 'pacote',
                'default_price' => 2500.00,
                'is_active' => true,
            ],
            [
                'name' => 'Code Review',
                'code' => 'CODE-REV',
                'description' => 'Revisão de código e análise de qualidade',
                'unit_type' => 'hora',
                'default_price' => 140.00,
                'is_active' => true,
            ],
            [
                'name' => 'Treinamento',
                'code' => 'TRAIN',
                'description' => 'Treinamento técnico para equipes',
                'unit_type' => 'hora',
                'default_price' => 250.00,
                'is_active' => true,
            ],
            [
                'name' => 'SEO e Performance',
                'code' => 'SEO-PERF',
                'description' => 'Otimização de SEO e performance de aplicações',
                'unit_type' => 'projeto',
                'default_price' => 5000.00,
                'is_active' => true,
            ],
        ];

        foreach ($services as $service) {
            Service::create($service);
        }
    }
}
