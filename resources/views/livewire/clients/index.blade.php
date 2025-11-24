<?php

use Livewire\Volt\Component;
use App\Models\Client;
use Mary\Traits\Toast;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Rule;
use Illuminate\Validation\Rule as ValidationRule; // Alias para evitar conflictos

new 
#[Layout('layouts.app')]
class extends Component {
    use Toast;

    public string $search = '';
    public bool $drawer = false;
    public ?Client $my_client = null;

    #[Rule('required|min:3')] public string $name = '';
    #[Rule('nullable')] public string $tax_id = '';
    #[Rule('nullable|email')] public string $email = '';
    #[Rule('nullable')] public string $phone = '';

    public function create(): void {
        $this->reset(['drawer', 'my_client', 'name', 'email', 'tax_id', 'phone']);
        $this->resetValidation();
        $this->drawer = true;
    }

    public function edit(Client $client): void {
        $this->my_client = $client;
        $this->name = $client->name;
        $this->tax_id = $client->tax_id;
        $this->email = $client->email;
        $this->phone = $client->phone;
        $this->drawer = true;
    }

    public function delete(Client $client): void {
        $client->delete();
        $this->success('Cliente eliminado');
    }

    public function save(): void {
        // Validación Multi-Tenant (El email/RUC debe ser único solo en MI empresa)
        $this->validate([
            'name' => 'required|min:3',
            'phone' => 'nullable',
            'email' => [
                'nullable', 
                'email',
                ValidationRule::unique('clients')->where(function ($query) {
                    return $query->where('company_id', auth()->user()->company_id);
                })->ignore($this->my_client->id ?? null)
            ],
            'tax_id' => [
                'nullable',
                ValidationRule::unique('clients')->where(function ($query) {
                    return $query->where('company_id', auth()->user()->company_id);
                })->ignore($this->my_client->id ?? null)
            ]
        ]);

        // Datos a guardar
        $data = [
            'name' => $this->name,
            'tax_id' => $this->tax_id,
            'email' => $this->email,
            'phone' => $this->phone,
        ];

        // Lógica de Guardado (Aquí estaba el error de sintaxis)
        if ($this->my_client) {
            // Si existe, actualizamos
            $this->my_client->update($data);
            $this->success('Cliente actualizado');
        } else {
            // Si no, creamos
            Client::create($data);
            $this->success('Cliente creado');
        }
        
        $this->drawer = false;
    }

    public function headers(): array {
        return [
            ['key' => 'name', 'label' => 'Nombre / Razón Social'],
            ['key' => 'tax_id', 'label' => 'ID Fiscal'],
            ['key' => 'contact', 'label' => 'Contacto'], 
        ];
    }

    public function with(): array {
        return [
            'clients' => Client::query()
                ->where('name', 'like', "%$this->search%")
                ->orWhere('tax_id', 'like', "%$this->search%")
                ->orderBy('id', 'desc')
                ->get(),
            'headers' => $this->headers()
        ];
    }
}; ?>

<div>
    <x-header title="Cartera de Clientes" subtitle="Gestión de relaciones comerciales" separator>
        <x-slot:middle class="!justify-end">
            <x-input icon="o-magnifying-glass" placeholder="Buscar cliente..." wire:model.live.debounce="search" class="w-72" />
        </x-slot:middle>
        <x-slot:actions>
            <x-button label="Nuevo Cliente" icon="o-user-plus" class="btn-primary" wire:click="create" />
        </x-slot:actions>
    </x-header>

    <x-card>
        <x-table :headers="$headers" :rows="$clients" striped @row-click="$wire.edit($event.detail.id)" class="cursor-pointer hover:bg-slate-50">
            
            @scope('cell_name', $client)
                <div class="font-bold text-slate-900">{{ $client->name }}</div>
                @if($client->email)
                    <div class="text-xs text-slate-400">{{ $client->email }}</div>
                @endif
            @endscope

            @scope('cell_tax_id', $client)
                <span class="font-mono text-slate-600 bg-slate-100 px-2 py-1 rounded text-xs">{{ $client->tax_id ?? 'N/A' }}</span>
            @endscope

            @scope('cell_contact', $client)
                @if($client->phone)
                    <div class="flex items-center gap-2 text-slate-600">
                        <x-icon name="o-phone" class="w-4 h-4" />
                        {{ $client->phone }}
                    </div>
                @else
                    <span class="text-slate-300 italic">Sin teléfono</span>
                @endif
            @endscope
            
            @scope('actions', $client)
                <div class="flex gap-1 justify-end" onclick="event.stopPropagation()">
                    <x-button icon="o-eye" link="/clients/{{ $client->id }}" class="btn-sm btn-ghost text-blue-600 hover:bg-blue-50" tooltip="Ver Perfil" />
                    <x-button icon="o-trash" class="btn-sm btn-ghost text-red-400 hover:text-red-600 hover:bg-red-50" wire:click="delete({{ $client->id }})" confirm="¿Eliminar este cliente y sus datos?" tooltip="Eliminar" />
                </div>
            @endscope

            <x-slot:empty>
                <div class="py-12 flex flex-col items-center justify-center text-slate-400">
                    <x-icon name="o-users" class="w-12 h-12 mb-2 opacity-20" />
                    <div class="font-medium">No hay clientes registrados</div>
                </div>
            </x-slot:empty>
        </x-table>
    </x-card>

    <x-drawer wire:model="drawer" title="{{ $my_client ? 'Editar Cliente' : 'Nuevo Cliente' }}" right class="w-full lg:w-1/3">
        <x-form wire:submit="save" class="space-y-4">
            <x-input label="Nombre / Razón Social" wire:model="name" icon="o-user" />
            
            <div class="grid grid-cols-2 gap-4">
                <x-input label="RUC / Cédula" wire:model="tax_id" icon="o-identification" />
                <x-input label="Teléfono" wire:model="phone" icon="o-phone" />
            </div>
            
            <x-input label="Email" wire:model="email" icon="o-envelope" />
            
            <x-slot:actions>
                <x-button label="Cancelar" wire:click="$toggle('drawer')" />
                <x-button label="Guardar Cliente" class="btn-primary" type="submit" spinner="save" />
            </x-slot:actions>
        </x-form>
    </x-drawer>
</div>
