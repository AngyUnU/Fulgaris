<?php

namespace Tests\Feature;

use App\Models\Activity;
use App\Models\Project;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BeTrackCoreTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $developer;

    protected Role $adminRole;

    protected Role $devRole;

    protected function setUp(): void
    {
        parent::setUp();

        $this->adminRole = Role::create([
            'name' => 'admin',
            'label' => 'Administrador',
            'description' => 'Admin role',
        ]);

        $this->devRole = Role::create([
            'name' => 'desarrollador',
            'label' => 'Desarrollador',
            'description' => 'Dev role',
        ]);

        $this->admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@test.com',
            'password' => bcrypt('password123'),
            'role_id' => $this->adminRole->id,
        ]);

        $this->developer = User::create([
            'name' => 'Dev User',
            'email' => 'dev@test.com',
            'password' => bcrypt('password123'),
            'role_id' => $this->devRole->id,
        ]);
    }

    public function test_dashboard_can_be_rendered_for_authenticated_users(): void
    {
        $response = $this->actingAs($this->admin)->get('/dashboard');
        $response->assertStatus(200);
        $response->assertSee('Resumen de trabajo');
        $response->assertDontSee('Bitácora de Avances');
    }

    public function test_projects_can_be_listed_and_created(): void
    {
        $response = $this->actingAs($this->admin)->get('/projects');
        $response->assertStatus(200);

        $postResponse = $this->actingAs($this->admin)->post('/projects', [
            'code' => 'BP-TEST-01',
            'name' => 'Proyecto de Prueba',
            'description' => 'Descripción del proyecto',
            'status' => 'en_progreso',
            'start_date' => now()->toDateString(),
            'due_date' => now()->addDays(30)->toDateString(),
            'leader_id' => $this->admin->id,
            'activities' => [[
                'title' => 'Actividad de arranque',
                'priority' => 'alta',
                'status' => 'en_progreso',
                'progress' => 35,
            ]],
        ]);

        $project = Project::where('code', 'BP-TEST-01')->firstOrFail();
        $postResponse->assertRedirect(route('projects.show', $project));
        $this->assertDatabaseHas('projects', ['code' => 'BP-TEST-01']);
        $this->assertDatabaseHas('activities', [
            'project_id' => $project->id,
            'title' => 'Actividad de arranque',
            'progress' => 35,
            'status' => 'en_progreso',
        ]);
        $this->assertFalse(\Illuminate\Support\Facades\Schema::hasTable('activity_checklist_items'));
    }

    public function test_activity_can_be_created_and_assigned(): void
    {
        $project = Project::create([
            'code' => 'BP-ACT-01',
            'name' => 'Proyecto Actividad',
            'status' => 'en_progreso',
            'created_by' => $this->admin->id,
        ]);

        $postResponse = $this->actingAs($this->admin)->post('/activities', [
            'project_id' => $project->id,
            'title' => 'Diseñar interfaz de usuario',
            'description' => 'Mockups y vistas',
            'priority' => 'alta',
            'status' => 'en_progreso',
            'due_date' => now()->addDays(10)->toDateString(),
            'assignees' => [$this->developer->id],
            'progress' => 80,
        ]);

        $postResponse->assertStatus(302);
        $this->assertDatabaseHas('activities', ['title' => 'Diseñar interfaz de usuario']);
        $this->assertDatabaseHas('activity_user', [
            'user_id' => $this->developer->id,
        ]);
        $this->assertDatabaseHas('activities', [
            'title' => 'Diseñar interfaz de usuario',
            'progress' => 80,
            'status' => 'en_progreso',
        ]);
    }

    public function test_activity_progress_can_be_updated_manually(): void
    {
        $project = Project::create([
            'code' => 'BP-PROGRESS-01',
            'name' => 'Proyecto Progreso Manual',
            'status' => 'en_progreso',
            'created_by' => $this->admin->id,
        ]);

        $this->actingAs($this->admin)->post('/activities', [
            'project_id' => $project->id,
            'title' => 'Actividad con progreso manual',
            'priority' => 'media',
            'status' => 'pendiente',
            'progress' => 0,
        ])->assertRedirect();

        $activity = Activity::where('title', 'Actividad con progreso manual')->firstOrFail();
        $this->actingAs($this->developer)->put(route('activities.update', $activity), [
            'title' => $activity->title,
            'priority' => 'media',
            'status' => 'en_progreso',
            'progress' => 67,
        ])->assertRedirect();
        $this->assertDatabaseHas('activities', ['id' => $activity->id, 'progress' => 67, 'status' => 'en_progreso']);

        $this->actingAs($this->developer)->put(route('activities.update', $activity), [
            'title' => $activity->title,
            'priority' => 'media',
            'status' => 'en_progreso',
            'progress' => 100,
        ])->assertRedirect();
        $this->assertDatabaseHas('activities', ['id' => $activity->id, 'progress' => 100, 'status' => 'completada']);
    }

    public function test_monitoring_can_update_manual_progress_with_a_comment(): void
    {
        $project = Project::create([
            'code' => 'BP-MON-01',
            'name' => 'Proyecto Seguimiento',
            'status' => 'en_progreso',
            'created_by' => $this->admin->id,
        ]);
        $activity = Activity::create([
            'project_id' => $project->id,
            'title' => 'Actividad de seguimiento',
            'priority' => 'media',
            'status' => 'en_progreso',
            'progress' => 20,
        ]);

        $this->actingAs($this->admin)->post(route('monitoring.comments.store'), [
            'activity_id' => $activity->id,
            'comment' => 'Se completó una parte del trabajo.',
            'progress' => 65,
        ])->assertRedirect();

        $this->assertDatabaseHas('activities', ['id' => $activity->id, 'progress' => 65]);
        $this->assertDatabaseHas('activity_comments', [
            'activity_id' => $activity->id,
            'progress_reported' => 65,
        ]);
    }

    public function test_mobile_api_workflow(): void
    {
        // 1. Mobile Login
        $loginResponse = $this->postJson('/api/v1/login', [
            'email' => 'dev@test.com',
            'password' => 'password123',
            'device_name' => 'IonicAppTest',
        ]);

        $loginResponse->assertStatus(200);
        $token = $loginResponse->json('token');
        $this->assertNotEmpty($token);

        // 2. Create activity assigned to dev
        $project = Project::create([
            'code' => 'BP-MOB-01',
            'name' => 'Proyecto Móvil',
            'status' => 'en_progreso',
        ]);

        $activity = Activity::create([
            'project_id' => $project->id,
            'title' => 'Tarea móvil',
            'priority' => 'media',
            'status' => 'en_progreso',
            'progress' => 0,
        ]);
        $activity->assignees()->attach($this->developer->id);
        // 3. Get my activities from mobile
        $activitiesResponse = $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/v1/my-activities');

        $activitiesResponse->assertStatus(200);
        $this->assertEquals(1, $activitiesResponse->json('count'));
        $activitiesResponse->assertJsonMissingPath('data.0.checklist_items');

        // 4. Report manual progress from mobile
        $updateResponse = $this->withHeader('Authorization', 'Bearer '.$token)
            ->postJson("/api/v1/activities/{$activity->id}/progress", [
                'progress' => 75,
                'comment' => 'El integrante completó la tarea desde la app móvil.',
            ]);

        $updateResponse->assertStatus(200);
        $this->assertDatabaseHas('activities', [
            'id' => $activity->id,
            'progress' => 75,
        ]);
        $this->assertDatabaseHas('activity_comments', [
            'activity_id' => $activity->id,
            'progress_reported' => 75,
            'source' => 'mobile',
        ]);
    }
}
