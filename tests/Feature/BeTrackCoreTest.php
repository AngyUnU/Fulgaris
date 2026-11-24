<?php

namespace Tests\Feature;

use App\Models\Activity;
use App\Models\ActivityComment;
use App\Models\Project;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
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
                'status' => 'en_revision',
            ]],
        ]);

        $project = Project::where('code', 'BP-TEST-01')->firstOrFail();
        $postResponse->assertRedirect(route('projects.show', $project));
        $this->assertDatabaseHas('projects', ['code' => 'BP-TEST-01']);
        $this->assertDatabaseHas('activities', [
            'project_id' => $project->id,
            'title' => 'Actividad de arranque',
            'status' => 'en_revision',
        ]);
        $this->assertFalse(Schema::hasColumn('activities', 'progress'));
        $this->assertTrue(Schema::hasColumn('activity_comments', 'progress_reported'));
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
        ]);

        $postResponse->assertStatus(302);
        $this->assertDatabaseHas('activities', ['title' => 'Diseñar interfaz de usuario']);
        $this->assertDatabaseHas('activity_user', [
            'user_id' => $this->developer->id,
        ]);
        $this->assertDatabaseHas('activities', ['title' => 'Diseñar interfaz de usuario', 'status' => 'en_progreso']);
    }

    public function test_activity_status_updates_drive_project_completion(): void
    {
        $project = Project::create([
            'code' => 'BP-STATUS-01',
            'name' => 'Proyecto por estados',
            'status' => 'en_progreso',
            'created_by' => $this->admin->id,
        ]);

        $this->actingAs($this->admin)->post('/activities', [
            'project_id' => $project->id,
            'title' => 'Actividad por estado',
            'priority' => 'media',
            'status' => 'pendiente',
            'assignees' => [$this->developer->id],
        ])->assertRedirect();

        $activity = Activity::where('title', 'Actividad por estado')->firstOrFail();
        $otherActivity = Activity::create([
            'project_id' => $project->id,
            'title' => 'Otra actividad pendiente',
            'priority' => 'baja',
            'status' => 'pendiente',
        ]);
        $otherActivity->assignees()->sync([$this->developer->id]);
        $this->assertSame(0, $project->fresh()->calculated_progress);

        $this->actingAs($this->developer)->put(route('activities.update', $activity), [
            'title' => $activity->title,
            'priority' => 'media',
            'status' => 'en_revision',
            'assignees' => [$this->developer->id],
        ])->assertRedirect();
        $this->assertDatabaseHas('activities', ['id' => $activity->id, 'status' => 'en_revision']);
        $this->assertSame(0, $project->fresh()->calculated_progress);

        $this->actingAs($this->developer)->put(route('activities.update', $activity), [
            'title' => $activity->title,
            'priority' => 'media',
            'status' => 'completada',
            'assignees' => [$this->developer->id],
        ])->assertRedirect();
        $this->assertDatabaseHas('activities', ['id' => $activity->id, 'status' => 'completada']);
        $this->assertSame(50, $project->fresh()->calculated_progress);
    }

    public function test_activity_requires_at_least_one_assignee_on_create_and_update(): void
    {
        $project = Project::create([
            'code' => 'BP-ASSIGNEE-01',
            'name' => 'Proyecto Encargados',
            'status' => 'en_progreso',
            'created_by' => $this->admin->id,
        ]);

        // Fails when creating without assignees
        $response = $this->actingAs($this->admin)->post('/activities', [
            'project_id' => $project->id,
            'title' => 'Actividad sin encargado',
            'priority' => 'alta',
            'status' => 'pendiente',
            'assignees' => [],
        ]);
        $response->assertSessionHasErrors('assignees');

        // Succeeds when at least 1 assignee is provided
        $createResponse = $this->actingAs($this->admin)->post('/activities', [
            'project_id' => $project->id,
            'title' => 'Actividad con encargado',
            'priority' => 'alta',
            'status' => 'pendiente',
            'assignees' => [$this->developer->id],
        ]);
        $createResponse->assertRedirect();
        $activity = Activity::where('title', 'Actividad con encargado')->firstOrFail();
        $this->assertTrue($activity->assignees->contains($this->developer));

        // Fails when updating and trying to empty assignees
        $updateFail = $this->actingAs($this->developer)->put(route('activities.update', $activity), [
            'title' => 'Actividad con encargado',
            'priority' => 'alta',
            'status' => 'en_progreso',
            'assignees' => [],
        ]);
        $updateFail->assertSessionHasErrors('assignees');

        // Succeeds when adding another assignee or switching
        $updateSuccess = $this->actingAs($this->developer)->put(route('activities.update', $activity), [
            'title' => 'Actividad con encargado',
            'priority' => 'alta',
            'status' => 'en_progreso',
            'assignees' => [$this->developer->id, $this->admin->id],
        ]);
        $updateSuccess->assertRedirect();
        $this->assertCount(2, $activity->fresh()->assignees);
    }

    public function test_activity_comments_can_store_and_open_private_image_evidence(): void
    {
        Storage::fake('local');

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
        ]);

        $this->actingAs($this->admin)->post(route('monitoring.comments.store'), [
            'activity_id' => $activity->id,
            'comment' => 'Se adjunta la captura como evidencia.',
            'evidence' => UploadedFile::fake()->createWithContent(
                'captura.png',
                base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO+/l1sAAAAASUVORK5CYII=')
            ),
        ])->assertRedirect();

        $comment = ActivityComment::where('activity_id', $activity->id)->firstOrFail();
        $this->assertSame('captura.png', $comment->evidence_name);
        $this->assertArrayNotHasKey('progress_reported', $comment->toArray());
        Storage::disk('local')->assertExists($comment->evidence_path);
        auth()->logout();
        $this->get(route('activities.comments.evidence', $comment))->assertRedirect(route('login'));
        $this->actingAs($this->admin)
            ->get(route('activities.comments.evidence', $comment))
            ->assertOk()
            ->assertHeader('X-Content-Type-Options', 'nosniff');
        $token = $this->admin->createToken('evidence-test')->plainTextToken;
        $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson("/api/v1/activity-comments/{$comment->id}/evidence")
            ->assertOk()
            ->assertHeader('X-Content-Type-Options', 'nosniff');
        $this->assertDatabaseHas('activities', ['id' => $activity->id, 'status' => 'en_progreso']);
        $this->actingAs($this->admin)->get('/activities')
            ->assertOk()
            ->assertSee('Evidencia')
            ->assertSee('captura.png')
            ->assertDontSee('Progreso (%)');
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
        ]);
        $activity->assignees()->attach($this->developer->id);
        // 3. Get my activities from mobile
        $activitiesResponse = $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/v1/my-activities');

        $activitiesResponse->assertStatus(200);
        $this->assertEquals(1, $activitiesResponse->json('count'));
        $activitiesResponse->assertJsonMissingPath('data.0.checklist_items');

        // 4. Update status from mobile
        $updateResponse = $this->withHeader('Authorization', 'Bearer '.$token)
            ->postJson("/api/v1/activities/{$activity->id}/status", [
                'status' => 'en_revision',
                'comment' => 'La actividad está lista para revisión.',
            ]);

        $updateResponse->assertStatus(200);
        $this->assertDatabaseHas('activities', [
            'id' => $activity->id,
            'status' => 'en_revision',
        ]);
        $this->assertDatabaseHas('activity_comments', [
            'activity_id' => $activity->id,
            'source' => 'mobile',
        ]);
    }
}
