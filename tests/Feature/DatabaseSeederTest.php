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

    public function test_welcome_page_describes_arkana_with_about_us_section(): void
    {
        $response = $this->get('/');

        $response->assertOk()
            ->assertSee('ArkanA')
            ->assertSee('Qué puedes hacer')
            ->assertSee('Actualiza desde el móvil')
            ->assertSee('Sobre Nosotros')
            ->assertDontSee('Fulgaris Technologies');
    }
}
