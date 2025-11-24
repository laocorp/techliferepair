<?php

use Livewire\Volt\Component;
use App\Models\RepairOrder;
use App\Models\Sale;
use App\Models\Part;
use App\Models\Client;
use App\Models\Asset;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

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
        $repairs = RepairOrder::selectRaw('DATE(created_at) as date, SUM(total_cost) as total')
            ->where('payment_status', 'paid')
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->get();

        $sales = collect([]);
        if (Schema::hasTable('sales')) {
            $sales = Sale::selectRaw('DATE(created_at) as date, SUM(total) as total')
                ->where('created_at', '>=', now()->subDays(30))
                ->groupBy('date')
                ->get();
        }

        $chartData = [];
        $labels = [];
        
        for ($i = 30; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $labels[] = now()->subDays($i)->format('d/m');
            
            $repairTotal = $repairs->firstWhere('date', $date)?->total ?? 0;
            $saleTotal = $sales->firstWhere('date', $date)?->total ?? 0;
            
            $chartData[] = $repairTotal + $saleTotal;
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
            ],
            'options' => [
                'maintainAspectRatio' => false,
                'plugins' => ['legend' => ['display' => false]],
                'scales' => ['y' => ['beginAtZero' => true, 'grid' => ['color' => '#f1f5f9']]]
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
                        'borderWidth' => 0
                    ]
                ]
            ],
            'options' => [
                'maintainAspectRatio' => false,
                'plugins' => ['legend' => ['position' => 'right', 'labels' => ['usePointStyle' => true, 'boxWidth' => 8]]]
            ]
        ];
    }

    public function with(): array
    {
        $isAdmin = auth()->user()->isAdmin();

        $repairRevenue = RepairOrder::whereIn('status', ['listo', 'entregado'])
            ->whereMonth('created_at', now()->month)
            ->sum('total_cost');
            
        $posRevenue = 0;
        if (Schema::hasTable('sales')) {
            $posRevenue = Sale::whereMonth('created_at', now()->month)->sum('total');
        }

        return [
            'revenue_month' => $isAdmin ? ($repairRevenue + $posRevenue) : 0,
            'active_orders' => RepairOrder::where('status', '!=', 'entregado')->count(),
            'total_clients' => Client::count(),
            'low_stock_parts' => Part::whereColumn('stock', '<=', 'stock_min')->count(),
            'latest_orders' => RepairOrder::with(['asset.client'])->latest()->take(5)->get(),
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

    <!-- STATS -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        
        @if(auth()->user()->isAdmin())
            <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200 flex items-center justify-between hover:shadow-md transition-shadow">
                <div>
                    <div class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Facturación Mes</div>
                    <div class="text-2xl font-black text-slate-900">${{ number_format($revenue_month, 2) }}</div>
                </div>
                <div class="p-3 bg-green-50 rounded-xl text-green-600">
                    <x-icon name="o-currency-dollar" class="w-6 h-6" />
                </div>
            </div>
        @endif

        <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200 flex items-center justify-between hover:shadow-md transition-shadow">
            <div>
                <div class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">En Taller</div>
                <div class="text-2xl font-black text-slate-900">{{ $active_orders }}</div>
            </div>
            <div class="p-3 bg-blue-50 rounded-xl text-blue-600">
                <x-icon name="o-wrench-screwdriver" class="w-6 h-6" />
            </div>
        </div>

        <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200 flex items-center justify-between hover:shadow-md transition-shadow">
            <div>
                <div class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Clientes</div>
                <div class="text-2xl font-black text-slate-900">{{ $total_clients }}</div>
            </div>
            <div class="p-3 bg-indigo-50 rounded-xl text-indigo-600">
                <x-icon name="o-users" class="w-6 h-6" />
            </div>
        </div>

        <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200 flex items-center justify-between hover:shadow-md transition-shadow">
            <div>
                <div class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Stock Crítico</div>
                <div class="text-2xl font-black text-slate-900">{{ $low_stock_parts }}</div>
            </div>
            <div class="p-3 bg-red-50 rounded-xl text-red-600">
                <x-icon name="o-exclamation-triangle" class="w-6 h-6" />
            </div>
        </div>
    </div>

    <!-- GRÁFICAS -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <div class="lg:col-span-2 bg-white p-6 rounded-xl shadow-sm border border-slate-200">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wide">Ingresos (30 días)</h3>
                <span class="text-xs font-medium text-slate-400">Actualizado hoy</span>
            </div>
            @if(auth()->user()->isAdmin())
                <div class="h-72 w-full">
                    <x-chart wire:model="incomeChart" class="h-full w-full" />
                </div>
            @else
                <div class="h-72 flex items-center justify-center bg-slate-50 rounded-xl border-2 border-dashed border-slate-200 text-slate-400">
                    <div class="text-center">
                        <div class="p-4 bg-white rounded-full inline-block mb-3 shadow-sm"><x-icon name="o-lock-closed" class="w-6 h-6 text-slate-300" /></div>
                        <div class="font-medium">Información Restringida</div>
                    </div>
                </div>
            @endif
        </div>

        <div class="lg:col-span-1 bg-white p-6 rounded-xl shadow-sm border border-slate-200">
            <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wide mb-6">Top Marcas</h3>
            <div class="h-72 w-full flex items-center justify-center">
                <x-chart wire:model="brandsChart" class="h-full w-full" />
            </div>
        </div>
    </div>

    <!-- TABLAS -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <x-card title="Últimos Ingresos" class="shadow-sm border border-slate-200" separator>
                <x-table :headers="$headers" :rows="$latest_orders" link="/orders/{id}" class="hover:bg-slate-50">
                    @scope('cell_id', $order)
                        <span class="font-bold text-slate-900 bg-slate-100 px-2 py-1 rounded text-xs font-mono">#{{ str_pad($order->id, 4, '0', STR_PAD_LEFT) }}</span>
                    @endscope
                    @scope('cell_status', $order)
                        <x-badge :value="$order->status_label" :class="'badge-' . $order->status_color" />
                    @endscope
                    @if(auth()->user()->isAdmin())
                        @scope('cell_total_cost', $order)
                            <span class="font-bold text-slate-900">$ {{ number_format($order->total_cost, 2) }}</span>
                        @endscope
                    @endif
                </x-table>
                <x-slot:actions>
                    <x-button label="Ver todas" link="/orders" class="btn-ghost btn-sm text-slate-500" />
                </x-slot:actions>
            </x-card>

            <x-card title="Mantenimiento Sugerido" subtitle="Equipos entregados hace > 3 meses" separator class="shadow-sm border border-slate-200 border-l-4 border-l-amber-400">
                <x-table :headers="$maintenance_headers" :rows="$maintenance_alerts">
                    @scope('cell_asset.client.name', $order)
                        <div>
                            <div class="font-bold text-slate-900">{{ $order->asset->client->name }}</div>
                            <div class="text-xs text-slate-500">{{ $order->asset->brand }} {{ $order->asset->model }}</div>
                        </div>
                    @endscope
                    @scope('cell_updated_at', $order)
                        <span class="text-xs font-medium bg-amber-50 text-amber-700 px-2 py-1 rounded border border-amber-100">Hace {{ $order->updated_at->diffInMonths() }} meses</span>
                    @endscope
                    @scope('actions', $order)
                        @if($order->asset->client->phone)
                            <x-button icon="o-chat-bubble-left" class="btn-sm btn-circle btn-success text-white shadow-sm"
                                link="https://wa.me/{{ preg_replace('/[^0-9]/', '', $order->asset->client->phone) }}?text=Hola..." external />
                        @endif
                    @endscope
                </x-table>
            </x-card>
        </div>

        <div class="lg:col-span-1 flex flex-col gap-4">
            <div class="bg-slate-900 rounded-xl p-6 text-white shadow-lg shadow-slate-900/20 cursor-pointer hover:scale-[1.02] transition-transform group" onclick="window.location='/orders'">
                <div class="flex justify-between items-center mb-4">
                    <div class="p-3 bg-white/10 rounded-lg group-hover:bg-white/20 transition"><x-icon name="o-plus" class="w-6 h-6" /></div>
                    <x-icon name="o-arrow-right" class="w-5 h-5 opacity-50 group-hover:translate-x-1 transition" />
                </div>
                <div class="font-bold text-lg">Nueva Recepción</div>
                <div class="text-sm opacity-60">Ingresar equipo al taller</div>
            </div>

            <div class="bg-blue-600 rounded-xl p-6 text-white shadow-lg shadow-blue-600/20 cursor-pointer hover:scale-[1.02] transition-transform group" onclick="window.location='/pos'">
                <div class="flex justify-between items-center mb-4">
                    <div class="p-3 bg-white/10 rounded-lg group-hover:bg-white/20 transition"><x-icon name="o-shopping-cart" class="w-6 h-6" /></div>
                    <x-icon name="o-arrow-right" class="w-5 h-5 opacity-50 group-hover:translate-x-1 transition" />
                </div>
                <div class="font-bold text-lg">Punto de Venta</div>
                <div class="text-sm opacity-80">Venta rápida de repuestos</div>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-6 hover:border-blue-500 hover:shadow-md transition-all cursor-pointer group" onclick="window.location='/parts'">
                <div class="flex justify-between items-center mb-4">
                    <div class="p-3 bg-slate-100 text-slate-600 rounded-lg group-hover:bg-blue-50 group-hover:text-blue-600 transition-colors"><x-icon name="o-archive-box" class="w-6 h-6" /></div>
                </div>
                <div class="font-bold text-lg text-slate-900">Inventario</div>
                <div class="text-sm text-slate-500 group-hover:text-blue-600 transition-colors">Ver stock y precios</div>
            </div>
        </div>
    </div>
</div>
