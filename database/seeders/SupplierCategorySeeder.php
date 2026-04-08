<?php

namespace Database\Seeders;

use App\Models\SupplierCategory;
use Illuminate\Database\Seeder;

class SupplierCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Tecnologia',    'slug' => 'tecnologia',    'is_active' => true, 'sort_order' => 1],
            ['name' => 'Design',        'slug' => 'design',        'is_active' => true, 'sort_order' => 2],
            ['name' => 'Marketing',     'slug' => 'marketing',     'is_active' => true, 'sort_order' => 3],
            ['name' => 'Jurídico',      'slug' => 'juridico',      'is_active' => true, 'sort_order' => 4],
            ['name' => 'Contabilidade', 'slug' => 'contabilidade', 'is_active' => true, 'sort_order' => 5],
        ];

        foreach ($categories as $data) {
            SupplierCategory::firstOrCreate(['slug' => $data['slug']], $data);
        }
    }
}
