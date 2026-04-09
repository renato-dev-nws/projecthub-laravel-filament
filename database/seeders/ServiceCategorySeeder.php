<?php

namespace Database\Seeders;

use App\Models\ServiceCategory;
use Illuminate\Database\Seeder;

class ServiceCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Programação PHP', 'slug' => 'programacao-php', 'is_active' => true, 'sort_order' => 1],
            ['name' => 'Programação Go', 'slug' => 'programacao-go', 'is_active' => true, 'sort_order' => 2],
            ['name' => 'Programação NodeJS', 'slug' => 'programacao-nodejs', 'is_active' => true, 'sort_order' => 3],
            ['name' => 'Programação JS/TS', 'slug' => 'programacao-jsts', 'is_active' => true, 'sort_order' => 4],
            ['name' => 'Programação JSX/TSX', 'slug' => 'programacao-jsxtsx', 'is_active' => true, 'sort_order' => 5],
            ['name' => 'Web Design', 'slug' => 'web-design', 'is_active' => true, 'sort_order' => 6],
            ['name' => 'Arquitetura de Software', 'slug' => 'arquitetura-software', 'is_active' => true, 'sort_order' => 7],
            ['name' => 'Engenharia de Software', 'slug' => 'engenharia-software', 'is_active' => true, 'sort_order' => 8],
            ['name' => 'Cloud Engineering', 'slug' => 'cloud-engineering', 'is_active' => true, 'sort_order' => 9],
            ['name' => 'Suporte', 'slug' => 'suporte', 'is_active' => true, 'sort_order' => 10],
            ['name' => 'Manutenção', 'slug' => 'manutencao', 'is_active' => true, 'sort_order' => 11],
        ];

        foreach ($categories as $data) {
            ServiceCategory::firstOrCreate(['slug' => $data['slug']], $data);
        }
    }
}
