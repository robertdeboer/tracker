<?php

declare(strict_types=1);

namespace Tests\Feature\DB;

use App\Models\Order;
use App\Models\Project;
use App\Models\ProjectUser;
use App\Models\TimeEntry;
use App\Models\User;
use App\Models\WorkItem;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Fixture\DB\ProjectFixture;
use Tests\TestCase;

class ProjectTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Validate inserting data
     *
     * @test
     * @return void
     */
    public function insertPasses(): void
    {
        $project = Project::factory()->create();
        $this->assertModelExists($project);
    }

    /**
     * Validate inserting fails
     *
     * @param string $node
     * @param mixed  $value
     *
     * @test
     * @dataProvider insertFailsDataProvider
     * @return void
     */
    public function insertFails(string $node, mixed $value): void
    {
        $this->expectException(QueryException::class);
        Project::factory()->create([...ProjectFixture::VALID_DATA, $node => $value]);
    }

    /**
     * Data provider for insertFails
     *
     * @return array<string, array<string, mixed>>
     */
    public static function insertFailsDataProvider(): array
    {
        return [
            'No name'                    => [Project::NAME, null],
            'Name too long'              => [Project::NAME, \Faker\Factory::create()->text(300)],
            'No customer id'             => [Project::CUSTOMER_ID, null],
            'Invalid customer id'        => [Project::CUSTOMER_ID, 'dog'],
            'No project manager id'      => [Project::PROJECT_MANAGER_ID, null],
            'Invalid project manager id' => [Project::PROJECT_MANAGER_ID, 'dog']
        ];
    }

    /**
     * Validate the unique name constraint
     *
     * @test
     * @return void
     */
    public function uniqueNamePasses(): void
    {
        $project = Project::factory()->create();
        $this->expectException(QueryException::class);
        Project::factory()->create([Project::NAME => $project->name]);
    }

    /**
     * Validate the customer relation
     *
     * @test
     * @return void
     */
    public function customerRelation(): void
    {
        $user    = User::factory(2)->create();
        $user    = $user->first();
        $project = Project::factory()->create([Project::CUSTOMER_ID => $user->id]);
        $this->assertInstanceOf(User::class, $project->customer);
        $this->assertEquals($user->id, $project->customer->id);
    }

    /**
     * Validate the project manager relation
     *
     * @test
     * @return void
     */
    public function projectManagerRelation(): void
    {
        $user    = User::factory(2)->create();
        $user    = $user->first();
        $project = Project::factory()->create([Project::PROJECT_MANAGER_ID => $user->id]);
        $this->assertInstanceOf(User::class, $project->customer);
        $this->assertEquals($user->id, $project->project_manager->id);
    }

    /**
     * Validate the orders relation
     *
     * @test
     * @return void
     */
    public function ordersRelation(): void
    {
        $project = Project::factory()->create();
        $order   = Order::factory()->create([Order::PROJECT_ID => $project->id]);
        $project->refresh();
        $this->assertInstanceOf(
            Collection::class,
            $project->orders
        );
        $this->assertInstanceOf(
            Order::class,
            $project->orders()->first(),
        );
        $this->assertEquals(
            $order->id,
            $project->orders()->first()->id
        );
    }

    /**
     * Validate the active scope
     *
     * @test
     * @return void
     */
    public function activeScope(): void
    {
        $project = Project::factory()->create([Project::IS_ACTIVE => true]);
        Project::factory()->create([Project::IS_ACTIVE => false]);
        $projects = Project::whereActive(true)->get();
        $this->assertInstanceOf(Collection::class, $projects);
        $this->assertInstanceOf(Project::class, $projects->first());
        $this->assertEquals($project->id, $projects->first()->id);
    }

    /**
     * Validate the customer scope
     *
     * @test
     * @return void
     */
    public function customerScope(): void
    {
        $user    = User::factory()->create();
        $project = Project::factory()->create([Project::CUSTOMER_ID => $user->id]);
        Project::factory()->create();
        $projects = Project::whereCustomer($user->id)->get();
        $this->assertInstanceOf(Collection::class, $projects);
        $this->assertInstanceOf(Project::class, $projects->first());
        $this->assertEquals($project->id, $projects->first()->id);
    }

    /**
     * Validate the project manager scope
     *
     * @test
     * @return void
     */
    public function projectManagerScope(): void
    {
        $user    = User::factory()->create();
        $project = Project::factory()->create([Project::PROJECT_MANAGER_ID => $user->id]);
        Project::factory()->create();
        $projects = Project::whereProjectManager($user->id)->get();
        $this->assertInstanceOf(Collection::class, $projects);
        $this->assertInstanceOf(Project::class, $projects->first());
        $this->assertEquals($project->id, $projects->first()->id);
    }

    /**
     * Validate the work items relation
     *
     * @test
     * @return void
     */
    public function workItemsRelation(): void
    {
        WorkItem::factory()->create();
        $project  = Project::factory()->create();
        $workItem = WorkItem::factory()->create([WorkItem::PROJECT_ID => $project->id]);
        $this->assertInstanceOf(
            Collection::class,
            $project->work_items
        );
        $this->assertCount(
            1,
            $project->work_items
        );
        $this->assertInstanceOf(
            WorkItem::class,
            $project->work_items()->first()
        );
        $this->assertEquals(
            $workItem->id,
            $project->work_items()->first()->id
        );
    }

    /**
     * Validate the users relation
     *
     * @test
     * @return void
     */
    public function usersRelation(): void
    {
        $user    = User::factory()->create();
        $project = Project::factory()->create();
        ProjectUser::factory()->create(
            [
                ProjectUser::USER_ID    => $user->id,
                ProjectUser::PROJECT_ID => $project->id
            ]
        );
        $project->load('users');
        $this->assertInstanceOf(
            Collection::class,
            $project->users
        );
        $this->assertCount(
            1,
            $project->users
        );
        $this->assertInstanceOf(
            User::class,
            $project->users()->first()
        );
        $this->assertEquals(
            $user->id,
            $project->users()->first()->id
        );
    }

    /**
     * Validate the time entries relation
     *
     * @test
     * @return void
     */
    public function timeEntriesRelation(): void
    {
        $project   = Project::factory()->create();
        $workItem  = WorkItem::factory()->create([WorkItem::PROJECT_ID => $project->id]);
        $timeEntry = TimeEntry::factory()->create([TimeEntry::WORK_ITEM_ID => $workItem->id]);
        TimeEntry::factory()->create();
        $this->assertInstanceOf(
            Collection::class,
            $project->time_entries
        );
        $this->assertCount(
            1,
            $project->time_entries
        );
        $this->assertInstanceOf(
            TimeEntry::class,
            $project->time_entries()->first()
        );
        $this->assertEquals(
            $timeEntry->id,
            $project->time_entries()->first()->id
        );
    }
}
