<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Informe Técnico #{{ $order->ticket_number ?? $order->id }}</title>
    <style>
        @page { margin: 0; }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            color: #1e293b;
            font-size: 11px;
            line-height: 1.4;
            margin-top: 3.5cm; margin-bottom: 2cm; margin-left: 1.5cm; margin-right: 1.5cm;
            background-color: #fff;
        }

        /* HEADER TÉCNICO (Naranja) */
        header {
            position: fixed; top: 0; left: 0; right: 0; height: 2.5cm;
            background-color: #fffbeb; /* Crema suave */
            border-bottom: 3px solid #d97706; /* Naranja Ingeniero */
            padding: 0.8cm 1.5cm;
        }
        
        .tech-title { font-size: 16px; font-weight: 900; color: #0f172a; text-transform: uppercase; }
        .tech-subtitle { font-size: 9px; color: #b45309; font-weight: bold; text-transform: uppercase; letter-spacing: 1px; margin-top: 2px; }
        .ref-box { text-align: right; }
        .ref-number { font-size: 16px; font-weight: bold; color: #0f172a; font-family: monospace; }

        /* FOOTER */
        footer {
            position: fixed; bottom: 0; left: 0; right: 0; height: 1.5cm;
            background-color: #fff; border-top: 1px solid #e2e8f0;
            text-align: center; font-size: 8px; color: #64748b; padding-top: 10px;
        }

        /* GRID DE DATOS */
        .tech-grid { width: 100%; border-collapse: collapse; margin-bottom: 20px; border: 1px solid #e2e8f0; }
        .tech-grid td { padding: 8px; border: 1px solid #e2e8f0; vertical-align: top; width: 33.33%; }
        .label { font-size: 8px; color: #64748b; font-weight: bold; text-transform: uppercase; display: block; margin-bottom: 2px; }
        .value { font-size: 11px; font-weight: bold; color: #0f172a; }

        /* SECCIONES */
        .section-head {
            background-color: #f8fafc; color: #0f172a; font-size: 10px; font-weight: 900;
            text-transform: uppercase; padding: 5px 10px; border-left: 4px solid #d97706;
            margin-top: 20px; margin-bottom: 10px;
        }

        /* CHECKLIST */
        .checklist-table { width: 100%; border-collapse: collapse; font-size: 10px; }
        .checklist-table td { border-bottom: 1px solid #f1f5f9; padding: 6px 5px; }
        .status-ok { color: #15803d; font-weight: bold; }
        .status-fail { color: #b91c1c; font-weight: bold; }
        .status-na { color: #94a3b8; font-weight: bold; opacity: 0.6; }

        /* CAJAS DE TEXTO */
        .text-block {
            background-color: #fff; border: 1px solid #e2e8f0; padding: 10px;
            border-radius: 4px; text-align: justify; min-height: 40px; font-size: 11px;
        }

        /* FOTOS (Tabla 2x2) */
        .photos-table { width: 100%; border-collapse: separate; border-spacing: 10px; margin-top: 5px; table-layout: fixed; }
        .photo-cell {
            width: 50%; background-color: #fff; border: 1px solid #cbd5e1;
            padding: 4px; border-radius: 4px; text-align: center; vertical-align: top;
        }
        .photo-img {
            width: 100%; height: 160px; object-fit: contain; background-color: #f8fafc; display: block; margin: 0 auto;
        }
        .photo-caption { font-size: 8px; color: #64748b; font-weight: bold; text-transform: uppercase; margin-top: 4px; }

        /* FIRMA */
        .signature-line { border-top: 1px solid #0f172a; width: 60%; margin: 0 auto; margin-top: 50px; padding-top: 5px; }
        .signature-text { font-size: 9px; font-weight: bold; text-transform: uppercase; text-align: center; color: #0f172a; }
    </style>
</head>
<body>

    <header>
        <table width="100%">
            <tr>
                <td width="60%">
                    <div class="tech-title">{{ $settings->company_name ?? 'TECHLIFE' }}</div>
                    <div class="tech-subtitle">Departamento de Ingeniería & Diagnóstico</div>
                </td>
                <td width="40%" class="ref-box">
                    <div style="font-size: 10px; font-weight: bold; color: #d97706; text-transform: uppercase;">Informe Técnico</div>
                    <div class="ref-number">Ref: #{{ $order->ticket_number ?? str_pad($order->id, 5, '0', STR_PAD_LEFT) }}</div>
                    <div style="font-size: 9px; color: #64748b;">Fecha: {{ date('d/m/Y') }}</div>
                </td>
            </tr>
        </table>
    </header>

    <footer>
        {{ $settings->company_name }} • {{ $settings->company_address }} <br>
        Este documento certifica el estado técnico del equipo en el momento de la inspección.
    </footer>

    <!-- FICHA TÉCNICA -->
    <table class="tech-grid">
        <tr>
            <td style="background-color: #fffbeb;">
                <span class="label">Propietario</span>
                <span class="value">{{ $order->asset->client->name }}</span>
                <div style="font-size: 9px; margin-top: 2px;">ID: {{ $order->asset->client->tax_id ?? 'N/A' }}</div>
            </td>
            <td>
                <span class="label">Equipo</span>
                <span class="value">{{ $order->asset->brand }} {{ $order->asset->model }}</span>
            </td>
            <td>
                <span class="label">Serial / Placa</span>
                <span class="value" style="font-family: monospace;">{{ $order->asset->serial_number }}</span>
            </td>
        </tr>
    </table>

    <!-- 1. INSPECCIÓN VISUAL -->
    @if(!empty($order->technicalReport->checklist))
    <div class="section-head">1. Inspección de Componentes</div>
    <table class="checklist-table">
        @foreach(array_chunk($order->technicalReport->checklist, 2, true) as $chunk)
            <tr>
                @foreach($chunk as $key => $val)
                    <td width="35%" style="font-weight: 600; color: #334155;">{{ $key }}</td>
                    <td width="15%" align="right">
                        @if($val == 'ok') <span class="status-ok">✓ OK</span>
                        @elseif($val == 'falla') <span class="status-fail">✕ FALLA</span>
                        @else <span class="status-na">N/A</span> @endif
                    </td>
                @endforeach
                @if(count($chunk) < 2) <td colspan="2"></td> @endif
            </tr>
        @endforeach
    </table>
    @endif

    <!-- 2. DIAGNÓSTICO -->
    <div class="section-head">2. Hallazgos y Diagnóstico</div>
    <div class="text-block">
        @if(!empty($order->technicalReport->findings))
            {!! nl2br(e($order->technicalReport->findings)) !!}
        @else
            <span style="color: #94a3b8; font-style: italic;">Sin observaciones registradas.</span>
        @endif
    </div>

    <!-- 3. SOLUCIÓN -->
    <div class="section-head">3. Procedimiento Realizado</div>
    <div class="text-block">
        @if(!empty($order->technicalReport->recommendations))
            {!! nl2br(e($order->technicalReport->recommendations)) !!}
        @else
            <span style="color: #94a3b8; font-style: italic;">En proceso de evaluación.</span>
        @endif
    </div>

    <!-- 4. EVIDENCIA FOTOGRÁFICA -->
    @if(!empty($order->technicalReport->photos))
        <div class="section-head" style="page-break-before: auto;">4. Evidencia Fotográfica</div>
        
        <table class="photos-table">
            @foreach(array_chunk($order->technicalReport->photos, 2) as $row)
                <tr>
                    @foreach($row as $index => $photo)
                        <td class="photo-cell">
                            <img src="{{ public_path('storage/' . $photo) }}" class="photo-img">
                            <div class="photo-caption">Evidencia {{ $loop->parent->index * 2 + $loop->index + 1 }}</div>
                        </td>
                    @endforeach
                    {{-- Rellenar celda si es impar --}}
                    @if(count($row) < 2) <td></td> @endif
                </tr>
            @endforeach
        </table>
    @endif

    <!-- FIRMA -->
    <div class="signature-line"></div>
    <div class="signature-text">Técnico Responsable</div>

</body>
</html>
