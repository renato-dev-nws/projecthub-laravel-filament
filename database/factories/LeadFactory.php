<?php

namespace Database\Factories;

use App\Models\Lead;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Lead>
 */
class LeadFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name'     => fake()->name(),
            'email'    => fake()->safeEmail(),
            'phone'    => fake()->numerify('(##) #####-####'),
            'company'  => fake()->company(),
            'status'   => 'new',
            'priority' => 'medium',
        ];
    }

    public function qualified(): static
    {
        return $this->state(['status' => 'qualified']);
    }

    public function converted(): static
    {
        return $this->state(['status' => 'converted']);
    }
}
