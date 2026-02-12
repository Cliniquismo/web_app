<?php

namespace Tests\Feature\Roles;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class RolesAndPermissionsTest extends TestCase
{
    use RefreshDatabase;

    public function test_roles_and_permissions_are_seeded_correctly()
    {
        $this->seed(\Database\Seeders\RolesAndPermissionsSeeder::class);

        // Roles existem
        $this->assertTrue(Role::where('name', 'admin')->exists());
        $this->assertTrue(Role::where('name', 'professional')->exists());
        $this->assertTrue(Role::where('name', 'patient')->exists());

        // Permissões existem
        $this->assertTrue(Permission::where('name', 'manage users')->exists());

        // Permissões vinculadas corretamente
        $admin = Role::findByName('admin');
        $this->assertTrue($admin->hasPermissionTo('manage users'));

        $patient = Role::findByName('patient');
        $this->assertFalse($patient->hasPermissionTo('manage users'));
    }

    public function test_patient_cannot_manage_users()
    {
        $this->seed(\Database\Seeders\RolesAndPermissionsSeeder::class);

        $user = \App\Models\User::factory()->create();
        $user->assignRole('patient');

        $this->actingAs($user);

        $this->assertFalse(auth()->user()->can('manage users'));
    }
}
