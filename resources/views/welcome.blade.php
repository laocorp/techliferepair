<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>TechLife | Sistema de Gestión</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700;900&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #fff; color: #0f172a; }
    </style>
</head>
<body class="antialiased">

    <!-- NAV -->
    <nav class="fixed w-full z-50 bg-white/80 backdrop-blur-md border-b border-slate-100">
        <div class="max-w-7xl mx-auto px-6 h-20 flex justify-between items-center">
            <div class="flex items-center gap-2">
                <div class="bg-slate-900 text-white p-2 rounded-lg shadow-md">
                    <x-icon name="o-wrench-screwdriver" class="w-5 h-5" />
                </div>
                <span class="font-bold text-xl tracking-tight text-slate-900">TECHLIFE</span>
            </div>
            @if (Route::has('login'))
                <div class="flex gap-4 items-center">
                    @auth
                        <a href="{{ url('/dashboard') }}" class="text-sm font-semibold text-slate-600 hover:text-slate-900 transition">Ir al Dashboard →</a>
                    @else
                        <a href="{{ route('login') }}" class="px-5 py-2.5 text-sm font-semibold text-white bg-slate-900 rounded-lg hover:bg-slate-800 transition shadow-lg shadow-slate-900/20">Iniciar Sesión</a>
                    @endauth
                </div>
            @endif
        </div>
    </nav>

    <!-- HERO SECTION -->
    <div class="relative pt-32 pb-20 lg:pt-48 overflow-hidden bg-gradient-to-b from-slate-50 to-white">
        <div class="max-w-7xl mx-auto px-6 grid lg:grid-cols-2 gap-16 items-center">
            
            <!-- TEXTO IZQUIERDA -->
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1 mb-6 text-xs font-bold tracking-wide text-blue-700 uppercase bg-blue-50 rounded-full border border-blue-100">
                    <span class="w-2 h-2 bg-blue-600 rounded-full animate-pulse"></span>
                    Sistema Operativo v2.0
                </div>
                <h1 class="text-5xl lg:text-7xl font-black tracking-tight text-slate-900 mb-6 leading-[1.1]">
                    Gestión total <br> para tu taller.
                </h1>
                <p class="text-lg text-slate-600 mb-8 leading-relaxed max-w-lg">
                    Software especializado para Centros de Servicio. Centraliza reparaciones, controla inventario y automatiza tu facturación en una plataforma segura.
                </p>
                
                <div class="flex flex-col sm:flex-row gap-4">
                    <a href="{{ route('register') }}" class="px-8 py-4 text-base font-bold text-white bg-blue-600 rounded-xl hover:bg-blue-700 transition shadow-xl shadow-blue-600/20 text-center">
                        Comenzar Gratis
                    </a>
                    
                    <!-- RASTREADOR RÁPIDO (WIDGET) -->
                    <form action="/track/search" method="POST" class="flex items-center shadow-sm rounded-xl overflow-hidden border border-slate-200 focus-within:ring-2 focus-within:ring-slate-900 transition">
                        @csrf
                        <div class="pl-4 text-slate-400">
                            <x-icon name="o-magnifying-glass" class="w-5 h-5" />
                        </div>
                        <input type="text" name="order_number" placeholder="Rastrear Orden #" class="h-14 px-3 border-none focus:ring-0 w-40 text-sm bg-white text-slate-900 placeholder-slate-400">
                        <button type="submit" class="h-14 px-5 bg-slate-50 hover:bg-slate-100 border-l border-slate-200 font-bold text-slate-600 text-sm transition">
                            Buscar
                        </button>
                    </form>
                </div>

                <div class="mt-12 pt-8 border-t border-slate-200 flex gap-8 text-sm font-semibold text-slate-500">
                    <div class="flex items-center gap-2">
                        <x-icon name="o-check-circle" class="w-5 h-5 text-green-500" />
                        Setup Instantáneo
                    </div>
                    <div class="flex items-center gap-2">
                        <x-icon name="o-shield-check" class="w-5 h-5 text-green-500" />
                        Datos Encriptados
                    </div>
                </div>
            </div>

            <!-- IMAGEN DERECHA (VISUAL) -->
            <div class="relative">
                <div class="absolute -inset-4 bg-gradient-to-r from-blue-100 to-slate-100 rounded-3xl blur-2xl opacity-50 -z-10"></div>
                <div class="relative rounded-2xl overflow-hidden shadow-2xl border border-slate-200 bg-white">
                    <img src="https://images.unsplash.com/photo-1460925895917-afdab827c52f?q=80&w=2015&auto=format&fit=crop" alt="Dashboard Preview" class="w-full h-auto object-cover opacity-95">
                    
                    <!-- Overlay UI Mockup -->
                    <div class="absolute inset-0 bg-gradient-to-t from-slate-900/60 to-transparent flex flex-col justify-end p-8">
                        <div class="text-white">
                            <div class="text-xs font-bold uppercase tracking-wider mb-1 text-blue-300">Vista Previa</div>
                            <h3 class="text-2xl font-bold">Panel de Control en Tiempo Real</h3>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

</body>
</html>
