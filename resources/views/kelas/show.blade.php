{{-- 
    VIEW: Detail Squad (Sisi Siswa) - Ultra Fluid UI
    FIX: Menghilangkan efek kepotong, standarisasi warna, & Deep Button konsisten.
--}}

@extends('layouts.siswa')

@section('title', 'Detail Squad')

@push('styles')
<style>
    /* 1. Global Card Logic - Fix Shadows and Cutting */
    .glass-card-wrapper {
        padding: 5px; /* Memberi ruang agar shadow tidak terpotong */
        width: 100%;
    }

    .glass-card { 
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        position: relative;
        background: white;
        border-radius: 2.5rem;
        border-width: 2px;
        border-bottom-width: 8px !important; /* Standarisasi Hero Card */
    }
    
    .dark .glass-card { background: #0f172a; } /* slate-900 */

    /* Efek Hover: Mengangkat dan memberi Glow */
    .glass-card:hover { 
        border-color: #a855f7; 
        box-shadow: 0 25px 50px -12px rgba(168, 85, 247, 0.2);
        transform: translateY(-6px);
    }

    /* Efek Click: Deep Feedback */
    .glass-card:active { 
        transform: scale(0.98) translateY(2px);
        border-bottom-width: 4px !important;
    }

    /* Khusus Kartu "SAYA" (Glow Purple) */
    .active-user-glow {
        border-color: #a855f7 !important;
        box-shadow: 0 0 20px rgba(168, 85, 247, 0.1);
    }

    /* 2. Standarisasi Siluet Teks */
    .card-silhouette {
        position: absolute; top: -0.5rem; right: -0.5rem;
        font-family: 'Bangers', cursive; font-size: 6rem;
        line-height: 1; opacity: 0.04; transform: rotate(15deg);
        pointer-events: none; z-index: 0; color: #64748b;
        transition: all 0.4s ease;
    }
    
    .glass-card:hover .card-silhouette { opacity: 0.12; transform: rotate(10deg) scale(1.1); color: #a855f7; }

    /* 3. Deep Button System (Ref: Theme Toggle Logic) */
    .btn-deep {
        transition: all 0.15s ease;
        border-width: 2px;
        border-bottom-width: 6px !important;
        display: flex; align-items: center; justify-content: center;
    }

    .btn-deep:active {
        transform: translateY(3px);
        border-bottom-width: 2px !important;
    }

    .btn-deep-white {
        background-color: #ffffff; border-color: #f1f5f9; color: #64748b;
    }
    .dark .btn-deep-white {
        background-color: #1e293b; border-color: #334155; color: #cbd5e1;
    }

    .btn-deep-purple {
        background-color: #9333ea; border-color: #7e22ce; color: #ffffff;
    }

    /* XP Badge Fluidity */
    .xp-badge-fluid {
        background: #f8fafc; border: 2px solid #e2e8f0;
        border-radius: 1.5rem; transition: all 0.3s ease;
    }
    .dark .xp-badge-fluid { background: #1e293b; border-color: #334155; }
</style>
@endpush

@section('header_left')
    <div class="flex items-center">
        {{-- Tombol Back - Deep Style --}}
        <a href="{{ route('kelas.index') }}" 
           class="w-10 h-10 md:w-11 md:h-11 rounded-xl btn-deep btn-deep-white">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3.5">
                <path d="M15 19l-7-7 7-7" />
            </svg>
        </a>
        <div class="flex flex-col text-left leading-none ml-3">
            <span class="text-base font-extrabold tracking-tight dark:text-white uppercase truncate max-w-[150px]">{{ $classroom->name }}</span>
            <div class="flex items-center space-x-1.5 mt-1.5">
                <span class="w-1.5 h-1.5 bg-purple-500 rounded-full animate-pulse"></span>
                <span class="text-[7px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest leading-none">Terminal Squad</span>
            </div>
        </div>
    </div>
@endsection

@section('content')
@php
    $sortedUsers = $classroom->users->sortByDesc('total_xp')->values();
    $userIndex = $sortedUsers->search(fn($u) => $u->id === auth()->id());
    $actualUserRank = $userIndex !== false ? $userIndex + 1 : '-';
@endphp

<div class="px-4 sm:px-10 py-8 flex flex-col items-center">
    <div class="w-full max-w-5xl mx-auto">
        
        {{-- SECTION 1: HERO (Halo User) --}}
        <div class="glass-card-wrapper mb-10">
            <div class="glass-card border-slate-200 dark:border-slate-800 p-8 flex flex-col md:flex-row items-center justify-between gap-6 overflow-hidden">
                <div class="card-silhouette">HELLO</div>
                <div class="relative z-10 text-center md:text-left">
                    <h2 class="text-3xl font-black text-slate-900 dark:text-white tracking-tight capitalize">Halo, {{ explode(' ', auth()->user()->name)[0] }}!</h2>
                    <p class="text-[12px] text-slate-500 dark:text-slate-400 mt-2 font-medium">
                        Posisi kamu saat ini: <span class="text-purple-600 dark:text-purple-400 font-black">Rank #{{ $actualUserRank }}</span> dalam squad ini.
                    </p>
                </div>
                <div class="bg-slate-900 dark:bg-slate-800 px-8 py-4 rounded-[2rem] border-b-4 border-purple-600 text-center">
                    <span class="text-[8px] font-black text-slate-500 uppercase tracking-widest block mb-1">Kode Squad</span>
                    <span class="text-2xl font-black text-white tracking-widest">{{ $classroom->class_code }}</span>
                </div>
            </div>
        </div>

        {{-- SECTION 2: MINI STATS (Efek Deep & Fluid) --}}
        <div class="grid grid-cols-2 md:grid-cols-3 gap-2 mb-12">
            @php
                $stats = [
                    ['label' => 'POIN SAYA', 'val' => number_format(auth()->user()->total_xp), 'icon' => 'xp.png', 'bg' => 'purple', 'sil' => 'XP'],
                    ['label' => 'GURU', 'val' => explode(' ', $classroom->teacher_name)[0], 'icon' => 'guru.png', 'bg' => 'indigo', 'sil' => 'BOSS'],
                    ['label' => 'POPULASI', 'val' => $classroom->users->count() . ' Siswa', 'icon' => 'kelas.png', 'bg' => 'slate', 'sil' => 'TEAM']
                ];
            @endphp
            @foreach($stats as $stat)
            <div class="glass-card-wrapper">
                <div class="glass-card border-slate-100 dark:border-slate-800 p-6 flex items-center gap-4 overflow-hidden">
                    <div class="card-silhouette text-5xl">{{ $stat['sil'] }}</div>
                    
                    {{-- Bagian Ikon yang Diperbaiki --}}
                    <div class="w-12 h-12 lg:w-14 lg:h-14 bg-{{ $stat['bg'] }}-50 dark:bg-{{ $stat['bg'] }}-900/20 rounded-2xl flex items-center justify-center shadow-inner border border-{{ $stat['bg'] }}-100 dark:border-{{ $stat['bg'] }}-800 relative z-10">
                        <img src="{{ asset('images/' . $stat['icon']) }}" 
                            alt="{{ $stat['label'] }}" 
                            class="w-8 h-8 lg:w-10 lg:h-10 object-contain drop-shadow-sm">
                    </div>

                    <div class="relative z-10 min-w-0">
                        <p class="text-[8px] font-black text-slate-400 uppercase tracking-widest">{{ $stat['label'] }}</p>
                        <p class="text-base font-black text-slate-900 dark:text-white mt-0.5 truncate">{{ $stat['val'] }}</p>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        {{-- SECTION 3: TUGAS EVALUASI (PURPLE THEME) --}}
        <div class="mb-6 text-left px-4">
            <h2 class="text-xl font-black text-slate-900 dark:text-white uppercase">Tugas Evaluasi</h2>
            <p class="text-[12px] text-slate-500 dark:text-slate-400 font-medium">Daftar misi khusus untuk penilaian dosen.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-2 mb-16">
            @forelse($classroom->tasks as $task)
            <div class="glass-card-wrapper">
                <div class="glass-card border-slate-100 dark:border-slate-800 p-7 overflow-hidden">
                    <div class="card-silhouette">TASK</div>
                    <div class="relative z-10">
                        <div class="flex justify-between items-center mb-4">
                            <span class="px-2 py-1 bg-purple-50 dark:bg-purple-900/20 text-purple-600 text-[8px] font-black uppercase rounded-lg border border-purple-100 dark:border-purple-800">
                                {{ $task->missions->count() }} Misi
                            </span>
                            <span class="text-[9px] font-bold text-slate-400">{{ $task->created_at->format('d M') }}</span>
                        </div>
                        <h4 class="text-xl font-black text-slate-800 dark:text-white leading-tight mb-6 h-12 line-clamp-2">{{ $task->name }}</h4>
                        
                        <a href="{{ route('kelas.task.show', $task->id) }}" 
                           class="w-full h-12 btn-deep btn-deep-purple rounded-2xl text-[10px] font-black uppercase tracking-widest">
                            Buka Pengerjaan
                        </a>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-span-full py-16 text-center border-4 border-dashed border-slate-200 dark:border-slate-800 rounded-[3rem]">
                <p class="text-xs text-slate-400 font-black uppercase tracking-[0.3em]">Belum Ada Tugas Aktif</p>
            </div>
            @endforelse
        </div>

        {{-- SECTION 4: LEADERBOARD --}}
        <div class="mb-6 text-left px-4">
            <h2 class="text-xl font-black text-slate-900 dark:text-white uppercase leading-tight">Klasemen Squad</h2>
            <p class="text-[12px] text-slate-500 dark:text-slate-400 font-medium mt-1">Persaingan poin antar anggota kelas.</p>
        </div>

        <div class="flex flex-col gap-2 mb-20">
            @foreach($sortedUsers as $index => $siswa)
                @php 
                    $rank = $index + 1;
                    $isMe = $siswa->id == auth()->id();
                @endphp
                
                <div class="glass-card-wrapper">
                    <div class="glass-card {{ $isMe ? 'active-user-glow' : 'border-slate-100 dark:border-slate-800' }} p-5 flex items-center justify-between gap-4 overflow-hidden">
                        <div class="card-silhouette text-6xl">#{{ $rank }}</div>
                        
                        <div class="flex items-center gap-4 lg:gap-6 z-10 min-w-0">
                            <div class="w-12 flex justify-center shrink-0">
                                @if($rank <= 3)
                                    <span class="text-3xl drop-shadow-md">{{ $rank == 1 ? '🥇' : ($rank == 2 ? '🥈' : '🥉') }}</span>
                                @else
                                    <span class="text-sm font-black text-slate-300 dark:text-slate-600">#{{ $rank }}</span>
                                @endif
                            </div>

                            <div class="w-14 h-14 rounded-[1.4rem] border-4 border-white dark:border-slate-800 shadow-md shrink-0 overflow-hidden" style="background-color: #{{ $siswa->profile_color ?? 'a855f7' }};">
                                <img src="https://api.dicebear.com/9.x/bottts/svg?seed={{ $siswa->avatar ?? $siswa->name }}&backgroundColor=transparent" class="w-full h-full object-cover pt-1 scale-110">
                            </div>

                            <div class="min-w-0">
                                <h4 class="text-base font-black text-slate-900 dark:text-white truncate capitalize leading-none flex items-center">
                                    {{ $siswa->name }}
                                    @if($isMe) 
                                        <span class="ml-2 px-2 py-0.5 bg-purple-600 text-white text-[8px] rounded-lg uppercase font-black tracking-widest shadow-lg shadow-purple-200">ME</span> 
                                    @endif
                                </h4>
                                    {{-- Di dalam loop @foreach($sortedUsers as $siswa) --}}
                                    <p class="text-[10px] text-slate-400 mt-2 font-bold uppercase tracking-tight">
                                        {{-- Hitung langsung dari koleksi yang sudah di-load di Controller --}}
                                        {{ $siswa->completedMaterials->count() }} Modul • {{ $siswa->progress->where('status', 'completed')->count() }} Misi
                                    </p>
                            </div>
                        </div>

                        <div class="xp-badge-fluid px-5 py-3 shrink-0 z-10 text-center min-w-[85px] shadow-sm">
                            <span class="text-lg font-black text-slate-900 dark:text-white leading-none">{{ number_format($siswa->total_xp) }}</span>
                            <span class="text-[8px] font-black text-slate-400 block uppercase tracking-tighter mt-1 leading-none">PTS</span>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection