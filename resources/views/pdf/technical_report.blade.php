<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Inf. Técnico #{{ str_pad($order->id, 4, '0', STR_PAD_LEFT) }}</title>
    <style>
        @page { margin: 0; }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            margin-top: 3cm; margin-bottom: 2cm; margin-left: 1cm; margin-right: 1cm;
            color: #1f2937; background-color: #fff; font-size: 11px; line-height: 1.3;
        }
        header {
            position: fixed; top: 0cm; left: 0cm; right: 0cm; height: 2.5cm;
            padding: 0.5cm 1cm; border-bottom: 3px solid #f59e0b; /* Naranja Industrial */
            background-color: #fffbeb;
        }
        footer {
            position: fixed; bottom: 0cm; left: 0cm; right: 0cm; height: 1.5cm;
            text-align: center; font-size: 9px; color: #64748b;
            border-top: 1px solid #e2e8f0; padding-top: 10px;
        }
        .header-table { width: 100%; }
        .logo { font-size: 22px; font-weight: 900; text-transform: uppercase; color: #0f172a; }
        .sub-logo { font-size: 10px; color: #64748b; margin-top: 2px; }
        
        .section-title {
            font-size: 10px; font-weight: bold; color: #b45309;
            text-transform: uppercase; margin-bottom: 5px; margin-top: 15px;
            border-bottom: 1px solid #fcd34d; padding-bottom: 2px; letter-spacing: 0.5px;
        }
        
        .info-table { width: 100%; margin-bottom: 15px; border-collapse: collapse; }
        .info-table td { padding: 5px; vertical-align: top; border: 1px solid #e5e7eb; }
        .label { font-weight: bold; color: #64748b; font-size: 9px; text-transform: uppercase; }
        .value { font-weight: bold; font-size: 11px; }

        /* CHECKLIST ESTILO INDUSTRIAL */
        .checklist-table { width: 100%; border-collapse: collapse; font-size: 10px; }
        .checklist-table td { border: 1px solid #e5e7eb; padding: 4px 8px; }
        .checklist-header { background-color: #f9fafb; font-weight: bold; color: #4b5563; }
        
        .text-box { background-color: #f9fafb; border: 1px solid #e5e7eb; padding: 8px; border-radius: 4px; min-height: 40px; font-size: 11px; }

        .photos-grid { width: 100%; margin-top: 10px; }
        .photo-cell { width: 33%; padding: 4px; text-align: center; vertical-align: top; }
        .photo-img { width: 100%; height: 140px; object-fit: contain; border: 1px solid #ccc; background: #f8f8f8; }
    </style>
</head>
<body>

    <header>
        <table class="header-table">
            <tr>
                <td style="vertical-align: top;">
                    <div class="logo">{{ $settings->company_name ?? 'SERVIMAQUINAS' }}</div>
                    <div class="sub-logo">DEPARTAMENTO TÉCNICO Y MANTENIMIENTO</div>
                </td>
                <td class="text-right" style="vertical-align: top;">
                    <div style="color: #d97706; font-weight: bold; text-transform: uppercase; font-size: 12px;">INFORME DE EVALUACIÓN</div>
                    <div style="font-size: 18px; font-weight: bold;">REF: OT-#{{ str_pad($order->id, 4, '0', STR_PAD_LEFT) }}</div>
                    <div style="font-size: 10px; color: #64748b;">Emisión: {{ date('d/m/Y H:i') }}</div>
                </td>
            </tr>
        </table>
    </header>

    <footer>
        {{ $settings->company_name }} - Informe de uso interno y garantía técnica. <br>
        {{ $settings->company_address }} | {{ $settings->company_phone }}
    </footer>

    <table class="info-table">
        <tr>
            <td width="50%" style="background-color: #fffbeb;">
                <div class="label">Propietario / Empresa</div>
                <div class="value">{{ $order->asset->client->name }}</div>
                <div style="font-size: 9px;">RUC/ID: {{ $order->asset->client->tax_id }}</div>
            </td>
            <td width="50%">
                <div class="label">Equipo / Máquina</div>
                <div class="value">{{ $order->asset->brand }} {{ $order->asset->model }}</div>
                <div class="label" style="margin-top: 4px;">Serie / Placa:</div>
                <div class="value">{{ $order->asset->serial_number }}</div>
            </td>
        </tr>
    </table>

    @if(!empty($order->technicalReport->checklist))
    <div class="section-title">1. Inspección Visual y Eléctrica</div>
    <table class="checklist-table">
        @foreach(array_chunk($order->technicalReport->checklist, 2, true) as $chunk)
            <tr>
                @foreach($chunk as $key => $val)
                    <td width="35%" class="checklist-header">{{ $key }}</td>
                    <td width="15%" style="text-align: center;">
                        @if($val == 'ok') <span style="color:green; font-weight:bold;">✓ OK</span>
                        @elseif($val == 'falla') <span style="color:red; font-weight:bold;">X FALLA</span>
                        @elseif($val == 'dañado') <span style="color:#d97706; font-weight:bold;">! DAÑO</span>
                        @else <span style="color:#999; font-size: 9px;">N/A</span> @endif
                    </td>
                @endforeach
                @if(count($chunk) < 2) <td colspan="2"></td> @endif
            </tr>
        @endforeach
    </table>
    @endif

    <div class="section-title">2. Diagnóstico de Falla / Hallazgos</div>
    <div class="text-box">
        {!! nl2br(e($order->technicalReport->findings)) !!}
    </div>

    <div class="section-title">3. Correcciones y Recomendaciones</div>
    <div class="text-box">
        {!! nl2br(e($order->technicalReport->recommendations)) !!}
    </div>

    @if(!empty($order->technicalReport->photos))
        <div class="section-title" style="page-break-before: auto;">4. Registro Fotográfico</div>
        <table class="photos-grid">
            <tr>
                @foreach($order->technicalReport->photos as $index => $photo)
                    <td class="photo-cell">
                        <img src="{{ public_path('storage/' . $photo) }}" class="photo-img">
                        <div style="font-size: 8px; margin-top: 2px; color: #666;">Evidencia {{ $index + 1 }}</div>
                    </td>
                    @if(($index + 1) % 3 == 0) </tr><tr> @endif
                @endforeach
            </tr>
        </table>
    @endif

    <div style="margin-top: 40px; text-align: center;">
        <div style="border-top: 1px solid #ccc; width: 40%; margin: 0 auto; padding-top: 5px; font-size: 10px; font-weight: bold;">
            Técnico Especialista Responsable
        </div>
    </div>

</body>
</html>
