<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Orden #{{ str_pad($order->id, 4, '0', STR_PAD_LEFT) }}</title>
    <style>
        @page { margin: 0; }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            color: #334155; /* Slate 700 */
            font-size: 12px;
            line-height: 1.5;
            margin-top: 3.8cm; /* Espacio para header */
            margin-bottom: 2.5cm; /* Espacio para footer */
            margin-left: 1.5cm;
            margin-right: 1.5cm;
            background-color: #fff;
        }

        /* --- HEADER FIJO --- */
        header {
            position: fixed; top: 0; left: 0; right: 0; height: 3cm;
            background-color: #ffffff;
            border-bottom: 2px solid #0f172a; /* Línea fuerte corporativa */
            padding: 0.8cm 1.5cm;
        }
        
        .logo-text { font-size: 22px; font-weight: 900; color: #0f172a; text-transform: uppercase; letter-spacing: -0.5px; }
        .company-details { font-size: 9px; color: #64748b; margin-top: 4px; line-height: 1.4; }

        .doc-label { font-size: 9px; font-weight: bold; color: #94a3b8; text-transform: uppercase; letter-spacing: 1px; text-align: right; }
        .doc-id { font-size: 26px; font-weight: 900; color: #0f172a; text-align: right; letter-spacing: -1px; margin-top: 2px; }
        .doc-date { font-size: 10px; color: #64748b; text-align: right; margin-top: 4px; font-weight: 500; }

        /* --- FOOTER FIJO --- */
        footer {
            position: fixed; bottom: 0; left: 0; right: 0; height: 1.8cm;
            background-color: #f8fafc; border-top: 1px solid #e2e8f0;
            padding: 15px 1.5cm;
            text-align: center;
        }
        .footer-text { font-size: 8px; color: #94a3b8; line-height: 1.4; }
        .warranty-badge {
            display: inline-block; background: #e0f2fe; color: #0369a1; 
            font-size: 8px; font-weight: bold; padding: 2px 6px; border-radius: 4px; margin-bottom: 5px;
        }

        /* --- ESTRUCTURA --- */
        table { width: 100%; border-collapse: collapse; }
        .section-spacing { margin-top: 30px; }

        /* --- CLIENTE & EQUIPO --- */
        .box-container { border: 1px solid #e2e8f0; border-radius: 6px; overflow: hidden; }
        .box-header { background-color: #f1f5f9; padding: 6px 12px; border-bottom: 1px solid #e2e8f0; }
        .box-title { font-size: 9px; font-weight: bold; text-transform: uppercase; color: #475569; letter-spacing: 0.5px; }
        .box-body { padding: 12px; background-color: #fff; }
        
        .data-label { font-size: 8px; color: #94a3b8; text-transform: uppercase; font-weight: bold; margin-bottom: 1px; display: block; }
        .data-value { font-size: 12px; color: #0f172a; font-weight: 600; margin-bottom: 8px; display: block; }
        .last-value { margin-bottom: 0; }

        /* --- REPORTE --- */
        .report-box { background-color: #f8fafc; border-left: 3px solid #3b82f6; padding: 15px; border-radius: 0 4px 4px 0; }
        .report-text { font-style: italic; color: #334155; }

        /* --- TABLA DE DETALLES --- */
        .items-header th {
            text-align: left; padding: 8px 10px; background-color: #0f172a; color: #fff;
            font-size: 9px; text-transform: uppercase; font-weight: bold; letter-spacing: 0.5px;
        }
        .item-row td { padding: 10px; border-bottom: 1px solid #f1f5f9; font-size: 11px; }
        .item-name { font-weight: bold; color: #1e293b; }
        .item-sku { font-size: 9px; color: #94a3b8; font-family: monospace; }
        .amount-col { text-align: right; font-family: monospace; font-size: 12px; }

        /* --- TOTALES --- */
        .total-box { background-color: #0f172a; color: white; padding: 15px; border-radius: 6px; text-align: right; }
        .total-label { font-size: 10px; text-transform: uppercase; opacity: 0.8; letter-spacing: 1px; }
        .total-amount { font-size: 28px; font-weight: 900; margin-top: 5px; letter-spacing: -0.5px; }
        
        /* --- QR & ESTADO --- */
        .qr-box { text-align: right; margin-top: 10px; }
        .status-pill {
            background-color: #f1f5f9; color: #475569; padding: 5px 10px; 
            border-radius: 20px; font-size: 10px; font-weight: bold; text-transform: uppercase; border: 1px solid #e2e8f0;
        }
    </style>
</head>
<body>

    <header>
        <table style="width: 100%;">
            <tr>
                <td style="vertical-align: top; width: 60%;">
                    <!-- Datos de la Empresa (Dinámicos) -->
                    <div class="logo-text">{{ $settings->company_name ?? 'TECHLIFE' }}</div>
                    <div class="company-details">
                        {{ $settings->company_address }} <br>
                        {{ $settings->company_phone }} • {{ $settings->company_email }} <br>
                        <strong>RUC/ID: {{ $settings->tax_id }}</strong>
                    </div>
                </td>
                <td style="vertical-align: top; text-align: right; width: 40%;">
                    <div class="doc-label">Orden de Trabajo</div>
                    <div class="doc-id">#{{ $order->ticket_number ?? str_pad($order->id, 5, '0', STR_PAD_LEFT) }}</div>
                    <div class="doc-date">{{ $order->created_at->format('d F, Y') }}</div>
                    
                    <!-- QR -->
                    @if($order->tracking_token)
                        <div class="qr-box">
                             <img src="data:image/svg+xml;base64, {{ base64_encode(\SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')->size(70)->margin(0)->color(15, 23, 42)->generate(route('track.status', $order->tracking_token))) }}" width="60">
                        </div>
                    @endif
                </td>
            </tr>
        </table>
    </header>

    <footer>
        @if($settings->warranty_text)
            <div class="warranty-badge">GARANTÍA</div>
            <div class="footer-text">{{ $settings->warranty_text }}</div>
        @endif
        <div class="footer-text" style="margin-top: 5px; opacity: 0.6;">
            Documento generado electrónicamente por sistema {{ config('app.name') }}.
        </div>
    </footer>

    <!-- 1. DATOS DEL CLIENTE Y EQUIPO -->
    <table style="width: 100%; border-spacing: 15px 0; margin-left: -15px; margin-right: -15px;">
        <tr>
            <td width="50%" style="vertical-align: top;">
                <div class="box-container">
                    <div class="box-header">
                        <div class="box-title">Cliente / Propietario</div>
                    </div>
                    <div class="box-body">
                        <span class="data-label">Nombre Completo</span>
                        <span class="data-value">{{ $order->asset->client->name }}</span>
                        
                        <table width="100%">
                            <tr>
                                <td>
                                    <span class="data-label">ID / RUC</span>
                                    <span class="data-value last-value">{{ $order->asset->client->tax_id ?? '---' }}</span>
                                </td>
                                <td>
                                    <span class="data-label">Teléfono</span>
                                    <span class="data-value last-value">{{ $order->asset->client->phone ?? '---' }}</span>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </td>
            <td width="50%" style="vertical-align: top;">
                <div class="box-container">
                    <div class="box-header">
                        <div class="box-title">Activo / Equipo</div>
                    </div>
                    <div class="box-body">
                        <span class="data-label">Equipo</span>
                        <span class="data-value">{{ $order->asset->brand }} {{ $order->asset->model }}</span>
                        
                        <table width="100%">
                            <tr>
                                <td>
                                    <span class="data-label">Serial / Placa</span>
                                    <span class="data-value last-value" style="font-family: monospace;">{{ $order->asset->serial_number }}</span>
                                </td>
                                <td>
                                    <span class="data-label">Tipo</span>
                                    <span class="data-value last-value">{{ $order->asset->type ?? 'General' }}</span>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </td>
        </tr>
    </table>

    <!-- 2. REPORTE INICIAL -->
    <div class="section-spacing">
        <div class="data-label" style="margin-bottom: 5px;">REPORTE INICIAL DE FALLA</div>
        <div class="report-box">
            <span class="report-text">"{{ $order->problem_description }}"</span>
        </div>
    </div>

    <!-- 3. DETALLE DE COSTOS -->
    <div class="section-spacing">
        <table class="items-table" style="width: 100%;">
            <tr class="items-header">
                <th style="border-radius: 4px 0 0 4px;">Descripción</th>
                <th style="text-align: center; width: 10%;">Cant.</th>
                <th style="text-align: right; width: 15%;">Precio Unit.</th>
                <th style="text-align: right; width: 20%; border-radius: 0 4px 4px 0;">Total</th>
            </tr>
            
            @if($order->parts->count() > 0)
                @foreach($order->parts as $part)
                <tr class="item-row">
                    <td>
                        <div class="item-name">{{ $part->name }}</div>
                        <div class="item-sku">{{ $part->sku }}</div>
                    </td>
                    <td style="text-align: center;">{{ $part->pivot->quantity }}</td>
                    <td class="amount-col">${{ number_format($part->pivot->price, 2) }}</td>
                    <td class="amount-col">${{ number_format($part->pivot->price * $part->pivot->quantity, 2) }}</td>
                </tr>
                @endforeach
            @else
                <tr class="item-row">
                    <td colspan="4" style="text-align: center; color: #94a3b8; padding: 20px;">
                        Sin repuestos o servicios adicionales cargados.
                    </td>
                </tr>
            @endif
        </table>
    </div>

    <!-- 4. TOTALES Y ESTADO -->
    <table style="width: 100%; margin-top: 20px;">
        <tr>
            <td width="60%" style="vertical-align: bottom;">
                <div class="data-label" style="margin-bottom: 5px;">Estado Actual</div>
                
                @php
                    $statusLabel = match($order->status) {
                        'recibido' => 'Recibido / Ingresado',
                        'diagnostico' => 'En Diagnóstico',
                        'espera_repuestos' => 'Esperando Repuestos',
                        'listo' => 'Listo para Retiro',
                        'entregado' => 'Entregado al Cliente',
                        default => $order->status
                    };
                @endphp
                
                <span class="status-pill">{{ strtoupper($statusLabel) }}</span>

                @if($order->is_warranty)
                    <span style="color: #d97706; font-weight: bold; font-size: 10px; margin-left: 10px;">★ CUBIERTO POR GARANTÍA</span>
                @endif
                
                <!-- FIRMAS -->
                <table style="width: 90%; margin-top: 50px;">
                    <tr>
                        <td width="45%" style="border-top: 1px solid #cbd5e1; padding-top: 5px;">
                            <div style="font-size: 8px; font-weight: bold; color: #94a3b8; text-align: center; text-transform: uppercase;">Recibí Conforme (Cliente)</div>
                        </td>
                        <td width="10%"></td>
                        <td width="45%" style="border-top: 1px solid #cbd5e1; padding-top: 5px;">
                            <div style="font-size: 8px; font-weight: bold; color: #94a3b8; text-align: center; text-transform: uppercase;">Entregado por (Taller)</div>
                        </td>
                    </tr>
                </table>
            </td>
            <td width="5%"></td>
            <td width="35%" style="vertical-align: top;">
                <div class="total-box">
                    <div class="total-label">Total a Pagar</div>
                    <div class="total-amount">${{ number_format($order->total_cost ?? 0, 2) }}</div>
                    
                    @if($order->payment_status == 'paid')
                        <div style="margin-top: 5px; font-size: 10px; font-weight: bold; color: #4ade80;">PAGADO</div>
                    @else
                        <div style="margin-top: 5px; font-size: 10px; font-weight: bold; color: #f87171;">PENDIENTE</div>
                    @endif
                </div>
            </td>
        </tr>
    </table>

</body>
</html>
