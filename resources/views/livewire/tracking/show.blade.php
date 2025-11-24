<?php

use Livewire\Volt\Component;
use App\Models\RepairOrder;
use Livewire\Attributes\Layout;

new 
#[Layout('layouts.guest')] // Usamos el layout Guest v5.0 (Blanco/Inter)
class extends Component {
    
    public string $token = '';
    public ?RepairOrder $order = null;

    public function mount($token): void
    {
        // Buscar orden o fallar elegantemente
        $this->order = RepairOrder::where('tracking_token', $token)->with('asset.client')->firstOrFail();
        $this->token = $token;
    }

    // Configuración visual de los pasos
    public function getStepsProperty()
    {
        return [
            ['status' => 'recibido', 'label' => 'Recibido', 'icon' => 'o-inbox-arrow-down', 'desc' => 'Ingreso al taller'],
            ['status' => 'diagnostico', 'label' => 'Diagnóstico', 'icon' => 'o-magnifying-glass', 'desc' => 'Evaluación técnica'],
            ['status' => 'espera_repuestos', 'label' => 'Repuestos', 'icon' => 'o-clock', 'desc' => 'Esperando piezas'],
            ['status' => 'listo', 'label' => 'Listo', 'icon' => 'o-check-badge', 'desc' => 'Disponible para retiro'],
            ['status' => 'entregado', 'label' => 'Entregado', 'icon' => 'o-home', 'desc' => 'Proceso finalizado'],
        ];
    }

    // Lógica para saber si el paso está completado o activo
    public function getStepStatus($stepStatus)
    {
        $statuses = ['recibido', 'diagnostico', 'espera_repuestos', 'listo', 'entregado'];
        $currentIndex = array_search($this->order->status, $statuses);
        $stepIndex = array_search($stepStatus, $statuses);
        
        // Caso especial: 'espera_repuestos' es un estado intermedio opcional o paralelo a diagnostico
        // Para simplificar visualmente:
        if ($this->order->status == 'espera_repuestos' && $stepStatus == 'diagnostico') return 'completed';
        
        if ($stepIndex < $currentIndex) return 'completed';
        if ($stepIndex === $currentIndex) return 'current';
        return 'pending';
    }
    
    // Color del encabezado según estado
    public function getStatusConfigProperty()
    {
        return match($this->order->status) {
            'recibido' => ['color' => 'bg-blue-600', 'label' => 'Recibido'],
            'diagnostico' => ['color' => 'bg-amber-500', 'label' => 'En Diagnóstico'],
            'espera_repuestos' => ['color' => 'bg-orange-600', 'label' => 'Espera Repuestos'],
            'listo' => ['color' => 'bg-emerald-600', 'label' => 'Listo para Retiro'],
            'entregado' => ['color' => 'bg-slate-800', 'label' => 'Entregado'],
            default => ['color' => 'bg-slate-500', 'label' => 'Desconocido'],
        };
    }
}; ?>

