<?php

use Illuminate\Http\Request;
use App\Models\RepairOrder;
use App\Models\Sale;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;
use App\Http\Controllers\PdfController;
use App\Http\Controllers\Auth\LogoutController;

// ==========================================
// 🌐 RUTAS PÚBLICAS (Sin Login)
// ==========================================

// Página de Inicio (Landing Page)
Route::view('/', 'welcome');

// 1. Ruta para ver el estado de la orden (QR)
Volt::route('/track/{token}', 'tracking.show')->name('track.status');

// 2. Lógica del buscador de la portada
Route::post('/track/search', function (Request $request) {
    $orderNumber = $request->input('order_number');
    
    // Intentar buscar por ID numérico
    $order = RepairOrder::find(intval($orderNumber));

    // Si no encuentra por ID, intentar buscar por Token (por si acaso pegan el token)
    if (!$order) {
        $order = RepairOrder::where('tracking_token', $orderNumber)->first();
    }

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

    // Perfil (Breeze)
    Route::view('profile', 'profile')->name('profile');

    // Módulos Principales
    Volt::route('/clients', 'clients.index')->name('clients');
    Volt::route('/clients/{client}', 'clients.show')->name('clients.show'); // Perfil de Cliente
    
    Volt::route('/assets', 'assets.index')->name('assets');
    Volt::route('/parts', 'parts.index')->name('parts');

    // Gestión de Órdenes
    Volt::route('/orders', 'repair-orders.index')->name('orders');
    Volt::route('/orders/{repairOrder}', 'repair-orders.edit')->name('orders.edit');
    Route::get('/orders/{order}/print', [PdfController::class, 'printOrder'])->name('orders.print');

    // Informes Técnicos
    Volt::route('/orders/{order}/report', 'reports.form')->name('reports.form');
    Route::get('/orders/{order}/report-pdf', [PdfController::class, 'printTechnicalReport'])->name('reports.print');

    // Tablero Kanban
    Volt::route('/kanban', 'repair-orders.kanban')->name('kanban');

    // Punto de Venta (POS) y Ventas
    Volt::route('/pos', 'pos.index')->name('pos');
    Volt::route('/sales', 'sales.index')->name('sales.index');
    Route::get('/pos/ticket/{sale}', [PdfController::class, 'printTicket'])->name('pos.print');

    // Portal de Cliente (Solo para rol 'client')
    Volt::route('/my-portal', 'client-portal.index')->name('client.portal');

    // Usuarios (Gestión de Equipo)
    Volt::route('/users', 'users.index')->name('users');

    // --- CONFIGURACIÓN (SOLO ADMIN) ---
    // Usamos Volt::route directamente con un middleware en línea para protegerlo
    Volt::route('/settings', 'settings.index')
        ->middleware(function ($request, $next) {
            if (!auth()->user()->isAdmin()) {
                abort(403, '⛔ ACCESO DENEGADO: Solo administradores.');
            }
            return $next($request);
        })
        ->name('settings');
        
    // --- GESTIÓN SAAS (SOLO SUPER ADMIN) ---
    Volt::route('/super/plans', 'super.plans')->name('super.plans');
    Volt::route('/super/companies', 'super.companies')->name('super.companies');

    // Salir
    Route::get('/logout', LogoutController::class)->name('logout');

}); 

// Rutas de Autenticación (Breeze)
require __DIR__.'/auth.php';
