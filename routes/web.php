<?php

use Illuminate\Http\Request;
use App\Models\RepairOrder;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;
use App\Http\Controllers\PdfController;
use App\Http\Controllers\Auth\LogoutController;

// ==========================================
// 🌐 RUTAS PÚBLICAS (Sin Login)
// ==========================================

Route::view('/', 'welcome');

// 1. Ruta para ver el estado de la orden (QR)
Volt::route('/track/{token}', 'tracking.show')->name('track.status');

// 2. Lógica del buscador de la portada
Route::post('/track/search', function (Request $request) {
    $id = intval($request->input('order_number'));
    $order = RepairOrder::find($id);

    if ($order && $order->tracking_token) {
        return redirect()->route('track.status', $order->tracking_token);
    }

    return back()->with('error', 'Orden no encontrada. Verifique el número.');
});


// ==========================================
// 🔒 RUTAS PROTEGIDAS (Requieren Login)
// ==========================================

Route::middleware(['auth', 'verified'])->group(function () {
    
    // Dashboard
    Volt::route('/dashboard', 'dashboard.index')->name('dashboard');

    // Perfil
    Route::view('profile', 'profile')->name('profile');

    // Módulos Principales
    Volt::route('/clients', 'clients.index')->name('clients');
    Volt::route('/assets', 'assets.index')->name('assets');
    Volt::route('/parts', 'parts.index')->name('parts');

    // Gestión de Órdenes
    Volt::route('/orders', 'repair-orders.index')->name('orders');
    Volt::route('/orders/{repairOrder}', 'repair-orders.edit')->name('orders.edit');
    Route::get('/orders/{order}/print', [PdfController::class, 'printOrder'])->name('orders.print');
   // Ruta para editar el informe
Volt::route('/orders/{order}/report', 'reports.form')->name('reports.form');

// Ruta para imprimir el informe (PDF)
Route::get('/orders/{order}/report-pdf', [PdfController::class, 'printTechnicalReport'])->name('reports.print');
    // --- CONFIGURACIÓN (SOLO ADMIN) ---
    // Le quitamos el middleware complejo. La seguridad la pondremos en el archivo del componente.
    Volt::route('/settings', 'settings.index')->name('settings');
    Volt::route('/users', 'users.index')->name('users');

    // Salir
    Route::get('/logout', LogoutController::class)->name('logout');

	Volt::route('/dashboard', 'dashboard.index')->name('dashboard');

    // Ruta del Portal Cliente
    Volt::route('/my-portal', 'client-portal.index')->name('client.portal');
Volt::route('/clients/{client}', 'clients.show')->name('clients.show');

});

require __DIR__.'/auth.php';
