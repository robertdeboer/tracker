<?php

namespace Tests\Fixture\Helpers;

use App\Models\Project;
use App\Models\TimeEntry;
use App\Models\WorkItem;
use Carbon\Carbon;

class ChartFixture
{
    public function __construct(
        protected int $workItemCount,
        protected int $timeEntryCount
    ) {
        //
    }

    public function getMonthProject(): Project
    {
        $startDate         = Carbon::now();
        $project           = Project::factory()->create(
            [
                Project::IS_ACTIVE => true
            ]
        );
        $workItemStartDate = $startDate;
        for ($i = 1; $i <= $this->workItemCount; $i++) {
            $workItem = WorkItem::factory()
                                ->create(
                                    [
                                        WorkItem::PROJECT_ID => $project->id,
                                        WorkItem::START_DATE => $workItemStartDate
                                    ]
                                );
            TimeEntry::factory()->create(
                [
                    TimeEntry::WORK_ITEM_ID => $workItem->id,
                    TimeEntry::DATE         => $workItemStartDate,
                    TimeEntry::HOURS        => 8
                ]
            );
            $workItemStartDate->subMonth();
        }
        return $project;
    }

    public function getWeekProject(): Project
    {
        $startDate         = Carbon::now();
        $project           = Project::factory()->create(
            [
                Project::IS_ACTIVE => true
            ]
        );
        $workItemStartDate = $startDate;
        for ($i = 1; $i <= $this->workItemCount; $i++) {
            $workItem = WorkItem::factory()
                                ->create(
                                    [
                                        WorkItem::PROJECT_ID => $project->id,
                                        WorkItem::START_DATE => $workItemStartDate
                                    ]
                                );
            TimeEntry::factory()->create(
                [
                    TimeEntry::WORK_ITEM_ID => $workItem->id,
                    TimeEntry::DATE         => $workItemStartDate,
                    TimeEntry::HOURS        => 8
                ]
            );
            $workItemStartDate->subWeek();
        }
        return $project;
    }

    public function getDayProject(): Project
    {
        $startDate         = Carbon::now();
        $project           = Project::factory()->create(
            [
                Project::IS_ACTIVE => true
            ]
        );
        $workItemStartDate = $startDate;
        for ($i = 1; $i <= $this->workItemCount; $i++) {
            $workItem = WorkItem::factory()
                                ->create(
                                    [
                                        WorkItem::PROJECT_ID => $project->id,
                                        WorkItem::START_DATE => $workItemStartDate
                                    ]
                                );
            TimeEntry::factory()->create(
                [
                    TimeEntry::WORK_ITEM_ID => $workItem->id,
                    TimeEntry::DATE         => $workItemStartDate,
                    TimeEntry::HOURS        => 8
                ]
            );
            $workItemStartDate->subDay();
        }
        return $project;
    }
}
