<?php

use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component {
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';

    public function register(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
        ]);

        $validated['password'] = Hash::make($validated['password']);

        event(new Registered($user = User::create($validated)));

        Auth::login($user);

$this->redirect(route('dashboard'));
    }
}; ?>

<div class="min-h-screen flex items-center justify-center bg-base-100 relative overflow-hidden py-10">
    
    <div class="w-full max-w-md p-6 relative z-10">
        
        <div class="text-center mb-8">
            <h2 class="text-2xl font-black text-white tracking-tight">Alta de Usuario</h2>
            <p class="text-gray-500 mt-1 text-sm">Registro de nuevo personal técnico.</p>
        </div>

        <div class="bg-base-200/50 backdrop-blur-sm border border-base-300 p-8 rounded-3xl shadow-2xl">
            <x-form wire:submit="register" class="space-y-4">
                
                <div>
                    <label class="text-xs font-bold text-gray-400 ml-1 uppercase">Nombre Completo</label>
                    <input wire:model="name" type="text" class="mt-1 w-full px-4 py-3 bg-base-100 border border-base-300 rounded-xl text-white focus:ring-2 focus:ring-primary outline-none">
                    @error('name') <span class="text-error text-xs">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="text-xs font-bold text-gray-400 ml-1 uppercase">Email</label>
                    <input wire:model="email" type="email" class="mt-1 w-full px-4 py-3 bg-base-100 border border-base-300 rounded-xl text-white focus:ring-2 focus:ring-primary outline-none">
                    @error('email') <span class="text-error text-xs">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="text-xs font-bold text-gray-400 ml-1 uppercase">Contraseña</label>
                    <input wire:model="password" type="password" class="mt-1 w-full px-4 py-3 bg-base-100 border border-base-300 rounded-xl text-white focus:ring-2 focus:ring-primary outline-none">
                    @error('password') <span class="text-error text-xs">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="text-xs font-bold text-gray-400 ml-1 uppercase">Confirmar</label>
                    <input wire:model="password_confirmation" type="password" class="mt-1 w-full px-4 py-3 bg-base-100 border border-base-300 rounded-xl text-white focus:ring-2 focus:ring-primary outline-none">
                </div>

                <button type="submit" class="btn btn-primary w-full h-12 rounded-xl text-white font-bold shadow-lg shadow-primary/20 mt-2" wire:loading.attr="disabled">
                    <span wire:loading.remove>REGISTRAR</span>
                    <span wire:loading class="loading loading-spinner"></span>
                </button>
            </x-form>
        </div>

        <div class="mt-8 text-center">
            <a href="{{ route('login') }}" class="text-sm text-gray-500 hover:text-primary transition">
                ¿Ya tienes acceso? <span class="font-bold text-white">Inicia Sesión</span>
            </a>
        </div>
        
    </div>
</div>
