<?php

declare(strict_types=1);

namespace Feature\Controllers;

use App\Models\Project;
use App\Models\Roles;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;
use Tests\Traits\Permissions;

class AdminControllerTest extends TestCase
{
    use Permissions;
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->buildPermissions();
        $this->user = User::factory()->create();
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
        $this->user->assignRole($role);
    }

    /**
     * Validate the dashboard is returned
     *
     * @test
     * @return void
     */
    public function dashboard(): void
    {
        $result = $this->actingAs($this->user)
                       ->get(route('dashboard'))
                       ->assertInertia(
                           fn (Assert $page) => $page->component('Dashboard')
                       );
    }

    /**
     * Validate the orders page is returned
     *
     * @test
     * @return void
     */
    public function orders(): void
    {
        $this->assignRole(Roles::SUPER_ADMIN);
        $result = $this->actingAs($this->user)
                       ->get(route('orders'))
                       ->assertInertia(
                           fn (Assert $page) => $page->component('Orders')
                       );
    }

    /**
     * Validate the orders page is restricted if the user
     * does not have a valid role
     *
     * @test
     * @return void
     */
    public function ordersRestricted(): void
    {
        $result = $this->actingAs($this->user)
                       ->get(route('orders'))
                       ->assertStatus(403);
    }

    /**
     * Validate the users page is returned
     *
     * @test
     * @return void
     */
    public function users(): void
    {
        $this->assignRole(Roles::SUPER_ADMIN);
        $result = $this->actingAs($this->user)
                       ->get(route('users'))
                       ->assertInertia(
                           fn (Assert $page) => $page->component('Users')
                       );
    }

    /**
     * Validate the users page is restricted if the user
     * does not have a valid role
     *
     * @test
     * @return void
     */
    public function usersRestricted(): void
    {
        $result = $this->actingAs($this->user)
                       ->get(route('users'))
                       ->assertStatus(403);
    }

    /**
     * Validate the project page is returned
     *
     * @test
     * @return void
     */
    public function project(): void
    {
        $project = Project::factory()->create();
        $this->assignRole(Roles::SUPER_ADMIN);
        $this->actingAs($this->user)
             ->get(route('project', ['id' => $project->id]))
             ->assertInertia(
                 fn (Assert $page) => $page->component('Project')
             );
    }

    /**
     * Validate the project page redirects to the dashboard
     * if the project doesn't exist
     *
     * @test
     * @return void
     */
    public function invalidProject(): void
    {
        Project::factory()->create();
        $this->assignRole(Roles::SUPER_ADMIN);
        $this->actingAs($this->user)
             ->get(route('project', ['id' => 1000]))
             ->assertRedirect(route('dashboard'));
    }

    /**
     * Validate the project page redirects to the dashboard
     * if the user cannot access the project
     *
     * @test
     * @return void
     */
    public function invalidProjectAccess(): void
    {
        $project = Project::factory()->create();
        $this->assignRole(Roles::ENGINEER);
        $this->actingAs($this->user)
             ->get(route('project', ['id' => $project->id]))
             ->assertRedirect(route('dashboard'));
    }
}
