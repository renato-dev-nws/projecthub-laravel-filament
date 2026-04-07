<?php

namespace Database\Factories;

use App\Models\Client;
use App\Models\ClientPortalUser;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * @extends Factory<ClientPortalUser>
 */
class ClientPortalUserFactory extends Factory
{
    public function definition(): array
    {
        return [
            'client_id'          => Client::factory(),
            'name'               => fake()->name(),
            'email'              => fake()->unique()->safeEmail(),
            'password'           => Hash::make('password'),
            'is_active'          => true,
        ];
    }

    public function inactive(): static
    {
        return $this->state(['is_active' => false]);
    }
}
