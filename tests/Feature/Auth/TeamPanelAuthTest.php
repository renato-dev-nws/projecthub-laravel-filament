<?php

namespace Tests\Feature\Auth;

use App\Models\ClientPortalUser;
use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TeamPanelAuthTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleAndPermissionSeeder::class);
    }

    public function test_unauthenticated_user_is_redirected_to_login(): void
    {
        $response = $this->get('/admin');

        $response->assertRedirect('/admin/login');
    }

    public function test_login_page_is_accessible(): void
    {
        $response = $this->get('/admin/login');

        $response->assertStatus(200);
    }

    public function test_authenticated_admin_can_access_panel(): void
    {
        $user = User::factory()->create();
        $user->assignRole('Admin');

        $response = $this->actingAs($user)->get('/admin');

        $response->assertSuccessful();
    }

    public function test_authenticated_project_manager_can_access_panel(): void
    {
        $user = User::factory()->create();
        $user->assignRole('Project Manager');

        $response = $this->actingAs($user)->get('/admin');

        $response->assertSuccessful();
    }

    public function test_client_portal_user_cannot_access_team_panel(): void
    {
        $portalUser = ClientPortalUser::factory()->create();

        // Portal user is not a web-guard user, so guard mismatch
        $response = $this->actingAs($portalUser, 'client_portal')->get('/admin');

        $response->assertRedirect('/admin/login');
    }
}
