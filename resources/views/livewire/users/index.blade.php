<?php

use Livewire\Volt\Component;
use App\Models\User;
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

    #[Rule('required|min:3')]
    public string $name = '';

    #[Rule('required|email')]
    public string $email = '';

    // Password es opcional al editar
    #[Rule('nullable|min:8')]
    public string $password = '';

    #[Rule('required')]
    public string $role = 'tech';

    public function mount(): void
    {
        // 🔒 SEGURIDAD: Solo el Admin entra aquí
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Acceso restringido a Administradores.');
        }
    }

    public function clean(): void
    {
        $this->reset(['drawer', 'my_user', 'name', 'email', 'password', 'role']);
        $this->resetValidation();
    }
	 public function create(): void
{
    $this->clean();
    $this->drawer = true;
}
    public function edit(User $user): void
    {
        $this->my_user = $user;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->role = $user->role;
        $this->password = ''; // No mostramos la password encriptada
        $this->drawer = true;
    }

    public function save(): void
    {
        // Validación dinámica para email único
        $this->validate([
            'name' => 'required|min:3',
            'email' => 'required|email|unique:users,email,' . ($this->my_user->id ?? 'NULL'),
            'role' => 'required',
            // Si es nuevo, password obligatoria. Si edita, opcional.
            'password' => $this->my_user ? 'nullable|min:8' : 'required|min:8'
        ]);

        $data = [
            'name' => $this->name,
            'email' => $this->email,
            'role' => $this->role,
        ];

        // Solo actualizamos password si escribió algo
        if (!empty($this->password)) {
            $data['password'] = Hash::make($this->password);
        }

        if ($this->my_user) {
            $this->my_user->update($data);
            $this->success('Usuario actualizado');
        } else {
            User::create($data);
            $this->success('Usuario creado');
        }

        $this->clean();
    }

    public function delete(User $user): void
    {
        if ($user->id === auth()->id()) {
            $this->error('No puedes eliminarte a ti mismo.');
            return;
        }
        $user->delete();
        $this->success('Usuario eliminado');
    }

    public function headers(): array
    {
        return [
            ['key' => 'name', 'label' => 'Nombre'],
            ['key' => 'email', 'label' => 'Email'],
            ['key' => 'role', 'label' => 'Rol'],
            ['key' => 'created_at', 'label' => 'Fecha Registro'],
        ];
    }

    public function with(): array
    {
        return [
            'users' => User::query()
                ->where('name', 'like', "%$this->search%")
                ->orderBy('name')
                ->get(),
            'headers' => $this->headers(),
            'roles' => [
                ['id' => 'admin', 'name' => 'Administrador (Acceso Total)'],
                ['id' => 'tech', 'name' => 'Técnico (Limitado)'],
            ]
        ];
    }
}; ?>

<div>
    <x-header title="Gestión de Equipo" subtitle="Control de acceso de usuarios" separator progress-indicator>
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
                @if($user->isAdmin())
                    <x-badge value="ADMIN" class="badge-primary" />
                @else
                    <x-badge value="TÉCNICO" class="badge-ghost" />
                @endif
            @endscope

            {{-- Fecha --}}
            @scope('cell_created_at', $user)
                {{ $user->created_at->format('d/m/Y') }}
            @endscope
            
            {{-- Acciones (Borrar) --}}
            @scope('actions', $user)
                <x-button icon="o-trash" spinner class="btn-sm btn-ghost text-error" wire:click.stop="delete({{ $user->id }})" confirm="¿Seguro que quieres eliminar a este usuario?" />
            @endscope
        </x-table>
    </x-card>

    <x-drawer wire:model="drawer" title="{{ $my_user ? 'Editar Usuario' : 'Nuevo Usuario' }}" right class="w-full lg:w-1/3">
        <x-form wire:submit="save">
            
            <x-input label="Nombre Completo" wire:model="name" icon="o-user" />
            <x-input label="Email Corporativo" wire:model="email" icon="o-envelope" type="email" />
            
            <x-select label="Rol / Permisos" icon="o-shield-check" :options="$roles" wire:model="role" />

            <x-input label="Contraseña" wire:model="password" icon="o-key" type="password" hint="{{ $my_user ? 'Déjalo en blanco para mantener la actual' : 'Mínimo 8 caracteres' }}" />

            <x-slot:actions>
                <x-button label="Cancelar" wire:click="clean" />
                <x-button label="Guardar" class="btn-primary" type="submit" spinner="save" />
            </x-slot:actions>
        </x-form>
    </x-drawer>
</div>
