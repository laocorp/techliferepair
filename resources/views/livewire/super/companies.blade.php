<?php

use Livewire\Volt\Component;
use App\Models\Company;
use App\Models\Plan;
use Mary\Traits\Toast;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Rule;

new 
#[Layout('layouts.app')]
class extends Component {
    use Toast;

    public bool $drawer = false;
    public ?Company $my_company = null;

    #[Rule('required')] public string $name = '';
    #[Rule('required')] public int $plan_id = 1;
    #[Rule('required')] public string $status = 'active';
    #[Rule('required|date')] public string $valid_until = '';

    public function mount() {
        if (!auth()->user()->is_super_admin) {
            abort(403, 'Zona restringida.');
        }
    }
   
   public function delete(Company $company): void {
        // Opcional: Validar si es una empresa real antes de borrar
        $company->delete(); 
        // Nota: Al borrar la empresa, los usuarios se borran en cascada por la migración
        $this->success('Empresa y usuarios eliminados correctamente');
    }

    public function edit(Company $company): void {
        $this->my_company = $company;
        $this->fill($company);
        // Ajuste de fecha para el input HTML
        $this->valid_until = $company->valid_until ? \Carbon\Carbon::parse($company->valid_until)->format('Y-m-d') : '';
        $this->drawer = true;
    }

    public function save(): void {
        $this->validate();
        
        if ($this->my_company) {
            $this->my_company->update($this->all());
            
            // Lógica extra: Si activas, podrías enviar un email, etc.
            if ($this->status == 'suspended') {
                $this->warning('Atención: Has suspendido el acceso a esta empresa.');
            } else {
                $this->success('Empresa actualizada correctamente');
            }
        }
        
        $this->drawer = false;
    }

    public function with(): array {
        return [
            'companies' => Company::with('plan')->orderBy('id', 'desc')->get(),
            'plans' => Plan::all(),
            'headers' => [
                ['key' => 'id', 'label' => '#', 'class' => 'w-1'],
                ['key' => 'name', 'label' => 'Empresa / Cliente'],
                ['key' => 'plan.name', 'label' => 'Plan'],
                ['key' => 'status', 'label' => 'Estado'],
                ['key' => 'valid_until', 'label' => 'Vencimiento'],
            ]
        ];
    }
}; ?>

<div>
    <x-header title="Cartera de Clientes SaaS" subtitle="Administración de inquilinos (Tenants)" separator />

    <x-card>
        <x-table :headers="$headers" :rows="$companies" @row-click="$wire.edit($event.detail.id)" class="cursor-pointer" striped>
            
            {{-- Nombre y Slug --}}
            @scope('cell_name', $company)
                <div>
                    <div class="font-bold text-slate-900">{{ $company->name }}</div>
                    <div class="text-xs text-gray-400 font-mono">{{ $company->slug }}</div>
                </div>
            @endscope

            {{-- Plan --}}
            @scope('cell_plan.name', $company)
                <div class="badge badge-ghost font-semibold text-xs">{{ $company->plan->name }}</div>
            @endscope

            {{-- Estado --}}
            @scope('cell_status', $company)
                @if($company->status == 'active')
                    <div class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                        Activo
                    </div>
                @else
                    <div class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                        Suspendido
                    </div>
                @endif
            @endscope

            {{-- Vencimiento --}}
            @scope('cell_valid_until', $company)
                @if($company->valid_until)
                    @php $days = \Carbon\Carbon::now()->diffInDays($company->valid_until, false); @endphp
                    
                    <div class="flex flex-col">
                        <span class="font-bold {{ $days < 5 ? 'text-red-600' : 'text-slate-700' }}">
                            {{ \Carbon\Carbon::parse($company->valid_until)->format('d/m/Y') }}
                        </span>
                        <span class="text-[10px] text-gray-500">
                            @if($days < 0) Vencido hace {{ abs(intval($days)) }} días
                            @else Quedan {{ intval($days) }} días
                            @endif
                        </span>
                    </div>
                @else
                    <span class="text-gray-400">—</span>
                @endif
            @endscope
@scope('actions', $company)
                <div class="flex gap-1" onclick="event.stopPropagation()">
                    <x-button 
                        icon="o-trash" 
                        class="btn-sm btn-ghost text-error" 
                        wire:click="delete({{ $company->id }})" 
                        confirm="¡CUIDADO! Esto borrará la empresa, sus usuarios, órdenes y datos. ¿Continuar?"
                        tooltip="Eliminar Empresa" 
                    />
                </div>
            @endscope

        </x-table>
    </x-card>

    <!-- DRAWER DE GESTIÓN -->
    <x-drawer wire:model="drawer" title="Gestionar Suscripción" right class="w-full lg:w-1/3">
        <x-form wire:submit="save">
            
            <div class="bg-slate-50 p-4 rounded-lg border border-slate-200 mb-4">
                <div class="text-xs font-bold text-gray-500 uppercase">Empresa</div>
                <div class="text-xl font-black text-slate-900">{{ $my_company->name ?? '' }}</div>
                <div class="text-xs text-gray-400">{{ $my_company->slug ?? '' }}</div>
            </div>

            <x-select label="Cambiar Plan" :options="$plans" wire:model="plan_id" icon="o-currency-dollar" />

            <x-select 
                label="Estado del Servicio" 
                :options="[['id'=>'active', 'name'=>'Activo (Permitir Acceso)'], ['id'=>'suspended', 'name'=>'SUSPENDIDO (Bloquear Acceso)']]" 
                wire:model="status" 
                icon="o-shield-check"
            />

            <x-input label="Fecha de Corte" wire:model="valid_until" type="date" icon="o-calendar" />

            <x-slot:actions>
                <x-button label="Cerrar" wire:click="$toggle('drawer')" />
                <x-button label="Actualizar Cliente" class="btn-primary" type="submit" spinner="save" />
	    </x-slot:actions>
        </x-form>
    </x-drawer>
</div>
