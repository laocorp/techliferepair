<?php

namespace App\Http\Controllers;

use App\Models\RepairOrder;
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

    // --- NUEVA FUNCIÓN: Imprimir Informe Técnico ---
    public function printTechnicalReport(RepairOrder $order)
    {
        // Cargamos relaciones necesarias + el informe técnico
        $order->load(['asset.client', 'technicalReport']);
        
        // Si no han llenado el informe, damos error
        if (!$order->technicalReport) {
            abort(404, 'Aún no se ha creado un informe técnico para esta orden.');
        }

        $pdf = Pdf::loadView('pdf.technical_report', compact('order'));
        return $pdf->stream("Informe-Tecnico-{$order->id}.pdf");
    }
}
