<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="techlife-v5">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'TechLife') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body { font-family: 'Inter', sans-serif; -webkit-font-smoothing: antialiased; }
        
        .input-pro {
            @apply w-full px-4 py-3.5 rounded-lg border border-slate-200 bg-slate-50 text-slate-900 font-medium placeholder-slate-400 focus:ring-2 focus:ring-slate-900 focus:border-transparent focus:bg-white outline-none transition-all shadow-sm;
        }
        
        .btn-pro {
            @apply w-full py-4 rounded-lg bg-slate-900 text-white font-bold hover:bg-slate-800 transition-all shadow-lg shadow-slate-900/20 flex justify-center items-center gap-2 disabled:opacity-70 disabled:cursor-not-allowed tracking-wide text-sm uppercase;
        }
    </style>
</head>
<body class="font-sans text-slate-900 antialiased bg-white">
    {{ $slot }}
</body>
</html>
