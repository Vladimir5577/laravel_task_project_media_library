<?php

namespace App\Http\Controllers;

use App\Services\TaskService;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function __construct(
        private readonly TaskService $taskService
    ) {
        $this->middleware('auth:sanctum');
    }

    public function index(Request $request, $project_id)
    {
        $filters = $request->only(['status', 'user_id', 'due_date']);

        return response()->json(
            $this->taskService->getTasksForProject($project_id, $filters)
        );
    }

    public function store(Request $request, $project_id)
    {
        $validated = $request->validate([
            'title' => 'required|string',
            'description' => 'required|string',
            'status' => 'required|in:planned,in_progress,done',
            'due_date' => 'nullable|date',
            'user_id' => 'required|exists:users,id',
        ]);

        $task = $this->taskService->createTask(
            $project_id,
            $validated,
            $request->file('attachment')
        );

        return response()->json($task, 201);
    }

    public function show($id)
    {
        return response()->json($this->taskService->findTask($id));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'title' => 'nullable|string',
            'description' => 'nullable|string',
            'status' => 'nullable|in:planned,in_progress,done',
            'due_date' => 'nullable|date',
            'user_id' => 'nullable|exists:users,id',
        ]);

        return response()->json($this->taskService->updateTask($id, $validated));
    }

    public function destroy($id)
    {
        $this->taskService->deleteTask($id);

        return response()->json(null, 204);
    }
}
