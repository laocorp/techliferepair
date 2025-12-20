<!DOCTYPE html>
<html>
<head>
    <style>
        @page { margin: 0; size: 50mm 25mm; }
        body { font-family: sans-serif; margin: 0; padding: 2px; text-align: center; }
        .name { font-size: 8px; font-weight: bold; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 100%; }
        .sku { font-size: 8px; margin-top: 1px; letter-spacing: 1px; }
        .price { font-size: 12px; font-weight: 900; margin-top: 1px; }
        .qr { width: 35px; height: 35px; display: block; margin: 1px auto; }
    </style>
</head>
<body>
    <div class="name">{{ Str::limit($part->name, 20) }}</div>
    <!-- QR con el SKU para escaneo rápido -->
    <img class="qr" src="data:image/svg+xml;base64, {{ base64_encode(\SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')->size(35)->margin(0)->generate($part->sku)) }}">
    <div class="sku">{{ $part->sku }}</div>
    <div class="price">${{ number_format($part->price, 2) }}</div>
</body>
</html>
