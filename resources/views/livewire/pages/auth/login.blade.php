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

<div class="min-h-screen flex w-full">
    
    <div class="flex-1 flex items-center justify-center p-8 bg-white">
        <div class="w-full max-w-sm space-y-8">
            
            <div>
                <div class="flex items-center gap-2 mb-6">
                    <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center text-white">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5"><path fill-rule="evenodd" d="M14.615 1.595a.75.75 0 01.359.852L12.982 9.75h7.268a.75.75 0 01.548 1.262l-10.5 11.25a.75.75 0 01-1.272-.71l1.992-7.302H3.75a.75.75 0 01-.548-1.262l10.5-11.25a.75.75 0 01.913-.143z" clip-rule="evenodd" /></svg>
                    </div>
                    <span class="font-bold text-xl tracking-tight text-slate-900">TECHLIFE</span>
                </div>
                <h2 class="text-3xl font-bold tracking-tight text-slate-900">Bienvenido de nuevo</h2>
                <p class="mt-2 text-sm text-slate-500">Ingresa tus credenciales para acceder al portal.</p>
            </div>

            <form wire:submit="login" class="space-y-5">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Email Corporativo</label>
                    <input wire:model="email" type="email" class="input-pro" placeholder="nombre@empresa.com">
                    @error('email') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Contraseña</label>
                    <input wire:model="password" type="password" class="input-pro" placeholder="••••••••">
                    @error('password') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div class="flex items-center justify-between">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" wire:model="remember" class="rounded border-gray-300 text-blue-600 focus:ring-blue-600">
                        <span class="text-sm text-slate-600">Recordarme</span>
                    </label>
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="text-sm font-medium text-blue-600 hover:text-blue-800">
                            ¿Olvidaste tu clave?
                        </a>
                    @endif
                </div>

                <button type="submit" class="btn-pro" wire:loading.attr="disabled">
                    <span wire:loading.remove>Iniciar Sesión</span>
                    <span wire:loading class="opacity-50">Cargando...</span>
                </button>
            </form>

            @if (Route::has('register'))
            <div class="pt-6 text-center border-t border-gray-100">
                <p class="text-sm text-slate-500">
                    ¿No tienes una cuenta? 
                    <a href="{{ route('register') }}" class="font-semibold text-blue-600 hover:text-blue-800">Regístrate</a>
                </p>
            </div>
            @endif
        </div>
    </div>

    <div class="hidden lg:block relative flex-1 bg-slate-900">
        <img class="absolute inset-0 w-full h-full object-cover opacity-40" 
             src="https://images.unsplash.com/photo-1581091226825-a6a2a5aee158?q=80&w=2070&auto=format&fit=crop" 
             alt="Industrial Tech">
        <div class="relative z-10 flex flex-col justify-end h-full p-12 text-white">
            <div class="w-12 h-1 bg-blue-500 mb-6"></div>
            <h3 class="text-4xl font-bold leading-tight mb-4">Gestión Inteligente para Servicios Técnicos.</h3>
            <p class="text-lg text-slate-300 max-w-md">Optimiza operaciones, controla inventarios y fideliza clientes con la plataforma líder del mercado.</p>
        </div>
    </div>
</div>
