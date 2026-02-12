<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Tenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TenantMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(\Database\Seeders\RolesAndPermissionsSeeder::class);
    }

    protected function actingAsUser(string $role = 'admin'): User
    {
        $user = User::factory()->create();
        $user->assignRole($role);

        $this->actingAs($user);

        return $user;
    }

    /** @test */
    public function admin_without_tenant_is_redirected_to_tenant_create_when_accessing_dashboard()
    {
        $this->actingAsUser('admin');

        $this->get(route('dashboard'))
            ->assertRedirect(route('tenants.create'));
    }

    /** @test */
    public function admin_with_tenant_can_access_dashboard()
    {
        $this->actingAsUser('admin');

        Tenant::factory()->create();

        $this->get(route('dashboard'))
            ->assertStatus(200);
    }

    /** @test */
    public function admin_with_tenant_cannot_access_tenant_create()
    {
        $this->actingAsUser('admin');

        Tenant::factory()->create();

        $this->get(route('tenants.create'))
            ->assertRedirect(route('dashboard'));
    }

    /** @test */
    public function admin_without_tenant_can_access_tenant_create()
    {
        $this->actingAsUser('admin');

        $this->get(route('tenants.create'))
            ->assertStatus(200);
    }
}
