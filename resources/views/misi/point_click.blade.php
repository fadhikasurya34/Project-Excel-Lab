{{-- //* (View) Simulasi Misi Point & Click - Optimized Scroll & Gamified --}}

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

    /* //* (Layout) Fix Scroll & Anti-Gesture */
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

    /* //* (Visual) Glassmorphism Theme - Blue */
    .glass-ui-shared {
        background: rgba(15, 23, 42, 0.95); backdrop-filter: blur(12px);
        border: 2px solid #3b82f6; 
        border-radius: 1.8rem; overflow: hidden;
    }

    /* //* (Fix) Proteksi tombol saat Dragging */
    body.is-dragging a, body.is-dragging button:not(.hud-btn) { 
        pointer-events: none !important; 
    }

    /* Update: HUD lebih compact (280px) */
    .hud-controller { position: fixed; z-index: 90; width: 280px; pointer-events: auto; }
    
    /* //* (Animation) Update: Efek Cahaya Neon TEBAL & Smooth (Blue-Emerald) */
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

    /* //* (Buttons) Gamified Pegas */
    .btn-pegas-blue { background: #2563eb; border-bottom: 4px solid #1e3a8a; transition: all 0.1s; }
    .btn-pegas-blue:active { transform: translateY(2px); border-bottom-width: 1px; }
    .btn-pegas-emerald { background: #10b981; border-bottom: 4px solid #064e3b; transition: all 0.1s; }
    .btn-pegas-emerald:active { transform: translateY(2px); border-bottom-width: 1px; }
    .btn-menu-pegas {transition: all 0.1s ease; border-bottom-width: 6px;}
    .btn-menu-pegas:active {transform: translateY(4px);border-bottom-width: 2px;}
    .btn-back-pegas {transition: all 0.1s ease;border-bottom-width: 6px;}
    .btn-back-pegas:active {transform: translateY(2px);border-bottom-width: 0px;}
    
    /* //* (FX) Error Feedback */
    .flash-error { position: fixed; inset: 0; background: rgba(239, 68, 68, 0.2); pointer-events: none; z-index: 100; animation: fade-out 0.4s forwards; }
    .shake { animation: shake 0.4s cubic-bezier(.36,.07,.19,.97) both; }
    @keyframes fade-out { from { opacity: 1; } to { opacity: 0; } }
    @keyframes shake { 10%, 90% { transform: translate3d(-2px, 0, 0); } 20%, 80% { transform: translate3d(4px, 0, 0); } 30%, 50%, 70% { transform: translate3d(-6px, 0, 0); } 40%, 60% { transform: translate3d(6px, 0, 0); } }

    /* //* (Marker) Done State */
    .marker-ring {
        position: absolute; width: 34px; height: 34px; margin-top: -17px; margin-left: -17px;
        border-radius: 50%; border: 2px solid white; display: flex; align-items: center; justify-content: center;
        transition: all 0.3s; z-index: 20; color: white; cursor: pointer;
    }
    .marker-done { background: #10b981; border-color: transparent; opacity: 0.8; cursor: default; }

    /* //* (Notification) FIXED Toast */
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
    .font-game { font-family: 'Bangers', cursive; }

    /* //* (GAMIFICATION UPDATE) Elegant Modal Center */
    .feedback-modal-wrapper {
        position: fixed; inset: 0; z-index: 10000;
        display: flex; align-items: center; justify-content: center;
        pointer-events: none; 
    }
    
    .feedback-modal {
        padding: 2rem 3rem; border-radius: 2rem;
        text-align: center; color: white;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        animation: popModal 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards;
        border-bottom: 8px solid rgba(0,0,0,0.2);
    }
    
    .feedback-modal.success { background: linear-gradient(135deg, #10b981 0%, #059669 100%); }
    .feedback-modal.error { background: linear-gradient(135deg, #ef4444 0%, #b91c1c 100%); }

    .feedback-title {
        font-size: 2rem; font-weight: 800; text-transform: uppercase;
        letter-spacing: 0.02em; margin-bottom: 0.5rem;
    }
    .feedback-subtitle {
        font-size: 1rem; font-weight: 500; opacity: 0.95;
    }
    @media (min-width: 768px) {
        .feedback-title { font-size: 2.5rem; }
        .feedback-subtitle { font-size: 1.1rem; }
    }
    @keyframes popModal {
        0% { transform: scale(0.8) translateY(20px); opacity: 0; }
        100% { transform: scale(1) translateY(0); opacity: 1; }
    }

    /* //* (GAMIFICATION) Floating Text Effect */
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

    {{-- (GAMIFICATION) Elegant Modal Feedback Center --}}
    <div x-show="feedbackModal.show" x-transition.opacity x-cloak class="feedback-modal-wrapper">
        <div class="feedback-modal" :class="feedbackModal.type">
            <div class="feedback-title" x-text="feedbackModal.title"></div>
            <div class="feedback-subtitle" x-text="feedbackModal.subtitle"></div>
        </div>
    </div>

    {{-- 1. Landscape Guard --}}
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

    {{-- FX Flash Error --}}
    <template x-if="showErrorEffect">
        <div class="flash-error"></div>
    </template>

    {{-- Toast (EFEK NEON KONTAINER) --}}
    <div x-show="toast.show" x-cloak x-transition.opacity class="toast-top shadow-xl flex items-center space-x-4 neon-attention-misi">
        <div class="w-10 h-10 rounded-xl flex items-center justify-center bg-blue-50 dark:bg-slate-900/50 shrink-0">
            <img :src="'{{ asset('images') }}/' + toast.icon" class="w-6 h-6 object-contain animate-bounce">
        </div>
        <div class="text-left flex-1">
            <p class="text-[12px] font-black text-slate-900 dark:text-white uppercase leading-none" x-text="toast.title"></p>
            <p class="text-[10px] font-bold text-slate-500 dark:text-slate-400 mt-1 leading-tight" x-text="toast.message"></p>
        </div>
    </div>

    {{-- HUD Instruksi (Draggable, Compact, & BIG TEXT + NEON GLOW) --}}
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

    {{-- Area Simulasi --}}
    <div class="simulation-wrapper !p-0" @click="handleBackgroundClick($event)">
        <div class="relative w-full inline-block">
            <img :src="steps[currentStep] ? steps[currentStep].image : ''" class="w-full h-auto block select-none shadow-2xl">
            
            <template x-for="(hs, index) in (steps[currentStep] ? steps[currentStep].hotspots : [])" :key="hs.id">
                <div class="marker-ring" 
                     :class="clickedHotspots.includes(hs.id) ? 'marker-done' : 'opacity-0'"
                     :style="`top: ${hs.y_percent}%; left: ${hs.x_percent}%;`"
                     @click.stop="handleHotspotClick(hs, index, $event)">
                    <span class="text-white text-xs font-black">✔</span>
                </div>
            </template>
        </div>
    </div>
    
    {{-- Modal Hint --}}
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
{{-- Library Confetti --}}
<script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js"></script>

<script>
    function missionEngine() {
        return {
            currentStep: 0, currentHotspotIndex: 0, clickedHotspots: [], isExpanded: true, 
            showModal: false, showErrorEffect: false, showHintButton: false,
            currentHint: '', boxX: 20, boxY: 80, isDragging: false, offX: 0, offY: 0,
            attempts: 0, currentPotentialXP: {{ $mission->max_score }},
            steps: @json($jsonData),
            toast: { show: false, title: '', message: '', icon: 'bintang.png' },
            feedbackModal: { show: false, type: '', title: '', subtitle: '' },
            
            // OBJEK AUDIO: Disimpan di sini agar bisa didaur ulang, tidak bikin baru terus
            audioPlayers: {
                click: null,
                benar: null,
                salah: null
            },

            storageKey: 'mission_{{ $mission->id }}_progress',
            isReview: {{ (auth()->user()->progress && auth()->user()->progress->where('mission_id', $mission->id)->where('status', 'completed')->isNotEmpty()) ? 'true' : 'false' }},

            init() {
                // INISIALISASI AUDIO 1 KALI SAJA
                try {
                    this.audioPlayers.click = new Audio('{{ asset("audio/click.mp3") }}');
                    this.audioPlayers.benar = new Audio('{{ asset("audio/benar.mp3") }}');
                    this.audioPlayers.salah = new Audio('{{ asset("audio/salah.mp3") }}');
                    
                    // Paksa browser pre-load filenya
                    this.audioPlayers.click.preload = 'auto';
                    this.audioPlayers.benar.preload = 'auto';
                    this.audioPlayers.salah.preload = 'auto';
                } catch(e) {
                    console.log("Audio Engine Error", e);
                }

                if (this.isReview) {
                    this.currentPotentialXP = {{ $mission->max_score }};
                } else {
                    const saved = localStorage.getItem(this.storageKey);
                    if (saved) {
                        const data = JSON.parse(saved);
                        this.currentStep = data.currentStep ?? 0;
                        this.currentHotspotIndex = data.currentHotspotIndex ?? 0;
                        this.clickedHotspots = data.clickedHotspots ?? [];
                        this.attempts = data.attempts ?? 0;
                        this.currentPotentialXP = data.currentPotentialXP ?? {{ $mission->max_score }};
                    } else {
                        this.saveToLocal();
                    }
                }
                this.$watch('currentPotentialXP', v => { 
                    const el = document.getElementById('header-xp-display');
                    if (el) el.innerText = v; 
                });
                this.$watch('currentStep', v => { 
                    const el = document.getElementById('header-step-current');
                    if (el) el.innerText = v + 1; 
                });
                document.getElementById('header-xp-display').innerText = this.currentPotentialXP;
                document.getElementById('header-step-current').innerText = this.currentStep + 1;
            },

            // ENGINE AUDIO YANG SUDAH DIPERBAIKI
            playSound(type) {
                try {
                    let player = this.audioPlayers[type];
                    if (player) {
                        player.currentTime = 0; // Kembalikan ke detik 0
                        let playPromise = player.play();
                        
                        if (playPromise !== undefined) {
                            playPromise.catch(error => {
                                console.log(`Audio ${type} diblokir browser:`, error);
                            });
                        }
                    }
                } catch(e) {}
            },

            saveToLocal() {
                if (this.isReview) return;
                try {
                    const payload = {
                        currentStep: this.currentStep, currentHotspotIndex: this.currentHotspotIndex,
                        clickedHotspots: this.clickedHotspots, attempts: this.attempts,
                        currentPotentialXP: this.currentPotentialXP
                    };
                    localStorage.setItem(this.storageKey, JSON.stringify(payload));
                } catch (e) {}
            },

            get allHotspotsInStepDone() {
                let step = this.steps[this.currentStep];
                return step && this.clickedHotspots.length === step.hotspots.length;
            },

            handleBackgroundClick(e) {
                if (this.allHotspotsInStepDone || this.isDragging) return;
                this.triggerError(e);
            },

            handleHotspotClick(hs, index, e) {
                if (this.isDragging) return;
                if (index === this.currentHotspotIndex) {
                    if (!this.clickedHotspots.includes(hs.id)) {
                        this.clickedHotspots.push(hs.id);
                        this.currentHotspotIndex++;
                        this.showErrorEffect = false;
                        this.showHintButton = false; 
                        this.saveToLocal();

                        // PANGGIL AUDIO (Klik Tepat)
                        this.playSound('click');
                        this.spawnFloatingText(e, '+Tepat', '#10b981');
                    }
                } else { 
                    this.triggerError(e); 
                }
            },

            triggerError(e) {
                if (this.showErrorEffect || this.isDragging) return;
                this.showErrorEffect = true;
                this.attempts++;
                this.showHintButton = true;
                let stepData = this.steps[this.currentStep];
                let correctHotspot = stepData ? stepData.hotspots[this.currentHotspotIndex] : null;
                this.currentHint = correctHotspot ? correctHotspot.content : 'Perhatikan instruksi misi.';
                
                if (!this.isReview && this.attempts > 3) {
                    let penalty = Math.floor({{ $mission->max_score }} * 0.05);
                    this.currentPotentialXP = Math.max(this.currentPotentialXP - penalty, Math.floor({{ $mission->max_score }} * 0.4));
                    this.showToast('Klik Salah', 'Point XP berkurang sedikit.', 'alert.png');
                } else {
                    this.showToast('Klik Salah', this.isReview ? 'Mode Review: XP aman.' : 'Perhatikan instruksi.', 'alert.png');
                }
                this.saveToLocal();

                // PANGGIL AUDIO (Klik Salah)
                this.playSound('salah');
                this.fireCrossParticles();
                if(e) this.spawnFloatingText(e, 'Meleset!', '#ef4444');

                setTimeout(() => { this.showErrorEffect = false; }, 1500);
            },

            showToast(title, message, icon = 'bintang.png') {
                this.toast = { show: true, title, message, icon };
                setTimeout(() => { this.toast.show = false; }, 3500);
            },

            triggerFeedbackModal(type, title, subtitle) {
                this.feedbackModal.type = type;
                this.feedbackModal.title = title;
                this.feedbackModal.subtitle = subtitle;
                this.feedbackModal.show = true;
                setTimeout(() => { this.feedbackModal.show = false; }, 2000);
            },

            fireConfetti() {
                var duration = 4 * 1000; 
                var end = Date.now() + duration;
                (function frame() {
                    confetti({ particleCount: 5, angle: 60, spread: 55, origin: { x: 0 }, colors: ['#10b981', '#3b82f6', '#fbbf24', '#ffffff'] });
                    confetti({ particleCount: 5, angle: 120, spread: 55, origin: { x: 1 }, colors: ['#10b981', '#3b82f6', '#fbbf24', '#ffffff'] });
                    if (Date.now() < end) { requestAnimationFrame(frame); }
                }());
            },

            fireCrossParticles() {
                var defaults = { spread: 360, ticks: 50, gravity: 0, decay: 0.94, startVelocity: 30, colors: ['#ef4444', '#b91c1c'] };
                function fire(particleRatio, opts) {
                    confetti(Object.assign({}, defaults, opts, { particleCount: Math.floor(40 * particleRatio), shapes: ['star'] }));
                }
                fire(0.25, { spread: 26, startVelocity: 55 });
                fire(0.2, { spread: 60 });
            },

            spawnFloatingText(e, text, color = '#fbbf24') {
                if (!e) return;
                
                let clientX = e.clientX;
                let clientY = e.clientY;
                
                if (e.touches && e.touches.length > 0) {
                    clientX = e.touches[0].clientX;
                    clientY = e.touches[0].clientY;
                } else if (e.changedTouches && e.changedTouches.length > 0) {
                    clientX = e.changedTouches[0].clientX;
                    clientY = e.changedTouches[0].clientY;
                }

                if (clientX === undefined || clientY === undefined) return;

                const el = document.createElement('div');
                el.className = 'floating-text';
                el.innerText = text;
                el.style.left = (clientX - 20) + 'px';
                el.style.top = (clientY - 20) + 'px';
                el.style.color = color;
                document.body.appendChild(el);
                setTimeout(() => el.remove(), 1000);
            },

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
                    axios.post("{{ route('misi.check', $mission->id) }}", { 
                        answer: 'MISSION_COMPLETED', attempts: this.attempts 
                    }).then(res => {
                        localStorage.removeItem(this.storageKey);

                        // PANGGIL AUDIO (Misi Selesai)
                        this.playSound('benar');
                        this.fireConfetti();
                        this.triggerFeedbackModal('success', 'Misi Selesai!', '+ ' + this.currentPotentialXP + ' XP Diraih');

                        setTimeout(() => { window.location.href = res.data.next_url; }, 4000);
                    });
                }
            },

            startDragging(e, target) {
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