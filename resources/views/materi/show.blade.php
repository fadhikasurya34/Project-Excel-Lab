{{-- //* (View) Engine Simulasi Materi Interaktif --}}

@php
    // //* (Process) Mapping data Eloquent ke JSON untuk Alpine.js */
    $jsonData = $material->activities->sortBy('step_order')->values()->map(function($step) {
        return [
            'id' => $step->id,
            'instruction' => $step->instruction ?? 'Ikuti instruksi pengerjaan studi kasus pada layar.',
            'image' => asset('storage/' . $step->step_image),
            'hotspots' => $step->hotspots->sortBy('order')->values()->map(function($hs) {
                return [
                    'id' => $hs->id, 
                    'x' => $hs->x_percent, 
                    'y' => $hs->y_percent,
                    'content' => $hs->content, 
                    'video' => $hs->video_path ? asset('storage/' . $hs->video_path) : null
                ];
            })->toArray()
        ];
    })->toArray();
@endphp

@extends('layouts.siswa')

@section('title', $material->title . ' - Simulasi')

@push('styles')
{{-- Meida Plyr CSS <style> --}}
<link rel="stylesheet" href="https://cdn.plyr.io/3.7.8/plyr.css" />

<style>
    /* //* (Guard) Mode Landscape */
    #landscape-notice { display: none; }
    @media screen and (orientation: portrait) and (max-width: 1024px) {
        #landscape-notice {
            display: flex; position: fixed; inset: 0; z-index: 9999;
            background: rgba(15, 23, 42, 0.95); backdrop-filter: blur(20px);
            flex-direction: column; align-items: center; justify-content: center;
            padding: 2rem; text-align: center; color: white;
        }
    }

    /* //* (Layout) Canvas & Scroller */
    .simulation-wrapper {
        position: relative; width: 100%; padding: 1.5rem;
        min-height: calc(100vh - 160px);
    }
    .excel-canvas { 
        width: 100%; height: auto; display: block; user-select: none; 
        border-radius: 1.5rem; box-shadow: 0 20px 50px -12px rgba(0, 0, 0, 0.2);
    }
    .main-scroller { 
        overflow-y: auto !important; overflow-x: hidden !important; 
        touch-action: pan-y !important; -webkit-overflow-scrolling: touch;
    }

    /* //* (Drag) */
    .draggable-box { 
        position: fixed; z-index: 30 !important; width: 330px; 
        pointer-events: none;
    }
    .bubble-interact, .glass-card { 
        pointer-events: auto !important; cursor: grab; user-select: none; touch-action: none; 
    }
    .bubble-interact:active, .glass-card:active { cursor: grabbing; }

    /* //* (UI) Tombol Pegas */
    .btn-pegas-6 { transition: all 0.1s ease; border-bottom-width: 6px !important; }
    .btn-pegas-6:active { transform: translateY(4px); border-bottom-width: 2px !important; }

    /* //* (Monitor) Tampilan Cinema Mode Video */
    .video-box-ultra { 
        position: fixed; 
        z-index: 250; 
        inset: 0;
        background: rgba(0, 0, 0, 0.95); 
        backdrop-filter: blur(15px);
        display: flex; 
        flex-direction: column; 
        align-items: center; 
        justify-content: center;
    }
    .video-box-ultra video {
        max-height: 100%;
        width: 100%;
    }

/* //* (Notification) Compact Top-Center Toast */
    .toast-top {
        position: fixed; 
        top: 1.5rem; 
        left: 50%; 
        transform: translateX(-50%);
        z-index: 1000; 
        background: white; 
        border-radius: 1.5rem;
        border: 2px solid #3b82f6; 
        border-bottom: 6px solid #1d4ed8;
        min-width: 260px; 
        padding: 0.8rem 1.2rem; 
        text-align: center;
        animation: toast-down 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }
    
    .dark .toast-top { background: #1e293b; border-color: #3b82f6; border-bottom-color: #1e40af; }

    @keyframes toast-down { 
        from { transform: translate(-50%, -100%) scale(0.8); opacity: 0; } 
        to { transform: translate(-50%, 0) scale(1); opacity: 1; } 
    }

    /* //* (Panel) Maximized State */
    .maximized-panel { 
        position: fixed !important; inset: 1.5rem !important;
        width: auto !important; height: auto !important; 
        max-width: none !important; z-index: 50 !important; 
        border-radius: 2.5rem !important; overflow: hidden;
    }

    /* //* (Misc) Fonts & Animations */
    .font-game { font-family: 'Bangers', cursive; }
    .phone-rotate { animation: rotatePhone 2s ease-in-out infinite; }
    @keyframes rotatePhone { 0%, 100% { transform: rotate(0deg); } 50% { transform: rotate(90deg); } }

    .glass-card { transition: all 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275); }
    .card-silhouette {
        position: absolute; top: -0.5rem; right: -0.5rem; font-family: 'Bangers', cursive;
        font-size: 5rem; line-height: 1; opacity: 0.05; transform: rotate(15deg);
        pointer-events: none; z-index: 0; color: #64748b; white-space: nowrap;
    }

    /* //* (Plyr) Kustomisasi Player */
    :root {
        --plyr-color-main: #4f46e5;
        --plyr-control-radius: 12px;
    }
    .plyr--video { border-radius: 1.5rem; }
</style>
@endpush

@section('header_left')
    {{-- //* (Nav) Kontrol navigasi & status modul aktif */ --}}
    <a href="{{ route('materi.index') }}" class="p-2 bg-white dark:bg-slate-800 border-2 border-slate-200 dark:border-slate-700 rounded-xl btn-pegas-6 text-slate-600 dark:text-slate-300 shadow-sm">
        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3.5"><path d="M15 19l-7-7 7-7" /></svg>
    </a>
    
    <div class="flex flex-col text-left ml-3 leading-none">
        <span class="text-base font-extrabold tracking-tight dark:text-white uppercase leading-none">{{ $material->title }}</span>
        <div class="flex items-center space-x-1.5 mt-1.5">
            <span class="w-1.5 h-1.5 bg-blue-500 rounded-full animate-pulse"></span>
            <span class="text-[7px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest leading-none">Simulasi Laboratorium Aktif</span>
        </div>
    </div>
@endsection

@section('content')

    {{-- 1. Blocker mode portrait --}}
    <div id="landscape-notice" class="fixed inset-0 z-[9999] hidden flex-col items-center justify-center bg-slate-950/95 backdrop-blur-2xl text-white p-8 text-center">
        <div class="phone-rotate mb-6 relative">
            <div class="absolute -inset-6 bg-blue-500/20 rounded-full blur-3xl animate-pulse"></div>
            <svg class="relative w-20 h-20 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
            </svg>
        </div>
        <h2 class="font-game text-3xl tracking-widest uppercase mb-2">Putar Layar</h2>
        <p class="text-slate-400 text-xs font-bold uppercase tracking-widest max-w-[250px]">Mode Landscape diperlukan untuk simulasi materi.</p>
    </div>

<div x-data="labInteraction()" class="relative w-full min-h-screen overflow-hidden main-scroller">

    {{-- 2. Toast Feedback (Fixed Top-Center - Style MISI) --}}
    <div x-show="toast.show" x-cloak x-transition.opacity 
        class="toast-top shadow-xl flex items-center space-x-3" 
        :style="toast.type === 'error' ? 'border-color: #ef4444; border-bottom-color: #b91c1c;' : 'border-color: #10b981; border-bottom-color: #047857;'">
        <div class="w-10 h-10 rounded-xl flex items-center justify-center shadow-inner bg-slate-50 dark:bg-slate-900/50 overflow-hidden">
            <img :src="'{{ asset('images') }}/' + toast.icon" 
                 class="w-7 h-7 object-contain animate-bounce">
        </div>
        
        <div class="text-left flex-1">
            <p class="text-[10px] font-black text-slate-900 dark:text-white uppercase leading-none" x-text="toast.title"></p>
            <p class="text-[9px] font-bold text-slate-500 dark:text-slate-400 mt-1" x-text="toast.message"></p>
        </div>
    </div>


    {{-- 3. Panel Instruksi (Draggable) --}}
    <div class="draggable-box" 
        :style="`top: ${instY}px; left: ${instX}px; z-index: 40;`" 
        @mousedown.stop="startDragging($event, 'inst')" 
        @touchstart.stop="startDragging($event, 'inst')">
        
        <div class="pointer-events-auto">
            <div class="bubble-interact w-[280px] bg-blue-600 text-white p-5 rounded-[2rem] shadow-2xl border-b-[6px] border-blue-800 flex flex-col space-y-2">
                <div class="flex items-center justify-between opacity-60">
                    <div class="flex items-center space-x-2">
                        <img src="{{ asset('images/misi.png') }}" class="w-4 h-4 object-contain">
                        <span class="text-[9px] font-black uppercase tracking-[0.2em]">Instruksi Lab</span>
                    </div>
                    <span class="text-[7px] font-bold uppercase">Drag</span>
                </div>
                <p class="text-sm font-extrabold leading-tight uppercase" x-text="steps[currentStep].instruction"></p>
            </div>

            {{-- Tombol Lanjut --}}
            <div class="w-full mt-4" x-show="allHotspotsInStepDone" x-cloak>
                <button type="button"
                        @click.stop="nextStep()" 
                        class="w-full py-4 bg-emerald-600 text-white rounded-[1.5rem] font-black text-[10px] uppercase tracking-widest btn-pegas-6 border-emerald-800 shadow-xl animate-pulse cursor-pointer relative z-[50]">
                    Lanjut Eksplorasi
                </button>
            </div>
        </div>
    </div>

    {{-- 4. Panel Feedback/Analisis (Draggable & Maximizable - Style MISI) --}}
    <div x-show="activeHotspotData !== null" x-cloak
         class="draggable-box transition-all duration-500" 
         :class="isMaximized ? 'maximized-panel' : 'w-[280px] shadow-2xl'" 
         :style="isMaximized ? '' : `top: ${explY}px; left: ${explX}px; z-index: 50; pointer-events: auto;`"
         @mousedown.stop="!isMaximized && startDragging($event, 'expl')" 
         @touchstart.stop="!isMaximized && startDragging($event, 'expl')">
        
        <div class="w-full h-full bg-white dark:bg-slate-900 border-2 border-emerald-500/50 rounded-[2rem] shadow-2xl overflow-hidden flex flex-col pointer-events-auto">
            <div class="px-5 py-4 flex items-center justify-between border-b dark:border-slate-800 bg-slate-50/50 dark:bg-slate-950/30">
                <div class="flex items-center space-x-2">
                    <img src="{{ asset('images/find.png') }}" class="w-4 h-4 object-contain animate-pulse">
                    <span class="text-[9px] font-black text-emerald-600 dark:text-emerald-400 uppercase tracking-widest">Informasi Lab</span>
                </div>
                
                <div class="flex items-center space-x-3">
                    <button @click.stop="isMaximized = !isMaximized" class="p-2 bg-white dark:bg-slate-800 border-2 border-slate-100 rounded-xl active:scale-90 transition-all">
                        <svg x-show="!isMaximized" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5v-4m0 4h-4m4 0l-5-5"/></svg>
                        <svg x-show="isMaximized" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path d="M9 4v5m0 0H4m5 0L4 4m11 0v5m0 0h5m-5 0l5-5M9 20v-5m0 0H4m5 0l-5 5m11 0v-5m0 0h5m-5 0l5 5"/></svg>
                    </button>
                    <button @click.stop="activeHotspotData = null; isMaximized = false; showVideo = false" class="p-2 bg-red-50 text-red-500 rounded-xl border-2 border-red-100 active:scale-90 transition-all">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
            </div>

            <div class="p-8 flex-1 overflow-y-auto scrollbar-hide flex flex-col items-center justify-center text-center">
                <div class="max-w-3xl mx-auto flex flex-col items-center">
                    <p class="text-slate-800 dark:text-slate-100 font-black uppercase tracking-tight leading-tight" 
                       :class="isMaximized ? 'text-3xl md:text-5xl px-4' : 'text-[11px]'">
                        <span x-text="activeHotspotData?.content"></span>
                    </p>
                    
                    <div x-show="isMaximized" class="mt-8 h-1 w-20 bg-emerald-500/30 mx-auto rounded-full"></div>

                    <template x-if="activeHotspotData?.video">
                        <button @click.stop="showVideo = true" 
                                class="mt-6 flex items-center space-x-3 px-6 py-4 bg-slate-900 text-blue-400 border-2 border-blue-500/30 rounded-2xl font-game text-xs tracking-[0.2em] btn-pegas-6 shadow-xl hover:scale-105 transition-transform">
                            <span> LIHAT VIDEO TUTORIAL</span>
                        </button>
                    </template>
                </div>
            </div>
        </div>
    </div>

    {{-- 5. Video Overlay --}}
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

    {{-- 6. Simulation Canvas (Full Mode) --}}
    <div class="simulation-wrapper !p-0">
        <div class="relative w-full">
            <img :src="steps[currentStep].image" 
                 class="w-full h-auto block select-none shadow-2xl" 
                 alt="Simulation">
            
            <template x-for="(hs, index) in steps[currentStep].hotspots" :key="hs.id">
                <button @click.stop="handleInteraction(hs, index)" 
                        class="absolute w-12 h-12 -mt-6 -ml-6 border-[3px] rounded-full transition-all duration-300 z-20 flex items-center justify-center shadow-2xl" 
                        :class="getHotspotStyle(hs, index)" 
                        :style="'top: ' + hs.y + '%; left: ' + hs.x + '%;'">
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
    function labInteraction() {
        return {
            // //* (State) Dasar Simulasi */
            category: "{{ $material->category }}", 
            currentStep: 0, 
            currentHotspotOrder: 0, 
            clickedHotspots: [],
            activeHotspotId: null, 
            activeHotspotData: null,
            steps: @json($jsonData),
            
            // //* (State) UI Control */
            sidebarOpen: false, 
            isMaximized: false, 
            showVideo: false,    
            showMainVideo: false, 
            
            // //* (State) Draggable Coordinates */
            instX: 40, instY: 120, 
            explX: window.innerWidth - 380, explY: 120,
            toastX: window.innerWidth / 2 - 140, toastY: 20, 
            isDragging: false, 
            dragTarget: null, 
            offX: 0, offY: 0,

            // //* (State) Toast System */
            toast: { show: false, title: '', message: '', type: 'info', icon: 'bintang.png' },

            // //* (Computed) */
            get allHotspotsInStepDone() { 
                let step = this.steps[this.currentStep]; 
                return step && this.currentHotspotOrder >= step.hotspots.length; 
            },

            // //* (Logic) Toast Helper */
            showToast(title, message, type = 'info', icon = 'bintang.png') {
                this.toast = { show: true, title, message, type, icon };
                setTimeout(() => { this.toast.show = false; }, 3500);
            },

            // //* (Logic) Universal Draggable System */
            startDragging(e, target) {
                if (this.isMaximized && target === 'expl') return;
                
                this.isDragging = true; 
                this.dragTarget = target;
                
                let cX = (e.type === 'touchstart') ? e.touches[0].clientX : e.clientX;
                let cY = (e.type === 'touchstart') ? e.touches[0].clientY : e.clientY;
                
                // Ambil koordinat target secara dinamis
                this.offX = cX - this[target + 'X']; 
                this.offY = cY - this[target + 'Y'];
                
                const move = (e) => {
                    if (!this.isDragging) return;
                    let x = (e.type === 'touchmove') ? e.touches[0].clientX : e.clientX;
                    let y = (e.type === 'touchmove') ? e.touches[0].clientY : e.clientY;
                    
                    // Update koordinat target
                    this[this.dragTarget + 'X'] = x - this.offX; 
                    this[this.dragTarget + 'Y'] = y - this.offY; 
                };

                const stop = () => { 
                    this.isDragging = false; 
                    this.dragTarget = null;
                    document.removeEventListener('mousemove', move); 
                    document.removeEventListener('mouseup', stop); 
                    document.removeEventListener('touchmove', move); 
                    document.removeEventListener('touchend', stop); 
                };

                document.addEventListener('mousemove', move); 
                document.addEventListener('mouseup', stop);
                document.addEventListener('touchmove', move, { passive: false }); 
                document.addEventListener('touchend', stop);
            },

            // //* (Logic) Visual Hotspot */
            getHotspotStyle(hs, index) {
                if (this.clickedHotspots.includes(hs.id)) return 'bg-emerald-500/60 border-emerald-400 ring-4 ring-emerald-500/20';
                if (index === this.currentHotspotOrder) { 
                    return 'bg-amber-500/40 border-amber-400 animate-pulse scale-110 z-30 cursor-pointer'; 
                }
                return 'opacity-0 pointer-events-none';
            },

            // //* (Logic) Interaction */
            handleInteraction(hs, index) {
                if (index === this.currentHotspotOrder) { 
                    if (!this.clickedHotspots.includes(hs.id)) {
                        this.clickedHotspots.push(hs.id); 
                    }
                    this.currentHotspotOrder++; 
                    this.activeHotspotId = hs.id; 
                    this.activeHotspotData = hs; 
                    this.showVideo = false;

                    this.$nextTick(() => {
                        const container = document.querySelector('.scrollbar-hide');
                        if (container) container.scrollTop = 0;
                    });
                } else {
                    this.showToast('Urutan Salah', 'Ikuti prosedur simulasi secara bertahap.', 'error', 'alert.png');
                }
            },

            // //* (Logic) Navigation */
            nextStep() {
                if (this.currentStep < this.steps.length - 1) { 
                    this.currentStep++; 
                    this.currentHotspotOrder = 0; 
                    this.clickedHotspots = []; 
                    this.activeHotspotId = null; 
                    this.isMaximized = false; 
                    this.showVideo = false;
                    
                    // Reset scroll simulasi ke atas
                    const scroller = document.querySelector('.main-scroller');
                    if (scroller) scroller.scrollTop = 0;
                } else { 
                    this.showToast('Eksplorasi Selesai', 'Materi telah dipahami sepenuhnya. Siap untuk praktik?', 'success', 'bintang.png');
                    setTimeout(() => {
                        window.location.href = "{{ route('materi.index') }}"; 
                    }, 3000);
                }
            }
        }
    }
</script>
@endpush
