<?php

declare(strict_types=1);

namespace Tests\Feature\DB;

use App\Models\Permissions;
use App\Models\Project;
use App\Models\ProjectUser;
use App\Models\Roles;
use App\Models\User;
use App\Models\WorkItem;
use App\Models\WorkItemUser;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Artisan;
use Tests\Fixture\DB\UserFixture;
use Tests\TestCase;

class UserTest extends TestCase
{
    use \Tests\Traits\Permissions;
    use RefreshDatabase;
    use WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        $this->buildPermissions();
    }

    /**
     * Validate inserting data
     * @test
     * @return void
     */
    public function insertPasses(): void
    {
        $user = User::factory()->create();
        $this->assertModelExists($user);
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
        $user = new User([...UserFixture::VALID_DATA, $node => $value]);
        $this->expectException(QueryException::class);
        $user->save();
    }

    /**
     * Data provider for insertFails test
     *
     * @return array<string, mixed>
     */
    public static function insertFailsDataProvider(): array
    {
        return [
            'No first name' => [User::FIRST_NAME, null],
            'No last name'  => [User::LAST_NAME, null],
            'No email'      => [User::EMAIL, null],
            'No password'   => [User::PASSWORD, null]
        ];
    }

    /**
     * Validate the unique name constraint
     * @test
     * @return void
     */
    public function uniqueNamePasses(): void
    {
        $user = User::factory()->create();
        $this->expectException(QueryException::class);
        User::factory()->create(
            [
                User::FIRST_NAME => $user->first_name,
                User::LAST_NAME  => $user->last_name
            ]
        );
    }

    /**
     * Validate the unique email constraint
     * @test
     * @return void
     */
    public function uniqueEmailPasses(): void
    {
        $user = User::factory()->create();
        $this->expectException(QueryException::class);
        User::factory()->create(
            [
                User::EMAIL => $user->email
            ]
        );
    }

    /**
     * Validate deleting a user
     *
     * @test
     * @return void
     */
    public function deletePasses(): void
    {
        $user = User::factory()->create();
        $user->delete();
        $this->assertModelMissing($user);
    }

    /**
     * Validate the customer projects relation
     *
     * @test
     * @return void
     */
    public function customerProjectsRelation(): void
    {
        $user    = User::factory()->create();
        $project = Project::factory()->create([Project::CUSTOMER_ID => $user->id]);
        Project::factory()->create();
        $project->refresh();
        $this->assertInstanceOf(Collection::class, $user->customer_projects);
        $this->assertInstanceOf(Project::class, $user->customer_projects()->first());
        $this->assertEquals($project->id, $user->customer_projects()->first()->id);
    }

    /**
     * Validate that deleting a user set as a project's customer fails
     *
     * @test
     * @return void
     */
    public function deleteCustomerRelationFails(): void
    {
        $user = User::factory()->create();
        Project::factory()->create([Project::CUSTOMER_ID => $user->id]);
        $this->expectException(QueryException::class);
        $user->delete();
    }

    /**
     * Validate the project manager projects relation
     *
     * @test
     * @return void
     */
    public function projectManagerProjectsRelation(): void
    {
        $user    = User::factory()->create();
        $project = Project::factory()->create([Project::PROJECT_MANAGER_ID => $user->id]);
        Project::factory()->create();
        $project->refresh();
        $this->assertInstanceOf(
            Collection::class,
            $user->project_manager_projects
        );
        $this->assertInstanceOf(Project::class, $user->project_manager_projects()->first());
        $this->assertEquals($project->id, $user->project_manager_projects()->first()->id);
    }

    /**
     * Validate that deleting a user set as a project's project manager fails
     *
     * @test
     * @return void
     */
    public function deleteProjectManagerRelationFails(): void
    {
        $user = User::factory()->create();
        Project::factory()->create([Project::PROJECT_MANAGER_ID => $user->id]);
        $this->expectException(QueryException::class);
        $user->delete();
    }

    /**
     * Validate the projects relation
     *
     * @test
     * @return void
     */
    public function projectsRelation(): void
    {
        $user    = User::factory()->create();
        $project = Project::factory()->create();
        ProjectUser::factory()->create(
            [
                ProjectUser::USER_ID    => $user->id,
                ProjectUser::PROJECT_ID => $project->id
            ]
        );
        $user->refresh();
        $this->assertInstanceOf(
            Collection::class,
            $user->projects
        );
        $this->assertInstanceOf(
            Project::class,
            $user->projects()->first()
        );
        $this->assertEquals(
            $project->id,
            $user->projects()->first()->id
        );
    }

    /**
     * Validate deleting a user that is assigned as a project user fails
     *
     * @test
     * @return void
     */
    public function deleteProjectUserFails(): void
    {
        $user    = User::factory()->create();
        $project = Project::factory()->create();
        ProjectUser::factory()->create(
            [
                ProjectUser::USER_ID    => $user->id,
                ProjectUser::PROJECT_ID => $project->id
            ]
        );
        $this->expectException(QueryException::class);
        $user->delete();
    }

    /**
     * Validate the owned work items relation
     *
     * @test
     * @return void
     */
    public function ownedWorkItemsRelation(): void
    {
        WorkItem::factory()->create();
        $user     = User::factory()->create();
        $workItem = WorkItem::factory()->create([WorkItem::OWNER_ID => $user->id]);
        $this->assertInstanceOf(
            Collection::class,
            $user->owned_work_items
        );
        $this->assertCount(
            1,
            $user->owned_work_items
        );
        $this->assertInstanceOf(
            WorkItem::class,
            $user->owned_work_items()->first()
        );
        $this->assertEquals(
            $workItem->id,
            $user->owned_work_items()->first()->id
        );
    }

    /**
     * Validate the assigned work items relation
     *
     * @test
     * @return void
     */
    public function assignedWorkItemsRelation(): void
    {
        WorkItemUser::factory()->create();
        $user     = User::factory()->create();
        $workItem = WorkItem::factory()->create();
        WorkItemUser::factory()->create(
            [
                WorkItemUser::USER_ID      => $user->id,
                WorkItemUser::WORK_ITEM_ID => $workItem->id
            ]
        );
        $this->assertInstanceOf(
            Collection::class,
            $user->assigned_work_items
        );
        $this->assertCount(
            1,
            $user->assigned_work_items
        );
        $this->assertInstanceOf(
            WorkItem::class,
            $user->assigned_work_items()->first()
        );
        $this->assertEquals(
            $workItem->id,
            $user->assigned_work_items()->first()->id
        );
    }

    /**
     * Validate user permissions are pull correctly
     *
     * @test
     * @return void
     */
    public function getPermissionsAsJson(): void
    {
        Artisan::call('app:permissions');
        $user = User::factory()->create();
        $user->assignRole(Roles::SUPER_ADMIN);
        $permissions = $user->getPermissionsAsJson();
        $this->assertIsString($permissions);
        $permissions = json_decode($permissions, true);
        /** @var array{roles: Collection<string>, permissions: Collection<string>} $permissions */
        $this->assertIsIterable($permissions['roles']);
        $this->assertCount(1, $permissions['roles']);
        $this->assertEquals(Roles::SUPER_ADMIN, $permissions['roles'][0]);
        $this->assertCount(count(Permissions::LIST), $permissions['permissions']);
    }
}
