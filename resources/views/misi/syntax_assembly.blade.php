{{-- //* (View) Lab Perakitan Rumus (Syntax Assembly) - Cloudinary Ready & Optimized Layout */ --}}

@extends('layouts.siswa')

@section('title', $mission->title . ' - Virtual Lab')

@push('styles')
<style>
    /* //* (Lockdown) Stabilisasi layout utama */
    .split-grid { 
        display: grid; 
        grid-template-columns: 1fr; 
        gap: 1rem; 
        padding: 1rem;
        min-height: calc(100vh - 150px); 
    }

    @media (min-width: 1024px) {
        .split-grid { 
            grid-template-columns: 1.1fr 1fr; 
            gap: 2rem; 
            padding: 1.5rem;
            /* FIX KONTINER TERPOTONG: Mengubah height tetap menjadi min-height agar bisa merentang natural ke bawah */
            min-height: calc(100vh - 140px); 
        }
        .scroll-column { 
            padding-bottom: 2rem;
            /* Dihapus overflow dan height 100% agar tidak memotong background */
        }
    }

    /* //* (Tactile) Desain blok sintaks - Responsive Size */
    .token-block {
        transition: all 0.15s cubic-bezier(0.4, 0, 0.2, 1);
        border-bottom-width: 4px !important;
        user-select: none; 
        touch-action: none;
        padding: 0.5rem 0.7rem;
        font-size: 11px;
        border-radius: 0.8rem;
    }
    
    @media (min-width: 768px) {
        .token-block {
            padding: 0.7rem 1.1rem;
            font-size: 13px;
            border-bottom-width: 5px !important;
            border-radius: 1rem;
        }
    }
    
    .token-block:active { 
        transform: translateY(3px) scale(0.96) !important; 
        border-bottom-width: 1px !important;
        filter: brightness(0.9);
    }
    
    /* //* (Motion) Drag Visuals */
    .sortable-drag { 
        opacity: 1 !important; 
        transform: scale(1.1) rotate(3deg) translateY(-5px) !important; 
        z-index: 1000 !important; cursor: grabbing !important;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5) !important;
    }
    .sortable-ghost { opacity: 0.1; background: #10b981 !important; border: 2px dashed #059669 !important; }

    /* //* (UI) Mekanik pegas & Card Styling */
    .btn-menu-pegas { transition: all 0.1s ease; border-bottom-width: 6px !important; }
    .btn-menu-pegas:active { transform: translateY(4px); border-bottom-width: 2px !important; }
    
    .glow-emerald-premium {
        box-shadow: 0 10px 25px -5px rgba(16, 185, 129, 0.4);
    }

    /* //* (Feedback) Animasi Flash Error Normal */
    .flash-error {
        position: fixed; inset: 0; background-color: rgba(239, 68, 68, 0.2);
        pointer-events: none; z-index: 9999; animation: fade-out 0.4s forwards;
    }
    .shake-error { animation: shake 0.4s both; border-color: #ef4444 !important; }
    @keyframes fade-out { 0% { opacity: 1; } 100% { opacity: 0; } }
    @keyframes shake {
        10%, 90% { transform: translate3d(-2px, 0, 0); }
        30%, 70% { transform: translate3d(-4px, 0, 0); }
        40%, 60% { transform: translate3d(4px, 0, 0); }
    }

    .scenario-modal {
        position: fixed; inset: 0; z-index: 500; background: rgba(15, 23, 42, 0.98);
        backdrop-filter: blur(15px); display: flex; align-items: center; justify-content: center; padding: 1.5rem;
    }

    .toast-top {
        position: fixed; 
        top: 4.5rem; 
        left: 50%; 
        transform: translateX(-50%);
        z-index: 1000; 
        background: white; 
        border-radius: 1.2rem; 
        border: 2px solid #6366f1; 
        border-bottom: 4px solid #4f46e5; 
        min-width: 220px; 
        padding: 0.5rem 1rem; 
        text-align: center;
        animation: toast-down 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }
    .dark .toast-top { background: #1e293b; border-color: #3b82f6; border-bottom-color: #1e40af;}
    @keyframes toast-down { 
        from { transform: translate(-50%, -150%) scale(0.8); opacity: 0; } 
        to { transform: translate(-50%, 0) scale(1); opacity: 1; } 
    }

    .compact-dropzone {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
        align-items: center;
        min-height: 70px;
    }
    @media (min-width: 768px) {
        .compact-dropzone { gap: 0.75rem; min-height: 80px; }
    }

    /* //* (GAMIFICATION UPDATE) Duolingo Style Bottom Sheet Modal */
    .feedback-modal-wrapper {
        position: fixed; inset: 0; z-index: 10000;
        display: flex; align-items: flex-end; justify-content: center;
        background: rgba(0, 0, 0, 0.4); /* Efek gelap di belakang */
        backdrop-filter: blur(2px);
    }
    
    .feedback-modal {
        width: 100%; max-width: 500px; /* Ukuran dirampingkan */
        background: #ffffff;
        padding: 1.5rem 1.5rem 2rem 1.5rem; /* Padding dikecilkan */
        border-radius: 1.5rem 1.5rem 0 0;
        text-align: left;
        box-shadow: 0 -10px 40px rgba(0, 0, 0, 0.15);
        pointer-events: auto; /* Agar tombol di dalam bisa diklik */
    }
    .dark .feedback-modal { background: #0f172a; box-shadow: 0 -10px 40px rgba(0, 0, 0, 0.6); }

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
    @php
        $backUrl = request('from_task') ? route('kelas.task.show', request('from_task')) : route('misi.category.levels', $mission->level->category);
    @endphp
    <a href="{{ $backUrl }}" class="p-2 bg-white dark:bg-slate-800 border-2 border-slate-200 dark:border-slate-700 rounded-xl btn-back-pegas text-slate-600 dark:text-slate-300 shadow-sm active:translate-y-1 transition-all">
        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3.5"><path d="M15 19l-7-7 7-7" /></svg>
    </a>

    <div class="flex flex-col text-left ml-3 leading-none">
        <span class="text-base font-extrabold tracking-tight dark:text-white uppercase">{{ $mission->title }}</span>
        <div class="flex items-center space-x-1.5 mt-1.5">
            <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full animate-pulse"></span>
            <span class="text-[7px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest leading-none">Lab Perakitan Rumus</span>
        </div>
    </div>
    <div class="ml-4 bg-emerald-50 dark:bg-emerald-950/30 px-4 py-2 rounded-2xl border border-emerald-100 dark:border-emerald-800 font-game text-emerald-600 text-xl tracking-wider">
        <span id="header-xp-display">{{ $mission->max_score }}</span> XP
    </div>
@endsection

@section('content')
<div x-data="missionEngine()" x-init="initEngine()" class="relative h-full">
    
    {{-- (GAMIFICATION) Duolingo Style Modal Feedback --}}
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
                {{-- Icon Alert / Bintang (Ukuran dirampingkan) --}}
                <div class="w-12 h-12 md:w-14 md:h-14 shrink-0 flex items-center justify-center rounded-full shadow-sm border-[3px]" 
                     :class="feedbackModal.type === 'error' ? 'bg-red-50 border-red-100 dark:bg-red-900/30 dark:border-red-800' : 'bg-emerald-50 border-emerald-100 dark:bg-emerald-900/30 dark:border-emerald-800'">
                    <img x-show="feedbackModal.type === 'error'" src="{{ asset('images/alert.png') }}" class="w-7 h-7 md:w-8 md:h-8 object-contain drop-shadow-sm">
                    <img x-show="feedbackModal.type === 'success'" src="{{ asset('images/bintang.png') }}" class="w-7 h-7 md:w-8 md:h-8 object-contain drop-shadow-sm">
                </div>
                
                {{-- Teks Hasil (Ukuran dirampingkan) --}}
                <div>
                    <div class="text-xl md:text-2xl font-black tracking-wide" 
                         :class="feedbackModal.type === 'error' ? 'text-red-500' : 'text-emerald-500'" 
                         x-text="feedbackModal.title"></div>
                    <div class="text-sm md:text-base font-bold opacity-90 mt-0.5" 
                         :class="feedbackModal.type === 'error' ? 'text-red-400 dark:text-red-300' : 'text-emerald-400 dark:text-emerald-300'" 
                         x-text="feedbackModal.subtitle"></div>
                </div>
            </div>
            
            {{-- Tombol OKE / Lanjut (Ukuran dan ketebalan border disesuaikan) --}}
            <button @click="handleFeedbackButton()" 
                    class="w-full py-3 md:py-3.5 rounded-xl font-black text-base md:text-lg text-white transition-all active:scale-95 border-b-[4px] active:border-b-0 active:translate-y-[4px]" 
                    :class="feedbackModal.type === 'error' ? 'bg-red-500 hover:bg-red-600 border-red-700' : 'bg-emerald-500 hover:bg-emerald-600 border-emerald-700'" 
                    x-text="feedbackModal.type === 'error' ? 'OKE' : 'LANJUT'">
            </button>
        </div>
    </div>

    {{-- Toast Notification (COMPACT) --}}
    <div x-show="toast.show" x-cloak x-transition.opacity 
        class="toast-top shadow-xl flex items-center space-x-3" 
        :style="toast.type === 'error' ? 'border-color: #ef4444; border-bottom-color: #b91c1c;' : 'border-color: #10b981; border-bottom-color: #047857;'">
        
        <div class="w-8 h-8 rounded-lg flex items-center justify-center animate-bounce bg-slate-50 dark:bg-slate-900/50 shadow-inner shrink-0">
            <img :src="'{{ asset('images') }}/' + toast.icon" class="w-5 h-5 object-contain">
        </div>
        <div class="text-left flex-1">
            <p class="text-[10px] font-black text-slate-900 dark:text-white uppercase leading-none" x-text="toast.title"></p>
            <p class="text-[9px] font-bold text-slate-500 dark:text-slate-400 mt-1 leading-tight" x-text="toast.message"></p>
        </div>
    </div>

    {{-- Scenario Modal (FIXED CLOSE BUTTON) --}}
    <div x-show="scenarioMaximized" x-transition.opacity x-cloak class="scenario-modal" @click="scenarioMaximized = false">
        <button class="absolute top-8 right-8 mt-8 p-3 bg-white/10 hover:bg-red-500 text-white rounded-2xl transition-colors z-50">
            <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
        <img src="{{ str_replace('/upload/', '/upload/f_auto,q_auto/', $mission->mission_image) }}" class="max-w-full max-h-full object-contain rounded-3xl shadow-2xl relative z-40">
    </div>

    <template x-if="status === 'wrong'">
        <div class="flash-error"></div>
    </template>

    <div class="split-grid max-w-7xl mx-auto"> 
        {{-- Monitor & Instruksi --}}
        <div class="scroll-column space-y-4 md:space-y-6">
            <div class="bg-white dark:bg-slate-900 p-3 md:p-4 rounded-[2rem] shadow-sm border-2 border-slate-200 dark:border-slate-800 border-b-[8px] relative group">
                <div class="absolute top-5 left-5 px-3 py-1 bg-slate-900 text-white text-[7px] font-black uppercase rounded-lg z-10">Monitor</div>
                <button @click="scenarioMaximized = true" class="absolute top-5 right-5 p-2 bg-emerald-500 text-white rounded-lg z-20 hover:scale-110 shadow-lg">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l5-5M4 16v4m0 0h4m-4 0l5-5m11 5v-4m0 4h-4m4 0l-5-5"/></svg>
                </button>
                <div class="overflow-hidden rounded-[1.5rem] bg-slate-50 dark:bg-slate-950 border border-slate-100 dark:border-slate-800/50 cursor-zoom-in" @click="scenarioMaximized = true">
                    <img src="{{ str_replace('/upload/', '/upload/f_auto,q_auto/', $mission->mission_image) }}" 
                         class="w-full h-auto object-contain max-h-[40vh] lg:max-h-[55vh]" alt="Skenario">
                </div>
            </div>

            <div class="bg-emerald-600 text-white p-6 md:p-7 rounded-[2rem] md:rounded-[2.5rem] shadow-xl border-b-[8px] border-emerald-800 relative">
                <span class="text-[8px] font-black uppercase tracking-widest text-emerald-100">Instruksi Misi</span>
                <p class="text-sm md:text-lg font-bold mt-2 leading-relaxed tracking-tight">{{ $mission->question }}</p>
            </div>
        </div>

        {{-- Workspace Perakitan --}}
        <div class="scroll-column space-y-4 md:space-y-6">
            <div class="bg-white dark:bg-slate-900 p-5 md:p-7 rounded-[2rem] md:rounded-[2.5rem] shadow-sm border-2 border-slate-200 dark:border-slate-800 border-b-[8px] flex flex-col transition-all duration-300" 
                 :class="status === 'wrong' ? 'shake-error shadow-red-100' : ''">
                
                <div class="flex justify-between items-center mb-4 md:mb-6 px-1">
                    <h3 class="text-[9px] md:text-[10px] font-black text-slate-400 uppercase tracking-widest text-left">Kotak Rakitan</h3>
                    <span class="text-[7px] md:text-[8px] font-black text-blue-500 uppercase tracking-widest animate-pulse" x-show="answerBox.length > 0">Klik hapus | Geser</span>
                </div>

                <div class="flex items-start gap-2 md:gap-3 bg-slate-50 dark:bg-slate-950 rounded-[1.8rem] md:rounded-[2.2rem] p-4 md:p-6 border-2 border-dashed border-slate-200 dark:border-slate-800 shadow-inner min-h-[120px] md:min-h-[140px] z-10">
                    <span class="text-3xl md:text-4xl font-mono font-black text-emerald-500 mt-1 select-none">=</span>
                    
                    {{-- FIXED SORTABLE DOM --}}
                    <div id="dropzone" class="compact-dropzone flex-grow" x-ref="dropzone">
                    </div>
                </div>

                {{-- Hint Bantuan --}}
                <div x-show="hint" x-transition class="mt-5 p-3 bg-amber-50 dark:bg-amber-900/20 border-2 border-amber-100 dark:border-amber-800 rounded-xl text-[10px] font-extrabold text-amber-700 dark:text-amber-400 text-center uppercase leading-snug">
                    <span x-text="hint"></span>
                </div>

                <button @click="submitSyntax($event)" class="btn-menu-pegas glow-emerald-premium w-full mt-6 py-4 md:py-5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-[1.5rem] md:rounded-[1.8rem] font-black text-[9px] md:text-[10px] tracking-[0.2em] uppercase border-emerald-800 shadow-lg">
                    Verifikasi Rakitan
                </button>
            </div>

            {{-- Gudang Komponen --}}
            <div class="bg-white dark:bg-slate-900 p-5 md:p-7 rounded-[2rem] md:rounded-[2.5rem] shadow-sm border-2 border-slate-200 dark:border-slate-800 border-b-[8px]">
                <h3 class="text-[9px] md:text-[10px] font-black text-slate-400 uppercase tracking-widest px-1 mb-5">Gudang Komponen</h3>
                <div class="flex flex-wrap gap-2 md:gap-3 justify-center">
                    <template x-for="(block, index) in availableBlocks" :key="'gudang-' + index">
                        <button @click="addToAnswer(block, $event)" :class="getBlockClass(block)"
                                class="token-block font-mono font-black shadow-md border-2 active:scale-90">
                            <span x-text="block"></span>
                        </button>
                    </template>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
{{-- Library pendukung --}}
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js"></script>

<script>
    function missionEngine() {
        return {
            answerBox: [], 
            status: 'idle', 
            hint: '', 
            scenarioMaximized: false,
            currentPotentialXP: {{ $mission->max_score }},
            rawAvailableBlocks: @js($availableBlocks), 
            attempts: 0,
            toast: { show: false, message: '', title: '', icon: 'bintang.png', type: 'info' }, 
            
            // State untuk Gamifikasi Modal & SFX
            feedbackModal: { show: false, type: '', title: '', subtitle: '', nextUrl: '' },
            sfxClick: null,
            sfxBenar: null,
            sfxSalah: null,

            storageKey: 'mission_{{ $mission->id }}_syntax_progress',
            isReview: {{ (auth()->user()->progress && auth()->user()->progress->where('mission_id', $mission->id)->where('status', 'completed')->isNotEmpty()) ? 'true' : 'false' }},

            initEngine() {
                try {
                    let audioClick = new Audio('{{ asset("audio/drop.mp3") }}');
                    let audioBenar = new Audio('{{ asset("audio/benar.mp3") }}');
                    let audioSalah = new Audio('{{ asset("audio/salah.mp3") }}');
                    
                    audioClick.load(); audioBenar.load(); audioSalah.load();
                    
                    this.sfxClick = audioClick;
                    this.sfxBenar = audioBenar;
                    this.sfxSalah = audioSalah;
                } catch(e) {
                    console.log('SFX tidak ditemukan, mengabaikan fitur suara.');
                }

                if (!this.isReview) {
                    const saved = localStorage.getItem(this.storageKey);
                    if (saved) {
                        const data = JSON.parse(saved);
                        this.answerBox = data.answerBox ?? [];
                        this.attempts = data.attempts ?? 0;
                        this.currentPotentialXP = data.currentPotentialXP ?? {{ $mission->max_score }};
                    }
                }

                this.$watch('currentPotentialXP', v => { 
                    const el = document.getElementById('header-xp-display');
                    if(el) el.innerText = v;
                });

                this.$watch('answerBox', () => { this.renderBox(); });

                const headerXp = document.getElementById('header-xp-display');
                if(headerXp) headerXp.innerText = this.currentPotentialXP;

                if (this.isReview) {
                    this.currentPotentialXP = {{ $mission->max_score }};
                }

                this.initSortable();
            },

            renderBox() {
                const dropzone = this.$refs.dropzone;
                dropzone.innerHTML = ''; 
                
                this.answerBox.forEach((item, index) => {
                    const div = document.createElement('div');
                    div.className = this.getBlockClass(item) + ' token-block font-mono font-black shadow-md border-2 cursor-grab active:cursor-grabbing';
                    div.innerText = item;
                    div.onclick = (e) => this.removeFromAnswer(index, e);
                    dropzone.appendChild(div);
                });
            },

            initSortable() {
                new Sortable(this.$refs.dropzone, {
                    animation: 200, 
                    ghostClass: 'sortable-ghost', 
                    dragClass: 'sortable-drag',
                    onEnd: (evt) => {
                        if (evt.oldIndex === evt.newIndex) return;
                        if(this.sfxClick && this.sfxClick.readyState >= 2) { 
                            this.sfxClick.currentTime = 0; this.sfxClick.play().catch(()=>{}); 
                        }
                        const list = [...this.answerBox];
                        const [movedItem] = list.splice(evt.oldIndex, 1);
                        list.splice(evt.newIndex, 0, movedItem);
                        this.answerBox = list; 
                        this.saveToLocal();
                        this.status = 'idle';
                    }
                });
                this.renderBox();
            },

            saveToLocal() {
                if (this.isReview) return;
                const payload = {
                    answerBox: this.answerBox, attempts: this.attempts, currentPotentialXP: this.currentPotentialXP
                };
                localStorage.setItem(this.storageKey, JSON.stringify(payload));
            },

            triggerToast(title, message, icon = 'alert.png', type = 'info') {
                this.toast.title = title;
                this.toast.message = message;
                this.toast.icon = icon; 
                this.toast.type = type;
                this.toast.show = true;
                setTimeout(() => { this.toast.show = false; }, 3500);
            },

            // Fitur Gamifikasi: Button handler (Untuk tombol OKE / Lanjut di popup)
            handleFeedbackButton() {
                this.feedbackModal.show = false;
                // Jika sukses, lanjut ke halaman berikutnya saat diklik
                if (this.feedbackModal.type === 'success' && this.feedbackModal.nextUrl) {
                    window.location.href = this.feedbackModal.nextUrl;
                }
            },

            triggerFeedbackModal(type, title, subtitle, nextUrl = '') {
                this.feedbackModal.type = type;
                this.feedbackModal.title = title;
                this.feedbackModal.subtitle = subtitle;
                this.feedbackModal.nextUrl = nextUrl;
                this.feedbackModal.show = true;
            },

            // Fitur Gamifikasi: Confetti (Sukses)
            fireConfetti() {
                var duration = 4 * 1000;
                var end = Date.now() + duration;
                (function frame() {
                    confetti({ particleCount: 5, angle: 60, spread: 55, origin: { x: 0 }, colors: ['#10b981', '#3b82f6', '#fbbf24', '#ffffff'] });
                    confetti({ particleCount: 5, angle: 120, spread: 55, origin: { x: 1 }, colors: ['#10b981', '#3b82f6', '#fbbf24', '#ffffff'] });
                    if (Date.now() < end) { requestAnimationFrame(frame); }
                }());
            },

            // Fitur Gamifikasi: Ledakan Merah (Gagal)
            fireCrossParticles() {
                var defaults = { spread: 360, ticks: 100, gravity: 0.8, decay: 0.92, startVelocity: 40, colors: ['#ef4444', '#b91c1c', '#fca5a5'] };
                
                function fire(particleRatio, opts) {
                    confetti(Object.assign({}, defaults, opts, { particleCount: Math.floor(150 * particleRatio), shapes: ['star', 'circle', 'square'] }));
                }
                fire(0.25, { spread: 30, startVelocity: 60 });
                fire(0.2, { spread: 60 });
                fire(0.35, { spread: 100, decay: 0.91, scalar: 0.8 });
                fire(0.1, { spread: 120, startVelocity: 30, decay: 0.92, scalar: 1.2 });
                fire(0.1, { spread: 120, startVelocity: 50 });
            },

            spawnFloatingText(e, text, color = '#fbbf24') {
                if (!e) return;
                const el = document.createElement('div');
                el.className = 'floating-text';
                el.innerText = text;
                el.style.left = (e.clientX - 20) + 'px';
                el.style.top = (e.clientY - 20) + 'px';
                el.style.color = color;
                document.body.appendChild(el);
                setTimeout(() => el.remove(), 1000);
            },

            get availableBlocks() {
                let result = [];
                this.rawAvailableBlocks.forEach(block => {
                    let tokens = block.match(/[A-Z0-9\%]+|[\(\)\,\;\:\=\"\>\<\$\%]/g);
                    if (tokens) { result.push(...tokens); } else { result.push(block); }
                });
                return [...new Set(result)].sort();
            },

            getBlockClass(block) {
                const funcRegex = /^(IF|SUM|AVERAGE|MIN|MAX|AND|OR|NOT|COUNT)$/i;
                const cellRegex = /^[A-Z]+\$?[0-9]+$/i;
                const stringRegex = /^".*"$/;
                const digitRegex = /^[0-9]+%?$/;

                if (funcRegex.test(block)) return 'bg-blue-50 text-blue-700 border-blue-200 border-b-blue-300 dark:bg-blue-900/60 dark:text-blue-300 dark:border-blue-800 dark:border-b-blue-500 shadow-blue-100/50';
                if (cellRegex.test(block)) return 'bg-emerald-50 text-emerald-700 border-emerald-200 border-b-emerald-300 dark:bg-emerald-900/60 dark:text-emerald-300 dark:border-emerald-800 dark:border-b-emerald-500 shadow-emerald-100/50';
                if (stringRegex.test(block) || digitRegex.test(block)) return 'bg-amber-50 text-amber-700 border-amber-200 border-b-amber-300 dark:bg-amber-950/60 dark:text-amber-300 dark:border-amber-800 dark:border-b-amber-500 shadow-amber-100/50';
                return 'bg-slate-50 text-slate-500 border-slate-200 border-b-slate-300 dark:bg-slate-800 dark:text-slate-300 dark:border-slate-700 dark:border-b-slate-500 shadow-slate-100/50';
            },

            addToAnswer(block, e) { 
                this.answerBox.push(block); 
                this.status = 'idle'; 
                this.hint = ''; 
                this.saveToLocal();
                
                if(this.sfxClick && this.sfxClick.readyState >= 2) { 
                    this.sfxClick.currentTime = 0; this.sfxClick.play().catch(()=>{}); 
                }
                // DITAMBAHKAN EFEK BINTANG
                this.spawnFloatingText(e, 'Pilih ⭐', '#10b981');
            },

            removeFromAnswer(index, e) { 
                this.answerBox.splice(index, 1); 
                this.saveToLocal();
                
                if(this.sfxClick && this.sfxClick.readyState >= 2) { 
                    this.sfxClick.currentTime = 0; this.sfxClick.play().catch(()=>{}); 
                }
                // DITAMBAHKAN EFEK ANGIN
                this.spawnFloatingText(e, 'Hapus 💥', '#ef4444');
            },
            
            submitSyntax(e) {
                if(this.answerBox.length === 0) { 
                    this.status = 'wrong'; 
                    this.triggerToast('Gagal!', 'Kotak rakitan masih kosong!', 'alert.png', 'error');
                    if(this.sfxSalah && this.sfxSalah.readyState >= 2) { 
                        this.sfxSalah.currentTime = 0; this.sfxSalah.play().catch(()=>{}); 
                    }
                    if(e) this.spawnFloatingText(e, 'Kosong! ⚠️', '#ef4444');
                    setTimeout(() => this.status = 'idle', 500); return; 
                }

                const formula = this.answerBox.join('');
                fetch("{{ route('misi.check', $mission->id) }}", {
                    method: "POST",
                    headers: { "Content-Type": "application/json", "X-CSRF-TOKEN": "{{ csrf_token() }}", "Accept": "application/json" },
                    body: JSON.stringify({ answer: formula })
                })
                .then(r => r.json())
                .then(data => {
                    if (data.status === 'success') {
                        localStorage.removeItem(this.storageKey);
                        
                        if(this.sfxBenar && this.sfxBenar.readyState >= 2) { 
                            this.sfxBenar.currentTime = 0; this.sfxBenar.play().catch(()=>{}); 
                        }
                        this.fireConfetti();
                        
                        // DITAMBAHKAN EFEK PESTA PADA MODAL SUKSES
                        this.triggerFeedbackModal('success', 'Tepat Sekali! 🎉', '+ ' + this.currentPotentialXP + ' XP Berhasil Diraih', data.next_url);
                        
                    } else {
                        this.status = 'wrong'; 
                        this.hint = data.message; 
                        this.attempts = data.attempts;
                        
                        if(this.sfxSalah && this.sfxSalah.readyState >= 2) { 
                            this.sfxSalah.currentTime = 0; this.sfxSalah.play().catch(()=>{}); 
                        }
                        
                        // Partikel meledak
                        this.fireCrossParticles(); 

                        // DITAMBAHKAN EMOJI NANGIS SAAT SALAH
                        if(e) this.spawnFloatingText(e, 'Masih salah! 😭', '#ef4444');
                        this.triggerFeedbackModal('error', 'Belum berhasil 😭', 'Ayo coba lagi!');
                        
                        if (!this.isReview && this.attempts > 3) {
                            let penalty = (this.attempts - 3) * ({{ $mission->max_score }} * 0.05);
                            this.currentPotentialXP = Math.max(Math.floor({{ $mission->max_score }} - penalty), Math.floor({{ $mission->max_score }} * 0.4));
                        }
                        this.saveToLocal();
                        setTimeout(() => { this.status = 'idle'; }, 500);
                    }
                }).catch(err => {
                    console.error('Fetch error:', err);
                });
            }
        }
    }
</script>
@endpush