{{-- //* (View) Simulasi Point & Click - Cloudinary Engine --}}

@php
    // //* (Process) Mapping data Eloquent ke JSON untuk Alpine.js (Cloudinary Ready) */
    $jsonData = $mission->steps->sortBy('step_order')->values()->map(function($step) {
        return [
            'id' => $step->id,
            'instruction' => $step->instruction ?? 'Ikuti instruksi pengerjaan pada layar.',
            // UPDATE: Langsung ambil URL Cloudinary & optimasi otomatis
            'image' => str_replace('/upload/', '/upload/f_auto,q_auto/', $step->step_image),
            'hotspots' => $step->hotspots->sortBy('order')->values()->map(function($hs) {
                return [
                    'id' => $hs->id, 
                    'x_percent' => $hs->x_percent, 
                    'y_percent' => $hs->y_percent,
                    'content' => $hs->content, 
                    // UPDATE: URL Video Cloudinary
                    'video' => $hs->video_path ?: null
                ];
            })->toArray()
        ];
    })->toArray();
@endphp

@extends('layouts.siswa')

@section('title', $mission->title)

@push('styles')
{{-- Plyr CSS untuk video tutorial --}}
<link rel="stylesheet" href="https://cdn.plyr.io/3.7.8/plyr.css" />
<style>
    /* (Guard) Blocker Layar Portrait */
    #landscape-notice { display: none; }
    @media screen and (orientation: portrait) and (max-width: 1024px) {
        #landscape-notice {
            display: flex; position: fixed; inset: 0; z-index: 9999; 
            background-color: rgba(15, 23, 42, 0.95); backdrop-filter: blur(20px);
            flex-direction: column; align-items: center; justify-content: center; 
            padding: 2rem; text-align: center; color: white;
        }
    }

    .main-scroller { 
        overflow-y: auto !important; overflow-x: hidden !important; 
        touch-action: pan-y !important; -webkit-overflow-scrolling: touch;
    }
    .excel-canvas { width: 100%; height: auto; display: block; user-select: none; }

    /* (Drag) */
    .draggable-box { position: fixed; z-index: 30 !important; pointer-events: none; } 
    .bubble-interact { pointer-events: auto !important; cursor: grab; user-select: none; touch-action: none; }
    .bubble-interact:active { cursor: grabbing; }

    .simulation-container { position: relative; width: 100%; padding: 1.5rem; min-height: calc(100vh - 150px); }
    
    .btn-pegas-6 { transition: all 0.1s ease; border-bottom-width: 6px !important; }
    .btn-pegas-6:active { transform: translateY(4px); border-bottom-width: 2px !important; }

    .maximized-panel { position: fixed !important; inset: 1rem !important; width: auto !important; height: auto !important; max-width: none !important; z-index: 50 !important; border-radius: 2rem !important; }
    .maximized-panel .bubble-interact { pointer-events: auto !important; cursor: default !important; }

    /* Video Overlay Style */
    .video-box-ultra { 
        position: fixed; z-index: 250; inset: 0;
        background: rgba(0, 0, 0, 0.95); backdrop-filter: blur(15px);
        display: flex; flex-direction: column; align-items: center; justify-content: center;
    }

    /* Efek visual */
    .flash-error { position: fixed; inset: 0; background-color: rgba(239, 68, 68, 0.2); pointer-events: none; z-index: 9999; animation: fade-out 0.4s forwards; }
    .shake { animation: shake 0.4s cubic-bezier(.36,.07,.19,.97) both; }
    @keyframes fade-out { 0% { opacity: 1; } 100% { opacity: 0; } }
    @keyframes shake { 10%, 90% { transform: translate3d(-2px, 0, 0); } 20%, 80% { transform: translate3d(4px, 0, 0); } 30%, 50%, 70% { transform: translate3d(-6px, 0, 0); } 40%, 60% { transform: translate3d(6px, 0, 0); } }

    .phone-rotate { animation: rotatePhone 2s ease-in-out infinite; }
    @keyframes rotatePhone { 0%, 100% { transform: rotate(0deg); } 50% { transform: rotate(90deg); } }
    .font-game { font-family: 'Bangers', cursive; }

    .toast-top {
        position: fixed; top: 1.5rem; left: 50%; transform: translateX(-50%);
        z-index: 1000; background: white; border-radius: 1.5rem;
        border: 2px solid #3b82f6; border-bottom: 6px solid #1d4ed8;
        min-width: 260px; padding: 0.8rem 1.2rem; text-align: center;
        animation: toast-down 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }
    .dark .toast-top { background: #1e293b; border-color: #3b82f6; border-bottom-color: #1e40af; }
    @keyframes toast-down { from { transform: translate(-50%, -100%) scale(0.8); opacity: 0; } to { transform: translate(-50%, 0) scale(1); opacity: 1; } }
</style>
@endpush

@section('header_left')
    <a href="{{ route('misi.category.levels', $mission->level->category) }}" class="p-2 bg-white dark:bg-slate-800 border-2 border-slate-200 dark:border-slate-700 rounded-xl btn-pegas-6 text-slate-600 dark:text-slate-300 shadow-sm">
        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3.5"><path d="M15 19l-7-7 7-7" /></svg>
    </a>
    
    <div class="flex flex-col text-left ml-3 leading-none">
        <span class="text-base font-extrabold tracking-tight dark:text-white uppercase truncate max-w-[150px] lg:max-w-none">{{ $mission->title }}</span>
        <div class="flex items-center space-x-1.5 mt-1.5">
            <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full animate-pulse"></span>
            <span class="text-[7px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest leading-none">
                Langkah <span id="header-step-current">1</span> / {{ count($jsonData) }}
            </span>
        </div>
    </div>

    <div class="ml-4 bg-emerald-50 dark:bg-emerald-950/30 px-4 py-2 rounded-2xl border border-emerald-100 dark:border-emerald-800 font-game text-emerald-600 text-xl tracking-wider hidden md:block">
        <span id="header-xp-display">{{ $mission->max_score }}</span> XP
    </div>
@endsection

@section('content')
    {{-- Notice Blocker --}}
    <div id="landscape-notice">
        <div class="phone-rotate mb-6 relative">
            <div class="absolute -inset-6 bg-blue-500/20 rounded-full blur-3xl animate-pulse"></div>
            <svg class="relative w-24 h-24 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
            </svg>
        </div>
        <h2 class="font-game text-4xl tracking-widest uppercase mb-3 text-white">Putar Layar</h2>
        <p class="text-slate-400 text-[10px] font-bold uppercase tracking-[0.2em] max-w-[280px]">Misi ini memerlukan mode Landscape untuk presisi.</p>
    </div>

<div x-data="missionEngine()" class="relative w-full h-full">
    
    {{-- Toast System --}}
    <div x-show="toast.show" x-cloak x-transition.opacity 
         class="toast-top shadow-xl flex items-center space-x-3" 
         :style="toast.type === 'error' ? 'border-color: #ef4444; border-bottom-color: #b91c1c;' : 'border-color: #10b981; border-bottom-color: #047857;'">
        
        <div class="w-10 h-10 rounded-xl flex items-center justify-center shadow-inner overflow-hidden bg-slate-50 dark:bg-slate-900/50">
            <img :src="'{{ asset('images') }}/' + toast.icon" class="w-7 h-7 object-contain animate-bounce">
        </div>

        <div class="text-left flex-1">
            <p class="text-[10px] font-black text-slate-900 dark:text-white uppercase leading-none" x-text="toast.title"></p>
            <p class="text-[9px] font-bold text-slate-500 dark:text-slate-400 mt-1" x-text="toast.message"></p>
        </div>
    </div>

    <template x-if="showErrorEffect">
        <div class="flash-error"></div>
    </template>

    {{-- Instruksi Misi --}}
    <div class="draggable-box" :style="`top: ${instY}px; left: ${instX}px; width: 300px;`" 
         @mousedown.stop="startDragging($event, 'inst')" @touchstart.stop="startDragging($event, 'inst')">
        <div class="bubble-interact w-full bg-blue-600 text-white p-5 rounded-[2.2rem] shadow-2xl border-b-[6px] border-blue-800 flex flex-col space-y-2"
             :class="showErrorEffect ? 'shake bg-red-600 border-red-800' : ''">
            
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <img :src="showErrorEffect ? '{{ asset('images/alert.png') }}' : '{{ asset('images/misi.png') }}'" class="w-6 h-6 object-contain">
                    <span class="text-[9px] font-black uppercase tracking-[0.2em] text-blue-100" x-text="showErrorEffect ? 'Klik Salah' : 'Instruksi Misi'"></span>
                </div>
                <span class="text-[7px] font-bold uppercase opacity-40">Drag</span>
            </div>
            <p class="text-[14px] font-extrabold leading-tight tracking-tight uppercase" x-text="steps[currentStep]?.instruction"></p>
        </div>
        
        <div class="bubble-interact w-full mt-4" x-show="isStepComplete">
            <button @click.stop="nextStep()" class="w-full py-4 bg-emerald-600 text-white rounded-[1.8rem] font-black text-[10px] uppercase tracking-widest btn-pegas-6 border-emerald-800 shadow-xl animate-pulse">
                <span x-text="currentStep < steps.length - 1 ? 'Lanjut Langkah Berikutnya ➔' : 'Selesaikan Misi Sekarang'"></span>
            </button>
        </div>
    </div>

    {{-- Feedback & Hotspot Content (Video Ready) --}}
    <div x-show="activeHotspotData !== null" x-cloak
        class="draggable-box transition-all duration-500" 
        :class="isMaximized ? 'maximized-panel' : 'w-[260px] shadow-2xl'" 
        :style="isMaximized ? '' : `top: ${explY}px; left: ${explX}px; pointer-events: auto;`"
        @mousedown.stop="!isMaximized && startDragging($event, 'expl')" 
        @touchstart.stop="!isMaximized && startDragging($event, 'expl')">
        
        <div class="w-full h-full bg-white dark:bg-slate-900 border-2 border-emerald-500/50 rounded-[2rem] shadow-2xl overflow-hidden flex flex-col pointer-events-auto">
            <div class="px-5 py-4 flex items-center justify-between border-b dark:border-slate-800 bg-slate-50/50 dark:bg-slate-950/30">
                <div class="flex items-center space-x-2">
                    <span class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></span>
                    <span class="text-[9px] font-black text-emerald-600 dark:text-emerald-400 uppercase tracking-widest">Informasi Lab</span>
                </div>
                
                <div class="flex items-center space-x-3">
                    <button @click.stop="isMaximized = !isMaximized" class="p-2 bg-white dark:bg-slate-800 border-2 border-slate-100 rounded-xl active:scale-90 transition-all"><svg x-show="!isMaximized" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5v-4m0 4h-4m4 0l-5-5"/></svg><svg x-show="isMaximized" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path d="M9 4v5m0 0H4m5 0L4 4m11 0v5m0 0h5m-5 0l5-5M9 20v-5m0 0H4m5 0l-5 5m11 0v-5m0 0h5m-5 0l5 5"/></svg></button>
                    <button @click.stop="activeHotspotData = null; isMaximized = false; showVideo = false" class="p-2 bg-red-50 rounded-xl border-2 border-red-100 text-red-500 active:scale-90 transition-all"><svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path d="M6 18L18 6M6 6l12 12"/></svg></button>
                </div>
            </div>

            <div class="p-8 flex-1 overflow-y-auto scrollbar-hide flex flex-col items-center justify-center text-center">
                <p class="text-slate-800 dark:text-slate-100 font-black uppercase tracking-tight leading-tight" :class="isMaximized ? 'text-3xl md:text-5xl' : 'text-[11px]'" x-text="activeHotspotData?.content"></p>
                
                {{-- Tombol Lihat Video Tutorial (Baru) --}}
                <template x-if="activeHotspotData?.video">
                    <button @click.stop="showVideo = true" class="mt-6 flex items-center space-x-3 px-6 py-4 bg-slate-900 text-blue-400 border-2 border-blue-500/30 rounded-2xl font-game text-xs tracking-[0.2em] btn-pegas-6 shadow-xl hover:scale-105 transition-transform">
                        <span>LIHAT VIDEO TUTORIAL</span>
                    </button>
                </template>
            </div>
        </div>
    </div>

    {{-- Video Overlay --}}
    <div x-show="showVideo && activeHotspotData?.video" x-cloak class="video-box-ultra" @mousedown.stop @touchstart.stop>
        <button @click.stop="showVideo = false" class="absolute top-6 right-6 z-[300] w-12 h-12 bg-red-500 text-white rounded-2xl flex items-center justify-center shadow-2xl active:scale-90 transition-transform">
            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="4"><path d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
        <div class="w-full h-full p-4 md:p-8 flex items-center justify-center">
            <div class="w-full max-w-6xl max-h-[85vh] aspect-video rounded-[2.5rem] overflow-hidden border-4 border-slate-800 shadow-2xl bg-black relative">
                <video :key="activeHotspotData?.video" :src="activeHotspotData?.video" autoplay controls playsinline class="w-full h-full object-contain"></video>
            </div>
        </div>
    </div>

    {{-- Canvas Interaksi --}}
    <div class="simulation-container !p-0" @click="handleBackgroundClick($event)">
        <div class="relative w-full">
            <img :src="steps[currentStep]?.image" class="excel-canvas shadow-2xl" alt="Simulation">
            <template x-for="(hs, index) in (steps[currentStep]?.hotspots || [])" :key="hs.id">
                <button @click.stop="handleHotspotClick(hs, index)" 
                        class="absolute w-12 h-12 -mt-6 -ml-6 transition-all duration-300 z-30 flex items-center justify-center rounded-full shadow-2xl"
                        :class="clickedHotspots.includes(hs.id) ? 'bg-emerald-500/60 border-emerald-400 opacity-100 ring-4 ring-emerald-500/20' : 'opacity-0 bg-transparent border-transparent'"
                        :style="`top: ${hs.y_percent}%; left: ${hs.x_percent}%;`"
                        :disabled="clickedHotspots.includes(hs.id)">
                    <span class="text-white text-[12px] font-black" x-show="clickedHotspots.includes(hs.id)">✔</span>
                </button>
            </template>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.plyr.io/3.7.8/plyr.polyfilled.js"></script>
<script>
    function missionEngine() {
        return {
            currentStep: 0, currentHotspotIndex: 0, clickedHotspots: [],
            activeHotspotData: null, showErrorEffect: false, isMaximized: false, showVideo: false,
            steps: @js($jsonData),
            instX: 40, instY: 100, explX: window.innerWidth - 300, explY: 100,
            isDragging: false, dragTarget: null, attempts: 0,
            currentPotentialXP: {{ $mission->max_score }},
            toast: { show: false, message: '', title: '', icon: '', type: 'info' },

            init() {
                this.$watch('currentPotentialXP', v => { document.getElementById('header-xp-display').innerText = v; });
                this.$watch('currentStep', v => { document.getElementById('header-step-current').innerText = v + 1; });
            },

            triggerToast(title, message, icon = 'bintang.png', type = 'info') {
                this.toast = { show: true, title, message, icon, type };
                setTimeout(() => { this.toast.show = false; }, 3000);
            },

            get isStepComplete() {
                let step = this.steps[this.currentStep];
                return step && this.currentHotspotIndex >= (step.hotspots ? step.hotspots.length : 0);
            },

            startDragging(e, target) {
                if (this.isMaximized && target === 'expl') return;
                this.isDragging = true; this.dragTarget = target;
                let cX = (e.type === 'touchstart') ? e.touches[0].clientX : e.clientX;
                let cY = (e.type === 'touchstart') ? e.touches[0].clientY : e.clientY;
                let tX = (target === 'inst') ? this.instX : this.explX;
                let tY = (target === 'inst') ? this.instY : this.explY;
                this.offX = cX - tX; this.offY = cY - tY;
                
                const move = (e) => {
                    if (!this.isDragging) return;
                    let x = (e.type === 'touchmove') ? e.touches[0].clientX : e.clientX;
                    let y = (e.type === 'touchmove') ? e.touches[0].clientY : e.clientY;
                    this[this.dragTarget + 'X'] = x - this.offX; 
                    this[this.dragTarget + 'Y'] = y - this.offY;
                };
                const stop = () => { this.isDragging = false; document.removeEventListener('mousemove', move); document.removeEventListener('mouseup', stop); };
                document.addEventListener('mousemove', move); document.addEventListener('mouseup', stop);
                document.addEventListener('touchmove', move, { passive: false }); document.addEventListener('touchend', stop);
            },

            handleHotspotClick(hs, index) {
                if (index === this.currentHotspotIndex) {
                    this.clickedHotspots.push(hs.id);
                    this.currentHotspotIndex++;
                    this.activeHotspotData = hs;
                    this.showErrorEffect = false;
                    this.showVideo = false;
                    if (this.isStepComplete) this.triggerToast('Sempurna!', 'Langkah pengerjaan selesai.', 'checklist.png', 'info');
                } else { this.triggerError(); }
            },

            handleBackgroundClick(e) {
                if (this.isStepComplete || this.activeHotspotData) return;
                this.triggerError();
            },

            triggerError() {
                if (this.showErrorEffect) return;
                this.showErrorEffect = true; this.attempts++;
                this.triggerToast('Klik Salah!', 'Perhatikan instruksi kembali.', 'alert.png', 'error');
                if (this.attempts > 3) {
                    let penalty = (this.attempts - 3) * ({{ $mission->max_score }} * 0.05);
                    this.currentPotentialXP = Math.max(Math.floor({{ $mission->max_score }} - penalty), Math.floor({{ $mission->max_score }} * 0.4));
                }
                setTimeout(() => { this.showErrorEffect = false; }, 400);
            },

            nextStep() {
                if (this.currentStep < this.steps.length - 1) { 
                    this.currentStep++; this.currentHotspotIndex = 0; 
                    this.clickedHotspots = []; this.activeHotspotData = null; this.isMaximized = false; this.showVideo = false;
                    document.querySelector('.main-scroller').scrollTop = 0;
                } else { 
                    axios.post("{{ route('misi.check', $mission->id) }}", { answer: 'POINT_CLICK_COMPLETED', attempts: this.attempts })
                    .then(res => { 
                        if (res.data.status === 'success') { 
                            this.triggerToast('Misi Selesai!', res.data.message, 'bintang.png', 'info');
                            setTimeout(() => { window.location.href = res.data.next_url; }, 1500);
                        } 
                    });
                }
            }
        }
    }
</script>
@endpush