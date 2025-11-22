<?php

use Livewire\Volt\Component;
use App\Models\Sale;
use Mary\Traits\Toast;
use Livewire\Attributes\Layout;

new 
#[Layout('layouts.app')]
class extends Component {
    use Toast;

    public string $search = '';
    
    // Variables para el detalle de la venta (Drawer)
    public bool $drawer = false;
    public ?Sale $selected_sale = null;

    public function showDetails(Sale $sale): void
    {
        $this->selected_sale = $sale->load('items.part', 'user');
        $this->drawer = true;
    }

    public function headers(): array
    {
        return [
            ['key' => 'id', 'label' => '# Ticket', 'class' => 'font-bold w-1'],
            ['key' => 'created_at', 'label' => 'Fecha'],
            ['key' => 'user.name', 'label' => 'Vendedor'],
            ['key' => 'payment_method', 'label' => 'Método'],
            ['key' => 'total', 'label' => 'Total', 'class' => 'text-right font-bold'],
        ];
    }

    public function with(): array
    {
        return [
            'sales' => Sale::with(['user'])
                ->orderBy('id', 'desc')
                ->paginate(20), // Paginación para que no se sature si vendes mucho
            'headers' => $this->headers()
        ];
    }
}; ?>

<div>
    <x-header title="Historial de Ventas" subtitle="Registro de transacciones de mostrador (POS)" separator>
        <x-slot:actions>
            <x-button label="Nueva Venta" icon="o-shopping-cart" link="/pos" class="btn-primary" />
        </x-slot:actions>
    </x-header>

    <x-card>
        <x-table :headers="$headers" :rows="$sales" striped @row-click="$wire.showDetails($event.detail.id)" class="cursor-pointer">
            
            @scope('cell_id', $sale)
                <span class="font-mono text-slate-600">#{{ str_pad($sale->id, 6, '0', STR_PAD_LEFT) }}</span>
            @endscope

            @scope('cell_created_at', $sale)
                {{ $sale->created_at->format('d/m/Y H:i') }}
            @endscope

            @scope('cell_payment_method', $sale)
                <div class="badge badge-ghost text-xs uppercase">{{ $sale->payment_method }}</div>
            @endscope

            @scope('cell_total', $sale)
                <span class="text-slate-900 text-lg">${{ number_format($sale->total, 2) }}</span>
            @endscope
            
            @scope('actions', $sale)
                <x-button icon="o-printer" class="btn-sm btn-ghost text-slate-400 hover:text-blue-600" link="{{ route('pos.print', $sale) }}" external tooltip="Imprimir Ticket" />
            @endscope

        </x-table>

        <!-- Paginación -->
        <div class="mt-4">
            {{ $sales->links() }}
        </div>
    </x-card>

    <!-- DRAWER DETALLE DE VENTA -->
    <x-drawer wire:model="drawer" title="Detalle de Venta" right class="w-full lg:w-1/3">
        @if($selected_sale)
            <div class="space-y-6">
                
                <div class="bg-slate-50 p-4 rounded-xl border border-slate-100">
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-xs font-bold text-slate-400 uppercase">Ticket #</span>
                        <span class="font-mono font-bold text-slate-900">{{ str_pad($selected_sale->id, 6, '0', STR_PAD_LEFT) }}</span>
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
                </div>

                <!-- Lista de Productos -->
                <div>
                    <h3 class="text-sm font-bold text-slate-900 mb-3 uppercase tracking-wide">Productos Vendidos</h3>
                    <div class="border rounded-lg overflow-hidden">
                        <table class="w-full text-sm">
                            <thead class="bg-slate-50 text-slate-500 border-b">
                                <tr>
                                    <th class="text-left p-2 font-medium">Desc</th>
                                    <th class="text-center p-2 font-medium">Cant</th>
                                    <th class="text-right p-2 font-medium">Total</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y">
                                @foreach($selected_sale->items as $item)
                                    <tr>
                                        <td class="p-2">
                                            <div class="font-bold text-slate-800">{{ $item->part->name }}</div>
                                            <div class="text-xs text-slate-400">{{ $item->part->sku }}</div>
                                        </td>
                                        <td class="p-2 text-center">{{ $item->quantity }}</td>
                                        <td class="p-2 text-right font-medium">${{ number_format($item->price * $item->quantity, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Total -->
                <div class="flex justify-between items-end pt-4 border-t border-dashed border-slate-300">
                    <span class="text-lg font-bold text-slate-500">TOTAL PAGADO</span>
                    <span class="text-3xl font-black text-slate-900">${{ number_format($selected_sale->total, 2) }}</span>
                </div>

                <!-- Acciones -->
                <div class="grid grid-cols-1 gap-2">
                    <x-button label="Reimprimir Ticket" icon="o-printer" class="btn-primary w-full" link="{{ route('pos.print', $selected_sale) }}" external />
                    <x-button label="Cerrar" wire:click="$toggle('drawer')" class="w-full" />
                </div>

            </div>
        @endif
    </x-drawer>
</div>
