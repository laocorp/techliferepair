<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="techlife-v5">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ isset($title) ? $title.' - ' : '' }} TechLife Enterprise</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen font-sans antialiased bg-base-200 text-base-content">

    {{-- BARRA SUPERIOR MÓVIL --}}
    <x-nav sticky class="lg:hidden border-b border-base-300 bg-white/80 backdrop-blur-md z-50">
        <x-slot:brand>
            <div class="flex items-center gap-2 text-slate-900">
                <div class="bg-slate-900 text-white p-1.5 rounded-lg">
                    <x-icon name="o-wrench-screwdriver" class="w-5 h-5" />
                </div>
                <span class="font-bold text-lg tracking-tight">TECHLIFE</span>
            </div>
        </x-slot:brand>
        <x-slot:actions>
            <label for="main-drawer" class="btn btn-sm btn-ghost lg:hidden">
                <x-icon name="o-bars-3" />
            </label>
        </x-slot:actions>
    </x-nav>

    {{-- LAYOUT PRINCIPAL --}}
    <x-main full-width>
        
        {{-- SIDEBAR PREMIUM --}}
        <x-slot:sidebar drawer="main-drawer" collapsible class="bg-white border-r border-slate-200 w-72 shadow-sm z-40">

            {{-- LOGO --}}
            <div class="h-24 flex flex-col justify-center px-6 mb-2">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-slate-900 text-white rounded-xl flex items-center justify-center shadow-lg shadow-slate-900/20">
                        <x-icon name="o-wrench-screwdriver" class="w-6 h-6" />
                    </div>
                    <div>
                        <div class="font-black text-xl tracking-tight text-slate-900 leading-none">TECHLIFE</div>
                        <div class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-1">Enterprise OS</div>
                    </div>
                </div>
            </div>

            {{-- PERFIL CARD --}}
            <div class="px-4 mb-8">
                <div class="p-3 bg-slate-50 border border-slate-100 rounded-xl flex items-center gap-3">
                    <x-avatar :image="url('https://robohash.org/'.auth()->user()->email)" class="!w-10 h-10 rounded-lg bg-white border border-slate-200 shadow-sm" />
                    <div class="overflow-hidden">
                        <div class="text-sm font-bold text-slate-900 truncate">{{ auth()->user()->name }}</div>
                        <div class="text-[10px] font-bold uppercase tracking-wide 
                            {{ auth()->user()->is_super_admin ? 'text-purple-600' : (auth()->user()->isAdmin() ? 'text-blue-600' : 'text-slate-500') }}">
                            {{ auth()->user()->is_super_admin ? 'Master Admin' : (auth()->user()->isAdmin() ? 'Administrador' : (auth()->user()->isClient() ? 'Cliente VIP' : 'Técnico')) }}
                        </div>
                    </div>
                </div>
            </div>

            {{-- MENÚ DE NAVEGACIÓN --}}
            <x-menu activate-by-route class="px-3 gap-1.5 text-sm font-medium text-slate-600">
                
                @if(auth()->user()->isClient())
                    <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-2 px-3">Mi Cuenta</div>
                    <x-menu-item title="Mi Portal" icon="o-computer-desktop" link="/my-portal" />
                    
                    <div class="mt-auto pt-6">
                        <div class="bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl p-4 text-white shadow-lg shadow-blue-500/30">
                            <div class="text-xs font-bold opacity-80 mb-1">Soporte Premium</div>
                            <div class="text-sm font-medium mb-3">¿Tienes dudas con tu equipo?</div>
                            <a href="https://wa.me/593900000000" target="_blank" class="flex items-center justify-center gap-2 bg-white/20 hover:bg-white/30 py-2 rounded-lg text-xs font-bold transition-colors cursor-pointer text-white no-underline">
                                <x-icon name="o-chat-bubble-left" class="w-4 h-4" /> Chat en Vivo
                            </a>
                        </div>
                    </div>
                @else
                    <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-2 px-3">Principal</div>
                    <x-menu-item title="Dashboard" icon="o-home" link="/dashboard" />
                    
                    <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mt-6 mb-2 px-3">Operativa</div>
                    <x-menu-item title="Órdenes" icon="o-clipboard-document-list" link="/orders" />
                    <x-menu-item title="Tablero Visual" icon="o-rectangle-group" link="/kanban" />
                    <x-menu-item title="Punto de Venta" icon="o-shopping-cart" link="/pos" />
		    <x-menu-item title="Caja / Turnos" icon="o-calculator" link="/cash" class="hover:bg-slate-100 hover:text-slate-900 rounded-md" />

                    <!-- NUEVO BOTÓN -->
                    <x-menu-item title="Historial Ventas" icon="o-banknotes" link="/sales" />
                    
                    <x-menu-item title="Equipos" icon="o-cube" link="/assets" />
                    
                    <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mt-6 mb-2 px-3">Administración</div>
                    <x-menu-item title="Clientes" icon="o-users" link="/clients" />
                    <x-menu-item title="Inventario" icon="o-archive-box" link="/parts" />
                    
                    @if(auth()->user()->isAdmin())
                        <x-menu-item title="Equipo" icon="o-user-group" link="/users" />
                        <x-menu-item title="Configuración" icon="o-cog-6-tooth" link="/settings" />
                    @endif

                    @if(auth()->user()->is_super_admin)
                        <div class="mt-8 mb-2 px-3 border-t border-slate-100 pt-4">
                            <div class="text-[10px] font-black text-purple-600 uppercase tracking-wider flex items-center gap-1">
                                <x-icon name="o-sparkles" class="w-3 h-3" /> SaaS Master
                            </div>
                        </div>
                        <x-menu-item title="Planes" icon="o-currency-dollar" link="/super/plans" class="text-purple-700 hover:bg-purple-50" />
                        <x-menu-item title="Empresas" icon="o-building-office-2" link="/super/companies" class="text-purple-700 hover:bg-purple-50" />
                    @endif
                @endif

                {{-- SALIR --}}
                <div class="mt-12 border-t border-slate-100 pt-4 pb-6">
                    <x-menu-item title="Cerrar Sesión" icon="o-arrow-left-on-rectangle" link="/logout" no-wire-navigate class="text-slate-400 hover:text-red-600 hover:bg-red-50 transition-colors" />
                </div>
            </x-menu>
        </x-slot:sidebar>

        {{-- CONTENIDO --}}
        <x-slot:content>
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                {{ $slot }}
            </div>
        </x-slot:content>
    </x-main>

    <x-toast />
</body>
</html>
