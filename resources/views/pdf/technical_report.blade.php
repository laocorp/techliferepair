<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Informe Técnico #{{ str_pad($order->id, 4, '0', STR_PAD_LEFT) }}</title>
    <style>
        @page { margin: 0; }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            color: #1e293b;
            font-size: 11px;
            line-height: 1.4;
            margin-top: 3.5cm;
            margin-bottom: 2cm;
            margin-left: 1.5cm;
            margin-right: 1.5cm;
            background-color: #fff;
        }

        /* HEADER */
        header {
            position: fixed; top: 0; left: 0; right: 0; height: 2.5cm;
            background-color: #fffbeb; /* Fondo crema suave técnico */
            border-bottom: 2px solid #f59e0b;
        }
        .header-content { margin-left: 1.5cm; margin-right: 1.5cm; padding-top: 0.8cm; }
        
        .doc-type { color: #d97706; font-weight: 900; text-transform: uppercase; font-size: 14px; text-align: right; letter-spacing: 1px; }
        .company-name { font-weight: 900; font-size: 18px; text-transform: uppercase; color: #0f172a; }

        /* FOOTER */
        footer {
            position: fixed; bottom: 0; left: 0; right: 0; height: 1.5cm;
            background-color: #fff; border-top: 1px solid #e2e8f0;
        }
        .footer-content { margin-left: 1.5cm; margin-right: 1.5cm; padding-top: 10px; text-align: center; font-size: 8px; color: #64748b; }

        /* ESTILOS GRID */
        .grid-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .grid-cell { border: 1px solid #e2e8f0; padding: 8px; vertical-align: top; }
        .grid-label { color: #64748b; font-size: 8px; font-weight: bold; text-transform: uppercase; display: block; margin-bottom: 2px; }
        .grid-value { font-weight: bold; font-size: 11px; color: #0f172a; }

        /* SECCIONES */
        .section-header {
            background-color: #f1f5f9; color: #0f172a; font-weight: bold; font-size: 10px;
            text-transform: uppercase; padding: 5px 10px; margin-top: 20px; margin-bottom: 10px;
            border-left: 4px solid #cbd5e1;
        }
        
        /* CHECKLIST */
        .checklist-table { width: 100%; border-collapse: collapse; font-size: 10px; }
        .checklist-table td { border-bottom: 1px solid #f1f5f9; padding: 6px 0; }
        
        /* FOTOS */
        .photo-wrapper {
            display: inline-block; width: 32%; margin-right: 1%; margin-bottom: 10px;
            border: 1px solid #e2e8f0; padding: 3px; background: #fff; border-radius: 4px; vertical-align: top;
        }
        .photo-img { width: 100%; height: 120px; object-fit: contain; background: #f8fafc; }

        .text-area { text-align: justify; font-size: 11px; line-height: 1.6; }
    </style>
</head>
<body>

    <header>
        <div class="header-content">
            <table style="width: 100%;">
                <tr>
                    <td>
                        <div class="company-name">{{ $settings->company_name ?? 'TECHLIFE' }}</div>
                        <div style="font-size: 9px; color: #64748b; text-transform: uppercase;">Departamento de Ingeniería</div>
                    </td>
                    <td>
                        <div class="doc-type">Informe Técnico</div>
                        <div style="text-align: right; font-weight: bold; font-size: 12px;">Ref: #{{ $order->ticket_number ?? str_pad($order->id, 5, '0', STR_PAD_LEFT) }}</div>
                        <div style="text-align: right; font-size: 9px; color: #64748b;">{{ date('d/m/Y') }}</div>
                    </td>
                </tr>
            </table>
        </div>
    </header>

    <footer>
        <div class="footer-content">
            {{ $settings->company_name }} - {{ $settings->company_address }} <br>
            Certificación técnica de estado.
        </div>
    </footer>

    <!-- DATOS -->
    <table class="grid-table">
        <tr>
            <td width="50%" class="grid-cell" style="background-color: #fffbeb;">
                <span class="grid-label">Cliente</span>
                <span class="grid-value">{{ $order->asset->client->name }}</span>
                <div style="font-size: 9px; margin-top: 2px;">ID: {{ $order->asset->client->tax_id ?? 'N/A' }}</div>
            </td>
            <td width="25%" class="grid-cell">
                <span class="grid-label">Equipo</span>
                <span class="grid-value">{{ $order->asset->brand }} {{ $order->asset->model }}</span>
            </td>
            <td width="25%" class="grid-cell">
                <span class="grid-label">Serial</span>
                <span class="grid-value" style="font-family: monospace;">{{ $order->asset->serial_number }}</span>
            </td>
        </tr>
    </table>

    <!-- 1. INSPECCIÓN -->
    @if(!empty($order->technicalReport->checklist))
    <div class="section-header">1. Inspección Visual</div>
    <table class="checklist-table">
        @foreach(array_chunk($order->technicalReport->checklist, 2, true) as $chunk)
            <tr>
                @foreach($chunk as $key => $val)
                    <td width="35%" style="font-weight: 600; color: #334155;">{{ $key }}</td>
                    <td width="15%" style="text-align: right;">
                        @if($val == 'ok') <span style="color: #15803d; font-weight: bold;">✓ OK</span>
                        @elseif($val == 'falla') <span style="color: #b91c1c; font-weight: bold;">✕ FALLA</span>
                        @else <span style="color: #94a3b8;">N/A</span> @endif
                    </td>
                @endforeach
                @if(count($chunk) < 2) <td colspan="2"></td> @endif
            </tr>
        @endforeach
    </table>
    @endif

    <!-- 2. HALLAZGOS -->
    <div class="section-header">2. Diagnóstico Técnico</div>
    <div class="text-area">
        @if(!empty($order->technicalReport->findings))
            {!! nl2br(e($order->technicalReport->findings)) !!}
        @else
            <span style="color: #94a3b8; font-style: italic;">Sin observaciones.</span>
        @endif
    </div>

    <!-- 3. PROCEDIMIENTO -->
    <div class="section-header">3. Solución Aplicada</div>
    <div class="text-area">
        @if(!empty($order->technicalReport->recommendations))
            {!! nl2br(e($order->technicalReport->recommendations)) !!}
        @else
            <span style="color: #94a3b8; font-style: italic;">En evaluación.</span>
        @endif
    </div>

    <!-- 4. FOTOS -->
    @if(!empty($order->technicalReport->photos))
        <div class="section-header" style="page-break-before: auto;">4. Evidencia Fotográfica</div>
        <div style="text-align: left;">
            @foreach($order->technicalReport->photos as $index => $photo)
                <div class="photo-wrapper">
                    <!-- Usamos public_path para asegurar que DOMPDF encuentre la imagen -->
                    <img src="{{ public_path('storage/' . $photo) }}" class="photo-img">
                    <div style="font-size: 8px; text-align: center; margin-top: 3px; color: #64748b;">IMG #{{ $index + 1 }}</div>
                </div>
            @endforeach
        </div>
    @endif

    <!-- FIRMA -->
    <table style="width: 100%; margin-top: 40px;">
        <tr>
            <td width="30%"></td>
            <td width="40%" style="text-align: center;">
                <div style="border-bottom: 1px solid #0f172a; margin-bottom: 5px;"></div>
                <div style="font-weight: bold; font-size: 10px;">Técnico Responsable</div>
            </td>
            <td width="30%"></td>
        </tr>
    </table>

</body>
</html>
