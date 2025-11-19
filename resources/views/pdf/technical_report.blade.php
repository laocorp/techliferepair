<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Informe Técnico #{{ str_pad($order->id, 4, '0', STR_PAD_LEFT) }}</title>
    <style>
        /* --- CONFIGURACIÓN GENERAL --- */
        @page { margin: 0; }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 11px;
            color: #1f2937;
            line-height: 1.3;
            margin-top: 3cm; /* Espacio para header */
            margin-bottom: 2cm; /* Espacio para footer */
            margin-left: 1cm;
            margin-right: 1cm;
            background-color: #fff;
        }

        /* --- HEADER FIJO --- */
        header {
            position: fixed;
            top: 0cm; left: 0cm; right: 0cm; height: 2.5cm;
            padding: 0.5cm 1cm;
            border-bottom: 3px solid #f59e0b; /* Naranja Industrial */
            background-color: #fffbeb; /* Fondo crema muy suave */
        }

        /* --- FOOTER FIJO --- */
        footer {
            position: fixed; bottom: 0cm; left: 0cm; right: 0cm; height: 1.5cm;
            text-align: center; font-size: 9px; color: #64748b;
            border-top: 1px solid #e2e8f0; padding-top: 10px;
            background-color: #fff;
        }

        /* --- UTILIDADES --- */
        .w-100 { width: 100%; }
        .w-50 { width: 50%; }
        .text-right { text-align: right; }
        .text-bold { font-weight: bold; }
        .uppercase { text-transform: uppercase; }
        .text-warning { color: #d97706; } /* Color de acento técnico */
        
        /* --- TABLA DE ENCABEZADO --- */
        .header-table { width: 100%; border-collapse: collapse; }
        .logo { font-size: 22px; font-weight: 900; letter-spacing: -1px; text-transform: uppercase; color: #0f172a; }
        .sub-logo { font-size: 9px; color: #64748b; margin-top: 2px; letter-spacing: 1px; }

        /* --- SECCIÓN DE DATOS (FICHA TÉCNICA) --- */
        .info-box {
            width: 100%;
            border: 1px solid #cbd5e1;
            border-radius: 4px;
            margin-bottom: 20px;
            overflow: hidden; /* Para que el borde radius funcione con background */
        }
        .info-header {
            background-color: #f1f5f9;
            padding: 5px 10px;
            border-bottom: 1px solid #cbd5e1;
            font-size: 9px;
            font-weight: bold;
            color: #475569;
            text-transform: uppercase;
        }
        .info-body { padding: 10px; }
        
        .data-row { margin-bottom: 4px; }
        .data-label { color: #64748b; font-size: 9px; font-weight: bold; text-transform: uppercase; width: 80px; display: inline-block; }
        .data-value { color: #0f172a; font-weight: bold; font-size: 11px; }

        /* --- TÍTULOS DE SECCIÓN --- */
        .section-title {
            font-size: 11px;
            font-weight: bold;
            color: #b45309; /* Naranja oscuro */
            text-transform: uppercase;
            margin-top: 20px;
            margin-bottom: 8px;
            border-bottom: 1px solid #fcd34d;
            padding-bottom: 3px;
            letter-spacing: 0.5px;
        }

        /* --- TABLA CHECKLIST --- */
        .checklist-table { width: 100%; border-collapse: collapse; font-size: 10px; }
        .checklist-table td { 
            border: 1px solid #e2e8f0; 
            padding: 6px 8px; 
            vertical-align: middle;
        }
        .checklist-item { font-weight: bold; color: #334155; }
        
        /* Badges de estado */
        .badge { padding: 2px 6px; border-radius: 4px; font-size: 9px; font-weight: bold; display: inline-block; }
        .badge-ok { background: #dcfce7; color: #166534; border: 1px solid #bbf7d0; }
        .badge-fail { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }
        .badge-na { background: #f1f5f9; color: #64748b; }

        /* --- CAJAS DE TEXTO (Diagnóstico) --- */
        .text-content {
            background-color: #fff;
            border: 1px solid #e2e8f0;
            padding: 10px;
            border-radius: 4px;
            font-size: 11px;
            color: #334151;
            min-height: 40px;
            text-align: justify;
        }

        /* --- GALERÍA DE FOTOS --- */
        .photos-table { width: 100%; border-collapse: separate; border-spacing: 5px; margin-top: 5px; }
        .photo-cell { 
            width: 33%; 
            background: #fff; 
            border: 1px solid #e2e8f0; 
            padding: 5px; 
            text-align: center; 
            vertical-align: top;
            border-radius: 4px;
        }
        .photo-img { 
            width: 100%; 
            height: 120px; 
            object-fit: contain; 
            background-color: #f8fafc;
            margin-bottom: 5px;
        }
        .photo-label { font-size: 8px; color: #64748b; text-transform: uppercase; }

        /* --- FIRMAS --- */
        .signatures { margin-top: 50px; width: 100%; border-collapse: collapse; }
        .sign-box { width: 40%; margin: 0 auto; text-align: center; }
        .sign-line { border-top: 1px solid #334155; margin-bottom: 5px; }
        .sign-text { font-size: 9px; font-weight: bold; color: #334155; }
    </style>
</head>
<body>

    <header>
        <table class="header-table">
            <tr>
                <td style="vertical-align: top; width: 60%;">
                    <div class="logo">{{ $settings->company_name ?? 'SERVIMAQUINAS' }}</div>
                    <div class="sub-logo">DEPARTAMENTO DE INGENIERÍA Y MANTENIMIENTO</div>
                    <div style="font-size: 9px; color: #64748b; margin-top: 5px;">
                        {{ $settings->company_address }} <br>
                        {{ $settings->company_phone }} | {{ $settings->company_email }}
                    </div>
                </td>
                <td class="text-right" style="vertical-align: top; width: 40%;">
                    <div style="color: #d97706; font-weight: bold; text-transform: uppercase; font-size: 14px; letter-spacing: 1px;">INFORME TÉCNICO</div>
                    <div style="font-size: 24px; font-weight: 900; color: #0f172a;">#{{ str_pad($order->id, 4, '0', STR_PAD_LEFT) }}</div>
                    <div style="font-size: 10px; color: #64748b; margin-top: 2px;">Emisión: {{ date('d/m/Y H:i') }}</div>
                </td>
            </tr>
        </table>
    </header>

    <footer>
        {{ $settings->company_name }} - Este documento es un reporte técnico del estado del equipo. No representa una factura fiscal.
    </footer>

    <table style="width: 100%; border-collapse: separate; border-spacing: 0 0;">
        <tr>
            <td style="width: 49%; vertical-align: top; padding-right: 1%;">
                <div class="info-box">
                    <div class="info-header">Propietario / Cliente</div>
                    <div class="info-body">
                        <div class="data-row">
                            <span class="data-value" style="font-size: 13px;">{{ $order->asset->client->name }}</span>
                        </div>
                        <div class="data-row">
                            <span class="data-label">ID/RUC:</span>
                            <span class="data-value">{{ $order->asset->client->tax_id ?? '---' }}</span>
                        </div>
                        <div class="data-row">
                            <span class="data-label">Contacto:</span>
                            <span class="data-value">{{ $order->asset->client->phone ?? '---' }}</span>
                        </div>
                    </div>
                </div>
            </td>
            
            <td style="width: 49%; vertical-align: top; padding-left: 1%;">
                <div class="info-box">
                    <div class="info-header">Especificaciones del Activo</div>
                    <div class="info-body">
                        <div class="data-row">
                            <span class="data-value" style="font-size: 13px;">{{ $order->asset->brand }} {{ $order->asset->model }}</span>
                        </div>
                        <div class="data-row">
                            <span class="data-label">Serie/Placa:</span>
                            <span class="data-value" style="background: #f1f5f9; padding: 0 4px; border-radius: 2px;">{{ $order->asset->serial_number }}</span>
                        </div>
                        <div class="data-row">
                            <span class="data-label">Tipo:</span>
                            <span class="data-value">{{ $order->asset->type ?? 'Maquinaria' }}</span>
                        </div>
                    </div>
                </div>
            </td>
        </tr>
    </table>

    @if(!empty($order->technicalReport->checklist))
        <div class="section-title">1. Inspección Visual y Eléctrica (Checklist)</div>
        <table class="checklist-table">
            @foreach(array_chunk($order->technicalReport->checklist, 2, true) as $chunk)
                <tr>
                    @foreach($chunk as $key => $val)
                        <td width="35%" class="checklist-item">{{ $key }}</td>
                        <td width="15%" style="text-align: center;">
                            @if($val == 'ok') 
                                <span class="badge badge-ok">✓ OPERATIVO</span>
                            @elseif($val == 'falla') 
                                <span class="badge badge-fail">✕ FALLA</span>
                            @elseif($val == 'dañado') 
                                <span class="badge badge-fail">! DAÑADO</span>
                            @else 
                                <span class="badge badge-na">NO APLICA</span> 
                            @endif
                        </td>
                    @endforeach
                    {{-- Rellenar si es impar --}}
                    @if(count($chunk) < 2) <td colspan="2" style="border: none;"></td> @endif
                </tr>
            @endforeach
        </table>
    @endif

    <div class="section-title">2. Hallazgos Técnicos y Diagnóstico</div>
    <div class="text-content">
        {!! nl2br(e($order->technicalReport->findings)) !!}
    </div>

    <div class="section-title">3. Trabajo Realizado y Recomendaciones</div>
    <div class="text-content">
        {!! nl2br(e($order->technicalReport->recommendations)) !!}
    </div>

    @if(!empty($order->technicalReport->photos))
        <div class="section-title" style="page-break-before: auto;">4. Registro Fotográfico</div>
        <table class="photos-table">
            <tr>
                @foreach($order->technicalReport->photos as $index => $photo)
                    <td class="photo-cell">
                        <img src="{{ public_path('storage/' . $photo) }}" class="photo-img">
                        <div class="photo-label">Evidencia #{{ $index + 1 }}</div>
                    </td>
                    
                    {{-- Romper fila cada 3 fotos --}}
                    @if(($index + 1) % 3 == 0) 
                        </tr><tr> 
                    @endif
                @endforeach
                
                {{-- Rellenar celdas vacías para mantener estructura --}}
                @php $remaining = 3 - (count($order->technicalReport->photos) % 3); @endphp
                @if($remaining < 3)
                    @for($i = 0; $i < $remaining; $i++)
                        <td style="border:none;"></td>
                    @endfor
                @endif
            </tr>
        </table>
    @endif

    <table class="signatures">
        <tr>
            <td>
                <div class="sign-box">
                    <div class="sign-line"></div>
                    <div class="sign-text">Firma del Técnico Responsable</div>
                </div>
            </td>
            <td>
                <div class="sign-box">
                    <div class="sign-line"></div>
                    <div class="sign-text">Conformidad del Cliente</div>
                </div>
            </td>
        </tr>
    </table>

</body>
</html>
