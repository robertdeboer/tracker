<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Project>
 */
class ProjectFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            Project::NAME               => $this->faker->unique()->words(3, true),
            Project::IS_ACTIVE          => $this->faker->boolean(),
            Project::DESCRIPTION        => $this->faker->sentences(3, true),
            Project::CUSTOMER_ID        => User::factory()->create()->id,
            Project::PROJECT_MANAGER_ID => User::factory()->create()->id,
        ];
    }
}
