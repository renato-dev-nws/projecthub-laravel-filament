<?php

namespace App\Services;

use App\Models\ServicePricingTier;
use App\Models\Service;

class PricingCalculatorService
{
    public function getPriceForHours(int $serviceId, float $hours): float
    {
        $tier = ServicePricingTier::where('service_id', $serviceId)
            ->where('min_hours', '<=', $hours)
            ->where(function ($q) use ($hours) {
                $q->whereNull('max_hours')
                  ->orWhere('max_hours', '>=', $hours);
            })
            ->orderBy('min_hours', 'desc')
            ->first();

        if ($tier) {
            return (float) $tier->price_per_hour;
        }

        return (float) (Service::find($serviceId)?->default_price ?? 0);
    }
}
