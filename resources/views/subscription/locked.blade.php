<!DOCTYPE html>
<html lang="es" data-theme="repair-pro">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Servicio Suspendido | TechLife</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-slate-50 min-h-screen flex items-center justify-center p-4">

    <div class="max-w-lg w-full bg-white rounded-2xl shadow-2xl border border-slate-200 overflow-hidden text-center">
        
        <div class="bg-red-600 p-6">
            <div class="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-8 h-8 text-white">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
                </svg>
            </div>
            <h1 class="text-2xl font-black text-white uppercase tracking-wide">Servicio Suspendido</h1>
        </div>

        <div class="p-8">
            <p class="text-slate-600 text-lg mb-6">
                Hola <strong>{{ auth()->user()->name }}</strong>,<br>
                Tu suscripción ha vencido o tu cuenta ha sido suspendida temporalmente por falta de pago.
            </p>

            <div class="bg-red-50 border border-red-100 rounded-lg p-4 mb-8 text-left">
                <div class="flex justify-between mb-1">
                    <span class="text-xs font-bold text-red-500 uppercase">Empresa</span>
                    <span class="text-xs font-bold text-slate-700">{{ auth()->user()->company->name }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-xs font-bold text-red-500 uppercase">Estado</span>
                    <span class="text-xs font-bold text-red-700">REQUIERE PAGO</span>
                </div>
            </div>

            <p class="text-sm text-slate-400 mb-6">
                Para reactivar el acceso inmediato a tus datos y operaciones, por favor actualiza tu método de pago.
            </p>

            <a href="https://wa.me/593900000000?text=Hola, quiero reactivar mi cuenta de TechLife" class="btn btn-primary w-full btn-lg shadow-xl shadow-blue-900/20">
                Contactar Soporte / Pagar
            </a>
            
            <div class="mt-4">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-sm text-slate-400 hover:text-slate-600 underline">
                        Cerrar Sesión
                    </button>
                </form>
            </div>
        </div>
    </div>

</body>
</html>
