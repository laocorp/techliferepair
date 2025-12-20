<?php

use Livewire\Volt\Component;
use App\Models\Sale;
use Mary\Traits\Toast;
use Livewire\Attributes\Layout;
use Illuminate\Database\Eloquent\Builder;

new 
#[Layout('layouts.app')]
class extends Component {
    use Toast;

    public string $search = '';
    public string $filter = 'today'; // 'today' o 'all'
    
    // Variables para el detalle de la venta (Drawer)
    public bool $drawer = false;
    public ?Sale $selected_sale = null;

    public function showDetails(Sale $sale): void
    {
        $this->selected_sale = $sale->load('items.part', 'user');
        $this->drawer = true;
    }

    // --- FUNCIÓN PARA ELIMINAR VENTA ---
    public function delete(Sale $sale): void
    {
        // Opcional: Verificar si el usuario tiene permiso para borrar ventas
        if (!auth()->user()->isAdmin()) {
            $this->error('No tienes permiso para eliminar ventas.');
            return;
        }

        // Al borrar la venta, los items se borran en cascada (por la BD)
        // Pero OJO: Si quieres devolver el stock al inventario, necesitas lógica extra aquí.
        // Por ahora, solo borramos el registro.
        
        $sale->delete();
        $this->success('Venta eliminada correctamente');
        $this->drawer = false; // Cerrar el drawer si estaba abierto
    }

    public function headers(): array
    {
        return [
            ['key' => 'id', 'label' => '# Ticket', 'class' => 'font-bold w-1'],
            ['key' => 'created_at', 'label' => 'Hora/Fecha'],
            ['key' => 'user.name', 'label' => 'Vendedor'],
            ['key' => 'total', 'label' => 'Total', 'class' => 'text-right font-bold text-slate-900'],
        ];
    }

    public function with(): array
    {
        // Query base
        $query = Sale::with(['user'])
            ->orderBy('id', 'desc');

        // Filtro de Búsqueda (por ID de ticket)
        if ($this->search) {
            $query->where('id', 'like', "%$this->search%");
        }

        // Filtro de Fecha (Hoy vs Todo)
        if ($this->filter === 'today') {
            $query->whereDate('created_at', now()->today());
        }

        // Calcular total vendido HOY (independiente del filtro de tabla)
        $totalToday = Sale::whereDate('created_at', now()->today())->sum('total');

        return [
            'sales' => $query->paginate(20),
            'total_today' => $totalToday,
            'headers' => $this->headers()
        ];
    }
}; ?>

