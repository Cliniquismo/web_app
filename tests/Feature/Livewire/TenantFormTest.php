<?php

namespace Tests\Feature\Livewire;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class TenantFormTest extends TestCase
{
    use RefreshDatabase;

    protected function actingAsUser()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        return $user;
    }

    /** @test */
    public function it_renders_create_form()
    {
        $this->actingAsUser();

        Livewire::test('tenants.form')
            ->assertStatus(200);
    }

    /** @test */
    public function it_creates_a_tenant()
    {
        $this->actingAsUser();

        Livewire::test('tenants.form')
            ->set('name', 'Tenant Teste')
            ->set('email', 'tenant@test.com')
            ->set('cnpj', '12.345.678/0001-99')
            ->set('active', true)
            ->call('save')
            ->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('tenants', [
            'name'  => 'Tenant Teste',
            'email' => 'tenant@test.com',
            'cnpj'  => '12.345.678/0001-99',
            'active' => true,
        ]);
    }

    /** @test */
    public function it_loads_tenant_data_when_editing()
    {
        $this->actingAsUser();

        $tenant = Tenant::factory()->create([
            'name' => 'Tenant Antigo',
            'email' => 'old@test.com',
            'cnpj' => '11.111.111/0001-11',
            'active' => false,
        ]);

        Livewire::test('tenants.form', ['tenant' => $tenant])
            ->assertSet('name', 'Tenant Antigo')
            ->assertSet('email', 'old@test.com')
            ->assertSet('cnpj', '11.111.111/0001-11')
            ->assertSet('active', false);
    }

    /** @test */
    public function it_updates_an_existing_tenant()
    {
        $this->actingAsUser();

        $tenant = Tenant::factory()->create();

        Livewire::test('tenants.form', ['tenant' => $tenant])
            ->set('name', 'Tenant Atualizado')
            ->set('email', 'novo@test.com')
            ->set('cnpj', '22.222.222/0001-22')
            ->set('active', true)
            ->call('save')
            ->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('tenants', [
            'id'    => $tenant->id,
            'name'  => 'Tenant Atualizado',
            'email' => 'novo@test.com',
            'cnpj'  => '22.222.222/0001-22',
            'active' => true,
        ]);
    }

    /** @test */
    public function it_validates_required_fields()
    {
        $this->actingAsUser();

        Livewire::test('tenants.form')
            ->call('save')
            ->assertHasErrors([
                'name' => 'required',
                'email' => 'required',
                'cnpj' => 'required',
            ]);
    }
}
