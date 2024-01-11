<?php

declare(strict_types=1);

namespace App\GraphQL\Queries;

use App\Models\Project;
use App\Models\TimeEntry;
use Nuwave\Lighthouse\Execution\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

final readonly class ProjectHoursUsed
{
    /**
     * Return a value for the field.
     *
     * @param Project        $project
     * @param array{}        $args        The field arguments passed by the client.
     * @param GraphQLContext $context     Shared between all fields.
     * @param ResolveInfo    $resolveInfo Metadata for advanced query resolution.
     *
     * @return mixed The result of resolving the field, matching what was promised in the schema
     */
    public function __invoke(Project $project, array $args, GraphQLContext $context, ResolveInfo $resolveInfo): float
    {
        return $project->time_entries()->sum(TimeEntry::HOURS) ?? 0.00;
    }
}
