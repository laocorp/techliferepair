<?php

use Livewire\Volt\Component;
use App\Models\RepairOrder;
use App\Models\Part; 
use Mary\Traits\Toast;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Rule;

new 
#[Layout('layouts.app')]
class extends Component {
    use Toast;

    public RepairOrder $repairOrder; 

    // -- Datos Editables de la Orden --
    #[Rule('required')]
    public string $status = '';

    #[Rule('nullable')]
    public ?string $diagnosis_notes = ''; 

    #[Rule('nullable|numeric')]
    public ?float $total_cost = null;

    // -- Variables para el Carrito de Repuestos --
    public ?int $selected_part_id = null;
    public int $quantity = 1;

    // Cargar datos al iniciar
    public function mount(RepairOrder $repairOrder): void
    {
        $this->repairOrder = $repairOrder;
        $this->fill($repairOrder); 
    }

    // --- LÓGICA DE WHATSAPP ---
    public function getWhatsappLinkProperty(): string
    {
        $client = $this->repairOrder->asset->client;
        
        // Validar si tiene teléfono
        if (empty($client->phone)) return '#';

        // Limpiar el número (quitar espacios, guiones)
        $phone = preg_replace('/[^0-9]/', '', $client->phone);

        // Datos para el mensaje
        $equipo = $this->repairOrder->asset->brand . ' ' . $this->repairOrder->asset->model;
        $orden = str_pad($this->repairOrder->id, 4, '0', STR_PAD_LEFT);
        $total = number_format($this->repairOrder->total_cost ?? 0, 2);
        
        // Mensaje inteligente según estado
        $mensaje = match ($this->status) {
            'recibido' => "Hola {$client->name}, recibimos tu equipo *{$equipo}*. Tu orden es la *#{$orden}*. Te avisaremos cuando inicie el diagnóstico.",
            'diagnostico' => "Hola {$client->name}, tu equipo *{$equipo}* (Orden #{$orden}) ya está en revisión técnica. Pronto te daremos el informe.",
            'espera_repuestos' => "Hola {$client->name}, estamos esperando repuestos para tu *{$equipo}*. Te mantendremos informado.",
            'listo' => "¡Hola {$client->name}! Tu equipo *{$equipo}* está *LISTO* ✅. \n\n💰 Total a pagar: *{$total}* \n📍 Puedes pasar a retirar en nuestro taller.",
            'entregado' => "Gracias por confiar en TECHLIFE. Tu equipo *{$equipo}* fue entregado exitosamente.",
            default => "Hola {$client->name}, te escribimos sobre tu orden #{$orden}."
        };

        return "https://wa.me/{$phone}?text=" . urlencode($mensaje);
    }

    // Guardar Cambios Generales
    public function save(): void
    {
        $this->validate([
            'status' => 'required',
            'diagnosis_notes' => 'nullable',
            'total_cost' => 'nullable|numeric'
        ]);

        $this->repairOrder->update([
            'status' => $this->status,
            'diagnosis_notes' => $this->diagnosis_notes,
            'total_cost' => $this->total_cost,
        ]);

        $this->success('¡Orden actualizada correctamente!');
    }

    // Opciones de Estado
    public function statuses(): array
    {
        return [
            ['id' => 'recibido', 'name' => 'Recibido'],
            ['id' => 'diagnostico', 'name' => 'En Diagnóstico'],
            ['id' => 'espera_repuestos', 'name' => 'Esperando Repuestos'],
            ['id' => 'listo', 'name' => 'Listo para Entrega'],
            ['id' => 'entregado', 'name' => 'Entregado / Cerrado'],
        ];
    }

    // Cargar lista de repuestos
    public function parts(): mixed
    {
        return Part::orderBy('name')->get();
    }

