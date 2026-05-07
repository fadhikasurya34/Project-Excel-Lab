{{-- //* (View) Engine Simulasi Materi - Optimized UI & Production Ready --}}
@php
    $jsonData = $material->activities->sortBy('step_order')->values()->map(function($step) {
        return [
            'id' => $step->id,
            'instruction' => $step->instruction ?? 'Ikuti instruksi pengerjaan pada layar.',
            'image' => $step->step_image, 
            'hotspots' => $step->hotspots->sortBy('order')->values()->map(function($hs, $index) {
                return [
                    'id' => $hs->id, 
                    'order_label' => $index + 1,
                    'x' => $hs->x_percent, 
                    'y' => $hs->y_percent,
                    'content' => $hs->content, 
                    'video' => $hs->video_path ?: null 
                ];
            })->toArray()
        ];
    })->toArray();
@endphp

@extends('layouts.siswa')

@section('title', $material->title . ' - Simulasi')

@push('styles')
{{-- FIX: Menghilangkan error favicon 404 di konsol --}}
<link rel="icon" href="data:;base64,iVBORw0KGgo=">

<style>
    /* //* (Guard) Mode Landscape Mobile - WAJIB */
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

    /* //* (Layout) Scroll Fix & Anti-Gesture */
    .simulation-wrapper { 
        position: relative; 
        width: 100%; 
        min-height: calc(100vh - 160px); 
        touch-action: none; 
    }
    .main-scroller { 
        overflow-y: auto !important; 
        overflow-x: hidden !important; 
        -webkit-overflow-scrolling: touch;
        height: 100%;
    }

    /* //* (Fix) Navigasi Lock saat Dragging */
    body.is-dragging a, body.is-dragging button:not(.hud-btn) { 
        pointer-events: none !important; 
    }

    /* //* (Visual) Glassmorphism Theme */
    .glass-ui-shared {
        background: rgba(15, 23, 42, 0.95); backdrop-filter: blur(12px);
        border: 2px solid #3b82f6; 
        border-radius: 1.8rem; overflow: hidden;
    }

    /* //* (Visual Update) Compact HUD container */
    .hud-controller { position: fixed; z-index: 90; width: 280px; pointer-events: auto; }
    
    .modal-overlay {
        position: fixed; inset: 0; z-index: 200; background: rgba(15, 23, 42, 0.85);
        display: flex; align-items: center; justify-content: center; padding: 1.5rem;
    }
    .modal-scroll { overflow-y: auto; padding: 1.25rem; flex: 1; }

    /* //* (Buttons) Gamified Pegas */
    .btn-pegas-blue { background: #2563eb; border-bottom: 4px solid #1e3a8a; transition: all 0.1s; }
    .btn-pegas-blue:active { transform: translateY(2px); border-bottom-width: 1px; }
    .btn-pegas-emerald { background: #10b981; border-bottom: 4px solid #064e3b; transition: all 0.1s; }
    .btn-pegas-emerald:active { transform: translateY(2px); border-bottom-width: 1px; }

    .btn-menu-pegas {transition: all 0.1s ease; border-bottom-width: 6px;}
    .btn-menu-pegas:active {transform: translateY(4px);border-bottom-width: 2px;}
    .btn-back-pegas {transition: all 0.1s ease;border-bottom-width: 6px;}
    .btn-back-pegas:active {transform: translateY(2px);border-bottom-width: 0px;}
    

    /* //* (Visual Update) TEBAL Neon Smooth effect (Biru/Ungu) */
    @keyframes neon-materi-smooth {
        0%, 100% {
            box-shadow: 0 0 20px rgba(59, 130, 246, 0.6), inset 0 0 10px rgba(59, 130, 246, 0.3);
            border-color: #3b82f6;
            filter: drop-shadow(0 0 2px rgba(59, 130, 246, 0.5));
        }
        50% {
            box-shadow: 0 0 50px rgba(59, 130, 246, 0.9), 0 0 25px rgba(168, 85, 247, 0.8), inset 0 0 30px rgba(168, 85, 247, 0.5);
            border-color: #ffffff;
            filter: drop-shadow(0 0 12px rgba(59, 130, 246, 0.9));
            transform: scale(1.02);
        }
    }

    .neon-attention-container {
        animation: neon-materi-smooth 2.5s infinite ease-in-out;
    }

    /* //* (Hotspot) Marker Ring */
    .marker-ring {
        position: absolute; 
        width: 34px; 
        height: 34px; 
        margin-top: -17px; 
        margin-left: -17px;
        border-radius: 50%; 
        border: 3px solid white;
        background: transparent !important;
        display: flex; 
        align-items: center; 
        justify-content: center;
        font-weight: 900; 
        font-size: 11px; 
        transition: all 0.3s; 
        z-index: 20;
    }
   .marker-active { 
    border-color: #f59e0b !important;
    box-shadow: 0 0 15px rgba(245, 158, 11, 0.4); 
    animation: pulse-border 1.5s infinite; 
    cursor: pointer; 
    }
    .marker-done { 
        border-color: #10b981 !important; 
        opacity: 1;
        background: transparent !important;
    }

    @keyframes pulse-border { 
        0% { box-shadow: 0 0 0 0px rgba(245, 158, 11, 0.4); } 
        70% { box-shadow: 0 0 0 10px rgba(245, 158, 11, 0); } 
        100% { box-shadow: 0 0 0 0px rgba(245, 158, 11, 0); } 
    }

    .video-overlay {
        position: fixed; inset: 0; z-index: 300; background: rgba(15, 23, 42, 0.7);
        display: flex; align-items: center; justify-content: center; padding: 1rem;
    }
    .video-window-small { width: 100%; max-width: 450px; }

    .toast-top {
        position: fixed; 
        top: 4.5rem; 
        left: 50%; 
        transform: translateX(-50%);
        z-index: 1000; 
        background: white; 
        border-radius: 1.2rem; 
        border: 2px solid #3b82f6; 
        min-width: 250px; 
        padding: 0.6rem 1.2rem; 
        text-align: center;
        animation: toast-down 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }
    .dark .toast-top { background: #1e293b; border-color: #3b82f6; }
    @keyframes toast-down { 
        from { transform: translate(-50%, -150%) scale(0.8); opacity: 0; } 
        to { transform: translate(-50%, 0) scale(1); opacity: 1; } 
    }
</style>
@endpush

@section('header_left')
    <a href="{{ route('materi.index') }}" class="p-2 bg-white dark:bg-slate-800 border-2 border-slate-200 dark:border-slate-700 rounded-xl btn-back-pegas text-slate-600 dark:text-slate-300 shadow-sm active:scale-90 transition-transform">
        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
            <path d="M15 19l-7-7 7-7" />
        </svg>
    </a>
    <div class="flex flex-col text-left ml-3 leading-none">
        <span class="text-base font-extrabold tracking-tight dark:text-white uppercase">{{ $material->title }}</span>
        <div class="flex items-center space-x-1.5 mt-1.5">
            <span class="w-1.5 h-1.5 bg-blue-500 rounded-full animate-pulse"></span>
            <span class="text-[7px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest leading-none">Simulasi Laboratorium Aktif</span>
        </div>
    </div>
@endsection

@section('content')
<div x-data="labInteraction()" class="relative w-full min-h-screen main-scroller bg-slate-950">

    {{-- 1. Landscape Rotary Guard --}}
    <div id="landscape-notice">
        <div class="phone-rotate mb-6 relative">
            <div class="absolute -inset-6 bg-blue-500/20 rounded-full blur-3xl animate-pulse"></div>
            <svg class="relative w-20 h-20 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
            </svg>
        </div>
        <h2 class="font-bold text-2xl uppercase mb-2">Putar Layar</h2>
        <p class="text-slate-400 text-[9px] uppercase tracking-widest">Mode Landscape diperlukan untuk simulasi interaktif.</p>
    </div>

    {{-- Toast System --}}
    <div x-show="toast.show" x-cloak x-transition.opacity class="toast-top shadow-xl flex items-center space-x-3 neon-attention-container">
        <div class="w-8 h-8 rounded-lg flex items-center justify-center bg-blue-50 dark:bg-slate-900/50 shrink-0">
            <img :src="'{{ asset('images') }}/' + toast.icon" class="w-5 h-5 object-contain animate-bounce">
        </div>
        <div class="text-left flex-1">
            <p class="text-[12px] font-black text-slate-900 dark:text-white uppercase leading-none" x-text="toast.title"></p>
            <p class="text-[10px] font-bold text-slate-500 dark:text-slate-400 mt-1 leading-tight" x-text="toast.message"></p>
        </div>
    </div>

    {{-- HUD Instruksi --}}
    <div class="hud-controller" :style="`top: ${boxY}px; left: ${boxX}px;`"
         @mousedown.stop="startDragging($event)" @touchstart.stop="startDragging($event)">
        
        <div class="glass-ui-shared neon-attention-container">
            <div class="p-3 border-b border-white/10 flex justify-between items-center bg-blue-500/10">
                <div class="flex items-center space-x-2">
                    <img src="{{ asset('images/misi.png') }}" class="w-3.5 h-3.5">
                    <span class="text-[8px] font-black text-blue-400 uppercase tracking-widest">Instruksi</span>
                </div>
                <button @click="isExpanded = !isExpanded" class="text-white hud-btn" @mousedown.stop @touchstart.stop>
                    <svg x-show="isExpanded" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path d="M19 9l-7 7-7-7" /></svg>
                    <svg x-show="!isExpanded" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path d="M5 15l7-7 7 7" /></svg>
                </button>
            </div>

            <div class="p-4" x-show="isExpanded" x-collapse>
                {{-- FIX: Kembalikan Teks Instruksi yang hilang --}}
                <p class="text-white text-[15px] font-black leading-tight mb-4 tracking-tight" x-text="steps[currentStep] ? steps[currentStep].instruction : ''"></p>

                <div class="flex flex-row gap-2" x-show="activeHotspot" x-transition.scale.origin.top>
                    {{-- FIX: Tambahkan @mousedown.stop agar tombol bisa diklik tanpa memicu drag --}}
                    <button @click="showModal = true" 
                            @mousedown.stop @touchstart.stop
                            class="hud-btn flex-1 py-2.5 btn-pegas-blue text-white rounded-xl font-black text-[9px] shadow-lg">
                        Penjelasan
                    </button>
                    
                    <template x-if="allHotspotsInStepDone">
                        <button @click="nextStep()" 
                                @mousedown.stop @touchstart.stop
                                class="hud-btn flex-1 py-2.5 btn-pegas-emerald text-white rounded-xl font-black text-[9px] shadow-lg">
                            Lanjut
                        </button>
                    </template>
                </div>
            </div>
        </div>
    </div>

    {{-- Sisanya tetap sesuai kode asli (Modal, Video, Canvas) --}}
    <div x-show="showModal" x-cloak class="modal-overlay" x-transition.opacity>
        <div class="glass-ui-shared w-full max-w-[550px] max-h-[80vh] flex flex-col shadow-2xl">
            <div class="p-4 border-b border-white/10 flex justify-between items-center bg-blue-500/10">
                <div class="flex items-center space-x-2">
                    <img src="{{ asset('images/find.png') }}" class="w-4 h-4">
                    <span class="text-[9px] font-black text-emerald-400 uppercase tracking-widest">Informasi Lab</span>
                </div>
                <button @click="showModal = false" class="p-2 bg-red-500/20 text-red-400 rounded-xl active:scale-90 transition-all">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="modal-scroll scrollbar-hide text-center">
                <p class="text-slate-100 font-bold text-sm leading-relaxed" x-text="activeHotspot?.content"></p>
                <template x-if="activeHotspot?.video">
                    <button @click="showVideo = true" class="mt-6 w-full py-4 bg-slate-800/50 text-blue-400 rounded-2xl font-black text-[10px] border-2 border-blue-500/30 border-b-4">
                        Putar Tutorial Video
                    </button>
                </template>
            </div>
        </div>
    </div>

    <div x-show="showVideo" x-cloak class="video-overlay" x-transition.fade>
        <div class="relative w-full max-w-[450px]">
            <div class="glass-ui-shared video-window-small shadow-2xl w-full">
                <div class="p-3 border-b border-white/10 flex items-center bg-blue-500/10">
                    <span class="text-[8px] font-black text-blue-400 uppercase tracking-widest ml-2">Video Player</span>
                </div>
                <div class="bg-black overflow-hidden">
                    <video :key="activeHotspot?.video" :src="activeHotspot?.video" controls autoplay class="w-full aspect-video object-contain"></video>
                </div>
            </div>
            <button @click="showVideo = false" class="absolute -bottom-5 -right-2 p-2 bg-red-600 text-white rounded-xl shadow-lg active:scale-90 transition-all z-[310] border-2 border-red-400">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="4"><path d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
    </div>

    <div class="simulation-wrapper !p-0">
        <div class="relative w-full inline-block">
            <img :src="steps[currentStep] ? steps[currentStep].image : ''" class="w-full h-auto block select-none shadow-2xl">
            <template x-for="(hs, index) in (steps[currentStep] ? steps[currentStep].hotspots : [])" :key="hs.id">
                <div class="marker-ring" 
                    :class="getMarkerClass(hs, index)"
                    :style="`top: ${hs.y}%; left: ${hs.x}%;`"
                    @click="handleInteraction(hs, index)">
                    <span class="text-amber-500" x-show="!clickedHotspots.includes(hs.id)" x-text="index + 1"></span>
                    <span class="text-emerald-500" x-show="clickedHotspots.includes(hs.id)">✔</span>
                </div>
            </template>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function labInteraction() {
        return {
            currentStep: 0, currentOrder: 0, clickedHotspots: [],
            activeHotspot: null, isExpanded: true, showModal: false, showVideo: false,
            boxX: 20, boxY: 80, isDragging: false, offX: 0, offY: 0,
            steps: @json($jsonData),
            toast: { show: false, title: '', message: '', icon: 'bintang.png' },
            storageKey: 'material_{{ $material->id }}_progress',

            init() {
                const savedData = localStorage.getItem(this.storageKey);
                if (savedData) {
                    try {
                        const parsed = JSON.parse(savedData);
                        this.currentStep = parsed.currentStep ?? 0;
                        this.currentOrder = parsed.currentOrder ?? 0;
                        this.clickedHotspots = parsed.clickedHotspots ?? [];
                        if (this.currentOrder > 0 && this.steps[this.currentStep] && this.steps[this.currentStep].hotspots[this.currentOrder - 1]) {
                            this.activeHotspot = this.steps[this.currentStep].hotspots[this.currentOrder - 1];
                        }
                    } catch (e) {
                        console.error("Gagal memuat progres:", e);
                    }
                }
            },
            saveProgress() {
                const payload = {
                    currentStep: this.currentStep,
                    currentOrder: this.currentOrder,
                    clickedHotspots: this.clickedHotspots
                };
                localStorage.setItem(this.storageKey, JSON.stringify(payload));
            },
            get allHotspotsInStepDone() {
                let currentStepData = this.steps[this.currentStep];
                return currentStepData && this.clickedHotspots.length === currentStepData.hotspots.length;
            },
            handleInteraction(hs, index) {
                if (index === this.currentOrder) {
                    this.activeHotspot = hs;
                    if (!this.clickedHotspots.includes(hs.id)) {
                        this.clickedHotspots = [...this.clickedHotspots, hs.id];
                        this.currentOrder++;
                        this.saveProgress();
                    }
                }
            },
            showToast(title, message, icon = 'bintang.png') {
                this.toast = { show: true, title, message, icon };
                setTimeout(() => { this.toast.show = false; }, 3500);
            },
            nextStep() {
                if (this.currentStep < this.steps.length - 1) {
                    this.currentStep++;
                    this.currentOrder = 0;
                    this.clickedHotspots = [];
                    this.activeHotspot = null;
                    this.showModal = false;
                    this.saveProgress();
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                } else {
                    localStorage.removeItem(this.storageKey);
                    this.showToast('Misi Selesai', 'Seluruh materi simulasi telah dipelajari.', 'bintang.png');
                    setTimeout(() => { window.location.href = "{{ route('materi.index') }}"; }, 4000);
                }
            },
            getMarkerClass(hs, index) {
                if (this.clickedHotspots.includes(hs.id)) return 'marker-done';
                if (index === this.currentOrder) return 'marker-active';
                return 'opacity-0 pointer-events-none';
            },
            startDragging(e) {
                // FIX: Cek apakah yang diklik adalah tombol. Jika YA, jangan jalankan drag.
                if (e.target.closest('button')) return;

                e.preventDefault(); 
                this.isDragging = true;
                document.body.classList.add('is-dragging');

                let cX = (e.type === 'touchstart') ? e.touches[0].clientX : e.clientX;
                let cY = (e.type === 'touchstart') ? e.touches[0].clientY : e.clientY;
                this.offX = cX - this.boxX;
                this.offY = cY - this.boxY;
                const move = (e) => {
                    if (!this.isDragging) return;
                    let x = (e.type === 'touchmove') ? e.touches[0].clientX : e.clientX;
                    let y = (e.type === 'touchmove') ? e.touches[0].clientY : e.clientY;
                    this.boxX = x - this.offX;
                    this.boxY = y - this.offY;
                };
                const stop = () => { 
                    setTimeout(() => { 
                        this.isDragging = false; 
                        document.body.classList.remove('is-dragging');
                    }, 100);
                    document.removeEventListener('mousemove', move); 
                    document.removeEventListener('touchmove', move); 
                };
                document.addEventListener('mousemove', move);
                document.addEventListener('touchmove', move, { passive: false });
                document.addEventListener('mouseup', stop);
                document.addEventListener('touchend', stop);
            }
        }
    }
</script>
@endpush