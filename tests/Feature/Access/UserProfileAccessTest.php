<?php

namespace Tests\Feature\Access;

use App\Filament\TeamPanel\Pages\UserProfile;
use App\Filament\TeamPanel\Resources\Users\UserResource;
use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserProfileAccessTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleAndPermissionSeeder::class);
    }

    public function test_project_manager_can_open_user_profile_but_cannot_open_users_index(): void
    {
        $viewer = User::factory()->create();
        $viewer->assignRole('Project Manager');

        $target = User::factory()->create();

        $profileResponse = $this->actingAs($viewer)
            ->get(UserProfile::getUrl(['user' => $target->id], panel: 'admin'));

        $indexResponse = $this->actingAs($viewer)
            ->get(UserResource::getUrl('index', panel: 'admin'));

        $profileResponse->assertOk();
        $indexResponse->assertForbidden();
    }

    public function test_admin_can_open_users_index(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('Admin');

        $response = $this->actingAs($admin)
            ->get(UserResource::getUrl('index', panel: 'admin'));

        $response->assertOk();
    }
}
