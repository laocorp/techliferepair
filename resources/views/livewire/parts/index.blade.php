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
    public ?Part $my_part = null;

    #[Rule('required|min:3')] public string $name = '';
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
        $this->validate([
            'name' => 'required',
            'sku' => 'required|unique:parts,sku,' . ($this->my_part->id ?? 'NULL'),
            'stock' => 'required',
            'price' => 'required'
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
            $this->success('Repuesto actualizado');
        } else {
            Part::create($data);
            $this->success('Repuesto creado');
        }
        $this->drawer = false;
    }

    public function with(): array {
        return [
            'parts' => Part::query()->where('name', 'like', "%$this->search%")->get(),
            'headers' => [
                ['key' => 'sku', 'label' => 'SKU'],
                ['key' => 'name', 'label' => 'Producto'],
                ['key' => 'stock', 'label' => 'Stock'],
                ['key' => 'price', 'label' => 'Precio']
            ]
        ];
    }
}; ?>

<div>
    <x-header title="Inventario" separator>
        <x-slot:actions>
            <x-button icon="o-plus" class="btn-primary" label="Nuevo Repuesto" wire:click="create" />
        </x-slot:actions>
    </x-header>

    <x-card>
        <x-table :headers="$headers" :rows="$parts" striped @row-click="$wire.edit($event.detail.id)" class="cursor-pointer">
            @scope('cell_price', $part) ${{ $part->price }} @endscope
            <x-slot:empty><x-icon name="o-cube" label="Vacío" /></x-slot:empty>
        </x-table>
    </x-card>

    <x-drawer wire:model="drawer" title="{{ $my_part ? 'Editar' : 'Nuevo' }}" right class="w-full lg:w-1/3">
        <x-form wire:submit="save">
            <x-input label="SKU" wire:model="sku" />
            <x-input label="Nombre" wire:model="name" />
            <x-input label="Stock" wire:model="stock" type="number" />
            <x-input label="Precio" wire:model="price" type="number" step="0.01" />
            @if(auth()->user()->isAdmin())
                <x-input label="Costo" wire:model="cost" type="number" step="0.01" />
            @endif
            <x-input label="Ubicación" wire:model="location" />
            <x-slot:actions>
                <x-button label="Cancelar" wire:click="$toggle('drawer')" />
                <x-button label="Guardar" class="btn-primary" type="submit" spinner="save" />
            </x-slot:actions>
        </x-form>
    </x-drawer>
</div>
