<?php

use Livewire\Volt\Component;
use App\Models\User;
use App\Models\Company;
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
    
    // Solo para Super Admin
    #[Rule('nullable')] public ?int $company_id = null;

    public function mount() {
        // Solo Admins o Super Admins pueden entrar aquí
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
        // Seguridad: No editar usuarios de otras empresas (si no soy Super Admin)
        if (!auth()->user()->is_super_admin && $user->company_id !== auth()->user()->company_id) {
            abort(403);
        }

        $this->my_user = $user;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->role = $user->role;
        $this->company_id = $user->company_id;
        $this->password = '';
        $this->drawer = true;
    }

    // --- FUNCIÓN DE ELIMINAR ---
    public function delete(User $user): void {
        // 1. No te puedes borrar a ti mismo
        if ($user->id === auth()->id()) {
            $this->error('No puedes eliminar tu propia cuenta.');
            return;
        }

        // 2. Seguridad: Solo borrar gente de mi empresa (o si soy Super Admin)
        if (!auth()->user()->is_super_admin && $user->company_id !== auth()->user()->company_id) {
            $this->error('No tienes permiso para eliminar este usuario.');
            return;
        }

        // 3. Ejecutar borrado
        $user->delete();
        $this->success('Usuario eliminado correctamente.');
    }

    public function save(): void {
        $this->validate([
    'name' => 'required|min:3',
    'phone' => 'nullable',
    // Email único solo en mi empresa
    'email' => [
        'nullable', 'email',
        Rule::unique('clients')->where(function ($query) {
            return $query->where('company_id', auth()->user()->company_id);
        })->ignore($this->my_client->id ?? null)
    ],
    // Tax ID único solo en mi empresa
    'tax_id' => [
        'nullable',
        Rule::unique('clients')->where(function ($query) {
            return $query->where('company_id', auth()->user()->company_id);
        })->ignore($this->my_client->id ?? null)
    ]
]);

        // Verificar límites de plan (Solo si es creación y NO es super admin)
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

        // Asignación de Empresa
        if (auth()->user()->is_super_admin) {
            $data['company_id'] = $this->company_id;
        } elseif (!$this->my_user) {
            // Si soy Admin normal, asigno a mi empresa
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

        if (auth()->user()->is_super_admin) {
            $headers[] = ['key' => 'company.name', 'label' => 'Empresa'];
        }

        return $headers;
    }

    public function with(): array {
        $query = User::with('company');

        // Filtro de seguridad para no mezclar empresas
        if (!auth()->user()->is_super_admin) {
            $query->where('company_id', auth()->user()->company_id);
        }

        $query->where('name', 'like', "%$this->search%");

        return [
            'users' => $query->orderBy('id', 'desc')->get(),
            'headers' => $this->headers(),
            'roles' => [
                ['id' => 'admin', 'name' => 'Administrador'], 
                ['id' => 'tech', 'name' => 'Técnico'],
                // ['id' => 'client', 'name' => 'Cliente (Portal)'] // Opcional mostrar clientes aquí
            ],
            'companies' => auth()->user()->is_super_admin ? Company::orderBy('name')->get() : []
        ];
    }
}; ?>

<div>
    <x-header title="Equipo de Trabajo" subtitle="Administra tus técnicos y accesos" separator>
        <x-slot:middle class="!justify-end">
            <x-input icon="o-magnifying-glass" placeholder="Buscar..." wire:model.live.debounce="search" />
        </x-slot:middle>
        <x-slot:actions>
            <x-button icon="o-plus" class="btn-primary" label="Nuevo Usuario" wire:click="create" />
        </x-slot:actions>
    </x-header>

    <x-card>
        <x-table :headers="$headers" :rows="$users" striped @row-click="$wire.edit($event.detail.id)" class="cursor-pointer">
            
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

            @scope('cell_company.name', $user)
                <span class="font-bold text-slate-700">{{ $user->company->name ?? '-' }}</span>
            @endscope

            {{-- BOTÓN DE ELIMINAR --}}
            @scope('actions', $user)
                <div class="flex" onclick="event.stopPropagation()">
                    <x-button 
                        icon="o-trash" 
                        spinner 
                        class="btn-sm btn-ghost text-error hover:bg-red-50" 
                        wire:click="delete({{ $user->id }})" 
                        confirm="¿Estás seguro de eliminar a {{ $user->name }}? Esta acción no se puede deshacer." 
                        tooltip="Eliminar Usuario"
                    />
                </div>
            @endscope

        </x-table>
        
        <x-slot:empty>
            <div class="text-center py-10 text-slate-400">
                <x-icon name="o-users" class="w-12 h-12 mx-auto mb-2 opacity-50" />
                No hay usuarios registrados en tu equipo.
            </div>
        </x-slot:empty>
    </x-card>

    <!-- DRAWER -->
    <x-drawer wire:model="drawer" title="{{ $my_user ? 'Editar Usuario' : 'Nuevo Usuario' }}" right class="w-full lg:w-1/3">
        <x-form wire:submit="save">
            
            <x-input label="Nombre" wire:model="name" icon="o-user" />
            <x-input label="Email" wire:model="email" icon="o-envelope" type="email" />
            
            <x-select label="Rol" :options="$roles" wire:model="role" icon="o-shield-check" />

            @if(auth()->user()->is_super_admin)
                <div class="bg-purple-50 p-4 rounded-lg border border-purple-100 my-2">
                    <div class="text-xs font-bold text-purple-800 uppercase mb-2">Zona Super Admin</div>
                    <x-select label="Empresa" :options="$companies" wire:model="company_id" icon="o-building-office-2" />
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
