<?php

namespace App\Repositories;

use App\Models\Task;
use Illuminate\Database\Eloquent\Collection;

class TaskRepository
{
    public function getByProject(int $projectId, array $filters = []): Collection
    {
        $query = Task::query()->where('project_id', $projectId);

        if (array_key_exists('status', $filters) && $filters['status']) {
            $query->where('status', $filters['status']);
        }

        if (array_key_exists('user_id', $filters) && $filters['user_id']) {
            $query->where('user_id', $filters['user_id']);
        }

        if (array_key_exists('due_date', $filters) && $filters['due_date']) {
            $query->where('due_date', $filters['due_date']);
        }

        return $query->get();
    }

    public function create(array $data): Task
    {
        return Task::create($data);
    }

    public function findOrFail(int $id): Task
    {
        return Task::findOrFail($id);
    }

    public function update(Task $task, array $data): Task
    {
        $task->update($data);

        return $task;
    }

    public function delete(Task $task): void
    {
        $task->delete();
    }
}


