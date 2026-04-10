<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RoleAndPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            'module.dashboard',
            'module.projects',
            'module.tasks',
            'module.crm',
            'module.support',
            'module.finance',
            'module.settings',
            'module.access',
            'projects.view_any',
            'projects.view_all',
            'projects.create',
            'projects.update',
            'projects.delete',
            'tasks.view_any',
            'tasks.update_status',
            'clients.view_any',
            'clients.create',
            'clients.update',
            'clients.delete',
            'leads.view_any',
            'leads.create',
            'leads.update',
            'leads.delete',
            'quotes.view_any',
            'quotes.create',
            'quotes.update',
            'quotes.delete',
            'support.view_any',
            'support.create',
            'support.update',
            'support.delete',
            'financial.view_any',
            'financial.create',
            'financial.update',
            'financial.delete',
            'settings.view_any',
            'settings.update',
            'users.view_any',
            'users.create',
            'users.update',
            'users.delete',
            'roles.view_any',
            'roles.create',
            'roles.update',
            'roles.delete',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        $roles = [
            'Super Admin' => $permissions,
            'Admin' => $permissions,
            'Project Manager' => [
                'module.dashboard',
                'module.projects',
                'module.tasks',
                'module.crm',
                'module.support',
                'projects.view_any',
                'projects.create',
                'projects.update',
                'projects.delete',
                'tasks.view_any',
                'tasks.update_status',
                'clients.view_any',
                'leads.view_any',
                'quotes.view_any',
                'support.view_any',
                'support.create',
                'support.update',
            ],
            'Developer' => [
                'module.dashboard',
                'module.projects',
                'module.tasks',
                'module.support',
                'projects.view_any',
                'tasks.view_any',
                'tasks.update_status',
                'support.view_any',
                'support.create',
                'support.update',
            ],
            'Designer' => [
                'module.dashboard',
                'module.projects',
                'module.tasks',
                'module.support',
                'projects.view_any',
                'tasks.view_any',
                'tasks.update_status',
                'support.view_any',
                'support.create',
                'support.update',
            ],
            'Account Manager' => [
                'module.dashboard',
                'module.projects',
                'module.crm',
                'module.support',
                'projects.view_any',
                'projects.view_all',
                'clients.view_any',
                'clients.create',
                'clients.update',
                'leads.view_any',
                'leads.create',
                'leads.update',
                'quotes.view_any',
                'quotes.create',
                'quotes.update',
                'support.view_any',
                'support.create',
                'support.update',
            ],
            'Financial' => [
                'module.dashboard',
                'module.projects',
                'module.finance',
                'projects.view_any',
                'projects.view_all',
                'financial.view_any',
                'financial.create',
                'financial.update',
                'financial.delete',
            ],
        ];

        foreach ($roles as $name => $rolePermissions) {
            $role = Role::firstOrCreate(['name' => $name, 'guard_name' => 'web']);
            $role->syncPermissions($rolePermissions);
        }

        app()[PermissionRegistrar::class]->forgetCachedPermissions();
    }
}