<div>
    <x-header title="Historial de Ventas" subtitle="Registro de transacciones de mostrador (POS)" separator>
        <x-slot:actions>
            <x-button label="Nueva Venta (POS)" icon="o-shopping-cart" link="/pos" class="btn-primary" />
        </x-slot:actions>
    </x-header>

    <!-- RESUMEN DE CAJA -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
        <div class="bg-slate-900 text-white p-6 rounded-xl shadow-lg flex items-center justify-between col-span-1">
            <div>
                <div class="text-xs font-bold uppercase tracking-wider opacity-70">Vendido Hoy</div>
                <div class="text-3xl font-black">${{ number_format($total_today, 2) }}</div>
            </div>
            <div class="p-3 bg-white/10 rounded-lg">
                <x-icon name="o-currency-dollar" class="w-8 h-8" />
            </div>
        </div>
    </div>

    <!-- FILTROS Y TABLA -->
    <div class="flex justify-between items-center mb-4">
        <!-- Pestañas de Filtro -->
        <div class="join">
            <button 
                class="join-item btn btn-sm {{ $filter === 'today' ? 'btn-active btn-neutral' : 'btn-ghost bg-white' }}" 
                wire:click="$set('filter', 'today')">
                Hoy
            </button>
            <button 
                class="join-item btn btn-sm {{ $filter === 'all' ? 'btn-active btn-neutral' : 'btn-ghost bg-white' }}" 
                wire:click="$set('filter', 'all')">
                Histórico
            </button>
        </div>

        <!-- Buscador -->
        <div class="w-48">
            <x-input icon="o-magnifying-glass" placeholder="Buscar ticket #..." wire:model.live.debounce="search" class="input-sm" />
        </div>
    </div>

    <x-card>
        <x-table :headers="$headers" :rows="$sales" striped @row-click="$wire.showDetails($event.detail.id)" class="cursor-pointer hover:bg-slate-50">
            
            @scope('cell_id', $sale)
                <span class="font-mono text-slate-500 font-bold">#{{ str_pad($sale->id, 6, '0', STR_PAD_LEFT) }}</span>
            @endscope

            @scope('cell_created_at', $sale)
                <div class="flex flex-col">
                    <span class="font-bold text-xs">{{ $sale->created_at->format('d/m/Y') }}</span>
                    <span class="text-xs text-gray-400">{{ $sale->created_at->format('H:i A') }}</span>
                </div>
            @endscope

            @scope('cell_total', $sale)
                ${{ number_format($sale->total, 2) }}
            @endscope
            
            @scope('actions', $sale)
                <div class="flex gap-1 justify-end" onclick="event.stopPropagation()">
                    <!-- Botón Imprimir -->
                    <x-button icon="o-printer" class="btn-sm btn-ghost text-slate-400 hover:text-blue-600" link="{{ route('pos.print', $sale) }}" external tooltip="Imprimir Ticket" />
                    
                    <!-- Botón Eliminar (Solo Admin) -->
                    @if(auth()->user()->isAdmin())
                        <x-button 
                            icon="o-trash" 
                            class="btn-sm btn-ghost text-slate-400 hover:text-red-600 hover:bg-red-50" 
                            wire:click="delete({{ $sale->id }})" 
                            confirm="¿Eliminar esta venta permanentemente?"
                            tooltip="Eliminar" 
                        />
                    @endif
                </div>
            @endscope

        </x-table>

        <div class="mt-4">
            {{ $sales->links() }}
        </div>
    </x-card>

    <!-- DRAWER DETALLE DE VENTA -->
    <x-drawer wire:model="drawer" title="Detalle de Venta" right class="w-full lg:w-1/3">
        @if($selected_sale)
            <div class="space-y-6">
                
                <div class="bg-slate-50 p-4 rounded-xl border border-slate-200">
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-xs font-bold text-slate-400 uppercase">Ticket #</span>
                        <span class="font-mono font-bold text-slate-900 text-lg">{{ str_pad($selected_sale->id, 6, '0', STR_PAD_LEFT) }}</span>
                    </div>
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-xs font-bold text-slate-400 uppercase">Fecha</span>
                        <span class="text-sm text-slate-700">{{ $selected_sale->created_at->format('d/m/Y H:i') }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-xs font-bold text-slate-400 uppercase">Vendedor</span>
                        <div class="flex items-center gap-2">
                            <x-avatar :image="url('https://robohash.org/'.$selected_sale->user->email)" class="!w-5 h-5" />
                            <span class="text-sm text-slate-700">{{ $selected_sale->user->name }}</span>
                        </div>
                    </div>
                    <!-- Cliente (Si existe) -->
                    @if($selected_sale->client)
                        <div class="flex justify-between items-center mt-2 pt-2 border-t border-slate-200">
                            <span class="text-xs font-bold text-slate-400 uppercase">Cliente</span>
                            <span class="text-sm text-slate-700 font-bold">{{ $selected_sale->client->name }}</span>
                        </div>
                    @endif
                </div>

                <!-- Lista de Productos -->
                <div>
                    <h3 class="text-xs font-black text-slate-900 mb-3 uppercase tracking-wide">Productos Vendidos</h3>
                    <div class="border rounded-lg overflow-hidden">
                        <table class="w-full text-sm">
                            <thead class="bg-slate-50 text-slate-500 border-b">
                                <tr>
                                    <th class="text-left p-2 font-medium">Desc</th>
                                    <th class="text-center p-2 font-medium">Cant</th>
                                    <th class="text-right p-2 font-medium">Total</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach($selected_sale->items as $item)
                                    <tr class="bg-white">
                                        <td class="p-3">
                                            <div class="font-bold text-slate-700">{{ $item->part->name }}</div>
                                            <div class="text-[10px] text-slate-400">{{ $item->part->sku }}</div>
                                        </td>
                                        <td class="p-3 text-center text-slate-600">x{{ $item->quantity }}</td>
                                        <td class="p-3 text-right font-bold text-slate-900">${{ number_format($item->price * $item->quantity, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Total -->
                <div class="flex justify-between items-end pt-4 border-t border-dashed border-slate-300">
                    <span class="text-sm font-bold text-slate-500 uppercase">Total Pagado</span>
                    <span class="text-3xl font-black text-slate-900">${{ number_format($selected_sale->total, 2) }}</span>
                </div>

                <!-- Acciones -->
                <div class="grid grid-cols-1 gap-3 pt-4">
                    <x-button label="Reimprimir Ticket" icon="o-printer" class="btn-outline w-full" link="{{ route('pos.print', $selected_sale) }}" external />
                    
                    @if(auth()->user()->isAdmin())
                        <x-button 
                            label="Eliminar Venta" 
                            icon="o-trash" 
                            class="btn-ghost text-red-500 hover:bg-red-50 w-full" 
                            wire:click="delete({{ $selected_sale->id }})" 
                            confirm="¿Estás seguro? Esto borrará el registro permanentemente."
                        />
                    @endif

                    <x-button label="Cerrar" wire:click="$toggle('drawer')" class="btn-ghost w-full text-slate-400" />
                </div>

            </div>
        @endif
    </x-drawer>
</div>
