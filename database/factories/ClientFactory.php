<?php

namespace Database\Factories;

use App\Models\Client;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Client>
 */
class ClientFactory extends Factory
{
    public function definition(): array
    {
        return [
            'company_name' => fake()->company(),
            'type'         => 'pessoa_juridica',
            'cnpj'         => fake()->unique()->numerify('##.###.###/####-##'),
            'email'        => fake()->unique()->companyEmail(),
            'phone'        => fake()->numerify('(##) #####-####'),
            'status'       => 'active',
            'country'      => 'BR',
        ];
    }

    public function prospect(): static
    {
        return $this->state(['status' => 'prospect']);
    }
}
