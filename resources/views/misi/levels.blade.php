{{-- //* (View) Peta Perjalanan Misi --}}

@extends('layouts.siswa')

@section('title', 'Peta Perjalanan - ' . $category)

@push('styles')
<style>
    /* //* (Visual) Transisi interaksi kartu */
    .glass-card {
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }

    /* //* (Hover) Glow identitas modul misi */
    .glass-card:hover { 
        border-color: #10b981; 
        box-shadow: 0 20px 40px -10px rgba(16, 185, 129, 0.3); 
    }

    /* //* (Dekorasi) Teks latar gaya game */
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

    /* //* (Active) Tekanan siluet saat klik */
    .glass-card:active .card-silhouette { 
        opacity: 0.4; 
        transform: rotate(5deg) scale(1.1); 
        color: #059669;
    }

    /* //* (UI) Mekanik tombol pegas 3D */
    .btn-menu-pegas {
        transition: all 0.1s ease;
        border-bottom-width: 6px;
    }
    .btn-menu-pegas:active {
        transform: translateY(4px);
        border-bottom-width: 2px;
    }

    .btn-back-pegas-6 {
        transition: all 0.1s ease;
        border-bottom-width: 6px !important;
    }
    .btn-back-pegas-6:active {
        transform: translateY(4px);
        border-bottom-width: 0px !important;
    }

    /* //* (Remedial) UI panel perbaikan skor */
    .remedial-box {
        background: #fffdf5;
        margin-top: -34px; 
        padding-top: 44px;
        padding-bottom: 10px;
        border-bottom: 5px solid #fbbf24;
        transition: all 0.3s ease;
        width: 90%; 
        margin-left: auto;
        margin-right: auto;
        z-index: 0;
    }
    .dark .remedial-box { 
        background: rgba(251, 191, 36, 0.05); 
        border-color: rgba(251, 191, 36, 0.2); 
    }

    .font-game { font-family: 'Bangers', cursive; }
</style>
@endpush

@section('header_left')
    {{-- //* (Nav) Kontrol kembali & tiket remedial --}}
    <a href="{{ route('misi.index') }}" class="p-2 bg-white dark:bg-slate-800 border-2 border-slate-200 dark:border-slate-700 rounded-xl btn-back-pegas-6 text-slate-600 dark:text-slate-300 shadow-sm active:scale-90 transition-transform">
        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3.5"><path d="M15 19l-7-7 7-7" /></svg>
    </a>
    <div class="flex flex-col text-left ml-3 leading-none">
        <span class="text-base font-extrabold tracking-tight dark:text-white uppercase leading-none">Peta Perjalanan</span>
        <div class="flex items-center space-x-1.5 mt-1.5">
            <div class="flex items-center bg-amber-50 dark:bg-amber-950/30 px-2 py-0.5 rounded-md border border-amber-200 dark:border-amber-800 shadow-sm">
                <div class="flex items-center space-x-2">
                    {{-- Ikon Tiket PNG --}}
                    <img src="{{ asset('images/tiket.png') }}" 
                        alt="Tiket" 
                        class="w-4 h-4 md:w-5 md:h-5 object-contain drop-shadow-sm">
                    
                    {{-- Informasi Tiket --}}
                    <span class="text-[9px] font-black text-amber-600 dark:text-amber-400 uppercase tracking-widest leading-none">
                        Tiket Hari Ini: <span class="text-amber-700 dark:text-amber-300">{{ $remainingTickets }} / 3</span>
                    </span>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('content')
<div class="px-4 sm:px-10 py-8 flex flex-col items-center">
    
    <div class="w-full max-w-md sm:max-w-3xl lg:max-w-5xl mx-auto transition-all duration-500">
        
        <div class="mb-10 text-left px-2">
            <h1 class="text-2xl lg:text-2xl font-black text-slate-900 dark:text-white leading-tight capitalize">Daftar Misi Tersedia</h1>
            <p class="text-sm lg:text-sm text-slate-500 dark:text-slate-400 font-medium mt-1">Selesaikan setiap tantangan untuk membuka level berikutnya.</p>
        </div>

        <div class="space-y-4 mb-24">
            @php $actualIteration = 1; @endphp
            
            @foreach($levels as $index => $level)
                @php
                    $mission = $level->missions->first();
                    if($level->level_order == 0 || !$mission) continue;
                    
                    // //* (Logic) Progress Check */
                    $isCompleted = in_array($mission->id, $userProgress);

                    // //* (Fixed Logic) High Score: Mengambil nilai absolut tertinggi dari seluruh percobaan */
                    // Kita ambil semua record, lalu urutkan di sisi PHP untuk memastikan sorting numerik akurat
                    $scoreData = $isCompleted 
                        ? \App\Models\Progress::where('user_id', Auth::id())
                            ->where('mission_id', $mission->id)
                            ->get()
                            ->sortByDesc('score')
                            ->first() 
                        : null;
                @endphp

                <div class="flex flex-col group">
                    {{-- //* (Card) Area utama informasi misi --}}
                    <div class="glass-card flex items-center gap-4 md:gap-6 bg-white dark:bg-slate-900 border-2 border-slate-200 dark:border-slate-800 rounded-[2.2rem] p-4 md:p-4 border-b-[8px] relative overflow-hidden z-10 shadow-sm active:scale-[0.98]">
                        
                        <div class="card-silhouette">LVL</div>

                        <div class="w-10 h-10 md:w-12 md:h-12 rounded-2xl flex-shrink-0 flex items-center justify-center text-xl md:text-2xl font-game shadow-inner z-10
                            {{ $isCompleted ? 'bg-emerald-50 text-emerald-600 dark:bg-emerald-900/20' : 'bg-slate-100 text-slate-400' }}">
                            {{ $actualIteration++ }}
                        </div>

                        <div class="flex-1 text-left min-w-0 z-10">
                            <div class="flex flex-wrap items-center gap-2 mb-0.5">
                                <h3 class="text-base md:text-lg font-bold text-slate-900 dark:text-white truncate capitalize leading-tight">
                                    {{ strtolower($mission->title) }}
                                </h3>
                                <span class="px-2 py-0.5 bg-slate-100 dark:bg-slate-800 text-slate-400 text-[7px] font-black uppercase rounded-lg tracking-widest">{{ $mission->mission_type }}</span>
                            </div>
                            
                            <div class="flex items-center gap-3">
                                <span class="text-[8px] font-black {{ $isCompleted ? 'text-blue-500' : 'text-emerald-600' }} uppercase tracking-widest bg-slate-50 dark:bg-slate-800/50 px-2 py-0.5 rounded-md">
                                    {{ $isCompleted ? 'Selesai' : 'Siap Dimulai' }}
                                </span>
                                <span class="text-xs font-game text-emerald-500 tracking-wider">+{{ $mission->max_score }} XP</span>
                            </div>
                        </div>

                        <div class="flex-shrink-0 z-10">
                            <a href="{{ route('misi.show', $mission->id) }}" 
                            class="btn-menu-pegas inline-flex justify-center items-center px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-black rounded-2xl text-[10px] tracking-widest uppercase border-emerald-800 z-10 shadow-lg shadow-emerald-100 transition-all">
                                {{ $isCompleted ? 'Review' : 'Mulai' }}
                            </a>
                        </div>
                    </div>

                    {{-- //* (Retry) Panel High Score --}}
                    @if($isCompleted && $scoreData)
                        <div class="remedial-box rounded-b-[2.2rem] border-x-2 border-slate-100 dark:border-slate-800 flex items-center justify-between px-6 md:px-8 gap-4">
                            <div class="flex items-center space-x-5">
                                <div class="flex flex-col leading-tight">
                                    <span class="text-[7px] font-black text-slate-400 uppercase tracking-widest">High Score</span>
                                    {{-- //* (Score) Pastikan menampilkan nilai tertinggi --}}
                                    <span class="text-xl font-game {{ $scoreData->score == $mission->max_score ? 'text-emerald-500' : 'text-amber-500' }}">
                                        {{ $scoreData->score }}<span class="text-[10px] font-bold text-slate-300">/{{ $mission->max_score }}</span>
                                    </span>
                                </div>

                                @if($scoreData->score < $mission->max_score)
                                    <div class="h-8 w-[1.5px] bg-slate-200 dark:bg-slate-700/50"></div>
                                    <p class="text-[9px] font-bold text-slate-500 dark:text-slate-400 leading-tight max-w-[140px]">
                                        Belum maksimal? Gunakan tiket untuk <span class="text-amber-600 dark:text-amber-500">Update Skor</span>.
                                    </p>
                                @endif
                            </div>

                            @if($scoreData->score < $mission->max_score)
                                <form action="{{ route('misi.retry', $mission->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn-menu-pegas flex items-center px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white font-black rounded-2xl text-[8px] tracking-widest uppercase border-amber-800 shadow-lg shadow-amber-100 dark:shadow-none transition-all">
                                        {{-- Ikon Tiket PNG --}}
                                        <img src="{{ asset('images/tiket.png') }}" 
                                            alt="Icon Tiket" 
                                            class="w-3.5 h-3.5 mr-1.5 object-contain drop-shadow-sm">
                                        
                                        <span>Tukar Tiket</span>
                                    </button>
                                </form>
                            @else
                                <div class="px-2 py-1 bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-100 dark:border-emerald-800/50 rounded-lg flex items-center space-x-1.5 shadow-sm">
                                    <img src="{{ asset('images/bintang.png') }}" 
                                        alt="Perfect" 
                                        class="w-3 h-3 object-contain animate-pulse">
                                    
                                    <span class="text-emerald-600 dark:text-emerald-400 text-[8px] font-black uppercase tracking-widest leading-none">
                                        Perfect
                                    </span>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
</div> 
@endsection