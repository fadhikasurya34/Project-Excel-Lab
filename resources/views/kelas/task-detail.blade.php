{{-- 
    VIEW: Detail Evaluasi (Task Detail) - Student Side
    STYLE: Premium Standardized Purple UI (Anti-Cutting Shadow)
--}}

@extends('layouts.siswa')

@section('title', 'Detail Evaluasi Squad')

@push('styles')
<style>
    /* 1. Global Card Logic - Fix Shadows and Cutting */
    .glass-card-wrapper {
        padding: 8px; 
        width: 100%;
    }

    .glass-card { 
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        position: relative;
        background: white;
        border-radius: 2.5rem;
        border-width: 2px;
        border-bottom-width: 8px !important;
    }
    
    .dark .glass-card { background: #0f172a; border-color: #1e293b; }

    .glass-card:hover { 
        border-color: #a855f7; 
        box-shadow: 0 25px 50px -12px rgba(168, 85, 247, 0.2);
        transform: translateY(-4px);
    }

    /* 2. Standarisasi Siluet Teks */
    .card-silhouette {
        position: absolute; top: -0.5rem; right: -0.5rem;
        font-family: 'Bangers', cursive; font-size: 6rem;
        opacity: 0.04; transform: rotate(15deg);
        pointer-events: none; z-index: 0; color: #64748b;
    }

    /* 3. Deep Button System */
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

    /* 4. Progress Track Purple Style */
    .progress-track { background: #f1f5f9; border-radius: 2rem; height: 12px; overflow: hidden; border: 2px solid #f1f5f9; }
    .dark .progress-track { background: #1e293b; border-color: #1e293b; }
    .progress-bar-fill { background: linear-gradient(90deg, #a855f7, #6366f1); height: 100%; border-radius: 2rem; transition: width 1.5s ease-in-out; }
</style>
@endpush

@section('header_left')
    <div class="flex items-center">
        {{-- Tombol Back - Deep Style --}}
        <a href="{{ route('kelas.show', $task->classroom_id) }}" 
           class="w-10 h-10 md:w-11 md:h-11 rounded-xl btn-deep btn-deep-white">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3.5">
                <path d="M15 19l-7-7 7-7" />
            </svg>
        </a>
        <div class="flex flex-col text-left leading-none ml-3">
            <span class="text-base font-extrabold tracking-tight dark:text-white uppercase truncate max-w-[150px]">{{ $task->name }}</span>
            <div class="flex items-center space-x-1.5 mt-1.5">
                <span class="w-1.5 h-1.5 bg-purple-500 rounded-full animate-pulse"></span>
                <span class="text-[7px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest leading-none">Evaluasi Squad</span>
            </div>
        </div>
    </div>
@endsection

@section('content')
<div class="px-4 sm:px-10 py-8 flex flex-col items-center">
    <div class="w-full max-w-4xl mx-auto">
        
        {{-- SECTION 1: CAPAIAN EVALUASI (Hero Style) --}}
        @php
            $totalSkorUser = $userProgress->sum('score'); 
            $totalMaxSkor = $task->missions->sum('max_score');
            $persentase = $totalMaxSkor > 0 ? ($totalSkorUser / $totalMaxSkor) * 100 : 0;
        @endphp

        <div class="glass-card-wrapper mb-10">
            <div class="glass-card border-slate-200 dark:border-slate-800 p-8 flex flex-col md:flex-row items-center justify-between gap-8 overflow-hidden">
                <div class="card-silhouette">STATS</div>
                
                <div class="relative z-10 flex-1 w-full">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-10 h-10 bg-purple-50 dark:bg-purple-900/30 rounded-xl flex items-center justify-center shadow-inner overflow-hidden">
                            <img src="{{ asset('images/Progres.png') }}" 
                                alt="Icon Progres" 
                                class="w-7 h-7 object-contain drop-shadow-sm">
                        </div>
                        <h2 class="text-2xl font-black text-slate-900 dark:text-white uppercase tracking-tight">Capaian Evaluasi</h2>
                    </div>
                    
                    <div class="flex items-end justify-between mb-2 px-1">
                        <span class="text-[10px] font-black text-purple-600 dark:text-purple-400 uppercase tracking-widest">Progress Poin</span>
                        <span class="text-xs font-black text-slate-900 dark:text-white">{{ number_format($persentase, 1) }}%</span>
                    </div>
                    <div class="progress-track">
                        <div class="progress-bar-fill" style="width: {{ $persentase }}%"></div>
                    </div>
                    <p class="text-[10px] text-slate-400 mt-4 font-bold uppercase tracking-tight">
                        Mengumpulkan {{ number_format($totalSkorUser) }} dari {{ number_format($totalMaxSkor) }} poin tersedia dalam tugas ini.
                    </p>
                </div>

                <div class="relative z-10 bg-slate-900 dark:bg-slate-800 px-10 py-6 rounded-[2.5rem] border-b-4 border-purple-600 text-center min-w-[180px]">
                    <span class="text-[9px] font-black text-slate-500 uppercase tracking-[0.2em] mb-2 block">Skor Kumulatif</span>
                    <h3 class="text-4xl font-black text-white tracking-tighter">{{ number_format($totalSkorUser) }}</h3>
                    <span class="text-[10px] font-black text-purple-400 mt-1 uppercase">XP POINTS</span>
                </div>
            </div>
        </div>

        {{-- SECTION 2: DAFTAR MISI --}}
        <div class="mb-6 text-left px-4">
            <h2 class="text-xl font-black text-slate-900 dark:text-white uppercase leading-tight">Daftar Misi Wajib</h2>
            <p class="text-[12px] text-slate-500 dark:text-slate-400 font-medium mt-1">Selesaikan seluruh misi untuk mendapatkan hasil maksimal.</p>
        </div>

        <div class="flex flex-col gap-4 mb-20">
            @foreach($task->missions as $mission)
                @php 
                    $prog = $userProgress->get($mission->id);
                    $isDone = $prog && $prog->status === 'completed';
                    $userScore = $prog ? $prog->score : 0; 
                @endphp
                
                <div class="glass-card-wrapper">
                    <div class="glass-card {{ $isDone ? 'border-emerald-500/50' : 'border-slate-100 dark:border-slate-800' }} p-6 flex flex-col md:flex-row items-center justify-between gap-6 overflow-hidden">
                        <div class="card-silhouette" style="font-size: 4.5rem;">MISSION</div>
                        
                        <div class="flex items-center gap-5 z-10 w-full md:w-auto">
                            <div class="w-14 h-14 {{ $isDone ? 'bg-emerald-50 dark:bg-emerald-900/20 text-emerald-500 border-emerald-100' : 'bg-purple-50 dark:bg-slate-800 text-purple-500 border-purple-100 dark:border-slate-700' }} rounded-[1.5rem] flex items-center justify-center border-2 border-dashed shrink-0 shadow-inner">

                                @if($isDone)
                                    <img src="{{ asset('images/Checklist.png') }}" 
                                        alt="Status Selesai" 
                                        class="w-10 h-10 object-contain drop-shadow-sm">
                                @else
                                    <img src="{{ asset('images/Noprogres.png') }}" 
                                        alt="Status Belum Mulai" 
                                        class="w-10 h-10 object-contain drop-shadow-sm grayscale opacity-70 dark:grayscale-0 dark:opacity-100">
                                @endif
                            </div>

                            <div class="min-w-0">
                                <h4 class="text-base font-black text-slate-800 dark:text-white uppercase truncate leading-none">
                                    {{ $mission->title }}
                                </h4>
                                <div class="flex items-center gap-3 mt-2">
                                    <span class="text-[8px] font-black px-2 py-0.5 bg-slate-100 dark:bg-slate-800 text-slate-500 rounded-lg uppercase tracking-widest">
                                        {{ $mission->mission_type ?? 'Evaluasi' }}
                                    </span>
                                    <span class="text-[9px] font-bold text-slate-400 uppercase">Target: {{ $mission->max_score }} XP</span>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center justify-between w-full md:w-auto gap-8 z-10 border-t md:border-t-0 pt-4 md:pt-0 border-slate-50 dark:border-slate-800">
                            @if($isDone)
                                <div class="text-right">
                                    <p class="text-[8px] font-black text-slate-400 uppercase leading-none mb-1">Skor Kamu</p>
                                    <p class="text-xl font-black text-emerald-500 leading-none">
                                        {{ number_format($userScore) }} <span class="text-[10px]">XP</span>
                                    </p>
                                </div>
                            @else
                                <div class="text-right">
                                    <p class="text-[8px] font-black text-slate-300 uppercase leading-none mb-1">Status</p>
                                    <p class="text-xs font-black text-slate-400 uppercase leading-none">Belum Ada</p>
                                </div>
                            @endif

                            <a href="{{ route('misi.show', [$mission->id, 'from_task' => $task->id]) }}" 
                            class="px-8 py-3.5 {{ $isDone ? 'btn-deep-white text-slate-400' : 'btn-deep-purple' }} btn-deep rounded-2xl text-[10px] font-black uppercase tracking-widest min-w-[150px]">
                                {{ $isDone ? 'Review Misi' : 'Kerjakan Sekarang' }}
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection