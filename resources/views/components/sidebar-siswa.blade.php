{{-- 
    DOCUMENTATION: SIDEBAR SISWA
    LOGIC: Peringkat Global dengan Medali PNG, Tiket Remedial, Peringkat Kelas, & Navigasi.
--}}

<aside x-show="sidebarOpen" x-cloak
       x-transition:enter="transition ease-out duration-500" 
       x-transition:enter-start="translate-x-full" 
       x-transition:enter-end="translate-x-0" 
       x-transition:leave="transition ease-in duration-400"
       x-transition:leave-start="translate-x-0"
       x-transition:leave-end="translate-x-full"
       class="absolute right-0 top-0 h-full w-80 md:w-[340px] bg-white dark:bg-slate-900 shadow-[0_0_40px_rgba(0,0,0,0.15)] border-l-2 border-slate-100 dark:border-slate-800 pointer-events-auto p-5 md:p-6 flex flex-col z-[110]">
    
    <style>
        .profile-float { animation: float 4s ease-in-out infinite; }
        @keyframes float { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-5px); } }
        .profile-ring { animation: spin-slow 12s linear infinite; }
        @keyframes spin-slow { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }

        /* Desain Tombol Bubbly Chunky */
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
            background: white;
        }
        .dark .sb-card { background: #0f172a; border-color: #1e293b; border-bottom-color: #020617; }
        
        .sb-card:active { transform: translateY(4px) scale(0.98); border-bottom-width: 2px; }

        /* Hover States */
        .menu-blue:hover { border-color: #bfdbfe; border-bottom-color: #3b82f6; background: #f8fafc; box-shadow: 0 10px 20px -5px rgba(59, 130, 246, 0.3); transform: translateY(-4px); }
        .menu-amber:hover { border-color: #fde68a; border-bottom-color: #f59e0b; background: #fffdf5; box-shadow: 0 10px 20px -5px rgba(245, 158, 11, 0.3); transform: translateY(-4px); }
        .menu-emerald:hover { border-color: #a7f3d0; border-bottom-color: #10b981; background: #f0fdf4; box-shadow: 0 10px 20px -5px rgba(16, 185, 129, 0.3); transform: translateY(-4px); }
        .menu-purple:hover { border-color: #e9d5ff; border-bottom-color: #a855f7; background: #faf5ff; box-shadow: 0 10px 20px -5px rgba(168, 85, 247, 0.3); transform: translateY(-4px); }
        .menu-red:hover { border-color: #fecaca; border-bottom-color: #ef4444; background: #fef2f2; box-shadow: 0 10px 20px -5px rgba(239, 68, 68, 0.3); transform: translateY(-4px); }

        .dark .menu-blue:hover, .dark .menu-amber:hover, .dark .menu-emerald:hover, .dark .menu-purple:hover, .dark .menu-red:hover 
        { background: rgba(255, 255, 255, 0.02) !important; }

        .font-game { font-family: 'Bangers', cursive; }
    </style>

    @php
        $user = Auth::user();
        $userColor = $user->profile_color ?? '3b82f6';
        
        // Mengambil data rank status dari Accessor di Model
        $rankStatus = $user->rank_status;

        // Logika Angka Ranking Global
        $allUsers = \App\Models\User::where('role', '!=', 'admin')->get()->sortByDesc('total_xp')->values();
        $globalRankIndex = $allUsers->search(fn($u) => $u->id === $user->id);
        $globalRank = $globalRankIndex !== false ? $globalRankIndex + 1 : '-';

        // Tiket Remedial
        $todayTicket = \App\Models\RetryTicket::where('user_id', $user->id)->where('date', now()->toDateString())->first();
        $remainingTickets = 3 - ($todayTicket ? $todayTicket->used_count : 0);

        // Peringkat Kelas
        $userClasses = $user->classrooms ?? collect();
        $firstClass = $userClasses->first();
        $classCount = $userClasses->count();
        
        $classRank = '-';
        if ($firstClass) {
            $classUsers = $firstClass->users->where('role', '!=', 'admin')->sortByDesc('total_xp')->values();
            $classRankIndex = $classUsers->search(fn($u) => $u->id === $user->id);
            $classRank = $classRankIndex !== false ? $classRankIndex + 1 : '-';
        }
    @endphp

    <div class="flex-1 overflow-y-auto scrollbar-hide pb-4 px-1">
        {{-- Header Section: Close & Ticket --}}
        <div class="flex items-center justify-between mb-6">
            <button @click="sidebarOpen = false" 
                    class="w-10 h-10 md:w-11 md:h-11 flex items-center justify-center rounded-xl btn-pegas bg-white dark:bg-slate-800 border-2 border-slate-100 dark:border-slate-700 shadow-sm text-slate-400 hover:text-red-500 transition-all">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3.5">
                    <path d="M15 19l-7-7 7-7"/>
                </svg>
            </button>

            {{-- Tiket Remedial --}}
            <div class="flex items-center space-x-2.5 px-4 py-2 bg-amber-50 dark:bg-amber-950/30 border-2 border-amber-100 dark:border-amber-800 rounded-2xl shadow-sm transition-all hover:scale-105">
            <img src="{{ asset('images/tiket.png') }}" 
                alt="Icon Tiket" 
                class="w-6 h-6 object-contain drop-shadow-sm inline-block">
                <div class="flex flex-col leading-none">
                    <span class="text-[7px] font-black text-amber-600 dark:text-amber-500 uppercase tracking-widest">Retry Available</span>
                    <span class="text-[11px] font-black text-amber-700 dark:text-amber-400 font-game mt-0.5">{{ $remainingTickets }} Sisa</span>
                </div>
            </div>
        </div>

        {{-- Profil Avatar Section --}}
        <div class="text-center group mb-8 mt-2">
            <div class="relative w-24 h-24 mx-auto mb-4 profile-float">
                <div class="absolute inset-0 rounded-[2.2rem] border-[3px] border-dashed opacity-50 profile-ring" style="border-color: #{{ $userColor }};"></div>
                <div class="absolute inset-1.5 rounded-[2rem] flex items-center justify-center text-white shadow-xl overflow-hidden border-4 border-white dark:border-slate-800" style="background: #{{ $userColor }};">
                    <img src="https://api.dicebear.com/9.x/bottts/svg?seed={{ $user->avatar ?? 'Felix' }}&backgroundColor=transparent" class="w-full h-full object-cover transform scale-110 pt-2 transition-transform duration-300 group-hover:scale-125">
                </div>
                <a href="{{ route('profile.edit') }}" class="absolute -bottom-2 -right-2 p-2.5 bg-white dark:bg-slate-800 rounded-full shadow-md border-2 border-slate-200 dark:border-slate-700 hover:text-blue-500 active:scale-90 transition-all flex items-center justify-center z-10">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3"><path d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                </a>
            </div>
            <h2 class="text-[9px] font-black tracking-[0.2em] uppercase mb-1 leading-none text-center opacity-70" style="color: #{{ $userColor }};">Sesi Aktif</h2>
            <p class="text-xl font-black text-slate-800 dark:text-white leading-tight capitalize truncate px-2">{{ $user->name }}</p>
        </div>

        <div class="flex flex-col space-y-4">
            {{-- Akumulasi XP --}}
            <a href="{{ route('misi.index') }}" class="sb-card menu-blue group">
                <div class="flex items-center space-x-3.5">
                    <div class="w-11 h-11 rounded-xl bg-blue-50 dark:bg-blue-900/40 flex items-center justify-center shrink-0 group-hover:scale-110 transition-transform">
                        <img src="{{ asset('images/XP.png') }}" class="w-7 h-7 object-contain">
                    </div>
                    <div class="text-left">
                        <p class="text-[9px] font-black text-slate-400 uppercase mb-0.5">Learning Power</p>
                        <p class="text-[11px] font-bold text-slate-700 dark:text-slate-300">Akumulasi XP</p>
                    </div>
                </div>
                <span class="text-base font-black text-blue-600 dark:text-blue-400">{{ number_format($user->total_xp) }}</span>
            </a>

            {{-- Global Rank --}}
            <a href="{{ route('peringkat.index') }}" class="sb-card menu-amber group">
                <div class="flex items-center space-x-3.5">
                    <div class="w-11 h-11 rounded-xl bg-amber-50 dark:bg-amber-900/40 flex items-center justify-center shrink-0 group-hover:scale-110 transition-transform">
                        <img src="{{ asset('images/peringkat.png') }}" class="w-7 h-7 object-contain">
                    </div>
                    <div class="text-left">
                        <p class="text-[9px] font-black text-slate-400 uppercase mb-0.5">Global Rank</p>
                        <p class="text-[11px] font-bold text-slate-700 dark:text-slate-300">Peringkat Server</p>
                    </div>
                </div>
                <span class="text-base font-black text-amber-600">#{{ $globalRank }}</span>
            </a>

            {{-- Title Badge (Peringkat Real-time dengan PNG) --}}
            <div class="sb-card menu-purple group cursor-default">
                <div class="flex items-center space-x-3.5">
                    {{-- Medali PNG sesuai logic rank_status --}}
                    <div class="w-11 h-11 rounded-xl bg-purple-50 dark:bg-purple-900/40 flex items-center justify-center shrink-0">
                        <img src="{{ asset('images/' . $rankStatus['medal']) }}" 
                             alt="Rank Medal" 
                             class="w-9 h-9 object-contain drop-shadow-sm">
                    </div>
                    <div class="text-left">
                        <p class="text-[9px] font-black text-slate-400 uppercase mb-0.5">Current Title</p>
                        <p class="text-[10px] font-black text-purple-600 dark:text-purple-400 uppercase tracking-wider">
                            {{ $rankStatus['title'] }}
                        </p>
                    </div>
                </div>
                <div class="w-2 h-2 rounded-full bg-purple-400 animate-ping"></div>
            </div>

            {{-- Squad/Kelas --}}
            <a href="{{ route('kelas.index') }}" class="sb-card menu-purple group">
                <div class="flex items-center space-x-3.5 overflow-hidden">
                    <div class="w-11 h-11 rounded-xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center shrink-0 group-hover:rotate-12 transition-transform">
                        <img src="{{ asset('images/squad.png') }}" class="w-7 h-7 object-contain">
                    </div>
                    <div class="text-left flex-1 min-w-0">
                        <p class="text-[9px] font-black text-slate-400 uppercase mb-0.5">Squad Terdaftar</p>
                        <p class="text-[11px] font-bold text-slate-800 dark:text-white truncate">{{ $firstClass ? $firstClass->name : 'Belum bergabung' }}</p>
                    </div>
                </div>
                <div class="flex items-center">
                    @if($classCount > 1) <span class="text-[9px] font-black text-purple-500 mr-1">+{{ $classCount - 1 }}</span> @endif
                    <svg class="w-4 h-4 text-slate-300 group-hover:text-purple-500 group-hover:translate-x-1 transition-all" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path d="M9 5l7 7-7 7"/></svg>
                </div>
            </a>
        </div>
    </div>

    {{-- Logout Section --}}
    <div class="pt-4 border-t-2 border-slate-100 dark:border-slate-800 bg-white dark:bg-slate-900 shrink-0 mt-2">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="sb-card menu-red w-full flex items-center justify-center space-x-3 group active:scale-95">
                <svg class="w-5 h-5 text-red-500 group-hover:rotate-12 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3.5"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                <span class="text-[11px] font-black text-red-600 dark:text-red-400 uppercase tracking-widest">Logout System</span>
            </button>
        </form>
    </div>
</aside>