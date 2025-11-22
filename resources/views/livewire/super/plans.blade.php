<?php

use Livewire\Volt\Component;
use App\Models\Plan;
use Mary\Traits\Toast;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Rule;

new 
#[Layout('layouts.app')]
class extends Component {
    use Toast;

    public bool $drawer = false;
    public ?Plan $my_plan = null;

    #[Rule('required')] public string $name = '';
    #[Rule('required|numeric|min:0')] public float $price = 0;
    #[Rule('required|integer|min:1')] public int $max_users = 1;
    #[Rule('required|integer|min:1')] public int $max_orders = 50;

    public function mount() {
        if (!auth()->user()->is_super_admin) {
            abort(403, 'Acceso exclusivo para el dueño del SaaS.');
        }
    }

    public function edit(Plan $plan): void {
        $this->my_plan = $plan;
        $this->fill($plan);
        $this->drawer = true;
    }

    // ESTA ES LA FUNCIÓN QUE ABRE EL DRAWER PARA CREAR
    public function clean(): void {
        $this->reset(['my_plan', 'name', 'price', 'max_users', 'max_orders']);
        $this->resetValidation();
        $this->drawer = true; // <--- ABRE EL DRAWER
    }

    public function save(): void {
        $this->validate();
        
        if ($this->my_plan) {
            $this->my_plan->update($this->all());
            $this->success('Plan actualizado correctamente');
        } else {
            Plan::create($this->all());
            $this->success('Nuevo plan creado');
        }
        
        $this->drawer = false;
    }

    public function with(): array {
        return [
            'plans' => Plan::all(),
            'headers' => [
                ['key' => 'id', 'label' => '#'],
                ['key' => 'name', 'label' => 'Nombre del Plan'],
                ['key' => 'price', 'label' => 'Precio Mensual', 'class' => 'text-right font-bold'],
                ['key' => 'max_users', 'label' => 'Usuarios', 'class' => 'text-center'],
                ['key' => 'max_orders', 'label' => 'Órdenes/Mes', 'class' => 'text-center'],
            ]
        ];
    }
}; ?>

<div>
    <x-header title="Planes de Suscripción" subtitle="Define tu modelo de negocio" separator>
        <x-slot:actions>
            {{-- Botón corregido: Llama a clean() --}}
            <x-button label="Nuevo Plan" icon="o-plus" class="btn-primary" wire:click="clean" />
        </x-slot:actions>
    </x-header>

    <div class="grid gap-6">
        <x-card>
            <x-table :headers="$headers" :rows="$plans" striped @row-click="$wire.edit($event.detail.id)" class="cursor-pointer">
                
                @scope('cell_price', $plan)
                    <span class="text-slate-900 text-lg">${{ number_format($plan->price, 2) }}</span>
                @endscope

                @scope('cell_max_users', $plan)
                    <span class="badge badge-ghost">{{ $plan->max_users }}</span>
                @endscope

                @scope('cell_max_orders', $plan)
                    <span class="badge badge-ghost">{{ $plan->max_orders }}</span>
                @endscope

            </x-table>
        </x-card>
    </div>

    <!-- DRAWER DE EDICIÓN -->
    <x-drawer wire:model="drawer" title="{{ $my_plan ? 'Editar Plan' : 'Crear Plan' }}" right class="w-full lg:w-1/3">
        <x-form wire:submit="save">
            <x-input label="Nombre Comercial" wire:model="name" placeholder="Ej. Plan Emprendedor" icon="o-tag" />
            
            <x-input label="Precio Mensual ($)" wire:model="price" type="number" step="0.01" prefix="$" class="font-bold text-lg" />

            <div class="divider text-xs font-bold text-gray-400">LÍMITES DEL SISTEMA</div>

            <div class="grid grid-cols-2 gap-4">
                <x-input label="Usuarios Permitidos" wire:model="max_users" type="number" hint="Accesos simultáneos" />
                <x-input label="Órdenes por Mes" wire:model="max_orders" type="number" hint="Capacidad operativa" />
            </div>

            <x-slot:actions>
                <x-button label="Cancelar" wire:click="$toggle('drawer')" />
                <x-button label="Guardar" class="btn-primary" type="submit" spinner="save" />
            </x-slot:actions>
        </x-form>
    </x-drawer>
</div>
