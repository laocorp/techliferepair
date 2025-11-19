
<?php

use Livewire\Volt\Component;
use App\Models\Client;
use Mary\Traits\Toast;
use Livewire\Attributes\Rule;
use Livewire\Attributes\Layout;

new 
#[Layout('layouts.app')]
class extends Component {
    use Toast;

    public string $search = '';
    public bool $drawer = false;
    
    // Objeto Cliente para Editar (Si es null, estamos creando)
    public ?Client $my_client = null;

    #[Rule('required|min:3')]
    public string $name = '';

    #[Rule('nullable')]
    public string $tax_id = '';

    #[Rule('nullable|email')]
    public string $email = '';

    #[Rule('nullable')]
    public string $phone = '';

    // Limpiar formulario
    public function clean(): void
    {
        $this->reset(['drawer', 'my_client', 'name', 'email', 'tax_id', 'phone']);
        $this->resetValidation();
    }
    // Función para abrir el cajón de crear
	public function create(): void
	{
	    $this->clean();      // Limpia el formulario
	    $this->drawer = true; // Abre el cajón
	}

    // Cargar datos para Editar
    public function edit(Client $client): void
    {
        $this->my_client = $client; // Guardamos el cliente actual
        $this->name = $client->name;
        $this->tax_id = $client->tax_id;
        $this->email = $client->email;
        $this->phone = $client->phone;
        
        $this->drawer = true; // Abrimos el drawer
    }

    public function save(): void
    {
        $this->validate();

        if ($this->my_client) {
            // MODO EDICIÓN
            $this->my_client->update([
                'name' => $this->name,
                'tax_id' => $this->tax_id,
                'email' => $this->email,
                'phone' => $this->phone,
            ]);
            $this->success('Cliente actualizado correctamente');
        } else {
            // MODO CREACIÓN
            Client::create([
                'name' => $this->name,
                'tax_id' => $this->tax_id,
                'email' => $this->email,
                'phone' => $this->phone,
            ]);
            $this->success('Cliente creado correctamente');
        }

        $this->clean();
    }

    public function headers(): array
    {
        return [
            ['key' => 'id', 'label' => '#', 'class' => 'w-1'],
            ['key' => 'name', 'label' => 'Cliente'],
            ['key' => 'tax_id', 'label' => 'RUC/CI'],
            ['key' => 'phone', 'label' => 'Teléfono'],
            ['key' => 'email', 'label' => 'Email'],
        ];
    }

    public function with(): array
    {
        return [
            'clients' => Client::query()
                ->where('name', 'like', "%$this->search%")
                ->orWhere('tax_id', 'like', "%$this->search%")
                ->orderBy('id', 'desc')
                ->get(),
            'headers' => $this->headers()
        ];
    }
}; ?>

<div>
    <x-header title="Clientes" subtitle="Gestión de dueños de equipos" separator progress-indicator>
        <x-slot:middle class="!justify-end">
            <x-input icon="o-magnifying-glass" placeholder="Buscar..." wire:model.live.debounce="search" />
        </x-slot:middle>
        <x-slot:actions>
            <x-button icon="o-plus" class="btn-primary" label="Nuevo Cliente" wire:click="create" />
        </x-slot:actions>
    </x-header>

    <x-card>
        <x-table :headers="$headers" :rows="$clients" striped @row-click="wire:click='edit($event.detail.id)'" class="cursor-pointer">
            @scope('cell_tax_id', $client)
                <span class="text-gray-500 font-bold">{{ $client->tax_id ?? '---' }}</span>
            @endscope            
            {{-- Botón de editar explícito en la tabla (opcional pero útil) --}}
            @scope('actions', $client)
                <x-button icon="o-pencil" spinner class="btn-sm btn-ghost text-primary" wire:click="edit({{ $client->id }})" />
            @endscope
		@scope('actions', $client)
                <div class="flex gap-1">
                    <x-button icon="o-eye" link="/clients/{{ $client->id }}" class="btn-sm btn-ghost text-info" tooltip="Ver Historial" />
                    
                    <x-button icon="o-pencil" spinner class="btn-sm btn-ghost text-warning" wire:click="edit({{ $client->id }})" tooltip="Editar Datos" />
                </div>
            @endscope

            <x-slot:empty>
                <x-icon name="o-users" label="No hay clientes registrados aún." />
            </x-slot:empty>
        </x-table>
    </x-card>

    <x-drawer wire:model="drawer" title="{{ $my_client ? 'Editar Cliente' : 'Nuevo Cliente' }}" right class="w-full lg:w-1/3">
        <x-form wire:submit="save">
            <x-input label="Nombre / Razón Social" wire:model="name" icon="o-user" />
            <x-input label="RUC / Cédula" wire:model="tax_id" icon="o-identification" />
            <x-input label="Teléfono (WhatsApp)" wire:model="phone" icon="o-phone" type="tel" />
            <x-input label="Email" wire:model="email" icon="o-envelope" type="email" />
    
            <x-slot:actions>
                <x-button label="Cancelar" wire:click="clean" />
                <x-button label="Guardar" class="btn-primary" type="submit" spinner="save" />
            </x-slot:actions>
        </x-form>
    </x-drawer>
</div>