    // Acción: Agregar Repuesto
    public function addPart(): void
    {
        $this->validate([
            'selected_part_id' => 'required|exists:parts,id',
            'quantity' => 'required|integer|min:1'
        ]);

        $part = Part::find($this->selected_part_id);

        // Verificar Stock
        if ($part->stock < $this->quantity) {
            $this->error("Solo quedan {$part->stock} unidades de {$part->name}");
            return;
        }

        // 1. Restar del Inventario
        $part->decrement('stock', $this->quantity);

        // 2. Agregar a la Orden (Pivot)
        $this->repairOrder->parts()->attach($part->id, [
            'quantity' => $this->quantity,
            'price' => $part->price
        ]);

        // 3. Actualizar Costo Total de la Orden
        $nuevoCosto = ($this->repairOrder->total_cost ?? 0) + ($part->price * $this->quantity);
        $this->repairOrder->update(['total_cost' => $nuevoCosto]);
        $this->total_cost = $nuevoCosto; 

        $this->success("Agregado: {$part->name}");
        $this->reset(['selected_part_id', 'quantity']);
    }

    // Acción: Quitar Repuesto
    public function removePart($partId): void
    {
        $pivot = $this->repairOrder->parts()->where('part_id', $partId)->first()->pivot;
        
        // 1. Devolver al Inventario
        Part::find($partId)->increment('stock', $pivot->quantity);

        // 2. Restar precio
        $nuevoCosto = $this->repairOrder->total_cost - ($pivot->price * $pivot->quantity);
        $this->repairOrder->update(['total_cost' => max(0, $nuevoCosto)]); 
        $this->total_cost = max(0, $nuevoCosto);

        // 3. Quitar de la orden
        $this->repairOrder->parts()->detach($partId);
        
        $this->success('Repuesto devuelto al stock');
    }
}; ?>

