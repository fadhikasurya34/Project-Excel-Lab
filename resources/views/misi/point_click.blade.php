{{-- (View) Halaman Simulasi Misi Interaktif Tipe Point & Click --}}

@php
    $jsonData = $mission->steps->sortBy('step_order')->values()->map(function($step) {
        return [
            'id' => $step->id,
            'instruction' => $step->instruction ?? 'Ikuti instruksi pengerjaan pada layar.',
            'image' => str_replace('/upload/', '/upload/f_auto,q_auto/', $step->step_image),
            'hotspots' => $step->hotspots->sortBy('order')->values()->map(function($hs) {
                return [
                    'id' => $hs->id, 
                    'x_percent' => $hs->x_percent, 
                    'y_percent' => $hs->y_percent,
                    'content' => $hs->content, 
                ];
            })->toArray()
        ];
    })->toArray();
@endphp

@extends('layouts.siswa')

@section('title', $mission->title)

@push('styles')
<style>
    /* (Style) Pelindung wajib mode landscape untuk layar perangkat mobile */
    #landscape-notice { display: none; }
    @media screen and (orientation: portrait) and (max-width: 1024px) {
        #landscape-notice {
            display: flex; position: fixed; inset: 0; z-index: 9999;
            background: rgba(15, 23, 42, 0.98); backdrop-filter: blur(20px);
            flex-direction: column; align-items: center; justify-content: center;
            padding: 2rem; text-align: center; color: white;
        }
    }
    .phone-rotate { animation: rotatePhone 2s ease-in-out infinite; }
    @keyframes rotatePhone { 0%, 100% { transform: rotate(0deg); } 50% { transform: rotate(90deg); } }

    /* (Style) Penstabil scrolling agar latar tidak bergeser saat interaksi layar */
    .simulation-wrapper { 
        position: relative; 
        width: 100%; 
        min-height: calc(100vh - 160px); 
    }
    .main-scroller { 
        overflow-y: auto !important; 
        overflow-x: hidden !important; 
        -webkit-overflow-scrolling: touch;
        height: 100%;
    }

    /* (Style) Tema transparan efek kaca (Glassmorphism) */
    .glass-ui-shared {
        background: rgba(15, 23, 42, 0.95); backdrop-filter: blur(12px);
        border: 2px solid #3b82f6; 
        border-radius: 1.8rem; overflow: hidden;
    }

    /* (Style) Proteksi agar tombol tak kasat mata tidak tertekan saat proses geser (drag) */
    body.is-dragging a, body.is-dragging button:not(.hud-btn) { 
        pointer-events: none !important; 
    }

    /* (Style) Konfigurasi panel Heads-Up Display (HUD) */
    .hud-controller { position: fixed; z-index: 90; width: 280px; pointer-events: auto; }
    
    /* (Style) Animasi pendaran cahaya pemikat atensi */
    @keyframes neon-misi-smooth {
        0%, 100% {
            box-shadow: 0 0 20px rgba(59, 130, 246, 0.6), inset 0 0 10px rgba(16, 185, 129, 0.3);
            border-color: #3b82f6;
            filter: drop-shadow(0 0 2px rgba(59, 130, 246, 0.5));
        }
        50% {
            box-shadow: 0 0 50px rgba(59, 130, 246, 0.9), 0 0 25px rgba(16, 185, 129, 0.8), inset 0 0 30px rgba(16, 185, 129, 0.5);
            border-color: #ffffff;
            filter: drop-shadow(0 0 12px rgba(59, 130, 246, 0.9));
            transform: scale(1.02);
        }
    }
    .neon-attention-misi {
        animation: neon-misi-smooth 2.5s infinite ease-in-out;
    }

    /* (Style) Detail visual tombol interaktif bergaya pegas tiga dimensi */
    .btn-pegas-blue { background: #2563eb; border-bottom: 4px solid #1e3a8a; transition: all 0.1s; }
    .btn-pegas-blue:active { transform: translateY(2px); border-bottom-width: 1px; }
    .btn-pegas-emerald { background: #10b981; border-bottom: 4px solid #064e3b; transition: all 0.1s; }
    .btn-pegas-emerald:active { transform: translateY(2px); border-bottom-width: 1px; }
    .btn-menu-pegas {transition: all 0.1s ease; border-bottom-width: 6px;}
    .btn-menu-pegas:active {transform: translateY(4px);border-bottom-width: 2px;}
    .btn-back-pegas {transition: all 0.1s ease;border-bottom-width: 6px;}
    .btn-back-pegas:active {transform: translateY(2px);border-bottom-width: 0px;}
    
    /* (Style) Efek kilatan layar penanda kesalahan klik */
    .flash-error { position: fixed; inset: 0; background: rgba(239, 68, 68, 0.2); pointer-events: none; z-index: 100; animation: fade-out 0.4s forwards; }
    .shake { animation: shake 0.4s cubic-bezier(.36,.07,.19,.97) both; }
    @keyframes fade-out { from { opacity: 1; } to { opacity: 0; } }
    @keyframes shake { 10%, 90% { transform: translate3d(-2px, 0, 0); } 20%, 80% { transform: translate3d(4px, 0, 0); } 30%, 50%, 70% { transform: translate3d(-6px, 0, 0); } 40%, 60% { transform: translate3d(6px, 0, 0); } }

    /* (Style) Desain area sentuh target utama (Hotspot) */
    .marker-ring {
        position: absolute; 
        width: 40px; height: 40px; 
        margin: 0;
        transform: translate(-50%, -50%);
        border-radius: 50%; 
        border: 3px solid transparent; 
        display: flex; align-items: center; justify-content: center;
        transition: all 0.3s; z-index: 20; cursor: pointer;
    }
    
    /* Status: Belum diklik */
    .marker-ring:not(.marker-done) {
        background: transparent;
        border-color: transparent;
    }
    
    /* Status: Sukses diklik */
    .marker-done { 
        background: transparent; 
        border-color: #10b981; 
        opacity: 1 !important; 
        cursor: default; 
    }
    .check-icon { color: #10b981; font-size: 1.25rem; font-weight: 900; }

    .font-game { font-family: 'Bangers', cursive; }

    /* (Style) Desain wadah popup umpan balik kemajuan siswa */
    .feedback-modal-wrapper {
        position: fixed; inset: 0; z-index: 10000;
        display: flex; align-items: flex-end; justify-content: center;
        background: rgba(0, 0, 0, 0.4);
        backdrop-filter: blur(2px);
    }
    
    .feedback-modal {
        width: 100%; max-width: 500px;
        background: #ffffff;
        padding: 1.5rem 1.5rem 2rem 1.5rem;
        border-radius: 1.5rem 1.5rem 0 0;
        text-align: left;
        box-shadow: 0 -10px 40px rgba(0, 0, 0, 0.15);
        pointer-events: auto;
    }
    .dark .feedback-modal { background: #0f172a; box-shadow: 0 -10px 40px rgba(0, 0, 0, 0.6); }

    /* (Style) Efek umpan balik visual melayang (Floating Teks) */
    .floating-text {
        position: fixed; z-index: 10000; font-weight: 900; font-size: 1.5rem;
        color: #fbbf24; text-shadow: 0 4px 6px rgba(0,0,0,0.4); pointer-events: none;
        animation: floatUp 1s ease-out forwards;
    }
    @keyframes floatUp {
        0% { transform: translateY(0) scale(1); opacity: 1; }
        100% { transform: translateY(-80px) scale(1.5); opacity: 0; }
    }
</style>
@endpush

@section('header_left')
    <a href="{{ route('misi.category.levels', $mission->level->category) }}" class="p-2 bg-white dark:bg-slate-800 border-2 border-slate-200 dark:border-slate-700 rounded-xl btn-back-pegas text-slate-600 dark:text-slate-300 shadow-sm active:scale-90 transition-transform">
        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
            <path d="M15 19l-7-7 7-7" />
        </svg>
    </a>
    
    <div class="flex flex-col text-left ml-3 leading-none">
        <span class="text-base font-extrabold tracking-tight dark:text-white uppercase truncate max-w-[150px]">{{ $mission->title }}</span>
        <div class="flex items-center space-x-1.5 mt-1.5">
            <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full animate-pulse"></span>
            <span class="text-[7px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest leading-none">
                Langkah <span id="header-step-current">1</span> / {{ count($jsonData) }}
            </span>
        </div>
    </div>

    <div class="ml-4 bg-emerald-50 dark:bg-emerald-950/30 px-4 py-2 rounded-2xl border border-emerald-100 dark:border-emerald-800 font-game text-emerald-600 text-xl tracking-wider">
        <span id="header-xp-display">{{ $mission->max_score }}</span> XP
    </div>
@endsection

@section('content')
<div x-data="missionEngine()" x-init="init()" class="relative w-full min-h-screen main-scroller bg-slate-950">

    {{-- (View) Peringatan instruksional wajib baca sebelum memulai misi --}}
    <div x-show="showIntro" x-cloak class="fixed inset-0 z-[1000] p-4 sm:p-6 bg-slate-950/90 backdrop-blur-md overflow-y-auto flex">
        <div class="bg-white dark:bg-slate-800 rounded-3xl w-full max-w-lg shadow-2xl m-auto border-4 border-emerald-500 transform transition-all"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-8 sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100">
            
            <div class="p-6 md:p-8 max-h-[85vh] overflow-y-auto scrollbar-hide">
                <div class="w-16 h-16 bg-emerald-100 dark:bg-emerald-900/30 rounded-full flex items-center justify-center mb-5 mx-auto shadow-inner border border-emerald-200 dark:border-emerald-800">
                    <svg class="w-8 h-8 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                
                {{-- (View) Teks judul peringatan --}}
                <h3 class="text-2xl md:text-3xl font-black text-emerald-600 dark:text-emerald-400 text-center mb-2 uppercase tracking-tight">PERHATIAN</h3>
                <p class="text-[13px] text-slate-500 dark:text-slate-400 text-center mb-8 font-medium">Baca panduan ini agar kamu tidak membuang-buang XP.</p>
                
                <div class="space-y-4 mb-8">
                    <div class="flex gap-4 items-start bg-slate-50 dark:bg-slate-900/50 p-4 rounded-2xl border border-slate-100 dark:border-slate-700">
                        <div class="text-2xl shrink-0 mt-0.5">🖱️</div>
                        <div>
                            <h4 class="text-xs font-black text-emerald-600 dark:text-emerald-400 uppercase tracking-widest mb-1.5">Satu Ketukan (Point & Click)</h4>
                            <p class="text-[13px] text-slate-600 dark:text-slate-400 leading-relaxed font-medium">Jika disuruh blok kolom (misal A1 ke D5), <strong>jangan ditarik (drag)</strong>. Cukup <strong>KLIK SATU KALI</strong> pada sel tujuan (A1) atau sesuai petunjuk.</p>
                        </div>
                    </div>
                    <div class="flex gap-4 items-start bg-slate-50 dark:bg-slate-900/50 p-4 rounded-2xl border border-slate-100 dark:border-slate-700">
                        <div class="text-2xl shrink-0 mt-0.5">🎯</div>
                        <div>
                            <h4 class="text-xs font-black text-emerald-600 dark:text-emerald-400 uppercase tracking-widest mb-1.5">Area Klik Harus Presisi</h4>
                            <p class="text-[13px] text-slate-600 dark:text-slate-400 leading-relaxed font-medium">Pastikan kamu selalu menekan <strong>tepat di tengah-tengah ikon, tombol, atau kolom Excel</strong> yang dimaksud dalam instruksi agar tidak meleset.</p>
                        </div>
                    </div>
                </div>

                <button @click="showIntro = false" class="w-full py-4 bg-emerald-500 hover:bg-emerald-600 text-white rounded-2xl font-black text-sm uppercase tracking-widest shadow-lg shadow-emerald-500/30 transition-all active:scale-95 border-b-4 border-emerald-700 active:border-b-0 active:translate-y-1">
                    Saya Mengerti, Mulai Misi!
                </button>
            </div>
        </div>
    </div>

    {{-- (View) Papan notifikasi penyelesaian misi secara keseluruhan --}}
    <div x-show="feedbackModal.show" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="translate-y-full opacity-0"
         x-transition:enter-end="translate-y-0 opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="translate-y-0 opacity-100"
         x-transition:leave-end="translate-y-full opacity-0"
         x-cloak class="feedback-modal-wrapper">
        
        <div class="feedback-modal" :class="feedbackModal.type">
            <div class="flex items-center gap-3 md:gap-4 mb-5 md:mb-6">
                <div class="w-12 h-12 md:w-14 md:h-14 shrink-0 flex items-center justify-center rounded-full shadow-sm border-[3px]" 
                     :class="feedbackModal.type === 'error' ? 'bg-red-50 border-red-100 dark:bg-red-900/30 dark:border-red-800' : 'bg-emerald-50 border-emerald-100 dark:bg-emerald-900/30 dark:border-emerald-800'">
                    <img x-show="feedbackModal.type === 'error'" src="{{ asset('images/alert.png') }}" class="w-7 h-7 md:w-8 md:h-8 object-contain">
                    <img x-show="feedbackModal.type === 'success'" src="{{ asset('images/bintang.png') }}" class="w-7 h-7 md:w-8 md:h-8 object-contain">
                </div>
                
                <div>
                    <div class="text-xl md:text-2xl font-black tracking-wide" 
                         :class="feedbackModal.type === 'error' ? 'text-red-500' : 'text-emerald-500'" 
                         x-text="feedbackModal.title"></div>
                    <div class="text-sm md:text-base font-bold opacity-90 mt-0.5" 
                         :class="feedbackModal.type === 'error' ? 'text-red-400 dark:text-red-300' : 'text-emerald-400 dark:text-emerald-300'" 
                         x-text="feedbackModal.subtitle"></div>
                </div>
            </div>
            
            <button @click="handleFeedbackButton()" 
                    class="w-full py-3 md:py-3.5 rounded-xl font-black text-base md:text-lg text-white transition-all active:scale-95 border-b-[4px] active:border-b-0 active:translate-y-[4px]" 
                    :class="feedbackModal.type === 'error' ? 'bg-red-500 hover:bg-red-600 border-red-700' : 'bg-emerald-500 hover:bg-emerald-600 border-emerald-700'" 
                    x-text="feedbackModal.type === 'error' ? 'OKE' : 'LANJUT'">
            </button>
        </div>
    </div>

    {{-- (View) Peringatan paksaan menggunakan rotasi mendatar di HP --}}
    <div id="landscape-notice">
        <div class="phone-rotate mb-6 relative">
            <div class="absolute -inset-6 bg-blue-500/20 rounded-full blur-3xl animate-pulse"></div>
            <svg class="relative w-20 h-20 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
            </svg>
        </div>
        <h2 class="font-game text-3xl tracking-widest uppercase mb-2">Putar Layar</h2>
        <p class="text-slate-400 text-[9px] uppercase tracking-widest">Misi wajib menggunakan mode Landscape agar presisi klik akurat.</p>
    </div>

    {{-- (View) Tempat render kilatan merah --}}
    <template x-if="showErrorEffect">
        <div class="flash-error"></div>
    </template>

    {{-- (View) Panel Informasi dan Panduan Navigasi Pengguna yang Bisa Digeser --}}
    <div class="hud-controller" :style="`top: ${boxY}px; left: ${boxX}px;`"
         @mousedown.stop="startDragging($event, 'box')" @touchstart.stop="startDragging($event, 'box')"
         @click.stop="">
        
        <div class="glass-ui-shared neon-attention-misi" :class="showErrorEffect ? 'shake border-red-500' : ''">
            <div class="p-3 border-b border-white/10 flex justify-between items-center bg-blue-500/10">
                <div class="flex items-center space-x-2">
                    <img :src="showErrorEffect ? '{{ asset('images/alert.png') }}' : '{{ asset('images/misi.png') }}'" class="w-3.5 h-3.5">
                    <span class="text-[8px] font-black text-blue-400 uppercase tracking-widest" x-text="showErrorEffect ? 'Salah Klik' : 'Tujuan Misi'"></span>
                </div>
                <button @click="isExpanded = !isExpanded" class="text-white hud-btn" @mousedown.stop @touchstart.stop>
                    <svg x-show="isExpanded" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path d="M19 9l-7 7-7-7" /></svg>
                    <svg x-show="!isExpanded" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path d="M5 15l7-7 7 7" /></svg>
                </button>
            </div>

            <div class="p-4" x-show="isExpanded" x-collapse>
                <p class="text-white text-[15px] font-black leading-tight mb-4 tracking-tight" x-text="steps[currentStep] ? steps[currentStep].instruction : ''"></p>

                <div class="flex flex-row gap-2">
                    <button x-show="showHintButton" @click="showModal = true" 
                            @mousedown.stop @touchstart.stop
                            class="hud-btn flex-1 py-2.5 btn-pegas-blue text-white rounded-xl font-black text-[9px] shadow-lg transition-all">
                        Hint Bantuan
                    </button>
                    <button x-show="allHotspotsInStepDone" @click="nextStep()" 
                            @mousedown.stop @touchstart.stop
                            class="hud-btn flex-1 py-2.5 btn-pegas-emerald text-white rounded-xl font-black text-[9px] shadow-lg transition-all">
                        Lanjut Skenario
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- (View) Panggung utama yang menampilkan tangkapan layar antarmuka aplikasi --}}
    <div class="simulation-wrapper !p-0" @click="handleBackgroundClick($event)">
        <div class="relative w-full inline-block">
            <img :src="steps[currentStep] ? steps[currentStep].image : ''" class="w-full h-auto block select-none shadow-2xl">
            
            <template x-for="(hs, index) in (steps[currentStep] ? steps[currentStep].hotspots : [])" :key="hs.id">
                <div class="marker-ring" 
                     :class="clickedHotspots.includes(hs.id) ? 'marker-done' : 'opacity-0'"
                     :style="`top: ${hs.y_percent}%; left: ${hs.x_percent}%;`"
                     @click.stop="handleHotspotClick(hs, index, $event)">
                    <span class="check-icon" x-show="clickedHotspots.includes(hs.id)">✔</span>
                </div>
            </template>
        </div>
    </div>
    
    {{-- (View) Modal khusus untuk menayangkan solusi petunjuk klik --}}
    <div x-show="showModal" x-cloak class="fixed inset-0 z-[200] flex items-center justify-center p-6 bg-slate-950/80 backdrop-blur-sm">
        <div class="glass-ui-shared w-full max-w-[500px] flex flex-col shadow-2xl">
            <div class="p-4 border-b border-white/10 flex justify-between items-center bg-blue-500/10">
                <div class="flex items-center space-x-2">
                    <img src="{{ asset('images/find.png') }}" class="w-4 h-4">
                    <span class="text-[9px] font-black text-amber-400 uppercase tracking-widest">Petunjuk Hint</span>
                </div>
                <button @click="showModal = false" class="p-2 bg-red-500/20 text-red-400 rounded-xl active:scale-90 transition-all">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="p-8 text-center scrollbar-hide overflow-y-auto max-h-[50vh]">
                <p class="text-slate-100 font-bold text-sm leading-relaxed" x-text="currentHint"></p>
                <button @click="showModal = false" class="mt-6 w-full py-3 bg-slate-800 text-slate-300 rounded-xl font-black text-[10px]">Mengerti</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
{{-- (Library) Dukungan efek visual taburan kertas --}}
<script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js"></script>

<script>
    function missionEngine() {
        return {
            currentStep: 0, currentHotspotIndex: 0, clickedHotspots: [], isExpanded: true, 
            showModal: false, showErrorEffect: false, showHintButton: false,
            currentHint: '', boxX: 20, boxY: 80, isDragging: false, offX: 0, offY: 0,
            attempts: 0, currentPotentialXP: {{ $mission->max_score }},
            steps: @json($jsonData),
            feedbackModal: { show: false, type: '', title: '', subtitle: '', nextUrl: '' },
            
            // (State) Variabel status modal peringatan awal
            showIntro: true,
            
            audioPlayers: { click: null, benar: null, salah: null },

            storageKey: 'mission_{{ $mission->id }}_progress',
            isReview: {{ (auth()->user()->progress && auth()->user()->progress->where('mission_id', $mission->id)->where('status', 'completed')->isNotEmpty()) ? 'true' : 'false' }},

            // (Action) Memuat status komponen awal termasuk inisiasi berkas audio
            init() {
                try {
                    this.audioPlayers.click = new Audio('{{ asset("audio/click.mp3") }}');
                    this.audioPlayers.benar = new Audio('{{ asset("audio/benar.mp3") }}');
                    this.audioPlayers.salah = new Audio('{{ asset("audio/salah.mp3") }}');
                    this.audioPlayers.click.preload = 'auto';
                    this.audioPlayers.benar.preload = 'auto';
                    this.audioPlayers.salah.preload = 'auto';
                } catch(e) {}

                if (this.isReview) {
                    this.currentPotentialXP = {{ $mission->max_score }};
                    this.showIntro = false;
                } else {
                    const saved = localStorage.getItem(this.storageKey);
                    if (saved) {
                        const data = JSON.parse(saved);
                        this.currentStep = data.currentStep ?? 0;
                        this.currentHotspotIndex = data.currentHotspotIndex ?? 0;
                        this.clickedHotspots = data.clickedHotspots ?? [];
                        this.attempts = data.attempts ?? 0;
                        this.currentPotentialXP = data.currentPotentialXP ?? {{ $mission->max_score }};
                        
                        // (Process) Mematikan modal intro jika siswa melanjutkan progres sebelumnya
                        if (this.currentStep > 0 || this.clickedHotspots.length > 0 || this.attempts > 0) {
                            this.showIntro = false;
                        }
                    }
                }
                this.$watch('currentPotentialXP', v => { document.getElementById('header-xp-display').innerText = v; });
                this.$watch('currentStep', v => { document.getElementById('header-step-current').innerText = v + 1; });
            },

            // (Helper) Mengeksekusi file suara secara selektif sesuai tipe umpan balik
            playSound(type) {
                try {
                    let player = this.audioPlayers[type];
                    if (player) { player.currentTime = 0; player.play().catch(()=>{}); }
                } catch(e) {}
            },

            // (Process) Menyimpan rekam jejak pengguna melalui memori bawaan browser
            saveToLocal() {
                if (this.isReview) return;
                const payload = {
                    currentStep: this.currentStep, currentHotspotIndex: this.currentHotspotIndex,
                    clickedHotspots: this.clickedHotspots, attempts: this.attempts,
                    currentPotentialXP: this.currentPotentialXP
                };
                localStorage.setItem(this.storageKey, JSON.stringify(payload));
            },

            // (Helper) Memverifikasi apakah seluruh target sasaran di layar terkini telah berhasil ditekan
            get allHotspotsInStepDone() {
                let step = this.steps[this.currentStep];
                return step && this.clickedHotspots.length === step.hotspots.length;
            },

            // (Action) Memproses interaksi saat pengguna menyentuh area yang keliru di layar
            handleBackgroundClick(e) {
                if (this.allHotspotsInStepDone || this.isDragging || this.showIntro) return;
                this.triggerError(e);
            },

            // (Action) Mengevaluasi akurasi urutan klik pengguna terhadap target sesungguhnya
            handleHotspotClick(hs, index, e) {
                if (this.isDragging || this.showIntro) return;
                if (index === this.currentHotspotIndex) {
                    if (!this.clickedHotspots.includes(hs.id)) {
                        this.clickedHotspots.push(hs.id);
                        this.currentHotspotIndex++;
                        this.showErrorEffect = false;
                        this.showHintButton = false; 
                        this.saveToLocal();
                        this.playSound('click');
                        this.spawnFloatingText(e, '+Tepat 🎯', '#10b981');
                    }
                } else { 
                    this.triggerError(e); 
                }
            },

            // (Process) Memberlakukan sanksi pengurangan XP dan mengaktifkan peringatan kesalahan
            triggerError(e) {
                if (this.showErrorEffect || this.isDragging || this.showIntro) return;
                this.showErrorEffect = true;
                this.attempts++;
                this.showHintButton = true;
                let stepData = this.steps[this.currentStep];
                let correctHotspot = stepData ? stepData.hotspots[this.currentHotspotIndex] : null;
                this.currentHint = correctHotspot ? correctHotspot.content : 'Perhatikan instruksi misi.';
                
                if (!this.isReview && this.attempts > 3) {
                    let penalty = Math.floor({{ $mission->max_score }} * 0.05);
                    this.currentPotentialXP = Math.max(this.currentPotentialXP - penalty, Math.floor({{ $mission->max_score }} * 0.4));
                    if(e) this.spawnFloatingText(e, '-XP 📉', '#ef4444');
                } else {
                    if(e) this.spawnFloatingText(e, 'Meleset! 😭', '#ef4444');
                }
                this.saveToLocal();
                this.playSound('salah');
                this.fireCrossParticles();
                setTimeout(() => { this.showErrorEffect = false; }, 1500);
            },

            // (Action) Menutup jendela konfirmasi penyelesaian dan memindahkan pengguna
            handleFeedbackButton() {
                this.feedbackModal.show = false;
                if (this.feedbackModal.type === 'success' && this.feedbackModal.nextUrl) {
                    window.location.href = this.feedbackModal.nextUrl;
                }
            },

            // (Helper) Merangkai pesan pada modal notifikasi hasil pengerjaan
            triggerFeedbackModal(type, title, subtitle, nextUrl = '') {
                this.feedbackModal.type = type;
                this.feedbackModal.title = title;
                this.feedbackModal.subtitle = subtitle;
                this.feedbackModal.nextUrl = nextUrl;
                this.feedbackModal.show = true;
            },

            // (Helper) Memantik algoritma animasi taburan kertas keberhasilan
            fireConfetti() {
                var duration = 4 * 1000; var end = Date.now() + duration;
                (function frame() {
                    confetti({ particleCount: 5, angle: 60, spread: 55, origin: { x: 0 }, colors: ['#10b981', '#3b82f6', '#fbbf24', '#ffffff'] });
                    confetti({ particleCount: 5, angle: 120, spread: 55, origin: { x: 1 }, colors: ['#10b981', '#3b82f6', '#fbbf24', '#ffffff'] });
                    if (Date.now() < end) { requestAnimationFrame(frame); }
                }());
            },

            // (Helper) Memantik algoritma animasi letupan partikel kesalahan
            fireCrossParticles() {
                var defaults = { spread: 360, ticks: 100, gravity: 0.8, decay: 0.92, startVelocity: 40, colors: ['#ef4444', '#b91c1c'] };
                function fire(particleRatio, opts) {
                    confetti(Object.assign({}, defaults, opts, { particleCount: Math.floor(150 * particleRatio), shapes: ['star', 'circle'] }));
                }
                fire(0.25, { spread: 30, startVelocity: 60 });
                fire(0.2, { spread: 60 });
            },

            // (Helper) Menciptakan umpan balik berupa teks mengambang sesaat pada koordinat klik
            spawnFloatingText(e, text, color = '#fbbf24') {
                if (!e) return;
                let clientX = e.clientX || (e.touches ? e.touches[0].clientX : 0);
                let clientY = e.clientY || (e.touches ? e.touches[0].clientY : 0);
                const el = document.createElement('div');
                el.className = 'floating-text';
                el.innerText = text;
                el.style.left = (clientX - 20) + 'px';
                el.style.top = (clientY - 20) + 'px';
                el.style.color = color;
                document.body.appendChild(el);
                setTimeout(() => el.remove(), 1000);
            },

            // (Action) Memfasilitasi perpindahan adegan tangkapan layar atau melakukan finalisasi misi ke server
            nextStep() {
                if (this.currentStep < this.steps.length - 1) {
                    this.currentStep++;
                    this.currentHotspotIndex = 0;
                    this.clickedHotspots = [];
                    this.showHintButton = false;
                    this.saveToLocal();
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                } else {
                    if (this.isReview) {
                        window.location.href = "{{ route('misi.category.levels', $mission->level->category) }}";
                        return;
                    }
                    axios.post("{{ route('misi.check', $mission->id) }}", { answer: 'MISSION_COMPLETED', attempts: this.attempts })
                    .then(res => {
                        localStorage.removeItem(this.storageKey);
                        this.playSound('benar');
                        this.fireConfetti();
                        this.triggerFeedbackModal('success', 'Misi Selesai! 🎉', '+ ' + this.currentPotentialXP + ' XP Diraih', res.data.next_url);
                    });
                }
            },

            // (Process) Mengendalikan sistem pergerakan geser bebas (Drag) dari panel HUD
            startDragging(e, target) {
                if (e.target.closest('button') || this.showIntro) return;
                e.preventDefault(); this.isDragging = true;
                document.body.classList.add('is-dragging');
                let cX = (e.type === 'touchstart') ? e.touches[0].clientX : e.clientX;
                let cY = (e.type === 'touchstart') ? e.touches[0].clientY : e.clientY;
                this.offX = cX - this.boxX; this.offY = cY - this.boxY;
                const move = (e) => {
                    if (!this.isDragging) return;
                    let x = (e.type === 'touchmove') ? e.touches[0].clientX : e.clientX;
                    let y = (e.type === 'touchmove') ? e.touches[0].clientY : e.clientY;
                    this.boxX = x - this.offX; this.boxY = y - this.offY;
                };
                const stop = () => { 
                    setTimeout(() => { this.isDragging = false; document.body.classList.remove('is-dragging'); }, 100);
                    document.removeEventListener('mousemove', move); document.removeEventListener('touchmove', move); 
                };
                document.addEventListener('mousemove', move); document.addEventListener('touchmove', move, { passive: false });
                document.addEventListener('mouseup', stop); document.addEventListener('touchend', stop);
            }
        }
    }
</script>
@endpush