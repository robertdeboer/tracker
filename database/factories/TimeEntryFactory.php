<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\TimeEntry;
use App\Models\User;
use App\Models\WorkItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TimeEntry>
 */
class TimeEntryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            TimeEntry::WORK_ITEM_ID => WorkItem::factory()->create()->id,
            TimeEntry::AUTHOR_ID    => User::factory()->create()->id,
            TimeEntry::HOURS        => $this->faker->numberBetween(1, 100),
            TimeEntry::DATE         => date('Y-m-d H:i:s'),
            TimeEntry::NOTE         => $this->faker->sentence()
        ];
    }
}
