<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="repair-pro">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ isset($title) ? $title.' - ' : '' }} {{ config('app.name', 'TechLife') }}</title>

    {{-- Fuente Inter --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
</head>
<body class="min-h-screen font-sans antialiased bg-base-200 text-base-content">

    {{-- BARRA SUPERIOR (Solo Móvil) --}}
    {{-- Fondo blanco con desenfoque para que se vea moderno --}}
    <x-nav sticky class="lg:hidden border-b border-slate-200 bg-white/90 backdrop-blur-md">
        <x-slot:brand>
            <div class="ml-2 font-black text-xl tracking-tight flex items-center gap-2 text-slate-900">
                <x-icon name="o-wrench-screwdriver" class="w-6 h-6 text-slate-900" />
                TECHLIFE
            </div>
        </x-slot:brand>
        <x-slot:actions>
            <label for="main-drawer" class="lg:hidden mr-3 btn btn-sm btn-ghost text-slate-500">
                <x-icon name="o-bars-3" class="cursor-pointer" />
            </label>
        </x-slot:actions>
    </x-nav>

    {{-- LAYOUT PRINCIPAL --}}
    <x-main full-width>
        
        {{-- SIDEBAR (Menú Lateral) --}}
        {{-- Clave: bg-white y border-slate-200 para el look limpio --}}
        <x-slot:sidebar drawer="main-drawer" collapsible class="bg-white border-r border-slate-200 w-72">

            {{-- 1. LOGO --}}
            <div class="h-20 flex items-center gap-3 px-6 border-b border-slate-100">
                <div class="w-8 h-8 bg-slate-900 rounded-lg flex items-center justify-center text-white shadow-md">
                    <x-icon name="o-wrench-screwdriver" class="w-5 h-5" />
                </div>
                <div>
                    <div class="font-bold text-lg tracking-tight text-slate-900">TECHLIFE</div>
                    <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Enterprise</div>
                </div>
            </div>

            {{-- 2. PERFIL DE USUARIO --}}
            <div class="px-4 py-6">
                <div class="flex items-center gap-3 px-2 p-2 rounded-lg border border-slate-100 bg-slate-50">
                    <x-avatar :image="url('https://robohash.org/'.auth()->user()->email)" class="!w-9 h-9 rounded-full border border-white shadow-sm" />
                    <div class="overflow-hidden">
                        <div class="text-sm font-bold text-slate-900 truncate">{{ auth()->user()->name }}</div>
                        <div class="text-[10px] text-slate-500 font-bold uppercase tracking-wide">
                            @if(auth()->user()->isClient())
                                Portal Cliente
                            @elseif(auth()->user()->isAdmin())
                                Administrador
                            @else
                                Técnico
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- 3. MENÚ PRINCIPAL --}}
            {{-- Usamos text-slate-600 para que no sea negro puro (más elegante) --}}
            <x-menu activate-by-route class="px-3 gap-1 text-sm font-medium text-slate-600">
                
                {{-- CASO A: ES UN CLIENTE --}}
                @if(auth()->user()->isClient())
                    
                    <x-menu-item title="Mi Portal" icon="o-computer-desktop" link="/my-portal" class="hover:bg-slate-100 hover:text-slate-900 rounded-md" />
                    
                    <div class="mt-4 px-2">
                        <div class="bg-blue-50 border border-blue-100 rounded-xl p-4 text-center">
                            <div class="text-xs font-bold text-blue-700 mb-1">¿Ayuda?</div>
                            <x-button label="Chat Soporte" icon="o-chat-bubble-left" class="btn-xs btn-primary w-full border-none shadow-none mt-1" external link="https://wa.me/593900000000" />
                        </div>
                    </div>

                {{-- CASO B: ES STAFF --}}
                @else

                    <x-menu-item title="Dashboard" icon="o-home" link="/dashboard" class="hover:bg-slate-100 hover:text-slate-900 rounded-md" />
                    
                    <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mt-6 mb-2 px-3">Operaciones</div>
                    
                    <x-menu-item title="Órdenes" icon="o-clipboard-document-list" link="/orders" class="hover:bg-slate-100 hover:text-slate-900 rounded-md" />
                    <x-menu-item title="Equipos" icon="o-cube" link="/assets" class="hover:bg-slate-100 hover:text-slate-900 rounded-md" />
                    
                    <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mt-6 mb-2 px-3">Gestión</div>
                    
                    <x-menu-item title="Clientes" icon="o-users" link="/clients" class="hover:bg-slate-100 hover:text-slate-900 rounded-md" />
                    <x-menu-item title="Inventario" icon="o-archive-box" link="/parts" class="hover:bg-slate-100 hover:text-slate-900 rounded-md" />
                    
                    {{-- SOLO ADMIN --}}
                    @if(auth()->user()->isAdmin())
                        <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mt-6 mb-2 px-3">Sistema</div>
                        <x-menu-item title="Usuarios" icon="o-user-group" link="/users" class="hover:bg-slate-100 hover:text-slate-900 rounded-md" />        
                        <x-menu-item title="Configuración" icon="o-cog-6-tooth" link="/settings" class="hover:bg-slate-100 hover:text-slate-900 rounded-md" />
                    @endif

                @endif

                {{-- BOTÓN SALIR --}}
                <div class="mt-auto pt-6 border-t border-slate-100">
                    <x-menu-item title="Cerrar Sesión" icon="o-arrow-left-on-rectangle" link="/logout" no-wire-navigate class="text-slate-500 hover:text-red-600 hover:bg-red-50 rounded-md" />
                </div>
                
            </x-menu>
        </x-slot:sidebar>

        {{-- CONTENIDO DE LA PÁGINA --}}
        <x-slot:content>
            <div class="max-w-7xl mx-auto">
                {{ $slot }}
            </div>
        </x-slot:content>
    </x-main>

    {{-- TOASTS --}}
    <x-toast />
</body>
</html>
