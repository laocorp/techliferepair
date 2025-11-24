<?php

use Livewire\Volt\Component;
use App\Models\Part;
use Mary\Traits\Toast;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Rule;
use Illuminate\Validation\Rule as ValidationRule; // <--- IMPORTANTE: Agregar esto

new 
#[Layout('layouts.app')]
class extends Component {
    use Toast;

    public string $search = '';
    public bool $drawer = false;
    public ?Part $my_part = null;

    #[Rule('required|min:3')] public string $name = '';
    // Quitamos la regla simple de aquí para validarla manualmente abajo
    public string $sku = ''; 
    #[Rule('required|integer|min:0')] public int $stock = 0;
    #[Rule('required|numeric|min:0')] public float $price = 0;
    #[Rule('nullable')] public float $cost = 0;
    #[Rule('nullable')] public string $location = '';

    public function create(): void {
        $this->reset(['drawer', 'my_part', 'name', 'sku', 'stock', 'price', 'cost', 'location']);
        $this->resetValidation();
        $this->drawer = true;
    }

    public function edit(Part $part): void {
        $this->my_part = $part;
        $this->name = $part->name;
        $this->sku = $part->sku;
        $this->stock = $part->stock;
        $this->price = $part->price;
        $this->cost = $part->cost ?? 0;
        $this->location = $part->location ?? '';
        $this->drawer = true;
    }

    public function save(): void {
        // Validación Multitenant Inteligente
        $this->validate([
            'name' => 'required|min:3',
            'stock' => 'required|integer|min:0',
            'price' => 'required|numeric|min:0',
            'sku' => [
                'required',
                // Validar que el SKU sea único SOLO dentro de mi empresa
                ValidationRule::unique('parts')
                    ->where('company_id', auth()->user()->company_id)
                    ->ignore($this->my_part->id ?? null)
            ]
        ]);

        $data = [
            'name' => $this->name,
            'sku' => $this->sku,
            'stock' => $this->stock,
            'price' => $this->price,
            'cost' => $this->cost,
            'location' => $this->location
        ];

        if ($this->my_part) {
            $this->my_part->update($data);
            $this->success('Producto actualizado');
        } else {
            Part::create($data);
            $this->success('Producto creado');
        }
        $this->drawer = false;
    }

    public function with(): array {
        return [
            'parts' => Part::query()
                ->where('name', 'like', "%$this->search%")
                ->orWhere('sku', 'like', "%$this->search%")
                ->orderBy('name')
                ->get(),
            'headers' => [
                ['key' => 'sku', 'label' => 'SKU', 'class' => 'font-mono text-xs w-24'],
                ['key' => 'name', 'label' => 'Producto'],
                ['key' => 'stock', 'label' => 'Stock', 'class' => 'text-center'],
                ['key' => 'price', 'label' => 'PVP', 'class' => 'text-right'],
                ['key' => 'location', 'label' => 'Ubicación', 'class' => 'text-right text-xs text-slate-400'],
            ]
        ];
    }
}; ?>

<div>
    <x-header title="Inventario" subtitle="Catálogo de productos y repuestos" separator>
        <x-slot:middle class="!justify-end">
            <x-input icon="o-magnifying-glass" placeholder="Buscar por nombre o SKU..." wire:model.live.debounce="search" class="w-72" />
        </x-slot:middle>
        <x-slot:actions>
            <x-button label="Nuevo Producto" icon="o-plus" class="btn-primary" wire:click="create" />
        </x-slot:actions>
    </x-header>

    <x-card>
        <x-table :headers="$headers" :rows="$parts" striped @row-click="$wire.edit($event.detail.id)" class="cursor-pointer hover:bg-slate-50">
            
            @scope('cell_sku', $part)
                <span class="bg-slate-100 text-slate-600 px-2 py-1 rounded font-bold">{{ $part->sku }}</span>
            @endscope

            @scope('cell_name', $part)
                <div class="font-bold text-slate-800">{{ $part->name }}</div>
            @endscope

            @scope('cell_stock', $part)
                @if($part->stock <= 5)
                    <span class="text-red-600 font-black bg-red-50 px-2 py-1 rounded border border-red-100">{{ $part->stock }}</span>
                @else
                    <span class="text-slate-700 font-bold">{{ $part->stock }}</span>
                @endif
            @endscope

            @scope('cell_price', $part)
                <span class="text-slate-900 font-bold">${{ number_format($part->price, 2) }}</span>
            @endscope

            <x-slot:empty>
                <div class="py-12 flex flex-col items-center justify-center text-slate-400">
                    <x-icon name="o-archive-box" class="w-12 h-12 mb-2 opacity-20" />
                    <div class="font-medium">Inventario vacío</div>
                </div>
            </x-slot:empty>
        </x-table>
    </x-card>

    <x-drawer wire:model="drawer" title="{{ $my_part ? 'Editar Producto' : 'Nuevo Producto' }}" right class="w-full lg:w-1/3">
        <x-form wire:submit="save" class="space-y-4">
            
            <div class="grid grid-cols-2 gap-4">
                <x-input label="Código SKU" wire:model="sku" icon="o-qr-code" />
                <x-input label="Ubicación" wire:model="location" icon="o-map-pin" placeholder="Ej. A-12" />
            </div>

            <x-input label="Nombre del Producto" wire:model="name" icon="o-tag" />
            
            <div class="grid grid-cols-2 gap-4">
                <x-input label="Stock Inicial" wire:model="stock" type="number" />
                <x-input label="Precio Venta ($)" wire:model="price" type="number" step="0.01" prefix="$" class="font-bold" />
            </div>

            @if(auth()->user()->isAdmin())
                <div class="bg-slate-50 p-3 rounded-lg border border-slate-200">
                    <x-input label="Costo de Compra ($)" wire:model="cost" type="number" step="0.01" prefix="$" hint="Solo visible para administradores" />
                </div>
            @endif
            
            <x-slot:actions>
                <x-button label="Cancelar" wire:click="$toggle('drawer')" />
                <x-button label="Guardar Producto" class="btn-primary" type="submit" spinner="save" />
            </x-slot:actions>
        </x-form>
    </x-drawer>
</div>
