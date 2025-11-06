<?php

namespace App\Http\Controllers;

use App\Mail\TaskCreated;
use App\Models\Task;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class TaskController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    public function index(Request $request, $project_id)
    {
        $tasks = Task::where('project_id', $project_id);

        if ($request->has('status')) {
            $tasks->where('status', $request->status);
        }

        if ($request->has('user_id')) {
            $tasks->where('user_id', $request->user_id);
        }

        if ($request->has('due_date')) {
            $tasks->where('due_date', $request->due_date);
        }

        return response()->json($tasks->get());
    }

    public function store(Request $request, $project_id)
    {
        $project = Project::findOrFail($project_id);

        $validated = $request->validate([
            'title' => 'required|string',
            'description' => 'required|string',
            'status' => 'required|in:planned,in_progress,done',
            'due_date' => 'nullable|date',
            'user_id' => 'required|exists:users,id',
        ]);

        $task = Task::create(array_merge($validated, ['project_id' => $project_id]));

        // Если прикреплён файл
        if ($request->hasFile('attachment')) {
            $task->addMediaFile($request->file('attachment'));
        }

        // Отправка уведомления по email
        Mail::to($task->user->email)->send(new TaskCreated($task));

        return response()->json($task, 201);
    }

    public function show($id)
    {
        $task = Task::findOrFail($id);
        return response()->json($task);
    }

    public function update(Request $request, $id)
    {
        $task = Task::findOrFail($id);

        $validated = $request->validate([
            'title' => 'nullable|string',
            'description' => 'nullable|string',
            'status' => 'nullable|in:planned,in_progress,done',
            'due_date' => 'nullable|date',
            'user_id' => 'nullable|exists:users,id',
        ]);

        $task->update($validated);

        return response()->json($task);
    }

    public function destroy($id)
    {
        $task = Task::findOrFail($id);
        $task->delete();

        return response()->json(null, 204);
    }
}
