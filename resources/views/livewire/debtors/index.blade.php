<?php

use Livewire\Volt\Component;
use App\Models\RepairOrder;
use Mary\Traits\Toast;
use Livewire\Attributes\Layout;

new 
#[Layout('layouts.app')]
class extends Component {
    use Toast;

    public string $search = '';

    public function headers(): array
    {
        return [
            ['key' => 'id', 'label' => '#OT', 'class' => 'font-bold w-1'],
            ['key' => 'asset.client.name', 'label' => 'Cliente Deudor'],
            ['key' => 'asset.model', 'label' => 'Equipo Entregado/Listo'],
            ['key' => 'status', 'label' => 'Estado Técnico'],
            ['key' => 'total_cost', 'label' => 'Deuda', 'class' => 'text-right font-black text-red-600'],
            ['key' => 'updated_at', 'label' => 'Fecha'],
        ];
    }

    public function with(): array
    {
        // Buscamos órdenes que NO están pagadas
        $query = RepairOrder::query()
            ->with(['asset.client'])
            ->where('payment_status', 'pending') // Filtro clave
            ->where('total_cost', '>', 0) // Que tengan costo
            ->orderBy('updated_at', 'desc');

        if ($this->search) {
            $query->whereHas('asset.client', fn($q) => $q->where('name', 'like', "%$this->search%"));
        }

        $debtors = $query->get();
        $total_debt = $debtors->sum('total_cost');

        return [
            'debtors' => $debtors,
            'total_debt' => $total_debt,
            'headers' => $this->headers()
        ];
    }
}; ?>

<div>
    <x-header title="Cuentas por Cobrar" subtitle="Gestión de cartera y cobros pendientes" separator>
        <x-slot:middle class="!justify-end">
            <x-input icon="o-magnifying-glass" placeholder="Buscar cliente..." wire:model.live.debounce="search" />
        </x-slot:middle>
    </x-header>

    <!-- RESUMEN DE DEUDA -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
        <div class="bg-red-50 border border-red-100 p-6 rounded-xl shadow-sm flex items-center justify-between col-span-1">
            <div>
                <div class="text-xs font-bold text-red-600 uppercase tracking-wider opacity-80">Deuda Total</div>
                <div class="text-3xl font-black text-red-700">${{ number_format($total_debt, 2) }}</div>
                <div class="text-xs text-red-400 mt-1">{{ count($debtors) }} órdenes pendientes</div>
            </div>
            <div class="p-3 bg-white rounded-lg text-red-500 shadow-sm">
                <x-icon name="o-banknotes" class="w-8 h-8" />
            </div>
        </div>
    </div>

    <x-card>
        <x-table :headers="$headers" :rows="$debtors" striped link="/orders/{id}" class="cursor-pointer hover:bg-slate-50">
            
            @scope('cell_id', $order)
                <span class="font-mono font-bold text-slate-700">#{{ str_pad($order->id, 4, '0', STR_PAD_LEFT) }}</span>
            @endscope

            @scope('cell_asset.client.name', $order)
                <div>
                    <div class="font-bold text-slate-900">{{ $order->asset->client->name }}</div>
                    <div class="text-xs text-slate-500">{{ $order->asset->client->phone }}</div>
                </div>
            @endscope

            @scope('cell_status', $order)
                <x-badge :value="$order->status_label" :class="'badge-' . $order->status_color" />
            @endscope

            @scope('cell_total_cost', $order)
                <span class="text-red-600 font-bold">$ {{ number_format($order->total_cost, 2) }}</span>
            @endscope

            @scope('cell_updated_at', $order)
                <span class="text-xs text-slate-400">{{ $order->updated_at->format('d/m/Y') }}</span>
            @endscope
            
            @scope('actions', $order)
                <div class="flex justify-end" onclick="event.stopPropagation()">
                    @if($order->asset->client->phone)
                        <x-button 
                            icon="o-chat-bubble-left-ellipsis" 
                            class="btn-sm btn-circle btn-success text-white shadow-sm" 
                            tooltip="Enviar Cobro WhatsApp"
                            external
                            link="https://wa.me/{{ preg_replace('/[^0-9]/', '', $order->asset->client->phone) }}?text=Hola {{ $order->asset->client->name }}, le escribimos de {{ $settings->company_name ?? 'TechLife' }}. Tiene un saldo pendiente de ${{ number_format($order->total_cost, 2) }} por la orden #{{ str_pad($order->id, 4, '0', STR_PAD_LEFT) }} ({{ $order->asset->model }}). Por favor ayúdenos con el pago." 
                        />
                    @endif
                </div>
            @endscope

            <x-slot:empty>
                <div class="py-12 flex flex-col items-center justify-center text-slate-400">
                    <div class="w-16 h-16 bg-green-50 rounded-full flex items-center justify-center mb-4">
                        <x-icon name="o-check-circle" class="w-8 h-8 text-green-500" />
                    </div>
                    <div class="font-medium text-slate-600">¡Excelente! No hay deudas pendientes.</div>
                </div>
            </x-slot:empty>
        </x-table>
    </x-card>
</div>