<div class="grid lg:grid-cols-3 gap-8">
    
    <div class="lg:col-span-1 space-y-6">
        
        <x-header title="Orden #{{ str_pad($repairOrder->id, 4, '0', STR_PAD_LEFT) }}" size="text-2xl" separator>
        @slot('actions')
            <x-button 
                label="Informe Técnico" 
                icon="o-document-magnifying-glass" 
                link="/orders/{{ $repairOrder->id }}/report" 
                class="btn-warning btn-outline btn-sm" 
            />

            <x-button 
                label="Imprimir Orden" 
                icon="o-printer" 
                link="{{ route('orders.print', $repairOrder) }}" 
                external 
                class="btn-outline btn-sm" 
            />
        @endslot
    </x-header>

        <x-card title="Datos del Cliente" class="shadow-lg">
            <div class="flex justify-between items-center">
                <div>
                    <div class="flex gap-3 items-center mb-2">
                        <x-icon name="o-user" class="w-6 h-6 text-primary" />
                        <span class="font-bold text-lg">{{ $repairOrder->asset->client->name }}</span>
                    </div>
                    <div class="text-gray-500 text-sm space-y-1">
                        <p class="flex items-center gap-2">
                            <x-icon name="o-phone" class="w-4 h-4" /> 
                            {{ $repairOrder->asset->client->phone ?? 'Sin teléfono' }}
                        </p>
                        <p class="flex items-center gap-2">
                            <x-icon name="o-envelope" class="w-4 h-4" /> 
                            {{ $repairOrder->asset->client->email ?? 'Sin email' }}
                        </p>
                    </div>
                </div>

                <div>
                    @if($repairOrder->asset->client->phone)
                        <x-button 
                            icon="o-chat-bubble-left-right" 
                            class="btn-circle bg-green-500 hover:bg-green-600 text-white border-none" 
                            tooltip="Enviar WhatsApp"
                            link="{{ $this->whatsapp_link }}"
                            external
                        />
                    @else
                        <x-button 
                            icon="o-no-symbol" 
                            class="btn-circle btn-ghost opacity-50" 
                            disabled
                            tooltip="Sin número guardado" 
                        />
                    @endif
                </div>
            </div>
        </x-card>

        <x-card title="Equipo en Taller" class="shadow-lg">
            <div class="font-bold text-xl mb-1">{{ $repairOrder->asset->brand }} {{ $repairOrder->asset->model }}</div>
            <div class="badge badge-neutral mb-4">{{ $repairOrder->asset->serial_number }}</div>
            
            <hr class="my-2 border-base-300" />
            
            <span class="text-xs font-bold uppercase text-gray-500">Problema Reportado:</span>
            <p class="italic mt-1">"{{ $repairOrder->problem_description }}"</p>

            @if($repairOrder->is_warranty)
                <div class="alert alert-warning mt-4 flex items-center gap-2 p-2">
                    <x-icon name="o-shield-check" />
                    <span class="font-bold text-sm">Reparación por GARANTÍA</span>
                </div>
            @endif
        </x-card>
    </div>

    <div class="lg:col-span-2 space-y-8">
        
        <x-card title="Gestión Técnica" separator class="shadow-xl border-t-4 border-primary">
            <x-form wire:submit="save">
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <x-select 
                        label="Estado Actual" 
                        :options="$this->statuses()" 
                        wire:model="status" 
                        icon="o-arrow-path"
                    />
                    
                    <x-input 
                        label="Costo Total ($)" 
                        wire:model="total_cost" 
                        prefix="$" 
                        type="number" 
                        step="0.01" 
                        hint="Mano de obra + Repuestos"
                    />
                </div>

                <x-textarea 
                    label="Diagnóstico Técnico / Trabajo Realizado" 
                    wire:model="diagnosis_notes" 
                    placeholder="Describe qué se revisó, qué falló y qué se cambió..."
                    rows="5"
                />

                <x-slot:actions>
                    <x-button label="Volver" link="/orders" />
                    <x-button label="Guardar Cambios" class="btn-primary" type="submit" spinner="save" icon="o-check" />
                </x-slot:actions>
            </x-form>
        </x-card>

        <x-card title="Repuestos Utilizados" separator class="shadow-xl">
            
            <div class="flex gap-3 items-end mb-6">
                <div class="w-full md:w-1/2">
                    <x-select 
                        label="Buscar Repuesto" 
                        icon="o-magnifying-glass" 
                        :options="$this->parts()" 
                        wire:model="selected_part_id"
                        placeholder="Seleccionar pieza..."
                        searchable
                    />
                </div>
                <div class="w-24">
                    <x-input label="Cant." type="number" wire:model="quantity" min="1" />
                </div>
                <x-button label="Agregar" icon="o-plus" class="btn-primary" wire:click="addPart" spinner="addPart" />
            </div>

            <div class="overflow-x-auto">
                <table class="table table-zebra">
                    <thead>
                        <tr class="bg-base-200">
                            <th>Repuesto</th>
                            <th class="text-center">Cantidad</th>
                            <th class="text-right">Precio Unit.</th>
                            <th class="text-right">Subtotal</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($repairOrder->parts as $part)
                            <tr>
                                <td class="font-bold">{{ $part->name }} <br> <span class="text-xs text-gray-500">{{ $part->sku }}</span></td>
                                <td class="text-center font-bold">{{ $part->pivot->quantity }}</td>
                                <td class="text-right">$ {{ number_format($part->pivot->price, 2) }}</td>
                                <td class="text-right font-black text-primary">$ {{ number_format($part->pivot->price * $part->pivot->quantity, 2) }}</td>
                                <td class="text-right">
                                    <x-button icon="o-trash" class="btn-sm btn-ghost text-error" wire:click="removePart({{ $part->id }})" tooltip="Quitar" confirm="¿Devolver al inventario?" />
                                </td>
                            </tr>
                        @endforeach
                        
                        @if($repairOrder->parts->isEmpty())
                            <tr>
                                <td colspan="5" class="text-center py-5 text-gray-500 italic">No se han usado repuestos aún.</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </x-card>
    </div>
</div>
