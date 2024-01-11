<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App;
use App\Helpers\Projects;
use App\Models\Project;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Inertia\Response;

class AdminController extends Controller
{
    /**
     * The dashboard
     *
     * @return Response
     */
    public function dashboard(): Response
    {
        return Inertia::render(
            'Dashboard',
            [
                'title' => config('app.name')
            ]
        );
    }

    /**
     * The orders page
     *
     * @return Response
     */
    public function orders(): Response
    {
        return Inertia::render(
            'Orders',
            [
                'title' => config('app.name')
            ]
        );
    }

    /**
     * The project detail page
     *
     * @param string $id The project ID
     *
     * @return Response|RedirectResponse
     */
    public function project(string $id): Response|RedirectResponse
    {
        $project = Project::find($id);
        if (!$project instanceof Project) {
            return redirect()->route('dashboard');
        }
        $helper = new Projects();
        $user   = auth()->user();
        if (!$helper->canAccessProject($user, $project->id)) {
            return redirect()->route('dashboard');
        }
        return Inertia::render(
            'Project',
            [
                'title'       => config('app.name'),
                'projectId'   => $project->id,
                'projectName' => $project->name
            ]
        );
    }

    /**
     * The users page
     *
     * @return Response
     */
    public function users(): Response
    {
        return Inertia::render(
            'Users',
            [
                'title' => config('app.name')
            ]
        );
    }

    public function welcome(): Response
    {
        return Inertia::render(
            'Welcome',
            [
                'canLogin'    => App::environment() !== 'production' && Route::has('login'),
                'canRegister' => App::environment() !== 'production' && Route::has('register'),
                'title'       => config('app.name')
            ]
        );
    }
}
