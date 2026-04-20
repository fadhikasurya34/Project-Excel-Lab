{{-- //* (View) Daftar Modul Laboratorium --}}

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
            <span class="text-[7px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest leading-none">Modul Laboratorium Aktif</span>
        </div>
    </div>
@endsection

@section('content')
<div class="px-4 sm:px-10 py-8 flex flex-col items-center">
    
    <div class="w-full max-w-md sm:max-w-3xl lg:max-w-5xl mx-auto transition-all duration-500">
        
        {{-- //* (Header) Informasi daftar modul --}}
        <div class="mb-10 text-left px-2">
            <h1 class="text-2xl lg:text-2xl font-black text-slate-900 dark:text-white leading-tight">Daftar Modul Laboratorium</h1>
            <p class="text-sm lg:text-sm text-slate-500 dark:text-slate-400 font-medium mt-1">Pilih materi fungsi logika untuk simulasi interaktif.</p>
        </div>

        {{-- //* (Grid) Layout kartu adaptif --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 lg:gap-8 mb-16">
            
            {{-- //* (Data) Loop daftar materi laboratorium --}}
            @foreach($materials as $item)
            
            {{-- //* (Logic) Validasi riwayat pengerjaan user --}}
            @php
                $isCompleted = Auth::user()->completedMaterials->where('material_id', $item->id)->first();
            @endphp
            
            <div class="glass-card bg-white dark:bg-slate-900 border-2 border-slate-200 dark:border-slate-800 rounded-[2.5rem] p-6 lg:p-8 border-b-[8px] flex flex-col items-start relative overflow-hidden h-full shadow-sm hover:-translate-y-2 active:scale-[0.98]">
                
                <div class="card-silhouette">MODUL</div>

                {{-- //* (Badge) Indikator status selesai --}}
                @if($isCompleted)
                    <div class="absolute top-6 right-6 lg:top-8 lg:right-8 bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 px-3 py-1.5 rounded-xl border border-emerald-100 dark:border-emerald-800 text-[9px] font-black uppercase tracking-widest z-20 flex items-center">
                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20"><path d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"></path></svg>
                        Selesai
                    </div>
                @endif

                <div class="flex-1 flex flex-col items-start gap-4 lg:gap-5 z-10">
                    <div class="w-14 h-14 lg:w-16 lg:h-16 bg-blue-50 dark:bg-blue-900/30 rounded-2xl flex items-center justify-center shadow-inner border border-blue-100 dark:border-blue-800 transition-transform hover:scale-105">
                        <img src="{{ asset('images/' . ($item->category == 'praktik' ? 'pembelajaran.png' : 'explore.png')) }}" 
                             alt="Icon Modul" 
                             class="w-10 h-10 lg:w-12 lg:h-12 object-contain drop-shadow-[0_4px_6px_rgba(37,99,235,0.3)]">
                    </div>
                    
                    <div class="pr-16">
                        <h3 class="text-lg lg:text-xl font-bold text-slate-900 dark:text-white capitalize leading-tight">
                            {{ strtolower($item->title) }}
                        </h3>
                        <p class="text-[11px] lg:text-[12px] text-slate-500 dark:text-slate-400 mt-2 leading-relaxed font-medium">
                            Klik untuk mensimulasikan antarmuka Excel dan memahami logikanya secara mendalam.
                        </p>
                    </div>
                </div>

                {{-- //* (Action) Akses detail simulasi modul --}}
                <a href="{{ route('materi.show', $item->id) }}" 
                    class="btn-menu-pegas w-36 lg:w-40 mt-8 py-3 bg-blue-600 hover:bg-blue-700 text-white font-black rounded-2xl text-[9px] lg:text-[10px] tracking-widest border-blue-800 uppercase text-center z-10 shadow-lg shadow-blue-100">
                    Buka Modul
                </a>
            </div>
            @endforeach

        </div>
    </div>
</div>
@endsection