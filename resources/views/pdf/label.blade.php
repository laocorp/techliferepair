<!DOCTYPE html>
<html>
<head>
    <style>
        @page { margin: 0; }
        body { font-family: sans-serif; text-align: center; padding: 3px; }
        .qr { width: 35px; height: 35px; margin: 0 auto; display: block; }
        .id { font-size: 10px; font-weight: bold; margin-top: 2px; }
        .date { font-size: 6px; }
    </style>
</head>
<body>
    <img class="qr" src="data:image/svg+xml;base64, {{ base64_encode(\SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')->size(35)->generate(route('track.status', $order->tracking_token))) }}">
    <div class="id">#{{ $order->ticket_number ?? $order->id }}</div>
    <div class="date">{{ date('d/m/Y') }}</div>
</body>
</html>
