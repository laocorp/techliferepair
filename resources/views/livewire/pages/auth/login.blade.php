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
    #[Rule(['required', 'string', 'email'])] public string $email = '';
    #[Rule(['required', 'string'])] public string $password = '';
    #[Rule(['boolean'])] public bool $remember = false;

    public function login(): void {
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

    protected function ensureIsNotRateLimited(): void {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) return;
        $seconds = RateLimiter::availableIn($this->throttleKey());
        throw ValidationException::withMessages(['email' => trans('auth.throttle', ['seconds' => $seconds, 'minutes' => ceil($seconds / 60)])]);
    }

    protected function throttleKey(): string {
        return Str::transliterate(Str::lower($this->email).'|'.request()->ip());
    }
}; ?>

<div class="min-h-screen flex w-full bg-white">
    
    <!-- IZQUIERDA: FORMULARIO -->
    <div class="flex-1 flex items-center justify-center p-8 lg:p-24 bg-white z-10">
        <div class="w-full max-w-sm space-y-8">
            
            <div>
                <a href="/" class="flex items-center gap-2 mb-8 group">
                    <div class="w-8 h-8 bg-slate-900 rounded-lg flex items-center justify-center text-white shadow-md group-hover:scale-105 transition-transform">
                        <x-icon name="o-wrench-screwdriver" class="w-5 h-5" />
                    </div>
                    <span class="font-bold text-xl tracking-tight text-slate-900">TECHLIFE</span>
                </a>
                <h2 class="text-3xl font-black tracking-tight text-slate-900">Bienvenido de nuevo</h2>
                <p class="mt-2 text-sm text-slate-500">Ingresa al panel de control de tu taller.</p>
            </div>

            <form wire:submit="login" class="space-y-5">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-2 tracking-wide">Email</label>
                    <input wire:model="email" type="email" class="input-pro" placeholder="nombre@empresa.com">
                    @error('email') <span class="text-red-500 text-xs mt-1 block font-medium">{{ $message }}</span> @enderror
                </div>

                <div>
                    <div class="flex justify-between items-center mb-2">
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wide">Contraseña</label>
                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="text-xs font-semibold text-blue-600 hover:text-blue-800">¿Olvidaste tu clave?</a>
                        @endif
                    </div>
                    <input wire:model="password" type="password" class="input-pro" placeholder="••••••••">
                    @error('password') <span class="text-red-500 text-xs mt-1 block font-medium">{{ $message }}</span> @enderror
                </div>

                <div class="flex items-center mb-4">
                    <input type="checkbox" wire:model="remember" id="remember" class="rounded border-slate-300 text-slate-900 focus:ring-slate-900 h-4 w-4">
                    <label for="remember" class="ml-2 text-sm text-slate-600 font-medium">Mantener sesión iniciada</label>
                </div>

                <button type="submit" class="btn-pro" wire:loading.attr="disabled">
                    <span wire:loading.remove>Iniciar Sesión</span>
                    <span wire:loading class="opacity-75">Accediendo...</span>
                </button>
            </form>

            <div class="pt-8 text-center border-t border-slate-100">
                <p class="text-sm text-slate-500">
                    ¿Aún no tienes cuenta? 
                    <a href="{{ route('register') }}" class="font-bold text-slate-900 hover:text-blue-600 transition">Registra tu Empresa</a>
                </p>
            </div>
        </div>
    </div>

    <!-- DERECHA: VISUAL -->
    <div class="hidden lg:block relative flex-1 bg-slate-900 overflow-hidden">
        <img class="absolute inset-0 w-full h-full object-cover opacity-30 mix-blend-luminosity" 
             src="https://images.unsplash.com/photo-1581091226825-a6a2a5aee158?q=80&w=2070&auto=format&fit=crop" 
             alt="Tech Background">
        
        <div class="absolute inset-0 bg-gradient-to-t from-slate-900 via-transparent to-transparent"></div>

        <div class="relative z-10 flex flex-col justify-end h-full p-20 text-white">
            <div class="w-16 h-1 bg-blue-500 mb-8"></div>
            <h3 class="text-4xl font-bold leading-tight mb-6">"La herramienta definitiva para escalar nuestro servicio técnico."</h3>
            <div class="flex items-center gap-4">
                <div class="h-10 w-10 rounded-full bg-white/20 backdrop-blur-sm flex items-center justify-center">
                    <span class="font-bold">JD</span>
                </div>
                <div>
                    <div class="font-bold">John Doe</div>
                    <div class="text-sm text-slate-400">CEO, FixMaster Inc.</div>
                </div>
            </div>
        </div>
    </div>
</div>
