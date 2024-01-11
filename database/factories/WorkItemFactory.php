<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Project;
use App\Models\User;
use App\Models\WorkItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<WorkItem>
 */
class WorkItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            WorkItem::PROJECT_ID  => Project::factory()->create()->id,
            WorkItem::OWNER_ID    => User::factory()->create()->id,
            WorkItem::NAME        => $this->faker->unique()->words(5, true),
            WorkItem::IS_OPEN     => $this->faker->boolean(),
            WorkItem::START_DATE  => date('Y-m-d H:i:s'),
            WorkItem::END_DATE    => null,
            WorkItem::TICKET_DATA => []
        ];
    }
}
