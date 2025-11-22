<?php

namespace App\Http\Controllers;

use App\Models\RepairOrder;
use App\Models\Sale; // <--- Asegúrate de importar Sale
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class PdfController extends Controller
{
    // Imprimir la Orden de Trabajo (Recibo)
    public function printOrder(RepairOrder $order)
    {
        $order->load(['asset.client', 'parts']);
        $pdf = Pdf::loadView('pdf.order', compact('order'));
        return $pdf->stream("Orden-{$order->id}.pdf");
    }

    // Imprimir Informe Técnico
    public function printTechnicalReport(RepairOrder $order)
    {
        $order->load(['asset.client', 'technicalReport']);
        
        if (!$order->technicalReport) {
            abort(404, 'Aún no se ha creado un informe técnico para esta orden.');
        }

        $pdf = Pdf::loadView('pdf.technical_report', compact('order'));
        return $pdf->stream("Informe-Tecnico-{$order->id}.pdf");
    }

    // --- NUEVA FUNCIÓN: Imprimir Ticket de Venta POS ---
    public function printTicket(Sale $sale)
    {
        // Cargamos los items y la relación con la parte/repuesto
        $sale->load('items.part');
        
        // Preparamos los datos para la vista del ticket
        $items = $sale->items->map(function($item) {
            return [
                'name' => $item->part->name,
                'quantity' => $item->quantity,
                'price' => $item->price
            ];
        });
        
        $total = $sale->total;

        // Usamos la vista pdf.ticket (que ya creamos antes)
        $pdf = Pdf::loadView('pdf.ticket', compact('items', 'total'));
        
        // Tamaño personalizado para ticket térmico (80mm x variable)
        $pdf->setPaper([0, 0, 226.77, 600], 'portrait'); 
        
        return $pdf->stream("Ticket-Venta-{$sale->id}.pdf");
    }
}
