<?php

namespace App\Http\Controllers;

use App\Models\RepairOrder;
use App\Models\Sale; // <--- Asegúrate de importar Sale
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Part;

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

public function printLabel(RepairOrder $order)
    {
        $pdf = Pdf::loadView('pdf.label', compact('order'));
        // Tamaño etiqueta estándar (50mm x 25mm)
        $pdf->setPaper([0, 0, 141.73, 70.86], 'landscape'); 
        return $pdf->stream("Label-{$order->ticket_number}.pdf");
    }

    // --- NUEVA FUNCIÓN: Imprimir Ticket de Venta POS ---
    public function printTicket(Sale $sale)
    {
        // CARGA DE RELACIONES: Es vital cargar 'client' aquí
        $sale->load(['items.part', 'client', 'user']);
        
        $settings = $this->getSettings();

        // Pasamos el objeto $sale completo a la vista
        $pdf = Pdf::loadView('pdf.ticket', compact('sale', 'settings'));
        
        // Tamaño ticket térmico (80mm ancho, largo dinámico estimado)
        // 226pt es aprox 80mm. Ajusta el largo (800) si tus tickets son muy largos
        $pdf->setPaper([0, 0, 226.77, 800], 'portrait'); 
        
        return $pdf->stream("Ticket-Venta-{$sale->id}.pdf");
    }

	// NUEVO: Etiqueta de Producto
    public function printProductLabel(Part $part)
    {
        $pdf = Pdf::loadView('pdf.product_label', compact('part'));
        // Tamaño etiqueta 50mm x 25mm
        $pdf->setPaper([0, 0, 141.73, 70.86], 'landscape'); 
        return $pdf->stream("Label-{$part->sku}.pdf");
    }

}
