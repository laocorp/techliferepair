<?php

use Livewire\Volt\Component;
use App\Models\CashRegister;
use App\Models\Sale;
use App\Models\RepairOrder;
use Mary\Traits\Toast;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Rule;
use Illuminate\Support\Facades\DB;

new 
#[Layout('layouts.app')]
class extends Component {
    use Toast;

    public ?CashRegister $activeRegister = null;
    
    // Variables para Abrir Caja
    #[Rule('required|numeric|min:0')]
    public float $opening_amount = 0.00;

    // Variables para Cerrar Caja
    #[Rule('required|numeric|min:0')]
    public float $counted_amount = 0.00;
    public string $closing_notes = '';

    // Totales Calculados
    public float $sales_total = 0.0;
    public float $repairs_total = 0.0;
    public float $system_total = 0.0;

    public function mount(): void
    {
        $this->checkActiveRegister();
    }

    public function checkActiveRegister(): void
    {
        // Buscar si este usuario tiene una caja abierta
        $this->activeRegister = CashRegister::where('user_id', auth()->id())
            ->where('status', 'open')
            ->first();

        if ($this->activeRegister) {
            $this->calculateTotals();
        }
    }

    public function calculateTotals(): void
    {
        // 1. Sumar Ventas POS hechas por este usuario desde que abrió caja
        $this->sales_total = Sale::where('user_id', auth()->id())
            ->where('created_at', '>=', $this->activeRegister->opened_at)
            ->sum('total');

        // 2. Sumar Reparaciones Cobradas (marcadas como pagadas) por este usuario hoy
        // Nota: Esto es una aproximación. Lo ideal sería tener una tabla de pagos específica.
        // Usamos updated_at como referencia del momento del cobro.
        $this->repairs_total = RepairOrder::where('payment_status', 'paid')
            ->where('updated_at', '>=', $this->activeRegister->opened_at)
            // Opcional: filtrar por usuario si guardaras quién cobró la orden
            ->sum('total_cost');

        // 3. Total Esperado = Inicio + Ventas + Reparaciones
        $this->system_total = $this->activeRegister->opening_amount + $this->sales_total + $this->repairs_total;
    }

    public function openRegister(): void
    {
        $this->validate(['opening_amount' => 'required|numeric|min:0']);

        CashRegister::create([
            'user_id' => auth()->id(),
            'company_id' => auth()->user()->company_id,
            'opening_amount' => $this->opening_amount,
            'opened_at' => now(),
            'status' => 'open'
        ]);

        $this->success('Caja Abierta Correctamente');
        $this->checkActiveRegister();
    }

    public function closeRegister(): void
    {
        $this->validate(['counted_amount' => 'required|numeric|min:0']);

        $difference = $this->counted_amount - $this->system_total;

        $this->activeRegister->update([
            'closed_at' => now(),
            'closing_amount' => $this->counted_amount,
            'calculated_amount' => $this->system_total,
            'difference' => $difference,
            'status' => 'closed',
            'notes' => $this->closing_notes
        ]);

        $this->success('Caja Cerrada. Diferencia: $' . number_format($difference, 2));
        
        // Recargar para mostrar estado cerrado (o redirigir)
        $this->activeRegister = null;
        $this->opening_amount = 0;
        $this->counted_amount = 0;
    }
}; ?>

