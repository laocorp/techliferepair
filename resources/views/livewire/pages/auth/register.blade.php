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
    
    <!-- IZQUIERDA: BENEFICIOS (Invertido) -->
    <div class="hidden lg:block relative flex-1 bg-slate-900 overflow-hidden">
        <img class="absolute inset-0 w-full h-full object-cover opacity-20 mix-blend-luminosity" 
             src="https://images.unsplash.com/photo-1581092160562-40aa08e78837?q=80&w=2070&auto=format&fit=crop" 
             alt="Workshop">
        <div class="absolute inset-0 bg-slate-900/50"></div>
        
        <div class="relative z-10 p-20 h-full flex flex-col justify-center">
            <div class="mb-10">
                 <span class="text-blue-400 font-bold tracking-widest uppercase text-xs">TechLife Enterprise</span>
                 <h2 class="text-4xl font-black text-white mt-4 mb-6 leading-tight">
                    Comienza tu prueba <br> gratuita de 30 días.
                 </h2>
                 <p class="text-slate-400 text-lg max-w-md">Únete a más de 500 centros de servicio que ya optimizaron su operación.</p>
            </div>

            <ul class="space-y-6">
                <li class="flex items-start gap-4">
                    <div class="p-2 bg-green-500/10 rounded-lg text-green-400"><x-icon name="o-check" class="w-6 h-6" /></div>
                    <div>
                        <h4 class="text-white font-bold">Gestión Ilimitada</h4>
                        <p class="text-slate-400 text-sm">Crea órdenes, clientes y productos sin restricciones.</p>
                    </div>
                </li>
                <li class="flex items-start gap-4">
                    <div class="p-2 bg-green-500/10 rounded-lg text-green-400"><x-icon name="o-check" class="w-6 h-6" /></div>
                    <div>
                        <h4 class="text-white font-bold">Soporte Prioritario</h4>
                        <p class="text-slate-400 text-sm">Acceso a nuestro equipo de expertos en migración.</p>
                    </div>
                </li>
            </ul>
        </div>
    </div>

    <!-- DERECHA: FORMULARIO -->
    <div class="flex-1 flex items-center justify-center p-8 lg:p-16 bg-white overflow-y-auto">
        <div class="w-full max-w-md space-y-8 my-auto">
            
            <div>
                <h2 class="text-3xl font-black tracking-tight text-slate-900">Registrar Empresa</h2>
                <p class="mt-2 text-sm text-slate-500">Configura tu cuenta administrativa.</p>
            </div>

            <form wire:submit="register" class="space-y-5">
                
                <!-- Nombre Empresa -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-2 tracking-wide">Nombre del Taller / Empresa</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <x-icon name="o-building-office" class="w-5 h-5 text-slate-400" />
                        </div>
                        <input wire:model="company_name" type="text" class="input-pro pl-10" placeholder="Ej. Servicios Técnicos Pérez" autofocus>
                    </div>
                    @error('company_name') <span class="text-red-500 text-xs mt-1 block font-medium">{{ $message }}</span> @enderror
                </div>

                <!-- Datos Usuario -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
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
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-2 tracking-wide">Contraseña Maestra</label>
                    <input wire:model="password" type="password" class="input-pro" placeholder="Mínimo 8 caracteres">
                    @error('password') <span class="text-red-500 text-xs mt-1 block font-medium">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-2 tracking-wide">Confirmar Contraseña</label>
                    <input wire:model="password_confirmation" type="password" class="input-pro" placeholder="Repite tu clave">
                </div>

                <div class="flex items-start gap-2 pt-2">
                     <input type="checkbox" required class="mt-1 rounded border-slate-300 text-slate-900 focus:ring-slate-900">
                     <p class="text-xs text-slate-500">Acepto los <a href="/legal" class="text-blue-600 underline" target="_blank">Términos de Servicio</a> y la Política de Privacidad.</p>
                </div>

                <button type="submit" class="btn-pro" wire:loading.attr="disabled">
                    <span wire:loading.remove>Crear Cuenta Empresarial</span>
                    <span wire:loading class="flex items-center gap-2">
                        <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Procesando...
                    </span>
                </button>
            </form>

            <div class="pt-6 text-center border-t border-slate-100">
                <p class="text-sm text-slate-500">
                    ¿Ya tienes cuenta? 
                    <a href="{{ route('login') }}" class="font-bold text-slate-900 hover:text-blue-600 transition">Inicia Sesión</a>
                </p>
            </div>
        </div>
    </div>
</div>
