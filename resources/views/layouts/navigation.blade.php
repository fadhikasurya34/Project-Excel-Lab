{{-- 
    COMPONENT: Navigation Hub
    TECH: Alpine.js (openMobile state), Laravel Auth Role System
--}}

<div x-data="{ openMobile: false }">
    {{-- (Section) MOBILE HEADER --}}
    <div class="lg:hidden bg-white border-b border-slate-100 p-4 flex items-center sticky top-0 z-[60]">
        {{-- Burger Menu Toggle: Sekarang di KIRI --}}
        <button @click="openMobile = !openMobile" class="p-2 rounded-xl text-slate-400 hover:bg-slate-50 transition active:scale-95 mr-3">
            <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                <path :class="{'hidden': openMobile, 'inline-flex': !openMobile }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 6h16M4 12h16M4 18h16" />
                <path :class="{'hidden': !openMobile, 'inline-flex': openMobile }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>

        <div class="flex items-center space-x-3">
            <img src="{{ asset('images/excel.png') }}" alt="Logo Excel" class="h-8 w-auto object-contain">
            <span class="font-black text-slate-900 tracking-tight">Virtual Lab</span>
        </div>
    </div>

    @if(Auth::user()->role === 'admin')
        {{-- (Component) ADMIN SIDEBAR --}}
        
        {{-- Backdrop Mobile: Supaya konten di belakang tidak bisa diklik saat menu buka --}}
        <div x-show="openMobile" x-cloak 
             x-transition:opacity
             @click="openMobile = false" 
             class="fixed inset-0 bg-slate-900/60 z-[70] lg:hidden backdrop-blur-sm"></div>

        {{-- Sidebar: FIXED & OVERLAY di Mobile --}}
        <aside :class="openMobile ? 'translate-x-0' : '-translate-x-full'" 
               class="fixed top-0 left-0 w-64 h-full bg-white border-r border-slate-100 z-[80] transform lg:translate-x-0 transition-transform duration-300 ease-in-out shadow-2xl lg:shadow-none flex flex-col">
            
            {{-- Brand Identity --}}
            <div class="p-8 pb-6 shrink-0">
                <div class="flex items-center space-x-3">
                    <div class="relative">
                        <div class="absolute -inset-1 bg-indigo-500/10 blur-lg rounded-full"></div>
                        <img src="{{ asset('images/excel.png') }}" alt="Logo Excel" class="relative h-12 w-auto object-contain">
                    </div>
                    <div>
                        <h1 class="text-base font-black text-slate-900 tracking-tight leading-none">Admin Panel</h1>
                        <p class="text-[9px] text-indigo-500 font-bold tracking-widest uppercase mt-1">Virtual Lab Excel</p>
                    </div>
                </div>
            </div>

            {{-- Navigation Links (Scrollable) --}}
            <nav class="flex-1 px-4 space-y-1.5 overflow-y-auto scrollbar-hide">
                <a href="{{ route('admin.dashboard') }}" 
                class="flex items-center px-4 py-3.5 text-[11px] uppercase tracking-[0.1em] font-bold rounded-xl transition-all {{ request()->routeIs('admin.dashboard') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-100' : 'text-slate-500 hover:bg-slate-50 hover:text-indigo-600' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                    Dashboard
                </a>

                <a href="{{ route('admin.materials.index') }}" 
                class="flex items-center px-4 py-3.5 text-[11px] uppercase tracking-[0.1em] font-bold rounded-xl transition-all {{ request()->routeIs('admin.materials.*') ? 'bg-blue-600 text-white shadow-lg shadow-blue-100' : 'text-slate-500 hover:bg-slate-50 hover:text-blue-600' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                    Manajemen Materi
                </a>

                <a href="{{ route('admin.missions.index') }}" 
                class="flex items-center px-4 py-3.5 text-[11px] uppercase tracking-[0.1em] font-bold rounded-xl transition-all {{ request()->routeIs('admin.missions.*') ? 'bg-emerald-600 text-white shadow-lg shadow-emerald-100' : 'text-slate-500 hover:bg-slate-50 hover:text-emerald-600' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                    Manajemen Misi
                </a>

                <a href="{{ route('admin.users.index') }}" 
                class="flex items-center px-4 py-3.5 text-[11px] uppercase tracking-[0.1em] font-bold rounded-xl transition-all {{ request()->routeIs('admin.users.*') ? 'bg-orange-500 text-white shadow-lg shadow-orange-100' : 'text-slate-500 hover:bg-slate-50 hover:text-orange-600' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197"></path></svg>
                    Monitoring Siswa
                </a>

                <a href="{{ route('admin.classrooms.index') }}" 
                class="flex items-center px-4 py-3.5 text-[11px] uppercase tracking-[0.1em] font-bold rounded-xl transition-all {{ request()->routeIs('admin.classrooms.*') ? 'bg-purple-600 text-white shadow-lg shadow-purple-100' : 'text-slate-500 hover:bg-slate-50 hover:text-purple-600' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                    Manajemen Kelas
                </a>

                <div class="pt-8 pb-3 px-5 text-[9px] font-black text-slate-400 uppercase tracking-[0.25em]">Sistem Pakar</div>

                <a href="{{ route('profile.edit') }}" 
                class="flex items-center px-4 py-3.5 text-[11px] uppercase font-bold rounded-xl transition-all {{ request()->routeIs('profile.edit') ? 'text-indigo-600 bg-indigo-50' : 'text-slate-500 hover:bg-slate-50' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                    Profil Admin
                </a>

                <form method="POST" action="{{ route('logout') }}" class="mt-4 pb-20">
                    @csrf
                    <button type="submit" class="w-full flex items-center px-4 py-3.5 text-[11px] uppercase font-bold text-red-500 hover:bg-red-50 rounded-xl transition-all text-left group">
                        <svg class="w-5 h-5 mr-3 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                        Keluar Panel
                    </button>
                </form>
            </nav>
            
            {{-- Profile Footer --}}
            <div class="shrink-0 p-6 border-t border-slate-50 bg-slate-50/30 backdrop-blur-md">
                <div class="flex items-center">
                    <div class="h-10 w-10 rounded-xl bg-slate-900 flex items-center justify-center text-white font-black text-xs shadow-lg">
                        {{ substr(Auth::user()->name, 0, 1) }}
                    </div>
                    <div class="ml-3 overflow-hidden">
                        <p class="text-[11px] font-black text-slate-900 truncate capitalize">{{ Auth::user()->name }}</p>
                        <p class="text-[9px] font-bold text-slate-400 truncate mt-0.5 lowercase">{{ Auth::user()->email }}</p>
                    </div>
                </div>
            </div>
        </aside>

    @else
        {{-- STUDENT TOP NAVIGATION (TETAP SEPERTI ASLINYA) --}}
        <nav class="bg-white border-b border-slate-100 w-full fixed top-0 z-50 shadow-sm backdrop-blur-lg bg-white/90">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-20">
                    <div class="flex items-center">
                        <div class="shrink-0 flex items-center">
                            <a href="{{ route('dashboard') }}" class="flex items-center space-x-3 group">
                                <img src="{{ asset('images/excel.png') }}" alt="Logo Excel" class="h-10 w-auto object-contain transition-transform group-hover:scale-110">
                                <span class="font-black text-xl text-slate-900 tracking-tighter">Virtual Lab</span>
                            </a>
                        </div>
                    </div>
                    {{-- User Dropdown dst... (tetap sesuai kodemu) --}}
                </div>
            </div>
        </nav>
    @endif
</div>