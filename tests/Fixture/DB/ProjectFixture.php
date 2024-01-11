<?php

declare(strict_types=1);

namespace Tests\Fixture\DB;

use App\Models\Project;

class ProjectFixture
{
    public const VALID_DATA = [
        Project::NAME               => 'My Test Project',
        Project::IS_ACTIVE          => true,
        Project::CUSTOMER_ID        => 1,
        Project::PROJECT_MANAGER_ID => 1,
        Project::DESCRIPTION        => 'This is a project description.'
    ];
}
