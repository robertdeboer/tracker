<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\User;
use App\Models\WorkItem;
use App\Models\WorkItemUser;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<WorkItemUser>
 */
class WorkItemUserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            WorkItemUser::USER_ID      => User::factory()->create()->id,
            WorkItemUser::WORK_ITEM_ID => WorkItem::factory()->create()->id
        ];
    }
}
