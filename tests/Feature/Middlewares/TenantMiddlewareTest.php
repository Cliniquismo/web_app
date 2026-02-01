<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Tenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TenantMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    protected function actingAsUser(): User
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        return $user;
    }

    /** @test */
    public function user_without_tenant_is_redirected_to_tenant_create_when_accessing_dashboard()
    {
        $this->actingAsUser();

        $this->get(route('dashboard'))
            ->assertRedirect(route('tenants.create'));
    }

    /** @test */
    public function user_with_tenant_can_access_dashboard()
    {
        $this->actingAsUser();

        Tenant::factory()->create();

        $this->get(route('dashboard'))
            ->assertStatus(200);
    }

    /** @test */
    public function user_with_tenant_cannot_access_tenant_create()
    {
        $this->actingAsUser();

        Tenant::factory()->create();
        
        $this->get(route('tenants.create'))
            ->assertRedirect(route('dashboard'));
    }

    /** @test */
    public function user_without_tenant_can_access_tenant_create()
    {
        $this->actingAsUser();

        $this->get(route('tenants.create'))
            ->assertStatus(200);
    }
}
