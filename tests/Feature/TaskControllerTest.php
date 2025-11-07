<?php

namespace Tests\Feature;

use App\Mail\TaskCreated;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class TaskControllerTest extends TestCase
{
    use RefreshDatabase;

    private function authenticate(): User
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        return $user;
    }

    public function test_index_filters_tasks_by_status_and_user(): void
    {
        $this->authenticate();

        $project = Project::factory()->create();
        $anotherProject = Project::factory()->create();
        $targetUser = User::factory()->create();

        Task::factory()->count(2)->create([
            'project_id' => $project->id,
            'status' => 'planned',
            'user_id' => $targetUser->id,
        ]);

        Task::factory()->create([
            'project_id' => $project->id,
            'status' => 'done',
        ]);

        Task::factory()->create([
            'project_id' => $anotherProject->id,
        ]);

        $response = $this->getJson("/api/projects/{$project->id}/tasks?status=planned&user_id={$targetUser->id}");

        $response->assertOk()
            ->assertJsonCount(2)
            ->assertJsonFragment(['status' => 'planned']);
    }

    public function test_store_creates_task_and_sends_notification(): void
    {
        $this->authenticate();

        Mail::fake();

        $project = Project::factory()->create();
        $assignee = User::factory()->create();

        $payload = [
            'title' => 'New Task',
            'description' => 'Task description',
            'status' => 'planned',
            'due_date' => '2025-11-15',
            'user_id' => $assignee->id,
        ];

        $response = $this->postJson("/api/projects/{$project->id}/tasks", $payload);

        $response->assertCreated()
            ->assertJsonFragment([
                'title' => 'New Task',
                'status' => 'planned',
            ]);

        $this->assertDatabaseHas('tasks', array_merge($payload, [
            'project_id' => $project->id,
        ]));

        Mail::assertSent(TaskCreated::class, function (TaskCreated $mail) use ($assignee) {
            return $mail->hasTo($assignee->email) && $mail->task->title === 'New Task';
        });
    }

    public function test_store_requires_title_description_status_and_user(): void
    {
        $this->authenticate();

        $project = Project::factory()->create();

        $response = $this->postJson("/api/projects/{$project->id}/tasks", []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['title', 'description', 'status', 'user_id']);
    }

    public function test_show_returns_task(): void
    {
        $this->authenticate();

        $task = Task::factory()->create([
            'title' => 'Review docs',
        ]);

        $response = $this->getJson("/api/tasks/{$task->id}");

        $response->assertOk()
            ->assertJson([
                'id' => $task->id,
                'title' => 'Review docs',
            ]);
    }

    public function test_update_changes_task_fields(): void
    {
        $this->authenticate();

        $task = Task::factory()->create([
            'status' => 'planned',
        ]);

        $response = $this->putJson("/api/tasks/{$task->id}", [
            'status' => 'done',
            'description' => 'Updated description',
        ]);

        $response->assertOk()
            ->assertJsonFragment([
                'status' => 'done',
                'description' => 'Updated description',
            ]);

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'status' => 'done',
            'description' => 'Updated description',
        ]);
    }

    public function test_destroy_deletes_task(): void
    {
        $this->authenticate();

        $task = Task::factory()->create();

        $response = $this->deleteJson("/api/tasks/{$task->id}");

        $response->assertNoContent();

        $this->assertDatabaseMissing('tasks', [
            'id' => $task->id,
        ]);
    }
}


