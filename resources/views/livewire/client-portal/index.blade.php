<?php

use Livewire\Volt\Component;
use App\Models\RepairOrder;
use Livewire\Attributes\Layout;

new 
#[Layout('layouts.app')] 
class extends Component {
    
    public function with(): array
    {
        // Buscar órdenes vinculadas al cliente logueado
        return [
            'my_orders' => RepairOrder::whereHas('asset', function($q) {
                $q->where('client_id', auth()->user()->client_id);
            })->latest()->get(),
            
            'headers' => [
                ['key' => 'id', 'label' => '# Orden'],
                ['key' => 'asset.model', 'label' => 'Equipo'],
                ['key' => 'status', 'label' => 'Estado Técnico'],
                ['key' => 'payment_status', 'label' => 'Estado Pago'],
                ['key' => 'total_cost', 'label' => 'Monto'],
            ]
        ];
    }
}; ?>

<div>
    <x-header title="Mi Portal" subtitle="Historial de mis reparaciones" separator />

    <div class="grid gap-6">
        <x-card>
            <x-table :headers="$headers" :rows="$my_orders" striped>
                
                @scope('cell_id', $order)
                    <span class="font-bold">#{{ str_pad($order->id, 4, '0', STR_PAD_LEFT) }}</span>
                @endscope

                @scope('cell_status', $order)
                    <x-badge :value="$order->status_label" :class="'badge-' . $order->status_color" />
                @endscope

                @scope('cell_payment_status', $order)
                    @if($order->payment_status == 'pending')
                        <x-badge value="PENDIENTE DE PAGO" class="badge-error font-bold" />
                    @else
                        <x-badge value="PAGADO" class="badge-success font-bold" />
                    @endif
                @endscope

                @scope('cell_total_cost', $order)
                    $ {{ number_format($order->total_cost, 2) }}
                @endscope
                
                @scope('actions', $order)
                    <x-button icon="o-printer" class="btn-sm btn-ghost" link="{{ route('orders.print', $order) }}" external tooltip="Descargar PDF" />
                @endscope

            </x-table>
            
            <x-slot:empty>
                <div class="text-center p-10">
                    <x-icon name="o-face-smile" class="w-10 h-10 text-gray-400" />
                    <p class="mt-2">No tienes órdenes registradas aún.</p>
                </div>
            </x-slot:empty>
        </x-card>
    </div>
</div>
