<?php

namespace Database\Seeders;

use App\Models\ContractClause;
use Illuminate\Database\Seeder;

class ContractClauseSeeder extends Seeder
{
    public function run(): void
    {
        $clauses = [
            [
                'title' => 'Objeto do contrato',
                'content' => 'A CONTRATADA executará os serviços descritos neste documento, respeitando o escopo acordado.',
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'title' => 'Prazos e entregas',
                'content' => 'Os prazos serão organizados por fases e roadmap, podendo sofrer ajustes mediante acordo entre as partes.',
                'sort_order' => 2,
                'is_active' => true,
            ],
            [
                'title' => 'Condições gerais',
                'content' => 'Este contrato entra em vigor na data de sua emissão e poderá ser revisado mediante aditivo formal.',
                'sort_order' => 3,
                'is_active' => true,
            ],
        ];

        foreach ($clauses as $clause) {
            ContractClause::updateOrCreate(
                ['title' => $clause['title']],
                $clause
            );
        }
    }
}
