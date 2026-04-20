{{-- 
    VIEW: Profil Detail Praktikan (Admin)
    DATA: $student, $completedMissions, $completedMaterials
    LOGIC: Manajemen Tab & Live Search aktivitas praktikan via Alpine.js.
--}}

<x-app-layout>
    <style>
        {{-- (Style) Standarisasi UI Terminal Khusus Monitoring (Aksen Orange) --}}
        .bg-admin {
            background-color: #f8fafc;
            background-image: radial-gradient(#e2e8f0 0.8px, transparent 0.8px);
            background-size: 32px 32px;
        }

        .admin-card {
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 1.5rem;
            transition: all 0.3s ease;
        }

        .btn-action {
            width: 2.5rem;
            height: 2.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 0.75rem;
            transition: all 0.2s;
        }

        .text-header { letter-spacing: -0.02em; }
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
        
        [x-cloak] { display: none !important; }
    </style>

    {{-- (Process) Inisialisasi State Tab & Filter Pencarian Aktif --}}
    <div class="min-h-screen bg-admin p-6 sm:p-10" x-data="{ tab: 'misi', search: '' }">
        <div class="max-w-7xl mx-auto relative">
            
            {{-- (Notification) Toast System: Feedback sinkronisasi data --}}
            @if(session('success') || session('error') || session('status'))
                <div x-data="{ show: true, progress: 100 }"
                    x-show="show"
                    x-init="let interval = setInterval(() => { progress -= 1; if(progress <= 0) { show = false; clearInterval(interval); } }, 30);"
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="translate-y-10 opacity-0 scale-90"
                    x-transition:enter-end="translate-y-0 opacity-100 scale-100"
                    x-transition:leave="transition ease-in duration-300"
                    class="fixed bottom-10 right-10 z-[200]">
                    
                    <div class="bg-slate-900 border {{ session('error') ? 'border-red-500/30' : 'border-orange-500/30' }} p-5 rounded-2xl shadow-[0_20px_50px_rgba(0,0,0,0.3)] flex items-center space-x-4 min-w-[320px] overflow-hidden relative">
                        <div class="absolute -top-10 -right-10 w-24 h-24 {{ session('error') ? 'bg-red-600/20' : 'bg-orange-600/20' }} blur-3xl"></div>
                        
                        <div class="flex-shrink-0 w-10 h-10 {{ session('error') ? 'bg-red-600' : 'bg-orange-600' }} rounded-xl flex items-center justify-center shadow-lg">
                            <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                @if(session('error'))
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                @else
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                @endif
                            </svg>
                        </div>

                        <div class="flex-1">
                            <p class="text-[10px] font-black {{ session('error') ? 'text-red-400' : 'text-orange-400' }} uppercase tracking-[0.2em] leading-none mb-1">Account Sync</p>
                            <p class="text-sm font-bold text-white tracking-tight leading-tight">
                                {{ session('success') ?? session('status') ?? session('error') }}
                            </p>
                        </div>

                        <div class="absolute bottom-0 left-0 h-1 {{ session('error') ? 'bg-red-600' : 'bg-orange-600' }} transition-all ease-linear" :style="'width: ' + progress + '%'"></div>
                    </div>
                </div>
            @endif

            {{-- (Section) Header: Navigasi & Search Bar Universal --}}
            <div class="flex flex-col md:flex-row md:items-end justify-between mb-10 gap-6">
                <div>
                    <a href="{{ route('admin.users.index') }}" class="group inline-flex items-center text-orange-600 font-bold text-[10px] tracking-widest uppercase hover:text-orange-700 transition-colors mb-4">
                        <svg class="w-4 h-4 mr-2 transform group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3">
                            <path d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Kembali ke monitoring
                    </a>
                    <h1 class="text-3xl font-extrabold text-slate-900 text-header tracking-tight">Profil <span class="text-orange-600">Praktikan</span></h1>
                    <p class="text-slate-500 font-medium text-sm mt-1">Kelola progres pengerjaan dan detail aktivitas individu.</p>
                </div>

                <div class="relative w-full md:w-80 group">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-300 group-focus-within:text-orange-500 transition-colors">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>
                    <input type="text" x-model="search" placeholder="Cari aktivitas..." 
                           class="w-full pl-11 pr-4 py-3 bg-white border border-slate-200 rounded-2xl focus:ring-4 focus:ring-orange-500/5 focus:border-orange-500 font-bold text-slate-700 shadow-sm transition-all outline-none">
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
                
                {{-- (Section) Kolom Kiri: Ringkasan Identitas --}}
                <div class="lg:col-span-1 space-y-6">
                    <div class="admin-card p-8 flex flex-col items-center">
                        <div class="w-24 h-24 rounded-[2rem] border-4 border-slate-50 shadow-md overflow-hidden mb-6" style="background-color: #{{ $student->profile_color ?? 'f97316' }}">
                            <img src="https://api.dicebear.com/9.x/bottts/svg?seed={{ $student->avatar ?? 'Felix' }}&backgroundColor=transparent" class="w-full h-full pt-2">
                        </div>
                        <h2 class="text-xl font-black text-slate-800 text-center leading-tight capitalize">{{ $student->name }}</h2>
                        <p class="text-slate-400 font-bold text-[10px] uppercase tracking-wider mt-2 mb-8">{{ $student->email }}</p>

                        @php
                            $usedTickets = \App\Models\RetryTicket::where('user_id', $student->id)->where('date', now()->toDateString())->value('used_count') ?? 0;
                            $remainingTickets = max(0, 3 - $usedTickets);
                        @endphp

                        <div class="w-full space-y-3">
                            <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100 flex justify-between items-center">
                                <span class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">Total Power</span>
                                <span class="text-lg font-black text-emerald-600">{{ number_format($student->ranking->total_xp ?? 0) }}</span>
                            </div>
                            <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100 flex justify-between items-center">
                                <span class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">Tiket Sisa</span>
                                <span class="text-lg font-black {{ $remainingTickets > 0 ? 'text-orange-500' : 'text-red-500' }}">{{ $remainingTickets }} / 3</span>
                            </div>
                        </div>
                    </div>

                    {{-- (Action) Navigasi Tab --}}
                    <div class="bg-white rounded-2xl border border-slate-200 p-2 flex flex-col space-y-1 shadow-sm">
                        <button @click="tab = 'misi'" :class="tab === 'misi' ? 'bg-orange-600 text-white shadow-lg' : 'text-slate-500 hover:bg-slate-50'" 
                            class="flex items-center space-x-3 px-5 py-4 rounded-xl transition-all font-bold text-xs uppercase tracking-widest text-left">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                            <span>Misi Selesai</span>
                        </button>
                        <button @click="tab = 'materi'" :class="tab === 'materi' ? 'bg-orange-600 text-white shadow-lg' : 'text-slate-500 hover:bg-slate-50'" 
                            class="flex items-center space-x-3 px-5 py-4 rounded-xl transition-all font-bold text-xs uppercase tracking-widest text-left">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                            <span>Materi Dibaca</span>
                        </button>
                    </div>

                    {{-- (Action) Tombol Administrator --}}
                    <div class="space-y-3 pt-2">
                        <form action="{{ route('admin.users.reset-tickets', $student->id) }}" method="POST" onsubmit="return confirm('Kembalikan 3 tiket remedial untuk siswa ini hari ini?')">
                            @csrf
                            <button type="submit" class="w-full flex items-center justify-between p-4 bg-white border border-slate-200 rounded-xl hover:border-orange-500 hover:shadow-md transition-all group">
                                <div class="flex items-center space-x-3">
                                    <div class="w-8 h-8 bg-orange-50 text-orange-600 rounded-lg flex items-center justify-center text-lg">🎟️</div>
                                    <span class="text-xs font-bold text-slate-700">Pulihkan Tiket</span>
                                </div>
                                <svg class="w-4 h-4 text-slate-400 group-hover:text-orange-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                            </button>
                        </form>

                        <form action="{{ route('admin.users.reset-xp', $student->id) }}" method="POST" onsubmit="return confirm('Aksi ini akan menghapus semua XP dan riwayat misi. Lanjutkan?')">
                            @csrf
                            <button type="submit" class="w-full flex items-center justify-between p-4 bg-white border border-slate-200 rounded-xl hover:border-red-500 transition-all group">
                                <div class="flex items-center space-x-3">
                                    <div class="w-8 h-8 bg-red-50 text-red-600 rounded-lg flex items-center justify-center">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                                    </div>
                                    <span class="text-xs font-bold text-slate-700">Reset Total XP</span>
                                </div>
                                <svg class="w-4 h-4 text-slate-400 group-hover:text-red-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                            </button>
                        </form>
                    </div>
                </div>

                {{-- (Section) Kolom Kanan: Riwayat Aktivitas --}}
                <div class="lg:col-span-3">
                    <div class="admin-card overflow-hidden min-h-[600px] flex flex-col">
                        
                        {{-- Tab Misi --}}
                        <div x-show="tab === 'misi'" x-transition class="flex flex-col h-full">
                            <div class="px-8 py-6 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
                                <h3 class="font-bold text-slate-800 text-sm uppercase tracking-wider">Riwayat Penyelesaian Misi</h3>
                                <span class="px-3 py-1 bg-white border border-slate-200 rounded-full text-[10px] font-black text-slate-400 uppercase tracking-widest">{{ $completedMissions->count() }} Data</span>
                            </div>
                            <div class="p-8 space-y-4 flex-1 overflow-y-auto custom-scrollbar">
                                @forelse($completedMissions as $progress)
                                    <div x-show="search === '' || '{{ strtolower($progress->mission->title) }}'.includes(search.toLowerCase())" 
                                        class="p-5 bg-white border border-slate-100 rounded-2xl flex items-center justify-between hover:border-orange-200 transition-all shadow-sm">
                                        <div class="flex items-center space-x-5 min-w-0">
                                            <div class="w-10 h-10 bg-orange-50 text-orange-600 rounded-xl flex items-center justify-center shrink-0">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            </div>
                                            <div class="truncate">
                                                <p class="font-bold text-slate-800 text-sm truncate capitalize leading-tight">{{ $progress->mission->title }}</p>
                                                <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mt-1">
                                                    {{ $progress->mission->level->category }} • {{ $progress->completion_time->diffForHumans() }}
                                                </p>
                                            </div>
                                        </div>
                                        <div class="flex items-center space-x-4">
                                            <div class="px-4 py-1.5 bg-emerald-50 text-emerald-600 rounded-xl border border-emerald-100 font-black text-xs">
                                                +{{ $progress->score }} XP
                                            </div>
                                            <form action="{{ route('admin.users.destroy-progress', $progress->id) }}" method="POST" class="m-0 flex items-center" onsubmit="return confirm('Hapus riwayat ini?')">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn-action text-slate-300 hover:text-red-500 hover:bg-red-50">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                @empty
                                    <div class="py-24 text-center">
                                        <p class="text-[10px] font-bold text-slate-300 uppercase tracking-[0.2em]">Belum ada misi yang tuntas</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>

                        {{-- Tab Materi --}}
                        <div x-show="tab === 'materi'" x-cloak x-transition class="flex flex-col h-full">
                            <div class="px-8 py-6 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
                                <h3 class="font-bold text-slate-800 text-sm uppercase tracking-wider">Riwayat Eksplorasi Materi</h3>
                                <span class="px-3 py-1 bg-white border border-slate-200 rounded-full text-[10px] font-black text-slate-400 uppercase tracking-widest">{{ $completedMaterials->count() }} Data</span>
                            </div>
                            <div class="p-8 space-y-4 flex-1 overflow-y-auto custom-scrollbar">
                                @forelse($completedMaterials as $mProg)
                                    <div x-show="search === '' || '{{ strtolower($mProg->material->title ?? 'materi') }}'.includes(search.toLowerCase())"
                                        class="p-5 bg-white border border-slate-100 rounded-2xl flex items-center justify-between hover:border-orange-200 transition-all shadow-sm">
                                        <div class="flex items-center space-x-5 min-w-0">
                                            <div class="w-10 h-10 bg-orange-50 text-orange-600 rounded-xl flex items-center justify-center shrink-0">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                                            </div>
                                            <div class="truncate">
                                                <p class="font-bold text-slate-800 text-sm truncate capitalize leading-tight">{{ $mProg->material->title ?? 'Materi Tidak Ditemukan' }}</p>
                                                <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mt-1">
                                                    Selesai dibaca pada {{ $mProg->created_at->format('d M Y') }}
                                                </p>
                                            </div>
                                        </div>
                                        <div class="flex items-center space-x-4">
                                            <form action="{{ route('admin.users.destroy-material-progress', $mProg->id) }}" method="POST" class="m-0 flex items-center" onsubmit="return confirm('Hapus riwayat baca materi ini?')">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn-action text-slate-300 hover:text-red-500 hover:bg-red-50">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                @empty
                                    <div class="py-24 text-center">
                                        <p class="text-[10px] font-bold text-slate-300 uppercase tracking-[0.2em]">Belum ada materi yang dibaca</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>

                {{-- Footer Terminal --}}
                <footer class="mt-20 py-8 border-t border-slate-200 flex flex-col md:flex-row items-center justify-between opacity-50 px-2">
                    <div class="text-left leading-tight">
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">© 2026 UNNES Informatics Education</p>
                        <p class="text-[9px] font-medium text-slate-400 uppercase mt-1">Penelitian Pengembangan Virtual Lab Excel</p>
                    </div>
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-tighter">Production v1.0</span>
                </footer>
            </div>
        </div>
    </div>
</x-app-layout>