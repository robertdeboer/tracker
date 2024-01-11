<?php

declare(strict_types=1);

namespace App\GraphQL\Queries;

use App\Helpers\Projects;
use App\Models\Order;
use App\Models\Project;
use App\Models\TimeEntry;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Nuwave\Lighthouse\Execution\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

final readonly class Dashboard
{
    /**
     * Return a value for the field.
     *
     * @param null           $root        Always null, since this field has no parent.
     * @param array{}        $args        The field arguments passed by the client.
     * @param GraphQLContext $context     Shared between all fields.
     * @param ResolveInfo    $resolveInfo Metadata for advanced query resolution.
     *
     * @return Collection A collection of projects
     */
    public function __invoke(
        null           $root,
        array          $args,
        GraphQLContext $context,
        ResolveInfo    $resolveInfo
    ): Collection {
        $inActive  = $args['in_active'] ?? false;
        $withHours = $args['with_hours'] ?? false;
        /** @var User $user */
        $user     = $context->user();
        $projects = new Projects();
        $projects = $projects->getProjects($user, $inActive);
        /** @var Project $project */
        if (!$withHours) {
            return $projects;
        }
        foreach ($projects as $project) {
            $ordered                = $project->orders()->sum(Order::HOURS);
            $project->hours_ordered = $ordered ?? 0;
            $used                   = $project->time_entries()->sum(TimeEntry::HOURS);
            $project->hours_used    = $used ?? 0;
        }
        return $projects;
    }
}
