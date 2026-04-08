<?php

namespace Database\Seeders;

use App\Models\FinancialCategory;
use Illuminate\Database\Seeder;

class FinancialCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Infraestrutura',       'type' => 'expense', 'is_active' => true, 'sort_order' => 1],
            ['name' => 'Prestação de Serviço', 'type' => 'income',  'is_active' => true, 'sort_order' => 2],
            ['name' => 'Administrativa',       'type' => 'expense', 'is_active' => true, 'sort_order' => 3],
            ['name' => 'Marketing',            'type' => 'expense', 'is_active' => true, 'sort_order' => 4],
            ['name' => 'Receita de Projeto',   'type' => 'income',  'is_active' => true, 'sort_order' => 5],
        ];

        foreach ($categories as $data) {
            FinancialCategory::firstOrCreate(['name' => $data['name']], $data);
        }
    }
}
