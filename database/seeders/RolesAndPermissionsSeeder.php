<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Permissions
        $permissions = [
            'manage users',
            'manage schedules',
            'manage clinics',
            'view patients',
            'edit medical records',
            'view own appointments',
            'create appointments',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Roles
        $admin = Role::firstOrCreate(['name' => 'admin']);
        $professional = Role::firstOrCreate(['name' => 'professional']);
        $patient = Role::firstOrCreate(['name' => 'patient']);

        // Assign permissions
        $admin->givePermissionTo(Permission::all());

        $professional->givePermissionTo([
            'manage schedules',
            'view patients',
            'edit medical records',
        ]);

        $patient->givePermissionTo([
            'view own appointments',
            'create appointments',
        ]);
    }
}
