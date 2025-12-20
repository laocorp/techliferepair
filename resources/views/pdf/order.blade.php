<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Orden #{{ $order->ticket_number ?? str_pad($order->id, 5, '0', STR_PAD_LEFT) }}</title>
    <style>
        @page { margin: 0; }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            color: #1e293b;
            font-size: 11px;
            line-height: 1.4;
            background-color: #fff;
            /* Márgenes para el contenido (dejan espacio a header/footer) */
            margin-top: 4.8cm;
            margin-bottom: 2.5cm;
            margin-left: 1.5cm;
            margin-right: 1.5cm;
        }

        /* --- HEADER --- */
        header {
            position: fixed; top: 0; left: 0; right: 0; height: 3cm;
            background-color: #ffffff;
            border-bottom: 2px solid #0f172a; /* Línea fuerte de marca */
            padding: 0.8cm 1.5cm;
        }

        .logo-text { font-size: 29px; font-weight: 900; text-transform: uppercase; letter-spacing: -0.5px; color: #0f172a; }
        .company-details { font-size: 14px; color: #64748b; margin-top: 4px; line-height: 1.3; }
        
        .doc-title { font-size: 10px; font-weight: bold; text-transform: uppercase; color: #3b82f6; letter-spacing: 2px; text-align: right; }
        .doc-number { font-size: 24px; font-weight: 900; color: #0f172a; text-align: right; letter-spacing: -0.5px; line-height: 1; margin-top: 2px; }
        .doc-date { font-size: 10px; color: #64748b; text-align: right; margin-top: 4px; }

        /* --- FOOTER --- */
        footer {
            position: fixed; bottom: 0; left: 0; right: 0; height: 1.8cm;
            background-color: #f8fafc;
            border-top: 1px solid #e2e8f0;
            padding: 12px 1.5cm;
            text-align: center;
            font-size: 8px;
            color: #94a3b8;
        }
        .warranty-tag {
            display: inline-block; background: #eff6ff; color: #1d4ed8; border: 1px solid #dbeafe;
            padding: 2px 6px; border-radius: 4px; font-weight: bold; margin-bottom: 4px;
        }

        /* --- LAYOUT --- */
        table { width: 100%; border-collapse: collapse; }
        .section-spacing { margin-top: 25px; }

        /* --- CAJAS DE INFO (Cliente / Equipo) --- */
        .info-box { border: 1px solid #e2e8f0; border-radius: 4px; overflow: hidden; }
        .box-header { background-color: #f1f5f9; padding: 6px 10px; border-bottom: 1px solid #e2e8f0; font-size: 9px; font-weight: bold; color: #475569; text-transform: uppercase; letter-spacing: 0.5px; }
        .box-body { padding: 10px; background-color: #fff; }
        
        .label { font-size: 8px; color: #94a3b8; text-transform: uppercase; font-weight: bold; display: block; margin-bottom: 1px; }
        .value { font-size: 11px; color: #0f172a; font-weight: 600; margin-bottom: 6px; display: block; }
        .value-mono { font-family: monospace; background: #f8fafc; padding: 2px 4px; border-radius: 3px; }

        /* --- TABLA DE ITEMS --- */
        .items-table th { 
            text-align: left; padding: 8px 5px; 
            border-bottom: 2px solid #e2e8f0; 
            font-size: 9px; font-weight: bold; text-transform: uppercase; color: #64748b;
        }
        .items-table td { 
            padding: 10px 5px; 
            border-bottom: 1px solid #f1f5f9; 
            font-size: 11px; 
            color: #334155;
        }
        .item-name { font-weight: bold; color: #0f172a; }
        .item-sub { font-size: 9px; color: #94a3b8; }

        /* --- TOTALES --- */
        .totals-area { margin-top: 20px; }
        .total-row td { padding: 3px 0; text-align: right; }
        .total-label { font-size: 10px; font-weight: bold; color: #64748b; text-transform: uppercase; }
        .total-value { font-size: 12px; font-weight: bold; color: #0f172a; width: 100px; }
        .grand-total { font-size: 16px; font-weight: 900; color: #0f172a; border-top: 2px solid #0f172a; padding-top: 5px; }

        /* --- ESTADO --- */
        .status-pill {
            background-color: #f1f5f9; color: #475569; 
            padding: 5px 12px; border-radius: 20px; 
            font-size: 10px; font-weight: bold; text-transform: uppercase; 
            border: 1px solid #e2e8f0; letter-spacing: 0.5px;
        }
        
        /* --- QR --- */
        .qr-container { text-align: right; margin-top: 5px; }
    </style>
</head>
<body>

    <header>
        <table width="100%">
            <tr>
                <td width="60%" valign="top">
                    <div class="logo-text">{{ $settings->company_name ?? 'TECHLIFE' }}</div>
                    <div class="company-details">
                        {{ $settings->company_address }} <br>
                        {{ $settings->company_phone }} • {{ $settings->company_email }} <br>
                        RUC: {{ $settings->tax_id }}
                    </div>
                </td>
                <td width="40%" valign="top" align="right">
                    <div class="doc-title">Orden de Servicio</div>
                    <div class="doc-number">#{{ $order->ticket_number ?? str_pad($order->id, 5, '0', STR_PAD_LEFT) }}</div>
                    <div class="doc-date">{{ $order->created_at->format('d/m/Y') }}</div>
                    
                    @if($order->tracking_token)
                    <div class="qr-container">
                         <!-- QR en SVG para máxima nitidez -->
                         <img src="data:image/svg+xml;base64, {{ base64_encode(\SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')->size(65)->margin(0)->color(15, 23, 42)->generate(route('track.status', $order->tracking_token))) }}" width="65">
                    </div>
                    @endif
                </td>
            </tr>
        </table>
    </header>

    <footer>
        @if($settings->warranty_text)
            <div class="warranty-tag">GARANTÍA DEL SERVICIO</div>
            <div style="margin-bottom: 5px;">{{ $settings->warranty_text }}</div>
        @endif
        <div style="opacity: 0.5;">Documento oficial generado por {{ config('app.name') }}.</div>
    </footer>

    <!-- CONTENIDO -->

    <!-- 1. GRID CLIENTE / EQUIPO -->
    <table style="width: 100%; border-spacing: 0; margin-bottom: 20px;">
        <tr>
            <!-- Caja Cliente -->
            <td width="48%" valign="top" style="padding-right: 10px;">
                <div class="info-box">
                    <div class="box-header">Cliente / Propietario</div>
                    <div class="box-body">
                        <span class="label">Nombre</span>
                        <span class="value">{{ $order->asset->client->name }}</span>
                        
                        <table width="100%">
                            <tr>
                                <td><span class="label">ID/RUC</span><span class="value" style="margin:0">{{ $order->asset->client->tax_id ?? '-' }}</span></td>
                                <td><span class="label">Teléfono</span><span class="value" style="margin:0">{{ $order->asset->client->phone ?? '-' }}</span></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </td>
            
            <td width="4%"></td>
            
            <!-- Caja Equipo -->
            <td width="48%" valign="top" style="padding-left: 10px;">
                <div class="info-box">
                    <div class="box-header">Equipo / Activo</div>
                    <div class="box-body">
                        <span class="label">Equipo</span>
                        <span class="value">{{ $order->asset->brand }} {{ $order->asset->model }}</span>
                        
                        <table width="100%">
                            <tr>
                                <td><span class="label">Serial</span><span class="value value-mono" style="margin:0">{{ $order->asset->serial_number }}</span></td>
                                <td><span class="label">Tipo</span><span class="value" style="margin:0">{{ $order->asset->type ?? 'General' }}</span></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </td>
        </tr>
    </table>

    <!-- 2. REPORTE DE FALLA -->
    <div class="section-spacing">
        <div class="label" style="border-bottom: 1px solid #e2e8f0; padding-bottom: 2px; margin-bottom: 5px;">REPORTE DE FALLA</div>
        <div style="background: #f8fafc; padding: 12px; border-radius: 4px; border: 1px solid #e2e8f0; color: #475569; font-style: italic;">
            "{{ $order->problem_description }}"
        </div>
    </div>

    <!-- 3. DETALLES Y COSTOS -->
    <div class="section-spacing">
        <table class="items-table">
            <thead>
                <tr>
                    <th width="50%">Descripción</th>
                    <th width="15%" style="text-align: center;">Cant</th>
                    <th width="15%" style="text-align: right;">Precio</th>
                    <th width="20%" style="text-align: right;">Total</th>
                </tr>
            </thead>
            <tbody>
                @if($order->parts->count() > 0)
                    @foreach($order->parts as $part)
                    <tr>
                        <td>
                            <div class="item-name">{{ $part->name }}</div>
                            <div class="item-sub">SKU: {{ $part->sku }}</div>
                        </td>
                        <td align="center">{{ $part->pivot->quantity }}</td>
                        <td align="right">${{ number_format($part->pivot->price, 2) }}</td>
                        <td align="right" style="font-weight: bold;">${{ number_format($part->pivot->price * $part->pivot->quantity, 2) }}</td>
                    </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="4" align="center" style="color: #94a3b8; padding: 15px;">- Solo Mano de Obra -</td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>

    <!-- 4. TOTALES Y ESTADO -->
    <table class="totals-area" width="100%">
        <tr>
            <td width="60%" valign="bottom">
                <span class="label">Estado Actual</span>
                <span class="status-pill">{{ strtoupper(str_replace('_', ' ', $order->status)) }}</span>

                <!-- FIRMAS -->
                <table width="90%" style="margin-top: 40px;">
                    <tr>
                        <td width="45%" align="center" style="border-top: 1px solid #94a3b8; padding-top: 4px;">
                            <div style="font-size: 7px; font-weight: bold; color: #94a3b8; text-transform: uppercase;">Recibido (Taller)</div>
                        </td>
                        <td width="10%"></td>
                        <td width="45%" align="center" style="border-top: 1px solid #94a3b8; padding-top: 4px;">
                            <div style="font-size: 7px; font-weight: bold; color: #94a3b8; text-transform: uppercase;">Aceptado (Cliente)</div>
                        </td>
                    </tr>
                </table>
            </td>
            
            <td width="5%"></td>
            
            <td width="35%" valign="top">
                <div style="background: #f8fafc; padding: 15px; border-radius: 4px; border: 1px solid #e2e8f0;">
                    <!-- Aquí podrías agregar Subtotal e IVA si lo calculas en el futuro -->
                    <table width="100%">
                        <tr class="total-row">
                            <td class="total-label">Total a Pagar</td>
                            <td class="total-value grand-total">${{ number_format($order->total_cost ?? 0, 2) }}</td>
                        </tr>
                    </table>
                    
                    <div style="text-align: right; margin-top: 8px;">
                        @if($order->payment_status == 'paid')
                            <span style="color: #16a34a; font-weight: bold; font-size: 10px; border: 1px solid #16a34a; padding: 2px 5px; border-radius: 3px;">PAGADO</span>
                        @else
                            <span style="color: #dc2626; font-weight: bold; font-size: 10px; border: 1px solid #dc2626; padding: 2px 5px; border-radius: 3px;">PENDIENTE</span>
                        @endif
                    </div>
                </div>
            </td>
        </tr>
    </table>

</body>
</html>
