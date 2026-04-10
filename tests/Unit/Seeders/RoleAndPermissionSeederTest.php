<?php

namespace Tests\Unit\Seeders;

use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class RoleAndPermissionSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_roles_receive_expected_permissions(): void
    {
        $this->seed(RoleAndPermissionSeeder::class);

        $projectManager = Role::findByName('Project Manager');
        $financial = Role::findByName('Financial');
        $designer = Role::findByName('Designer');

        $this->assertTrue($projectManager->hasPermissionTo('projects.create'));
        $this->assertTrue($projectManager->hasPermissionTo('projects.update'));
        $this->assertTrue($projectManager->hasPermissionTo('projects.delete'));
        $this->assertFalse($projectManager->hasPermissionTo('financial.view_any'));

        $this->assertTrue($financial->hasPermissionTo('projects.view_any'));
        $this->assertTrue($financial->hasPermissionTo('projects.view_all'));
        $this->assertFalse($financial->hasPermissionTo('projects.create'));
        $this->assertFalse($financial->hasPermissionTo('projects.update'));

        $this->assertFalse($designer->hasPermissionTo('module.finance'));
    }
}
