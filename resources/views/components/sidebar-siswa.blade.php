{{-- 
    DOCUMENTATION: SIDEBAR SISWA (REVISI SILVER GLOW & SCROLL LOGOUT)
--}}

<aside x-show="sidebarOpen" x-cloak
       x-transition:enter="transition ease-out duration-500" 
       x-transition:enter-start="translate-x-full" 
       x-transition:enter-end="translate-x-0" 
       x-transition:leave="transition ease-in duration-400"
       x-transition:leave-start="translate-x-0"
       x-transition:leave-end="translate-x-full"
       class="absolute right-0 top-0 h-full w-80 md:w-[340px] bg-white dark:bg-slate-900 shadow-[0_0_50px_rgba(0,0,0,0.2)] border-l-2 border-slate-100 dark:border-slate-800 pointer-events-auto flex flex-col z-[110]">
    
    <style>
        .profile-float { animation: float 4s ease-in-out infinite; }
        @keyframes float { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-5px); } }
        .profile-ring { animation: spin-slow 12s linear infinite; }
        @keyframes spin-slow { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }

        /* --- 1. CHUNKY SILVER GLOW CARD --- */
        .sb-card {
            border-radius: 1.5rem;
            border: 2px solid #f1f5f9;
            border-bottom: 6px solid #cbd5e1;
            transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            position: relative;
            padding: 0.875rem 1.25rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: #ffffff;
        }

        .dark .sb-card { 
            background: #1e293b; 
            border-color: #334155; 
            border-bottom-color: #020617; 
        }
        
        .sb-card:active { transform: translateY(4px) scale(0.98); border-bottom-width: 2px; }

        /* --- 2. EFEK SILVER MENYALA (GLOW) --- */
        .menu-silver:hover { 
            border-color: #cbd5e1; 
            border-bottom-color: #94a3b8; 
            background: #f8fafc; 
            /* Efek Cahaya Silver */
            box-shadow: 0 15px 30px -5px rgba(148, 163, 184, 0.4); 
            transform: translateY(-5px); 
        }

        .dark .menu-silver:hover { 
            background: #2d3a4f !important; 
            border-color: #64748b;
            border-bottom-color: #1e293b;
            box-shadow: 0 15px 30px -5px rgba(0, 0, 0, 0.6);
        }

        /* Khusus Logout (Tetap Red Alert) */
        .menu-red:hover { 
            border-color: #fecaca; 
            border-bottom-color: #ef4444; 
            background: #fef2f2; 
            box-shadow: 0 15px 30px -5px rgba(239, 68, 68, 0.3);
            transform: translateY(-5px); 
        }
        .dark .menu-red:hover { background: rgba(239, 68, 68, 0.1) !important; }

        .font-game { font-family: 'Bangers', cursive; }
    </style>

    @php
        $user = Auth::user();
        $userColor = $user->profile_color ?? '3b82f6';
        $rankStatus = $user->rank_status;
        $allUsers = \App\Models\User::where('role', '!=', 'admin')->get()->sortByDesc('total_xp')->values();
        $globalRankIndex = $allUsers->search(fn($u) => $u->id === $user->id);
        $globalRank = $globalRankIndex !== false ? $globalRankIndex + 1 : '-';
        $todayTicket = \App\Models\RetryTicket::where('user_id', $user->id)->where('date', now()->toDateString())->first();
        $remainingTickets = 3 - ($todayTicket ? $todayTicket->used_count : 0);
        $userClasses = $user->classrooms ?? collect();
        $firstClass = $userClasses->first();
    @endphp

    {{-- KONTENER UTAMA (SCROLLABLE) --}}
    <div class="flex-1 overflow-y-auto scrollbar-hide p-5 md:p-6">
        
        {{-- Header Section: Close & Ticket --}}
        <div class="flex items-center justify-between mb-8">
            <button @click="sidebarOpen = false" 
                    class="w-10 h-10 flex items-center justify-center rounded-xl btn-pegas bg-white dark:bg-slate-800 border-2 border-slate-100 dark:border-slate-700 shadow-sm text-slate-400 hover:text-red-500 transition-all">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3.5">
                    <path d="M15 19l-7-7 7-7"/>
                </svg>
            </button>

            {{-- Info Tiket (Efek Pegas Tanpa Navigasi) --}}
            <div class="sb-card menu-silver cursor-default !py-2 !px-4">
                <div class="flex items-center space-x-2.5">
                    <img src="{{ asset('images/tiket.png') }}" class="w-6 h-6 object-contain drop-shadow-sm">
                    <div class="flex flex-col leading-none">
                        <span class="text-[7px] font-black text-slate-400 uppercase tracking-widest">Retry Available</span>
                        <span class="text-[11px] font-black text-slate-600 dark:text-slate-300 font-game mt-0.5">{{ $remainingTickets }} Sisa</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Profil Avatar --}}
        <div class="text-center group mb-10">
            <div class="relative w-24 h-24 mx-auto mb-4 profile-float">
                <div class="absolute inset-0 rounded-[2.2rem] border-[3px] border-dashed opacity-40 profile-ring" style="border-color: #{{ $userColor }};"></div>
                <div class="absolute inset-1.5 rounded-[2rem] flex items-center justify-center shadow-xl overflow-hidden border-4 border-white dark:border-slate-800" style="background: #{{ $userColor }};">
                    <img src="https://api.dicebear.com/9.x/bottts/svg?seed={{ $user->avatar ?? 'Felix' }}&backgroundColor=transparent" class="w-full h-full object-cover transform scale-110 pt-2 transition-transform duration-300 group-hover:scale-125">
                </div>
                <a href="{{ route('profile.edit') }}" class="absolute -bottom-2 -right-2 p-2 bg-white dark:bg-slate-800 rounded-full shadow-lg border-2 border-slate-100 dark:border-slate-700 hover:text-blue-500 transition-all z-10">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3"><path d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                </a>
            </div>
            <p class="text-xl font-black text-slate-800 dark:text-white leading-tight capitalize px-2">{{ $user->name }}</p>
        </div>

        {{-- MAIN MENU LIST (SILVER THEME) --}}
        <div class="flex flex-col space-y-4">
            
            {{-- Akumulasi XP (Link ke Misi) --}}
            <a href="{{ route('misi.index') }}" class="sb-card menu-silver group">
                <div class="flex items-center space-x-3.5">
                    <div class="w-11 h-11 rounded-xl bg-slate-100 dark:bg-slate-800/50 flex items-center justify-center shrink-0">
                        <img src="{{ asset('images/xp.png') }}" class="w-7 h-7 object-contain">
                    </div>
                    <div class="text-left">
                        <p class="text-[9px] font-black text-slate-400 uppercase mb-0.5">Learning Power</p>
                        <p class="text-[11px] font-bold text-slate-700 dark:text-slate-300">Akumulasi XP</p>
                    </div>
                </div>
                <span class="text-base font-black text-slate-600 dark:text-slate-300">{{ number_format($user->total_xp) }}</span>
            </a>

            {{-- Peringkat Global (Link ke Peringkat) --}}
            <a href="{{ route('peringkat.show') }}" class="sb-card menu-silver group">
                <div class="flex items-center space-x-3.5">
                    <div class="w-11 h-11 rounded-xl bg-slate-100 dark:bg-slate-800/50 flex items-center justify-center shrink-0">
                        <img src="{{ asset('images/peringkat.png') }}" class="w-7 h-7 object-contain">
                    </div>
                    <div class="text-left">
                        <p class="text-[9px] font-black text-slate-400 uppercase mb-0.5">Global Rank</p>
                        <p class="text-[11px] font-bold text-slate-700 dark:text-slate-300">Peringkat Server</p>
                    </div>
                </div>
                <span class="text-base font-black text-slate-600 dark:text-slate-300">#{{ $globalRank }}</span>
            </a>

            {{-- Title Badge (Show only) --}}
            <div class="sb-card menu-silver group cursor-default">
                <div class="flex items-center space-x-3.5">
                    <div class="w-11 h-11 rounded-xl bg-slate-100 dark:bg-slate-800/50 flex items-center justify-center shrink-0">
                        <img src="{{ asset('images/' . $rankStatus['medal']) }}" class="w-9 h-9 object-contain">
                    </div>
                    <div class="text-left">
                        <p class="text-[9px] font-black text-slate-400 uppercase mb-0.5">Current Title</p>
                        <p class="text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                            {{ $rankStatus['title'] }}
                        </p>
                    </div>
                </div>
            </div>

            {{-- Squad Kelas (Link ke Kelas) --}}
            <a href="{{ route('kelas.index') }}" class="sb-card menu-silver group">
                <div class="flex items-center space-x-3.5 overflow-hidden">
                    <div class="w-11 h-11 rounded-xl bg-slate-100 dark:bg-slate-800/50 flex items-center justify-center shrink-0">
                        <img src="{{ asset('images/squad.png') }}" class="w-7 h-7 object-contain">
                    </div>
                    <div class="text-left flex-1 min-w-0">
                        <p class="text-[9px] font-black text-slate-400 uppercase mb-0.5">Squad Terdaftar</p>
                        <p class="text-[11px] font-bold text-slate-700 dark:text-slate-300 truncate">{{ $firstClass ? $firstClass->name : 'Belum Bergabung' }}</p>
                    </div>
                </div>
                <svg class="w-4 h-4 text-slate-300 group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path d="M9 5l7 7-7 7"/></svg>
            </a>

            <div class="h-4"></div> {{-- Spacer --}}

            {{-- LOGOUT BUTTON (IKUT SCROLL) --}}
            <form method="POST" action="{{ route('logout') }}" class="pb-8">
                @csrf
                <button type="submit" class="sb-card menu-red w-full flex items-center justify-center space-x-3 group">
                    <svg class="w-5 h-5 text-red-500 group-hover:rotate-12 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                    <span class="text-[11px] font-black text-red-600 dark:text-red-400 uppercase tracking-widest">Logout System</span>
                </button>
            </form>

        </div>
    </div>
</aside>