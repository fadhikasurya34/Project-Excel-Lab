{{-- //* (View) Dashboard Utama Praktikan */ --}}

@extends('layouts.siswa')

@section('title', 'Dashboard Praktikan')

@push('styles')
<style>
    /* //* (Visual) Transisi kartu interaktif */
    .glass-card {
        transition: all 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }
    
    /* //* (Hover) Identitas warna unik tiap fitur */
    .card-blue:hover { border-color: #3b82f6; box-shadow: 0 20px 40px -10px rgba(59, 130, 246, 0.4); }
    .card-emerald:hover { border-color: #10b981; box-shadow: 0 20px 40px -10px rgba(16, 185, 129, 0.4); }
    .card-purple:hover { border-color: #a855f7; box-shadow: 0 20px 40px -10px rgba(168, 85, 247, 0.4); }
    .card-amber:hover { border-color: #f59e0b; box-shadow: 0 20px 40px -10px rgba(245, 158, 11, 0.4); }

    /* //* (Effect) Tipografi latar belakang transparan */
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
    }
    
    /* //* (State) Animasi siluet saat interaksi aktif */
    .glass-card:hover .card-silhouette { opacity: 0.12; transform: rotate(10deg) scale(1.05); }
    .glass-card:active .card-silhouette { opacity: 0.4; transform: rotate(5deg) scale(1.1); color: #475569; }

    /* //* (UI) Mekanik tombol pegas 3D standar lab */
    .btn-menu-pegas {
        transition: all 0.1s ease;
        border-bottom-width: 6px;
    }
    .btn-menu-pegas:active {
        transform: translateY(4px);
        border-bottom-width: 2px;
    }
</style>
@endpush

@section('header_left')
    {{-- //* (Header) Identitas platform & status server aktif */ --}}
    <div class="flex flex-col text-left leading-none">
            {{-- Teks Virtual Lab hanya diletakkan di sini --}}

            <span class="text-base font-extrabold tracking-tight dark:text-white uppercase leading-none">Virtual Laboratory</span>
            
            {{-- Status Server --}}
            <div class="flex items-center space-x-1.5 mt-1.5">
                <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full animate-pulse"></span>
                <span class="text-[9px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest leading-none">
                    Server Aktif
                </span>
            </div>
        </div>
@endsection

@section('content')
<div class="px-4 sm:px-10 py-8 flex flex-col items-center justify-center min-h-[85vh]">
    
    <div class="w-full max-w-md sm:max-w-3xl lg:max-w-5xl mx-auto transition-all duration-500">
        
        {{-- //* (Info) Pesan sambutan praktikan aktif */ --}}
        <div class="mb-10 text-left px-2">
            <h1 class="text-2xl lg:text-2xl font-black text-slate-900 dark:text-white leading-tight capitalize">Selamat datang, {{ Auth::user()->name }}!</h1>
            <p class="text-sm lg:text-sm text-slate-500 dark:text-slate-400 font-medium mt-1">Selesaikan misi laboratorium Excel hari ini.</p>
        </div>

        {{-- //* (Grid) Layout menu utama navigasi fitur */ --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 lg:gap-8 mb-16">
            
            {{-- //* (Card) Modul eksplorasi materi interaktif */ --}}
            <div class="glass-card card-blue bg-white dark:bg-slate-900 border-2 border-slate-200 dark:border-slate-800 rounded-[2.5rem] p-6 lg:p-8 border-b-[8px] flex flex-col items-start relative overflow-hidden h-full shadow-sm hover:-translate-y-2 active:scale-[0.98]">
                <div class="card-silhouette">MATERI</div>
                <div class="flex-1 flex flex-col items-start gap-4 lg:gap-5 z-10">
                <div class="w-14 h-14 lg:w-16 lg:h-16 bg-blue-50 dark:bg-blue-900/30 rounded-2xl flex items-center justify-center shadow-inner border border-blue-100 dark:border-blue-800 transition-transform hover:scale-105">
                    <img src="{{ asset('images/materi.png') }}" 
                        alt="Icon Materi" 
                        class="w-10 h-10 lg:w-12 lg:h-12 object-contain drop-shadow-[0_4px_6px_rgba(37,99,235,0.3)]">
                </div>
                    <div>
                        <h3 class="text-lg lg:text-xl font-bold text-slate-900 dark:text-white capitalize leading-tight">Eksplorasi Materi</h3>
                        <p class="text-[11px] lg:text-[12px] text-slate-500 dark:text-slate-400 mt-2 leading-relaxed font-medium">Pelajari rumus dan fungsi logika Excel secara interaktif.</p>
                    </div>
                </div>
                <a href="{{ route('materi.index') }}" class="btn-menu-pegas w-36 lg:w-40 mt-6 lg:mt-8 py-3 bg-blue-600 hover:bg-blue-700 text-white font-black rounded-2xl text-[9px] lg:text-[10px] tracking-widest border-blue-800 uppercase text-center z-10 shadow-lg shadow-blue-100">Buka Materi</a>
            </div>

            {{-- //* (Card) Modul misi pengerjaan simulasi */ --}}
            <div class="glass-card card-emerald bg-white dark:bg-slate-900 border-2 border-slate-200 dark:border-slate-800 rounded-[2.5rem] p-6 lg:p-8 border-b-[8px] flex flex-col items-start relative overflow-hidden h-full shadow-sm hover:-translate-y-2 active:scale-[0.98]">
                <div class="card-silhouette">MISI</div>
                <div class="flex-1 flex flex-col items-start gap-4 lg:gap-5 z-10">
                <div class="w-14 h-14 lg:w-16 lg:h-16 bg-emerald-50 dark:bg-emerald-900/30 rounded-2xl flex items-center justify-center shadow-inner border border-emerald-100 dark:border-emerald-800 transition-transform hover:scale-105">
                    <img src="{{ asset('images/misi.png') }}" 
                        alt="Icon Misi" 
                        class="w-10 h-10 lg:w-12 lg:h-12 object-contain drop-shadow-[0_4px_6px_rgba(16,185,129,0.3)]">
                </div>
                    <div>
                        <h3 class="text-lg lg:text-xl font-bold text-slate-900 dark:text-white capitalize leading-tight">Misi Praktik</h3>
                        <p class="text-[11px] lg:text-[12px] text-slate-500 dark:text-slate-400 mt-2 leading-relaxed font-medium">Uji kemampuan logikamu dan raih skor terbaik hari ini.</p>
                    </div>
                </div>
                <a href="{{ route('misi.index') }}" class="btn-menu-pegas w-36 lg:w-40 mt-6 lg:mt-8 py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-black rounded-2xl text-[9px] lg:text-[10px] tracking-widest border-emerald-800 uppercase text-center z-10 shadow-lg shadow-emerald-100">Mulai Misi</a>
            </div>

            {{-- //* (Card) Manajemen keanggotaan kelas/squad */ --}}
            <div class="glass-card card-purple bg-white dark:bg-slate-900 border-2 border-slate-200 dark:border-slate-800 rounded-[2.5rem] p-6 lg:p-8 border-b-[8px] flex flex-col items-start relative overflow-hidden h-full shadow-sm hover:-translate-y-2 active:scale-[0.98]">
                <div class="card-silhouette">SQUAD</div>
                <div class="flex-1 flex flex-col items-start gap-4 lg:gap-5 z-10">
                <div class="w-14 h-14 lg:w-16 lg:h-16 bg-purple-50 dark:bg-purple-900/30 rounded-2xl flex items-center justify-center shadow-inner border border-purple-100 dark:border-purple-800 transition-transform hover:scale-105">
                    <img src="{{ asset('images/squad.png') }}" 
                        alt="Icon Squad" 
                        class="w-10 h-10 lg:w-12 lg:h-12 object-contain drop-shadow-[0_4px_6px_rgba(147,51,234,0.3)]">
                </div>
                    <div>
                        <h3 class="text-lg lg:text-xl font-bold text-slate-900 dark:text-white capitalize leading-tight">Gabung Kelas</h3>
                        <p class="text-[11px] lg:text-[12px] text-slate-500 dark:text-slate-400 mt-2 leading-relaxed font-medium">Masukkan kode untuk masuk ke dalam squad lab.</p>
                    </div>
                </div>
                <a href="{{ route('kelas.index') }}" class="btn-menu-pegas w-36 lg:w-40 mt-6 lg:mt-8 py-3 bg-purple-600 hover:bg-purple-700 text-white font-black rounded-2xl text-[9px] lg:text-[10px] tracking-widest border-purple-800 uppercase text-center z-10 shadow-lg shadow-purple-100">Input Kode</a>
            </div>

            {{-- //* (Card) Akses papan peringkat global */ --}}
            <div class="glass-card card-amber bg-white dark:bg-slate-900 border-2 border-slate-200 dark:border-slate-800 rounded-[2.5rem] p-6 lg:p-8 border-b-[8px] flex flex-col items-start relative overflow-hidden h-full shadow-sm hover:-translate-y-2 active:scale-[0.98]">
                <div class="card-silhouette">RANK</div>
                <div class="flex-1 flex flex-col items-start gap-4 lg:gap-5 z-10">
                <div class="w-14 h-14 lg:w-16 lg:h-16 bg-amber-50 dark:bg-amber-900/30 rounded-2xl flex items-center justify-center shadow-inner border border-amber-100 dark:border-amber-800 transition-transform hover:scale-105">
                    <img src="{{ asset('images/peringkat.png') }}" 
                        alt="Icon Peringkat" 
                        class="w-10 h-10 lg:w-12 lg:h-12 object-contain drop-shadow-[0_4px_6px_rgba(217,119,6,0.3)]">
                </div>
                    <div>
                        <h3 class="text-lg lg:text-xl font-bold text-slate-900 dark:text-white capitalize leading-tight">Peringkat Global</h3>
                        <p class="text-[11px] lg:text-[12px] text-slate-500 dark:text-slate-400 mt-2 leading-relaxed font-medium">Pantau posisimu dalam klasemen skor antar praktikan.</p>
                    </div>
                </div>
                <a href="{{ route('peringkat.index') }}" class="btn-menu-pegas w-36 lg:w-40 mt-6 lg:mt-8 py-3 bg-amber-600 hover:bg-amber-700 text-white font-black rounded-2xl text-[9px] lg:text-[10px] tracking-widest border-amber-800 uppercase text-center z-10 shadow-lg shadow-amber-100">Lihat Rank</a>
            </div>
        </div>
    </div>
</div>
@endsection