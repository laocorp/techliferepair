<?php

use Livewire\Volt\Component;
use App\Models\RepairOrder;
use App\Models\Sale; // <--- Importar el modelo Sale
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
        // Seguridad: Si es cliente, redirigir a su portal
        if (auth()->user()->isClient()) {
            $this->redirect(route('client.portal'), navigate: true);
            return; 
        }

        $this->buildIncomeChart();
        $this->buildBrandsChart();
    }

    public function buildIncomeChart(): void
    {
        // 1. Obtener ingresos diarios por Reparaciones
        $repairs = RepairOrder::selectRaw('DATE(created_at) as date, SUM(total_cost) as total')
            ->where('payment_status', 'paid') // Solo lo cobrado
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->get();

        // 2. Obtener ingresos diarios por Ventas POS
        $sales = Sale::selectRaw('DATE(created_at) as date, SUM(total) as total')
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->get();

        $chartData = [];
        $labels = [];
        
        // 3. Combinar ambos ingresos día a día
        for ($i = 30; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $labels[] = now()->subDays($i)->format('d/m'); // Etiqueta eje X
            
            $repairTotal = $repairs->firstWhere('date', $date)?->total ?? 0;
            $saleTotal = $sales->firstWhere('date', $date)?->total ?? 0;
            
            $chartData[] = $repairTotal + $saleTotal;
        }

        // Configuración Chart.js
        $this->incomeChart = [
            'type' => 'line',
            'data' => [
                'labels' => $labels,
                'datasets' => [
                    [
                        'label' => 'Ingresos Totales ($)',
                        'data' => $chartData,
                        'borderColor' => '#3b82f6', // Azul Tech
                        'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                        'fill' => true,
                        'tension' => 0.3 // Curva suave
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
        // Contamos marcas de equipos reparados
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

        // Calcular Ingresos Totales del Mes (Reparaciones + POS)
        $repairRevenue = RepairOrder::whereIn('status', ['listo', 'entregado'])
            ->whereMonth('created_at', now()->month)
            ->sum('total_cost');
            
        $posRevenue = Sale::whereMonth('created_at', now()->month)->sum('total');

        $totalRevenue = $repairRevenue + $posRevenue;

        return [
            'revenue_month' => $isAdmin ? $totalRevenue : 0,

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

    <!-- FILA 1: TARJETAS DE ESTADÍSTICAS -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        
        @if(auth()->user()->isAdmin())
            <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200 flex items-center justify-between">
                <div>
                    <div class="text-sm text-slate-500 font-medium mb-1">Facturación Mes</div>
                    {{-- Aquí se muestra la suma total de Reparaciones + POS --}}
                    <div class="text-2xl font-black text-slate-900">${{ number_format($revenue_month, 2) }}</div>
                </div>
                <div class="p-3 bg-green-50 rounded-lg text-green-600">
                    <x-icon name="o-currency-dollar" class="w-6 h-6" />
                </div>
            </div>
        @endif

        <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200 flex items-center justify-between">
            <div>
                <div class="text-sm text-slate-500 font-medium mb-1">En Taller</div>
                <div class="text-2xl font-black text-slate-900">{{ $active_orders }}</div>
            </div>
            <div class="p-3 bg-blue-50 rounded-lg text-blue-600">
                <x-icon name="o-wrench-screwdriver" class="w-6 h-6" />
            </div>
        </div>

        <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200 flex items-center justify-between">
            <div>
                <div class="text-sm text-slate-500 font-medium mb-1">Clientes</div>
                <div class="text-2xl font-black text-slate-900">{{ $total_clients }}</div>
            </div>
            <div class="p-3 bg-indigo-50 rounded-lg text-indigo-600">
                <x-icon name="o-users" class="w-6 h-6" />
            </div>
        </div>

        <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200 flex items-center justify-between">
            <div>
                <div class="text-sm text-slate-500 font-medium mb-1">Stock Crítico</div>
                <div class="text-2xl font-black text-slate-900">{{ $low_stock_parts }}</div>
            </div>
            <div class="p-3 bg-red-50 rounded-lg text-red-600">
                <x-icon name="o-exclamation-triangle" class="w-6 h-6" />
            </div>
        </div>
    </div>

    <!-- FILA 2: GRÁFICAS -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <div class="lg:col-span-2 bg-white p-6 rounded-xl shadow-sm border border-slate-200">
            <h3 class="text-sm font-bold text-slate-700 uppercase tracking-wide mb-4">Ingresos (30 días)</h3>
            @if(auth()->user()->isAdmin())
                <div class="h-64 w-full">
                    <x-chart wire:model="incomeChart" class="h-full w-full" />
                </div>
            @else
                <div class="h-64 flex items-center justify-center bg-slate-50 rounded-lg border border-dashed border-slate-200 text-slate-400">
                    <div class="text-center">
                        <x-icon name="o-lock-closed" class="w-8 h-8 mx-auto mb-2" />
                        <div>Información Restringida</div>
                    </div>
                </div>
            @endif
        </div>

        <div class="lg:col-span-1 bg-white p-6 rounded-xl shadow-sm border border-slate-200">
            <h3 class="text-sm font-bold text-slate-700 uppercase tracking-wide mb-4">Top Marcas</h3>
            <div class="h-64 w-full">
                <x-chart wire:model="brandsChart" class="h-full w-full" />
            </div>
        </div>
    </div>

    <!-- FILA 3: TABLAS -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            
            <!-- TABLA PRINCIPAL -->
            <x-card title="Últimos Ingresos" class="shadow-sm border border-slate-200" separator>
                <x-table :headers="$headers" :rows="$latest_orders" link="/orders/{id}">
                    @scope('cell_id', $order)
                        <span class="font-bold text-slate-900">OT-{{ str_pad($order->id, 4, '0', STR_PAD_LEFT) }}</span>
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

            <!-- TABLA MANTENIMIENTO -->
            <x-card title="Oportunidades de Mantenimiento" subtitle="> 3 meses" separator class="shadow-sm border border-slate-200 border-l-4 border-l-amber-400">
                <x-table :headers="$maintenance_headers" :rows="$maintenance_alerts">
                    @scope('cell_asset.client.name', $order)
                        <div>
                            <div class="font-bold">{{ $order->asset->client->name }}</div>
                            <div class="text-xs text-slate-400">{{ $order->asset->brand }} {{ $order->asset->model }}</div>
                        </div>
                    @endscope
                    @scope('cell_updated_at', $order)
                        <span class="text-xs font-mono bg-slate-100 px-2 py-1 rounded">Hace {{ $order->updated_at->diffInMonths() }} meses</span>
                    @endscope
                    @scope('actions', $order)
                        @if($order->asset->client->phone)
                            <x-button icon="o-chat-bubble-left" class="btn-sm btn-circle btn-success text-white"
                                link="https://wa.me/{{ preg_replace('/[^0-9]/', '', $order->asset->client->phone) }}?text=Hola..." external />
                        @endif
                    @endscope
                </x-table>
            </x-card>
        </div>

        <!-- ACCESOS RÁPIDOS -->
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

            <div class="bg-white border border-slate-200 rounded-xl p-6 hover:border-blue-400 transition-colors cursor-pointer group" onclick="window.location='/parts'">
                <div class="flex justify-between items-center mb-4">
                    <div class="p-3 bg-blue-50 text-blue-600 rounded-lg group-hover:bg-blue-600 group-hover:text-white transition-colors"><x-icon name="o-archive-box" class="w-6 h-6" /></div>
                </div>
                <div class="font-bold text-lg text-slate-900">Inventario</div>
                <div class="text-sm text-slate-500">Ver stock y precios</div>
            </div>
        </div>
    </div>
</div>
