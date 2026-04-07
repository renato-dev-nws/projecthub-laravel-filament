<?php

namespace Database\Factories;

use App\Models\Quote;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Quote>
 */
class QuoteFactory extends Factory
{
    public function definition(): array
    {
        return [
            'number'           => 'QT-' . fake()->unique()->numerify('#####'),
            'title'            => fake()->sentence(4),
            'status'           => 'draft',
            'currency'         => 'BRL',
            'subtotal'         => 0,
            'discount_percent' => 0,
            'discount_value'   => 0,
            'tax_percent'      => 0,
            'tax_value'        => 0,
            'total'            => 0,
            'created_by'       => User::factory(),
        ];
    }

    public function draft(): static
    {
        return $this->state(['status' => 'draft']);
    }

    public function sent(): static
    {
        return $this->state(['status' => 'sent']);
    }

    public function approved(): static
    {
        return $this->state(['status' => 'approved']);
    }
}
