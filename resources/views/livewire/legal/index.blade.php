<?php

use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use Illuminate\Http\Request;

new #[Layout('layouts.guest')] class extends Component {
    
    // Variable de estado para la pestaña activa
    public string $tab = 'terms';

    // Inicialización segura
    public function mount(Request $request): void
    {
        // Si la URL es /legal?section=privacy, abrimos esa pestaña. Si no, 'terms'.
        $this->tab = $request->query('section', 'terms');
    }

    // Cambio de pestaña dinámico
    public function setTab($tab): void
    {
        $this->tab = $tab;
        // Opcional: Podríamos actualizar la URL sin recargar, pero por ahora lo mantenemos simple
    }
}; ?>

<div class="min-h-screen bg-[#F8FAFC] font-sans text-slate-900">
    
    <!-- NAV PÚBLICO (Estilo Enterprise) -->
    <nav class="bg-white border-b border-slate-200 sticky top-0 z-50 bg-opacity-90 backdrop-blur-md">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <!-- Logo -->
                <div class="flex items-center gap-3">
                    <a href="/" class="flex items-center gap-2 group no-underline">
                        <div class="bg-slate-900 text-white p-1.5 rounded-lg shadow-sm group-hover:bg-blue-600 transition-colors duration-300">
                            <x-icon name="o-wrench-screwdriver" class="w-5 h-5" />
                        </div>
                        <span class="font-bold text-lg tracking-tight text-slate-900">TECHLIFE</span>
                    </a>
                    <div class="h-6 w-px bg-slate-200 mx-2"></div>
                    <span class="text-xs font-bold text-slate-500 uppercase tracking-wide">Centro de Recursos</span>
                </div>
                
                <!-- Acciones -->
                <div class="flex items-center gap-4">
                    <a href="/" class="text-sm font-medium text-slate-500 hover:text-slate-900 transition-colors">Volver al Sitio</a>
                    <a href="{{ route('login') }}" class="px-4 py-2 text-sm font-bold text-white bg-slate-900 rounded-lg hover:bg-slate-800 transition-all shadow-sm">
                        Acceso Clientes
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- CONTENEDOR PRINCIPAL -->
    <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
        <div class="lg:grid lg:grid-cols-12 lg:gap-12">
            
            <!-- SIDEBAR DE NAVEGACIÓN -->
            <aside class="lg:col-span-3 mb-8 lg:mb-0">
                <nav class="space-y-1 sticky top-24">
                    
                    <div class="pb-4 mb-4 border-b border-slate-200">
                        <h3 class="px-3 text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Legal y Cumplimiento</h3>
                        
                        <button wire:click="setTab('terms')" 
                            class="group w-full flex items-center pl-3 pr-4 py-2 text-sm font-medium rounded-lg transition-all duration-200 {{ $tab === 'terms' ? 'bg-white text-blue-600 shadow-sm ring-1 ring-slate-200' : 'text-slate-600 hover:bg-slate-200/50 hover:text-slate-900' }}">
                            <x-icon name="o-scale" class="flex-shrink-0 -ml-1 mr-3 h-5 w-5 transition-colors {{ $tab === 'terms' ? 'text-blue-500' : 'text-slate-400 group-hover:text-slate-500' }}" />
                            <span class="truncate">Términos de Servicio</span>
                        </button>

                        <button wire:click="setTab('privacy')" 
                            class="group w-full flex items-center pl-3 pr-4 py-2 text-sm font-medium rounded-lg transition-all duration-200 {{ $tab === 'privacy' ? 'bg-white text-blue-600 shadow-sm ring-1 ring-slate-200' : 'text-slate-600 hover:bg-slate-200/50 hover:text-slate-900' }}">
                            <x-icon name="o-shield-check" class="flex-shrink-0 -ml-1 mr-3 h-5 w-5 transition-colors {{ $tab === 'privacy' ? 'text-blue-500' : 'text-slate-400 group-hover:text-slate-500' }}" />
                            <span class="truncate">Política de Privacidad</span>
                        </button>
                    </div>

                    <div>
                        <h3 class="px-3 text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Ayuda y Soporte</h3>
                        
                        <button wire:click="setTab('docs_start')" 
                            class="group w-full flex items-center pl-3 pr-4 py-2 text-sm font-medium rounded-lg transition-all duration-200 {{ $tab === 'docs_start' ? 'bg-white text-blue-600 shadow-sm ring-1 ring-slate-200' : 'text-slate-600 hover:bg-slate-200/50 hover:text-slate-900' }}">
                            <x-icon name="o-rocket-launch" class="flex-shrink-0 -ml-1 mr-3 h-5 w-5 transition-colors {{ $tab === 'docs_start' ? 'text-blue-500' : 'text-slate-400 group-hover:text-slate-500' }}" />
                            <span class="truncate">Guía de Inicio</span>
                        </button>

                        <button wire:click="setTab('docs_faq')" 
                            class="group w-full flex items-center pl-3 pr-4 py-2 text-sm font-medium rounded-lg transition-all duration-200 {{ $tab === 'docs_faq' ? 'bg-white text-blue-600 shadow-sm ring-1 ring-slate-200' : 'text-slate-600 hover:bg-slate-200/50 hover:text-slate-900' }}">
                            <x-icon name="o-question-mark-circle" class="flex-shrink-0 -ml-1 mr-3 h-5 w-5 transition-colors {{ $tab === 'docs_faq' ? 'text-blue-500' : 'text-slate-400 group-hover:text-slate-500' }}" />
                            <span class="truncate">Preguntas Frecuentes</span>
                        </button>
                    </div>

                    <!-- Caja de contacto -->
                    <div class="mt-8 pt-6 border-t border-slate-200">
                        <div class="bg-slate-900 rounded-xl p-5 text-white">
                            <h4 class="font-bold text-sm mb-2">¿Necesitas ayuda técnica?</h4>
                            <p class="text-xs text-slate-300 mb-4">Nuestro equipo de soporte está disponible 24/7 para clientes Enterprise.</p>
				<a href="mailto:{{ $settings->company_email }}" class="block w-full text-center bg-white text-slate-900 py-2 rounded-lg text-xs font-bold hover:bg-blue-50 transition">
                                Contactar Soporte
                            </a>
                        </div>
                    </div>
                </nav>
            </aside>

            <!-- CONTENIDO PRINCIPAL -->
            <main class="lg:col-span-9">
                <div class="bg-white shadow-sm ring-1 ring-slate-200 rounded-2xl p-8 lg:p-12 min-h-[600px]">
                    
                    {{-- SECCIÓN: TÉRMINOS DE SERVICIO --}}
                    @if($tab === 'terms')
                        <div class="max-w-3xl">
                            <span class="text-blue-600 font-bold tracking-wide uppercase text-xs">Legal</span>
                            <h1 class="text-3xl font-black text-slate-900 mt-2 mb-6">Términos de Servicio</h1>
                            <div class="prose prose-slate prose-headings:font-bold prose-a:text-blue-600 hover:prose-a:text-blue-500">
                                <p class="lead text-slate-600 text-lg">Al utilizar la plataforma TechLife, aceptas cumplir con los siguientes términos diseñados para asegurar la calidad y seguridad del servicio.</p>
                                <p class="text-sm text-slate-400">Última actualización: {{ date('d/m/Y') }}</p>

                                <h3 class="text-xl text-slate-800 mt-8 mb-4">1. Definiciones del Servicio</h3>
                                <p class="text-slate-600 leading-relaxed">TechLife provee una plataforma SaaS (Software as a Service) para la gestión operativa de talleres y centros de servicio. El "Suscriptor" es la entidad comercial que contrata el servicio, y los "Usuarios" son las personas autorizadas por el Suscriptor.</p>

                                <h3 class="text-xl text-slate-800 mt-8 mb-4">2. Acuerdos de Nivel de Servicio (SLA)</h3>
                                <ul class="list-disc pl-5 space-y-2 text-slate-600">
                                    <li><strong>Disponibilidad:</strong> Garantizamos un uptime del 99.9% mensual.</li>
                                    <li><strong>Soporte:</strong> El tiempo de respuesta para incidencias críticas es de menos de 4 horas.</li>
                                    <li><strong>Mantenimiento:</strong> Las ventanas de mantenimiento se notificarán con 48 horas de antelación.</li>
                                </ul>

                                <h3 class="text-xl text-slate-800 mt-8 mb-4">3. Propiedad Intelectual y Datos</h3>
                                <p class="text-slate-600 leading-relaxed">Usted conserva todos los derechos y la propiedad de los datos cargados en el sistema (Clientes, Órdenes, Inventario). TechLife se reserva el derecho de usar metadatos anonimizados para mejorar el rendimiento del sistema.</p>

                                <h3 class="text-xl text-slate-800 mt-8 mb-4">4. Pagos y Cancelación</h3>
                                <p class="text-slate-600 leading-relaxed">El servicio se factura mensualmente por adelantado. Puede cancelar su suscripción en cualquier momento desde el panel de control. No se realizan reembolsos por meses parciales no utilizados.</p>
                            </div>
                        </div>
                    @endif

                    {{-- SECCIÓN: POLÍTICA DE PRIVACIDAD --}}
                    @if($tab === 'privacy')
                        <div class="max-w-3xl">
                            <span class="text-green-600 font-bold tracking-wide uppercase text-xs">Seguridad</span>
                            <h1 class="text-3xl font-black text-slate-900 mt-2 mb-6">Política de Privacidad</h1>
                            
                            <div class="bg-green-50 border border-green-100 rounded-xl p-6 mb-8">
                                <h4 class="font-bold text-green-800 mb-2 flex items-center gap-2">
                                    <x-icon name="o-lock-closed" class="w-5 h-5" />
                                    Compromiso de Seguridad
                                </h4>
                                <p class="text-sm text-green-700">Sus datos están encriptados en reposo (AES-256) y en tránsito (TLS 1.3). No vendemos, alquilamos ni compartimos su información comercial con terceros bajo ninguna circunstancia.</p>
                            </div>

                            <div class="space-y-8 text-slate-600">
                                <div>
                                    <h3 class="font-bold text-slate-900 text-lg mb-2">1. Datos que Recopilamos</h3>
                                    <p>Recopilamos información necesaria para la prestación del servicio: Información de contacto comercial, datos de facturación y registros de actividad del sistema para auditoría de seguridad.</p>
                                </div>

                                <div>
                                    <h3 class="font-bold text-slate-900 text-lg mb-2">2. Uso de la Información</h3>
                                    <p>Utilizamos sus datos exclusivamente para:</p>
                                    <ul class="list-disc pl-5 mt-2 space-y-1">
                                        <li>Proveer y mantener el servicio SaaS.</li>
                                        <li>Procesar pagos y facturación.</li>
                                        <li>Notificar sobre cambios importantes o alertas de seguridad.</li>
                                    </ul>
                                </div>

                                <div>
                                    <h3 class="font-bold text-slate-900 text-lg mb-2">3. Retención de Datos</h3>
                                    <p>Conservamos sus datos mientras su cuenta esté activa. Al cancelar la suscripción, tiene un periodo de gracia de 30 días para exportar su información antes de la eliminación permanente.</p>
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- SECCIÓN: PRIMEROS PASOS --}}
                    @if($tab === 'docs_start')
                        <div class="max-w-3xl">
                            <span class="text-purple-600 font-bold tracking-wide uppercase text-xs">Documentación</span>
                            <h1 class="text-3xl font-black text-slate-900 mt-2 mb-8">Guía de Inicio Rápido</h1>

                            <div class="space-y-8">
                                <!-- Paso 1 -->
                                <div class="flex gap-6">
                                    <div class="flex-none">
                                        <div class="w-12 h-12 bg-slate-900 text-white rounded-2xl flex items-center justify-center font-black text-xl shadow-lg shadow-slate-900/20">1</div>
                                    </div>
                                    <div>
                                        <h3 class="text-xl font-bold text-slate-900">Configuración del Taller</h3>
                                        <p class="text-slate-600 mt-2 leading-relaxed">
                                            Lo primero es personalizar tu entorno. Ve al menú <strong>Configuración</strong> para subir tu logotipo y definir los términos de garantía que aparecerán en las órdenes impresas. Esto asegura que todos tus documentos tengan tu marca.
                                        </p>
                                    </div>
                                </div>

                                <!-- Paso 2 -->
                                <div class="flex gap-6">
                                    <div class="flex-none">
                                        <div class="w-12 h-12 bg-blue-600 text-white rounded-2xl flex items-center justify-center font-black text-xl shadow-lg shadow-blue-600/20">2</div>
                                    </div>
                                    <div>
                                        <h3 class="text-xl font-bold text-slate-900">Gestión de Usuarios</h3>
                                        <p class="text-slate-600 mt-2 leading-relaxed">
                                            Invita a tus técnicos. En la sección <strong>Equipo</strong>, crea cuentas para tu personal. Asígnales el rol de "Técnico" para restringir su acceso a la información financiera sensible.
                                        </p>
                                    </div>
                                </div>

                                <!-- Paso 3 -->
                                <div class="flex gap-6">
                                    <div class="flex-none">
                                        <div class="w-12 h-12 bg-white border-2 border-slate-200 text-slate-400 rounded-2xl flex items-center justify-center font-black text-xl">3</div>
                                    </div>
                                    <div>
                                        <h3 class="text-xl font-bold text-slate-900">Tu Primera Orden</h3>
                                        <p class="text-slate-600 mt-2 leading-relaxed">
                                            Registra la entrada de un equipo. El sistema generará automáticamente un número de orden y un código QR único para que tu cliente pueda rastrear el estado de su reparación en tiempo real.
                                        </p>
                                        <div class="mt-4 p-4 bg-slate-50 rounded-lg border border-slate-200 text-sm text-slate-500 italic">
                                            Tip Pro: Usa el botón de WhatsApp para notificar al cliente con un solo clic.
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- SECCIÓN: FAQ --}}
                    @if($tab === 'docs_faq')
                        <div class="max-w-3xl">
                            <span class="text-orange-500 font-bold tracking-wide uppercase text-xs">Soporte</span>
                            <h1 class="text-3xl font-black text-slate-900 mt-2 mb-8">Preguntas Frecuentes</h1>

                            <div class="space-y-4">
                                <!-- Pregunta 1 -->
                                <details class="group border border-slate-200 rounded-xl bg-white open:ring-2 open:ring-blue-100 open:border-blue-300 transition-all duration-200">
                                    <summary class="flex cursor-pointer items-center justify-between p-6 text-lg font-bold text-slate-900 select-none">
                                        ¿Cómo recupero la contraseña de un técnico?
                                        <span class="transition group-open:rotate-180">
                                            <x-icon name="o-chevron-down" class="w-5 h-5 text-slate-400" />
                                        </span>
                                    </summary>
                                    <div class="px-6 pb-6 text-slate-600 leading-relaxed">
                                        Como administrador, puedes ir a la sección <strong>Usuarios</strong>, hacer clic en el botón de editar (lápiz) del técnico correspondiente y escribir una nueva contraseña en el campo. Al guardar, la contraseña se actualizará inmediatamente.
                                    </div>
                                </details>

                                <!-- Pregunta 2 -->
                                <details class="group border border-slate-200 rounded-xl bg-white open:ring-2 open:ring-blue-100 open:border-blue-300 transition-all duration-200">
                                    <summary class="flex cursor-pointer items-center justify-between p-6 text-lg font-bold text-slate-900 select-none">
                                        ¿Mis datos son visibles para otros talleres?
                                        <span class="transition group-open:rotate-180">
                                            <x-icon name="o-chevron-down" class="w-5 h-5 text-slate-400" />
                                        </span>
                                    </summary>
                                    <div class="px-6 pb-6 text-slate-600 leading-relaxed">
                                        <strong>Absolutamente no.</strong> TechLife utiliza una arquitectura Multi-Tenant estricta a nivel de base de datos. Cada empresa tiene un ID único y todas las consultas están aisladas. Es técnicamente imposible que un usuario de otro taller vea tus clientes, inventario o ventas.
                                    </div>
                                </details>

                                <!-- Pregunta 3 -->
                                <details class="group border border-slate-200 rounded-xl bg-white open:ring-2 open:ring-blue-100 open:border-blue-300 transition-all duration-200">
                                    <summary class="flex cursor-pointer items-center justify-between p-6 text-lg font-bold text-slate-900 select-none">
                                        ¿Cómo funciona el Portal de Clientes?
                                        <span class="transition group-open:rotate-180">
                                            <x-icon name="o-chevron-down" class="w-5 h-5 text-slate-400" />
                                        </span>
                                    </summary>
                                    <div class="px-6 pb-6 text-slate-600 leading-relaxed">
                                        Cuando registras un cliente nuevo con su correo electrónico, el sistema le crea automáticamente una cuenta de usuario. El cliente puede iniciar sesión con ese correo (y una contraseña temporal que tú defines, por defecto 'Cliente123') para ver el historial de sus equipos, descargar facturas y ver su estado de cuenta.
                                    </div>
                                </details>

                                 <!-- Pregunta 4 -->
                                 <details class="group border border-slate-200 rounded-xl bg-white open:ring-2 open:ring-blue-100 open:border-blue-300 transition-all duration-200">
                                    <summary class="flex cursor-pointer items-center justify-between p-6 text-lg font-bold text-slate-900 select-none">
                                        ¿Puedo usar el sistema desde mi celular?
                                        <span class="transition group-open:rotate-180">
                                            <x-icon name="o-chevron-down" class="w-5 h-5 text-slate-400" />
                                        </span>
                                    </summary>
                                    <div class="px-6 pb-6 text-slate-600 leading-relaxed">
                                        Sí. TechLife es una aplicación web progresiva (PWA) totalmente responsiva. Puedes acceder desde cualquier navegador en Android o iOS y la interfaz se adaptará perfectamente a tu pantalla.
                                    </div>
                                </details>
                            </div>
                        </div>
                    @endif

                </div>
            </main>
        </div>
    </div>
    
    <!-- Footer Simple -->
    <footer class="bg-white border-t border-slate-200 mt-12">
        <div class="max-w-7xl mx-auto py-10 px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row justify-between items-center gap-4">
            <div class="text-slate-400 text-sm">
                &copy; {{ date('Y') }} TechLife Solutions Inc. Todos los derechos reservados.
            </div>
            <div class="flex gap-6">
                <a href="#" class="text-slate-400 hover:text-slate-600 transition">Términos</a>
                <a href="#" class="text-slate-400 hover:text-slate-600 transition">Privacidad</a>
                <a href="#" class="text-slate-400 hover:text-slate-600 transition">Contacto</a>
            </div>
        </div>
    </footer>
</div>
