<?php

use Livewire\Volt\Component;
use App\Models\RepairOrder;
use Livewire\Attributes\Layout;

new 
#[Layout('layouts.app')]
class extends Component {
    
    // Esta función se llama desde JS al soltar una tarjeta
    public function updateStatus($orderId, $newStatus): void
    {
        $order = RepairOrder::find($orderId);
        
        if ($order) {
            $order->update(['status' => $newStatus]);
        }
    }
    
    // Colores más modernos y sutiles para los bordes
    public function getStatusColor($status): string
    {
        return match ($status) {
            'recibido' => 'border-l-4 border-l-blue-500',
            'diagnostico' => 'border-l-4 border-l-yellow-500',
            'espera_repuestos' => 'border-l-4 border-l-red-500',
            'listo' => 'border-l-4 border-l-green-500',
            default => 'border-l-4 border-l-slate-300',
        };
    }
    
    // Iconos para cada estado
    public function getStatusIcon($status): string
    {
        return match ($status) {
            'recibido' => 'o-inbox-arrow-down',
            'diagnostico' => 'o-magnifying-glass',
            'espera_repuestos' => 'o-clock',
            'listo' => 'o-check-circle',
            default => 'o-question-mark-circle',
        };
    }

    public function with(): array
    {
        $allOrders = RepairOrder::with('asset.client')
            ->where('status', '!=', 'entregado')
            ->get();

        $groupedOrders = [
            'recibido' => $allOrders->where('status', 'recibido'),
            'diagnostico' => $allOrders->where('status', 'diagnostico'),
            'espera_repuestos' => $allOrders->where('status', 'espera_repuestos'),
            'listo' => $allOrders->where('status', 'listo'),
        ];

        return [
            'orders' => $groupedOrders
        ];
    }
}; ?>

<div class="h-[calc(100vh-100px)] flex flex-col">
    
    <div class="mb-4">
        <x-header title="Tablero de Trabajo" subtitle="Gestión visual del flujo de reparaciones" separator>
            <x-slot:actions>
                <x-button label="Vista Lista" icon="o-list-bullet" link="/orders" class="btn-ghost btn-sm" />
                <x-button label="Nueva Orden" icon="o-plus" class="btn-primary btn-sm" link="/orders" />
            </x-slot:actions>
        </x-header>
    </div>

    <!-- CONTENEDOR DE COLUMNAS -->
    <div class="flex-1 overflow-x-auto overflow-y-hidden">
        <div class="flex gap-6 h-full pb-4 min-w-[1200px]">
            
            @foreach(['recibido', 'diagnostico', 'espera_repuestos', 'listo'] as $status)
                
                <!-- COLUMNA -->
                <div class="flex-1 flex flex-col bg-slate-100/80 rounded-xl border border-slate-200/60 min-w-[280px] max-w-xs h-full">
                    
                    <!-- CABECERA -->
                    <div class="p-3 flex justify-between items-center border-b border-slate-200 bg-slate-50/50 rounded-t-xl backdrop-blur-sm">
                        <div class="flex items-center gap-2 font-bold text-slate-700 uppercase text-xs tracking-wider">
                            <x-icon name="{{ $this->getStatusIcon($status) }}" class="w-4 h-4 opacity-50" />
                            {{ str_replace('_', ' ', $status) }}
                        </div>
                        <div class="bg-white px-2 py-0.5 rounded-md text-xs font-bold text-slate-500 border border-slate-200 shadow-sm">
                            {{ $orders[$status]->count() }}
                        </div>
                    </div>

                    <!-- ZONA DE TARJETAS (SORTABLE) -->
                    <div 
                        class="flex-1 p-3 space-y-3 overflow-y-auto custom-scrollbar sortable-list" 
                        data-status="{{ $status }}"
                        wire:ignore.self
                    >
                        @foreach($orders[$status] as $order)
                            
                            <!-- TARJETA INDIVIDUAL -->
                            <div 
                                class="bg-white p-3 rounded-lg shadow-sm border border-slate-200 cursor-grab active:cursor-grabbing hover:shadow-md hover:-translate-y-0.5 transition-all group {{ $this->getStatusColor($status) }}"
                                data-id="{{ $order->id }}"
                                wire:key="order-{{ $order->id }}"
                            >
                                <!-- Header Tarjeta -->
                                <div class="flex justify-between items-start mb-2">
                                    <span class="font-black text-slate-800 text-sm">#{{ str_pad($order->id, 4, '0', STR_PAD_LEFT) }}</span>
                                    <a href="/orders/{{ $order->id }}" class="text-slate-400 hover:text-blue-600 transition-colors opacity-0 group-hover:opacity-100" title="Ver detalles">
                                        <x-icon name="o-pencil-square" class="w-4 h-4" />
                                    </a>
                                </div>
                                
                                <!-- Info Equipo -->
                                <div class="text-sm font-semibold text-slate-700 mb-0.5 line-clamp-2 leading-tight">
                                    {{ $order->asset->brand }} {{ $order->asset->model }}
                                </div>
                                
                                <!-- Cliente -->
                                <div class="text-xs text-slate-500 mb-3 flex items-center gap-1 truncate">
                                    <x-icon name="o-user" class="w-3 h-3 opacity-70" />
                                    {{ $order->asset->client->name }}
                                </div>

                                <!-- Footer Tarjeta -->
                                <div class="flex justify-between items-center border-t border-slate-50 pt-2 mt-1">
                                    <div class="flex items-center gap-1 text-[10px] text-slate-400 font-mono" title="Fecha de ingreso">
                                        <x-icon name="o-calendar" class="w-3 h-3" />
                                        {{ $order->created_at->format('d/m') }}
                                    </div>
                                    
                                    @if($order->total_cost > 0)
                                        <span class="text-[10px] font-bold text-green-700 bg-green-50 px-1.5 py-0.5 rounded border border-green-100">
                                            ${{ number_format($order->total_cost, 0) }}
                                        </span>
                                    @endif
                                </div>
                            </div>

                        @endforeach
                        
                        <!-- Placeholder si está vacío (opcional, mejora visual) -->
                        @if($orders[$status]->isEmpty())
                            <div class="h-24 border-2 border-dashed border-slate-200 rounded-lg flex items-center justify-center text-slate-300 text-xs italic">
                                Sin órdenes
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

<script>
    document.addEventListener('livewire:navigated', () => {
        initKanban();
    });

    function initKanban() {
        const columns = document.querySelectorAll('.sortable-list');

        columns.forEach(column => {
            new Sortable(column, {
                group: 'kanban',
                animation: 200, // Un poco más suave
                ghostClass: 'opacity-50', // Clase visual al arrastrar
                dragClass: 'rotate-2', // Pequeña rotación al arrastrar para efecto "físico"
                delay: 100, // Pequeño delay para evitar drags accidentales en táctil
                delayOnTouchOnly: true,
                onEnd: function (evt) {
                    const itemEl = evt.item;
                    const newStatus = evt.to.getAttribute('data-status');
                    const orderId = itemEl.getAttribute('data-id');

                    @this.updateStatus(orderId, newStatus);
                }
            });
        });
    }
</script>
