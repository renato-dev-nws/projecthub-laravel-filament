<?php

namespace Database\Seeders;

use App\Models\Service;
use App\Models\ServicePricingTier;
use Illuminate\Database\Seeder;

class ServicePricingTierSeeder extends Seeder
{
    public function run(): void
    {
        $services = Service::all();

        foreach ($services as $service) {
            ServicePricingTier::updateOrCreate(
                [
                    'service_id' => $service->id,
                    'min_hours' => 0,
                    'max_hours' => null,
                ],
                [
                    'price_per_hour' => (float) $service->default_price,
                    'label' => 'Padrão',
                    'sort_order' => 1,
                ]
            );
        }
    }
}
