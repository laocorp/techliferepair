<?php

use Livewire\Volt\Component;
use App\Models\User;
use App\Models\Company; // <--- Importante
use Mary\Traits\Toast;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Rule;
use Illuminate\Support\Facades\Hash;

new 
#[Layout('layouts.app')]
class extends Component {
    use Toast;

    public string $search = '';
    public bool $drawer = false;
    public ?User $my_user = null;

    #[Rule('required|min:3')] public string $name = '';
    #[Rule('required|email')] public string $email = '';
    #[Rule('nullable|min:8')] public string $password = '';
    #[Rule('required')] public string $role = 'tech';
    
    // Nuevo campo para asignar empresa (nullable)
    #[Rule('nullable')] public ?int $company_id = null;

    public function mount() {
        // Permitir entrada a Admin o Super Admin
        if (!auth()->user()->isAdmin() && !auth()->user()->is_super_admin) {
            abort(403, 'Acceso restringido.');
        }
    }

    public function create(): void {
        $this->reset(['drawer', 'my_user', 'name', 'email', 'password', 'role', 'company_id']);
        $this->resetValidation();
        $this->drawer = true;
    }

    public function edit(User $user): void {
        $this->my_user = $user;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->role = $user->role;
        $this->company_id = $user->company_id; // Cargar la empresa actual
        $this->password = '';
        $this->drawer = true;
    }

    public function delete(User $user): void {
        if ($user->id === auth()->id()) {
            $this->error('No puedes eliminarte a ti mismo.');
            return;
        }
        $user->delete();
        $this->success('Usuario eliminado');
    }

    public function save(): void {
        $this->validate([
            'name' => 'required|min:3',
            'email' => 'required|email|unique:users,email,' . ($this->my_user->id ?? 'NULL'),
            'role' => 'required',
            'password' => $this->my_user ? 'nullable|min:8' : 'required|min:8',
            'company_id' => 'nullable'
        ]);

        // Verificar límites solo si NO es super admin
        if (!$this->my_user && !auth()->user()->is_super_admin) {
             if (!auth()->user()->company->canAddUser()) {
                 $this->error('Límite de usuarios alcanzado. Mejora tu plan.');
                 return;
             }
        }

        $data = ['name' => $this->name, 'email' => $this->email, 'role' => $this->role];
        
        if (!empty($this->password)) {
            $data['password'] = Hash::make($this->password);
        }

        // LOGICA DE EMPRESA
        if (auth()->user()->is_super_admin) {
            // Si soy Super Admin, asigno la empresa que seleccioné (o NULL)
            $data['company_id'] = $this->company_id;
        } elseif (!$this->my_user) {
            // Si soy Admin normal y estoy creando, asigno mi propia empresa
            $data['company_id'] = auth()->user()->company_id;
        }

        if ($this->my_user) {
            $this->my_user->update($data);
            $this->success('Usuario actualizado');
        } else {
            User::create($data);
            $this->success('Usuario creado');
        }
        $this->drawer = false;
    }

    public function headers(): array {
        $headers = [
            ['key' => 'name', 'label' => 'Nombre'],
            ['key' => 'email', 'label' => 'Email'],
            ['key' => 'role', 'label' => 'Rol'],
        ];

        // Si soy Super Admin, quiero ver de qué empresa son
        if (auth()->user()->is_super_admin) {
            $headers[] = ['key' => 'company.name', 'label' => 'Empresa Asignada'];
        }

        return $headers;
    }

    public function with(): array {
        return [
            'users' => User::with('company')->where('name', 'like', "%$this->search%")->orderBy('id', 'desc')->get(),
            'headers' => $this->headers(),
            'roles' => [
                ['id' => 'admin', 'name' => 'Administrador'], 
                ['id' => 'tech', 'name' => 'Técnico'],
                ['id' => 'client', 'name' => 'Cliente (Portal)']
            ],
            // Cargar lista de empresas solo si soy Super Admin
            'companies' => auth()->user()->is_super_admin ? Company::orderBy('name')->get() : []
        ];
    }
}; ?>

<div>
    <x-header title="Usuarios" subtitle="Gestión de accesos y personal" separator>
        <x-slot:middle class="!justify-end">
            <x-input icon="o-magnifying-glass" placeholder="Buscar..." wire:model.live.debounce="search" />
        </x-slot:middle>
        <x-slot:actions>
            <x-button icon="o-plus" class="btn-primary" label="Nuevo Usuario" wire:click="create" />
        </x-slot:actions>
    </x-header>

    <x-card>
        <x-table :headers="$headers" :rows="$users" striped @row-click="$wire.edit($event.detail.id)" class="cursor-pointer">
            
            {{-- Badge de Rol --}}
            @scope('cell_role', $user)
                @if($user->is_super_admin)
                    <span class="badge badge-primary font-bold bg-purple-600 text-white border-none">SUPER ADMIN</span>
                @elseif($user->role == 'admin')
                    <span class="badge badge-neutral font-bold">ADMIN</span>
                @elseif($user->role == 'client')
                    <span class="badge badge-info font-bold text-white">CLIENTE</span>
                @else
                    <span class="badge badge-ghost">TÉCNICO</span>
                @endif
            @endscope

            {{-- Badge de Empresa (Solo visible para Super Admin por los headers) --}}
            @scope('cell_company.name', $user)
                @if($user->company)
                    <span class="font-bold text-slate-700">{{ $user->company->name }}</span>
                @else
                    <span class="text-red-400 text-xs italic">Sin Asignar (Huérfano)</span>
                @endif
            @endscope

            {{-- Acciones --}}
            @scope('actions', $user)
                <div class="flex" onclick="event.stopPropagation()">
                    <x-button icon="o-trash" spinner class="btn-sm btn-ghost text-error" wire:click="delete({{ $user->id }})" confirm="¿Eliminar usuario definitivamente?" />
                </div>
            @endscope

        </x-table>
    </x-card>

    <!-- DRAWER -->
    <x-drawer wire:model="drawer" title="{{ $my_user ? 'Editar Usuario' : 'Nuevo Usuario' }}" right class="w-full lg:w-1/3">
        <x-form wire:submit="save">
            
            <x-input label="Nombre" wire:model="name" icon="o-user" />
            <x-input label="Email" wire:model="email" icon="o-envelope" />
            
            <x-select label="Rol" :options="$roles" wire:model="role" icon="o-shield-check" />

            {{-- SELECTOR DE EMPRESA (SOLO SUPER ADMIN) --}}
            @if(auth()->user()->is_super_admin)
                <div class="bg-purple-50 p-4 rounded-lg border border-purple-100 my-2">
                    <div class="text-xs font-bold text-purple-800 uppercase mb-2">Zona Super Admin</div>
                    <x-select 
                        label="Asignar a Empresa" 
                        :options="$companies" 
                        wire:model="company_id" 
                        icon="o-building-office-2" 
                        placeholder="Selecciona una empresa..."
                        hint="El usuario pertenecerá a este taller"
                    />
                </div>
            @endif

            <x-input label="Contraseña" wire:model="password" icon="o-key" type="password" hint="{{ $my_user ? 'Déjalo vacío para no cambiarla' : 'Mínimo 8 caracteres' }}" />
            
            <x-slot:actions>
                <x-button label="Cancelar" wire:click="$toggle('drawer')" />
                <x-button label="Guardar" class="btn-primary" type="submit" spinner="save" />
            </x-slot:actions>
        </x-form>
    </x-drawer>
</div>
