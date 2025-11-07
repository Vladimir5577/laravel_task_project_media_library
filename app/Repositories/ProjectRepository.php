<?php

namespace App\Repositories;

use App\Models\Project;
use Illuminate\Database\Eloquent\Collection;

class ProjectRepository
{
    public function getAll(): Collection
    {
        return Project::all();
    }

    public function create(array $data): Project
    {
        return Project::create($data);
    }

    public function findOrFail(int $id): Project
    {
        return Project::findOrFail($id);
    }

    public function update(Project $project, array $data): Project
    {
        $project->update($data);

        return $project;
    }

    public function delete(Project $project): void
    {
        $project->delete();
    }
}


