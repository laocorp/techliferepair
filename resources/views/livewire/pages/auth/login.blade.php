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
        $this->redirectIntended(default: RouteServiceProvider::HOME);
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

<div class="min-h-screen flex items-center justify-center bg-base-100 relative overflow-hidden">
    
    <div class="absolute inset-0 bg-[url('https://grainy-gradients.vercel.app/noise.svg')] opacity-20"></div>
    <div class="absolute top-[-10%] right-[-10%] w-96 h-96 bg-primary/10 rounded-full blur-[100px]"></div>

    <div class="w-full max-w-md p-8 relative z-10">
        
        <div class="text-center mb-10">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-base-200 border border-base-300 shadow-2xl mb-6">
                <x-icon name="o-lock-closed" class="w-8 h-8 text-primary" />
            </div>
            <h2 class="text-3xl font-black text-white tracking-tight">Bienvenido</h2>
            <p class="text-gray-500 mt-2 text-sm">Ingresa al panel de control administrativo.</p>
        </div>

        <div class="bg-base-200/50 backdrop-blur-sm border border-base-300 p-8 rounded-3xl shadow-2xl">
            <x-form wire:submit="login" class="space-y-5">
                
                <div class="space-y-2">
                    <label class="text-xs font-bold text-gray-400 ml-1 uppercase">Email Corporativo</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <x-icon name="o-envelope" class="w-5 h-5 text-gray-500" />
                        </div>
                        <input wire:model="email" type="email" 
                               class="w-full pl-11 pr-4 py-4 bg-base-100 border border-base-300 rounded-xl text-white focus:ring-2 focus:ring-primary focus:border-transparent transition-all outline-none placeholder-gray-600"
                               placeholder="usuario@techlife.com">
                    </div>
                    @error('email') <span class="text-error text-xs ml-1">{{ $message }}</span> @enderror
                </div>

                <div class="space-y-2">
                    <label class="text-xs font-bold text-gray-400 ml-1 uppercase">Contraseña</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <x-icon name="o-key" class="w-5 h-5 text-gray-500" />
                        </div>
                        <input wire:model="password" type="password" 
                               class="w-full pl-11 pr-4 py-4 bg-base-100 border border-base-300 rounded-xl text-white focus:ring-2 focus:ring-primary focus:border-transparent transition-all outline-none placeholder-gray-600"
                               placeholder="••••••••">
                    </div>
                    @error('password') <span class="text-error text-xs ml-1">{{ $message }}</span> @enderror
                </div>

                <div class="flex items-center justify-between pt-2">
                    <label class="flex items-center gap-3 cursor-pointer group">
                        <input type="checkbox" wire:model="remember" class="checkbox checkbox-primary checkbox-xs rounded-md" /> 
                        <span class="text-sm text-gray-400 group-hover:text-white transition">Mantener sesión</span>
                    </label>
                </div>

                <button type="submit" class="btn btn-primary w-full h-14 rounded-xl text-white text-lg font-bold shadow-lg shadow-primary/20 mt-4" wire:loading.attr="disabled">
                    <span wire:loading.remove>INGRESAR</span>
                    <span wire:loading class="loading loading-spinner"></span>
                </button>
            </x-form>
        </div>

        @if (Route::has('register'))
            <div class="mt-8 text-center">
                <a href="{{ route('register') }}" class="text-sm text-gray-500 hover:text-primary transition">
                    ¿No tienes cuenta? <span class="font-bold text-white">Solicitar Acceso</span>
                </a>
            </div>
        @endif
        
    </div>
</div>
