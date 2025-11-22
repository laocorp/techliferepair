<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="repair-pro">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'TechLife') }}</title>

    <!-- Fuente Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body { font-family: 'Inter', sans-serif; }
        /* Ajuste fino para inputs en la vista guest */
        .input-pro {
            @apply w-full px-4 py-3 rounded-lg border border-slate-200 bg-white text-slate-900 focus:ring-2 focus:ring-slate-900 focus:border-transparent outline-none transition-all placeholder-slate-400;
        }
        .btn-pro {
            @apply w-full py-3.5 rounded-lg bg-slate-900 text-white font-bold hover:bg-slate-800 transition-all shadow-lg shadow-slate-900/10 disabled:opacity-50;
        }
    </style>
</head>
<body class="font-sans text-slate-900 antialiased bg-white">
    {{ $slot }}
</body>
</html>
