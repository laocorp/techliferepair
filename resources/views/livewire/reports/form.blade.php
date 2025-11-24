<?php

use Livewire\Volt\Component;
use App\Models\RepairOrder;
use App\Models\TechnicalReport;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Rule;
use Livewire\WithFileUploads;
use Mary\Traits\Toast;

new 
#[Layout('layouts.app')]
class extends Component {
    use Toast, WithFileUploads;

    public RepairOrder $order;
    public ?TechnicalReport $report = null;

    #[Rule('required|min:10')]
    public string $findings = '';

    #[Rule('required|min:10')]
    public string $recommendations = '';

    public array $checklist_items = [
        'Cable de Poder / Enchufe' => 'ok',
        'Interruptor / Gatillo' => 'ok',
        'Carcasa / Chasis' => 'ok',
        'Motor / Carbones' => 'ok',
        'Ventilación / Filtros' => 'ok',
        'Pinza Masa / Porta-Electrodo' => 'no_aplica',
        'Mandril / Cabezal' => 'no_aplica',
        'Manómetros / Válvulas' => 'no_aplica',
        'Nivel de Aceite' => 'no_aplica'
    ];

    // VALIDACIÓN: Máximo 4 fotos en total, máximo 10MB por foto
    #[Rule(['new_photos' => 'array|max:4', 'new_photos.*' => 'image|max:10240'])] 
    public $new_photos = [];
    
    public $existing_photos = [];

    public function mount(RepairOrder $order): void
    {
        $this->order = $order;
        $this->report = $this->order->technicalReport;

        if ($this->report) {
            $this->findings = $this->report->findings;
            $this->recommendations = $this->report->recommendations;
            $this->checklist_items = array_merge($this->checklist_items, $this->report->checklist ?? []);
            $this->existing_photos = $this->report->photos ?? [];
        }
    }

    public function save(): void
    {
        // Validar que entre nuevas y viejas no pasen de 4
        if ((count($this->existing_photos) + count($this->new_photos)) > 4) {
            $this->error('Máximo 4 fotos permitidas por informe.');
            return;
        }

        $this->validate();

        $photoPaths = $this->existing_photos;
        
        // Guardar fotos nuevas
        foreach ($this->new_photos as $photo) {
            $path = $photo->store('reports', 'public');
            $photoPaths[] = $path;
        }

        $data = [
            'findings' => $this->findings,
            'recommendations' => $this->recommendations,
            'checklist' => $this->checklist_items,
            'photos' => $photoPaths
        ];

        if ($this->report) {
            $this->report->update($data);
        } else {
            $this->report = TechnicalReport::create(array_merge(['repair_order_id' => $this->order->id, 'company_id' => auth()->user()->company_id], $data));
        }

        $this->success('Informe Técnico Guardado');
        $this->new_photos = []; // Limpiar input
        $this->existing_photos = $photoPaths; // Actualizar vista
    }
    
    public function removePhoto($index): void
    {
        unset($this->existing_photos[$index]);
        $this->existing_photos = array_values($this->existing_photos);
        
        if ($this->report) {
            $this->report->update(['photos' => $this->existing_photos]);
        }
    }
}; ?>

<div>
    <div class="max-w-5xl mx-auto">
        <x-header title="Informe Técnico" subtitle="Orden #{{ str_pad($order->id, 4, '0', STR_PAD_LEFT) }} - {{ $order->asset->brand }} {{ $order->asset->model }}" separator>
            <x-slot:actions>
                <x-button label="Volver" icon="o-arrow-left" link="/orders/{{ $order->id }}" />
                
                @if($report)
                     <x-button label="Descargar PDF" icon="o-printer" class="btn-warning text-white" link="{{ route('reports.print', $order->id) }}" external />
                @endif
    
                <x-button label="Guardar Informe" icon="o-check" class="btn-primary" wire:click="save" spinner="save" />
            </x-slot:actions>
        </x-header>
    
        <div class="grid lg:grid-cols-2 gap-8">
            
            <!-- COLUMNA IZQUIERDA -->
            <div class="space-y-6">
                <x-card title="Diagnóstico y Solución" class="shadow-xl">
                    <x-textarea label="Hallazgos Técnicos" wire:model="findings" rows="6" placeholder="Describe el daño..." />
                    <div class="h-4"></div>
                    <x-textarea label="Recomendaciones" wire:model="recommendations" rows="6" placeholder="Trabajo realizado..." />
                </x-card>
            </div>
    
            <!-- COLUMNA DERECHA -->
            <div class="space-y-6">
                <x-card title="Checklist de Estado" class="shadow-xl h-96 overflow-y-auto">
                    <div class="grid grid-cols-1 gap-2">
                        @foreach($checklist_items as $key => $value)
                            <div class="flex justify-between items-center border-b border-base-200 pb-2">
                                <span class="font-bold text-sm">{{ $key }}</span>
                                <select wire:model="checklist_items.{{ $key }}" class="select select-xs select-ghost w-32 text-right">
                                    <option value="ok">OK ✅</option>
                                    <option value="falla">Falla ❌</option>
                                    <option value="dañado">Dañado ⚠️</option>
                                    <option value="no_aplica">N/A ⚪</option>
                                </select>
                            </div>
                        @endforeach
                    </div>
                </x-card>
    
                <x-card title="Evidencia (Máx 4 Fotos)" class="shadow-xl">
                    <x-file wire:model="new_photos" label="Subir Fotos" accept="image/*" multiple icon="o-camera" hint="Máximo 4 imágenes en total" />
    
                    <!-- Galería de Fotos -->
                    <div class="grid grid-cols-2 gap-4 mt-4">
                        @foreach($existing_photos as $index => $photo)
                            <div class="relative group">
                                <img src="{{ asset('storage/' . $photo) }}" class="w-full h-32 object-cover rounded-lg border border-base-300">
                                <button wire:click="removePhoto({{ $index }})" class="absolute top-1 right-1 bg-red-500 text-white rounded-full p-1 shadow-md">
                                    <x-icon name="o-x-mark" class="w-4 h-4" />
                                </button>
                            </div>
                        @endforeach

                        @foreach($new_photos as $photo)
                            <div class="relative opacity-50">
                                <img src="{{ $photo->temporaryUrl() }}" class="w-full h-32 object-cover rounded-lg border border-primary border-2">
                                <div class="absolute inset-0 flex items-center justify-center text-xs font-bold text-white bg-black/50 rounded-lg">SUBIENDO...</div>
                            </div>
                        @endforeach
                    </div>
                </x-card>
            </div>
        </div>
    </div>
</div>
