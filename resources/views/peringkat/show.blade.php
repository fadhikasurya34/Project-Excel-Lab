{{-- //* (View) Papan Klasemen Peringkat Global (Hall of Fame) */ --}}

@php
    //* (State) Data identitas visual user aktif */
    $userColor = Auth::user()->profile_color ?? '10b981';
    $userClasses = Auth::user()->classrooms ?? collect();
@endphp

@extends('layouts.siswa')

@section('title', 'Hall of Fame')

@push('styles')
<style>
    /* //* (Visual) Struktur dasar kartu gamifikasi super rounded */
    .glass-card-gamified {
        background: white;
        border-radius: 2.5rem; 
        border: 2px solid #f1f5f9;
        transition: transform 0.2s cubic-bezier(0.175, 0.885, 0.32, 1.275), 
                    box-shadow 0.8s ease-out, 
                    border-color 0.4s ease;
        position: relative;
        overflow: hidden;
    }
    .dark .glass-card-gamified { background: #0f172a; border-color: #1e293b; }

    /* //* (Effect) Logika kilauan visual pada kartu */
    .glass-card-gamified::after {
        content: "";
        position: absolute;
        top: -50%; left: -60%;
        width: 30%; height: 200%;
        background: rgba(255, 255, 255, 0.5);
        transform: rotate(35deg);
        transition: all 0.6s ease;
        pointer-events: none;
        z-index: 20;
    }
    .glass-card-gamified:hover::after { left: 130%; }

    /* //* (Podium) Konfigurasi kedalaman pilar piala (Depth) */
    @media (min-width: 768px) {
        .glass-card-gamified { border-radius: 4rem; } 
        .card-rank-1 { min-height: 250px; border-bottom: 12px solid #a16207 !important; }
        .card-rank-2 { min-height: 200px; border-bottom: 10px solid #334155 !important; }
        .card-rank-3 { min-height: 185px; border-bottom: 10px solid #9a3412 !important; }
    }

    /* //* (Theme) Efek pancaran warna berdasarkan rank piala */
    .glow-rank-1:hover, .glow-rank-1:active { 
        box-shadow: 0 20px 60px -10px rgba(254, 240, 7, 0.8), 0 0 30px rgba(253, 224, 71, 0.5); 
        border-color: #fde047;
    }
    .glow-rank-2:hover, .glow-rank-2:active { 
        box-shadow: 0 20px 60px -10px rgba(148, 163, 184, 0.7), 0 0 25px rgba(148, 163, 184, 0.4); 
        border-color: #cbd5e1;
    }
    .glow-rank-3:hover, .glow-rank-3:active { 
        box-shadow: 0 20px 60px -10px rgba(251, 146, 60, 0.7), 0 0 25px rgba(251, 146, 60, 0.4); 
        border-color: #fdba74;
    }

    /* //* (Interaction) Feedback visual pada daftar klasemen */
    .list-item-rank { border-left: 6px solid #e2e8f0; transition: all 0.3s, box-shadow 0.8s ease-out; }
    .dark .list-item-rank { border-left-color: #1e293b; }
    .list-item-rank:hover, .list-item-rank:active { 
        box-shadow: 0 15px 40px -5px rgba(168, 85, 247, 0.6), 0 0 20px rgba(168, 85, 247, 0.2); 
        border-left-color: #a855f7 !important;
        transform: translateX(5px);
    }

    /* //* (Decoration) Siluet tipografi latar belakang */
    .card-silhouette {
        position: absolute; font-family: 'Bangers', cursive; line-height: 0.8;
        opacity: 0.18; transform: rotate(-8deg); pointer-events: none;
        z-index: 0; color: #64748b; transition: all 0.4s ease;
    }
    .glass-card-gamified:hover .card-silhouette { opacity: 0.45; transform: rotate(0deg) scale(1.2); }
    .silhouette-podium { bottom: -0.5rem; right: -0.5rem; font-size: 10rem; }
    @media (min-width: 768px) { .silhouette-podium { font-size: 14rem; bottom: -1.5rem; } }
    .silhouette-list { top: 50%; right: 1.5rem; transform: translateY(-50%); font-size: 5rem; opacity: 0.12; }

    .btn-xp-depth {
        border-bottom: 4px solid #000000 !important;
        box-shadow: 0 8px 15px -5px rgba(0,0,0,0.3);
    }
    .dark .btn-xp-depth { border-bottom-color: #475569 !important; box-shadow: none; }

    .badge-you-podium {
        position: absolute; top: 0.5rem; left: 50%; transform: translateX(-50%);
        background: linear-gradient(to right, #a855f7, #ec4899); color: white;
        padding: 3px 12px; border-radius: 999px; font-size: 9px; font-weight: 900;
        box-shadow: 0 10px 25px rgba(168, 85, 247, 0.6); z-index: 50;
        animation: float-you 2.5s ease-in-out infinite;
    }
    @keyframes float-you { 0%, 100% { transform: translate(-50%, 0); } 50% { transform: translate(-50%, -6px); } }

    .glass-card-gamified:active { transform: scale(0.94); }
</style>
@endpush

@section('header_left')
    {{-- //* (Nav) Kontrol navigasi kembali  --}}
    <a href="{{ route('peringkat.index') }}" class="w-10 h-10 flex items-center justify-center bg-white dark:bg-slate-800 border-2 border-slate-100 dark:border-slate-700 rounded-xl btn-pegas text-slate-500 hover:text-blue-500 transition-colors">
        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3.5"><path d="M15 19l-7-7 7-7" /></svg>
    </a>
    <div class="flex flex-col text-left ml-3 leading-none">
        <span class="text-base font-black dark:text-white uppercase tracking-tighter leading-none">
            Hall of <span class="text-blue-600">Fame</span>
        </span>
        <div class="flex items-center space-x-1.5 mt-1">
            <span class="w-1 h-1 bg-emerald-500 rounded-full animate-pulse"></span>
            <span class="text-[7px] font-black text-slate-400 uppercase tracking-widest leading-none">Global Ranking Live</span>
        </div>
    </div>
@endsection

@section('content')
<div class="px-4 py-2 md:py-4 flex flex-col items-center">
    
    <div class="w-full max-w-6xl mx-auto transition-all duration-500">
        
        <div class="mb-4 md:mb-6 text-center">
            <h1 class="text-lg md:text-2xl font-black text-slate-900 dark:text-white tracking-tighter uppercase leading-none">The Champions</h1>
            <p class="text-[8px] md:text-[10px] text-slate-400 font-bold mt-1 uppercase tracking-[0.2em]">Penghargaan Praktikan Terbaik</p>
        </div>

        {{-- //* (Podium) Render visual tiga besar --}}
        <div class="grid grid-cols-2 md:grid-cols-3 gap-2 md:gap-4 mb-6 md:mb-10 max-w-[900px] mx-auto items-end px-1">
            @php 
                $top3 = $rankings->take(3);
                // Kita cuma atur urutan tampilan: Rank 2 (Kiri), Rank 1 (Tengah), Rank 3 (Kanan)
                $displayOrder = [
                    ['rank' => 2, 'data' => $top3[1] ?? null],
                    ['rank' => 1, 'data' => $top3[0] ?? null],
                    ['rank' => 3, 'data' => $top3[2] ?? null],
                ];
            @endphp

            @foreach($displayOrder as $item)
                @if($item['data'])
                    @php 
                        $user = $item['data']->user;
                        $status = $user->rank_status; // Manggil array ['title', 'medal', 'color'] dari Model
                        $isMe = $user->id == auth()->id();
                        $gridClass = ($item['rank'] == 1) ? 'col-span-2 md:col-span-1 order-1 md:order-2' : (($item['rank'] == 2) ? 'col-span-1 order-2 md:order-1' : 'col-span-1 order-3 md:order-3');
                    @endphp

                    <div class="podium-item glass-card-gamified card-rank-{{ $item['rank'] }} glow-rank-{{ $item['rank'] }} {{ $gridClass }} {{ $isMe ? 'is-me-card' : '' }} p-2.5 md:p-4 flex flex-col items-center justify-center text-center cursor-pointer select-none">
                        
                        @if($isMe) <div class="badge-you-podium uppercase">Kamu</div> @endif
                        <div class="card-silhouette silhouette-podium">{{ $item['rank'] }}</div>
                        
                        <div class="relative z-30 mb-2 md:mb-4 mt-3">
                            <div class="w-20 h-20 md:w-32 md:h-32 rounded-3xl md:rounded-[3.5rem] border-2 md:border-4 border-white dark:border-slate-800 shadow-xl overflow-hidden" 
                                style="background-color: #{{ $user->profile_color ?? '3b82f6' }};">
                                <img src="https://api.dicebear.com/9.x/bottts/svg?seed={{ $user->avatar ?? 'Felix' }}&backgroundColor=transparent" class="w-full h-full object-cover scale-110">
                            </div>

                            {{-- Medali Badge - AMAN karena ambil dari model --}}
                            <div class="absolute -bottom-3 -left-4 md:-bottom-5 md:-left-6 z-40 transition-transform hover:scale-125 duration-300 drop-shadow-[0_10px_15px_rgba(0,0,0,0.4)]">
                                <img src="{{ asset('images/' . ($status['medal'] ?? 'Apprentice.png')) }}" 
                                    class="h-14 w-auto md:h-24 object-contain">
                            </div>
                        </div>

                        <div class="relative z-30 w-full px-1">
                            <h3 class="text-slate-900 dark:text-white font-black text-xs md:text-lg leading-none truncate capitalize">
                                {{ explode(' ', $user->name)[0] }}
                            </h3>
                            {{-- Gelar Dinamis dari Model --}}
                            <p class="text-[7px] md:text-[9px] font-extrabold text-{{ $status['color'] ?? 'slate' }}-500 dark:text-{{ $status['color'] ?? 'slate' }}-400 uppercase tracking-[0.2em] mt-1.5 opacity-80">
                                {{ $status['title'] ?? 'Excel Apprentice' }}
                            </p>
                        </div>

                        <div class="mt-3 md:mt-5 px-3 py-1 md:px-5 md:py-1.5 bg-slate-950 dark:bg-slate-800 text-white rounded-xl md:rounded-[1.5rem] shadow-xl z-30 border border-slate-700/50">
                            <span class="text-[11px] md:text-lg font-black">{{ number_format($item['data']->total_xp) }}</span>
                            <span class="text-[7px] md:text-[9px] font-bold text-slate-400 ml-0.5 uppercase">xp</span>
                        </div>
                    </div>
                @endif
            @endforeach
        </div>

        {{-- //* (Ranking List) --}}
        <div class="max-w-2xl lg:max-w-3xl mx-auto space-y-1.5 md:space-y-2 mb-10 px-1">
            @foreach($rankings->skip(3) as $rank)
                @php 
                    $realRank = $loop->iteration + 3; 
                    $isMe = $rank->user->id == auth()->id();
                    $status = $rank->user->rank_status; // Sama, manggil dari model
                @endphp
                <div class="list-item-rank glass-card-gamified {{ $isMe ? 'ring-2 ring-purple-500 border-transparent' : '' }} p-2 md:p-2.5 flex items-center justify-between group shadow-sm cursor-pointer select-none">
                    
                    <div class="card-silhouette silhouette-list">#{{ $realRank }}</div>

                    <div class="flex items-center space-x-3 md:space-x-5 relative z-30">
                        <div class="w-6 h-6 md:w-8 md:h-8 bg-slate-100 dark:bg-slate-800 rounded-lg flex items-center justify-center font-black text-slate-500 text-[9px] md:text-xs">
                            {{ $realRank }}
                        </div>

                        <div class="w-8 h-8 md:w-10 md:h-10 rounded-lg md:rounded-[2.2rem] overflow-hidden border border-slate-200 dark:border-slate-700 shadow-sm" style="background-color: #{{ $rank->user->profile_color ?? '3b82f6' }};">
                            <img src="https://api.dicebear.com/9.x/bottts/svg?seed={{ $rank->user->avatar ?? 'Felix' }}&backgroundColor=transparent" class="w-full h-full object-cover scale-110">
                        </div>

                        <div class="min-w-0">
                            <h4 class="text-[10px] md:text-sm font-bold text-slate-800 dark:text-white capitalize truncate max-w-[120px] md:max-w-none flex items-center">
                                {{ $rank->user->name }}
                                @if($isMe) <span class="ml-1.5 px-1.5 py-0.5 bg-purple-600 text-white text-[6px] rounded-md uppercase font-black tracking-widest shrink-0">Kamu</span> @endif
                            </h4>
                            {{-- Gelar Dinamis dari Model --}}
                            <p class="text-[7px] md:text-[8px] font-black text-slate-400 uppercase tracking-tighter">{{ $status['title'] }}</p>
                        </div>
                    </div>
                    
                    <div class="text-right whitespace-nowrap pr-2 relative z-30">
                        <span class="text-xs md:text-base font-black text-slate-700 dark:text-slate-300">{{ number_format($rank->total_xp) }}</span>
                        <span class="text-[7px] md:text-[9px] font-bold text-slate-400 uppercase ml-0.5">xp</span>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection