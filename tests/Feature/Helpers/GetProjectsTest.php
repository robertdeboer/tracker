<?php

declare(strict_types=1);

namespace Feature\Helpers;

use App\Helpers\Projects;
use App\Models\Project;
use App\Models\ProjectUser;
use App\Models\Roles;
use App\Models\User;
use App\Models\WorkItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class GetProjectsTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private Projects $helper;

    /**
     * Per test setup
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->user   = User::factory()->create();
        $this->helper = new Projects();
    }

    /**
     * Assign a given role to the test user
     *
     * @param string $role
     *
     * @return void
     */
    public function assignRole(string $role): void
    {
        Role::findOrCreate($role);
        $this->user->assignRole($role);
    }

    /**
     * Validate getting projects as a user with no assigned role
     * @test
     * @return void
     */
    public function noRole(): void
    {
        Project::factory(3)->create();
        $list = $this->helper->getProjects($this->user, true);
        $this->assertCount(
            0,
            $list
        );
    }

    /**
     * Validate getting projects as a super admin
     * @test
     * @return void
     */
    public function getSuperAdminProjects(): void
    {
        $this->assignRole(Roles::SUPER_ADMIN);
        Project::factory(3)->create();
        $list = $this->helper->getProjects($this->user, true);
        $this->assertCount(
            3,
            $list
        );
    }

    /**
     * Validate getting only active projects as a super admin
     * @test
     * @return void
     */
    public function getSuperAdminProjectsOnlyActive(): void
    {
        $this->assignRole(Roles::SUPER_ADMIN);
        Project::factory(1)->create([Project::IS_ACTIVE => true]);
        Project::factory(2)->create([Project::IS_ACTIVE => false]);
        $list = $this->helper->getProjects($this->user);
        $this->assertCount(
            1,
            $list
        );
    }

    /**
     * Validate getting projects as an admin
     * @test
     * @return void
     */
    public function getAdminProjects(): void
    {
        $this->assignRole(Roles::ADMIN);
        Project::factory(3)->create();
        $list = $this->helper->getProjects($this->user, true);
        $this->assertCount(
            3,
            $list
        );
    }

    /**
     * Validate getting only active projects as an admin
     * @test
     * @return void
     */
    public function getAdminProjectsOnlyActive(): void
    {
        $this->assignRole(Roles::ADMIN);
        Project::factory(1)->create([Project::IS_ACTIVE => true]);
        Project::factory(2)->create([Project::IS_ACTIVE => false]);
        $list = $this->helper->getProjects($this->user);
        $this->assertCount(
            1,
            $list
        );
    }

    /**
     * Validate getting projects as a project manager
     * @test
     * @return void
     */
    public function getProjectManagerProjects(): void
    {
        $this->assignRole(Roles::PROJECT_MANAGER);
        Project::factory(3)->create();
        $project = Project::factory()->create(
            [
                Project::PROJECT_MANAGER_ID => $this->user->id
            ]
        );
        $list    = $this->helper->getProjects($this->user, true);
        $this->assertCount(
            1,
            $list
        );
        $this->assertEquals(
            $project->id,
            $list->first()->id
        );
    }

    /**
     * Validate getting projects as a project manager
     * @test
     * @return void
     */
    public function getProjectManagerProjectsOnlyActive(): void
    {
        $this->assignRole(Roles::PROJECT_MANAGER);
        Project::factory(3)->create([Project::IS_ACTIVE => false]);
        $project = Project::factory()->create(
            [
                Project::PROJECT_MANAGER_ID => $this->user->id,
                Project::IS_ACTIVE          => true
            ]
        );
        $list    = $this->helper->getProjects($this->user);
        $this->assertCount(
            1,
            $list
        );
        $this->assertEquals(
            $project->id,
            $list->first()->id
        );
    }

    /**
     * Validate getting projects as a project user
     * @test
     * @return void
     */
    public function getProjectUsersProjects(): void
    {
        $this->assignRole(Roles::PROJECT_MANAGER);
        Project::factory(3)->create();
        $project = Project::factory()->create();
        ProjectUser::factory()->create(
            [
                ProjectUser::USER_ID    => $this->user->id,
                ProjectUser::PROJECT_ID => $project->id
            ]
        );
        $list = $this->helper->getProjects($this->user, true);
        $this->assertCount(
            1,
            $list
        );
        $this->assertEquals(
            $project->id,
            $list->first()->id
        );
    }

    /**
     * Validate getting projects as an engineer
     * @test
     * @return void
     */
    public function getEngineerProjects(): void
    {
        $this->assignRole(Roles::ENGINEER);
        WorkItem::factory(3)->create();
        $project = Project::factory()->create();
        WorkItem::factory()->create(
            [
                WorkItem::OWNER_ID   => $this->user->id,
                WorkItem::PROJECT_ID => $project->id
            ]
        );
        $list = $this->helper->getProjects($this->user, true);
        $this->assertCount(
            1,
            $list
        );
        $this->assertEquals(
            $project->id,
            $list->first()->id
        );
    }

    /**
     * Validate that a person cannot access a project
     * if checked
     *
     * @test
     * @return void
     */
    public function canAccessProjectFails(): void
    {
        $project = Project::factory()->create();
        $this->assignRole(Roles::PROJECT_MANAGER);
        $this->assertFalse(
            $this->helper->canAccessProject(
                $this->user,
                $project->id
            )
        );
    }

    /**
     * Validate that a person can access a project
     * if checked
     *
     * @test
     * @return void
     */
    public function canAccessProjectPasses(): void
    {
        $this->assignRole(Roles::PROJECT_MANAGER);
        $project = Project::factory()->create(
            [
                Project::PROJECT_MANAGER_ID => $this->user->id
            ]
        );
        $this->assertTrue(
            $this->helper->canAccessProject(
                $this->user,
                $project->id
            )
        );
    }

    /**
     * Validate that an admin can access a project
     * if checked
     *
     * @test
     * @return void
     */
    public function canAccessProjectAsAdmin(): void
    {
        $this->assignRole(Roles::ADMIN);
        $project = Project::factory()->create();
        $this->assertTrue(
            $this->helper->canAccessProject(
                $this->user,
                $project->id
            )
        );
    }

    /**
     * Validate that a super admin can access a project
     * if checked
     *
     * @test
     * @return void
     */
    public function canAccessProjectAsSuperAdmin(): void
    {
        $this->assignRole(Roles::SUPER_ADMIN);
        $project = Project::factory()->create();
        $this->assertTrue(
            $this->helper->canAccessProject(
                $this->user,
                $project->id
            )
        );
    }
}
