<?php

use Livewire\Volt\Component;
use App\Models\Client;
use App\Models\RepairOrder;
use App\Models\Asset;
use Livewire\Attributes\Layout;

new 
#[Layout('layouts.app')]
class extends Component {
    
    public Client $client;

    public function mount(Client $client): void
    {
        $this->client = $client;
    }

    public function with(): array
    {
        return [
            // 1. Órdenes de este cliente (Las buscamos a través de sus equipos)
            'orders' => RepairOrder::whereHas('asset', function($q) {
                $q->where('client_id', $this->client->id);
            })->orderBy('id', 'desc')->get(),

            // 2. Equipos de este cliente
            'assets' => Asset::where('client_id', $this->client->id)->get(),

            // 3. Estadísticas
            'total_spent' => RepairOrder::whereHas('asset', fn($q) => $q->where('client_id', $this->client->id))
                ->sum('total_cost'),
            
            'active_count' => RepairOrder::whereHas('asset', fn($q) => $q->where('client_id', $this->client->id))
                ->where('status', '!=', 'entregado')
                ->count()
        ];
    }

    // Encabezados para la tabla de Órdenes
    public function ordersHeaders(): array
    {
        return [
            ['key' => 'id', 'label' => '#OT', 'class' => 'font-bold'],
            ['key' => 'asset.model', 'label' => 'Equipo'],
            ['key' => 'status', 'label' => 'Estado'],
            ['key' => 'total_cost', 'label' => 'Total', 'class' => 'text-right'],
            ['key' => 'created_at', 'label' => 'Fecha'],
        ];
    }

    // Encabezados para la tabla de Equipos
    public function assetsHeaders(): array
    {
        return [
            ['key' => 'serial_number', 'label' => 'Serial'],
            ['key' => 'brand', 'label' => 'Marca'],
            ['key' => 'model', 'label' => 'Modelo'],
            ['key' => 'type', 'label' => 'Tipo'],
        ];
    }
}; ?>

<div>
    <!-- ENCABEZADO CON NAVEGACIÓN -->
    <x-header title="Perfil del Cliente" separator>
        <x-slot:middle class="!justify-start">
            <div class="text-sm breadcrumbs text-gray-500">
                <ul>
                    <li><a href="/clients">Clientes</a></li>
                    <li class="font-bold text-primary">{{ $client->name }}</li>
                </ul>
            </div>
        </x-slot:middle>
    </x-header>

    <div class="grid lg:grid-cols-3 gap-8">
        
        <!-- COLUMNA IZQUIERDA: DATOS Y RESUMEN -->
        <div class="lg:col-span-1 space-y-6">
            
            <!-- Tarjeta de Contacto -->
            <x-card class="shadow-xl border-t-4 border-primary">
                <div class="text-center mb-4">
                    <div class="avatar placeholder mb-2">
                        <div class="bg-neutral text-neutral-content rounded-full w-20">
                            <span class="text-3xl">{{ substr($client->name, 0, 1) }}</span>
                        </div>
                    </div>
                    <h2 class="text-xl font-black">{{ $client->name }}</h2>
                    <div class="badge badge-ghost mt-2">{{ $client->tax_id ?? 'Sin RUC' }}</div>
                </div>

                <div class="divider"></div>

                <div class="space-y-3 text-sm">
                    <div class="flex items-center gap-3">
                        <x-icon name="o-phone" class="text-gray-400" />
                        <span class="font-bold">{{ $client->phone ?? '---' }}</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <x-icon name="o-envelope" class="text-gray-400" />
                        <span>{{ $client->email ?? '---' }}</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <x-icon name="o-map-pin" class="text-gray-400" />
                        <span>{{ $client->address ?? '---' }}</span>
                    </div>
                </div>

                <!-- Botón Rápido de WhatsApp -->
                @if($client->phone)
                    <x-button 
                        label="Contactar Cliente" 
                        icon="o-chat-bubble-left-right" 
                        class="btn-success text-white w-full mt-6" 
                        link="https://wa.me/{{ preg_replace('/[^0-9]/', '', $client->phone) }}" 
                        external 
                    />
                @endif
            </x-card>

            <!-- Estadísticas Rápidas -->
            <div class="grid grid-cols-2 gap-4">
                <div class="bg-base-200 p-4 rounded-xl text-center border border-base-300">
                    <div class="text-xs text-gray-500 uppercase font-bold">Inversión Total</div>
                    <div class="text-xl font-black text-success">${{ number_format($total_spent, 2) }}</div>
                </div>
                <div class="bg-base-200 p-4 rounded-xl text-center border border-base-300">
                    <div class="text-xs text-gray-500 uppercase font-bold">En Taller</div>
                    <div class="text-xl font-black text-warning">{{ $active_count }}</div>
                </div>
            </div>
        </div>

        <!-- COLUMNA DERECHA: PESTAÑAS DE INFORMACIÓN -->
        <div class="lg:col-span-2">
            <x-tabs selected="tab-orders">
                
                <!-- PESTAÑA 1: HISTORIAL DE ÓRDENES -->
                <x-tab name="tab-orders" label="Historial de Órdenes" icon="o-clipboard-document-list">
                    <x-card class="mt-4 shadow-xl">
                        <x-table :headers="$this->ordersHeaders()" :rows="$orders" link="/orders/{id}" striped>
                            
                            @scope('cell_id', $order)
                                <span class="font-bold text-primary">OT-{{ str_pad($order->id, 4, '0', STR_PAD_LEFT) }}</span>
                            @endscope

                            @scope('cell_status', $order)
                                <x-badge :value="$order->status_label" :class="'badge-' . $order->status_color" />
                            @endscope

                            @scope('cell_total_cost', $order)
                                $ {{ number_format($order->total_cost, 2) }}
                            @endscope

                            @scope('cell_created_at', $order)
                                {{ $order->created_at->format('d/m/Y') }}
                            @endscope

                            <x-slot:empty>
                                <div class="text-center py-10 text-gray-500">
                                    <x-icon name="o-face-smile" class="w-10 h-10 mb-2" />
                                    <div>Este cliente no tiene reparaciones registradas.</div>
                                </div>
                            </x-slot:empty>
                        </x-table>
                    </x-card>
                </x-tab>

                <!-- PESTAÑA 2: EQUIPOS DEL CLIENTE -->
                <x-tab name="tab-assets" label="Mis Equipos" icon="o-wrench-screwdriver">
                    <x-card class="mt-4 shadow-xl">
                        <div class="flex justify-end mb-4">
                            <!-- Truco: Enviamos el ID del cliente en la URL para que el formulario ya sepa de quién es -->
                            <!-- Nota: Esto requiere un pequeño ajuste en Assets/Index si quieres que se prellene, 
                                 pero por ahora solo muestra la lista -->
                        </div>

                        <x-table :headers="$this->assetsHeaders()" :rows="$assets" striped>
                            @scope('cell_serial_number', $asset)
                                <span class="font-mono bg-base-200 px-2 py-1 rounded">{{ $asset->serial_number }}</span>
                            @endscope

                            <x-slot:empty>
                                <div class="text-center py-10 text-gray-500">
                                    No hay equipos registrados a su nombre.
                                </div>
                            </x-slot:empty>
                        </x-table>
                    </x-card>
                </x-tab>
            </x-tabs>
        </div>

    </div>
</div>
