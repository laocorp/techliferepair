<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="repair-pro">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ isset($title) ? $title.' - ' : '' }} {{ config('app.name', 'TechLife') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen font-sans antialiased bg-base-100 text-base-content">

    {{-- BARRA SUPERIOR (Solo Móvil) --}}
    <x-nav sticky class="lg:hidden border-b border-base-300 bg-base-200/80 backdrop-blur-md">
        <x-slot:brand>
            <div class="ml-2 font-black text-xl tracking-tight flex items-center gap-2">
                <x-icon name="o-wrench-screwdriver" class="w-6 h-6 text-primary" />
                TECHLIFE
            </div>
        </x-slot:brand>
        <x-slot:actions>
            <label for="main-drawer" class="lg:hidden mr-3">
                <x-icon name="o-bars-3" class="cursor-pointer" />
            </label>
        </x-slot:actions>
    </x-nav>

    {{-- LAYOUT PRINCIPAL --}}
    <x-main full-width>
        
        {{-- SIDEBAR (Menú Lateral) --}}
        <x-slot:sidebar drawer="main-drawer" collapsible class="bg-base-200 lg:bg-base-200 border-r border-base-300 w-72">

            {{-- LOGO --}}
            <div class="p-6 flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-primary flex items-center justify-center shadow-lg shadow-primary/30">
                    <x-icon name="o-wrench-screwdriver" class="text-white w-6 h-6" />
                </div>
                <div>
                    <div class="font-black text-xl tracking-tight text-white">TECHLIFE</div>
                    <div class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Service OS</div>
                </div>
            </div>

            {{-- PERFIL --}}
            <div class="px-4 mb-6">
                <div class="bg-base-100 p-3 rounded-xl border border-base-300 flex items-center gap-3 shadow-sm">
                    <x-avatar :image="url('https://robohash.org/'.auth()->user()->email)" class="!w-10 h-10 rounded-lg bg-base-200" />
                    <div class="overflow-hidden">
                        <div class="text-sm font-bold truncate text-white">{{ auth()->user()->name }}</div>
                        <div class="text-xs text-primary font-bold uppercase tracking-wide">
                            {{ auth()->user()->isAdmin() ? 'Administrador' : 'Técnico' }}
                        </div>
                    </div>
                </div>
            </div>

            {{-- MENÚ --}}
            <x-menu activate-by-route class="px-3 gap-1 text-sm font-medium">
                <x-menu-item title="Dashboard" icon="o-home" link="/dashboard" class="rounded-lg" />
                
                <x-menu-sub title="Operaciones" icon="o-cpu-chip">
                    <x-menu-item title="Órdenes de Trabajo" icon="o-clipboard-document-list" link="/orders" />
                    <x-menu-item title="Equipos" icon="o-cube" link="/assets" />
                </x-menu-sub>
                
                <x-menu-item title="Clientes" icon="o-users" link="/clients" class="rounded-lg" />
                <x-menu-item title="Inventario" icon="o-archive-box" link="/parts" class="rounded-lg" />
                
                {{-- SECCIÓN ADMIN (Protegida) --}}
                @if(auth()->user()->isAdmin())
                    <x-menu-separator title="Administración" class="mt-4 mb-2 text-xs font-bold text-gray-500 uppercase tracking-wider pl-3" />
	            <x-menu-item title="Equipo / Usuarios" icon="o-user-group" link="/users" class="rounded-lg" />       
 		    <x-menu-item title="Configuración" icon="o-cog-6-tooth" link="/settings" class="rounded-lg" />
                @endif
                
                <div class="mt-6"></div>
                <x-menu-item title="Cerrar Sesión" icon="o-power" link="/logout" no-wire-navigate class="text-error hover:bg-error/10 rounded-lg" />
            </x-menu>
        </x-slot:sidebar>

        {{-- CONTENIDO DE LA PÁGINA --}}
        <x-slot:content>
            {{ $slot }}
        </x-slot:content>
    </x-main>

    {{-- TOASTS (Notificaciones) --}}
    <x-toast />
</body>
</html>
