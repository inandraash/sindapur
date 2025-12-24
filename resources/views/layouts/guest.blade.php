<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>
        <link rel="icon" href="{{ asset('images/logo_1.png') }}?v=3" type="image/png">
        <link rel="shortcut icon" href="{{ asset('images/logo_1.png') }}?v=3" type="image/png">
        <link rel="alternate icon" href="{{ asset('favicon.ico') }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gradient-to-br from-indigo-50 via-white to-purple-50">
            <div>
                <a href="/" class="inline-block hover:opacity-80 transition">
                    <img src="{{ asset('images/logo_1.png') }}" alt="Logo" class="w-24 h-24 sm:w-32 sm:h-32 object-contain" />
                </a>
            </div>

            <div class="w-full sm:max-w-md mt-6 mx-4 sm:mx-0 px-6 py-8 bg-white dark:bg-gray-800 shadow-lg sm:rounded-lg rounded-xl border border-gray-100 dark:border-gray-700">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
