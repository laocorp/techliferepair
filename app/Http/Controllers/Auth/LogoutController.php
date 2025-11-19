<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LogoutController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        // 1. Cerrar la sesión del usuario
        Auth::guard('web')->logout();

        // 2. Invalidar la sesión (Seguridad)
        $request->session()->invalidate();

        // 3. Regenerar el token CSRF (Seguridad)
        $request->session()->regenerateToken();

        // 4. Redirigir al Login (o a la página de inicio '/')
        return redirect('/login'); 
    }
}
