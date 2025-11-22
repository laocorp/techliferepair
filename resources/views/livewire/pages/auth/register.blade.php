<?php

use App\Models\User;
use App\Models\Company;
use App\Models\Plan;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use Illuminate\Support\Str;

new #[Layout('layouts.guest')] class extends Component {
    
    public string $company_name = '';
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';

    public function register(): void
    {
        $validated = $this->validate([
            'company_name' => ['required', 'string', 'max:255', 'unique:companies,name'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
        ]);

        $plan = Plan::first(); 
        if (!$plan) {
            $plan = Plan::create(['name' => 'Básico', 'price' => 0, 'max_users' => 3, 'max_orders' => 50]);
        }

        $company = Company::create([
            'name' => $this->company_name,
            'slug' => Str::slug($this->company_name) . '-' . rand(1000,9999),
            'plan_id' => $plan->id,
            'status' => 'active',
            'valid_until' => now()->addDays(30)
        ]);

        $user = User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => Hash::make($this->password),
            'role' => 'admin',
            'company_id' => $company->id
        ]);

        event(new Registered($user));

        Auth::login($user);

        $this->redirect(route('dashboard'));
    }
}; ?>

<div class="min-h-screen flex w-full bg-white">
    
    <!-- LADO IZQUIERDO: IMAGEN (Invertido para dinamismo) -->
    <div class="hidden lg:block relative flex-1 bg-slate-900 overflow-hidden">
        <img class="absolute inset-0 w-full h-full object-cover opacity-40 mix-blend-overlay" 
             src="https://images.unsplash.com/photo-1581092160562-40aa08e78837?q=80&w=2070&auto=format&fit=crop" 
             alt="Workshop">
        
        <div class="absolute inset-0 bg-gradient-to-b from-slate-900/80 via-transparent to-slate-900/90"></div>
        
        <div class="relative z-10 p-16 h-full flex flex-col justify-between">
            <div class="flex items-center gap-2 text-white font-bold text-xl">
                <x-icon name="o-wrench-screwdriver" class="w-6 h-6" /> TECHLIFE
            </div>
            <div>
                <h3 class="text-3xl font-bold text-white leading-tight mb-4">
                    Comienza tu prueba gratuita de 30 días.
                </h3>
                <ul class="space-y-3 text-slate-300">
                    <li class="flex items-center gap-2"><x-icon name="o-check" class="w-5 h-5 text-green-400" /> Gestión ilimitada de órdenes</li>
                    <li class="flex items-center gap-2"><x-icon name="o-check" class="w-5 h-5 text-green-400" /> Control de inventario avanzado</li>
                    <li class="flex items-center gap-2"><x-icon name="o-check" class="w-5 h-5 text-green-400" /> Portal de clientes incluido</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- LADO DERECHO: FORMULARIO -->
    <div class="flex-1 flex items-center justify-center p-8 lg:p-24 bg-white">
        <div class="w-full max-w-md space-y-6">
            
            <div>
                <h2 class="text-3xl font-bold tracking-tight text-slate-900">Registra tu Taller</h2>
                <p class="mt-2 text-sm text-slate-500">Configura tu espacio de trabajo en segundos.</p>
            </div>

            <form wire:submit="register" class="space-y-5">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-2 tracking-wide">Nombre de tu Empresa</label>
                    <input wire:model="company_name" type="text" class="input-pro" placeholder="Ej. Reparaciones Pérez">
                    @error('company_name') <span class="text-red-500 text-xs mt-1 block font-medium">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-2 tracking-wide">Tu Nombre</label>
                    <input wire:model="name" type="text" class="input-pro" placeholder="Juan Pérez">
                    @error('name') <span class="text-red-500 text-xs mt-1 block font-medium">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-2 tracking-wide">Email</label>
                    <input wire:model="email" type="email" class="input-pro" placeholder="tu@email.com">
                    @error('email') <span class="text-red-500 text-xs mt-1 block font-medium">{{ $message }}</span> @enderror
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-2 tracking-wide">Contraseña</label>
                        <input wire:model="password" type="password" class="input-pro" placeholder="Mín 8 car.">
                        @error('password') <span class="text-red-500 text-xs mt-1 block font-medium">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-2 tracking-wide">Confirmar</label>
                        <input wire:model="password_confirmation" type="password" class="input-pro" placeholder="Repetir">
                    </div>
                </div>

                <button type="submit" class="btn-pro" wire:loading.attr="disabled">
                    <span wire:loading.remove>Crear Cuenta</span>
                    <span wire:loading class="opacity-75">Configurando...</span>
                </button>
            </form>

            <div class="pt-6 text-center border-t border-slate-100">
                <p class="text-sm text-slate-500">
                    ¿Ya tienes cuenta? 
                    <a href="{{ route('login') }}" class="font-bold text-slate-900 hover:underline">Inicia Sesión</a>
                </p>
            </div>
        </div>
    </div>
</div>
