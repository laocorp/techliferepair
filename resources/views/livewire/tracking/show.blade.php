<?php

use Livewire\Volt\Component;
use App\Models\RepairOrder;
use Livewire\Attributes\Layout;

new 
#[Layout('layouts.guest')] // Importante: Layout público
class extends Component {
    
    public string $token = '';
    public ?RepairOrder $order = null;

    public function mount($token): void
    {
        $this->token = $token;
        // Buscamos por el token secreto, no por ID
        $this->order = RepairOrder::where('tracking_token', $token)->with('asset')->firstOrFail();
    }
}; ?>

<div class="min-h-screen bg-gray-50 py-10 px-4">
    <div class="max-w-lg mx-auto">
        
        <div class="text-center mb-8">
            <div class="inline-flex p-3 bg-slate-900 rounded-xl text-white mb-4">
                <x-icon name="o-wrench-screwdriver" class="w-8 h-8" />
            </div>
            <h1 class="text-2xl font-black text-slate-900">Estado de Reparación</h1>
            <p class="text-gray-500 text-sm">Orden #{{ str_pad($order->id, 4, '0', STR_PAD_LEFT) }}</p>
        </div>

        <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
            
            <div class="p-6 text-center 
                @if($order->status == 'listo') bg-green-500 
                @elseif($order->status == 'entregado') bg-slate-600 
                @elseif($order->status == 'espera_repuestos') bg-red-500
                @else bg-blue-600 @endif text-white">
                
                <div class="uppercase tracking-widest text-xs font-bold opacity-80 mb-1">Estado Actual</div>
                <div class="text-3xl font-black uppercase">{{ str_replace('_', ' ', $order->status) }}</div>
            </div>

            <div class="p-8 space-y-6">
                
                <div>
                    <div class="text-xs font-bold text-gray-400 uppercase mb-1">Equipo</div>
                    <div class="text-xl font-bold text-slate-900">{{ $order->asset->brand }} {{ $order->asset->model }}</div>
                    <div class="text-sm text-gray-500">Serial: {{ $order->asset->serial_number }}</div>
                </div>

                <hr class="border-gray-100">

                @if($order->diagnosis_notes)
                <div>
                    <div class="text-xs font-bold text-gray-400 uppercase mb-2">Informe Técnico</div>
                    <div class="bg-gray-50 p-4 rounded-xl text-sm text-gray-700 border border-gray-200">
                        {{ $order->diagnosis_notes }}
                    </div>
                </div>
                @endif

                @if($order->total_cost > 0)
                <div class="flex justify-between items-end pt-4">
                    <div class="text-xs font-bold text-gray-400 uppercase">Total a Pagar</div>
                    <div class="text-3xl font-black text-slate-900">${{ number_format($order->total_cost, 2) }}</div>
                </div>
                @endif

            </div>
            
            <div class="bg-gray-50 p-4 text-center text-xs text-gray-400 border-t border-gray-100">
                Actualizado: {{ $order->updated_at->diffForHumans() }}
            </div>
        </div>

        <div class="text-center mt-8">
            <a href="/" class="text-sm font-bold text-blue-600 hover:underline">Volver al Inicio</a>
        </div>

    </div>
</div>
