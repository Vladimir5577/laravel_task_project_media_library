<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ProjectService;

class ProjectController extends Controller
{
    public function __construct(
        private readonly ProjectService $projectService
    ) {
    }

    public function index()
    {
        return response()->json($this->projectService->getAll());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'description' => 'nullable|string',
        ]);

        $project = $this->projectService->create($validated);

        return response()->json($project, 201);
    }

    public function show($id)
    {
        return response()->json($this->projectService->find($id));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'nullable|string',
            'description' => 'nullable|string',
        ]);

        return response()->json($this->projectService->update($id, $validated));
    }

    public function destroy($id)
    {
        $this->projectService->delete($id);

        return response()->json(null, 204);
    }
}
