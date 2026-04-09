<?php

namespace Tests\Feature\Auth;

use App\Models\Client;
use App\Models\ClientPortalUser;
use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClientPortalAuthTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleAndPermissionSeeder::class);
    }

    public function test_unauthenticated_user_is_redirected_to_portal_login(): void
    {
        $response = $this->get('/client');

        $response->assertRedirect('/client/login');
    }

    public function test_portal_login_page_is_accessible(): void
    {
        $response = $this->get('/client/login');

        $response->assertStatus(200);
    }

    public function test_active_portal_user_can_access_client_portal(): void
    {
        $portalUser = ClientPortalUser::factory()->create(['is_active' => true]);

        $response = $this->actingAs($portalUser, 'client_portal')->get('/client');

        $response->assertSuccessful();
    }

    public function test_inactive_portal_user_cannot_access_client_portal(): void
    {
        $portalUser = ClientPortalUser::factory()->inactive()->create();

        $response = $this->actingAs($portalUser, 'client_portal')->get('/client');

        // Filament calls canAccessPanel(), which returns false for inactive users
        $response->assertStatus(403);
    }

    public function test_team_user_cannot_access_client_portal(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/client');

        $response->assertRedirect('/client/login');
    }
}
