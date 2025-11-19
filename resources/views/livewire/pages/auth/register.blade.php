<?php

use App\Models\User;
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

<div class="min-h-screen flex w-full">
    
    <div class="hidden lg:block relative flex-1 bg-slate-900">
        <img class="absolute inset-0 w-full h-full object-cover opacity-40" 
             src="https://images.unsplash.com/photo-1486312338219-ce68d2c6f44d?q=80&w=2072&auto=format&fit=crop" 
             alt="Workspace">
        <div class="relative z-10 flex flex-col justify-end h-full p-12 text-white">
            <div class="w-12 h-1 bg-blue-500 mb-6"></div>
            <h3 class="text-4xl font-bold leading-tight mb-4">Únete a TechLife Enterprise.</h3>
            <p class="text-lg text-slate-300 max-w-md">Comienza hoy a transformar la productividad de tu taller.</p>
        </div>
    </div>

    <div class="flex-1 flex items-center justify-center p-8 bg-white">
        <div class="w-full max-w-sm space-y-6">
            
            <div>
                <h2 class="text-3xl font-bold tracking-tight text-slate-900">Crear Cuenta</h2>
                <p class="mt-2 text-sm text-slate-500">Completa tus datos para comenzar.</p>
            </div>

            <form wire:submit="register" class="space-y-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Nombre Completo</label>
                    <input wire:model="name" type="text" class="input-pro" placeholder="Juan Pérez">
                    @error('name') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Email</label>
                    <input wire:model="email" type="email" class="input-pro" placeholder="tu@email.com">
                    @error('email') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Contraseña</label>
                    <input wire:model="password" type="password" class="input-pro" placeholder="Mínimo 8 caracteres">
                    @error('password') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Confirmar Contraseña</label>
                    <input wire:model="password_confirmation" type="password" class="input-pro" placeholder="Repite tu clave">
                </div>

                <button type="submit" class="btn-pro" wire:loading.attr="disabled">
                    <span wire:loading.remove>Registrarse</span>
                    <span wire:loading class="opacity-50">Creando cuenta...</span>
                </button>
            </form>

            <div class="pt-6 text-center border-t border-gray-100">
                <p class="text-sm text-slate-500">
                    ¿Ya tienes cuenta? 
                    <a href="{{ route('login') }}" class="font-semibold text-blue-600 hover:text-blue-800">Inicia Sesión</a>
                </p>
            </div>
        </div>
    </div>
</div>
