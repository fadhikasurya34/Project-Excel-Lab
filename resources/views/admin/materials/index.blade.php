{{-- 
    VIEW: Manajemen Konten Modul (Admin)
    DATA: $materials (Eager Loading: activities_count)
    DESC: Mengelola modul materi dan praktik serta navigasi ke Storyboard.
--}}

<x-app-layout>
    <style>
        {{-- (Style) Custom CSS untuk Latar Belakang & Tipografi Terminal --}}
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

        .table-header {
            background: #fcfdfe;
            border-bottom: 2px solid #f1f5f9;
        }

        .btn-blue-main {
            background-color: #2563eb;
            color: white;
            font-weight: 800;
            border-radius: 1rem;
            box-shadow: 0 10px 15px -3px rgba(37, 99, 235, 0.2);
            transition: all 0.2s ease;
        }
        .btn-blue-main:hover {
            background-color: #1d4ed8;
            transform: translateY(-1px);
            box-shadow: 0 12px 20px -3px rgba(37, 99, 235, 0.3);
        }

        .text-header { letter-spacing: -0.02em; }
    </style>

    <div class="min-h-screen bg-admin p-4 sm:p-10">

        {{-- (Notification) Toast System --}}
        @if(session('success') || session('error') || session('status'))
            <div x-data="{ show: true, progress: 100 }"
                x-show="show"
                x-init="let interval = setInterval(() => { progress -= 1; if(progress <= 0) { show = false; clearInterval(interval); } }, 30);"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="translate-y-10 opacity-0 scale-90"
                x-transition:enter-end="translate-y-0 opacity-100 scale-100"
                class="fixed bottom-6 right-4 sm:bottom-10 sm:right-10 z-[200]">
                
                <div class="bg-slate-900 border {{ session('error') ? 'border-red-500/30' : 'border-blue-500/30' }} p-5 rounded-2xl shadow-[0_20px_50px_rgba(0,0,0,0.3)] flex items-center space-x-4 min-w-[280px] sm:min-w-[320px] overflow-hidden relative">
                    <div class="absolute -top-10 -right-10 w-24 h-24 {{ session('error') ? 'bg-red-600/20' : 'bg-blue-600/20' }} blur-3xl"></div>
                    <div class="flex-shrink-0 w-10 h-10 {{ session('error') ? 'bg-red-600' : 'bg-blue-600' }} rounded-xl flex items-center justify-center shadow-lg">
                        <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                            @if(session('error'))
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            @else
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                            @endif
                        </svg>
                    </div>
                    <div class="flex-1">
                        <p class="text-[10px] font-black {{ session('error') ? 'text-red-400' : 'text-blue-400' }} uppercase tracking-[0.2em] leading-none mb-1">Module Sync</p>
                        <p class="text-sm font-bold text-white tracking-tight leading-tight">{{ session('success') ?? session('status') ?? session('error') }}</p>
                    </div>
                    <div class="absolute bottom-0 left-0 h-1 {{ session('error') ? 'bg-red-600' : 'bg-blue-600' }} transition-all ease-linear" :style="'width: ' + progress + '%'"></div>
                </div>
            </div>
        @endif

        {{-- (Section) Header --}}
        <div class="flex flex-col md:flex-row md:items-center justify-between mb-8 sm:mb-12 gap-6">
            <div>
                <div class="flex items-center space-x-2 mb-2">
                    <span class="px-2 py-0.5 bg-blue-50 text-blue-700 rounded text-[10px] font-bold tracking-widest uppercase">Admin Terminal</span>
                    <div class="w-1.5 h-1.5 bg-emerald-500 rounded-full shadow-[0_0_8px_rgba(16,185,129,0.6)]"></div>
                </div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 text-header tracking-tight">
                    Manajemen <span class="text-blue-600">Konten Modul</span>
                </h1>
                <p class="text-slate-500 font-medium text-xs sm:text-sm mt-1">Konfigurasi alur instruksional dan media pembelajaran interaktif.</p>
            </div>
            <a href="{{ route('admin.materials.create') }}" class="btn-blue-main inline-flex items-center justify-center px-6 sm:px-8 py-3.5 sm:py-4 text-[10px] sm:text-[11px] uppercase tracking-[0.15em] w-full md:w-auto">
                <svg class="w-5 h-5 mr-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path d="M12 4v16m8-8H4"></path></svg>
                Buat modul baru
            </a>
        </div>

        {{-- (Section) Statistik --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4 sm:gap-6 mb-10">
            <div class="admin-card p-5 sm:p-6 flex items-center justify-between hover:border-blue-300">
                <div>
                    <p class="text-[10px] font-bold text-slate-400 tracking-widest uppercase mb-1">Total Modul</p>
                    <h3 class="text-2xl sm:text-3xl font-black text-slate-900">{{ $materials->count() }}</h3>
                </div>
                <div class="w-10 h-10 sm:w-12 sm:h-12 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2M7 7h10"></path></svg>
                </div>
            </div>
            <div class="admin-card p-5 sm:p-6 flex items-center justify-between hover:border-blue-300">
                <div>
                    <p class="text-[10px] font-bold text-slate-400 tracking-widest uppercase mb-1">Kategori Materi</p>
                    <h3 class="text-2xl sm:text-3xl font-black text-slate-900">{{ $materials->where('category', 'materi')->count() }}</h3>
                </div>
                <div class="w-10 h-10 sm:w-12 sm:h-12 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                </div>
            </div>
            <div class="admin-card p-5 sm:p-6 flex items-center justify-between hover:border-blue-300 sm:col-span-2 md:col-span-1">
                <div>
                    <p class="text-[10px] font-bold text-slate-400 tracking-widest uppercase mb-1">Kategori Praktik</p>
                    <h3 class="text-2xl sm:text-3xl font-black text-slate-900">{{ $materials->where('category', 'praktik')->count() }}</h3>
                </div>
                <div class="w-10 h-10 sm:w-12 sm:h-12 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                </div>
            </div>
        </div>

        {{-- (Section) Tabel Modul - FIX RESPONSIVE PORTRAIT --}}
        <div class="admin-card overflow-hidden shadow-sm border-none md:border md:border-slate-200">
            <table class="w-full text-left border-collapse block md:table">
                {{-- Header hanya muncul di Landscape/Windows --}}
                <thead class="table-header hidden md:table-header-group">
                    <tr>
                        <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-[0.15em]">Identitas Modul</th>
                        <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-[0.15em] text-center">Kategori</th>
                        <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-[0.15em] text-center">Struktur</th>
                        <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-[0.15em] text-right">Manajemen</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 block md:table-row-group">
                    @forelse($materials as $material)
                    <tr class="hover:bg-slate-50/80 transition-colors group block md:table-row p-5 md:p-0 mb-4 md:mb-0 bg-white md:bg-transparent rounded-2xl md:rounded-none border border-slate-100 md:border-none shadow-sm md:shadow-none">
                        
                        {{-- Identitas --}}
                        <td class="block md:table-cell py-2 md:py-6 md:px-8">
                            <div class="flex flex-col">
                                <span class="text-base font-bold text-slate-800 tracking-tight leading-tight capitalize">{{ $material->title }}</span>
                                <span class="text-[10px] text-slate-400 font-bold mt-1 tracking-wider uppercase">ID: #MOD-{{ $material->id }}</span>
                            </div>
                        </td>

                        {{-- Kategori & Struktur (Digabung di portrait untuk hemat ruang) --}}
                        <td class="block md:table-cell py-3 md:py-6 md:px-8 md:text-center">
                            <div class="flex md:contents items-center justify-between">
                                <span class="md:hidden text-[10px] font-bold text-slate-400 uppercase tracking-widest">Atribut</span>
                                <div class="flex items-center space-x-2">
                                    <span class="px-2 py-1 rounded-lg text-[9px] font-black tracking-widest uppercase bg-blue-50 text-blue-600 border border-blue-100/50">
                                        {{ $material->category }}
                                    </span>
                                    <div class="inline-flex items-center space-x-1.5 bg-slate-50 px-2.5 py-1 rounded-lg border border-slate-100 md:hidden lg:inline-flex">
                                        <span class="text-[11px] font-black text-slate-700">{{ $material->activities_count }}</span>
                                        <span class="text-[8px] font-black text-slate-400 uppercase">Langkah</span>
                                    </div>
                                </div>
                            </div>
                        </td>

                        {{-- Struktur (Hanya muncul di Landscape/Windows) --}}
                        <td class="hidden md:table-cell py-6 px-8 text-center">
                            <div class="inline-flex items-center space-x-2 bg-slate-50 px-3 py-1.5 rounded-xl border border-slate-100">
                                <span class="text-sm font-black text-slate-700">{{ $material->activities_count }}</span>
                                <span class="text-[9px] font-black text-slate-400 uppercase tracking-tighter">Langkah</span>
                            </div>
                        </td>

                        {{-- Action Buttons --}}
                        <td class="block md:table-cell py-4 md:py-6 md:px-8 md:text-right border-t border-slate-50 md:border-none mt-4 md:mt-0">
                            <div class="flex items-center justify-between md:justify-end md:space-x-4">
                                <span class="md:hidden text-[10px] font-bold text-slate-400 uppercase tracking-widest">Opsi Kelola</span>
                                <div class="flex items-center space-x-2 sm:space-x-4">
                                    <a href="{{ route('admin.materials.steps', $material->id) }}" class="inline-flex items-center px-4 py-2 sm:px-5 sm:py-2.5 bg-blue-600 text-white text-[9px] sm:text-[10px] font-black rounded-xl hover:bg-blue-700 transition-all shadow-md shadow-blue-100 uppercase tracking-widest">
                                        Storyboard
                                    </a>
                                    <a href="{{ route('admin.materials.edit', $material->id) }}" class="p-2 text-slate-400 hover:text-blue-600 transition-colors">
                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    </a>
                                    <form action="{{ route('admin.materials.destroy', $material->id) }}" method="POST" class="inline">
                                        @csrf @method('DELETE')
                                        <button type="submit" onclick="return confirm('Hapus modul?')" class="p-2 text-slate-400 hover:text-red-500 transition-colors">
                                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr class="block md:table-row">
                        <td colspan="4" class="px-8 py-24 text-center block md:table-cell">
                            <p class="text-[10px] text-slate-300 font-black uppercase tracking-[0.3em]">Belum ada modul yang terdaftar</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>