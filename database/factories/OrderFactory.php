<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Order;
use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            Order::REFERENCE_NUMBER => $this->faker->unique()->lexify('????????'),
            Order::DATE             => date('Y-m-d H:i:s'),
            Order::EMAIL            => $this->faker->email(),
            Order::HOURS            => $this->faker->numberBetween(1, 100),
            Order::PROJECT_ID       => Project::factory()->create()->id
        ];
    }
}
