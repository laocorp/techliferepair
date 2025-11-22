<?php

use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Rule;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component {
    #[Rule(['required', 'string', 'email'])]
    public string $email = '';

    #[Rule(['required', 'string'])]
    public string $password = '';

    #[Rule(['boolean'])]
    public bool $remember = false;

    public function login(): void
    {
        $this->validate();
        $this->ensureIsNotRateLimited();

        if (! Auth::attempt($this->only(['email', 'password'], $this->remember))) {
            RateLimiter::hit($this->throttleKey());
            throw ValidationException::withMessages(['email' => trans('auth.failed')]);
        }

        RateLimiter::clear($this->throttleKey());
        session()->regenerate();
        $this->redirectIntended(default: route('dashboard'));
    }

    protected function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) return;
        $seconds = RateLimiter::availableIn($this->throttleKey());
        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', ['seconds' => $seconds, 'minutes' => ceil($seconds / 60)]),
        ]);
    }

    protected function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->email).'|'.request()->ip());
    }
}; ?>

<div class="min-h-screen flex w-full bg-white">
    
    <!-- SECCIÓN IZQUIERDA: FORMULARIO -->
    <div class="flex-1 flex items-center justify-center p-8 lg:p-24">
        <div class="w-full max-w-sm space-y-8">
            
            <!-- Header -->
            <div>
                <div class="flex items-center gap-2 mb-8">
                    <div class="w-8 h-8 bg-slate-900 rounded-lg flex items-center justify-center text-white shadow-md">
                        <x-icon name="o-wrench-screwdriver" class="w-5 h-5" />
                    </div>
                    <span class="font-bold text-xl tracking-tight text-slate-900">TECHLIFE</span>
                </div>
                <h2 class="text-3xl font-bold tracking-tight text-slate-900">Bienvenido</h2>
                <p class="mt-2 text-sm text-slate-500">Ingresa tus credenciales para acceder al portal.</p>
            </div>

            <!-- Form -->
            <form wire:submit="login" class="space-y-5">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-2 tracking-wide">Email Corporativo</label>
                    <input wire:model="email" type="email" class="input-pro" placeholder="nombre@empresa.com">
                    @error('email') <span class="text-red-500 text-xs mt-1 block font-medium">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-2 tracking-wide">Contraseña</label>
                    <input wire:model="password" type="password" class="input-pro" placeholder="••••••••">
                    @error('password') <span class="text-red-500 text-xs mt-1 block font-medium">{{ $message }}</span> @enderror
                </div>

                <div class="flex items-center justify-between">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" wire:model="remember" class="rounded border-slate-300 text-slate-900 focus:ring-slate-900">
                        <span class="text-sm text-slate-600 font-medium">Recordarme</span>
                    </label>
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="text-sm font-semibold text-blue-600 hover:text-blue-800 transition">
                            Recuperar clave
                        </a>
                    @endif
                </div>

                <button type="submit" class="btn-pro" wire:loading.attr="disabled">
                    <span wire:loading.remove>Iniciar Sesión</span>
                    <span wire:loading class="opacity-75">Verificando...</span>
                </button>
            </form>

            @if (Route::has('register'))
            <div class="pt-8 text-center border-t border-slate-100">
                <p class="text-sm text-slate-500">
                    ¿No tienes una cuenta? 
                    <a href="{{ route('register') }}" class="font-bold text-slate-900 hover:underline">Solicitar Acceso</a>
                </p>
            </div>
            @endif
        </div>
    </div>

    <!-- SECCIÓN DERECHA: IMAGEN (Solo en pantallas grandes) -->
    <div class="hidden lg:block relative flex-1 bg-slate-900 overflow-hidden">
        <img class="absolute inset-0 w-full h-full object-cover opacity-40 mix-blend-overlay" 
             src="https://images.unsplash.com/photo-1504384308090-c54be3855833?q=80&w=1974&auto=format&fit=crop" 
             alt="Tech Background">
        
        <div class="absolute inset-0 bg-gradient-to-t from-slate-900 via-transparent to-transparent"></div>

        <div class="relative z-10 flex flex-col justify-end h-full p-16 text-white">
            <div class="w-12 h-1 bg-blue-500 mb-6"></div>
            <h3 class="text-4xl font-bold leading-tight mb-4">Eficiencia operativa para talleres modernos.</h3>
            <p class="text-lg text-slate-300 max-w-md leading-relaxed">
                "TechLife ha transformado la manera en que gestionamos nuestro servicio técnico. Simplemente indispensable."
            </p>
        </div>
    </div>
</div>
