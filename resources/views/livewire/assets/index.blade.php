<?php

use Livewire\Volt\Component;
use App\Models\Asset;
use App\Models\Client; // Necesitamos cargar los clientes
use Mary\Traits\Toast;
use Livewire\Attributes\Rule;
use Livewire\Attributes\Layout;

new 
#[Layout('layouts.app')]
class extends Component {
    use Toast;

    public string $search = '';
    public bool $drawer = false;

    // -- Variables del Formulario --
    #[Rule('required')]
    public ?int $client_id = null; // El dueño seleccionado

    #[Rule('required')]
    public string $brand = '';

    #[Rule('required')]
    public string $model = '';

    #[Rule('required|unique:assets,serial_number')] // Validación única
    public string $serial_number = '';

    #[Rule('nullable')]
    public string $type = '';

    public function clean(): void
    {
        $this->reset(['drawer', 'client_id', 'brand', 'model', 'serial_number', 'type']);
        $this->resetValidation();
    }

    public function save(): void
    {
        $this->validate();

        Asset::create([
            'client_id' => $this->client_id,
            'brand' => $this->brand,
            'model' => $this->model,
            'serial_number' => $this->serial_number,
            'type' => $this->type,
        ]);

        $this->success('¡Equipo registrado correctamente!');
        $this->clean();
    }

    // -- Cargar Clientes para el Select --
    public function clients(): mixed
    {
        return Client::orderBy('name')->get();
    }

    public function headers(): array
    {
        return [
            ['key' => 'id', 'label' => '#', 'class' => 'w-1'],
            ['key' => 'serial_number', 'label' => 'Serial', 'class' => 'font-bold'],
            ['key' => 'brand', 'label' => 'Marca'],
            ['key' => 'model', 'label' => 'Modelo'],
            ['key' => 'client.name', 'label' => 'Dueño'], // Relación automática
        ];
    }

    public function with(): array
    {
        return [
            'assets' => Asset::query()
                ->with('client') // Carga inteligente para no lentear la BD
                ->where('serial_number', 'like', "%$this->search%")
                ->orWhere('brand', 'like', "%$this->search%")
                ->orderBy('id', 'desc')
                ->get(),
            'headers' => $this->headers(),
            'clients_list' => $this->clients(), // Enviamos la lista de clientes
        ];
    }
}; ?>

<div>
    <x-header title="Equipos / Activos" subtitle="Inventario de máquinas de clientes" separator progress-indicator>
        <x-slot:middle class="!justify-end">
            <x-input icon="o-magnifying-glass" placeholder="Buscar por Serial o Marca..." wire:model.live.debounce="search" />
        </x-slot:middle>
        <x-slot:actions>
            <x-button icon="o-plus" class="btn-primary" label="Registrar Equipo" wire:click="$toggle('drawer')" />
        </x-slot:actions>
    </x-header>

    <x-card>
        <x-table :headers="$headers" :rows="$assets" striped>
            {{-- Decoración para la Marca --}}
            @scope('cell_brand', $asset)
                <x-badge :value="$asset->brand" class="badge-ghost" />
            @endscope

            <x-slot:empty>
                <x-icon name="o-cube" label="No hay equipos registrados." />
            </x-slot:empty>
        </x-table>
    </x-card>

    <x-drawer wire:model="drawer" title="Registrar Equipo" right class="w-full lg:w-1/3">
        <x-form wire:submit="save">
            
            <x-select 
                label="Dueño del Equipo" 
                icon="o-user" 
                :options="$clients_list" 
                wire:model="client_id"
                placeholder="Seleccione un cliente..."
            />

            <div class="grid grid-cols-2 gap-4">
                <x-input label="Marca" wire:model="brand" placeholder="Ej. Stihl" icon="o-tag" />
                <x-input label="Modelo" wire:model="model" placeholder="Ej. MS-382" />
            </div>

            <x-input label="Número de Serie (Obligatorio)" wire:model="serial_number" icon="o-qr-code" />
            <x-input label="Tipo de Equipo" wire:model="type" placeholder="Ej. Motosierra" />

            <x-slot:actions>
                <x-button label="Cancelar" wire:click="clean" />
                <x-button label="Guardar" class="btn-primary" type="submit" spinner="save" />
            </x-slot:actions>
        </x-form>
    </x-drawer>
</div>
