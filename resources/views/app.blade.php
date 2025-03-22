<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title inertia>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts and Styles -->
        @routes
        <link href="/build/app.css" rel="stylesheet">

        <!-- Inertia -->
        <script src="https://unpkg.com/inertia.js@0.11.1/dist/inertia.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/vue@3.2.47/dist/vue.global.prod.js"></script>
        <script src="https://unpkg.com/@inertiajs/inertia-vue3"></script>
        <script src="/build/app.js" defer></script>

        @inertiaHead
    </head>
    <body class="font-sans antialiased">
        @inertia
    </body>
</html> 