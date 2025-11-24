<?php

use Livewire\Volt\Component;
use App\Models\RepairOrder;
use App\Models\Asset;
use Mary\Traits\Toast;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Rule;

new 
#[Layout('layouts.app')]
class extends Component {
    use Toast;

    public string $search = '';
    public bool $drawer = false;
    
    public ?int $asset_id = null;
    
    #[Rule('required|min:5')]
    public string $problem_description = '';
    
    #[Rule('boolean')]
    public bool $is_warranty = false;

    public function create(): void
    {
        $this->reset(['drawer', 'asset_id', 'problem_description', 'is_warranty']);
        $this->resetValidation();
        $this->drawer = true;
    }

    public function save(): void
    {
        $this->validate([
            'asset_id' => 'required',
            'problem_description' => 'required|min:5',
            'is_warranty' => 'boolean'
        ]);

        $user = auth()->user();

        if (!$user->is_super_admin) {
            if (!$user->company) {
                $this->error('Error Crítico: Tu usuario no tiene una empresa asignada.');
                return;
            }
            if (!$user->company->canCreateOrder()) {
                $this->error('Límite de órdenes mensuales alcanzado. Mejora tu plan.');
                return;
            }
        }

        RepairOrder::create([
            'asset_id' => $this->asset_id,
            'problem_description' => $this->problem_description,
            'is_warranty' => $this->is_warranty,
            'status' => 'recibido'
        ]);

        $this->success('¡Orden de Trabajo creada!');
        $this->drawer = false;
    }

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
            ['key' => 'id', 'label' => '#OT', 'class' => 'font-bold w-20'],
            ['key' => 'asset.client.name', 'label' => 'Cliente'],
            ['key' => 'asset.model', 'label' => 'Equipo'],
            ['key' => 'status', 'label' => 'Estado', 'class' => 'text-center'],
            ['key' => 'created_at', 'label' => 'Fecha', 'class' => 'text-right text-slate-400'],
        ];
    }

    public function with(): array
    {
        return [
            'orders' => RepairOrder::query()
                ->with(['asset.client'])
                ->where('id', 'like', "%$this->search%") // Búsqueda por ID
                ->orWhereHas('asset.client', fn($q) => $q->where('name', 'like', "%$this->search%")) // Búsqueda por Cliente
                ->orderBy('id', 'desc')
                ->get(),
            'headers' => $this->headers(),
            'assets_list' => $this->assets(),
        ];
    }
}; ?>

<div>
    <x-header title="Órdenes de Trabajo" subtitle="Control operativo y seguimiento" separator>
        <x-slot:middle class="!justify-end">
            <x-input icon="o-magnifying-glass" placeholder="Buscar orden..." wire:model.live.debounce="search" class="w-72" />
        </x-slot:middle>
        <x-slot:actions>
            <x-button label="Nueva Orden" icon="o-plus" class="btn-primary" wire:click="create" />
        </x-slot:actions>
    </x-header>

    <x-card>
        <x-table :headers="$headers" :rows="$orders" striped link="/orders/{id}" class="cursor-pointer hover:bg-slate-50">
            
            @scope('cell_id', $order)
                <span class="font-mono text-slate-900 font-bold bg-slate-100 px-2 py-1 rounded">#{{ str_pad($order->id, 4, '0', STR_PAD_LEFT) }}</span>
            @endscope

            @scope('cell_asset.client.name', $order)
                <div>
                    <div class="font-bold text-slate-900">{{ $order->asset->client->name }}</div>
                    <div class="text-xs text-slate-500">{{ $order->asset->client->phone ?? 'Sin teléfono' }}</div>
                </div>
            @endscope

            @scope('cell_asset.model', $order)
                <div class="flex flex-col">
                    <span class="font-medium text-slate-700">{{ $order->asset->brand }} {{ $order->asset->model }}</span>
                    <span class="text-xs text-slate-400 font-mono">{{ $order->asset->serial_number }}</span>
                </div>
            @endscope

            @scope('cell_status', $order)
                <div class="flex justify-center">
                    <x-badge :value="$order->status_label" :class="'badge-' . $order->status_color" />
                </div>
                @if($order->is_warranty)
                    <div class="text-center mt-1">
                        <span class="text-[10px] font-bold text-amber-600 uppercase tracking-wider">Garantía</span>
                    </div>
                @endif
            @endscope

            @scope('cell_created_at', $order)
                <span class="text-slate-500">{{ $order->created_at->format('d/m/Y') }}</span>
            @endscope
            
            <x-slot:empty>
                <div class="py-12 flex flex-col items-center justify-center text-slate-400">
                    <x-icon name="o-clipboard-document-list" class="w-12 h-12 mb-2 opacity-20" />
                    <div class="font-medium">No hay órdenes registradas</div>
                </div>
            </x-slot:empty>
        </x-table>
    </x-card>

    <x-drawer wire:model="drawer" title="Nueva Orden" right class="w-full lg:w-1/3">
        <x-form wire:submit="save" class="space-y-4">
            
            <div class="bg-blue-50 border border-blue-100 rounded-lg p-4 mb-4">
                <div class="flex items-start gap-3">
                    <x-icon name="o-information-circle" class="w-5 h-5 text-blue-600 mt-0.5" />
                    <div class="text-sm text-blue-800">
                        Selecciona un equipo registrado. Si el cliente es nuevo, regístralo primero en la sección de Clientes.
                    </div>
                </div>
            </div>

            <x-select 
                label="Equipo / Activo" 
                icon="o-wrench-screwdriver" 
                :options="$assets_list" 
                wire:model="asset_id"
                placeholder="Buscar por serial o modelo..."
                searchable
            />

            <x-textarea 
                label="Descripción del Problema" 
                wire:model="problem_description" 
                placeholder="Describe la falla reportada por el cliente..." 
                rows="5"
                class="font-medium"
            />

            <div class="flex items-center gap-3 p-3 border border-slate-200 rounded-lg hover:bg-slate-50 transition-colors cursor-pointer" onclick="document.getElementById('warranty-check').click()">
                <x-checkbox id="warranty-check" wire:model="is_warranty" />
                <div>
                    <div class="font-bold text-slate-700">Reparación por Garantía</div>
                    <div class="text-xs text-slate-500">Marca esta casilla si no se cobrará mano de obra</div>
                </div>
            </div>

            <x-slot:actions>
                <x-button label="Cancelar" wire:click="$toggle('drawer')" />
                <x-button label="Crear Orden" class="btn-primary" type="submit" spinner="save" />
            </x-slot:actions>
        </x-form>
    </x-drawer>
</div>
