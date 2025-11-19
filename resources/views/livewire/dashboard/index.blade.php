<?php

use Livewire\Volt\Component;
use App\Models\RepairOrder;
use App\Models\Part;
use App\Models\Client;
use Livewire\Attributes\Layout;

new 
#[Layout('layouts.app')]
class extends Component {
    
    public function with(): array
    {
        return [
            // Dinero (Solo lo calcula si eres admin para ahorrar recursos)
            'revenue_month' => auth()->user()->isAdmin() 
                ? RepairOrder::whereIn('status', ['listo', 'entregado'])->whereMonth('created_at', now()->month)->sum('total_cost')
                : 0,

            'active_orders' => RepairOrder::where('status', '!=', 'entregado')->count(),
            'total_clients' => Client::count(),
            'low_stock_parts' => Part::whereColumn('stock', '<=', 'stock_min')->count(),

            'latest_orders' => RepairOrder::with(['asset.client'])
                ->latest()
                ->take(5)
                ->get(),
                
            'headers' => $this->headers()
        ];
    }

    public function headers(): array
    {
        $headers = [
            ['key' => 'id', 'label' => '#OT', 'class' => 'font-bold w-1'],
            ['key' => 'asset.client.name', 'label' => 'Cliente'],
            ['key' => 'status', 'label' => 'Estado'],
        ];

        // Solo agregamos la columna de dinero si es ADMIN
        if(auth()->user()->isAdmin()) {
            $headers[] = ['key' => 'total_cost', 'label' => 'Total', 'class' => 'text-right font-bold'];
        }

        return $headers;
    }
}; ?>

<div>
    <x-header title="Dashboard" subtitle="Resumen de operaciones - {{ now()->format('F Y') }}" separator />

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
        
        {{-- 💰 CAJA DE DINERO (SOLO ADMIN) --}}
        @if(auth()->user()->isAdmin())
            <x-stat 
                title="Facturación Mes" 
                value="$ {{ number_format($revenue_month, 2) }}" 
                icon="o-currency-dollar" 
                class="shadow-xl bg-base-200 border border-base-300"
                description="Órdenes finalizadas"
                color="text-success"
            />
        @endif

        <x-stat 
            title="En Taller" 
            value="{{ $active_orders }}" 
            icon="o-wrench-screwdriver" 
            class="shadow-xl bg-base-200 border border-base-300"
            description="Máquinas pendientes"
            color="text-primary"
        />

        <x-stat 
            title="Cartera Clientes" 
            value="{{ $total_clients }}" 
            icon="o-users" 
            class="shadow-xl bg-base-200 border border-base-300"
            color="text-info"
        />

        <x-stat 
            title="Stock Crítico" 
            value="{{ $low_stock_parts }}" 
            icon="o-exclamation-triangle" 
            class="shadow-xl bg-base-200 border border-base-300"
            description="Repuestos por agotar"
            color="text-error"
        />
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <div class="lg:col-span-2">
            <x-card title="Últimos Ingresos" class="shadow-xl h-full" separator>
                <x-table :headers="$headers" :rows="$latest_orders" link="/orders/{id}">
                    
                    @scope('cell_id', $order)
                        <span class="font-bold text-primary">OT-{{ str_pad($order->id, 4, '0', STR_PAD_LEFT) }}</span>
                    @endscope

                    @scope('cell_status', $order)
                        <x-badge :value="$order->status_label" :class="'badge-' . $order->status_color" />
                    @endscope

                    @if(auth()->user()->isAdmin())
                        @scope('cell_total_cost', $order)
                            $ {{ number_format($order->total_cost, 2) }}
                        @endscope
                    @endif

                </x-table>
                
                <x-slot:actions>
                    <x-button label="Ver todas" link="/orders" class="btn-ghost btn-sm" />
                </x-slot:actions>
            </x-card>
        </div>

        <div class="lg:col-span-1 flex flex-col gap-4">
            
            <div class="bg-primary/10 border border-primary/20 rounded-2xl p-6 flex items-center gap-4 hover:bg-primary/20 transition cursor-pointer" onclick="window.location='/orders'">
                <div class="bg-primary text-white p-4 rounded-full shadow-lg shadow-primary/40">
                    <x-icon name="o-plus" class="w-6 h-6" />
                </div>
                <div>
                    <div class="font-bold text-lg text-white">Nueva Recepción</div>
                    <div class="text-xs opacity-70 text-gray-300">Ingresar equipo</div>
                </div>
            </div>

            <div class="bg-secondary/10 border border-secondary/20 rounded-2xl p-6 flex items-center gap-4 hover:bg-secondary/20 transition cursor-pointer" onclick="window.location='/parts'">
                <div class="bg-secondary text-white p-4 rounded-full shadow-lg shadow-secondary/40">
                    <x-icon name="o-archive-box" class="w-6 h-6" />
                </div>
                <div>
                    <div class="font-bold text-lg text-white">Inventario</div>
                    <div class="text-xs opacity-70 text-gray-300">Ver stock actual</div>
                </div>
            </div>

        </div>
    </div>
</div>
