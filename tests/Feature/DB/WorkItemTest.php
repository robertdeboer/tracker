<?php

declare(strict_types=1);

namespace Tests\Feature\DB;

use App\Models\Project;
use App\Models\TimeEntry;
use App\Models\User;
use App\Models\WorkItem;
use App\Models\WorkItemUser;
use Carbon\Exceptions\InvalidFormatException;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WorkItemTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Validate modifying data
     *
     * @test
     * @return void
     */
    public function insertPasses(): void
    {
        $workItem = WorkItem::factory()->create();
        $this->assertModelExists($workItem);
    }

    /**
     * Validate modifying invalid data fails
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
        WorkItem::factory()->create([$node => $value]);
    }

    /**
     * Validate the work item unique project name constraint
     *
     * @test
     * @return void
     */
    public function uniqueNameConstraint(): void
    {
        $workItem = WorkItem::factory()->create();
        $this->expectException(QueryException::class);
        WorkItem::factory()->create(
            [
                WorkItem::PROJECT_ID => $workItem->project_id,
                WorkItem::NAME       => $workItem->name
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
            'No project id'           => [WorkItem::PROJECT_ID, null],
            'Invalid project id'      => [WorkItem::PROJECT_ID, 'dog'],
            'Non-existent project id' => [WorkItem::PROJECT_ID, 1000],
            'No owner id'             => [WorkItem::OWNER_ID, null],
            'Invalid owner id'        => [WorkItem::OWNER_ID, 'dog'],
            'Non-existent owner id'   => [WorkItem::OWNER_ID, 1000],
            'No name'                 => [WorkItem::NAME, null]
        ];
    }

    /**
     * Validate date are checked for correctness
     *
     * @test
     * @return void
     */
    public function invalidDateFails(): void
    {
        $this->expectException(InvalidFormatException::class);
        WorkItem::factory()->create([WorkItem::START_DATE => 'dog']);
        $this->expectException(InvalidFormatException::class);
        WorkItem::factory()->create([WorkItem::END_DATE => 'dog']);
    }

    /**
     * Validate deleting a work item
     *
     * @test
     * @return void
     */
    public function deletePasses(): void
    {
        $workItem = WorkItem::factory()->create();
        $workItem->delete();
        $this->assertModelMissing($workItem);
    }

    /**
     * Validate the scope WhereOpen
     *
     * @test
     * @return void
     */
    public function scopeWhereOpen(): void
    {
        $workItem = WorkItem::factory()->create([WorkItem::IS_OPEN => 1]);
        WorkItem::factory()->create([WorkItem::IS_OPEN => 0]);
        $workItems = WorkItem::whereOpen(true)->get();
        $this->assertInstanceOf(
            Collection::class,
            $workItems
        );
        $this->assertCount(
            1,
            $workItems
        );
        $this->assertInstanceOf(
            WorkItem::class,
            $workItems->first()
        );
        $this->assertEquals(
            $workItem->id,
            $workItems->first()->id
        );
    }

    /**
     * Validate the project relation
     *
     * @test
     * @return void
     */
    public function projectRelation(): void
    {
        $project  = Project::factory()->create();
        $workItem = WorkItem::factory()->create([WorkItem::PROJECT_ID => $project->id]);
        $this->assertInstanceOf(
            Project::class,
            $workItem->project
        );
        $this->assertEquals(
            $project->id,
            $workItem->project->id
        );
    }

    /**
     * Validate deleting a project with a work item fails
     *
     * @test
     * @return void
     */
    public function deleteOnProjectRelationFails(): void
    {
        $project = Project::factory()->create();
        WorkItem::factory()->create([WorkItem::PROJECT_ID => $project->id]);
        $this->expectException(QueryException::class);
        $project->delete();
    }

    /**
     * Validate the scope WhereProject
     *
     * @test
     * @return void
     */
    public function scopeWhereProject(): void
    {
        $project  = Project::factory()->create();
        $workItem = WorkItem::factory()->create([WorkItem::PROJECT_ID => $project->id]);
        WorkItem::factory()->create();
        $this->assertInstanceOf(
            Collection::class,
            WorkItem::whereProject($project->id)->get()
        );
        $this->assertCount(
            1,
            WorkItem::whereProject($project->id)->get()
        );
        $this->assertInstanceOf(
            WorkItem::class,
            WorkItem::whereProject($project->id)->get()->first()
        );
        $this->assertEquals(
            $workItem->id,
            WorkItem::whereProject($project->id)->get()->first()->id
        );
    }

    /**
     * Validate the owner relation
     *
     * @test
     * @return void
     */
    public function ownerRelation(): void
    {
        $user     = User::factory()->create();
        $workItem = WorkItem::factory()->create([WorkItem::OWNER_ID => $user->id]);
        $this->assertInstanceOf(
            User::class,
            $workItem->owner
        );
        $this->assertEquals(
            $user->id,
            $workItem->owner->id
        );
    }

    /**
     * Validate delete a user who is the owner of a work item fails
     *
     * @test
     * @return void
     */
    public function deleteOnOwnerRelationFails(): void
    {
        $user     = User::factory()->create();
        $workItem = WorkItem::factory()->create([WorkItem::OWNER_ID => $user->id]);
        $this->expectException(QueryException::class);
        $user->delete();
    }

    /**
     * Validate the scope WhereOwner
     *
     * @test
     * @return void
     */
    public function scopeWhereOwner(): void
    {
        $user     = User::factory()->create();
        $workItem = WorkItem::factory()->create([WorkItem::OWNER_ID => $user->id]);
        WorkItem::factory()->create();
        $this->assertInstanceOf(
            Collection::class,
            WorkItem::whereOwner($user->id)->get()
        );
        $this->assertCount(
            1,
            WorkItem::whereOwner($user->id)->get()
        );
        $this->assertInstanceOf(
            WorkItem::class,
            WorkItem::whereOwner($user->id)->get()->first()
        );
        $this->assertEquals(
            $workItem->id,
            WorkItem::whereOwner($user->id)->get()->first()->id
        );
    }

    /**
     * Validate the work item user relation
     *
     * @test
     * @return void
     */
    public function usersRelation(): void
    {
        $userId     = User::factory()->create()->id;
        $workItemId = WorkItem::factory()->create()->id;
        WorkItemUser::factory()->create(
            [
                WorkItemUser::USER_ID      => $userId,
                WorkItemUser::WORK_ITEM_ID => $workItemId
            ]
        );
        $workItem = WorkItem::find($workItemId)->load('users');
        $this->assertInstanceOf(
            Collection::class,
            $workItem->users
        );
        $this->assertCount(
            1,
            $workItem->users
        );
        $this->assertInstanceOf(
            User::class,
            $workItem->users()->first()
        );
        $this->assertEquals(
            $userId,
            $workItem->users()->first()->id
        );
    }

    /**
     * Validate delete a user assigned to a work item fails
     *
     * @test
     * @return void
     */
    public function deleteOnUserRelationFails(): void
    {
        $user     = User::factory()->create();
        $workItem = WorkItem::factory()->create();
        WorkItemUser::factory()->create(
            [
                WorkItemUser::USER_ID      => $user->id,
                WorkItemUser::WORK_ITEM_ID => $workItem->id
            ]
        );
        $this->expectException(QueryException::class);
        $user->delete();
    }

    /**
     * Validate the time entry relation
     *
     * @test
     * @return void
     */
    public function timeEntryRelations(): void
    {
        $workItem  = WorkItem::factory()->create();
        $timeEntry = TimeEntry::factory()->create([TimeEntry::WORK_ITEM_ID => $workItem->id]);
        TimeEntry::factory()->create();
        $this->assertInstanceOf(
            Collection::class,
            $workItem->time_entries
        );
        $this->assertCount(
            1,
            $workItem->time_entries
        );
        $this->assertInstanceOf(
            TimeEntry::class,
            $workItem->time_entries()->first()
        );
        $this->assertEquals(
            $timeEntry->id,
            $workItem->time_entries()->first()->id
        );
    }

    /**
     * Validate deleting a work item with a time entry fails
     *
     * @test
     * @return void
     */
    public function deleteOnTimeEntryRelationFails(): void
    {
        $workItem = WorkItem::factory()
                            ->create();
        TimeEntry::factory()
                 ->create([TimeEntry::WORK_ITEM_ID => $workItem->id]);
        $this->expectException(QueryException::class);
        $workItem->delete();
    }
}
