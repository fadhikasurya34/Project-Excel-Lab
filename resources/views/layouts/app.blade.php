<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <style>
            [x-cloak] { display: none !important; }
            .custom-scrollbar::-webkit-scrollbar { width: 6px; }
            .custom-scrollbar::-webkit-scrollbar-track { background: #f1f1f1; }
            .custom-scrollbar::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
            .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #cbd5e1; }
        </style>
    </head>
    <body class="font-sans antialiased bg-gray-100">
        <div class="h-screen flex overflow-hidden">
            
            @include('layouts.navigation')

            <main class="flex-1 overflow-y-auto custom-scrollbar h-full {{ Auth::user()->role === 'admin' ? 'lg:ml-64' : '' }} transition-all duration-300">
                
                @isset($header)
                    <header class="bg-white shadow-sm sticky top-0 z-10">
                        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                            {{ $header }}
                        </div>
                    </header>
                @endisset

                <div class="py-12 px-4 sm:px-6 lg:px-8">
                    <div class="max-w-7xl mx-auto">
                        {{ $slot }}
                    </div>
                </div>
            </main>
        </div>
    </body>
</html>