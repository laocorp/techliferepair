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

    <nav class="fixed w-full z-50 bg-white/80 backdrop-blur-md border-b border-slate-100">
        <div class="max-w-7xl mx-auto px-6 h-20 flex justify-between items-center">
            <div class="flex items-center gap-2">
                <div class="bg-blue-600 text-white p-2 rounded-lg">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M11.42 15.17L17.25 21A2.652 2.652 0 0021 17.25l-5.877-5.877M11.42 15.17l2.496-3.03c.317-.384.74-.626 1.208-.766M11.42 15.17l-4.655 5.653a2.548 2.548 0 11-3.586-3.586l6.837-5.63m5.108-.233c.55-.164 1.163-.188 1.703-.127 1.543.174 2.099.563 2.099.563L15 2.25 8.684 7.812C8.519 9.388 9.207 10.593 10.167 11.264" /></svg>
                </div>
                <span class="font-bold text-xl tracking-tight">TECHLIFE</span>
            </div>
            @if (Route::has('login'))
                <div class="flex gap-4">
                    @auth
                        <a href="{{ url('/dashboard') }}" class="text-sm font-semibold text-slate-600 hover:text-blue-600 transition">Ir al Dashboard →</a>
                    @else
                        <a href="{{ route('login') }}" class="px-5 py-2.5 text-sm font-semibold text-white bg-slate-900 rounded-lg hover:bg-slate-800 transition">Iniciar Sesión</a>
                    @endauth
                </div>
            @endif
        </div>
    </nav>

    <div class="relative pt-32 pb-20 lg:pt-48 overflow-hidden">
        <div class="max-w-7xl mx-auto px-6 grid lg:grid-cols-2 gap-12 items-center">
            
            <div>
                <div class="inline-block px-4 py-1.5 mb-6 text-xs font-semibold tracking-wide text-blue-600 uppercase bg-blue-50 rounded-full border border-blue-100">
                    Solución Enterprise v2.0
                </div>
                <h1 class="text-5xl lg:text-7xl font-black tracking-tight text-slate-900 mb-6 leading-tight">
                    Control total <br> de tu taller.
                </h1>
                <p class="text-lg text-slate-600 mb-8 leading-relaxed max-w-lg">
                    Software especializado para Centros de Servicio. Gestiona reparaciones, inventario y garantías en una plataforma unificada y segura.
                </p>
                <div class="flex gap-4">
                    <a href="{{ route('login') }}" class="px-8 py-4 text-base font-bold text-white bg-blue-600 rounded-xl hover:bg-blue-700 transition shadow-lg shadow-blue-600/20">
                        Comenzar Ahora
                    </a>
                    <form action="/track/search" method="POST" class="flex items-center">
                        @csrf
                        <input type="text" name="order_number" placeholder="# Orden" class="h-14 px-4 border border-slate-200 rounded-l-xl focus:outline-none focus:border-blue-500 w-32 text-sm">
                        <button type="submit" class="h-14 px-4 bg-slate-100 border border-slate-200 border-l-0 rounded-r-xl hover:bg-slate-200 font-bold text-slate-600 text-sm">
                            Rastrear
                        </button>
                    </form>
                </div>
                <div class="mt-10 pt-8 border-t border-slate-100 flex gap-8 text-sm font-semibold text-slate-500">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        Setup Instantáneo
                    </div>
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        Soporte 24/7
                    </div>
                </div>
            </div>

            <div class="relative rounded-2xl overflow-hidden shadow-2xl border border-slate-200 bg-slate-50">
                <img src="https://images.unsplash.com/photo-1551288049-bebda4e38f71?q=80&w=2070&auto=format&fit=crop" alt="Dashboard" class="w-full h-auto opacity-90">
                <div class="absolute inset-0 bg-gradient-to-t from-slate-900/50 to-transparent"></div>
                <div class="absolute bottom-8 left-8 right-8 text-white">
                    <div class="text-sm font-medium opacity-90">Dashboard de Control</div>
                    <div class="text-2xl font-bold">Monitoreo en tiempo real</div>
                </div>
            </div>

        </div>
    </div>

</body>
</html>
