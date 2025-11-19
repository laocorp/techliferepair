<?php

use Livewire\Volt\Component;
use App\Models\RepairOrder;
use App\Models\Part;
use App\Models\Client;
use Livewire\Attributes\Layout;

new 
#[Layout('layouts.app')]
class extends Component {
    
    public function mount(): void
    {
        // 1. Redirección de Seguridad para Clientes
        if (auth()->user()->isClient()) {
            $this->redirect(route('client.portal'), navigate: true);
            return; 
        }
    }

    public function with(): array
    {
        // Lógica para mostrar dinero solo a admins
        $isAdmin = auth()->user()->isAdmin();

        return [
            'revenue_month' => $isAdmin 
                ? RepairOrder::whereIn('status', ['listo', 'entregado'])->whereMonth('created_at', now()->month)->sum('total_cost')
                : 0,

            'active_orders' => RepairOrder::where('status', '!=', 'entregado')->count(),
            'total_clients' => Client::count(),
            'low_stock_parts' => Part::whereColumn('stock', '<=', 'stock_min')->count(),

            // Últimas órdenes
            'latest_orders' => RepairOrder::with(['asset.client'])
                ->latest()
                ->take(5)
                ->get(),
            
            // Alertas de Mantenimiento (> 3 meses)
            'maintenance_alerts' => RepairOrder::query()
                ->with(['asset.client'])
                ->where('status', 'entregado')
                ->whereDate('updated_at', '<=', now()->subMonths(3))
                ->latest()
                ->take(5) 
                ->get(),

            // Headers para la tabla principal
            'headers' => $this->headers(),
            
            // Headers para la tabla de mantenimiento (ESTO FALTABA)
            'maintenance_headers' => $this->maintenanceHeaders()
        ];
    }

    // Columnas tabla principal
    public function headers(): array
    {
        $headers = [
            ['key' => 'id', 'label' => '#OT', 'class' => 'font-bold w-1'],
            ['key' => 'asset.client.name', 'label' => 'Cliente'],
            ['key' => 'status', 'label' => 'Estado'],
        ];

        if(auth()->user()->isAdmin()) {
            $headers[] = ['key' => 'total_cost', 'label' => 'Total', 'class' => 'text-right font-bold'];
        }

        return $headers;
    }

    // Columnas tabla mantenimiento (NUEVO)
    public function maintenanceHeaders(): array
    {
        return [
            ['key' => 'asset.client.name', 'label' => 'Cliente / Equipo'],
            ['key' => 'updated_at', 'label' => 'Fecha Entrega'],
        ];
    }
}; ?>

<div>
    <x-header title="Dashboard" subtitle="Resumen de operaciones - {{ now()->format('F Y') }}" separator />

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
        
        {{-- DINERO (SOLO ADMIN) --}}
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
        
        <div class="lg:col-span-2 space-y-8">
            
            <x-card title="Últimos Ingresos" class="shadow-xl" separator>
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

            {{-- AQUÍ ESTABA EL ERROR: FALTABA :headers --}}
            <x-card title="🔔 Oportunidades de Mantenimiento" subtitle="Equipos entregados hace > 3 meses" separator class="border-l-4 border-warning shadow-xl">
                <x-table :headers="$maintenance_headers" :rows="$maintenance_alerts">
                    
                    @scope('cell_asset.client.name', $order)
                        <div class="flex flex-col">
                            <span class="font-bold">{{ $order->asset->client->name }}</span>
                            <span class="text-xs opacity-60">{{ $order->asset->brand }} {{ $order->asset->model }}</span>
                        </div>
                    @endscope
                    
                    @scope('cell_updated_at', $order)
                        Hace {{ $order->updated_at->diffInMonths() }} meses
                    @endscope
                    
                    @scope('actions', $order)
                        @if($order->asset->client->phone)
                            <x-button icon="o-chat-bubble-left" class="btn-sm btn-circle btn-success text-white"
                                link="https://wa.me/{{ preg_replace('/[^0-9]/', '', $order->asset->client->phone) }}?text=Hola {{ $order->asset->client->name }}, notamos que tu equipo {{ $order->asset->model }} ya cumplió su ciclo de mantenimiento. ¿Deseas agendar una revisión?" external />
                        @endif
                    @endscope
                </x-table>
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
