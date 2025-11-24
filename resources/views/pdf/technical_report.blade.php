<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Informe Técnico #{{ str_pad($order->id, 4, '0', STR_PAD_LEFT) }}</title>
    <style>
        /* RESET Y BASE */
        @page { margin: 0; }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            color: #334155;
            font-size: 11px;
            line-height: 1.3;
            /* MÁRGENES EXACTOS PARA QUE TODO CUADRE */
            margin-top: 3.8cm; 
            margin-bottom: 2cm;
            margin-left: 1.5cm;
            margin-right: 1.5cm;
            background-color: #fff;
        }

        /* HEADER TÉCNICO */
        header {
            position: fixed; top: 0; left: 0; right: 0; height: 2.2cm;
            background-color: #fffbeb; /* Fondo crema ingeniería */
            border-bottom: 3px solid #d97706; /* Naranja fuerte */
            padding: 0.6cm 1.5cm;
        }

        /* FOOTER */
        footer {
            position: fixed; bottom: 0; left: 0; right: 0; height: 1.2cm;
            background-color: #fff; border-top: 1px solid #cbd5e1;
            text-align: center; font-size: 8px; color: #64748b; padding-top: 10px;
        }

        /* TABLAS DE LAYOUT */
        table { width: 100%; border-collapse: collapse; border-spacing: 0; }
        
        /* ENCABEZADOS */
        .company-name { font-size: 16px; font-weight: 900; text-transform: uppercase; color: #0f172a; }
        .doc-title { font-size: 12px; font-weight: bold; color: #d97706; text-transform: uppercase; letter-spacing: 1px; text-align: right; }
        .doc-ref { font-size: 18px; font-weight: 900; color: #0f172a; text-align: right; }

        /* SECCIONES */
        .section-header {
            background-color: #f1f5f9;
            color: #0f172a;
            font-size: 9px;
            font-weight: 900;
            text-transform: uppercase;
            padding: 6px 10px;
            border-left: 4px solid #d97706;
            margin-top: 15px;
            margin-bottom: 10px;
            letter-spacing: 0.5px;
        }

        /* FICHA TÉCNICA */
        .info-table td {
            border: 1px solid #e2e8f0;
            padding: 8px;
            vertical-align: top;
            width: 33.33%; /* 3 Columnas exactas */
        }
        .info-label { font-size: 8px; color: #64748b; font-weight: bold; text-transform: uppercase; display: block; margin-bottom: 2px; }
        .info-value { font-size: 11px; font-weight: bold; color: #0f172a; }

        /* CHECKLIST */
        .checklist-table td { border-bottom: 1px solid #f1f5f9; padding: 5px 0; }
        .check-label { font-weight: 600; color: #334155; font-size: 10px; }
        .status-ok { color: #15803d; font-weight: bold; font-size: 9px; }
        .status-fail { color: #b91c1c; font-weight: bold; font-size: 9px; }

        /* CAJAS DE TEXTO */
        .text-box {
            border: 1px solid #e2e8f0;
            background-color: #fcfcfc;
            padding: 10px;
            border-radius: 2px;
            font-size: 10px;
            text-align: justify;
            min-height: 40px;
        }

        /* --- FOTOS (La parte importante) --- */
        .photos-table { width: 100%; border: none; margin-top: 5px; }
        .photos-table td {
            width: 50%; /* 2 fotos por fila */
            padding: 5px; /* Espacio entre fotos */
            vertical-align: top;
            border: none; /* Sin borde en la celda contenedora */
        }
        .photo-card {
            border: 1px solid #cbd5e1;
            background-color: #fff;
            padding: 4px;
            border-radius: 2px;
        }
        .photo-img {
            width: 100%;
            height: 160px; /* Altura fija para que todas se vean igual */
            object-fit: contain;
            background-color: #f8fafc;
            display: block;
        }
        .photo-label {
            font-size: 8px;
            color: #64748b;
            text-align: center;
            margin-top: 4px;
            font-weight: bold;
            text-transform: uppercase;
            border-top: 1px solid #f1f5f9;
            padding-top: 2px;
        }

        /* FIRMAS */
        .signature-box { border-top: 1px solid #0f172a; width: 80%; margin: 0 auto; padding-top: 5px; }
        .signature-text { font-size: 8px; font-weight: bold; color: #0f172a; text-transform: uppercase; text-align: center; }
    </style>
</head>
<body>

    <header>
        <table>
            <tr>
                <td width="60%">
                    <div class="company-name">{{ $settings->company_name ?? 'TECHLIFE' }}</div>
                    <div style="font-size: 9px; color: #64748b;">DEPARTAMENTO DE INGENIERÍA Y DIAGNÓSTICO</div>
                </td>
                <td width="40%" align="right">
                    <div class="doc-title">Informe Técnico</div>
                    <div class="doc-ref">Ref: #{{ $order->ticket_number ?? str_pad($order->id, 5, '0', STR_PAD_LEFT) }}</div>
                    <div style="font-size: 9px; color: #64748b;">Fecha: {{ date('d/m/Y') }}</div>
                </td>
            </tr>
        </table>
    </header>

    <footer>
        {{ $settings->company_name }} • {{ $settings->company_address }} <br>
        Este documento certifica el estado técnico del equipo en el momento de la inspección.
    </footer>

    <!-- DATOS PRINCIPALES (Tabla de 3 columnas) -->
    <table class="info-table">
        <tr>
            <td style="background-color: #fffbeb;">
                <span class="info-label">Cliente</span>
                <span class="info-value">{{ $order->asset->client->name }}</span>
                <div style="font-size: 9px; margin-top: 2px;">ID: {{ $order->asset->client->tax_id ?? 'N/A' }}</div>
            </td>
            <td>
                <span class="info-label">Equipo</span>
                <span class="info-value">{{ $order->asset->brand }} {{ $order->asset->model }}</span>
            </td>
            <td>
                <span class="info-label">Serial / Placa</span>
                <span class="info-value" style="font-family: monospace;">{{ $order->asset->serial_number }}</span>
            </td>
        </tr>
    </table>

    <!-- 1. INSPECCIÓN -->
    @if(!empty($order->technicalReport->checklist))
    <div class="section-header">1. Inspección Visual y Eléctrica</div>
    <table class="checklist-table">
        @foreach(array_chunk($order->technicalReport->checklist, 2, true) as $chunk)
            <tr>
                @foreach($chunk as $key => $val)
                    <td width="35%" class="check-label">{{ $key }}</td>
                    <td width="15%" align="right">
                        @if($val == 'ok') <span class="status-ok">✓ OK</span>
                        @elseif($val == 'falla') <span class="status-fail">✕ FALLA</span>
                        @elseif($val == 'dañado') <span class="status-fail" style="color: #c2410c;">! DAÑO</span>
                        @else <span style="color: #94a3b8;">N/A</span> @endif
                    </td>
                @endforeach
                {{-- Rellenar celda vacía si es impar --}}
                @if(count($chunk) < 2) <td colspan="2"></td> @endif
            </tr>
        @endforeach
    </table>
    @endif

    <!-- 2. HALLAZGOS -->
    <div class="section-header">2. Diagnóstico de Falla</div>
    <div class="text-box">
        {!! nl2br(e($order->technicalReport->findings)) !!}
    </div>

    <!-- 3. SOLUCIÓN -->
    <div class="section-header">3. Procedimiento Realizado</div>
    <div class="text-box">
        {!! nl2br(e($order->technicalReport->recommendations)) !!}
    </div>

    <!-- 4. REGISTRO FOTOGRÁFICO -->
    @if(!empty($order->technicalReport->photos))
        <div class="section-header" style="page-break-before: auto;">4. Evidencia Fotográfica</div>
        
        <table class="photos-table">
            {{-- Iteramos las fotos en grupos de 2 para hacer filas perfectas --}}
            @foreach(array_chunk($order->technicalReport->photos, 2) as $row)
                <tr>
                    @foreach($row as $index => $photo)
                        <td>
                            <div class="photo-card">
                                <!-- Usamos public_path para asegurar carga local -->
                                <img src="{{ public_path('storage/' . $photo) }}" class="photo-img">
                                <div class="photo-label">Evidencia {{ $loop->parent->index * 2 + $loop->index + 1 }}</div>
                            </div>
                        </td>
                    @endforeach

                    {{-- Si hay un número impar de fotos, rellenamos la celda vacía para mantener la estructura --}}
                    @if(count($row) < 2) <td></td> @endif
                </tr>
            @endforeach
        </table>
    @endif

    <!-- FIRMAS -->
    <table style="width: 100%; margin-top: 40px;">
        <tr>
            <td width="30%"></td>
            <td width="40%" align="center">
                <div class="signature-box"></div>
                <div class="signature-text">Técnico Responsable</div>
            </td>
            <td width="30%"></td>
        </tr>
    </table>

</body>
</html>