<div>
    <x-header title="Control de Caja" subtitle="Gestión de turnos y efectivo" separator />

    @if(!$activeRegister)
        <!-- VISTA: CAJA CERRADA (FORMULARIO APERTURA) -->
        <div class="max-w-md mx-auto mt-10">
            <x-card title="Apertura de Caja" class="shadow-xl border border-slate-200 text-center">
                <div class="py-4">
                    <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4 text-slate-400">
                        <x-icon name="o-lock-closed" class="w-8 h-8" />
                    </div>
                    <p class="text-slate-500 mb-6">Ingresa el monto de efectivo inicial en caja.</p>
                    
                    <x-form wire:submit="openRegister">
                        <x-input label="Monto Inicial ($)" wire:model="opening_amount" type="number" step="0.01" prefix="$" class="font-bold text-lg" />
                        
                        <x-slot:actions>
                            <x-button label="ABRIR CAJA" class="btn-primary w-full font-bold" type="submit" spinner="openRegister" />
                        </x-slot:actions>
                    </x-form>
                </div>
            </x-card>
        </div>

    @else
        <!-- VISTA: CAJA ABIERTA (PANEL DE CONTROL) -->
        <div class="grid lg:grid-cols-2 gap-8">
            
            <!-- Columna Izquierda: Resumen -->
            <div class="space-y-6">
                
                <!-- Tarjeta de Estado -->
                <div class="bg-green-50 border border-green-200 rounded-xl p-6 flex items-center justify-between">
                    <div>
                        <div class="text-xs font-bold text-green-700 uppercase tracking-wide mb-1">Caja Abierta</div>
                        <div class="text-sm text-green-800">Desde: {{ $activeRegister->opened_at->format('d/m/Y H:i') }}</div>
                    </div>
                    <div class="text-right">
                        <div class="text-xs font-bold text-green-700 uppercase tracking-wide mb-1">Base Inicial</div>
                        <div class="text-xl font-black text-green-900">${{ number_format($activeRegister->opening_amount, 2) }}</div>
                    </div>
                </div>

                <!-- Desglose de Movimientos -->
                <x-card title="Movimientos del Turno" class="shadow-md">
                    <div class="space-y-4">
                        <div class="flex justify-between items-center border-b border-slate-100 pb-2">
                            <span class="text-slate-600 flex items-center gap-2"><x-icon name="o-shopping-cart" class="w-4 h-4"/> Ventas POS</span>
                            <span class="font-bold text-slate-900">+ ${{ number_format($sales_total, 2) }}</span>
                        </div>
                        <div class="flex justify-between items-center border-b border-slate-100 pb-2">
                            <span class="text-slate-600 flex items-center gap-2"><x-icon name="o-wrench-screwdriver" class="w-4 h-4"/> Reparaciones</span>
                            <span class="font-bold text-slate-900">+ ${{ number_format($repairs_total, 2) }}</span>
                        </div>
                        <div class="flex justify-between items-center pt-2 bg-slate-50 p-3 rounded-lg">
                            <span class="font-bold text-slate-800 uppercase text-sm">Total Esperado en Caja</span>
                            <span class="font-black text-2xl text-blue-600">${{ number_format($system_total, 2) }}</span>
                        </div>
                    </div>
                </x-card>
            </div>

            <!-- Columna Derecha: Cierre -->
            <div>
                <x-card title="Cierre de Turno" class="shadow-xl border-t-4 border-red-500">
                    <p class="text-sm text-slate-500 mb-6">Cuenta el dinero físico en tu caja e ingrésalo abajo para cerrar el turno.</p>

                    <x-form wire:submit="closeRegister">
                        <x-input label="Dinero Contado ($)" wire:model="counted_amount" type="number" step="0.01" prefix="$" class="font-bold text-xl" hint="Suma billetes y monedas" />
                        
                        <x-textarea label="Notas de Cierre" wire:model="closing_notes" placeholder="Observaciones (ej. Gasté $5 en agua)" />

                        @if($counted_amount > 0)
                            <div class="alert {{ ($counted_amount - $system_total) >= 0 ? 'alert-success' : 'alert-error' }} text-xs py-2">
                                <x-icon name="{{ ($counted_amount - $system_total) >= 0 ? 'o-check' : 'o-exclamation-triangle' }}" />
                                <span>
                                    Diferencia: <strong>${{ number_format($counted_amount - $system_total, 2) }}</strong>
                                    {{ ($counted_amount - $system_total) >= 0 ? '(Sobra/Perfecto)' : '(Falta dinero)' }}
                                </span>
                            </div>
                        @endif

                        <x-slot:actions>
                            <x-button label="CERRAR CAJA" class="btn-error w-full font-bold text-white" type="submit" spinner="closeRegister" icon="o-lock-closed" confirm="¿Estás seguro de cerrar caja? Esta acción no se puede deshacer." />
                        </x-slot:actions>
                    </x-form>
                </x-card>
            </div>
        </div>
    @endif
</div>
