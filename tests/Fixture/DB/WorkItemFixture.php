<?php

declare(strict_types=1);

namespace Tests\Fixture\DB;

use App\Models\WorkItem;

class WorkItemFixture
{
    public const VALID_DATA = [
        WorkItem::PROJECT_ID  => 1,
        WorkItem::OWNER_ID    => 1,
        WorkItem::NAME        => 'My First Work Item',
        WorkItem::IS_OPEN     => true,
        WorkItem::START_DATE  => '2023-09-01 12:00:00',
        WorkItem::END_DATE    => null,
        WorkItem::TICKET_DATA => []
    ];
}
