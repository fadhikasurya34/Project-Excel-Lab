{{-- (View) Halaman Lab Perakitan Rumus (Syntax Assembly) --}}

@extends('layouts.siswa')

@section('title', $mission->title . ' - Virtual Lab')

@push('styles')
<style>
    /* (Style) Stabilisasi layout utama */
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
            min-height: calc(100vh - 140px); 
        }
        .scroll-column { 
            padding-bottom: 2rem;
        }
    }

    /* (Style) Desain blok sintaks untuk kemudahan sentuhan di perangkat mobile */
    .token-block {
        transition: all 0.15s cubic-bezier(0.4, 0, 0.2, 1);
        border-bottom-width: 3px !important;
        user-select: none; 
        touch-action: pan-y; 
        padding: 0.6rem 0.8rem;
        font-size: 11px;
        border-radius: 0.6rem;
        margin: 0.1rem; 
        position: relative;
    }
    
    /* (Style) Perluasan area target sentuhan tak kasat mata */
    .token-block::after {
        content: '';
        position: absolute;
        top: -4px; left: -4px; right: -4px; bottom: -4px;
    }
    
    @media (min-width: 768px) {
        .token-block {
            padding: 0.5rem 0.8rem;
            font-size: 12px;
            border-bottom-width: 4px !important;
            border-radius: 0.8rem;
            margin: 0;
        }
    }
    
    .token-block:active { 
        transform: translateY(2px) scale(0.96) !important; 
        border-bottom-width: 1px !important;
        filter: brightness(0.9);
    }
    
    /* (Style) Efek visual saat blok digeser (drag) */
    .sortable-drag { 
        opacity: 0.9 !important; 
        transform: scale(1.1) rotate(2deg) !important; 
        z-index: 1000 !important; cursor: grabbing !important;
        box-shadow: 0 15px 30px -10px rgba(0, 0, 0, 0.3) !important;
    }
    .sortable-ghost { opacity: 0.2; background: #10b981 !important; border: 2px dashed #059669 !important; border-radius: 0.8rem;}

    /* (Style) Efek animasi pegas pada tombol */
    .btn-menu-pegas {transition: all 0.1s ease; border-bottom-width: 6px;}
    .btn-menu-pegas:active {transform: translateY(4px);border-bottom-width: 2px;}
    .btn-back-pegas {transition: all 0.1s ease;border-bottom-width: 6px;}
    .btn-back-pegas:active {transform: translateY(2px);border-bottom-width: 0px;}
    
    .glow-emerald-premium {
        box-shadow: 0 10px 25px -5px rgba(16, 185, 129, 0.4);
    }

    /* (Style) Animasi kilatan merah saat terjadi kesalahan */
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
        overflow: auto; 
    }

    /* (Style) Penyesuaian gesture zoom pada gambar */
    .scenario-modal img {
        touch-action: pinch-zoom;
    }

    .toast-top {
        position: fixed; 
        top: 4.5rem; 
        left: 50%; 
        transform: translateX(-50%);
        z-index: 1000; 
        background: white; 
        border-radius: 1.2rem; 
        border: 2px solid #10b981; 
        border-bottom: 4px solid #059669; 
        min-width: 220px; 
        padding: 0.5rem 1rem; 
        text-align: center;
        animation: toast-down 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }
    .dark .toast-top { background: #1e293b; border-color: #10b981; border-bottom-color: #064e3b;}
    @keyframes toast-down { 
        from { transform: translate(-50%, -150%) scale(0.8); opacity: 0; } 
        to { transform: translate(-50%, 0) scale(1); opacity: 1; } 
    }

    /* (Style) Desain responsif kotak area perakitan */
    .compact-dropzone {
        display: flex;
        flex-wrap: wrap;
        gap: 0.4rem; 
        align-content: flex-start; 
        min-height: 80px; 
        padding: 0.5rem;
    }
    @media (min-width: 768px) {
        .compact-dropzone { gap: 0.4rem; min-height: 100px; }
    }

    /* (Style) Tampilan modal notifikasi penyelesaian misi */
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

    /* (Style) Animasi teks melayang pada titik aksi */
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
    
    <div class="flex items-center w-full max-w-[80vw] overflow-hidden font-sans">
        <a href="{{ $backUrl }}" class="shrink-0 p-2 bg-white dark:bg-slate-800 border-2 border-slate-200 dark:border-slate-700 rounded-xl btn-back-pegas text-slate-600 dark:text-slate-300 shadow-sm active:translate-y-1 transition-all">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3.5"><path d="M15 19l-7-7 7-7" /></svg>
        </a>

        <div class="flex flex-col text-left ml-3 leading-none flex-1 min-w-0">
            <span class="text-base font-extrabold tracking-tight dark:text-white uppercase truncate">{{ $mission->title }}</span>
            <div class="flex items-center space-x-1.5 mt-1.5 shrink-0">
                <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full animate-pulse"></span>
                <span class="text-[7px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest leading-none">Lab Perakitan Rumus</span>
            </div>
        </div>
        
        <div class="shrink-0 ml-3 md:ml-4 bg-emerald-50 dark:bg-emerald-950/30 px-3 md:px-4 py-1.5 md:py-2 rounded-2xl border border-emerald-100 dark:border-emerald-800 text-emerald-600 text-lg md:text-xl font-bold tracking-wider">
            <span id="header-xp-display">{{ $mission->max_score }}</span> XP
        </div>
    </div>
@endsection

@section('content')
<div x-data="missionEngine()" x-init="initEngine()" class="relative h-full font-sans">
    
    {{-- (View) Modal Peringatan Awal Tata Cara Merakit --}}
    <div x-show="showIntro" x-cloak class="fixed inset-0 z-[1000] p-4 sm:p-6 bg-slate-950/90 backdrop-blur-md overflow-y-auto flex font-sans">
        <div class="bg-white dark:bg-slate-800 rounded-3xl w-full max-w-lg shadow-2xl m-auto border-4 border-emerald-500 transform transition-all"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-8 sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100">
            
            <div class="p-6 md:p-8 max-h-[85vh] overflow-y-auto scrollbar-hide">
                <div class="w-16 h-16 bg-emerald-100 dark:bg-emerald-900/30 rounded-full flex items-center justify-center mb-5 mx-auto shadow-inner border border-emerald-200 dark:border-emerald-800">
                    <svg class="w-8 h-8 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                
                <h3 class="text-2xl md:text-3xl font-black text-emerald-600 dark:text-emerald-400 text-center mb-2 uppercase tracking-tight">PERHATIAN</h3>
                <p class="text-[13px] text-slate-500 dark:text-slate-400 text-center mb-8 font-medium leading-relaxed">Pahami aturan perakitan berikut agar misi berhasil dengan XP penuh.</p>
                
                <div class="space-y-4 mb-8">
                    <div class="flex gap-4 items-start bg-slate-50 dark:bg-slate-900/50 p-4 rounded-2xl border border-slate-100 dark:border-slate-700">
                        <div class="text-2xl shrink-0 mt-0.5">💲</div>
                        <div>
                            <h4 class="text-xs font-black text-emerald-600 dark:text-emerald-400 uppercase tracking-widest mb-1.5">Penulisan VLOOKUP / HLOOKUP</h4>
                            <p class="text-[13px] text-slate-600 dark:text-slate-400 leading-relaxed font-medium">Ingat! Tambahkan tanda <strong>$</strong> secara lengkap. Misal harusnya <strong>$A$1</strong> (<code>$</code> + <code>A</code> + <code>$</code> + <code>1</code>), bukan hanya <strong>$A1</strong> (<code>$</code> + <code>A</code> + <code>1</code>).</p>
                        </div>
                    </div>
                    <div class="flex gap-4 items-start bg-slate-50 dark:bg-slate-900/50 p-4 rounded-2xl border border-slate-100 dark:border-slate-700">
                        <div class="text-2xl shrink-0 mt-0.5">🎮</div>
                        <div>
                            <h4 class="text-xs font-black text-emerald-600 dark:text-emerald-400 uppercase tracking-widest mb-1.5">Konsep Hapus & Geser</h4>
                            <p class="text-[13px] text-slate-600 dark:text-slate-400 leading-relaxed font-medium"><strong>Klik</strong> blok di kotak rakitan jika ingin menghapusnya. <strong>Tahan & Geser (Drag)</strong> blok jika ingin memindah urutannya.</p>
                        </div>
                    </div>
                </div>

                <button @click="showIntro = false" class="w-full py-4 bg-emerald-500 hover:bg-emerald-600 text-white rounded-2xl font-black text-sm uppercase tracking-widest shadow-lg shadow-emerald-500/30 transition-all active:scale-95 border-b-4 border-emerald-700 active:border-b-0 active:translate-y-1">
                    Mulai Merakit Rumus!
                </button>
            </div>
        </div>
    </div>

    {{-- (View) Modal Notifikasi Hasil Pemeriksaan (Feedback) --}}
    <div x-show="feedbackModal.show" x-cloak class="feedback-modal-wrapper font-sans">
        <div class="feedback-modal" :class="feedbackModal.type">
            <div class="flex items-center gap-3 md:gap-4 mb-5 md:mb-6">
                {{-- (View) Ikon status jawaban --}}
                <div class="w-12 h-12 md:w-14 md:h-14 shrink-0 flex items-center justify-center rounded-full shadow-sm border-[3px]" 
                     :class="feedbackModal.type === 'error' ? 'bg-red-50 border-red-100 dark:bg-red-900/30 dark:border-red-800' : 'bg-emerald-50 border-emerald-100 dark:bg-emerald-900/30 dark:border-emerald-800'">
                    <img x-show="feedbackModal.type === 'error'" src="{{ asset('images/alert.png') }}" class="w-7 h-7 md:w-8 md:h-8 object-contain">
                    <img x-show="feedbackModal.type === 'success'" src="{{ asset('images/bintang.png') }}" class="w-7 h-7 md:w-8 md:h-8 object-contain">
                </div>
                {{-- (View) Teks informasi hasil --}}
                <div>
                    <div class="text-xl md:text-2xl font-black tracking-wide" :class="feedbackModal.type === 'error' ? 'text-red-500' : 'text-emerald-500'" x-text="feedbackModal.title"></div>
                    <div class="text-sm md:text-base font-bold opacity-90 mt-0.5" :class="feedbackModal.type === 'error' ? 'text-red-400 dark:text-red-300' : 'text-emerald-400 dark:text-emerald-300'" x-text="feedbackModal.subtitle"></div>
                </div>
            </div>
            {{-- (View) Tombol aksi lanjutan --}}
            <button @click="handleFeedbackButton()" class="w-full py-3 md:py-3.5 rounded-xl font-black text-base md:text-lg text-white transition-all active:scale-95 border-b-[4px] active:border-b-0 active:translate-y-[4px]" 
                    :class="feedbackModal.type === 'error' ? 'bg-red-500 hover:bg-red-600 border-red-700' : 'bg-emerald-500 hover:bg-emerald-600 border-emerald-700'" 
                    x-text="feedbackModal.type === 'error' ? 'OKE' : 'LANJUT'"></button>
        </div>
    </div>

    {{-- (View) Modal pembesaran gambar skenario soal --}}
    <div x-show="scenarioMaximized" x-transition.opacity x-cloak class="scenario-modal" @click="scenarioMaximized = false">
        <button class="absolute top-8 right-8 mt-8 p-3 bg-white/10 hover:bg-red-500 text-white rounded-2xl transition-colors z-[600]">
            <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
        <img src="{{ str_replace('/upload/', '/upload/f_auto,q_auto/', $mission->mission_image) }}" class="max-w-full max-h-full object-contain rounded-3xl shadow-2xl relative z-40">
    </div>

    <template x-if="status === 'wrong'">
        <div class="flash-error"></div>
    </template>

    <div class="split-grid max-w-7xl mx-auto"> 
        {{-- (View) Area gambar monitor dan teks instruksi --}}
        <div class="scroll-column space-y-4 md:space-y-6">
            <div class="bg-white dark:bg-slate-900 p-3 md:p-4 rounded-[2rem] shadow-sm border-2 border-slate-200 dark:border-slate-800 border-b-[8px] relative group">
                <div class="absolute top-5 left-5 px-3 py-1 bg-slate-900 text-white text-[7px] font-black uppercase rounded-lg z-10">Monitor</div>
                <button @click="scenarioMaximized = true" class="absolute top-5 right-5 p-2 bg-emerald-500 text-white rounded-lg z-20 hover:scale-110 shadow-lg">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l5-5M4 16v4m0 0h4m-4 0l5-5m11 5v-4m0 4h-4m4 0l-5-5"/></svg>
                </button>
                <div class="overflow-hidden rounded-[1.5rem] bg-slate-50 dark:bg-slate-950 border border-slate-100 dark:border-slate-800/50 cursor-zoom-in" @click="scenarioMaximized = true">
                    <img src="{{ str_replace('/upload/', '/upload/f_auto,q_auto/', $mission->mission_image) }}" 
                         class="w-full h-auto object-contain max-h-[30vh] lg:max-h-[45vh] mx-auto" alt="Skenario">
                </div>
            </div>

            <div class="bg-emerald-600 text-white p-6 md:p-7 rounded-[2rem] md:rounded-[2.5rem] shadow-xl border-b-[8px] border-emerald-800 relative">
                <span class="text-[8px] font-black uppercase tracking-widest text-emerald-100">Instruksi Misi</span>
                <p class="text-sm md:text-lg font-bold mt-2 leading-relaxed tracking-tight">{{ $mission->question }}</p>
            </div>
        </div>

        {{-- (View) Area kerja perakitan rumus --}}
        <div class="scroll-column space-y-4 md:space-y-6">
            <div class="bg-white dark:bg-slate-900 p-4 md:p-6 rounded-[2rem] md:rounded-[2.5rem] shadow-sm border-2 border-slate-200 dark:border-slate-800 border-b-[8px] flex flex-col transition-all duration-300" 
                 :class="status === 'wrong' ? 'shake-error shadow-red-100' : ''">
                
                <div class="flex justify-between items-center mb-3 md:mb-5 px-1">
                    <h3 class="text-[9px] md:text-[10px] font-black text-slate-400 uppercase tracking-widest text-left">Kotak Rakitan</h3>
                    <span class="text-[7px] md:text-[8px] font-black text-emerald-500 uppercase tracking-widest animate-pulse" x-show="answerBox.length > 0">Klik hapus | Geser</span>
                </div>

                <div class="bg-slate-50 dark:bg-slate-950 rounded-[1.2rem] border-2 border-dashed border-slate-200 dark:border-slate-800 shadow-inner z-10 w-full">
                    <div id="dropzone" class="compact-dropzone w-full" x-ref="dropzone">
                    </div>
                </div>

                {{-- (View) Kotak informasi bantuan (Hint) --}}
                <div x-show="hint" x-transition class="mt-4 p-3 bg-amber-50 dark:bg-amber-900/20 border-2 border-amber-100 dark:border-amber-800 rounded-xl text-[10px] font-extrabold text-amber-700 dark:text-amber-400 text-center uppercase leading-snug">
                    <span x-text="hint"></span>
                </div>

                <button @click="submitSyntax($event)" class="btn-menu-pegas glow-emerald-premium w-full mt-5 py-3 md:py-4 bg-emerald-600 hover:bg-emerald-700 text-white rounded-[1.2rem] font-black text-[9px] md:text-[10px] tracking-[0.2em] uppercase border-emerald-800 shadow-lg">
                    Verifikasi Rakitan
                </button>
            </div>

            {{-- (View) Area daftar blok komponen rumus yang tersedia --}}
            <div class="bg-white dark:bg-slate-900 p-4 md:p-6 rounded-[2rem] md:rounded-[2.5rem] shadow-sm border-2 border-slate-200 dark:border-slate-800 border-b-[8px]">
                <h3 class="text-[9px] md:text-[10px] font-black text-slate-400 uppercase tracking-widest px-1 mb-4">Gudang Komponen</h3>
                <div class="flex flex-wrap gap-1.5 md:gap-2 justify-center">
                    <template x-for="(block, index) in availableBlocks" :key="'gudang-' + index">
                        <button @click="addToAnswer(block, $event)" :class="getBlockClass(block)"
                                class="token-block font-mono font-black shadow-sm border-2 active:scale-90">
                            <span x-text="block"></span>
                        </button>
                    </template>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- (View) Notifikasi pop-up (Toast) --}}
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

