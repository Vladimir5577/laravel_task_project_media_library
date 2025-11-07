<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ProjectControllerTest extends TestCase
{
    use RefreshDatabase;

    private function authenticate(): User
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        return $user;
    }

    public function test_index_returns_projects_for_authenticated_user(): void
    {
        $this->authenticate();

        Project::factory()->count(3)->create();

        $response = $this->getJson('/api/projects');

        $response->assertOk()
            ->assertJsonCount(3);
    }

    public function test_store_creates_project_with_valid_data(): void
    {
        $this->authenticate();

        $payload = [
            'name' => 'New Project',
            'description' => 'Project description',
        ];

        $response = $this->postJson('/api/projects', $payload);

        $response->assertCreated()
            ->assertJsonFragment($payload);

        $this->assertDatabaseHas('projects', $payload);
    }

    public function test_store_requires_name_field(): void
    {
        $this->authenticate();

        $response = $this->postJson('/api/projects', [
            'description' => 'Missing name field',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name']);
    }

    public function test_show_returns_project(): void
    {
        $this->authenticate();

        $project = Project::factory()->create();

        $response = $this->getJson("/api/projects/{$project->id}");

        $response->assertOk()
            ->assertJson([
                'id' => $project->id,
                'name' => $project->name,
                'description' => $project->description,
            ]);
    }

    public function test_update_updates_project_fields(): void
    {
        $this->authenticate();

        $project = Project::factory()->create();

        $response = $this->putJson("/api/projects/{$project->id}", [
            'name' => 'Updated Name',
            'description' => 'Updated Description',
        ]);

        $response->assertOk()
            ->assertJsonFragment([
                'name' => 'Updated Name',
                'description' => 'Updated Description',
            ]);

        $this->assertDatabaseHas('projects', [
            'id' => $project->id,
            'name' => 'Updated Name',
            'description' => 'Updated Description',
        ]);
    }

    public function test_destroy_deletes_project(): void
    {
        $this->authenticate();

        $project = Project::factory()->create();

        $response = $this->deleteJson("/api/projects/{$project->id}");

        $response->assertNoContent();

        $this->assertDatabaseMissing('projects', [
            'id' => $project->id,
        ]);
    }
}


