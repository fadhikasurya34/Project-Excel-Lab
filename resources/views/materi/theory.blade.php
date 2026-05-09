{{-- //* (View) Player Materi Teori (Sisi Siswa) --}}

@extends('layouts.siswa')

@section('title', 'Materi Teori - ' . $material->title)

@push('styles')
<style>
    /* //* (UI) Kontainer Iframe Responsif Asli Anda */
    .video-container {
        position: relative;
        padding-bottom: 56.25%; /* Ratio 16:9 */
        height: 0;
        overflow: hidden;
        border-radius: 1.5rem;
        background: #000;
        box-shadow: 0 20px 50px -10px rgba(0, 0, 0, 0.2);
        transition: all 0.3s ease; 
    }

    .video-container iframe {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        border: 0;
        z-index: 10;
    }

    /* Penyesuaian Kontainer saat Masuk Fullscreen Native (Android/Desktop) */
    .video-container:fullscreen, .video-container:-webkit-full-screen {
        padding-bottom: 0;
        height: 100dvh;
        width: 100vw;
        border-radius: 0;
        border: none;
        background: #000;
    }

    /* //* STRATEGI BARU IOS SAFARI: "PURE FLEXBOX CENTER"
       Bypass bug Safari: Sembunyikan elemen sekitar dan hitamkan layar. Jangan ubah position iframe.
    */
    body.is-ios-fs {
        background-color: #000 !important;
        overflow: hidden !important; 
        display: flex !important;
        flex-direction: column !important;
        align-items: center !important;
        justify-content: center !important;
        height: 100dvh !important;
        width: 100vw !important;
        margin: 0 !important;
        padding: 0 !important;
    }

    /* Sembunyikan SEMUA elemen layout bawaan yang mengganggu */
    body.is-ios-fs header, 
    body.is-ios-fs nav, 
    body.is-ios-fs aside,
    body.is-ios-fs [class*="fixed top-0"], 
    body.is-ios-fs .z-50,
    body.is-ios-fs .content-header,
    body.is-ios-fs .info-section,
    body.is-ios-fs .fullscreen-btn-container {
        display: none !important;
        opacity: 0 !important;
        visibility: hidden !important;
    }

    /* Lepaskan batasan ukuran dari pembungkus konten */
    body.is-ios-fs .main-wrapper,
    body.is-ios-fs .inner-wrapper,
    body.is-ios-fs .video-section {
        padding: 0 !important;
        margin: 0 !important;
        max-width: 100% !important;
        width: 100% !important;
        height: auto !important;
        border: none !important;
        box-shadow: none !important;
        background: transparent !important;
    }

    /* Mekarkan video container menutupi seluruh lebar layar */
    body.is-ios-fs .video-container {
        width: 100vw !important;
        border-radius: 0 !important;
        border: none !important;
        /* Biarkan padding-bottom 56.25% agar aspect ratio tetap waras & Safari tidak ngebug */
    }

    /* //* Tombol Keluar Layar Penuh */
    .btn-exit-fs {
        display: none; 
        position: absolute;
        top: 20px; 
        right: 20px;
        z-index: 2147483647 !important; 
        background: rgba(220, 38, 38, 0.7); 
        color: white;
        width: 44px; 
        height: 44px;
        padding: 0; 
        border-radius: 50%; 
        border: 2px solid rgba(255, 255, 255, 0.4);
        backdrop-filter: blur(8px);
        align-items: center;
        justify-content: center;
        cursor: pointer;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
        pointer-events: auto !important;
        -webkit-tap-highlight-color: transparent;
    }
    
    .btn-exit-fs:active {
        background: rgba(185, 28, 28, 1);
        transform: scale(0.90);
    }

    /* Munculkan tombol saat Fullscreen Native atau Fallback iOS */
    .video-container:fullscreen .btn-exit-fs, 
    .video-container:-webkit-full-screen .btn-exit-fs {
        display: flex !important; 
    }

    /* Untuk iOS, tombol dibuat fixed agar menempel di layar HP, bukan di video */
    body.is-ios-fs .btn-exit-fs {
        display: flex !important;
        position: fixed !important;
        top: max(20px, env(safe-area-inset-top)) !important;
        right: max(20px, env(safe-area-inset-right)) !important;
    }

    /* //* (Card) Glass Effect untuk Deskripsi dengan Support Dark Mode */
    .description-card {
        background: rgba(255, 255, 255, 0.8);
        backdrop-filter: blur(10px);
        border: 2px solid #e2e8f0;
        border-radius: 2rem;
    }
    
    /* Perbaikan Kontras Warna Dark Mode Secara Manual */
    :is(.dark .description-card) {
        background: rgba(30, 41, 59, 0.8); /* slate-800 */
        border-color: #334155; /* slate-700 */
    }

    .btn-back-pegas {
        transition: all 0.1s ease;
        border-bottom-width: 6px;
    }
    .btn-back-pegas:active {
        transform: translateY(2px);
        border-bottom-width: 0px;
    }
</style>
@endpush

@section('header_left')
    {{-- //* (Nav) Kembali ke Daftar Modul --}}
    <a href="{{ route('materi.category.list', $material->category_id) }}" class="p-2 bg-white dark:bg-slate-800 border-2 border-slate-200 dark:border-slate-700 rounded-xl btn-back-pegas text-slate-600 dark:text-slate-300 shadow-sm active:scale-90 transition-transform">
        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
        </svg>
    </a>
    <div class="flex flex-col text-left ml-3 leading-none">
        <span class="text-base font-extrabold tracking-tight dark:text-white uppercase leading-none">Materi Teori</span>
        <div class="flex items-center space-x-1.5 mt-1.5">
            <span class="w-1.5 h-1.5 bg-blue-500 rounded-full animate-pulse"></span>
            <span class="text-[7px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest leading-none">Sedang Dipelajari</span>
        </div>
    </div>
@endsection

@section('content')
<div class="px-4 sm:px-10 py-8 main-wrapper">
    <div class="max-w-4xl mx-auto inner-wrapper">
        
        {{-- //* 1. Header Konten (Judul & Deskripsi Paling Atas) --}}
        <div class="mb-6 content-header">
            <div class="flex items-center space-x-3 mb-4">
                <span class="px-3 py-1 bg-blue-100 dark:bg-blue-900/50 text-blue-600 dark:text-blue-400 rounded-lg text-[10px] font-black uppercase tracking-widest">
                    {{ $material->material_type }}
                </span>
                <span class="text-slate-300 dark:text-slate-600">/</span>
                <span class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest">
                    ID Modul: #{{ str_pad($material->id, 4, '0', STR_PAD_LEFT) }}
                </span>
            </div>
            
            <h1 class="text-2xl lg:text-3xl font-black text-slate-900 dark:text-white mb-4 capitalize">
                {{ strtolower($material->title) }}
            </h1>
            
            <div class="prose prose-slate dark:prose-invert max-w-none mb-2">
                <p class="text-slate-600 dark:text-slate-400 leading-relaxed font-medium">
                    {{ $material->description }}
                </p>
            </div>
        </div>

        {{-- //* 2. (Player) Area Konten Utama (PDF/Video) --}}
        <div class="mb-8 video-section">
            @php
                $contentUrl = $material->activities->first()->step_image ?? null;
            @endphp

            @if($contentUrl)
                {{-- ID ditambahkan ke kontainer untuk target script --}}
                <div id="materi-container" class="video-container border-4 border-white dark:border-slate-800 shadow-2xl">
                    
                    {{-- Tombol Keluar Darurat --}}
                    <button type="button" onclick="toggleCustomFullscreen()" class="btn-exit-fs" aria-label="Tutup Layar">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>

                    {{-- iframe dibiarkan berjalan normal --}}
                    <iframe src="{{ $contentUrl }}" allow="autoplay; fullscreen"></iframe>
                    
                </div>

                {{-- Toolbar Bawah Player (Digunakan untuk Dokumen/PDF) --}}
                <div class="mt-4 flex justify-end fullscreen-btn-container">
                    <button type="button" onclick="toggleCustomFullscreen()" class="flex items-center gap-2 px-6 py-3 bg-slate-800 dark:bg-slate-700 hover:bg-slate-900 text-white rounded-xl shadow-sm transition-all active:scale-95 text-[11px] font-bold uppercase tracking-widest">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l5-5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"></path>
                        </svg>
                        <span>Perbesar Layar</span>
                    </button>
                </div>
            @else
                <div class="bg-slate-200 dark:bg-slate-800 rounded-[2rem] h-96 flex items-center justify-center border-4 border-dashed border-slate-300 dark:border-slate-700">
                    <p class="text-slate-500 dark:text-slate-400 font-bold uppercase tracking-widest text-xs">Konten tidak tersedia</p>
                </div>
            @endif
        </div>

        {{-- //* 3. Instruksi & Info --}}
        <div class="grid grid-cols-1 gap-6 info-section">
            
            {{-- Instruksi Card --}}
            <div class="description-card p-6 lg:p-8 shadow-sm">
                <div class="flex items-center space-x-4">
                    <div class="w-10 h-10 bg-blue-50 dark:bg-blue-900/30 rounded-full flex shrink-0 items-center justify-center">
                        <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-width="2.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest leading-none mb-1">Instruksi</p>
                        <p class="text-xs font-bold text-slate-700 dark:text-slate-300">Pelajari materi di atas dengan seksama.</p>
                    </div>
                </div>
            </div>

            {{-- Tips Belajar --}}
            <div class="bg-white dark:bg-slate-800 border-2 border-slate-100 dark:border-slate-700 p-6 lg:p-8 rounded-[2rem] shadow-sm">
                <h4 class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-[0.2em] mb-4">Tips Belajar</h4>
                <ul class="space-y-3">
                    <li class="flex items-start space-x-3 text-xs font-bold text-slate-600 dark:text-slate-300">
                        <span class="text-blue-500 mt-0.5">●</span>
                        <span>Gunakan mode fullscreen pada player untuk tampilan lebih jelas.</span>
                    </li>
                    <li class="flex items-start space-x-3 text-xs font-bold text-slate-600 dark:text-slate-300">
                        <span class="text-blue-500 mt-0.5">●</span>
                        <span>Catat poin-poin penting sebelum lanjut ke praktikum.</span>
                    </li>
                </ul>
            </div>
            
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script>
    const container = document.getElementById("materi-container");

    function toggleCustomFullscreen() {
        // Deteksi apakah web dalam mode Fallback iOS
        const isCurrentlyIOSFS = document.body.classList.contains('is-ios-fs');

        if (isCurrentlyIOSFS) {
            disableIOSFallback();
            return;
        }

        // Deteksi cerdas apakah pengguna memakai iPhone/iPad/iPod
        const isIOS = /iPad|iPhone|iPod/.test(navigator.userAgent) && !window.MSStream;

        if (!document.fullscreenElement && !document.webkitFullscreenElement) {
            
            const reqFS = container.requestFullscreen || container.webkitRequestFullscreen || container.msRequestFullscreen;
            
            // HANYA jalankan Native Fullscreen jika alatnya BUKAN Apple iOS
            if (reqFS && !isIOS) {
                reqFS.call(container).catch(err => {
                    console.error("Native Fullscreen ditolak, memicu fallback.");
                    enableIOSFallback();
                });
            } else {
                // Di iPhone/iPad (Terutama untuk Dokumen PDF), gunakan fungsi In-Place Expand
                enableIOSFallback();
            }
            
        } else {
            // KELUAR FULLSCREEN NATIVE (Android/Desktop)
            const extFS = document.exitFullscreen || document.webkitExitFullscreen || document.msExitFullscreen;
            if (extFS) {
                extFS.call(document);
            }
        }
    }

    // Fungsi Jurus Pamungkas iOS: In-Place Expand (Hanya untuk dokumen/fallback)
    function enableIOSFallback() {
        window.scrollTo(0, 0); // Pastikan layar naik ke paling atas
        document.body.classList.add('is-ios-fs');
    }

    function disableIOSFallback() {
        document.body.classList.remove('is-ios-fs');
    }

    // Pendengar event jika user Android/PC keluar pakai tombol ESC atau Back
    ['fullscreenchange', 'webkitfullscreenchange', 'msfullscreenchange'].forEach(eventType => {
        document.addEventListener(eventType, () => {
            if (!document.fullscreenElement && !document.webkitFullscreenElement && !document.msFullscreenElement) {
                disableIOSFallback();
            }
        });
    });
</script>
@endpush