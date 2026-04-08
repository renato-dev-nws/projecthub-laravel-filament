<?php

namespace Database\Seeders;

use App\Models\ServiceCategory;
use Illuminate\Database\Seeder;

class ServiceCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Programação PHP',       'slug' => 'programacao-php',     'is_active' => true, 'sort_order' => 1],
            ['name' => 'Programação Go',        'slug' => 'programacao-go',      'is_active' => true, 'sort_order' => 2],
            ['name' => 'Programação Vue',       'slug' => 'programacao-vue',     'is_active' => true, 'sort_order' => 3],
            ['name' => 'Programação NodeJS',    'slug' => 'programacao-nodejs',  'is_active' => true, 'sort_order' => 4],
            ['name' => 'Design Gráfico',        'slug' => 'design-grafico',      'is_active' => true, 'sort_order' => 5],
            ['name' => 'Composição de Estilos', 'slug' => 'composicao-estilos',  'is_active' => true, 'sort_order' => 6],
            ['name' => 'Infraestrutura',        'slug' => 'infraestrutura',      'is_active' => true, 'sort_order' => 7],
            ['name' => 'Manutenção',            'slug' => 'manutencao',          'is_active' => true, 'sort_order' => 8],
            ['name' => 'Suporte',               'slug' => 'suporte',             'is_active' => true, 'sort_order' => 9],
        ];

        foreach ($categories as $data) {
            ServiceCategory::firstOrCreate(['slug' => $data['slug']], $data);
        }
    }
}
