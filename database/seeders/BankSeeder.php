<?php

namespace Database\Seeders;

use App\Models\Bank;
use Illuminate\Database\Seeder;

class BankSeeder extends Seeder
{
    public function run(): void
    {
        $banks = [
            ['name' => 'Itaú Unibanco', 'code' => '341', 'agency' => '0001', 'account_number' => '12345-6', 'balance' => 50000.00, 'is_active' => true],
            ['name' => 'Nubank',        'code' => '260', 'agency' => '0001', 'account_number' => '78901-2', 'balance' => 15000.00, 'is_active' => true],
            ['name' => 'Bradesco',      'code' => '237', 'agency' => '0042', 'account_number' => '99887-7', 'balance' => 8500.00,  'is_active' => true],
        ];

        foreach ($banks as $data) {
            Bank::firstOrCreate(['code' => $data['code']], $data);
        }
    }
}
