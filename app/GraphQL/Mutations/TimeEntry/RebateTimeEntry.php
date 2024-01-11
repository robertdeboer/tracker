<?php

declare(strict_types=1);

namespace App\GraphQL\Mutations\TimeEntry;

use App\Models\TimeEntry;
use GraphQL\Server\RequestError;

final readonly class RebateTimeEntry
{
    /**
     * @param null              $_
     * @param array{id: number} $args
     *
     * @return TimeEntry
     * @throws RequestError
     */
    public function __invoke(null $_, array $args): TimeEntry
    {
        $timeEntry = TimeEntry::find($args['id']);
        if (!$timeEntry instanceof TimeEntry) {
            throw new RequestError('This time entry does not exists');
        }
        $rebate = new TimeEntry(
            [
                TimeEntry::WORK_ITEM_ID => $timeEntry->work_item_id,
                TimeEntry::AUTHOR_ID    => auth()->user()->id,
                TimeEntry::HOURS        => $timeEntry->hours * -1,
                TimeEntry::NOTE         => "Refund of entry {$timeEntry->id}"
            ]
        );
        $rebate->save();
        return $rebate;
    }
}
