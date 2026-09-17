<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="overflow-x-hidden">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title inertia>{{ config('app.name', 'Laravel') }}</title>

        @php($favicon = \App\Models\Setting::where('key', 'favicon')->value('value'))
        <link rel="icon" href="{{ $favicon ?: asset('favicon.svg') }}?v={{ md5($favicon ?: 'default') }}">
        <link rel="shortcut icon" href="{{ $favicon ?: asset('favicon.svg') }}?v={{ md5($favicon ?: 'default') }}">
        <link rel="apple-touch-icon" href="{{ $favicon ?: asset('favicon.svg') }}?v={{ md5($favicon ?: 'default') }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:ital,wght@0,100..900;1,100..900&family=Outfit:wght@100..900&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @routes
        @vite(['resources/js/app.js', "resources/js/Pages/{$page['component']}.vue"])
        @inertiaHead
    </head>
    <body class="font-sans antialiased overflow-x-hidden">
        @inertia
    </body>
</html>
