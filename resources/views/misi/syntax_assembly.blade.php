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
            height: calc(100vh - 140px); 
        }
        .scroll-column { 
            height: 100%; 
            overflow-y: auto; 
            padding-bottom: 2rem;
            scrollbar-width: none; 
        }
        .scroll-column::-webkit-scrollbar { display: none; }
    }

    /* //* (Tactile) Desain blok sintaks - Responsive Size */
    .token-block {
        transition: all 0.15s cubic-bezier(0.4, 0, 0.2, 1);
        border-bottom-width: 4px !important;
        user-select: none; 
        touch-action: none;
        /* Default Mobile Portrait (Compact) */
        padding: 0.5rem 0.7rem;
        font-size: 11px;
        border-radius: 0.8rem;
    }
    
    /* Desktop & Landscape Size */
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

    /* //* (Feedback) Animasi */
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
    .font-game { font-family: 'Bangers', cursive; }

    /* //* (Notification) Original Style */
    .toast-top {
        position: fixed; top: 1.5rem; left: 50%; transform: translateX(-50%);
        z-index: 1000; background: white; border-radius: 1.5rem;
        border: 2px solid #6366f1; border-bottom: 6px solid #4f46e5;
        min-width: 260px; padding: 1rem 1.5rem; text-align: center;
        animation: toast-down 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }
    .dark .toast-top { background: #1e293b; border-color: #3b82f6; border-bottom-color: #1e40af;}
    @keyframes toast-down { from { transform: translate(-50%, -100%) scale(0.8); opacity: 0; } to { transform: translate(-50%, 0) scale(1); opacity: 1; } }

    /* Custom dropzone for better space management */
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
</style>
@endpush

@section('header_left')
    @php
        $backUrl = request('from_task') ? route('kelas.task.show', request('from_task')) : route('misi.category.levels', $mission->level->category);
    @endphp
    <a href="{{ $backUrl }}" class="p-2 bg-white dark:bg-slate-800 border-2 border-slate-200 dark:border-slate-700 rounded-xl btn-menu-pegas text-slate-600 dark:text-slate-300 shadow-sm active:translate-y-1 transition-all">
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
<div x-data="missionEngine()" class="relative h-full">
    
    {{-- Toast Notification --}}
    <div x-show="toast.show" x-cloak x-transition.opacity 
        class="toast-top shadow-xl flex items-center space-x-3" 
        :style="toast.type === 'error' ? 'border-color: #ef4444; border-bottom-color: #b91c1c;' : 'border-color: #10b981; border-bottom-color: #047857;'">
        <div class="w-10 h-10 rounded-xl flex items-center justify-center animate-bounce bg-slate-50 dark:bg-slate-900/50 shadow-inner">
            <img :src="'{{ asset('images') }}/' + toast.icon" class="w-7 h-7 object-contain">
        </div>
        <div class="text-left flex-1">
            <p class="text-[10px] font-black text-slate-900 dark:text-white uppercase leading-none" x-text="toast.title"></p>
            <p class="text-[9px] font-bold text-slate-500 dark:text-slate-400 mt-1" x-text="toast.message"></p>
        </div>
    </div>

    {{-- Scenario Modal --}}
    <div x-show="scenarioMaximized" x-transition.opacity x-cloak class="scenario-modal" @click="scenarioMaximized = false">
        <button class="absolute top-8 right-8 p-3 bg-white/10 hover:bg-red-500 text-white rounded-2xl transition-colors">
            <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
        <img src="{{ str_replace('/upload/', '/upload/f_auto,q_auto/', $mission->mission_image) }}" class="max-w-full max-h-full object-contain rounded-3xl shadow-2xl">
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
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5v-4m0 4h-4m4 0l-5-5"/></svg>
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
                    <div id="dropzone" class="compact-dropzone flex-grow"
                        x-init="new Sortable($el, { 
                            animation: 200, ghostClass: 'sortable-ghost', dragClass: 'sortable-drag',
                            onEnd: (evt) => {
                                if (evt.oldIndex === evt.newIndex) return;
                                const list = [...answerBox];
                                const [movedItem] = list.splice(evt.oldIndex, 1);
                                list.splice(evt.newIndex, 0, movedItem);
                                answerBox = []; $nextTick(() => { answerBox = list; });
                            }
                        })">
                        <template x-for="(item, index) in answerBox" :key="index + '-' + item">
                            <div @click="removeFromAnswer(index)" :class="typeof item !== 'undefined' ? getBlockClass(item) : ''"
                                 class="token-block font-mono font-black shadow-md border-2 cursor-grab active:cursor-grabbing">
                                <span x-text="item"></span>
                            </div>
                        </template>
                    </div>
                </div>

                <div x-show="hint" x-transition class="mt-5 p-3 bg-amber-50 dark:bg-amber-900/20 border-2 border-amber-100 dark:border-amber-800 rounded-xl text-[9px] font-bold text-amber-700 dark:text-amber-400 text-center uppercase">
                    <span x-text="hint"></span>
                </div>

                <button @click="submitSyntax()" class="btn-menu-pegas glow-emerald-premium w-full mt-6 py-4 md:py-5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-[1.5rem] md:rounded-[1.8rem] font-black text-[9px] md:text-[10px] tracking-[0.2em] uppercase border-emerald-800 shadow-lg">
                    Verifikasi Rakitan
                </button>
            </div>

            {{-- Gudang Komponen --}}
            <div class="bg-white dark:bg-slate-900 p-5 md:p-7 rounded-[2rem] md:rounded-[2.5rem] shadow-sm border-2 border-slate-200 dark:border-slate-800 border-b-[8px]">
                <h3 class="text-[9px] md:text-[10px] font-black text-slate-400 uppercase tracking-widest px-1 mb-5">Gudang Komponen</h3>
                <div class="flex flex-wrap gap-2 md:gap-3 justify-center">
                    <template x-for="(block, index) in availableBlocks" :key="'gudang-' + index">
                        <button @click="addToAnswer(block)" :class="getBlockClass(block)"
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
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
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
            
            // FIX: Beri default icon 'bintang.png' agar browser tidak memanggil URL kosong
            toast: { show: false, message: '', title: '', icon: 'bintang.png', type: 'info' }, 

            // --- STORAGE & REVIEW LOGIC ---
            storageKey: 'mission_{{ $mission->id }}_syntax_progress',
            isReview: {{ (auth()->user()->progress && auth()->user()->progress->where('mission_id', $mission->id)->where('status', 'completed')->isNotEmpty()) ? 'true' : 'false' }},

            init() {
                console.log("=== SYNTAX ENGINE START ===");

                // 1. Load data dari LocalStorage jika bukan mode Review
                if (!this.isReview) {
                    const saved = localStorage.getItem(this.storageKey);
                    if (saved) {
                        const data = JSON.parse(saved);
                        this.answerBox = data.answerBox ?? [];
                        this.attempts = data.attempts ?? 0;
                        this.currentPotentialXP = data.currentPotentialXP ?? {{ $mission->max_score }};
                        console.log("Progress dimuat.");
                    }
                }

                // 2. Watcher untuk UI Header
                this.$watch('currentPotentialXP', v => { 
                    const el = document.getElementById('header-xp-display');
                    if(el) el.innerText = v;
                });

                // 3. Force Sync UI Header saat pertama kali muat (Refresh)
                const headerXp = document.getElementById('header-xp-display');
                if(headerXp) headerXp.innerText = this.currentPotentialXP;

                if (this.isReview) {
                    this.currentPotentialXP = {{ $mission->max_score }};
                }
            },

            saveToLocal() {
                if (this.isReview) return;
                const payload = {
                    answerBox: this.answerBox,
                    attempts: this.attempts,
                    currentPotentialXP: this.currentPotentialXP
                };
                localStorage.setItem(this.storageKey, JSON.stringify(payload));
            },

            triggerToast(title, message, icon = 'bintang.png', type = 'info') {
                this.toast.title = title;
                this.toast.message = message;
                this.toast.icon = icon; 
                this.toast.type = type;
                this.toast.show = true;
                setTimeout(() => { this.toast.show = false; }, 3500);
            },

            get availableBlocks() {
                let result = [];
                this.rawAvailableBlocks.forEach(block => {
                    let tokens = block.match(/[A-Z0-9]+|[\(\)\,\;\:\=\"\>\<\$]/g);
                    if (tokens) { result.push(...tokens); } else { result.push(block); }
                });
                return [...new Set(result)].sort();
            },

            getBlockClass(block) {
                const funcRegex = /^(IF|SUM|AVERAGE|MIN|MAX|AND|OR|NOT|COUNT)$/i;
                const cellRegex = /^[A-Z]+\$?[0-9]+$/i;
                const stringRegex = /^".*"$/;
                const digitRegex = /^[0-9]+%?$/;

                if (funcRegex.test(block)) 
                    return 'bg-blue-50 text-blue-700 border-blue-200 border-b-blue-300 dark:bg-blue-900/60 dark:text-blue-300 dark:border-blue-800 dark:border-b-blue-500 shadow-blue-100/50 dark:shadow-none';
                if (cellRegex.test(block)) 
                    return 'bg-emerald-50 text-emerald-700 border-emerald-200 border-b-emerald-300 dark:bg-emerald-900/60 dark:text-emerald-300 dark:border-emerald-800 dark:border-b-emerald-500 shadow-emerald-100/50 dark:shadow-none';
                if (stringRegex.test(block) || digitRegex.test(block)) 
                    return 'bg-amber-50 text-amber-700 border-amber-200 border-b-amber-300 dark:bg-amber-950/60 dark:text-amber-300 dark:border-amber-800 dark:border-b-amber-500 shadow-amber-100/50 dark:shadow-none';
                
                return 'bg-slate-50 text-slate-500 border-slate-200 border-b-slate-300 dark:bg-slate-800 dark:text-slate-300 dark:border-slate-700 dark:border-b-slate-500 shadow-slate-100/50 dark:shadow-none';
            },

            addToAnswer(block) { 
                this.answerBox.push(block); 
                this.status = 'idle'; 
                this.hint = ''; 
                this.saveToLocal();
            },

            removeFromAnswer(index) { 
                this.answerBox.splice(index, 1); 
                this.saveToLocal();
            },
            
            submitSyntax() {
                if(this.answerBox.length === 0) { 
                    this.status = 'wrong'; 
                    this.triggerToast('Gagal!', 'Kotak rakitan masih kosong!', 'alert.png', 'error');
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
                        this.triggerToast('Berhasil!', data.message, 'bintang.png', 'info');
                        setTimeout(() => { window.location.href = data.next_url; }, 1500);
                    } else {
                        this.status = 'wrong'; 
                        this.hint = data.message; 
                        this.attempts = data.attempts;
                        this.triggerToast('Periksa Lagi!', 'Rumus belum tepat.', 'find.png', 'error');
                        
                        if (!this.isReview && this.attempts > 3) {
                            let penalty = (this.attempts - 3) * ({{ $mission->max_score }} * 0.05);
                            this.currentPotentialXP = Math.max(Math.floor({{ $mission->max_score }} - penalty), Math.floor({{ $mission->max_score }} * 0.4));
                        }
                        
                        this.saveToLocal();
                        setTimeout(() => { this.status = 'idle'; }, 500);
                    }
                });
            }
        }
    }
</script>
@endpush