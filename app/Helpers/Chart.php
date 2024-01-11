<?php

declare(strict_types=1);

namespace App\Helpers;

use App\Models\Project;
use App\Models\TimeEntry;
use Carbon\Carbon;

class Chart
{
    public const DAYS   = 'days';
    public const DAYS_FORMAT = 'm/d';
    public const WEEKS  = 'weeks';
    public const WEEKS_FORMAT  = 'W';
    public const MONTHS = 'months';
    public const MONTHS_FORMAT = 'Y/m';

    public function __construct(
        protected Project $project
    ) {
    }

    /**
     * Chart the hours for a project based on the time range
     * This returns an array keyed with the group names and the value of the group
     * Example: ['WW 21' => 8, 'WW 22' => 36]
     *
     * @param Carbon $start
     * @param Carbon $end
     *
     * @return array{string, float}
     */
    public function chartHours(Carbon $start, Carbon $end): array
    {
        // Step 1 - grab our time entries
        $entries = $this->project->time_entries()
                                 ->whereBetween(TimeEntry::DATE, [$start, $end])
                                 ->orderBy(TimeEntry::DATE)
                                 ->get();
        // Step 2 - determine how the hours are going to be grouped based on the size of the date range
        // This is determine by the number of days in the range and constant of 13 columns
        $diff   = (int)$start->diff($end)->format('%a');
        $groups = [];
        if ($diff <= 13) {
            // 1 - 13 days
            $format = self::DAYS;
            for ($i = 1; $i <= 13; $i++) {
                $groups[$end->format(self::DAYS_FORMAT)] = 0;
                $end->subDay();
                if($end < $start) {
                    break;
                }
            }
        } elseif ($diff <= 91) {
            // 1 - 13 weeks
            $format = self::WEEKS;
            for ($i = 1; $i <= 13; $i++) {
                $groups['WW ' . $end->format(self::WEEKS_FORMAT)] = 0;
                $end->subWeek();
                if($end < $start) {
                    break;
                }
            }
        } else {
            // 1 - 12 months
            $format = self::MONTHS;
            for ($i = 1; $i <= 13; $i++) {
                $groups[$end->format(self::MONTHS_FORMAT)] = 0;
                $end->subMonth();
                if($end < $start) {
                    break;
                }
            }
        }
        $groups = array_reverse($groups);
        // Step 3 - Add each time entry to the appropriate bucket
        /** @var TimeEntry $entry */
        foreach ($entries as $entry) {
            switch ($format) {
                case self::DAYS:
                    $groups[$entry->date->format(self::DAYS_FORMAT)] += $entry->hours;
                    break;
                case self::WEEKS:
                    $groups['WW ' . $entry->date->format(self::WEEKS_FORMAT)] += $entry->hours;
                    break;
                case self::MONTHS:
                    $groups[$entry->date->format(self::MONTHS_FORMAT)] += $entry->hours;
                    break;
                default:
                    // do nothing
                    break;
            }
        }
        return $groups;
    }
}
