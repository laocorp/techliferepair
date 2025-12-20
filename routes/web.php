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

// Legal y Documentación
Volt::route('/legal', 'legal.index')->name('legal');

// Pantalla de Bloqueo por Suscripción
Route::view('/subscription-locked', 'subscription.locked')->name('subscription.locked');

// Rastreo por QR (Enlace directo)
Volt::route('/track/{token}', 'tracking.show')->name('track.status');

// Lógica del Buscador de Orden (Landing Page)
Route::post('/track/search', function (Request $request) {
    $search = $request->input('order_number');
    
    // 1. Buscar por el Nuevo Código (Ticket Number) - Ej: T-0001
    $order = RepairOrder::where('ticket_number', $search)->first();

    // 2. Si no encuentra, buscar por ID numérico (Legacy)
    if (!$order && is_numeric($search)) {
        $order = RepairOrder::find(intval($search));
    }

    // 3. Si no encuentra, buscar por Token (QR)
    if (!$order) {
        $order = RepairOrder::where('tracking_token', $search)->first();
    }

    // Si encontramos la orden, redirigir al tracking
    if ($order && $order->tracking_token) {
        return redirect()->route('track.status', $order->tracking_token);
    }

    return back()->with('error', 'Orden no encontrada. Verifique el código (Ej: T-0001).');
});

// ==========================================
// 🔒 RUTAS PROTEGIDAS (Requieren Login)
// ==========================================

Route::middleware(['auth', 'verified'])->group(function () {
    
    // Dashboard Principal
    Volt::route('/dashboard', 'dashboard.index')->name('dashboard');

    // Perfil de Usuario
    Route::view('profile', 'profile')->name('profile');

    // --- MÓDULOS OPERATIVOS ---
    
    // Clientes
    Volt::route('/clients', 'clients.index')->name('clients');
    Volt::route('/clients/{client}', 'clients.show')->name('clients.show'); // Perfil 360
    // Ruta de Deudores
    Volt::route('/debtors', 'debtors.index')->name('debtors.index');
    // Equipos y Repuestos
    Volt::route('/assets', 'assets.index')->name('assets');
    Volt::route('/parts', 'parts.index')->name('parts');
    Route::get('/parts/{part}/label', [PdfController::class, 'printProductLabel'])->name('parts.label');

    // Gestión de Órdenes
    Volt::route('/orders', 'repair-orders.index')->name('orders');
    Volt::route('/orders/{repairOrder}', 'repair-orders.edit')->name('orders.edit'); // Mesa de trabajo
    Route::get('/orders/{order}/print', [PdfController::class, 'printOrder'])->name('orders.print');

    // Informes Técnicos
    Volt::route('/orders/{order}/report', 'reports.form')->name('reports.form');
    Route::get('/orders/{order}/report-pdf', [PdfController::class, 'printTechnicalReport'])->name('reports.print');
    Route::get('/orders/{order}/label', [PdfController::class, 'printLabel'])->name('orders.label');
    // Tablero Kanban
    Volt::route('/kanban', 'repair-orders.kanban')->name('kanban');

    // Punto de Venta (POS) y Ventas
    Volt::route('/pos', 'pos.index')->name('pos');
    Volt::route('/cash', 'cash.index')->name('cash.index');
    Volt::route('/sales', 'sales.index')->name('sales.index'); // Historial de Ventas
    Route::get('/pos/ticket/{sale}', [PdfController::class, 'printTicket'])->name('pos.print');

    // Portal de Cliente (Solo para rol 'client')
    Volt::route('/my-portal', 'client-portal.index')->name('client.portal');

    // --- ADMINISTRACIÓN ---
    Volt::route('/users', 'users.index')->name('users');

    // Configuración (Protegida internamente en el componente)
    Volt::route('/settings', 'settings.index')->name('settings');
        
    // --- SUPER ADMIN SAAS ---
    Volt::route('/super/plans', 'super.plans')->name('super.plans');
    Volt::route('/super/companies', 'super.companies')->name('super.companies');

    // Cerrar Sesión (Soporta GET y POST para evitar errores)
    Route::any('/logout', LogoutController::class)->name('logout');

}); 

// Rutas de Autenticación (Breeze)
require __DIR__.'/auth.php';
