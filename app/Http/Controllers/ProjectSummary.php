<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Helpers\Chart;
use App\Http\Requests\EmailProjectSummaryRequest;
use App\Models\Project;
use Carbon\Carbon;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use InvalidArgumentException;

class ProjectSummary extends Controller
{
    /**
     * @param string $id
     * @param string $start
     * @param string $end
     *
     * @return Application|Factory|View|\Illuminate\Foundation\Application
     */
    public function get(
        string $id,
        string $start,
        string $end
    ): Application|Factory|View|\Illuminate\Foundation\Application {
        $project = Project::find($id);
        if (!$project instanceof Project) {
            throw new InvalidArgumentException('This project does not exists');
        }
        return view(
            'ProjectSummary',
            $this->generate(
                $project,
                new Carbon($start),
                new Carbon($end)
            )
        );
    }

    /**
     * Email the project summary
     *
     * @param EmailProjectSummaryRequest $request
     *
     * @return void
     */
    public function send(EmailProjectSummaryRequest $request)
    {
        Log::debug('email', $request->validated());
        Log::debug('start', [new Carbon($request->validated('start'))]);
        Log::debug('end', [new Carbon($request->validated('end'))]);
        $project = Project::find($request->validated('id'));
        if (!$project instanceof Project) {
            throw new InvalidArgumentException('This project does not exists');
        }
        $summary = $this->generate(
            $project,
            new Carbon($request->validated('start')),
            new Carbon($request->validated('end'))
        );
        Log::debug('summary', $summary);
        Mail::to($project->customer)
            ->send(new \App\Mail\ProjectSummary($summary));
    }

    /**
     * Generate the project summary
     *
     * @param Project $project
     * @param Carbon  $startDate
     * @param Carbon  $endDate
     *
     * @return array
     */
    public function generate(Project $project, Carbon $startDate, Carbon $endDate)
    {
        $chart = new Chart($project);
        $chart = $chart->chartHours($startDate, $endDate);
        $project->load('work_items');
        $rebated = 0;
        $total   = 0;
        foreach ($project->work_items as $workItem) {
            foreach ($workItem->time_entries as $timeEntry) {
                if ($timeEntry->hours < 0) {
                    $rebated += abs($timeEntry->hours);
                } else {
                    $total += $timeEntry->hours;
                }
            }
        }

        return [
            'project' => $project,
            'rebated' => $rebated,
            'total'   => $total,
            'chart'   => [
                'labels' => array_keys($chart),
                'data'   => $chart
            ]
        ];
    }
}
