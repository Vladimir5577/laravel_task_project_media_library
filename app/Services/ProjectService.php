<?php

namespace App\Services;

use App\Models\Project;
use App\Repositories\ProjectRepository;
use Illuminate\Database\Eloquent\Collection;

class ProjectService
{
    public function __construct(
        private readonly ProjectRepository $projectRepository
    ) {
    }

    public function getAll(): Collection
    {
        return $this->projectRepository->getAll();
    }

    public function create(array $data): Project
    {
        return $this->projectRepository->create($data);
    }

    public function find(int $id): Project
    {
        return $this->projectRepository->findOrFail($id);
    }

    public function update(int $id, array $data): Project
    {
        $project = $this->projectRepository->findOrFail($id);

        return $this->projectRepository->update($project, $data);
    }

    public function delete(int $id): void
    {
        $project = $this->projectRepository->findOrFail($id);

        $this->projectRepository->delete($project);
    }
}


