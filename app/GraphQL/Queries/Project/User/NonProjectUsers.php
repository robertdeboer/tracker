<?php

declare(strict_types=1);

namespace App\GraphQL\Queries\Project\User;

use App\Models\Project;
use App\Models\User;
use App\Models\WorkItem;
use Illuminate\Database\Eloquent\Collection;

final readonly class NonProjectUsers
{
    /**
     * @param null  $_
     * @param array $args
     *
     * @return Collection<User>
     */
    public function __invoke(null $_, array $args): Collection
    {
        $project = Project::find($args['input']['id']);
        if (!$project instanceof Project) {
            return new Collection();
        }
        $ids = [];
        $project->users->each(function ($user) use (&$ids) {
            $ids[] = $user->id;
        });
        /** @var WorkItem $workItem */
        $project->work_items->each(function ($workItem) use (&$ids) {
            $ids[] = $workItem->owner_id;
        });
        $ids[] = $project->project_manager_id;
        $ids[] = $project->customer_id;
        return User::whereNotIn(User::ID, $ids)->get();
    }
}
