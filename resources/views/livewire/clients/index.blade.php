<?php

use Livewire\Volt\Component;
use App\Models\Client;
use Mary\Traits\Toast;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Rule;

new 
#[Layout('layouts.app')]
class extends Component {
    use Toast;

    public string $search = '';
    public bool $drawer = false;
    public ?Client $my_client = null;

    #[Rule('required|min:3')] public string $name = '';
    #[Rule('nullable')] public string $tax_id = '';
    #[Rule('nullable|email')] public string $email = '';
    #[Rule('nullable')] public string $phone = '';

    // FUNCIÓN DE CREAR
    public function create(): void {
        $this->reset(['drawer', 'my_client', 'name', 'email', 'tax_id', 'phone']);
        $this->resetValidation();
        $this->drawer = true;
    }

    // FUNCIÓN DE EDITAR
    public function edit(Client $client): void {
        $this->my_client = $client;
        $this->name = $client->name;
        $this->tax_id = $client->tax_id;
        $this->email = $client->email;
        $this->phone = $client->phone;
        $this->drawer = true;
    }

    // FUNCIÓN DE ELIMINAR (NUEVA)
    public function delete(Client $client): void {
        // Opcional: Podrías verificar si tiene órdenes activas antes de borrar
        $client->delete();
        $this->success('Cliente eliminado correctamente');
    }

    public function save(): void {
        $this->validate();

        if ($this->my_client) {
            $this->my_client->update([
                'name' => $this->name,
                'tax_id' => $this->tax_id,
                'email' => $this->email,
                'phone' => $this->phone,
            ]);
            $this->success('Cliente actualizado');
        } else {
            Client::create([
                'name' => $this->name,
                'tax_id' => $this->tax_id,
                'email' => $this->email,
                'phone' => $this->phone,
            ]);
            $this->success('Cliente creado');
        }
        $this->drawer = false;
    }

    public function headers(): array {
        return [
            ['key' => 'name', 'label' => 'Cliente'],
            ['key' => 'tax_id', 'label' => 'ID/RUC'],
            ['key' => 'phone', 'label' => 'Teléfono'],
            ['key' => 'email', 'label' => 'Email'],
        ];
    }

    public function with(): array {
        return [
            'clients' => Client::query()
                ->where('name', 'like', "%$this->search%")
                ->orderBy('id', 'desc')
                ->get(),
            'headers' => $this->headers()
        ];
    }
}; ?>

<div>
    <x-header title="Clientes" subtitle="Gestión de dueños de equipos" separator>
        <x-slot:middle class="!justify-end">
            <x-input icon="o-magnifying-glass" placeholder="Buscar..." wire:model.live.debounce="search" />
        </x-slot:middle>
        <x-slot:actions>
            <x-button icon="o-plus" class="btn-primary" label="Nuevo Cliente" wire:click="create" />
        </x-slot:actions>
    </x-header>

    <x-card>
        <x-table :headers="$headers" :rows="$clients" striped @row-click="$wire.edit($event.detail.id)" class="cursor-pointer">
            
            {{-- ACCIONES --}}
            @scope('actions', $client)
                <div class="flex gap-1" onclick="event.stopPropagation()"> {{-- stopPropagation evita que se abra el drawer de editar al hacer clic en borrar --}}
                    
                    <!-- Ver Perfil -->
                    <x-button icon="o-eye" link="/clients/{{ $client->id }}" class="btn-sm btn-ghost text-info" tooltip="Ver Historial" />
                    
                    <!-- Editar -->
                    <x-button icon="o-pencil" class="btn-sm btn-ghost text-warning" wire:click="edit({{ $client->id }})" tooltip="Editar" />
                    
                    <!-- Eliminar (NUEVO) -->
                    <x-button 
                        icon="o-trash" 
                        class="btn-sm btn-ghost text-error" 
                        wire:click="delete({{ $client->id }})" 
                        confirm="¿Estás seguro de eliminar a {{ $client->name }}? Se borrarán sus datos."
                        tooltip="Eliminar" 
                    />
                </div>
            @endscope

            <x-slot:empty>
                <x-icon name="o-users" label="No hay clientes registrados." />
            </x-slot:empty>
        </x-table>
    </x-card>

    <x-drawer wire:model="drawer" title="{{ $my_client ? 'Editar Cliente' : 'Nuevo Cliente' }}" right class="w-full lg:w-1/3">
        <x-form wire:submit="save">
            <x-input label="Nombre / Razón Social" wire:model="name" icon="o-user" />
            <x-input label="RUC / Cédula" wire:model="tax_id" icon="o-identification" />
            <x-input label="Teléfono" wire:model="phone" icon="o-phone" />
            <x-input label="Email" wire:model="email" icon="o-envelope" />
            <x-slot:actions>
                <x-button label="Cancelar" wire:click="$toggle('drawer')" />
                <x-button label="Guardar" class="btn-primary" type="submit" spinner="save" />
            </x-slot:actions>
        </x-form>
    </x-drawer>
</div>
