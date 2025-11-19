<?php

use Livewire\Volt\Component;
use App\Models\RepairOrder;
use App\Models\Part;
use App\Models\Client;
use App\Models\Asset;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\DB;

new 
#[Layout('layouts.app')]
class extends Component {
    
    public array $incomeChart = [];
    public array $brandsChart = [];

    public function mount(): void
    {
        if (auth()->user()->isClient()) {
            $this->redirect(route('client.portal'), navigate: true);
            return; 
        }

        $this->buildIncomeChart();
        $this->buildBrandsChart();
    }

    public function buildIncomeChart(): void
    {
        $data = RepairOrder::selectRaw('DATE(created_at) as date, SUM(total_cost) as total')
            ->where('payment_status', 'paid')
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $chartData = [];
        $labels = [];
        
        for ($i = 30; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $labels[] = now()->subDays($i)->format('d/m');
            $record = $data->firstWhere('date', $date);
            $chartData[] = $record ? $record->total : 0;
        }

        $this->incomeChart = [
            'type' => 'line',
            'data' => [
                'labels' => $labels,
                'datasets' => [
                    [
                        'label' => 'Ingresos ($)',
                        'data' => $chartData,
                        'borderColor' => '#3b82f6',
                        'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                        'fill' => true,
                        'tension' => 0.3
                    ]
                ]
            ]
        ];
    }

    public function buildBrandsChart(): void
    {
        $data = Asset::select('brand', DB::raw('count(*) as total'))
            ->groupBy('brand')
            ->orderByDesc('total')
            ->take(5)
            ->get();

        $this->brandsChart = [
            'type' => 'doughnut',
            'data' => [
                'labels' => $data->pluck('brand'),
                'datasets' => [
                    [
                        'data' => $data->pluck('total'),
                        'backgroundColor' => ['#0f172a', '#3b82f6', '#6366f1', '#94a3b8', '#cbd5e1'],
                    ]
                ]
            ],
            'options' => [
                'plugins' => [
                    'legend' => ['position' => 'bottom']
                ]
            ]
        ];
    }

    public function with(): array
    {
        $isAdmin = auth()->user()->isAdmin();

        return [
            'revenue_month' => $isAdmin 
                ? RepairOrder::whereIn('status', ['listo', 'entregado'])->whereMonth('created_at', now()->month)->sum('total_cost')
                : 0,

            'active_orders' => RepairOrder::where('status', '!=', 'entregado')->count(),
            'total_clients' => Client::count(),
            'low_stock_parts' => Part::whereColumn('stock', '<=', 'stock_min')->count(),

            'latest_orders' => RepairOrder::with(['asset.client'])
                ->latest()
                ->take(5)
                ->get(),
            
            'maintenance_alerts' => RepairOrder::query()
                ->with(['asset.client'])
                ->where('status', 'entregado')
                ->whereDate('updated_at', '<=', now()->subMonths(3))
                ->latest()->take(5)->get(),

            'headers' => $this->headers(),
            'maintenance_headers' => $this->maintenanceHeaders()
        ];
    }

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
                class="shadow-sm bg-white border border-slate-200"
                description="Órdenes finalizadas"
                color="text-success"
            />
        @endif

        <x-stat 
            title="En Taller" 
            value="{{ $active_orders }}" 
            icon="o-wrench-screwdriver" 
            class="shadow-sm bg-white border border-slate-200"
            description="Máquinas pendientes"
            color="text-primary"
        />

        <x-stat 
            title="Cartera Clientes" 
            value="{{ $total_clients }}" 
            icon="o-users" 
            class="shadow-sm bg-white border border-slate-200"
            color="text-info"
        />

        <x-stat 
            title="Stock Crítico" 
            value="{{ $low_stock_parts }}" 
            icon="o-exclamation-triangle" 
            class="shadow-sm bg-white border border-slate-200"
            description="Repuestos por agotar"
            color="text-error"
        />
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
        
        <div class="lg:col-span-2">
            <x-card title="Tendencia de Ingresos (30 días)" separator class="shadow-sm h-full">
                @if(auth()->user()->isAdmin())
                    <x-chart wire:model="incomeChart" class="h-64" />
                @else
                    <div class="h-64 flex items-center justify-center bg-slate-50 rounded-lg border border-dashed border-slate-300 text-slate-400">
                        <div class="text-center">
                            <x-icon name="o-lock-closed" class="w-8 h-8 mx-auto mb-2" />
                            <div>Información Financiera Restringida</div>
                        </div>
                    </div>
                @endif
            </x-card>
        </div>

        <div class="lg:col-span-1">
            <x-card title="Top Marcas" separator class="shadow-sm h-full">
                <x-chart wire:model="brandsChart" class="h-64" />
            </x-card>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <div class="lg:col-span-2 space-y-8">
            
            <x-card title="Últimos Ingresos" class="shadow-sm" separator>
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

            <x-card title="🔔 Oportunidades de Mantenimiento" subtitle="Equipos entregados hace > 3 meses" separator class="border-l-4 border-warning shadow-sm">
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
                                link="https://wa.me/{{ preg_replace('/[^0-9]/', '', $order->asset->client->phone) }}?text=Hola {{ $order->asset->client->name }}, notamos que tu equipo {{ $order->asset->model }} ya cumplió su ciclo de mantenimiento." external />
                        @endif
                    @endscope
                </x-table>
            </x-card>
        </div>

        <div class="lg:col-span-1 flex flex-col gap-4">
            <div class="bg-primary/5 border border-primary/20 rounded-xl p-6 flex items-center gap-4 hover:bg-primary/10 transition cursor-pointer" onclick="window.location='/orders'">
                <div class="bg-primary text-white p-3 rounded-full shadow-md">
                    <x-icon name="o-plus" class="w-6 h-6" />
                </div>
                <div>
                    <div class="font-bold text-lg text-slate-800">Nueva Recepción</div>
                    <div class="text-xs opacity-70 text-slate-500">Ingresar equipo</div>
                </div>
            </div>

            <div class="bg-indigo-50 border border-indigo-200 rounded-xl p-6 flex items-center gap-4 hover:bg-indigo-100 transition cursor-pointer" onclick="window.location='/parts'">
                <div class="bg-indigo-600 text-white p-3 rounded-full shadow-md">
                    <x-icon name="o-archive-box" class="w-6 h-6" />
                </div>
                <div>
                    <div class="font-bold text-lg text-slate-800">Inventario</div>
                    <div class="text-xs opacity-70 text-slate-500">Ver stock actual</div>
                </div>
            </div>
        </div>
    </div>
</div>
