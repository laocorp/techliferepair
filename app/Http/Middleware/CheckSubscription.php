<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Carbon\Carbon;
// Asegúrate de importar el modelo Company si lo necesitas explícitamente,
// aunque accederemos a él a través de la relación del usuario.

class CheckSubscription
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // 1. EVITAR BUCLE INFINITO: Si ya estamos en la página de bloqueo o saliendo, no hacer nada
        // Es CRÍTICO excluir estas rutas para que no se redirija a sí mismo eternamente.
        if ($request->routeIs('subscription.locked') || $request->routeIs('logout')) {
            return $next($request);
        }

        // Solo verificamos si el usuario está logueado y TIENE empresa asignada
        // (Los Super Admin o usuarios sin empresa podrían tener otra lógica o saltarse esto)
        if ($user && $user->company) {
            
            // A. Caso: Empresa SUSPENDIDA manualmente
            if ($user->company->status === 'suspended') {
                return redirect()->route('subscription.locked');
            }

            // B. Caso: Fecha de Vencimiento Pasada (Suscripción caducada)
            // Verificamos si existe fecha de validez y si hoy es mayor a esa fecha
            if ($user->company->valid_until && Carbon::now()->gt($user->company->valid_until)) {
                return redirect()->route('subscription.locked');
            }

            // C. Caso: Alerta de "Próximo a Vencer" (Menos de 5 días)
            // Esto es opcional pero muy útil para avisar antes de bloquear.
            // Usamos la sesión para no mostrar la alerta en cada recarga, solo una vez por sesión.
            if ($user->company->valid_until) {
                $daysLeft = Carbon::now()->diffInDays($user->company->valid_until, false);
                
                // Si faltan entre 0 y 5 días y no se ha mostrado la alerta...
                if ($daysLeft >= 0 && $daysLeft <= 5 && !$request->session()->has('subscription_alert_shown')) {
                    // Enviamos un mensaje flash a la sesión (que tu layout debe mostrar)
                    session()->flash('warning', "Atención: Tu suscripción vence en {$daysLeft} días. ¡Renueva pronto para evitar la suspensión del servicio!");
                    // Marcamos que ya se mostró
                    $request->session()->put('subscription_alert_shown', true);
                }
            }
        }

        return $next($request);
    }
}
