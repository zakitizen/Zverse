<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleDashboardMenuTest extends TestCase
{
    use RefreshDatabase;

    public function test_pewarta_sees_dashboard_link_in_navbar(): void
    {
        $user = User::create([
            'username' => 'reporter3',
            'display_name' => 'Reporter Tiga',
            'password' => bcrypt('secret123'),
            'role' => 'pewarta',
        ]);

        $this->actingAs($user);

        $response = $this->get('/')->assertOk();
        $html = $response->getContent();

        $this->assertStringContainsString('Dashboard Pewarta', $html);
    }

    public function test_redaksi_sees_dashboard_link_in_navbar(): void
    {
        $user = User::create([
            'username' => 'editor1',
            'display_name' => 'Editor Satu',
            'password' => bcrypt('secret123'),
            'role' => 'redaksi',
        ]);

        $this->actingAs($user);

        $response = $this->get('/')->assertOk();
        $html = $response->getContent();

        $this->assertStringContainsString('Dashboard Redaksi', $html);
    }
}
