<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DatabaseSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_default_seeder_creates_roles_without_demo_records(): void
    {
        $this->seed();

        $this->assertDatabaseCount('roles', 4);
        $this->assertDatabaseCount('users', 0);
        $this->assertDatabaseCount('projects', 0);
        $this->assertDatabaseCount('activities', 0);
    }

    public function test_welcome_page_describes_betrack_without_company_branding(): void
    {
        $response = $this->get('/');

        $response->assertOk()
            ->assertSee('Qué puedes hacer con BeTrack')
            ->assertSee('Actualiza desde el móvil')
            ->assertDontSee('Fulgaris Technologies');
    }
}
