<?php

namespace Database\Seeders;

use App\Models\Supplier;
use App\Models\SupplierCategory;
use Illuminate\Database\Seeder;

class SupplierSeeder extends Seeder
{
    public function run(): void
    {
        $tech   = SupplierCategory::where('slug', 'tecnologia')->first();
        $design = SupplierCategory::where('slug', 'design')->first();
        $mkg    = SupplierCategory::where('slug', 'marketing')->first();

        $suppliers = [
            ['name' => 'AWS Brazil',          'email' => 'billing@aws.com',        'phone' => null,           'supplier_category_id' => $tech?->id],
            ['name' => 'Studio Criativo XYZ', 'email' => 'contato@studio.com.br',  'phone' => '(11) 99999-0001', 'supplier_category_id' => $design?->id],
            ['name' => 'Google Workspace',    'email' => 'billing@google.com',     'phone' => null,           'supplier_category_id' => $tech?->id],
            ['name' => 'Agência Crescer',     'email' => 'contato@crescer.com.br', 'phone' => '(11) 3333-4444', 'supplier_category_id' => $mkg?->id],
        ];

        foreach ($suppliers as $data) {
            Supplier::firstOrCreate(['name' => $data['name']], $data);
        }
    }
}
