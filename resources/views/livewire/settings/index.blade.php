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

    #[Rule('required')] public string $company_name = '';
    #[Rule('nullable')] public string $company_address = '';
    #[Rule('nullable')] public string $company_phone = '';
    #[Rule('nullable|email')] public string $company_email = '';
    #[Rule('nullable')] public string $tax_id = '';
    #[Rule('nullable')] public string $warranty_text = '';
    
    // NUEVOS CAMPOS FINANCIEROS
    #[Rule('required')] public string $currency_symbol = '$';
    #[Rule('required')] public string $tax_name = 'IVA';
    #[Rule('required|numeric|min:0|max:100')] public float $tax_rate = 0;

    public function mount(): void
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, '⛔ ACCESO DENEGADO');
        }

        // Usar firstOrCreate para asegurar que exista registro para esta empresa
        $this->setting = Setting::firstOrCreate(
            ['company_id' => auth()->user()->company_id],
            ['company_name' => 'Mi Empresa']
        );
        
        $this->fill($this->setting);
    }

    public function save(): void
    {
        $this->validate();
        
        $this->setting->update([
            'company_name' => $this->company_name,
            'company_address' => $this->company_address,
            'company_phone' => $this->company_phone,
            'company_email' => $this->company_email,
            'tax_id' => $this->tax_id,
            'warranty_text' => $this->warranty_text,
            'currency_symbol' => $this->currency_symbol,
            'tax_name' => $this->tax_name,
            'tax_rate' => $this->tax_rate,
        ]);

        $this->success('Configuración actualizada');
    }
}; ?>

<div>
    <x-header title="Configuración de Empresa" subtitle="Personaliza tu marca y finanzas" separator>
        <x-slot:actions>
            <x-button label="Guardar Todo" class="btn-primary" wire:click="save" spinner="save" icon="o-check" />
        </x-slot:actions>
    </x-header>

    <div class="grid lg:grid-cols-2 gap-8">
        
        <!-- DATOS GENERALES -->
        <x-card title="Identidad del Negocio" class="shadow-sm border border-slate-200">
            <div class="space-y-4">
                <x-input label="Nombre Comercial" wire:model="company_name" icon="o-building-office" />
                <div class="grid grid-cols-2 gap-4">
                    <x-input label="RUC / ID Fiscal" wire:model="tax_id" icon="o-identification" />
                    <x-input label="Teléfono" wire:model="company_phone" icon="o-phone" />
                </div>
                <x-input label="Dirección" wire:model="company_address" icon="o-map-pin" />
                <x-input label="Email de Contacto" wire:model="company_email" icon="o-envelope" />
            </div>
        </x-card>

        <div class="space-y-8">
            <!-- DATOS FINANCIEROS (NUEVO) -->
            <x-card title="Configuración Fiscal" class="shadow-sm border border-slate-200 border-l-4 border-l-blue-500">
                <div class="grid grid-cols-3 gap-4">
                    <x-input label="Símbolo Moneda" wire:model="currency_symbol" placeholder="$" />
                    <x-input label="Nombre Impuesto" wire:model="tax_name" placeholder="IVA" />
                    <x-input label="% Tasa" wire:model="tax_rate" type="number" suffix="%" />
                </div>
                <p class="text-xs text-slate-500 mt-2">
                    Ejemplo: Si configuras 12%, el sistema calculará: Subtotal + 12% = Total.
                </p>
            </x-card>

            <!-- GARANTÍA -->
            <x-card title="Términos Legales" class="shadow-sm border border-slate-200">
                <x-textarea 
                    label="Texto de Garantía (Pie de página PDF)" 
                    wire:model="warranty_text" 
                    rows="4" 
                />
            </x-card>
        </div>

    </div>
</div>
