<?php

use App\Models\Tenant;
use Livewire\Component;

new class extends Component {
    public ?Tenant $tenant = null;

    public ?string $name = null;
    public ?string $email = null;
    public ?string $cnpj = null;
    public bool $active = false;

    public function mount(?Tenant $tenant = null)
    {
        if (isset($tenant->id)) {
            $this->tenant = $tenant;

            $this->fill([
                'name' => $tenant->name,
                'email' => $tenant->email,
                'cnpj' => $tenant->cnpj,
                'active' => $tenant->active ?? false,
            ]);
        }
    }

    protected function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'cnpj' => 'required|string|max:18',
            'active' => 'boolean',
        ];
    }

    public function save()
    {
        $this->validate();

        Tenant::updateOrCreate(
            ['id' => $this->tenant?->id],
            [
                'name' => $this->name,
                'email' => $this->email,
                'cnpj' => $this->cnpj,
                'active' => $this->active,
            ],
        );

        session()->flash('status', 'Tenant salvo com sucesso.');

        return redirect()->route('dashboard');
    }
};
?>

<div>
    <div class="flex flex-col gap-6">
        <x-auth-header :title="isset($tenant->id) ? __('Editar tenant') : __('Vamos começar')" :description="__('Adicione as informações do tenant')" />

        <x-auth-session-status class="text-center" :status="session('status')" />

        <form wire:submit.prevent="save" class="flex flex-col gap-6">

            <flux:input wire:model.defer="name" :label="__('Nome')" type="text" required autofocus
                placeholder="Nome do tenant" />
            <flux:error name="name" />


            <flux:input wire:model.defer="email" :label="__('Email')" type="email" required
                placeholder="Email do tenant" />
            <flux:error name="email" />


            <div x-data="{
                value: '{{ $cnpj }}',
                mask(v) {
                    return v
                        .replace(/\D/g, '')
                        .replace(/^(\d{2})(\d)/, '$1.$2')
                        .replace(/^(\d{2})\.(\d{3})(\d)/, '$1.$2.$3')
                        .replace(/\.(\d{3})(\d)/, '.$1/$2')
                        .replace(/(\d{4})(\d)/, '$1-$2')
                        .slice(0, 18);
                }
            }" x-init="value = mask(value || '');
            $wire.set('cnpj', value);" wire:ignore>
                <flux:input :label="__('CNPJ')" type="text" required placeholder="00.000.000/0000-00"
                    x-model="value"
                    x-on:input="
            value = mask(value);
            $wire.set('cnpj', value);
        " />
                <flux:error name="cnpj" />
            </div>


            <flux:field variant="inline">
                <flux:checkbox wire:model="active" />
                <flux:label>Ativo</flux:label>
                <flux:error name="active" />
            </flux:field>

            <div class="flex items-center justify-end">
                <flux:button variant="primary" type="submit" class="w-full">
                    {{ __('Salvar') }}
                </flux:button>
            </div>
        </form>

        <div class="text-sm text-center text-zinc-600 dark:text-zinc-400">
            <flux:link :href="route('dashboard')" wire:navigate>
                {{ __('Voltar') }}
            </flux:link>
        </div>
    </div>
</div>