<div class="min-h-screen bg-slate-50 py-12 px-4 sm:px-6 lg:px-8">
    
    <div class="max-w-3xl mx-auto">
        
        <!-- LOGO CENTRADO -->
        <div class="text-center mb-10">
            <a href="/" class="inline-flex items-center gap-2 group">
                <div class="bg-slate-900 text-white p-2.5 rounded-xl shadow-xl shadow-slate-900/10 transition-transform group-hover:scale-105">
                    <x-icon name="o-wrench-screwdriver" class="w-6 h-6" />
                </div>
                <span class="font-black text-2xl tracking-tight text-slate-900">TECHLIFE</span>
            </a>
        </div>

        <!-- TARJETA PRINCIPAL -->
        <div class="bg-white rounded-2xl shadow-2xl shadow-slate-200/50 overflow-hidden border border-slate-100">
            
            <!-- HEADER DE ESTADO -->
            <div class="{{ $this->statusConfig['color'] }} p-8 text-center text-white relative overflow-hidden">
                <div class="absolute inset-0 bg-black/5"></div> <!-- Textura sutil -->
                <div class="absolute -right-10 -top-10 text-white/10 transform rotate-12">
                    <x-icon name="o-clipboard-document-check" class="w-40 h-40" />
                </div>

                <div class="relative z-10">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/20 backdrop-blur-sm border border-white/10 text-xs font-bold uppercase tracking-widest mb-3">
                        Orden #{{ str_pad($order->id, 4, '0', STR_PAD_LEFT) }}
                    </div>
                    <h1 class="text-4xl md:text-5xl font-black tracking-tight uppercase">
                        {{ $this->statusConfig['label'] }}
                    </h1>
                    <p class="mt-2 text-white/80 text-sm font-medium">
                        Actualizado {{ $order->updated_at->diffForHumans() }}
                    </p>
                </div>
            </div>

            <div class="p-6 sm:p-10">
                
                <!-- TIMELINE (LÍNEA DE TIEMPO) -->
                <div class="mb-12">
                    <div class="relative">
                        <!-- Línea base gris -->
                        <div class="absolute top-5 left-0 w-full h-1 bg-slate-100 rounded-full"></div>
                        
                        <!-- Línea de progreso de color -->
                        @php
                            $statuses = ['recibido', 'diagnostico', 'espera_repuestos', 'listo', 'entregado'];
                            $currentIndex = array_search($order->status, $statuses);
                            $progress = ($currentIndex / (count($statuses) - 1)) * 100;
                        @endphp
                        <div class="absolute top-5 left-0 h-1 {{ $this->statusConfig['color'] }} rounded-full transition-all duration-1000 ease-out shadow-sm" style="width: {{ $progress }}%"></div>

                        <div class="relative z-10 flex justify-between w-full">
                            @foreach($this->steps as $step)
                                @php $statusState = $this->getStepStatus($step['status']); @endphp
                                <div class="flex flex-col items-center group w-16 sm:w-20">
                                    
                                    <!-- Círculo del paso -->
                                    <div class="w-10 h-10 rounded-full flex items-center justify-center border-4 transition-all duration-500 z-10 bg-white
                                        {{ $statusState === 'completed' ? $this->statusConfig['color'] . ' border-transparent text-white shadow-md' : '' }}
                                        {{ $statusState === 'current' ? 'border-' . str_replace('bg-', '', $this->statusConfig['color']) . ' text-slate-700 scale-110 ring-4 ring-slate-50' : '' }}
                                        {{ $statusState === 'pending' ? 'border-slate-100 text-slate-300' : '' }}
                                    ">
                                        <x-icon name="{{ $step['icon'] }}" class="w-4 h-4" />
                                    </div>
                                    
                                    <!-- Etiqueta -->
                                    <div class="mt-3 text-center hidden sm:block">
                                        <div class="text-[10px] font-bold uppercase tracking-wider {{ $statusState === 'current' ? 'text-slate-900' : 'text-slate-400' }}">
                                            {{ $step['label'] }}
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        
                        <!-- Etiqueta móvil del paso actual -->
                        <div class="sm:hidden text-center mt-4 p-3 bg-slate-50 rounded-lg border border-slate-100">
                            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block mb-1">Paso Actual</span>
                            <span class="font-black text-slate-800">{{ $this->statusConfig['label'] }}</span>
                        </div>
                    </div>
                </div>

                <!-- GRID DE DETALLES -->
                <div class="grid md:grid-cols-2 gap-10">
                    
                    <!-- COLUMNA 1: EQUIPO -->
                    <div class="space-y-6">
                        <div class="flex items-center gap-2 pb-2 border-b border-slate-100">
                            <x-icon name="o-cube" class="w-4 h-4 text-slate-400" />
                            <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest">Detalles del Equipo</h3>
                        </div>

                        <div>
                            <div class="text-2xl font-bold text-slate-900 leading-tight">{{ $order->asset->brand }} {{ $order->asset->model }}</div>
                            <div class="flex flex-wrap gap-2 mt-2">
                                <span class="px-2 py-1 rounded-md bg-slate-100 text-slate-600 text-xs font-mono font-bold border border-slate-200">SN: {{ $order->asset->serial_number }}</span>
                                <span class="px-2 py-1 rounded-md bg-slate-100 text-slate-600 text-xs font-bold border border-slate-200">{{ $order->asset->type ?? 'Equipo General' }}</span>
                            </div>
                        </div>

                        <div class="bg-slate-50 p-4 rounded-xl border border-slate-100 relative">
                            <div class="absolute top-4 left-4 text-slate-300"><x-icon name="o-chat-bubble-bottom-center-text" class="w-5 h-5" /></div>
                            <div class="pl-8">
                                <div class="text-xs font-bold text-slate-400 uppercase mb-1">Problema Reportado</div>
                                <p class="text-slate-700 text-sm italic leading-relaxed">"{{ $order->problem_description }}"</p>
                            </div>
                        </div>
                    </div>

                    <!-- COLUMNA 2: INFORME Y COSTOS -->
                    <div class="space-y-6">
                         <div class="flex items-center gap-2 pb-2 border-b border-slate-100">
                            <x-icon name="o-clipboard-document-check" class="w-4 h-4 text-slate-400" />
                            <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest">Informe Técnico</h3>
                        </div>

                        @if($order->diagnosis_notes)
                            <div class="prose prose-sm text-slate-600 leading-relaxed">
                                <p>{{ $order->diagnosis_notes }}</p>
                            </div>
                        @else
                             <div class="flex flex-col items-center justify-center py-8 text-center bg-slate-50 rounded-xl border border-dashed border-slate-200">
                                <div class="animate-pulse bg-slate-200 rounded-full h-8 w-8 flex items-center justify-center mb-2">
                                    <x-icon name="o-ellipsis-horizontal" class="w-5 h-5 text-slate-400" />
                                </div>
                                <span class="text-sm text-slate-500 font-medium">Inspección técnica en curso...</span>
                            </div>
                        @endif

                        <!-- CAJA DE PAGO -->
                        @if($order->total_cost > 0)
                            <div class="bg-white rounded-xl p-5 border border-slate-200 shadow-sm">
                                <div class="flex justify-between items-center mb-2">
                                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wide">Total a Pagar</span>
                                    
                                    @if($order->payment_status == 'paid')
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-green-100 text-green-700 text-[10px] font-bold uppercase tracking-wide border border-green-200">
                                            <x-icon name="o-check" class="w-3 h-3"/> Pagado
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-amber-100 text-amber-700 text-[10px] font-bold uppercase tracking-wide border border-amber-200">
                                            Pendiente
                                        </span>
                                    @endif
                                </div>
                                <div class="text-3xl font-black text-slate-900 tracking-tight">
                                    ${{ number_format($order->total_cost, 2) }}
                                </div>
                            </div>
                        @endif
                    </div>

                </div>
            </div>
            
            <!-- FOOTER DE ACCIONES -->
            <div class="bg-slate-50 px-6 py-6 border-t border-slate-200 flex flex-col sm:flex-row justify-between items-center gap-4">
                <a href="/" class="text-sm font-bold text-slate-500 hover:text-slate-800 transition flex items-center gap-2">
                    <x-icon name="o-arrow-left" class="w-4 h-4" /> Volver al Inicio
                </a>
                
<a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $settings->company_phone ?? '') }}?text=Hola {{ $settings->company_name }}, estoy consultando por mi orden #{{ $order->ticket_number ?? $order->id }}..." 
                   target="_blank"
                   class="btn bg-[#25D366] hover:bg-[#128C7E] text-white border-none w-full sm:w-auto gap-2 shadow-lg shadow-green-500/20 font-bold">
                    <x-icon name="o-chat-bubble-left" class="w-5 h-5" />
                    Consultar por WhatsApp
                </a>                

            </div>

        </div>

        <div class="text-center mt-12">
            <p class="text-xs font-bold text-slate-300 uppercase tracking-widest flex items-center justify-center gap-2">
                <x-icon name="o-shield-check" class="w-4 h-4" />
                Powered by TechLife Enterprise
            </p>
        </div>

    </div>
</div>
