<?php

declare(strict_types=1);

namespace App\GraphQL\Queries\Project\Chart;

use App\Helpers\Chart;
use App\Models\Project;
use Exception;

final readonly class GetHours
{
    /**
     * @param null                                                               $_
     * @param array{input: array{project_id: string,start: string, end: string}} $args
     *
     * @return array{label: array<string>, data: array<float>}
     * @throws Exception
     */
    public function __invoke(null $_, array $args): array
    {
        $data = [
            'labels' => [],
            'data'   => []
        ];
        $project = Project::find($args['input']['project_id']);
        if (!$project instanceof Project) {
            return $data;
        }
        $chart = new Chart($project);
        $chart = $chart->chartHours($args['input']['start'], $args['input']['end']);
        return [
            'labels' => array_keys($chart),
            'data'   => array_values($chart)
        ];
    }
}
