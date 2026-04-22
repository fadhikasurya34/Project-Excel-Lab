{{-- 
    LAYOUT: Shell Aplikasi Siswa
--}}

<!DOCTYPE html>
<html lang="id" x-data="{ 
    darkMode: localStorage.getItem('theme') === 'dark', 
    sidebarOpen: false, 
    toggleTheme() { 
        this.darkMode = !this.darkMode; 
        if (this.darkMode) {
            document.documentElement.classList.add('dark');
            localStorage.setItem('theme', 'dark');
        } else {
            document.documentElement.classList.remove('dark');
            localStorage.setItem('theme', 'light');
        }
    } 
}" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>@yield('title') - Virtual Lab</title>

    <script>
        (function() {
            const t = localStorage.getItem('theme');
            if (t === 'dark' || (!t && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        })();
    </script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&family=Bangers&display=swap" rel="stylesheet">
    
    <style>
        [x-cloak] { display: none !important; }

        /* --- 1. BACKGROUND NEUTRAL GAMIFIKASI --- */
        .bg-lab {
            background-color: #f8fafc;
            background-image: 
                radial-gradient(#cbd5e1 1.5px, transparent 1.5px), 
                linear-gradient(#e2e8f0 1px, transparent 1px), 
                linear-gradient(90deg, #e2e8f0 1px, transparent 1px);
            background-size: 24px 24px, 120px 120px, 120px 120px;
            transition: background-color 0.5s ease;
        }
        .dark .bg-lab { 
            background-color: #020617;
            background-image: 
                radial-gradient(#1e293b 1px, transparent 1px),
                linear-gradient(#0f172a 1px, transparent 1px), 
                linear-gradient(90deg, #0f172a 1px, transparent 1px);
        }

        /* --- 2. APP SHELL--- */
        .app-wrapper {
            display: flex;
            flex-direction: column;
            height: 100vh;
            overflow: hidden; 
        }

        .main-scroller {
            flex: 1;
            overflow-y: auto; 
            scroll-behavior: smooth;
            -webkit-overflow-scrolling: touch;
        }

        /* --- 3. EFEK TOMBOL PEGAS  --- */
        .btn-pegas { 
            position: relative; 
            transition: all 0.1s ease; 
            border-bottom-width: 6px !important; 
            border-bottom-style: solid;
            border-bottom-color: #cbd5e1 !important; 
        }
        .dark .btn-pegas { 
            border-bottom-color: #334155 !important;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.3);
        }
        .btn-pegas:active { 
            transform: translateY(4px); 
            border-bottom-width: 2px !important; 
            box-shadow: none !important;
        }

        .font-game { font-family: 'Bangers', cursive; }
        
        /* Hide Scrollbar Utility */
        .scrollbar-hide::-webkit-scrollbar { width: 0; display: none; }
        .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
    </style>

    @stack('styles')
</head>
<body class="h-full font-sans antialiased bg-lab text-slate-800 dark:text-slate-100 overflow-hidden">

    <div class="app-wrapper">
        
        {{-- HEADER --}}
        <nav class="shrink-0 h-[72px] flex items-center justify-between px-4 md:px-8 bg-white/80 dark:bg-slate-900/80 backdrop-blur-xl border-b-2 border-slate-200/50 dark:border-slate-800/50 z-40">
            <div class="flex items-center">
                
                {{-- LOGO DINAMIS --}}
                <div class="relative {{ request()->routeIs('dashboard') ? 'flex' : 'hidden md:flex' }} items-center justify-center mr-3">
                    <img src="{{ asset('images/excel.png') }}" class="h-10 md:h-12 w-auto block dark:hidden">
                    <img src="{{ asset('images/excel 2.png') }}" class="h-10 md:h-12 w-auto hidden dark:block">
                </div>

                @if(View::hasSection('header_left'))
                    {{-- Border kiri dinamis --}}
                    <div class="flex items-center {{ request()->routeIs('dashboard') ? 'border-l-2' : 'border-l-0 md:border-l-2' }} border-slate-200 dark:border-slate-700 {{ request()->routeIs('dashboard') ? 'pl-4' : 'pl-0 md:pl-4' }}">
                        @yield('header_left')
                    </div>
                @endif
            </div>

            <div class="flex items-center space-x-2 md:space-x-3">
                {{-- 1. Tombol Home --}}
                <a href="{{ route('dashboard') }}" 
                class="hidden md:flex w-10 h-10 md:w-11 md:h-11 items-center justify-center rounded-xl btn-pegas bg-white dark:bg-slate-800 border-2 border-emerald-100 dark:border-emerald-900/30 text-emerald-500 dark:text-emerald-400 hover:bg-emerald-50 dark:hover:bg-emerald-900/20 transition-all">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                </a>

                {{-- 2. Tombol Theme Toggle--}}
                <button @click="toggleTheme()" 
                        class="hidden md:flex w-10 h-10 md:w-11 md:h-11 items-center justify-center rounded-xl btn-pegas bg-white dark:bg-slate-800 border-2 border-slate-100 dark:border-slate-700 transition-colors">
                    <svg x-show="darkMode" x-cloak class="w-5 h-5 text-yellow-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                    <svg x-show="!darkMode" class="w-5 h-5 text-slate-500 dark:text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                    </svg>
                </button>
                
                {{-- 3. Tombol Profile & Sidebar --}}
                <button @click="sidebarOpen = true" 
                        class="flex items-center p-1 bg-white dark:bg-slate-800 border-2 border-slate-100 dark:border-slate-700 rounded-full btn-pegas group shadow-sm">
                    @php $userColor = Auth::user()->profile_color ?? '10b981'; @endphp
                    <div class="w-8 h-8 rounded-full border-2 border-white dark:border-slate-600 overflow-hidden" style="background-color: #{{ $userColor }};">
                        <img src="https://api.dicebear.com/9.x/bottts/svg?seed={{ Auth::user()->avatar ?? 'Felix' }}&backgroundColor=transparent" class="w-full h-full object-cover pt-1 transform scale-110">
                    </div>
                    <svg class="w-4 h-4 text-slate-400 mx-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"/></svg>
                </button>
            </div>
        </nav>

        {{-- MAIN CONTENT SCROLLER --}}
        <main class="main-scroller scrollbar-hide">
            <div class="min-h-full">
                @yield('content')
            </div>

            {{-- FOOTER--}}
            <footer class="bg-white/60 dark:bg-slate-900/60 backdrop-blur-md border-t-2 border-slate-200/50 dark:border-slate-800/50 px-4 md:px-8 py-5 transition-colors duration-500">
                <div class="max-w-7xl mx-auto flex flex-col md:flex-row items-center justify-between gap-4">
                    <div class="flex flex-col items-center md:items-start opacity-70 text-center md:text-left">
                        <span class="text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-[0.2em]">© 2026 Virtual Lab Excel</span>
                        <span class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mt-1">Pendidikan Teknik Informatika dan Komputer UNNES</span>
                        <span class="text-[8px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest mt-1">Fadhlan Surya Ardhika</span>
                    </div>

                    <div class="flex items-center space-x-4">
                        <div class="flex items-center space-x-2 px-3 py-1.5 bg-slate-100 dark:bg-slate-800/50 rounded-xl border border-slate-200 dark:border-slate-700">
                            <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full animate-pulse"></span>
                            <span class="text-[9px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest">Server Online</span>
                        </div>
                        <div class="text-[9px] font-black text-blue-500 bg-blue-50 dark:bg-blue-900/20 px-3 py-1.5 rounded-xl border border-blue-100 dark:border-blue-800 uppercase tracking-[0.1em]">
                            Terminal v1.0
                        </div>
                    </div>
                </div>
            </footer>
        </main>
        {{-- 
            FLOATING NAV: Home & Dark Mode
            Posisi: Kanan bawah
        --}}
        <div class="fixed bottom-6 right-4 flex flex-col space-y-4 z-50 md:hidden">
            
            {{-- 1. Tombol Home --}}
            <a href="{{ route('dashboard') }}" 
            class="w-11 h-11 flex items-center justify-center rounded-xl btn-pegas bg-white dark:bg-slate-800 border-2 border-slate-100 dark:border-slate-700 text-slate-500 dark:text-emerald-400 shadow-xl">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
            </a>

            {{-- 2. Tombol Theme Toggle --}}
            <button @click="toggleTheme()" 
                    class="w-11 h-11 flex items-center justify-center rounded-xl btn-pegas bg-white dark:bg-slate-800 border-2 border-slate-100 dark:border-slate-700 shadow-xl">
                {{-- Ikon Matahari (Muncul pas Dark Mode) --}}
                <svg x-show="darkMode" x-cloak class="w-5 h-5 text-yellow-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
                {{-- Ikon Bulan --}}
                <svg x-show="!darkMode" class="w-5 h-5 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                </svg>
            </button>
        </div>
        {{-- KODE SIDEBAR --}}
        <div class="fixed inset-0 z-[100] pointer-events-none" x-show="sidebarOpen" x-cloak>
            <div @click="sidebarOpen = false" 
                x-transition.opacity 
                class="absolute inset-0 bg-slate-950/60 backdrop-blur-md pointer-events-auto"></div>
            <x-sidebar-siswa />
        </div>
    @stack('scripts')
</body>
</html>