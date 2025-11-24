<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'TechLife') }} | Sistema de Gestión para Talleres</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Inter', sans-serif; scroll-behavior: smooth; }
        .gradient-text {
            background: linear-gradient(to right, #0f172a, #3b82f6);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
    </style>
</head>
<body class="antialiased bg-white text-slate-900">

    <!-- NAV -->
    <nav class="fixed w-full z-50 bg-white/90 backdrop-blur-md border-b border-slate-100 transition-all">
        <div class="max-w-7xl mx-auto px-6 h-20 flex justify-between items-center">
            <div class="flex items-center gap-2">
                <div class="bg-slate-900 text-white p-2 rounded-lg shadow-lg shadow-slate-900/20">
                    <x-icon name="o-wrench-screwdriver" class="w-5 h-5" />
                </div>
                <span class="font-bold text-xl tracking-tight text-slate-900">TECHLIFE</span>
            </div>
            
            <div class="hidden md:flex items-center gap-8 text-sm font-medium text-slate-600">
                <a href="#features" class="hover:text-slate-900 transition">Características</a>
                <a href="#track" class="hover:text-slate-900 transition">Rastrear Orden</a>
                <a href="/legal" class="hover:text-slate-900 transition">Ayuda</a>
            </div>

            <div class="flex gap-4 items-center">
                @if (Route::has('login'))
                    @auth
                        <a href="{{ url('/dashboard') }}" class="text-sm font-bold text-slate-900 hover:text-blue-600">Ir al Dashboard →</a>
                    @else
                        <a href="{{ route('login') }}" class="hidden md:block text-sm font-bold text-slate-600 hover:text-slate-900 px-2">Login</a>
                        <a href="{{ route('register') }}" class="px-5 py-2.5 text-sm font-bold text-white bg-slate-900 rounded-lg hover:bg-slate-800 transition shadow-lg shadow-slate-900/20 transform hover:-translate-y-0.5">
                            Prueba Gratis
                        </a>
                    @endauth
                @endif
            </div>
        </div>
    </nav>

    <!-- HERO SECTION -->
    <div class="relative pt-32 pb-20 lg:pt-48 overflow-hidden">
        <div class="absolute top-0 left-1/2 -translate-x-1/2 w-[1000px] h-[500px] bg-blue-50/50 rounded-full blur-[100px] -z-10"></div>
        
        <div class="max-w-7xl mx-auto px-6 grid lg:grid-cols-2 gap-16 items-center">
            
            <!-- Texto -->
            <div class="text-center lg:text-left">
                <div class="inline-flex items-center gap-2 px-3 py-1 mb-6 text-xs font-bold tracking-wide text-blue-700 uppercase bg-blue-50 rounded-full border border-blue-100">
                    <span class="w-2 h-2 bg-blue-600 rounded-full animate-pulse"></span>
                    SaaS v5.0 Enterprise
                </div>
                <h1 class="text-5xl lg:text-7xl font-black tracking-tight text-slate-900 mb-6 leading-[1.1]">
                    El sistema operativo de tu <span class="gradient-text">Taller.</span>
                </h1>
                <p class="text-lg text-slate-600 mb-8 leading-relaxed max-w-lg mx-auto lg:mx-0">
                    Centraliza reparaciones, inventario, ventas y clientes en una sola plataforma. Diseñado para Centros de Servicio que buscan escalar.
                </p>
                
                <div class="flex flex-col sm:flex-row gap-4 justify-center lg:justify-start">
                    <a href="{{ route('register') }}" class="px-8 py-4 text-base font-bold text-white bg-blue-600 rounded-xl hover:bg-blue-700 transition shadow-xl shadow-blue-600/20 text-center">
                        Crear Cuenta de Empresa
                    </a>
                    <a href="#features" class="px-8 py-4 text-base font-bold text-slate-700 bg-white border border-slate-200 rounded-xl hover:bg-slate-50 transition text-center">
                        Ver Demo
                    </a>
                </div>

                <div class="mt-10 flex items-center justify-center lg:justify-start gap-6 text-sm text-slate-500 font-medium">
                    <div class="flex items-center gap-2"><x-icon name="o-check-circle" class="w-5 h-5 text-green-500" /> Sin tarjeta de crédito</div>
                    <div class="flex items-center gap-2"><x-icon name="o-check-circle" class="w-5 h-5 text-green-500" /> Setup en 2 min</div>
                </div>
            </div>

            <!-- Widget de Rastreo (Para clientes finales) -->
            <div id="track" class="relative">
                <div class="absolute -inset-1 bg-gradient-to-r from-blue-500 to-indigo-500 rounded-2xl blur opacity-20"></div>
                <div class="relative bg-white p-8 rounded-2xl shadow-2xl border border-slate-100">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="p-2 bg-slate-100 rounded-lg"><x-icon name="o-magnifying-glass" class="w-6 h-6 text-slate-900" /></div>
                        <div>
                            <h3 class="font-bold text-lg text-slate-900">Rastreo de Reparación</h3>
                            <p class="text-xs text-slate-500">Consulta el estado de tu equipo en tiempo real</p>
                        </div>
                    </div>

                    <form action="/track/search" method="POST" class="space-y-4">
                        @csrf
                        <div>
                            <label class="text-xs font-bold text-slate-500 uppercase mb-1 block">Número de Orden / Ticket</label>
                            <input type="text" name="order_number" placeholder="Ej. 0001" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-lg focus:ring-2 focus:ring-slate-900 outline-none font-mono text-lg">
                        </div>
                        <button type="submit" class="w-full py-3 bg-slate-900 text-white font-bold rounded-lg hover:bg-slate-800 transition shadow-lg">
                            Consultar Estado
                        </button>
                    </form>
                    
                    <div class="mt-6 pt-6 border-t border-slate-100 text-center">
                        <p class="text-xs text-slate-400">¿Tienes dudas? <a href="https://wa.me/593900000000" class="text-blue-600 hover:underline">Contacta a soporte</a></p>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- CARACTERÍSTICAS (FEATURES) -->
    <div id="features" class="py-24 bg-slate-50 border-y border-slate-200">
        <div class="max-w-7xl mx-auto px-6">
            <div class="text-center max-w-3xl mx-auto mb-16">
                <h2 class="text-3xl font-black text-slate-900 mb-4">Todo lo que necesitas para operar</h2>
                <p class="text-slate-500 text-lg">Dejamos atrás las hojas de cálculo. TechLife profesionaliza cada aspecto de tu negocio.</p>
            </div>

            <div class="grid md:grid-cols-3 gap-8">
                <!-- Feature 1 -->
                <div class="bg-white p-8 rounded-2xl shadow-sm border border-slate-200 hover:shadow-md transition-shadow">
                    <div class="w-12 h-12 bg-blue-50 rounded-xl flex items-center justify-center text-blue-600 mb-6">
                        <x-icon name="o-clipboard-document-list" class="w-6 h-6" />
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-3">Gestión de Órdenes</h3>
                    <p class="text-slate-500 leading-relaxed">Control total del ciclo de vida de la reparación. Desde la recepción hasta la entrega, con estados personalizables y costos automáticos.</p>
                </div>

                <!-- Feature 2 -->
                <div class="bg-white p-8 rounded-2xl shadow-sm border border-slate-200 hover:shadow-md transition-shadow">
                    <div class="w-12 h-12 bg-green-50 rounded-xl flex items-center justify-center text-green-600 mb-6">
                        <x-icon name="o-qr-code" class="w-6 h-6" />
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-3">Rastreo por QR</h3>
                    <p class="text-slate-500 leading-relaxed">Genera etiquetas con códigos QR únicos. Tus clientes escanean y ven el estado sin llamarte. Transparencia total.</p>
                </div>

                <!-- Feature 3 -->
                <div class="bg-white p-8 rounded-2xl shadow-sm border border-slate-200 hover:shadow-md transition-shadow">
                    <div class="w-12 h-12 bg-purple-50 rounded-xl flex items-center justify-center text-purple-600 mb-6">
                        <x-icon name="o-chart-bar" class="w-6 h-6" />
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-3">Reportes Financieros</h3>
                    <p class="text-slate-500 leading-relaxed">Dashboard en tiempo real. Conoce tus ingresos diarios, qué marcas reparas más y controla tu inventario de repuestos.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- FOOTER -->
    <footer class="bg-white py-12 border-t border-slate-200">
        <div class="max-w-7xl mx-auto px-6 flex flex-col md:flex-row justify-between items-center gap-6">
            <div class="flex items-center gap-2">
                <div class="bg-slate-900 text-white p-1.5 rounded-lg">
                    <x-icon name="o-wrench-screwdriver" class="w-4 h-4" />
                </div>
                <span class="font-bold text-lg tracking-tight text-slate-900">TECHLIFE</span>
            </div>
            <div class="text-sm text-slate-500">
                &copy; {{ date('Y') }} TechLife Solutions.
            </div>
            <div class="flex gap-6 text-sm font-medium text-slate-600">
                <a href="/legal?section=terms" class="hover:text-slate-900">Términos</a>
                <a href="/legal?section=privacy" class="hover:text-slate-900">Privacidad</a>
            </div>
        </div>
    </footer>

</body>
</html>
