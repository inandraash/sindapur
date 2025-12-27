<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data="{ darkMode: localStorage.getItem('darkMode') === 'true' }"
      x-init="$watch('darkMode', val => localStorage.setItem('darkMode', val))"
      :class="{ 'dark': darkMode }">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>@isset($pageTitle){{ $pageTitle }} - @endisset{{ config('app.name', 'SINDAPUR') }}</title>
        <link rel="icon" href="{{ asset('images/logo_1.png') }}?v=3" type="image/png">
        <link rel="shortcut icon" href="{{ asset('images/logo_1.png') }}?v=3" type="image/png">
        <link rel="alternate icon" href="{{ asset('favicon.ico') }}">
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    </head>
    <body class="font-sans antialiased">
        <div 
            x-data="{ 
                sidebarOpen: (typeof window !== 'undefined' ? window.innerWidth >= 1024 : true),
                isLoading: false,
            }"
            x-init="
                const setLoading = (state) => { isLoading = state; };
                window.addEventListener('beforeunload', () => setLoading(true));
                const attachToForms = () => {
                    document.querySelectorAll('form').forEach(form => {
                        form.addEventListener('submit', () => setLoading(true));
                    });
                };
                attachToForms();
                document.addEventListener('turbo:load', attachToForms);
            "
            class="relative min-h-screen bg-gradient-to-br from-gray-100 via-gray-50 to-white"
        >
            
            @include('layouts.partials._sidebar')


            <div class="flex-1 flex flex-col transition-all duration-300 ease-in-out" :class="{'lg:ml-64': sidebarOpen}">
                

                <header class="bg-white/80 backdrop-blur shadow-sm border-b border-gray-100 sticky top-0 z-10 transition-all duration-300">
                    <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8 flex justify-between items-center">
                        <div class="flex items-center gap-3">
                            <button x-on:click="sidebarOpen = !sidebarOpen" class="text-gray-500 hover:text-indigo-600 hover:scale-110 focus:outline-none mr-1 transition-all duration-200">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7" />
                                </svg>
                            </button>
                            @isset($header)
                                {{ $header }}
                            @endisset
                        </div>
                        
                        @include('layouts.navigation')
                    </div>
                </header>

                <main class="flex-1 overflow-y-auto">
                    <div class="py-10">
                        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                            {{ $slot }}
                        </div>
                    </div>
                </main>
            </div>

            <div 
                x-show="isLoading"
                x-transition.opacity.duration.200ms
                class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/60 backdrop-blur"
                x-cloak
            >
                <div class="flex flex-col items-center gap-3 text-white">
                    <div class="h-12 w-12 border-4 border-white/30 border-t-indigo-500 rounded-full animate-spin"></div>
                    <p class="text-sm font-medium tracking-wide">Memuat...</p>
                </div>
            </div>
        </div>
    </body>
</html>