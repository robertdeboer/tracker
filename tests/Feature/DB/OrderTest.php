<?php

declare(strict_types=1);

namespace Tests\Feature\DB;

use App\Models\Order;
use App\Models\Project;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Fixture\DB\OrderFixture;
use Tests\TestCase;

class OrderTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Validate modifying data passes
     *
     * @test
     * @return void
     */
    public function insertPasses(): void
    {
        $order = Order::factory()->create();
        $this->assertModelExists($order);
    }

    /**
     * Validate inserting data fails
     *
     * @test
     * @dataProvider insertFailsDataProvider
     *
     * @param string $node
     * @param mixed  $value
     *
     * @return void
     */
    public function insertFails(string $node, mixed $value): void
    {
        $this->expectException(QueryException::class);
        Order::factory()->create(
            [
                ...OrderFixture::VALID_DATA,
                $node => $value
            ]
        );
    }

    /**
     * Data provider for insertFails
     *
     * @return array<string, array<string, mixed>>
     */
    public static function insertFailsDataProvider(): array
    {
        return [
            'No email'             => [Order::EMAIL, null],
            'Invalid hours'        => [Order::HOURS, 'dog'],
            'Invalid project id'   => [Order::PROJECT_ID, 'dog'],
            'Non existent project' => [Order::PROJECT_ID, 1000]
        ];
    }

    /**
     * Validate removing an un-assigned order passes
     *
     * @test
     * @return void
     */
    public function remove(): void
    {
        $order = Order::factory()->create();
        $order->delete();
        $this->assertModelMissing($order);
    }

    /**
     * Validate the project relation
     *
     * @test
     * @return void
     */
    public function projectRelation(): void
    {
        $project = Project::factory()->create();
        $order   = Order::factory()->create([Order::PROJECT_ID => $project->id]);
        $this->assertInstanceOf(
            Project::class,
            $order->project
        );
        $this->assertEquals(
            $project->id,
            $order->project->id
        );
    }

    /**
     * Validate the project scope
     *
     * @test
     * @return void
     */
    public function scopeWhereProject(): void
    {
        $projects = Project::factory(2)->create();
        Order::factory()->create(
            [
                Order::PROJECT_ID => $projects->first()->id
            ]
        );
        $orders = Order::whereProject($projects->first()->id)->get();
        $this->assertInstanceOf(
            Collection::class,
            $orders
        );
        $this->assertCount(
            1,
            $orders
        );
        $this->assertInstanceOf(
            Order::class,
            $orders->first()
        );
        $this->assertEquals(
            $projects->first()->id,
            $orders->first()->project_id
        );
    }
}
