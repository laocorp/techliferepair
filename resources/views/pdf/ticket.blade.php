<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Ticket de Venta</title>
    <style>
        @page { margin: 0; }
        body {
            font-family: 'Courier New', monospace;
            font-size: 10px;
            margin: 5px;
            width: 72mm; /* Ancho estándar de ticket */
        }
        .header { text-align: center; margin-bottom: 10px; border-bottom: 1px dashed #000; padding-bottom: 5px; }
        .logo { font-weight: bold; font-size: 14px; text-transform: uppercase; }
        .info { font-size: 9px; }
        
        .items { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        .items th { text-align: left; border-bottom: 1px solid #000; font-size: 9px; }
        .items td { padding: 2px 0; }
        .total-row { border-top: 1px dashed #000; font-weight: bold; font-size: 12px; }
        
        .footer { text-align: center; font-size: 9px; margin-top: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo">{{ $settings->company_name ?? 'TECHLIFE' }}</div>
        <div class="info">{{ $settings->company_address }}</div>
        <div class="info">Tel: {{ $settings->company_phone }}</div>
        <div class="info">Fecha: {{ date('d/m/Y H:i') }}</div>
    </div>

    <table class="items">
        <thead>
            <tr>
                <th>CANT</th>
                <th>DESC</th>
                <th style="text-align: right;">TOTAL</th>
            </tr>
        </thead>
        <tbody>
            @foreach($items as $item)
            <tr>
                <td>{{ $item['quantity'] }}</td>
                <td>{{ $item['name'] }}</td>
                <td style="text-align: right;">${{ number_format($item['price'] * $item['quantity'], 2) }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="3" style="padding-top: 5px;"></td>
            </tr>
            <tr class="total-row">
                <td colspan="2">TOTAL:</td>
                <td style="text-align: right;">${{ number_format($total, 2) }}</td>
            </tr>
        </tfoot>
    </table>

    <div class="footer">
        ¡Gracias por su compra!<br>
        Revise su mercadería antes de salir.
    </div>
</body>
</html>


