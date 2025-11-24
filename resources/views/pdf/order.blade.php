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
            margin-top: 3.8cm; /* Espacio para header fijo */
            margin-bottom: 2.5cm; /* Espacio para footer fijo */
            margin-left: 1.5cm;
            margin-right: 1.5cm;
            background-color: #fff;
        }

        /* --- HEADER FIJO PREMIUM --- */
        header {
            position: fixed; top: 0; left: 0; right: 0; height: 3cm;
            background-color: #ffffff;
            border-bottom: 2px solid #0f172a; /* Línea sólida oscura */
            padding: 0.8cm 1.5cm;
        }
        
        .logo-text { font-size: 22px; font-weight: 900; color: #0f172a; text-transform: uppercase; letter-spacing: -0.5px; }
        .company-details { font-size: 9px; color: #64748b; margin-top: 4px; line-height: 1.4; }

        .doc-title { font-size: 10px; font-weight: bold; text-transform: uppercase; color: #3b82f6; letter-spacing: 2px; text-align: right; }
        .doc-number { font-size: 28px; font-weight: 900; color: #0f172a; text-align: right; letter-spacing: -1px; margin-top: 2px; line-height: 1; }
        .doc-date { font-size: 10px; color: #64748b; text-align: right; margin-top: 4px; font-weight: 500; }

        /* --- FOOTER FIJO --- */
        footer {
            position: fixed; bottom: 0; left: 0; right: 0;
            height: 2cm;
            background-color: #f8fafc;
            border-top: 1px solid #e2e8f0;
            padding: 15px 1.5cm;
            text-align: center;
        }
        .footer-text { font-size: 8px; color: #94a3b8; line-height: 1.4; }
        .warranty-badge {
            display: inline-block; background: #e0f2fe; color: #0369a1; 
            font-size: 9px; font-weight: bold; padding: 3px 8px; border-radius: 4px; margin-bottom: 5px;
            text-transform: uppercase; letter-spacing: 0.5px;
        }

        /* --- ESTRUCTURA --- */
        table { width: 100%; border-collapse: collapse; }
        .section-spacing { margin-top: 30px; }

        /* --- CAJAS DE CLIENTE & EQUIPO --- */
        .box-container { border: 1px solid #e2e8f0; border-radius: 6px; overflow: hidden; }
        .box-header { background-color: #f1f5f9; padding: 8px 12px; border-bottom: 1px solid #e2e8f0; }
        .box-title { font-size: 9px; font-weight: 900; text-transform: uppercase; color: #475569; letter-spacing: 1px; }
        .box-body { padding: 12px; background-color: #fff; }
        
        .data-label { font-size: 8px; color: #94a3b8; text-transform: uppercase; font-weight: bold; margin-bottom: 2px; display: block; letter-spacing: 0.5px; }
        .data-value { font-size: 12px; color: #0f172a; font-weight: 600; margin-bottom: 10px; display: block; }
        .last-value { margin-bottom: 0; }

        /* --- REPORTE --- */
        .section-label { font-size: 10px; font-weight: bold; color: #94a3b8; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 8px; border-bottom: 1px solid #e2e8f0; padding-bottom: 4px; display: block; width: 100%; }
        
        .report-box { background-color: #f8fafc; border-left: 4px solid #3b82f6; padding: 15px; border-radius: 0 4px 4px 0; }
        .report-text { font-style: italic; color: #334155; }

        /* --- TABLA DE DETALLES --- */
        .items-table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        .items-table th { 
            text-align: left; padding: 10px; background-color: #0f172a; color: #fff;
            font-size: 9px; text-transform: uppercase; font-weight: bold; letter-spacing: 1px;
        }
        .items-table td { 
            padding: 12px 10px; 
            border-bottom: 1px solid #f1f5f9;
            font-size: 11px;
            font-weight: 500;
        }
        .item-name { font-weight: bold; color: #1e293b; font-size: 12px; }
        .item-sku { font-size: 9px; color: #94a3b8; font-family: monospace; margin-top: 2px; }
        .amount-col { text-align: right; font-family: monospace; font-size: 12px; font-weight: bold; }

        /* --- TOTALES --- */
        .total-box { background-color: #0f172a; color: white; padding: 20px; border-radius: 6px; text-align: right; }
        .total-label { font-size: 10px; text-transform: uppercase; opacity: 0.8; letter-spacing: 2px; font-weight: bold; }
        .total-amount { font-size: 32px; font-weight: 900; color: #fff; line-height: 1; margin-top: 5px; letter-spacing: -1px; }
        
        /* --- QR --- */
        .qr-container { text-align: right; margin-top: 8px; }
        
        /* STATUS */
        .status-pill {
            display: inline-block;
            background-color: #f1f5f9; color: #475569; padding: 6px 12px; 
            border-radius: 20px; font-size: 10px; font-weight: 900; text-transform: uppercase; border: 1px solid #e2e8f0; letter-spacing: 0.5px;
        }
    </style>
</head>
<body>

    <header>
        <table style="width: 100%;">
            <tr>
                <td width="60%" style="vertical-align: top;">
                    <!-- Datos de la Empresa -->
                    <div class="logo-text">{{ $settings->company_name ?? 'TECHLIFE' }}</div>
                    <div class="company-details">
                        {{ $settings->company_address }} <br>
                        {{ $settings->company_phone }} • {{ $settings->company_email }} <br>
                        <strong>RUC/ID: {{ $settings->tax_id }}</strong>
                    </div>
                </td>
                <td width="40%" style="vertical-align: top; text-align: right;">
                    <div class="doc-title">Orden de Servicio</div>
                    <div class="doc-number">#{{ $order->ticket_number ?? str_pad($order->id, 5, '0', STR_PAD_LEFT) }}</div>
                    <div class="doc-date">{{ $order->created_at->format('d F, Y') }} &nbsp;|&nbsp; {{ $order->created_at->format('h:i A') }}</div>
                    
                    <!-- QR -->
                    @if($order->tracking_token)
                        <div class="qr-container">
                             <!-- Usamos SVG para máxima nitidez en impresión -->
                             <img src="data:image/svg+xml;base64, {{ base64_encode(\SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')->size(70)->margin(0)->color(15, 23, 42)->generate(route('track.status', $order->tracking_token))) }}" width="70">
                        </div>
                    @endif
                </td>
            </tr>
        </table>
    </header>

    <footer>
        @if($settings->warranty_text)
            <div class="warranty-badge">GARANTÍA DEL SERVICIO</div>
            <div class="footer-text">{{ $settings->warranty_text }}</div>
        @endif
        <div class="footer-text" style="margin-top: 8px; opacity: 0.6;">
            Documento oficial generado electrónicamente por sistema {{ config('app.name') }}.
        </div>
    </footer>

    <!-- CONTENIDO -->
    
    <!-- 1. CLIENTE Y EQUIPO (GRID PERFECTO) -->
    <table style="width: 100%; border-spacing: 0; margin-top: 10px;">
        <tr>
            <!-- CAJA CLIENTE -->
            <td width="48%" style="vertical-align: top; padding-right: 10px;">
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
                        <div style="margin-top: 8px;">
                             <span class="data-label">Email</span>
                             <span class="data-value last-value">{{ $order->asset->client->email ?? '---' }}</span>
                        </div>
                    </div>
                </div>
            </td>
            
            <td width="4%"></td>
            
            <!-- CAJA EQUIPO -->
            <td width="48%" style="vertical-align: top; padding-left: 10px;">
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
                                    <span class="data-value last-value" style="font-family: monospace; background: #f1f5f9; padding: 2px 6px; border-radius: 4px;">
                                        {{ $order->asset->serial_number }}
                                    </span>
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

    <!-- 2. REPORTE DE FALLA -->
    <div class="section-spacing">
        <div class="section-label">Reporte Inicial de Falla</div>
        <div class="report-box">
            <span class="report-text">"{{ $order->problem_description }}"</span>
        </div>
    </div>

    <!-- 3. DIAGNÓSTICO (Opcional) -->
    @if($order->diagnosis_notes)
        <div class="section-spacing">
            <div class="section-label">Informe Técnico</div>
            <div style="font-size: 11px; color: #334155; line-height: 1.6; text-align: justify;">
                {!! nl2br(e($order->diagnosis_notes)) !!}
            </div>
        </div>
    @endif

    <!-- 4. TABLA DE REPUESTOS Y SERVICIOS -->
    <div class="section-spacing">
        <div class="section-label">Detalle de Costos</div>
        <table class="items-table">
            <thead>
                <tr>
                    <th width="50%" style="border-radius: 4px 0 0 4px;">Descripción</th>
                    <th width="15%" style="text-align: center;">Cant.</th>
                    <th width="15%" style="text-align: right;">P. Unitario</th>
                    <th width="20%" style="text-align: right; border-radius: 0 4px 4px 0;">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @if($order->parts->count() > 0)
                    @foreach($order->parts as $part)
                    <tr>
                        <td>
                            <div class="item-name">{{ $part->name }}</div>
                            <div class="item-sku">SKU: {{ $part->sku }}</div>
                        </td>
                        <td style="text-align: center;">{{ $part->pivot->quantity }}</td>
                        <td class="amount-col">${{ number_format($part->pivot->price, 2) }}</td>
                        <td class="amount-col">${{ number_format($part->pivot->price * $part->pivot->quantity, 2) }}</td>
                    </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="4" style="text-align: center; color: #94a3b8; padding: 20px; font-style: italic;">
                            - Solo servicios de mano de obra -
                        </td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>

    <!-- 5. TOTALES Y FIRMAS -->
    <table style="width: 100%; margin-top: 30px;">
        <tr>
            <!-- Izquierda: Estado -->
            <td width="60%" style="vertical-align: bottom;">
                <div class="section-label">Estado Actual</div>
                
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
                
                <div class="status-pill">{{ strtoupper($statusLabel) }}</div>

                @if($order->is_warranty)
                    <div style="margin-top: 10px; color: #d97706; font-weight: bold; font-size: 10px; display: flex; align-items: center;">
                        ★ APLICA GARANTÍA DE FÁBRICA
                    </div>
                @endif
                
                <!-- Espacio para firmas -->
                <table style="width: 90%; margin-top: 50px;">
                    <tr>
                        <td width="45%" style="border-top: 1px solid #94a3b8; padding-top: 5px;">
                            <div style="font-size: 8px; font-weight: bold; color: #94a3b8; text-align: center; text-transform: uppercase; letter-spacing: 1px;">Firma Recibido (Taller)</div>
                        </td>
                        <td width="10%"></td>
                        <td width="45%" style="border-top: 1px solid #94a3b8; padding-top: 5px;">
                            <div style="font-size: 8px; font-weight: bold; color: #94a3b8; text-align: center; text-transform: uppercase; letter-spacing: 1px;">Firma Cliente (Acepto)</div>
                        </td>
                    </tr>
                </table>
            </td>
            
            <!-- Derecha: Total Gigante -->
            <td width="5%"></td>
            <td width="35%" style="vertical-align: top;">
                <div class="total-box">
                    <div class="total-label">Total a Pagar</div>
                    <div class="total-amount">${{ number_format($order->total_cost ?? 0, 2) }}</div>
                    
                    @if($order->payment_status == 'paid')
                        <div style="color: #4ade80; font-weight: bold; font-size: 11px; margin-top: 8px; border: 1px solid #4ade80; display: inline-block; padding: 2px 8px; border-radius: 4px;">PAGADO</div>
                    @else
                        <div style="color: #f87171; font-weight: bold; font-size: 11px; margin-top: 8px; border: 1px solid #f87171; display: inline-block; padding: 2px 8px; border-radius: 4px;">PENDIENTE</div>
                    @endif
                </div>
            </td>
        </tr>
    </table>

</body>
</html>
