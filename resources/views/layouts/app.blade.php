<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
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
            /* Scrollbar lebih tipis & rapi */
            .custom-scrollbar::-webkit-scrollbar { width: 4px; }
            .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
            .custom-scrollbar::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
            .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #cbd5e1; }
            
            /* Smooth transitions for layout shifts */
            .layout-transition { transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
        </style>
    </head>
    <body class="font-sans antialiased bg-slate-50 text-slate-900 h-full overflow-x-hidden">
        
        {{-- Navigation Component (Admin Sidebar / Student Nav) --}}
        @include('layouts.navigation')

        {{-- 
            MAIN WRAPPER 
            Admin: Kasih margin kiri cuma di desktop (lg:ml-72)
        --}}
        <main class="layout-transition min-h-screen flex flex-col {{ Auth::user()->role === 'admin' ? 'lg:ml-72' : '' }}">
            
            {{-- Slot Header (Jika ada) --}}
            @isset($header)
                <header class="bg-white/80 backdrop-blur-md border-b border-slate-100 sticky top-0 z-30 lg:z-10">
                    <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            {{-- CONTENT AREA --}}
            <div class="flex-1 w-full overflow-x-hidden">
                {{-- Padding dinamis: py-6 di mobile agar tidak terlalu jauh ke bawah --}}
                <div class="py-6 md:py-10 px-4 sm:px-6 lg:px-8">
                    <div class="max-w-7xl mx-auto">
                        {{ $slot }}
                    </div>
                </div>
            </div>

            {{-- Footer Area (Opsional) --}}
            <footer class="mt-auto py-6 px-6 md:px-10 border-t border-slate-100 bg-white/50">
                <div class="max-w-7xl mx-auto flex flex-col md:flex-row items-center justify-between gap-4">
                    {{-- Copyright & Info --}}
                    <div class="flex flex-col items-center md:items-start text-center md:text-left leading-tight">
                        <p class="text-[9px] font-black text-slate-500 uppercase tracking-widest">
                            © 2026 UNNES Informatics Education
                        </p>
                        <p class="text-[8px] font-bold text-slate-400 uppercase mt-1">
                            Virtual Lab Excel Research Project
                        </p>
                    </div>

                    {{-- Versi/Badge --}}
                    <div class="flex items-center space-x-3">
                        <span class="px-2.5 py-1 bg-slate-100 text-slate-500 rounded-lg text-[8px] font-black uppercase tracking-tighter border border-slate-200">
                            Production v1.0
                        </span>
                        <div class="flex items-center space-x-1.5 opacity-50">
                            <div class="w-1 h-1 bg-emerald-500 rounded-full animate-pulse"></div>
                            <span class="text-[8px] font-bold text-slate-400 uppercase">System Ready</span>
                        </div>
                    </div>
                </div>
            </footer>
        </main>

        @stack('scripts')
    </body>
</html>