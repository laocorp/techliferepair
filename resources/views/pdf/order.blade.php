<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Orden #{{ str_pad($order->id, 4, '0', STR_PAD_LEFT) }}</title>
    <style>
        /* --- CONFIGURACIÓN BASE --- */
        @page { margin: 0; }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            margin-top: 3.5cm; /* Aumenté un poco para que quepa el QR sin chocar */
            margin-bottom: 2cm;
            margin-left: 1cm;
            margin-right: 1cm;
            color: #1f2937;
            background-color: #fff;
            font-size: 12px;
            line-height: 1.4;
        }

        /* --- HEADER FIJO --- */
        header {
            position: fixed;
            top: 0cm;
            left: 0cm;
            right: 0cm;
            height: 3cm; /* Header más alto para alojar el QR */
            padding: 0.5cm 1cm;
            border-bottom: 3px solid #3b82f6;
            background-color: #f8fafc;
        }

        /* --- FOOTER FIJO --- */
        footer {
            position: fixed;
            bottom: 0cm;
            left: 0cm;
            right: 0cm;
            height: 1.5cm;
            text-align: center;
            font-size: 9px;
            color: #64748b;
            border-top: 1px solid #e2e8f0;
            padding-top: 10px;
            background-color: #fff;
        }

        /* --- UTILIDADES --- */
        .w-100 { width: 100%; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .text-primary { color: #3b82f6; }
        .text-bold { font-weight: bold; }
        .uppercase { text-transform: uppercase; }
        
        /* --- TABLAS --- */
        .header-table { width: 100%; border-collapse: collapse; }
        .logo { font-size: 24px; font-weight: 900; letter-spacing: -1px; text-transform: uppercase; color: #0f172a; }
        .sub-logo { font-size: 10px; color: #64748b; margin-top: 2px; }
        
        /* --- CAJAS (GRID) --- */
        .boxes-table { width: 100%; border-collapse: separate; border-spacing: 0; margin-bottom: 20px; margin-top: 10px; }
        .box-cell {
            width: 48%;
            border: 1px solid #e2e8f0;
            padding: 12px;
            vertical-align: top;
            border-radius: 6px;
            background-color: #fff;
        }
        .spacer-cell { width: 4%; }

        .box-title {
            color: #3b82f6;
            font-weight: bold;
            font-size: 10px;
            text-transform: uppercase;
            margin-bottom: 8px;
            border-bottom: 1px solid #f1f5f9;
            padding-bottom: 4px;
            letter-spacing: 0.5px;
        }
        .box-content { font-size: 12px; }
        .box-label { color: #94a3b8; font-size: 9px; font-weight: bold; text-transform: uppercase; }

        /* --- SECCIONES --- */
        .section-title {
            font-size: 10px;
            font-weight: bold;
            color: #64748b;
            text-transform: uppercase;
            margin-bottom: 5px;
            margin-top: 20px;
            letter-spacing: 0.5px;
        }
        .grey-box {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            padding: 12px;
            font-style: italic;
            color: #334151;
            font-size: 11px;
            border-radius: 6px;
        }

        /* --- TABLA ÍTEMS --- */
        .items-table { width: 100%; border-collapse: collapse; margin-top: 5px; }
        .items-table th { 
            background: #f1f5f9; 
            font-size: 9px; 
            text-align: left; 
            padding: 8px; 
            border-bottom: 1px solid #cbd5e1;
            color: #475569;
            font-weight: bold;
            text-transform: uppercase;
        }
        .items-table td { padding: 8px; border-bottom: 1px solid #e2e8f0; font-size: 11px; }

        /* --- ESTADOS --- */
        .status-badge {
            color: white;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
            display: inline-block;
            letter-spacing: 0.5px;
        }
        .bg-recibido { background-color: #3b82f6; }
        .bg-diagnostico { background-color: #f59e0b; }
        .bg-espera_repuestos { background-color: #ef4444; }
        .bg-listo { background-color: #10b981; }
        .bg-entregado { background-color: #64748b; }

        /* --- TOTALES --- */
        .bottom-table { width: 100%; margin-top: 30px; border-collapse: collapse; }
        .total-box {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            padding: 15px;
            text-align: right;
            border-radius: 6px;
        }
        .total-label { font-size: 10px; font-weight: bold; color: #64748b; text-transform: uppercase; margin-bottom: 5px; }
        .total-amount { font-size: 26px; font-weight: 900; color: #0f172a; letter-spacing: -1px; }

        /* --- FIRMAS --- */
        .signatures-table { width: 100%; margin-top: 60px; }
        .sign-line {
            border-top: 1px solid #cbd5e1;
            width: 85%;
            margin: 0 auto;
            padding-top: 8px;
            font-size: 10px;
            color: #64748b;
            text-align: center;
            text-transform: uppercase;
        }
    </style>
</head>
<body>

    <header>
        <table class="header-table">
            <tr>
                <td style="vertical-align: top; width: 60%;">
                    <div class="logo">{{ $settings->company_name ?? 'TECHLIFE' }}</div>
                    <div class="sub-logo">
                        {{ $settings->company_address }} <br>
                        {{ $settings->company_phone }} | {{ $settings->company_email }} <br>
                        RUC: {{ $settings->tax_id }}
                    </div>
                </td>

                <td class="text-right" style="vertical-align: top; width: 40%;">
                    <div class="text-primary text-bold uppercase" style="font-size: 12px; letter-spacing: 1px;">Orden de Trabajo</div>
                    <div style="font-size: 26px; font-weight: bold; color: #0f172a; line-height: 1;">
                        #{{ str_pad($order->id, 4, '0', STR_PAD_LEFT) }}
                    </div>
                    
                    @if($order->tracking_token)
                        <div style="margin-top: 8px; float: right;">
                             <img src="data:image/png;base64, {{ base64_encode(\SimpleSoftwareIO\QrCode\Facades\QrCode::format('png')->size(70)->generate(route('track.status', $order->tracking_token))) }}">
                            <div style="font-size: 8px; color: #94a3b8; margin-top: 2px;">Escanear para rastrear</div>
                        </div>
                    @endif

                    <div style="clear: both; font-size: 10px; color: #64748b; padding-top: 5px;">
                        {{ $order->created_at->format('d/m/Y - h:i A') }}
                    </div>
                </td>
            </tr>
        </table>
    </header>

    <footer>
        <strong>{{ $settings->company_name }}</strong> - Documento oficial generado electrónicamente. <br>
        {{ $settings->warranty_text }}
    </footer>

    <table class="boxes-table">
        <tr>
            <td class="box-cell">
                <div class="box-title">Equipo / Maquinaria</div> <div class="box-content">
                    <div style="font-size: 13px; font-weight: bold; margin-bottom: 5px; color: #0f172a;">
                        {{ $order->asset->brand }} {{ $order->asset->model }}
                    </div>
                    <span class="box-label">Serial / Placa:</span> {{ $order->asset->serial_number }}<br>
                    <span class="box-label">Tipo:</span> {{ $order->asset->type ?? 'General' }}
                </div>
            </td>
            <td class="spacer-cell"></td>
            <td class="box-cell">
                <div class="box-title">Equipo / Activo</div>
                <div class="box-content">
                    <div style="font-size: 13px; font-weight: bold; margin-bottom: 5px; color: #0f172a;">
                        {{ $order->asset->brand }} {{ $order->asset->model }}
                    </div>
                    <span class="box-label">Serial:</span> {{ $order->asset->serial_number }}<br>
                    <span class="box-label">Tipo:</span> {{ $order->asset->type ?? 'General' }}
                </div>
            </td>
        </tr>
    </table>

    <div class="section-title">Reporte Inicial del Cliente</div>
    <div class="grey-box">
        "{{ $order->problem_description }}"
    </div>

    @if($order->diagnosis_notes)
        <div class="section-title">Diagnóstico Técnico</div>
        <div style="padding: 12px; border: 1px solid #e2e8f0; border-radius: 6px; font-size: 11px; background: #fff;">
            {{ $order->diagnosis_notes }}
        </div>
    @endif

    @if($order->parts->count() > 0)
        <div class="section-title">Repuestos y Servicios</div>
        <table class="items-table">
            <thead>
                <tr>
                    <th>Descripción</th>
                    <th class="text-center">Cant.</th>
                    <th class="text-right">Precio Unit.</th>
                    <th class="text-right">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->parts as $part)
                <tr>
                    <td>
                        <span style="font-weight: bold; color: #334155;">{{ $part->name }}</span> 
                        <span style="color:#94a3b8; font-size:9px;">(SKU: {{ $part->sku }})</span>
                    </td>
                    <td class="text-center">{{ $part->pivot->quantity }}</td>
                    <td class="text-right">${{ number_format($part->pivot->price, 2) }}</td>
                    <td class="text-right text-bold">${{ number_format($part->pivot->price * $part->pivot->quantity, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <table class="bottom-table">
        <tr>
            <td style="width: 60%; vertical-align: top;">
                <div class="section-title" style="margin-top: 0;">Estado Actual</div>
                
                @php
                    $colorClass = match($order->status) {
                        'recibido' => 'bg-recibido',
                        'diagnostico' => 'bg-diagnostico',
                        'espera_repuestos' => 'bg-espera_repuestos',
                        'listo' => 'bg-listo',
                        'entregado' => 'bg-entregado',
                        default => 'bg-recibido',
                    };
                @endphp

                <span class="status-badge {{ $colorClass }}">
                    {{ strtoupper(str_replace('_', ' ', $order->status)) }}
                </span>

                @if($order->is_warranty)
                    <div style="margin-top: 10px; color: #eab308; font-weight: bold; font-size: 10px; display: flex; align-items: center;">
                        ★ REPARACIÓN POR GARANTÍA
                    </div>
                @endif
            </td>
            <td style="width: 40%; vertical-align: top;">
                <div class="total-box">
                    <div class="total-label">Total a Pagar</div>
                    <div class="total-amount">${{ number_format($order->total_cost ?? 0, 2) }}</div>
                </div>
            </td>
        </tr>
    </table>

    <table class="signatures-table">
        <tr>
            <td>
                <div class="sign-line">Recibido por (Taller)</div>
            </td>
            <td>
                <div class="sign-line">Aceptado por (Cliente)</div>
            </td>
        </tr>
    </table>

</body>
</html>
