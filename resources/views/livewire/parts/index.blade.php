<?php

use Livewire\Volt\Component;
use App\Models\Part;
use Mary\Traits\Toast;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Rule;

new 
#[Layout('layouts.app')]
class extends Component {
    use Toast;

    public string $search = '';
    public bool $drawer = false;
    
    // Variable clave: Si tiene datos, estamos editando. Si es null, creando.
    public ?Part $my_part = null; 

    #[Rule('required|min:3')]
    public string $name = '';

    // El SKU lo validamos manualmente abajo para evitar error de duplicado al editar
    public string $sku = '';

    #[Rule('required|integer|min:0')]
    public int $stock = 0;

    #[Rule('required|numeric|min:0')]
    public float $price = 0.00;

    #[Rule('nullable|numeric|min:0')]
    public float $cost = 0.00;

    #[Rule('nullable')]
    public string $location = '';

    // Limpiar todo para cuando creamos uno nuevo
    public function clean(): void
    {
        $this->reset(['drawer', 'my_part', 'name', 'sku', 'stock', 'price', 'cost', 'location']);
        $this->resetValidation();
    }

    // CARGAR DATOS PARA EDITAR
    public function edit(Part $part): void
    {
        $this->my_part = $part; // Guardamos el repuesto que tocaste
        $this->name = $part->name;
        $this->sku = $part->sku;
        $this->stock = $part->stock;
        $this->price = $part->price;
        $this->cost = $part->cost ?? 0;
        $this->location = $part->location ?? '';
        
        $this->drawer = true; // Abrimos el cajón
    }

    public function save(): void
    {
        // Validación especial: El SKU debe ser único, PERO ignorando al producto actual si estamos editando
        $this->validate([
            'name' => 'required|min:3',
            'sku' => 'required|unique:parts,sku,' . ($this->my_part->id ?? 'NULL'),
            'stock' => 'required|integer|min:0',
            'price' => 'required|numeric|min:0',
            'cost' => 'nullable|numeric|min:0',
            'location' => 'nullable'
        ]);

        if ($this->my_part) {
            // MODO EDICIÓN (Actualizar)
            $this->my_part->update([
                'name' => $this->name,
                'sku' => $this->sku,
                'stock' => $this->stock,
                'price' => $this->price,
                'cost' => $this->cost,
                'location' => $this->location
            ]);
            $this->success('Repuesto actualizado correctamente');
        } else {
            // MODO CREACIÓN (Nuevo)
            Part::create([
                'name' => $this->name,
                'sku' => $this->sku,
                'stock' => $this->stock,
                'price' => $this->price,
                'cost' => $this->cost,
                'location' => $this->location
            ]);
            $this->success('Repuesto creado correctamente');
        }

        $this->clean(); // Cerrar y limpiar
    }

    public function headers(): array
    {
        return [
            ['key' => 'sku', 'label' => 'SKU', 'class' => 'font-bold w-1'],
            ['key' => 'name', 'label' => 'Producto'],
            ['key' => 'stock', 'label' => 'Stock', 'class' => 'text-center'],
            ['key' => 'price', 'label' => 'Precio', 'class' => 'text-right'],
            ['key' => 'location', 'label' => 'Ubicación'],
        ];
    }

    public function with(): array
    {
        return [
            'parts' => Part::query()
                ->where('name', 'like', "%$this->search%")
                ->orWhere('sku', 'like', "%$this->search%")
                ->orderBy('name')
                ->get(),
            'headers' => $this->headers()
        ];
    }
}; ?>

<div>
    <x-header title="Inventario" subtitle="Control de repuestos" separator progress-indicator>
        <x-slot:middle class="!justify-end">
            <x-input icon="o-magnifying-glass" placeholder="Buscar..." wire:model.live.debounce="search" />
        </x-slot:middle>
        <x-slot:actions>
            <x-button icon="o-plus" class="btn-primary" label="Nuevo" wire:click="clean; $toggle('drawer')" />
        </x-slot:actions>
    </x-header>

    <x-card>
        <x-table :headers="$headers" :rows="$parts" striped @row-click="$wire.edit($event.detail.id)" class="cursor-pointer">    
            @scope('cell_stock', $part)
                @if($part->stock <= $part->stock_min)
                    <x-badge :value="$part->stock" class="bg-red-600 text-white font-bold border-none" />
                @else
                    <x-badge :value="$part->stock" class="badge-success font-bold" />
                @endif
            @endscope

            @scope('cell_price', $part)
                $ {{ number_format($part->price, 2) }}
            @endscope

            <x-slot:empty>
                <x-icon name="o-cube" label="Inventario vacío." />
            </x-slot:empty>
        </x-table>
    </x-card>

    <x-drawer wire:model="drawer" title="{{ $my_part ? 'Editar Repuesto' : 'Nuevo Repuesto' }}" right class="w-full lg:w-1/3">
        <x-form wire:submit="save">
            
            <div class="grid grid-cols-2 gap-4">
                <x-input label="Código SKU" wire:model="sku" icon="o-qr-code" />
                <x-input label="Ubicación" wire:model="location" icon="o-map-pin" />
            </div>

            <x-input label="Nombre del Repuesto" wire:model="name" icon="o-tag" />

            <div class="grid grid-cols-2 gap-4">
                <x-input label="Stock Actual" wire:model="stock" type="number" />
                <x-input label="Precio Venta ($)" wire:model="price" type="number" step="0.01" prefix="$" />
            </div>
            
            <x-input label="Costo Compra ($)" wire:model="cost" type="number" step="0.01" prefix="$" hint="Solo visible para admin" />

            <x-slot:actions>
                <x-button label="Cancelar" wire:click="clean" />
                <x-button label="Guardar" class="btn-primary" type="submit" spinner="save" />
            </x-slot:actions>
        </x-form>
    </x-drawer>
</div>
