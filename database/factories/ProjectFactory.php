<?php

namespace Database\Factories;

use App\Models\Client;
use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Project>
 */
class ProjectFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name'             => fake()->words(3, true),
            'code'             => strtoupper(fake()->unique()->lexify('PROJ-????')),
            'status'           => 'planning',
            'priority'         => 'medium',
            'progress_percent' => 0,
            'logged_hours'     => 0,
            'spent'            => 0,
            'color'            => '#6366f1',
            'client_id'        => Client::factory(),
            'project_manager_id' => User::factory(),
        ];
    }

    public function active(): static
    {
        return $this->state(['status' => 'active']);
    }

    public function completed(): static
    {
        return $this->state(['status' => 'completed', 'progress_percent' => 100]);
    }
}
