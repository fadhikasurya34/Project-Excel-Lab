{{-- 
    VIEW: Squad Kelas (Hub Komunitas)
    DATA: $classrooms (Daftar squad yang diikuti)
    DESC: Area bagi siswa untuk bergabung ke kelas baru via kode akses dan memantau squad aktif.
--}}

@extends('layouts.siswa')

@section('title', 'Squad Kelas')

@push('styles')
<style>
    {{-- (Style) Standarisasi Visual: Identik dengan Dashboard Utama --}}
    .glass-card {
        transition: all 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }
    
    {{-- Hover Glows: Identitas Ungu untuk fitur Kelas --}}
    .card-purple:hover { border-color: #a855f7; box-shadow: 0 20px 40px -10px rgba(168, 85, 247, 0.4); }

    {{-- (Style) Efek Siluet: Teks latar belakang besar dengan Active State 0.4 --}}
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
    
    .glass-card:hover .card-silhouette { 
        opacity: 0.12; 
        transform: rotate(10deg) scale(1.05); 
    }

    .glass-card:active .card-silhouette { 
        opacity: 0.4; 
        transform: rotate(5deg) scale(1.1); 
        color: #475569;
    }

    {{-- (Style) Tombol Pegas: Ketebalan border 6px untuk feedback fisik --}}
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
    {{-- (Header) Navigasi Kembali & Status Hub --}}
    <div class="flex items-center">
        <a href="{{ route('dashboard') }}" class="p-2 bg-white dark:bg-slate-800 border-2 border-slate-200 dark:border-slate-700 rounded-xl btn-menu-pegas text-slate-600 dark:text-slate-300 shadow-sm mr-3 active:scale-90 transition-transform">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3.5">
                <path d="M15 19l-7-7 7-7" />
            </svg>
        </a>
        <div class="flex flex-col text-left leading-none">
            <span class="text-base font-extrabold tracking-tight dark:text-white uppercase leading-none">Squad Kelas</span>
            <div class="flex items-center space-x-1.5 mt-1.5">
                <span class="w-1.5 h-1.5 bg-purple-500 rounded-full animate-pulse"></span>
                <span class="text-[7px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest leading-none">Hub Terhubung</span>
            </div>
        </div>
    </div>
@endsection

@section('content')
<div class="px-4 sm:px-10 py-8 flex flex-col items-center justify-center min-h-[85vh]">
    
    <div class="w-full max-w-md sm:max-w-3xl lg:max-w-5xl mx-auto transition-all duration-500">
        
        {{-- (Section) Hero Card: Input Kode Akses Squad --}}
        <div class="glass-card card-purple bg-white dark:bg-slate-900 border-2 border-slate-200 dark:border-slate-800 rounded-[2.5rem] p-6 lg:p-8 border-b-[8px] relative overflow-hidden mb-12 shadow-sm">
            <div class="card-silhouette">JOIN</div>
            
            <div class="relative z-10 flex flex-col lg:flex-row items-center justify-between gap-6">
                <div class="flex items-center space-x-4 text-left w-full">
                    <div class="w-14 h-14 lg:w-16 lg:h-16 bg-purple-50 dark:bg-purple-900/30 rounded-2xl flex items-center justify-center shadow-inner border border-purple-100 dark:border-purple-800 shrink-0">
                        <img src="{{ asset('images/code.png') }}" alt="Icon Code" class="w-10 h-10 lg:w-12 lg:h-12 object-contain drop-shadow-[0_4px_6px_rgba(168,85,247,0.3)]">
                    </div>
                    <div>
                        <h2 class="text-lg lg:text-xl font-bold text-slate-900 dark:text-white leading-tight">Input Kode Kelas</h2>
                        <p class="text-[11px] lg:text-[12px] text-slate-500 dark:text-slate-400 mt-1.5 leading-relaxed font-medium">Masukkan kode akses untuk bergabung ke squad.</p>
                    </div>
                </div>

                {{-- Form Join: Sinkronisasi ke Controller --}}
                <form action="{{ route('kelas.store') }}" method="POST" class="w-full lg:w-auto flex items-center gap-2.5">
                    @csrf
                    <input type="text" name="class_code" placeholder="KODE-SQUAD" required
                           class="w-full lg:w-48 px-4 py-3 bg-slate-50 dark:bg-slate-800/50 border-2 border-slate-200 dark:border-slate-700 rounded-2xl outline-none focus:border-purple-500 dark:text-white font-black tracking-widest transition-all text-[10px] uppercase shadow-inner">
                    <button type="submit" class="btn-menu-pegas px-6 py-3 bg-purple-600 hover:bg-purple-700 text-white font-black rounded-2xl text-[9px] lg:text-[10px] tracking-widest border-purple-800 uppercase shadow-lg shadow-purple-100">
                        Gabung
                    </button>
                </form>
            </div>
        </div>

        {{-- (Section) List Content: Daftar Squad Terdaftar --}}
        <div class="mb-10 text-left px-2">
            <h1 class="text-2xl lg:text-2xl font-black text-slate-900 dark:text-white leading-tight uppercase">Daftar Squad Terdaftar</h1>
            <p class="text-sm lg:text-sm text-slate-500 dark:text-slate-400 font-medium mt-1">Kelola keanggotaan laboratorium anda di sini.</p>
        </div>

        {{-- Grid 2x2: Pola yang sama dengan Dashboard --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 lg:gap-8 mb-16">
            @forelse($classrooms as $kelas)
                <div class="glass-card card-purple bg-white dark:bg-slate-900 border-2 border-slate-200 dark:border-slate-800 rounded-[2.5rem] p-6 lg:p-8 border-b-[8px] flex flex-col items-start relative overflow-hidden h-full shadow-sm hover:-translate-y-2 active:scale-[0.98]">
                    <div class="card-silhouette">SQUAD</div>
                    
                    <div class="flex-1 flex flex-col items-start gap-4 lg:gap-5 z-10 w-full">
                        <div class="w-14 h-14 lg:w-16 lg:h-16 bg-purple-50 dark:bg-purple-900/30 rounded-2xl flex items-center justify-center shadow-inner border border-purple-100 dark:border-purple-800 transition-transform hover:scale-105">
                            <img src="{{ asset('images/kelas.png') }}" alt="Icon Kelas" class="w-10 h-10 lg:w-12 lg:h-12 object-contain drop-shadow-[0_4px_6px_rgba(168,85,247,0.3)]">
                        </div>
                        <div class="text-left w-full">
                            <h3 class="text-lg lg:text-xl font-bold text-slate-900 dark:text-white capitalize leading-tight">
                                {{ $kelas->name }}
                            </h3>
                            <p class="text-[11px] lg:text-[12px] text-slate-500 dark:text-slate-400 mt-2 leading-relaxed font-medium">
                                Guru: {{ $kelas->teacher_name }}<br>
                                <span class="text-purple-500 font-bold uppercase tracking-tighter">{{ $kelas->users->count() }} Anggota Aktif</span>
                            </p>
                        </div>
                    </div>

                    {{-- Tombol Buka --}}
                    <a href="{{ route('kelas.show', $kelas->id) }}" 
                       class="btn-menu-pegas w-36 lg:w-40 mt-6 lg:mt-8 py-3 bg-slate-100 dark:bg-slate-800 text-purple-600 dark:text-purple-400 font-black rounded-2xl text-[9px] lg:text-[10px] tracking-widest border-slate-200 dark:border-slate-700 uppercase text-center z-10">
                        Buka Squad
                    </a>
                </div>
            @empty
                {{-- State Kosong --}}
                <div class="col-span-full glass-card border-2 border-dashed border-slate-200 dark:border-slate-800 rounded-[2.5rem] p-12 flex flex-col items-center justify-center text-center opacity-50">
                    <img src="{{ asset('images/kelas.png') }}" class="w-16 h-16 object-contain grayscale opacity-30 mb-4">
                    <p class="text-[11px] font-black text-slate-400 uppercase tracking-widest">Belum Ada Squad Terdeteksi</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection