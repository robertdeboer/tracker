<?php

declare(strict_types=1);

namespace Feature\Helpers;

use App\Helpers\Chart;
use App\Models\Project;
use Carbon\Carbon;
use Tests\Fixture\Helpers\ChartFixture;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ChartTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Validate the chart interval is set for months
     * and the label is correct
     *
     * @test
     * @return void
     */
    public function monthInterval(): void
    {
        $project = Project::factory()->create();
        $chart   = new Chart($project);
        $end     = Carbon::now();
        $start   = Carbon::now()->subDays(100);
        $result  = $chart->chartHours($start, $end);
        foreach ($result as $label => $value) {
            $this->assertEquals(0, $value);
            $this->assertMatchesRegularExpression('/^\d{4}\/\d{2}$/i', $label);
        }
    }

    /**
     * Validate the chart interval is set for weeks
     * and the label is correct
     *
     * @test
     * @return void
     */
    public function weekInterval(): void
    {
        $project = Project::factory()->create();
        $chart   = new Chart($project);
        $end     = Carbon::now();
        $start   = Carbon::now()->subDays(91);
        $result  = $chart->chartHours($start, $end);
        foreach ($result as $label => $value) {
            $this->assertEquals(0, $value);
            $this->assertMatchesRegularExpression('/^WW \d{2}$/i', $label);
        }
    }

    /**
     * Validate the chart interval is set for days
     * and the label is correct
     *
     * @test
     * @return void
     */
    public function dayInterval(): void
    {
        $project = Project::factory()->create();
        $chart   = new Chart($project);
        $end     = Carbon::now();
        $start   = Carbon::now()->subDays(8);
        $result  = $chart->chartHours($start, $end);
        foreach ($result as $label => $value) {
            $this->assertEquals(0, $value);
            $this->assertMatchesRegularExpression('/^\d{2}\/\d{2}$/i', $label);
        }
    }

    /**
     * Validate the charted data is correct when by month
     *
     * @test
     * @return void
     */
    public function monthChart(): void
    {
        $fixture = new ChartFixture(4, 1);
        $project = $fixture->getMonthProject();
        $chart   = new Chart($project);
        $end     = Carbon::now();
        $start   = Carbon::now()->subDays(100);
        $result  = $chart->chartHours($start, $end);
        foreach ($result as $value) {
            $this->assertEquals(8, $value);
        }
    }

    /**
     * Validate the charted data is correct when by week
     *
     * @test
     * @return void
     */
    public function weekChart(): void
    {
        $fixture = new ChartFixture(4, 1);
        $project = $fixture->getWeekProject();
        $chart   = new Chart($project);
        $end     = Carbon::now();
        $start   = Carbon::now()->subDays(28);
        $result  = $chart->chartHours($start, $end);
        foreach ($result as $value) {
            $this->assertEquals(8, $value);
        }
    }

    /**
     * Validate the charted data is correct when by days
     *
     * @test
     * @return void
     */
    public function dayChart(): void
    {
        $fixture = new ChartFixture(4, 1);
        $project = $fixture->getDayProject();
        $chart   = new Chart($project);
        $end     = Carbon::now();
        $start   = Carbon::now()->subDays(4);
        $result  = $chart->chartHours($start, $end);
        foreach ($result as $value) {
            $this->assertEquals(8, $value);
        }
    }
}
