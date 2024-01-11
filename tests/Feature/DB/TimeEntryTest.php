<?php

declare(strict_types=1);

namespace Tests\Feature\DB;

use App\Models\Project;
use App\Models\TimeEntry;
use App\Models\User;
use App\Models\WorkItem;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TimeEntryTest extends TestCase
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
        $timeEntry = TimeEntry::factory()->create();
        $this->assertModelExists($timeEntry);
    }

    /**
     * Validate modifying data fails
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
        TimeEntry::factory()->create([$node => $value]);
    }

    /**
     * Data provider for insertFails
     *
     * @return array<string, array<string, mixed>>
     */
    public static function insertFailsDataProvider(): array
    {
        return [
            'No work item id'        => [TimeEntry::WORK_ITEM_ID, null],
            'Invalid work item id'   => [TimeEntry::WORK_ITEM_ID, 'dog'],
            'Non-existent work item' => [TimeEntry::WORK_ITEM_ID, 1000],
            'No author id'           => [TimeEntry::AUTHOR_ID, null],
            'Invalid author id'      => [TimeEntry::AUTHOR_ID, 'dog'],
            'Non-existent author'    => [TimeEntry::AUTHOR_ID, 1000],
            'No hours'               => [TimeEntry::HOURS, null],
            'Invalid hours'          => [TimeEntry::AUTHOR_ID, 'dog'],
        ];
    }

    /**
     * Validate deleting a time entry
     *
     * @test
     * @return void
     */
    public function deletePasses(): void
    {
        $timeEntry = TimeEntry::factory()->create();
        $timeEntry->delete();
        $this->assertModelMissing($timeEntry);
    }

    /**
     * Validate the project relation
     *
     * @test
     * @return void
     */
    public function projectRelation(): void
    {
        $project   = Project::factory()->create();
        $workItem  = WorkItem::factory()->create([WorkItem::PROJECT_ID => $project->id]);
        $timeEntry = TimeEntry::factory()->create([TimeEntry::WORK_ITEM_ID => $workItem->id]);
        $this->assertInstanceOf(
            Project::class,
            $timeEntry->project
        );
        $this->assertEquals(
            $project->id,
            $timeEntry->project->id
        );
    }

    /**
     * Validate the work item relation
     *
     * @test
     * @return void
     */
    public function workItemRelation(): void
    {
        $workItem  = WorkItem::factory()->create();
        $timeEntry = TimeEntry::factory()->create([TimeEntry::WORK_ITEM_ID => $workItem->id]);
        $this->assertInstanceOf(
            WorkItem::class,
            $timeEntry->work_item
        );
        $this->assertEquals(
            $workItem->id,
            $timeEntry->work_item->id
        );
    }

    /**
     * Validate the whereWorkItem scope
     *
     * @test
     * @return void
     */
    public function scopeWhereWorkItem(): void
    {
        TimeEntry::factory()->create();
        $workItem  = WorkItem::factory()->create();
        $timeEntry = TimeEntry::factory()->create([TimeEntry::WORK_ITEM_ID => $workItem->id]);
        $entries   = TimeEntry::whereWorkItem($workItem->id)->get();
        $this->assertInstanceOf(
            Collection::class,
            $entries
        );
        $this->assertCount(
            1,
            $entries
        );
        $this->assertInstanceOf(
            TimeEntry::class,
            $entries->first()
        );
        $this->assertEquals(
            $timeEntry->id,
            $entries->first()->id
        );
    }

    /**
     * Validate the author<User> relation
     *
     * @test
     * @return void
     */
    public function authorRelation(): void
    {
        $user      = User::factory()->create();
        $timeEntry = TimeEntry::factory()->create([TimeEntry::AUTHOR_ID => $user->id]);
        $this->assertInstanceOf(
            User::class,
            $timeEntry->author
        );
        $this->assertEquals(
            $user->id,
            $timeEntry->author->id
        );
    }

    /**
     * Validate the whereAuthor scope
     *
     * @test
     * @return void
     */
    public function scopeWhereAuthor(): void
    {
        TimeEntry::factory()->create();
        $author  = User::factory()->create();
        $timeEntry = TimeEntry::factory()->create([TimeEntry::AUTHOR_ID => $author->id]);
        $entries   = TimeEntry::whereAuthor($author->id)->get();
        $this->assertInstanceOf(
            Collection::class,
            $entries
        );
        $this->assertCount(
            1,
            $entries
        );
        $this->assertInstanceOf(
            TimeEntry::class,
            $entries->first()
        );
        $this->assertEquals(
            $timeEntry->id,
            $entries->first()->id
        );
    }
}
