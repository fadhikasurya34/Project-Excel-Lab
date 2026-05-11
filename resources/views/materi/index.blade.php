{{-- //* (View) Daftar Topik Laboratorium (Folder) --}}

@extends('layouts.siswa')

@section('title', 'Eksplorasi Materi')

@push('styles')
<style>
    /* //* (Card) Visual identitas materi (Glow Blue) */
    .glass-card {
        transition: all 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }
    
    .glass-card:hover { 
        border-color: #3b82f6; 
        box-shadow: 0 20px 40px -10px rgba(59, 130, 246, 0.3); 
    }

    /* //* (Effect) Siluet latar belakang kartu */
    .card-silhouette {
        position: absolute;
        top: -0.5rem;
        right: -0.5rem;
        font-family: 'Bangers', cursive;
        font-size: 5rem;
        line-height: 1;
        opacity: 0.05;
        transform: rotate(15deg);
        pointer-events: none;
        z-index: 0;
        color: #64748b;
        transition: all 0.3s ease;
        white-space: nowrap;
    }
    
    .glass-card:hover .card-silhouette { opacity: 0.12; transform: rotate(10deg) scale(1.05); }

    /* //* (Active) Feedback tekanan siluet */
    .glass-card:active .card-silhouette { 
        opacity: 0.4; 
        transform: rotate(5deg) scale(1.1); 
        color: #1e4ed8;
    }

    /* //* (UI) Mekanik tombol pegas 3D standar lab */
    .btn-menu-pegas {
        transition: all 0.1s ease;
        border-bottom-width: 6px;
    }
    .btn-menu-pegas:active {
        transform: translateY(4px);
        border-bottom-width: 2px;
    }

    .btn-back-pegas {
        transition: all 0.1s ease;
        border-bottom-width: 6px;
    }
    .btn-back-pegas:active {
        transform: translateY(2px);
        border-bottom-width: 0px;
    }

    /* Fix untuk hiding element Alpine */
    [x-cloak] { display: none !important; }
</style>
@endpush

@section('header_left')
    {{-- //* (Nav) Kembali ke Dashboard --}}
    <a href="{{ route('dashboard') }}" class="p-2 bg-white dark:bg-slate-800 border-2 border-slate-200 dark:border-slate-700 rounded-xl btn-back-pegas text-slate-600 dark:text-slate-300 shadow-sm active:scale-90 transition-transform">
        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
            <path d="M15 19l-7-7 7-7" />
        </svg>
    </a>
    <div class="flex flex-col text-left ml-3 leading-none">
        <span class="text-base font-extrabold tracking-tight dark:text-white uppercase leading-none">Eksplorasi Materi</span>
        <div class="flex items-center space-x-1.5 mt-1.5">
            <span class="w-1.5 h-1.5 bg-blue-500 rounded-full animate-pulse"></span>
            <span class="text-[7px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest leading-none">Katalog Topik Aktif</span>
        </div>
    </div>
@endsection

@section('content')
{{-- //* (State) Alpine data untuk menyimpan input pencarian --}}
<div class="px-4 sm:px-10 py-8 flex flex-col items-center" x-data="{ searchQuery: '' }">
    
    <div class="w-full max-w-md sm:max-w-3xl lg:max-w-5xl mx-auto transition-all duration-500">
        
        {{-- //* (Header & Search Bar) Informasi daftar topik --}}
        <div class="mb-10 px-2 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="text-left">
                <h1 class="text-2xl lg:text-2xl font-black text-slate-900 dark:text-white leading-tight">Pusat Pembelajaran Excel</h1>
                <p class="text-sm lg:text-sm text-slate-500 dark:text-slate-400 font-medium mt-1">Pilih kategori materi untuk mulai mengeksplorasi modul laboratorium.</p>
            </div>

            {{-- //* (Search Input) Desain Premium Blue --}}
            <div class="relative w-full md:w-72 lg:w-80 shrink-0 group">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none transition-colors group-focus-within:text-blue-500 text-slate-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>
                <input type="text" x-model="searchQuery" 
                       class="w-full pl-12 pr-4 py-3.5 bg-white dark:bg-slate-900 border-2 border-slate-200 dark:border-slate-700 rounded-2xl text-sm font-bold text-slate-800 dark:text-white focus:outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all shadow-sm placeholder-slate-400" 
                       placeholder="Cari topik...">
                
                {{-- Tombol clear pencarian muncul jika ada teks --}}
                <button x-show="searchQuery.length > 0" @click="searchQuery = ''" x-cloak
                        class="absolute inset-y-0 right-0 pr-4 flex items-center text-slate-400 hover:text-red-500 transition-colors">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
        </div>

        {{-- //* (Grid) Layout kartu adaptif --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 lg:gap-8 mb-16" x-ref="topicGrid">
            
            {{-- //* (Data) Loop daftar folder kategori materi --}}
            @foreach($categories as $cat)
            <div x-show="searchQuery === '' || '{{ strtolower($cat->name) }}'.includes(searchQuery.toLowerCase())"
                 x-transition.opacity.duration.300ms
                 class="glass-card bg-white dark:bg-slate-900 border-2 border-slate-200 dark:border-slate-800 rounded-[2.5rem] p-6 lg:p-8 border-b-[8px] flex flex-col items-start relative overflow-hidden h-full shadow-sm hover:-translate-y-2 active:scale-[0.98]">
                
                <div class="card-silhouette">TOPIK</div>

                {{-- //* (Badge) Indikator Jumlah Modul --}}
                <div class="absolute top-6 right-6 lg:top-8 lg:right-8 bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 px-3 py-1.5 rounded-xl border border-blue-100 dark:border-blue-800 text-[9px] font-black uppercase tracking-widest z-20 flex items-center">
                    {{ $cat->materials_count }} Modul
                </div>

                <div class="flex-1 flex flex-col items-start gap-4 lg:gap-5 z-10">
                    <div class="w-14 h-14 lg:w-16 lg:h-16 bg-blue-50 dark:bg-blue-900/30 rounded-2xl flex items-center justify-center shadow-inner border border-blue-100 dark:border-blue-800 transition-transform hover:scale-105">
                        <img src="{{ asset('images/pembelajaran.png') }}" 
                             alt="Icon Topik" 
                             class="w-10 h-10 lg:w-12 lg:h-12 object-contain drop-shadow-[0_4px_6px_rgba(37,99,235,0.3)]">
                    </div>
                    
                    <div class="pr-16">
                        <h3 class="text-lg lg:text-xl font-bold text-slate-900 dark:text-white capitalize leading-tight">
                            {{ strtolower($cat->name) }}
                        </h3>
                        <p class="text-[11px] lg:text-[12px] text-slate-500 dark:text-slate-400 mt-2 leading-relaxed font-medium">
                            {{ $cat->description }}
                        </p>
                    </div>
                </div>

                {{-- //* (Action) Buka daftar materi di dalam topik --}}
                <a href="{{ route('materi.category.list', $cat->id) }}" 
                    class="btn-menu-pegas w-36 lg:w-40 mt-8 py-3 bg-blue-600 hover:bg-blue-700 text-white font-black rounded-2xl text-[9px] lg:text-[10px] tracking-widest border-blue-800 uppercase text-center z-10 shadow-lg shadow-blue-100">
                    Buka Folder
                </a>
            </div>
            @endforeach

        </div>

        {{-- //* (Feedback) Menampilkan info jika pencarian tidak ditemukan --}}
        <div x-cloak 
             x-show="searchQuery.length > 0 && Array.from($refs.topicGrid.children).every(el => el.style.display === 'none')" 
             class="py-12 flex flex-col items-center justify-center text-center opacity-80 animate-pulse">
            <svg class="w-16 h-16 text-slate-300 dark:text-slate-600 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7" />
            </svg>
            <p class="text-slate-500 dark:text-slate-400 font-bold text-sm">Tidak menemukan topik materi yang mengandung kata <br><span class="text-blue-500" x-text="`&quot;${searchQuery}&quot;`"></span></p>
        </div>

    </div>
</div>
@endsection