@endsection

@push('scripts')
{{-- (Library) Memuat script eksternal pendukung --}}
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
            
            // (State) Properti untuk tampilan modal dan efek suara
            showIntro: true,
            feedbackModal: { show: false, type: '', title: '', subtitle: '', nextUrl: '' },
            sfxClick: null,
            sfxBenar: null,
            sfxSalah: null,

            storageKey: 'mission_{{ $mission->id }}_syntax_progress',
            isReview: {{ (auth()->user()->progress && auth()->user()->progress->where('mission_id', $mission->id)->where('status', 'completed')->isNotEmpty()) ? 'true' : 'false' }},

            // (Action) Inisialisasi engine misi, memuat progres, dan mempersiapkan fitur audio
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
                        
                        if(this.answerBox.length > 0 || this.attempts > 0) {
                            this.showIntro = false;
                        }
                    }
                }

                this.$watch('currentPotentialXP', v => { 
                    const el = document.getElementById('header-xp-display');
                    if(el) el.innerText = v;
                });

                this.$watch('answerBox', () => { this.renderBox(); });

                // (Event) Memantau perubahan status zoom gambar layar
                this.$watch('scenarioMaximized', v => { this.toggleZoom(v); });

                const headerXp = document.getElementById('header-xp-display');
                if(headerXp) headerXp.innerText = this.currentPotentialXP;

                if (this.isReview) {
                    this.currentPotentialXP = {{ $mission->max_score }};
                    this.showIntro = false;
                }

                this.initSortable();
            },

            // (Helper) Mengatur konfigurasi viewport agar mengizinkan zoom in/out di perangkat mobile
            toggleZoom(isMaximized) {
                let metaViewport = document.querySelector('meta[name="viewport"]');
                if (isMaximized) {
                    if(metaViewport) metaViewport.setAttribute('content', 'width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes');
                } else {
                    if(metaViewport) metaViewport.setAttribute('content', 'width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no');
                }
            },

            // (Process) Merender ulang tampilan blok rumus di dalam area kotak perakitan
            renderBox() {
                const dropzone = this.$refs.dropzone;
                dropzone.innerHTML = ''; 
                
                this.answerBox.forEach((item, index) => {
                    const div = document.createElement('div');
                    div.className = this.getBlockClass(item) + ' token-block font-mono font-black shadow-sm border-2 cursor-grab active:cursor-grabbing';
                    div.innerText = item;
                    div.onclick = (e) => this.removeFromAnswer(index, e);
                    dropzone.appendChild(div);
                });
            },

            // (Action) Library SortableJS fitur geser-letak (drag-and-drop) blok rumus
            initSortable() {
                new Sortable(this.$refs.dropzone, {
                    animation: 150, 
                    easing: "cubic-bezier(0.25, 1, 0.5, 1)", 
                    ghostClass: 'sortable-ghost', 
                    dragClass: 'sortable-drag',
                    delay: 100, 
                    delayOnTouchOnly: true,
                    swapThreshold: 0.65, 
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

            // (Process) Menyimpan progres perakitan sementara ke penyimpanan lokal browser (Local Storage)
            saveToLocal() {
                if (this.isReview) return;
                const payload = {
                    answerBox: this.answerBox, attempts: this.attempts, currentPotentialXP: this.currentPotentialXP
                };
                localStorage.setItem(this.storageKey, JSON.stringify(payload));
            },

            // (Helper) Menampilkan notifikasi popup sementara
            triggerToast(title, message, icon = 'alert.png', type = 'info') {
                this.toast.title = title;
                this.toast.message = message;
                this.toast.icon = icon; 
                this.toast.type = type;
                this.toast.show = true;
                setTimeout(() => { this.toast.show = false; }, 3500);
            },

            // (Action) Menangani kejadian saat tombol pada modal umpan balik ditekan
            handleFeedbackButton() {
                this.feedbackModal.show = false;
                if (this.feedbackModal.type === 'success' && this.feedbackModal.nextUrl) {
                    window.location.href = this.feedbackModal.nextUrl;
                }
            },

            // (Helper) Membuka modal umpan balik dengan status sukses atau gagal
            triggerFeedbackModal(type, title, subtitle, nextUrl = '') {
                this.feedbackModal.type = type;
                this.feedbackModal.title = title;
                this.feedbackModal.subtitle = subtitle;
                this.feedbackModal.nextUrl = nextUrl;
                this.feedbackModal.show = true;
            },

            // (Helper) Menampilkan animasi perayaan kertas warna-warni
            fireConfetti() {
                var duration = 4 * 1000;
                var end = Date.now() + duration;
                (function frame() {
                    confetti({ particleCount: 5, angle: 60, spread: 55, origin: { x: 0 }, colors: ['#10b981', '#3b82f6', '#fbbf24', '#ffffff'] });
                    confetti({ particleCount: 5, angle: 120, spread: 55, origin: { x: 1 }, colors: ['#10b981', '#3b82f6', '#fbbf24', '#ffffff'] });
                    if (Date.now() < end) { requestAnimationFrame(frame); }
                }());
            },

            // (Helper) Menampilkan animasi partikel kegagalan berwarna merah
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

            // (Helper) Membuat efek teks melayang sesaat pada posisi klik kursor
            spawnFloatingText(e, text, color = '#fbbf24') {
                if (!e) return;
                const el = document.createElement('div');
                el.className = 'floating-text font-sans';
                el.innerText = text;
                el.style.left = (e.clientX - 20) + 'px';
                el.style.top = (e.clientY - 20) + 'px';
                el.style.color = color;
                document.body.appendChild(el);
                setTimeout(() => el.remove(), 1000);
            },

            // (Process) Menyusun dan mengacak daftar blok komponen rumus yang siap digunakan
            get availableBlocks() {
                let result = [];
                this.rawAvailableBlocks.forEach(block => {
                    let tokens = block.match(/[A-Z0-9\%]+|[\(\)\,\;\:\=\"\>\<\$\%]/g);
                    if (tokens) { result.push(...tokens); } else { result.push(block); }
                });
                return [...new Set(result)].sort();
            },

            // (Helper) Menentukan warna tema blok berdasarkan jenis konten teksnya (fungsi, sel, string)
            getBlockClass(block) {
                const funcRegex = /^(IF|SUM|AVERAGE|MIN|MAX|AND|OR|NOT|COUNT|VLOOKUP|HLOOKUP)$/i;
                const cellRegex = /^[A-Z]+\$?[0-9]+$/i;
                const stringRegex = /^".*"$/;
                const digitRegex = /^[0-9]+%?$/;

                if (funcRegex.test(block)) return 'bg-emerald-50 text-emerald-700 border-emerald-200 border-b-emerald-400 dark:bg-emerald-900/60 dark:text-emerald-300 dark:border-emerald-800';
                if (cellRegex.test(block)) return 'bg-blue-50 text-blue-700 border-blue-200 border-b-blue-400 dark:bg-blue-900/60 dark:text-blue-300 dark:border-blue-800';
                if (stringRegex.test(block) || digitRegex.test(block)) return 'bg-amber-50 text-amber-700 border-amber-200 border-b-amber-400 dark:bg-amber-950/60 dark:text-amber-300 dark:border-amber-800';
                return 'bg-slate-50 text-slate-500 border-slate-200 border-b-slate-400 dark:bg-slate-800 dark:text-slate-300 dark:border-slate-700';
            },

            // (Action) Menambahkan blok komponen dari gudang ke dalam kotak rakitan saat diklik
            addToAnswer(block, e) { 
                this.answerBox.push(block); 
                this.status = 'idle'; 
                this.hint = ''; 
                this.saveToLocal();
                
                if(this.sfxClick && this.sfxClick.readyState >= 2) { 
                    this.sfxClick.currentTime = 0; this.sfxClick.play().catch(()=>{}); 
                }
                this.spawnFloatingText(e, 'Pilih ⭐', '#10b981');
            },

            // (Action) Menghapus blok komponen dari kotak rakitan saat diklik
            removeFromAnswer(index, e) { 
                this.answerBox.splice(index, 1); 
                this.saveToLocal();
                
                if(this.sfxClick && this.sfxClick.readyState >= 2) { 
                    this.sfxClick.currentTime = 0; this.sfxClick.play().catch(()=>{}); 
                }
                this.spawnFloatingText(e, 'Hapus 💥', '#ef4444');
            },
            
            // (Action) Mengirim susunan rumus ke server untuk divalidasi
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
                        
                        // (Process) Menampilkan modal keberhasilan saat rumus tepat
                        this.triggerFeedbackModal('success', 'Tepat Sekali! 🎉', '+ ' + this.currentPotentialXP + ' XP Berhasil Diraih', data.next_url);
                        
                    } else {
                        this.status = 'wrong'; 
                        this.hint = data.message; 
                        this.attempts = data.attempts;
                        
                        if(this.sfxSalah && this.sfxSalah.readyState >= 2) { 
                            this.sfxSalah.currentTime = 0; this.sfxSalah.play().catch(()=>{}); 
                        }
                        
                        // (Process) Memicu efek partikel silang saat jawaban salah
                        this.fireCrossParticles(); 

                        // (Process) Menampilkan teks melayang tanda kesalahan
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