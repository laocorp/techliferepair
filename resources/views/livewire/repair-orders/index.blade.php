<?php

use Livewire\Volt\Component;
use App\Models\RepairOrder;
use App\Models\Asset;
use Mary\Traits\Toast;
use Livewire\Attributes\Rule;
use Livewire\Attributes\Layout;

new 
#[Layout('layouts.app')]
class extends Component {
    use Toast;

    public string $search = '';
    public bool $drawer = false;

    // -- Formulario --
    #[Rule('required')]
    public ?int $asset_id = null;

    #[Rule('required|min:5')]
    public string $problem_description = '';

    #[Rule('boolean')]
    public bool $is_warranty = false;

    public function clean(): void
    {
        $this->reset(['drawer', 'asset_id', 'problem_description', 'is_warranty']);
        $this->resetValidation();
    }

public function create(): void
{
    $this->clean();
    $this->drawer = true;
}

    public function save(): void
    {
        $this->validate();

        RepairOrder::create([
            'asset_id' => $this->asset_id,
            'problem_description' => $this->problem_description,
            'is_warranty' => $this->is_warranty,
            'status' => 'recibido' // Siempre empieza en Recibido
        ]);

        $this->success('¡Orden de Trabajo creada!');
        $this->clean();
    }

    // -- Cargar Equipos para el Select (Formato: Marca Modelo - Serial) --
    public function assets(): mixed
    {
        return Asset::with('client')->get()->map(function($asset) {
            return [
                'id' => $asset->id,
                'name' => "{$asset->brand} {$asset->model} - ({$asset->serial_number}) - {$asset->client->name}"
            ];
        });
    }

    public function headers(): array
    {
        return [
            ['key' => 'id', 'label' => '#OT', 'class' => 'font-bold w-1'],
            ['key' => 'asset.client.name', 'label' => 'Cliente'],
            ['key' => 'asset.model', 'label' => 'Equipo'],
            ['key' => 'status', 'label' => 'Estado'],
            ['key' => 'created_at', 'label' => 'Fecha'],
        ];
    }

    public function with(): array
    {
        return [
            'orders' => RepairOrder::query()
                ->with(['asset.client']) // Carga relaciones anidadas
                ->orderBy('id', 'desc')
                ->get(),
            'headers' => $this->headers(),
            'assets_list' => $this->assets(),
        ];
    }
}; ?>

<div>
    <x-header title="Órdenes de Trabajo" subtitle="Control de reparaciones" separator progress-indicator>
        <x-slot:middle class="!justify-end">
            <x-input icon="o-magnifying-glass" placeholder="Buscar..." wire:model.live.debounce="search" />
        </x-slot:middle>
        <x-slot:actions>
	<x-button icon="o-plus" class="btn-primary" label="Nueva Orden" wire:click="create" />
        </x-slot:actions>
    </x-header>

    <x-card>
        <x-table :headers="$headers" :rows="$orders" striped link="/orders/{id}">
            
            {{-- Columna Personalizada: ID --}}
            @scope('cell_id', $order)
                <span class="font-black text-primary">OT-{{ str_pad($order->id, 4, '0', STR_PAD_LEFT) }}</span>
            @endscope

            {{-- Columna Personalizada: Equipo (Marca + Modelo + Serial) --}}
            @scope('cell_asset.model', $order)
                <div class="flex flex-col">
                    <span class="font-bold">{{ $order->asset->brand }} {{ $order->asset->model }}</span>
                    <span class="text-xs text-gray-500">{{ $order->asset->serial_number }}</span>
                </div>
            @endscope

            {{-- Columna Personalizada: Estado con Badge de Color --}}
            @scope('cell_status', $order)
                <x-badge :value="$order->status_label" :class="'badge-' . $order->status_color" />
                @if($order->is_warranty)
                    <x-badge value="GARANTÍA" class="badge-outline ml-2" />
                @endif
            @endscope

            {{-- Columna Personalizada: Fecha --}}
            @scope('cell_created_at', $order)
                {{ $order->created_at->format('d/m/Y H:i') }}
            @endscope

            <x-slot:empty>
                <x-icon name="o-clipboard-document-list" label="No hay órdenes activas." />
            </x-slot:empty>
        </x-table>
    </x-card>

    <x-drawer wire:model="drawer" title="Nueva Orden de Trabajo" right class="w-full lg:w-1/3">
        <x-form wire:submit="save">
            
            <x-select 
                label="Seleccionar Equipo" 
                icon="o-wrench-screwdriver" 
                :options="$assets_list" 
                wire:model="asset_id"
                placeholder="Buscar por serial o modelo..."
                searchable
            />

            <x-textarea 
                label="Descripción del Problema" 
                wire:model="problem_description" 
                placeholder="Ej. No enciende, hace ruido extraño..." 
                rows="4"
            />

            <x-checkbox label="¿Es reparación por GARANTÍA?" wire:model="is_warranty" />

            <x-slot:actions>
                <x-button label="Cancelar" wire:click="clean" />
                <x-button label="Crear Orden" class="btn-primary" type="submit" spinner="save" />
            </x-slot:actions>
        </x-form>
    </x-drawer>
</div>
