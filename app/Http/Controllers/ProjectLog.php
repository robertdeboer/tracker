<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Exceptions\DataException;
use App\Models\Project;
use App\Models\WorkItem;

class ProjectLog extends Controller
{
    public function get(string $id): string
    {
        $project = Project::find($id);
        if (!$project instanceof Project) {
            throw new DataException('This project does not exists');
        }
        $csv = '"Work Item","Title","IPS No","Start Date","End Date","Closed","Owner","Total Hours"';
        $csv .= "\n";
        /** @var WorkItem $workItem */
        foreach($project->work_items as $workItem) {
            $workItem->loadSum('time_entries as hours', 'hours');
            $csv .= '"'. $workItem->id .'",';
            $csv .= '"'. $workItem->name .'",';
            $csv .= '"'. (empty($workItem->ticket_data) ? '' : $workItem->ticket_data['id']) .'",';
            $csv .= '"'. $workItem->start_date->format('Y-m-d') .'",';
            if(empty($workItem->end_date)) {
                $csv .= '"",';
            } else {
                $csv .= '"'. $workItem->end_date->format('Y-m-d') .'",';
            }
            $csv .= '"'. (empty($workItem->end_date) ? 'No' : 'Yes') .'",';
            $csv .= '"'. $workItem->owner->first_name . ' ' . $workItem->owner->last_name .'",';
            $csv .= '"'. (is_null($workItem->hours) ? 0 : $workItem->hours) .'"';
            $csv .= "\n";
        }
        return $csv;
    }
}
