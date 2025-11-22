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

    {{-- Chart.js y SortableJS --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen font-sans antialiased bg-base-200 text-base-content">

    {{-- BARRA SUPERIOR (Móvil) --}}
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
        
        {{-- SIDEBAR (Blanco) --}}
        <x-slot:sidebar drawer="main-drawer" collapsible class="bg-white border-r border-slate-200 w-72">

            {{-- LOGO --}}
            <div class="h-20 flex items-center gap-3 px-6 border-b border-slate-100">
                <div class="w-8 h-8 bg-slate-900 rounded-lg flex items-center justify-center text-white shadow-md">
                    <x-icon name="o-wrench-screwdriver" class="w-5 h-5" />
                </div>
                <div>
                    <div class="font-bold text-lg tracking-tight text-slate-900">TECHLIFE</div>
                    <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Enterprise v2.5</div>
                </div>
            </div>

            {{-- PERFIL --}}
            <div class="px-4 py-6">
                <div class="flex items-center gap-3 px-3 py-2 rounded-lg border border-slate-100 bg-slate-50/50 hover:bg-slate-50 transition-colors">
                    <x-avatar :image="url('https://robohash.org/'.auth()->user()->email)" class="!w-10 h-10 rounded-full border-2 border-white shadow-sm" />
                    <div class="overflow-hidden">
                        <div class="text-sm font-bold text-slate-900 truncate">{{ auth()->user()->name }}</div>
                        <div class="text-[10px] text-blue-600 font-bold uppercase tracking-wide">
                            @if(auth()->user()->isClient()) Portal Cliente
                            @elseif(auth()->user()->isAdmin()) Administrador
                            @else Técnico @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- MENÚ --}}
            <x-menu activate-by-route class="px-3 gap-1 text-sm font-medium text-slate-600">
                
                @if(auth()->user()->isClient())
                    <x-menu-item title="Mi Portal" icon="o-computer-desktop" link="/my-portal" class="hover:bg-slate-50 hover:text-slate-900 rounded-md" />
                    <div class="mt-4 px-2">
                        <div class="bg-blue-50 border border-blue-100 rounded-xl p-4 text-center">
                            <div class="text-xs font-bold text-blue-700 mb-1">¿Ayuda?</div>
                            <x-button label="Chat WhatsApp" icon="o-chat-bubble-left" class="btn-xs btn-primary w-full border-none shadow-none mt-1" external link="https://wa.me/593900000000" />
                        </div>
                    </div>
                @else
                    <x-menu-item title="Dashboard" icon="o-home" link="/dashboard" class="hover:bg-slate-50 hover:text-slate-900 rounded-md" />
                    
                    <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mt-6 mb-2 px-3">Operaciones</div>
                    <x-menu-item title="Órdenes" icon="o-clipboard-document-list" link="/orders" class="hover:bg-slate-50 hover:text-slate-900 rounded-md" />
                    <x-menu-item title="Tablero Visual" icon="o-rectangle-group" link="/kanban" class="hover:bg-slate-50 hover:text-slate-900 rounded-md" />
                    <x-menu-item title="Punto de Venta" icon="o-shopping-cart" link="/pos" class="hover:bg-slate-50 hover:text-slate-900 rounded-md" />
                    <x-menu-item title="Historial Ventas" icon="o-receipt-percent" link="/sales" class="hover:bg-slate-100 hover:text-slate-900 rounded-md" />
                    <x-menu-item title="Equipos" icon="o-cube" link="/assets" class="hover:bg-slate-50 hover:text-slate-900 rounded-md" />
                    
                    <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mt-6 mb-2 px-3">Gestión</div>
                    <x-menu-item title="Clientes" icon="o-users" link="/clients" class="hover:bg-slate-50 hover:text-slate-900 rounded-md" />
                    <x-menu-item title="Inventario" icon="o-archive-box" link="/parts" class="hover:bg-slate-50 hover:text-slate-900 rounded-md" />
                    
                    @if(auth()->user()->isAdmin())
                        <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mt-6 mb-2 px-3">Sistema</div>
                        <x-menu-item title="Usuarios" icon="o-user-group" link="/users" class="hover:bg-slate-50 hover:text-slate-900 rounded-md" />        
                        <x-menu-item title="Configuración" icon="o-cog-6-tooth" link="/settings" class="hover:bg-slate-50 hover:text-slate-900 rounded-md" />
                    @endif

                    @if(auth()->user()->is_super_admin)
                         <div class="mt-6 mb-2 px-3 border-t border-slate-100 pt-4">
                            <div class="text-[10px] font-black text-purple-600 uppercase tracking-wider flex items-center gap-1">
                                <x-icon name="o-sparkles" class="w-3 h-3" /> MASTER SAAS
                            </div>
                        </div>
                        <x-menu-item title="Planes" icon="o-currency-dollar" link="/super/plans" class="text-purple-700 hover:bg-purple-50 rounded-md font-bold" />
                        <x-menu-item title="Empresas" icon="o-building-office-2" link="/super/companies" class="text-purple-700 hover:bg-purple-50 rounded-md font-bold" />
                    @endif
                @endif

                <div class="mt-auto pt-6 border-t border-slate-100 pb-6">
                    <x-menu-item title="Cerrar Sesión" icon="o-arrow-left-on-rectangle" link="/logout" no-wire-navigate class="text-slate-500 hover:text-red-600 hover:bg-red-50 rounded-md" />
                </div>
                
            </x-menu>
        </x-slot:sidebar>

        {{-- CONTENIDO --}}
        <x-slot:content>
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
                {{ $slot }}
            </div>
        </x-slot:content>
    </x-main>

    <x-toast />
</body>
</html>
