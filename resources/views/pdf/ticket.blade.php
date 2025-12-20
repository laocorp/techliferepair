<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Ticket #{{ $sale->id }}</title>
    <style>
        @page { margin: 0; }
        body {
            font-family: 'Courier New', Courier, monospace; /* Fuente monoespaciada clásica de ticket */
            font-size: 10px;
            line-height: 1.2;
            margin: 5px;
            color: #000;
            width: 100%;
        }
        
        /* ENCABEZADO */
        .header { text-align: center; margin-bottom: 10px; }
        .logo { font-weight: bold; font-size: 12px; text-transform: uppercase; display: block; margin-bottom: 4px; }
        .info { display: block; font-size: 9px; }
        
        /* SEPARADORES */
        .divider { border-top: 1px dashed #000; margin: 5px 0; }
        
        /* SECCIÓN CLIENTE */
        .client-info { font-size: 9px; margin-bottom: 5px; }
        .client-row { display: block; }
        .label { font-weight: bold; margin-right: 3px; }

        /* TABLA DE PRODUCTOS */
        .items-table { width: 100%; border-collapse: collapse; margin-top: 5px; }
        .items-table th { 
            text-align: left; 
            border-bottom: 1px dashed #000; 
            font-size: 9px; 
            padding-bottom: 2px; 
        }
        .items-table td { 
            padding: 4px 0; 
            vertical-align: top; 
            font-size: 9px;
        }
        
        /* COLUMNAS */
        .col-qty { width: 10%; text-align: center; vertical-align: top; }
        .col-desc { width: 65%; }
        .col-total { width: 25%; text-align: right; vertical-align: top; }

        /* TOTALES */
        .totals { margin-top: 10px; text-align: right; }
        .total-row { font-size: 12px; font-weight: bold; margin-top: 5px; border-top: 1px dashed #000; padding-top: 5px; display: inline-block; width: 100%; }
        
        /* PIE */
        .footer { text-align: center; font-size: 9px; margin-top: 15px; padding-bottom: 10px; }
    </style>
</head>
<body>

    <!-- 1. DATOS DE LA EMPRESA -->
    <div class="header">
        <div class="logo">{{ $settings->company_name ?? 'TECHLIFE' }}</div>
        <span class="info">{{ $settings->company_address }}</span>
        <span class="info">Tel: {{ $settings->company_phone }}</span>
        
        <div class="divider"></div>
        
        <span class="info">Ticket #: {{ str_pad($sale->id, 6, '0', STR_PAD_LEFT) }}</span>
        <span class="info">Fecha: {{ $sale->created_at->format('d/m/Y H:i') }}</span>
        <span class="info">Cajero: {{ $sale->user->name }}</span>
    </div>

    <div class="divider"></div>

    <!-- 2. DATOS DEL CLIENTE (Dinámico) -->
    <div class="client-info">
        @if($sale->client)
            <div class="client-row"><span class="label">CLIENTE:</span> {{ $sale->client->name }}</div>
            <div class="client-row"><span class="label">ID/RUC:</span> {{ $sale->client->tax_id ?? 'N/A' }}</div>
            @if($sale->client->phone)
                <div class="client-row"><span class="label">TEL:</span> {{ $sale->client->phone }}</div>
            @endif
            @if($sale->client->address)
                <div class="client-row"><span class="label">DIR:</span> {{ Str::limit($sale->client->address, 30) }}</div>
            @endif
        @else
            <div style="text-align: center; font-weight: bold;">CONSUMIDOR FINAL</div>
        @endif
    </div>

    <div class="divider"></div>

    <!-- 3. LISTA DE PRODUCTOS -->
    <table class="items-table">
        <thead>
            <tr>
                <th class="col-qty">CNT</th>
                <th class="col-desc">DESCRIPCION</th>
                <th class="col-total">TOTAL</th>
            </tr>
        </thead>
        <tbody>
            @foreach($sale->items as $item)
            <tr>
                <td class="col-qty">{{ $item->quantity }}</td>
                <td class="col-desc">
                    {{ $item->part->name }}
                    @if($item->part->sku)
                        <br><span style="font-size: 8px;">SKU: {{ $item->part->sku }}</span>
                    @endif
                </td>
                <td class="col-total">
                    ${{ number_format($item->price * $item->quantity, 2) }}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="divider"></div>

    <!-- 4. TOTALES -->
    <div class="totals">
        {{-- Ejemplo de desglose de impuestos (opcional) --}}
        {{-- 
        <div>Subtotal: ${{ number_format($sale->total / (1 + ($settings->tax_rate/100)), 2) }}</div>
        <div>{{ $settings->tax_name }} ({{ $settings->tax_rate }}%): ${{ number_format($sale->total - ($sale->total / (1 + ($settings->tax_rate/100))), 2) }}</div> 
        --}}
        
        <div class="total-row">
            TOTAL: ${{ number_format($sale->total, 2) }}
        </div>
        <div style="font-size: 9px; margin-top: 4px;">
            METODO: {{ strtoupper($sale->payment_method ?? 'Efectivo') }}
        </div>
    </div>

    <!-- 5. PIE DE PÁGINA -->
    <div class="footer">
        ¡Gracias por su compra!<br>
        Revise su mercadería antes de salir.<br>
        No se aceptan devoluciones pasadas 24h.<br>
        Sistema TechLife Enterprise
    </div>

</body>
</html>
