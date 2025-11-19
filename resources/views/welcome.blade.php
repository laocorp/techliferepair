<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="repair-pro">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>TechLife | Enterprise System</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased font-sans bg-base-100 text-base-content">

    <nav class="w-full fixed top-0 z-50 backdrop-blur-md border-b border-base-300/50 bg-base-100/80">
        <div class="max-w-7xl mx-auto px-6 h-20 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-primary/10 rounded-xl flex items-center justify-center border border-primary/20">
                    <x-icon name="o-wrench-screwdriver" class="w-6 h-6 text-primary" />
                </div>
                <div class="leading-tight">
                    <div class="font-black text-xl tracking-tight text-white">TECHLIFE</div>
                    <div class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Enterprise</div>
                </div>
            </div>
        <div class="w-full max-w-md mx-auto mb-10">
            <form action="/track/search" method="POST" class="relative group">
                @csrf
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <x-icon name="o-magnifying-glass" class="w-5 h-5 text-gray-400 group-focus-within:text-primary transition" />
                </div>
                <input type="text" name="order_number" placeholder="Ingresa tu # de Orden (Ej. 0001)" 
                       class="w-full pl-12 pr-4 py-4 bg-white/10 backdrop-blur-md border border-white/20 rounded-full text-white placeholder-gray-400 focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition-all shadow-xl">
                <button type="submit" class="absolute right-2 top-2 bottom-2 bg-primary hover:bg-primary/80 text-white px-6 rounded-full font-bold text-sm transition-all">
                    Rastrear
                </button>
            </form>
        </div>    
            @if (Route::has('login'))
                <div class="flex gap-4">
	
                    @auth
                        <a href="{{ url('/dashboard') }}" class="btn btn-sm btn-ghost">Ir al Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-primary btn-sm px-6 font-bold shadow-lg shadow-primary/20">
                            Iniciar Sesión
                        </a>
                    @endauth
                </div>
            @endif
        </div>
    </nav>

    <div class="relative min-h-screen flex items-center justify-center overflow-hidden pt-20">
        
        <div class="absolute top-0 left-1/2 -translate-x-1/2 w-[1000px] h-[500px] bg-primary/10 rounded-full blur-[120px] pointer-events-none"></div>

        <div class="relative z-10 max-w-5xl mx-auto px-6 text-center">
            
            <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full border border-base-300 bg-base-200/50 text-xs font-bold text-gray-400 mb-8">
                <span class="w-2 h-2 rounded-full bg-success animate-pulse"></span>
                Sistema Operativo v1.0
            </div>

            <h1 class="text-5xl md:text-7xl font-black tracking-tight text-white mb-8 leading-tight">
                Gestión técnica <br>
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-primary to-secondary">inteligente y centralizada.</span>
            </h1>
            
            <p class="text-xl text-gray-400 max-w-2xl mx-auto mb-12 font-light leading-relaxed">
                Plataforma integral para Centros de Servicio Autorizados. Control de activos, trazabilidad en tiempo real y facturación automatizada.
            </p>

            <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
                @if (Route::has('login'))
                    @auth
                        <a href="{{ url('/dashboard') }}" class="btn btn-primary btn-lg px-10 w-full sm:w-auto text-white shadow-xl shadow-primary/30 border-none">
                            Acceder al Panel
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-primary btn-lg px-10 w-full sm:w-auto text-white shadow-xl shadow-primary/30 border-none">
                            Ingresar al Sistema
                        </a>
                        <div class="text-sm text-gray-500 mt-4 sm:mt-0 sm:ml-4">
                            Acceso restringido a personal autorizado.
                        </div>
                    @endauth
                @endif
            </div>

            <div class="mt-20 grid grid-cols-1 md:grid-cols-3 gap-6 text-left opacity-60">
                <div class="p-6 border border-base-300 bg-base-200/30 rounded-2xl">
                    <x-icon name="o-cube" class="w-8 h-8 text-primary mb-4" />
                    <h3 class="font-bold text-white mb-2">Control de Activos</h3>
                    <p class="text-sm text-gray-500">Historial completo por serial.</p>
                </div>
                <div class="p-6 border border-base-300 bg-base-200/30 rounded-2xl">
                    <x-icon name="o-clipboard-document-list" class="w-8 h-8 text-primary mb-4" />
                    <h3 class="font-bold text-white mb-2">Órdenes de Trabajo</h3>
                    <p class="text-sm text-gray-500">Flujo de estados y repuestos.</p>
                </div>
                <div class="p-6 border border-base-300 bg-base-200/30 rounded-2xl">
                    <x-icon name="o-chart-bar" class="w-8 h-8 text-primary mb-4" />
                    <h3 class="font-bold text-white mb-2">Reportes</h3>
                    <p class="text-sm text-gray-500">Métricas de rendimiento.</p>
                </div>
            </div>

        </div>
    </div>

</body>
</html>
