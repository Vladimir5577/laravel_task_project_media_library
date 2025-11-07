<?php

namespace App\Services;

use App\Models\Task;
use App\Mail\TaskCreated;
use Illuminate\Http\UploadedFile;
use App\Repositories\TaskRepository;
use Illuminate\Support\Facades\Mail;
use App\Repositories\ProjectRepository;
use Illuminate\Database\Eloquent\Collection;

class TaskService
{
    public function __construct(
        private readonly TaskRepository $taskRepository,
        private readonly ProjectRepository $projectRepository
    ) {
    }

    public function getTasksForProject(int $projectId, array $filters = []): Collection
    {
        return $this->taskRepository->getByProject($projectId, $filters);
    }

    public function createTask(int $projectId, array $data, ?UploadedFile $attachment = null): Task
    {
        $this->projectRepository->findOrFail($projectId);

        $task = $this->taskRepository->create(array_merge($data, [
            'project_id' => $projectId,
        ]));

        if ($attachment) {
            $task->addMediaFile($attachment);
        }

        Mail::to($task->user->email)->send(new TaskCreated($task));

        return $task;
    }

    public function findTask(int $id): Task
    {
        return $this->taskRepository->findOrFail($id);
    }

    public function updateTask(int $id, array $data): Task
    {
        $task = $this->taskRepository->findOrFail($id);

        return $this->taskRepository->update($task, $data);
    }

    public function deleteTask(int $id): void
    {
        $task = $this->taskRepository->findOrFail($id);

        $this->taskRepository->delete($task);
    }
}


