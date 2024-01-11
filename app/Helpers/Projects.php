<?php

declare(strict_types=1);

namespace App\Helpers;

use App\Models\Project;
use App\Models\Roles;
use App\Models\User;
use App\Models\WorkItem;
use Illuminate\Database\Eloquent\Collection;

class Projects
{
    /**
     * Get all projects for a user
     *
     * @param User $user
     * @param bool $getInActive
     *
     * @return Collection
     */
    public function getProjects(User &$user, bool $getInActive = false): Collection
    {
        switch ($user->getRoleNames()->first()) {
            case Roles::SUPER_ADMIN:
            case Roles::ADMIN:
                $projects = $getInActive ? Project::all() : Project::whereActive()->get();
                break;
            case Roles::PROJECT_MANAGER:
                $projects = $this->getProjectManagerProjects($user, $getInActive);
                break;
            case Roles::ENGINEER:
                $projects = $this->getEngineerProjects($user, $getInActive);
                break;
            default:
                $projects = new Collection();
                break;
        }
        return $projects->sortBy(Project::CREATED_AT, SORT_REGULAR, true);
    }

    /**
     * Get all projects an engineer is associated with
     *
     * @param User $engineer
     * @param bool $getInActive
     *
     * @return Collection
     */
    public function getEngineerProjects(User &$engineer, bool $getInActive = false): Collection
    {
        $workItems = $engineer->assigned_work_items;
        $list      = new Collection();
        /** @var WorkItem $workItem */
        foreach ($workItems as $workItem) {
            if (!$getInActive && !$workItem->project->is_active) {
                continue;
            }
            $list->add($workItem->project);
        }
        $workItems = $engineer->owned_work_items;
        /** @var WorkItem $workItem */
        foreach ($workItems as $workItem) {
            if (!$getInActive && !$workItem->project->is_active) {
                continue;
            }
            $list->add($workItem->project);
        }
        return $list->unique();
    }

    /**
     * Get project manager projects
     *
     * @param User $projectManager
     * @param bool $getInActive
     *
     * @return Collection
     */
    public function getProjectManagerProjects(User &$projectManager, bool $getInActive = false): Collection
    {
        $list  = $getInActive ?
            Project::whereProjectManager($projectManager->id)->get() :
            Project::whereActive()->whereProjectManager($projectManager->id)->get();
        $other = User::find($projectManager->id)->projects;
        foreach ($other as $project) {
            if (!$getInActive && !$project->is_active) {
                continue;
            }
            $list->add($project);
        }
        return $list->unique();
    }

    /**
     * Determine if a given user can access a project
     *
     * @param User $user
     * @param int  $projectId
     *
     * @return bool
     */
    public function canAccessProject(User &$user, int $projectId): bool
    {
        if (in_array($user->getRoleNames()->first(), [Roles::SUPER_ADMIN, Roles::ADMIN])) {
            return true;
        }
        $projects = $this->getProjects($user, true);
        return $projects->contains(
            function (Project $project) use ($projectId) {
                return $project->id == $projectId;
            }
        );
    }
}
