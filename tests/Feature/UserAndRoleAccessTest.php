<?php

namespace Tests\Feature;

use App\Models\Activity;
use App\Models\Project;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserAndRoleAccessTest extends TestCase
{
    use RefreshDatabase;

    private Role $adminRole;

    private Role $leaderRole;

    private Role $developerRole;

    private Role $qaRole;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->adminRole = $this->createRole('admin');
        $this->leaderRole = $this->createRole('lider');
        $this->developerRole = $this->createRole('desarrollador');
        $this->qaRole = $this->createRole('qa');
        $this->admin = $this->createUser($this->adminRole, 'admin@example.com');
    }

    public function test_only_admins_can_create_accounts_and_choose_their_roles(): void
    {
        $payload = [
            'name' => 'New Developer',
            'email' => 'developer@example.com',
            'role_id' => $this->developerRole->id,
            'password' => 'Secure123!',
            'password_confirmation' => 'Secure123!',
        ];

        $this->actingAs($this->admin)->post('/users', $payload)
            ->assertRedirect(route('users.index'));

        $this->assertDatabaseHas('users', [
            'email' => 'developer@example.com',
            'role_id' => $this->developerRole->id,
        ]);
        $this->actingAs($this->admin)->get('/users')->assertOk()->assertSee('Crear cuenta de usuario');

        $developer = $this->createUser($this->developerRole, 'existing@example.com');

        $this->actingAs($developer)->post('/users', [
            ...$payload,
            'email' => 'blocked@example.com',
        ])->assertForbidden();

        $this->actingAs($developer)->get('/users')->assertForbidden();
        $this->assertDatabaseMissing('users', ['email' => 'blocked@example.com']);
    }

    public function test_admin_must_confirm_role_changes_with_their_current_password(): void
    {
        $user = $this->createUser($this->developerRole, 'role-target@example.com');

        $this->actingAs($this->admin)->patch("/users/{$user->id}/role", [
            'role_id' => $this->qaRole->id,
            'current_password' => 'wrong-password',
        ])->assertSessionHasErrors('current_password');

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'role_id' => $this->developerRole->id,
        ]);

        $this->actingAs($this->admin)->patch("/users/{$user->id}/role", [
            'role_id' => $this->qaRole->id,
            'current_password' => 'password123',
        ])->assertRedirect();

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'role_id' => $this->qaRole->id,
        ]);
    }

    public function test_project_management_is_limited_to_admins_and_project_leaders(): void
    {
        $developer = $this->createUser($this->developerRole, 'developer@example.com');
        $qa = $this->createUser($this->qaRole, 'qa@example.com');
        $project = $this->createProject();
        $this->actingAs($this->admin)->get("/projects/{$project->id}")
            ->assertOk()
            ->assertSee('Editar proyecto')
            ->assertSee('Eliminar proyecto');
        $payload = [
            'name' => 'Updated project',
            'description' => 'Updated description',
            'status' => 'en_progreso',
        ];

        foreach ([$developer, $qa] as $user) {
            $this->actingAs($user)->post('/projects', [
                'code' => 'DENIED-'.$user->id,
                ...$payload,
            ])->assertForbidden();

            $this->actingAs($user)->put("/projects/{$project->id}", $payload)->assertForbidden();
            $this->actingAs($user)->delete("/projects/{$project->id}")->assertForbidden();
        }

        $leader = $this->createUser($this->leaderRole, 'leader@example.com');
        $this->actingAs($leader)->put("/projects/{$project->id}", $payload)->assertRedirect();
        $this->assertDatabaseHas('projects', ['id' => $project->id, 'name' => 'Updated project']);

        $this->actingAs($leader)->delete("/projects/{$project->id}", [
            'admin_password' => 'wrong-password',
        ])->assertSessionHasErrors('admin_password');
        $this->assertDatabaseHas('projects', ['id' => $project->id]);

        $this->actingAs($leader)->delete("/projects/{$project->id}", [
            'admin_password' => 'password123',
        ])->assertRedirect(route('projects.index'));
        $this->assertDatabaseMissing('projects', ['id' => $project->id]);
    }

    public function test_developers_and_qa_can_create_update_and_delete_activities(): void
    {
        $project = $this->createProject();

        foreach ([
            [$this->createUser($this->developerRole, 'developer@example.com'), 'Developer'],
            [$this->createUser($this->qaRole, 'qa@example.com'), 'QA'],
        ] as [$user, $label]) {
            $this->actingAs($user)->get('/activities')
                ->assertOk()
                ->assertSee('Nueva Actividad');

            $activity = Activity::create([
                'project_id' => $project->id,
                'title' => 'Existing activity',
                'priority' => 'media',
                'status' => 'pendiente',
                'progress' => 0,
            ]);

            $this->actingAs($user)->post('/activities', [
                'project_id' => $project->id,
                'title' => "{$label} activity",
                'priority' => 'media',
                'status' => 'pendiente',
                'progress' => 0,
            ])->assertRedirect();

            $this->assertDatabaseHas('activities', ['title' => "{$label} activity"]);

            $this->actingAs($user)->put("/activities/{$activity->id}", [
                'title' => "{$label} updated",
                'priority' => 'media',
                'status' => 'en_progreso',
                'progress' => 50,
            ])->assertRedirect();

            $this->assertDatabaseHas('activities', ['id' => $activity->id, 'title' => "{$label} updated"]);

            $activity = Activity::create([
                'project_id' => $project->id,
                'title' => "{$label} activity to delete",
                'priority' => 'media',
                'status' => 'pendiente',
                'progress' => 0,
            ]);

            $this->actingAs($user)->delete("/activities/{$activity->id}", [
                'activity_id' => $activity->id,
                'admin_password' => 'wrong-password',
            ])->assertSessionHasErrors('admin_password');
            $this->assertDatabaseHas('activities', ['id' => $activity->id]);

            $this->actingAs($user)->delete("/activities/{$activity->id}", [
                'activity_id' => $activity->id,
                'admin_password' => 'password123',
            ])->assertRedirect();
            $this->assertDatabaseMissing('activities', ['id' => $activity->id]);
        }
    }

    private function createRole(string $name): Role
    {
        return Role::create([
            'name' => $name,
            'label' => ucfirst($name),
            'description' => "{$name} role",
        ]);
    }

    private function createUser(Role $role, string $email): User
    {
        return User::create([
            'name' => ucfirst($role->name),
            'email' => $email,
            'password' => 'password123',
            'role_id' => $role->id,
        ]);
    }

    private function createProject(): Project
    {
        return Project::create([
            'code' => 'BP-'.uniqid(),
            'name' => 'Test project',
            'status' => 'planificacion',
            'created_by' => $this->admin->id,
        ]);
    }
}
