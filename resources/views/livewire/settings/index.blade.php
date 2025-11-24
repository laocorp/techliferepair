<?php

use Livewire\Volt\Component;
use App\Models\Setting;
use Mary\Traits\Toast;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Rule;

new 
#[Layout('layouts.app')]
class extends Component {
    use Toast;

    public Setting $setting;

    #[Rule('required')]
    public string $company_name = '';
    #[Rule('nullable')]
    public string $company_address = '';
    #[Rule('nullable')]
    public string $company_phone = '';
    #[Rule('nullable|email')]
    public string $company_email = '';
    #[Rule('nullable')]
    public string $tax_id = '';
    #[Rule('nullable')]
    public string $warranty_text = '';

    public function mount(): void
    {
        // 🔒 SEGURIDAD BLINDADA:
        // Si el usuario NO es admin, abortamos con un error 403 (Prohibido)
        if (!auth()->user()->isAdmin()) {
            abort(403, '⛔ ACCESO DENEGADO: Solo administradores.');
        }

        // Cargar configuración
        $this->setting = Setting::first() ?? new Setting();
        $this->fill($this->setting);
    }

    public function save(): void
    {
        $this->validate();
        
        $this->setting->fill([
            'company_name' => $this->company_name,
            'company_address' => $this->company_address,
            'company_phone' => $this->company_phone,
            'company_email' => $this->company_email,
            'tax_id' => $this->tax_id,
            'warranty_text' => $this->warranty_text,
        ])->save();

        $this->success('¡Configuración actualizada!');
    }
}; ?>

<div>
    <x-header title="Configuración de Empresa" subtitle="Personaliza tu marca y documentos" separator />

    <div class="grid lg:grid-cols-2 gap-8">
        <x-card title="Datos Generales" class="shadow-xl">
            <x-form wire:submit="save">
                <x-input label="Nombre de la Empresa" wire:model="company_name" icon="o-building-office" />
                <x-input label="Dirección" wire:model="company_address" icon="o-map-pin" />
                <div class="grid grid-cols-2 gap-4">
                    <x-input label="Teléfono" wire:model="company_phone" icon="o-phone" />
                    <x-input label="RUC / ID Fiscal" wire:model="tax_id" icon="o-identification" />
                </div>
                <x-input label="Email de Contacto" wire:model="company_email" icon="o-envelope" />
                
                <x-textarea 
                    label="Texto de Garantía (Para el PDF)" 
                    wire:model="warranty_text" 
                    hint="Aparecerá al pie de las órdenes de trabajo"
                    rows="3" 
                />

                <x-slot:actions>
                    <x-button label="Guardar Configuración" class="btn-primary" type="submit" spinner="save" icon="o-check" />
                </x-slot:actions>
            </x-form>
        </x-card>

        <x-card title="Vista Previa en Documentos" class="bg-base-200">
            <div class="text-center p-5 bg-white text-black rounded shadow">
                <h1 class="text-2xl font-black uppercase">{{ $company_name ?: 'NOMBRE EMPRESA' }}</h1>
                <p class="text-xs text-gray-500">{{ $company_address }}</p>
                <p class="text-xs text-gray-500">Tel: {{ $company_phone }} | {{ $company_email }}</p>
            </div>
        </x-card>
    </div>
</div>
