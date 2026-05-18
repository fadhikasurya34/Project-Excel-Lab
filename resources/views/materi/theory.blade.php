{{-- //* (View) Player Materi Teori (Sisi Siswa) --}}

@extends('layouts.siswa')

@section('title', 'Materi Teori - ' . $material->title)

@push('styles')
<style>
    /* =========================================================
       CSS ORIGINAL PDF (100% PERSIS KODE ASLI ANDA, TIDAK DIUBAH)
       ========================================================= */
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

    .video-container:fullscreen, .video-container:-webkit-full-screen {
        padding-bottom: 0;
        height: 100dvh;
        width: 100vw;
        border-radius: 0;
        border: none;
        background: #000;
    }

    body.is-ios-fs {
        background-color: #000 !important;
        /* FIX ZOOM: Ubah dari hidden ke auto agar bisa digeser saat di zoom */
        overflow: auto !important; 
        display: flex !important;
        flex-direction: column !important;
        align-items: center !important;
        justify-content: center !important;
        height: 100dvh !important;
        width: 100vw !important;
        margin: 0 !important;
        padding: 0 !important;
    }

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

    body.is-ios-fs .video-container {
        width: 100vw !important;
        height: 100dvh !important; 
        padding-bottom: 0 !important;
        border-radius: 0 !important;
        border: none !important;
        /* FIX ZOOM: Izinkan scroll saat di-zoom */
        overflow: auto !important; 
        -webkit-overflow-scrolling: touch !important; 
    }

    /* FIX ZOOM: Izinkan gesture zoom pada iframe saat layar penuh */
    body.is-ios-fs .video-container iframe {
        touch-action: pan-x pan-y pinch-zoom !important;
    }

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

    .video-container:fullscreen .btn-exit-fs, 
    .video-container:-webkit-full-screen .btn-exit-fs {
        display: flex !important; 
    }

    body.is-ios-fs .btn-exit-fs {
        display: flex !important;
        position: fixed !important;
        top: max(20px, env(safe-area-inset-top)) !important;
        right: max(20px, env(safe-area-inset-right)) !important;
    }

    /* Mencegah tabrakan saat fullscreen aktif */
    body.is-ios-fs .text-content-block { display: none !important; }

    /* //* (Card) Glass Effect untuk Deskripsi dengan Support Dark Mode */
    .description-card {
        background: rgba(255, 255, 255, 0.8);
        backdrop-filter: blur(10px);
        border: 2px solid #e2e8f0;
        border-radius: 2rem;
    }
    
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

    /* =========================================================
       CSS RUANG DISKUSI (MODERN CHAT / FB-YT STYLE)
       ========================================================= */
    .comment-thread-line {
        position: absolute; left: 19px; top: 40px; bottom: 0;
        width: 2px; background: #e2e8f0; border-radius: 10px;
    }
    .dark .comment-thread-line { background: #334155; }

    .comment-bubble {
        transition: all 0.2s ease;
        border: 2px solid transparent;
    }
    .my-comment-bubble {
        background: rgba(99, 102, 241, 0.05) !important;
        border-color: rgba(99, 102, 241, 0.2) !important;
    }
    .dark .my-comment-bubble {
        background: rgba(99, 102, 241, 0.1) !important;
        border-color: rgba(99, 102, 241, 0.3) !important;
    }

    /* Animasi Toast */
    @keyframes slideInUp {
        from { transform: translateY(100%); opacity: 0; }
        to { transform: translateY(0); opacity: 1; }
    }
    @keyframes fadeOut {
        from { opacity: 1; }
        to { opacity: 0; }
    }
    .toast-animate-in { animation: slideInUp 0.4s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
    .toast-animate-out { animation: fadeOut 0.4s ease forwards; }

    [x-cloak] { display: none !important; }
</style>
@endpush

@section('header_left')
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
        
        {{-- //* 1. Header Konten --}}
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

        {{-- //* 2. Area Multi-Konten Pembelajaran --}}
        <div class="mb-10 space-y-12 video-section">
            @php
                $videoUrl = $material->video_url ?? null; 
                $pdfUrl = $material->pdf_url ?? null; 
                $textContent = $material->text_content ?? null;
                $legacyUrl = $material->activities->first()->step_image ?? null;
                
                // Konversi Legacy URL
                if (empty($videoUrl) && empty($pdfUrl) && $legacyUrl) {
                    if (str_contains(strtolower($legacyUrl), 'youtube') || str_contains(strtolower($legacyUrl), 'mp4') || str_contains(strtolower($legacyUrl), 'drive.google')) { 
                        $videoUrl = $legacyUrl; 
                    } else { 
                        $pdfUrl = $legacyUrl; 
                    }
                }
                if ($videoUrl && $pdfUrl === $videoUrl) { $pdfUrl = null; }

                // FIX: Paksa Google Drive menjadi Embed Preview Bersih
                if ($videoUrl && str_contains($videoUrl, 'drive.google.com')) {
                    $videoUrl = preg_replace('/\/view.*/', '/preview', $videoUrl);
                }
            @endphp

            {{-- URUTAN 1: VIDEO TUTORIAL --}}
            @if($videoUrl)
            <div>
                <h3 class="text-lg md:text-xl font-black text-slate-800 dark:text-white mb-4 flex items-center gap-3 multi-content-header">
                    <span class="w-10 h-10 rounded-xl bg-blue-100 dark:bg-blue-900/50 text-blue-600 flex items-center justify-center shadow-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path><path d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </span>
                    Video Tutorial
                </h3>
                
                <div class="relative w-full overflow-hidden rounded-[1.5rem] border-4 border-white dark:border-slate-800 shadow-xl bg-black" style="padding-top: 56.25%;">
                    <iframe src="{{ $videoUrl }}" class="absolute top-0 left-0 w-full h-full border-none" allow="autoplay; fullscreen" allowfullscreen="true" webkitallowfullscreen="true" mozallowfullscreen="true"></iframe>
                </div>
                
                <p class="text-[10px] text-slate-500 mt-3 flex items-center gap-1.5 font-bold">
                    <svg class="w-3.5 h-3.5 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Putar HP Anda ke mode horizontal atau gunakan tombol [ ] di dalam video untuk memperbesar layar.
                </p>
            </div>
            @endif

            {{-- URUTAN 2: MODUL PDF --}}
            @if($pdfUrl)
            <div>
                <h3 class="text-lg md:text-xl font-black text-slate-800 dark:text-white mb-4 flex items-center gap-3 multi-content-header">
                    <span class="w-10 h-10 rounded-xl bg-red-100 dark:bg-red-900/50 text-red-600 flex items-center justify-center shadow-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                    </span>
                    Modul Interaktif (PDF)
                </h3>
                <div id="materi-container" class="video-container border-4 border-white dark:border-slate-800 shadow-2xl">
                    <button type="button" onclick="toggleCustomFullscreen()" class="btn-exit-fs" aria-label="Tutup Layar">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                    <iframe src="{{ $pdfUrl }}" allow="autoplay; fullscreen" allowfullscreen="true" webkitallowfullscreen="true" mozallowfullscreen="true"></iframe>
                </div>
                <div class="mt-4 flex justify-end fullscreen-btn-container">
                    <button type="button" onclick="toggleCustomFullscreen()" class="flex items-center gap-2 px-6 py-3 bg-slate-800 dark:bg-slate-700 hover:bg-slate-900 text-white rounded-xl shadow-sm transition-all active:scale-95 text-[11px] font-bold uppercase tracking-widest">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l5-5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"></path></svg>
                        <span>Perbesar Layar PDF</span>
                    </button>
                </div>
            </div>
            @endif

            {{-- URUTAN 3: TEKS MATERI --}}
            @if($textContent)
            <div class="text-content-block">
                <h3 class="text-lg md:text-xl font-black text-slate-800 dark:text-white mb-4 flex items-center gap-3 multi-content-header">
                    <span class="w-10 h-10 rounded-xl bg-emerald-100 dark:bg-emerald-900/50 text-emerald-600 flex items-center justify-center shadow-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    </span>
                    Bahan Bacaan
                </h3>
                <div class="description-card p-6 md:p-8 shadow-sm">
                    <div id="textContainer" class="relative overflow-hidden" style="max-height: 180px; transition: max-height 0.6s ease;">
                        <div class="prose prose-slate dark:prose-invert max-w-none text-[14px] md:text-[15px] leading-loose whitespace-pre-line tracking-wide text-slate-700 dark:text-slate-300 prose-p:text-justify prose-li:my-1 pb-4">
                            {{ $textContent }}
                        </div>
                        <div id="textOverlay" class="absolute bottom-0 left-0 right-0 h-20 bg-gradient-to-t from-[rgba(255,255,255,0.98)] dark:from-[rgba(30,41,59,0.98)] to-transparent pointer-events-none transition-opacity duration-300"></div>
                    </div>
                    <button id="btnToggleText" onclick="toggleTextExpansion()" class="mt-4 flex items-center justify-center w-full py-3.5 bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 font-bold text-xs rounded-xl hover:bg-emerald-100 dark:hover:bg-emerald-800/50 transition-colors border border-emerald-100 dark:border-emerald-800 shadow-sm active:scale-[0.98]">
                        <span id="btnTextLabel">BACA TEKS</span>
                        <svg id="btnTextIcon" class="w-4 h-4 ml-2 transition-transform duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                </div>
            </div>
            @endif
        </div>

        {{-- //* 3. Instruksi & Info --}}
        <div class="grid grid-cols-1 gap-6 info-section">
            <div class="description-card p-6 lg:p-8 shadow-sm">
                <div class="flex items-center space-x-4">
                    <div class="w-10 h-10 bg-blue-50 dark:bg-blue-900/30 rounded-full flex shrink-0 items-center justify-center">
                        <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477-4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                    </div>
                    <div>
                        <p class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest leading-none mb-1">Instruksi</p>
                        <p class="text-xs font-bold text-slate-700 dark:text-slate-300">Pelajari materi di atas dengan seksama.</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- //* 4. Ruang Diskusi Kelas (FB/YT STYLE) --}}
        <div class="mt-12 info-section" x-data="{ replyTo: null, replyName: '' }" id="diskusi-section">
            
            <h3 id="diskusi-header" class="text-lg md:text-xl font-black text-slate-800 dark:text-white mb-6 flex items-center gap-3 multi-content-header">
                <span class="w-10 h-10 rounded-xl bg-indigo-100 dark:bg-indigo-900/50 text-indigo-600 flex items-center justify-center shadow-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z"></path></svg>
                </span>
                Diskusi Pelajaran ({{ $material->comments ? $material->comments->count() : 0 }})
            </h3>

            <div class="bg-white dark:bg-slate-800 border-2 border-slate-100 dark:border-slate-700 rounded-[2.5rem] shadow-sm overflow-hidden p-6 md:p-8">
                
                {{-- Form Kirim Komentar Utama --}}
                <div class="mb-10">
                    <form action="{{ route('materi.comment', $material->id) }}" method="POST" class="flex gap-4" onsubmit="submitAjax(event, this, 'Komentar dikirim!')">
                        @csrf
                        <div class="shrink-0 w-10 h-10 rounded-xl border-2 border-slate-100 dark:border-slate-700 overflow-hidden shadow-sm" style="background-color: #{{ Auth::user()->profile_color ?? '10b981' }}">
                            <img src="https://api.dicebear.com/9.x/bottts/svg?seed={{ Auth::user()->avatar ?? 'Felix' }}&backgroundColor=transparent" class="w-full h-full object-contain p-0.5">
                        </div>

                        <div class="flex-1 relative">
                            <textarea name="body" rows="1" class="w-full pl-5 pr-14 py-3 bg-slate-50 dark:bg-slate-900 border-b-2 border-slate-200 dark:border-slate-700 focus:border-indigo-500 focus:outline-none text-sm font-medium text-slate-800 dark:text-white transition-all placeholder-slate-400 resize-none overflow-hidden" placeholder="Tulis komentar publik..." required oninput="this.style.height = '';this.style.height = this.scrollHeight + 'px'"></textarea>
                            <button type="submit" class="absolute right-2 top-2 p-1.5 text-indigo-600 hover:scale-110 transition-transform active:scale-90 submit-btn">
                                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z"></path></svg>
                            </button>
                        </div>
                    </form>
                </div>

                {{-- Thread Komentar --}}
                <div id="diskusi-list" class="space-y-8">
                    @forelse($material->comments as $comment)
                        <div class="relative">
                            {{-- Thread Line --}}
                            @if($comment->replies->count() > 0)
                                <div class="comment-thread-line" style="bottom: -20px;"></div>
                            @endif

                            {{-- Komentar Utama --}}
                            <div class="flex gap-4 group">
                                <div class="shrink-0 w-10 h-10 rounded-xl border-2 border-white dark:border-slate-700 shadow-md flex items-center justify-center z-10 overflow-hidden" style="background-color: #{{ $comment->user->profile_color ?? '10b981' }}">
                                    <img src="https://api.dicebear.com/9.x/bottts/svg?seed={{ $comment->user->avatar ?? 'Felix' }}&backgroundColor=transparent" class="w-full h-full object-contain p-0.5">
                                </div>

                                <div class="flex-1">
                                    <div class="comment-bubble p-4 rounded-2xl bg-slate-50 dark:bg-slate-900/50 {{ $comment->user_id === Auth::id() ? 'my-comment-bubble' : '' }}">
                                        <div class="flex items-center gap-2 mb-1">
                                            <span class="text-[12px] font-black text-slate-800 dark:text-slate-200 capitalize">
                                                {{ $comment->user->name }}
                                                @if($comment->user_id === Auth::id()) <span class="ml-1 text-[8px] bg-indigo-500 text-white px-1.5 py-0.5 rounded-full">SAYA</span> @endif
                                            </span>
                                            <span class="text-[9px] font-bold text-slate-400">{{ $comment->created_at->diffForHumans() }}</span>
                                        </div>
                                        <p class="text-[13px] text-slate-600 dark:text-slate-400 leading-relaxed whitespace-pre-line">{{ $comment->body }}</p>
                                    </div>

                                    {{-- Actions --}}
                                    <div class="flex items-center gap-5 mt-2 ml-2 mb-4">
                                        <div class="flex items-center gap-1">
                                            <form action="{{ route('comment.react', [$comment->id, 'like']) }}" method="POST" onsubmit="submitAjax(event, this)">@csrf
                                                <button type="submit" class="text-[10px] font-black text-slate-400 hover:text-blue-500 flex items-center gap-1 submit-btn">👍 {{ $comment->likes ?? 0 }}</button>
                                            </form>
                                            <form action="{{ route('comment.react', [$comment->id, 'dislike']) }}" method="POST" onsubmit="submitAjax(event, this)">@csrf
                                                <button type="submit" class="text-[10px] font-black text-slate-400 hover:text-red-500 flex items-center gap-1 submit-btn">👎 {{ $comment->dislikes ?? 0 }}</button>
                                            </form>
                                        </div>
                                        <button @click="replyTo = {{ $comment->id }}" class="text-[10px] font-black text-slate-500 hover:text-indigo-600 uppercase tracking-widest">Balas</button>
                                        
                                        @if($comment->user_id === Auth::id())
                                            <form action="{{ route('materi.comment.destroy', $comment->id) }}" method="POST" onsubmit="event.preventDefault(); openDeleteModal(this, 'Komentar Utama Dihapus!');">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="text-[10px] font-black text-red-400 hover:text-red-600 uppercase tracking-widest submit-btn">Hapus</button>
                                            </form>
                                        @endif
                                    </div>

                                    {{-- Form Balasan Inline --}}
                                    <div x-show="replyTo === {{ $comment->id }}" x-cloak class="mt-2 mb-6 ml-4">
                                        <form action="{{ route('materi.comment', $material->id) }}" method="POST" class="flex gap-3" onsubmit="submitAjax(event, this, 'Balasan dikirim!')">
                                            @csrf
                                            <input type="hidden" name="parent_id" value="{{ $comment->id }}">
                                            <div class="shrink-0 w-8 h-8 rounded-xl border-2 border-slate-100 overflow-hidden" style="background-color: #{{ Auth::user()->profile_color ?? '10b981' }}">
                                                <img src="https://api.dicebear.com/9.x/bottts/svg?seed={{ Auth::user()->avatar ?? 'Felix' }}&backgroundColor=transparent" class="w-full h-full object-contain p-0.5">
                                            </div>
                                            <div class="flex-1 relative">
                                                <input type="text" name="body" class="w-full pl-4 pr-12 py-2 bg-slate-100 dark:bg-slate-900 border-none rounded-xl text-xs font-medium text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500" placeholder="Balas {{ $comment->user->name }}..." required>
                                                <button type="submit" class="absolute right-2 top-1.5 p-1 text-indigo-600 hover:scale-110 transition-transform submit-btn">
                                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z"></path></svg>
                                                </button>
                                            </div>
                                            <button type="button" @click="replyTo = null" class="cancel-reply text-xs text-slate-400 hover:text-red-500 font-bold px-2">Batal</button>
                                        </form>
                                    </div>

                                    {{-- List Balasan --}}
                                    <div class="space-y-6 mt-4">
                                        @foreach($comment->replies->sortByDesc('created_at') as $reply)
                                            <div class="flex gap-3 ml-4 relative z-10">
                                                <div class="shrink-0 w-8 h-8 rounded-xl border-2 border-white dark:border-slate-700 shadow-sm flex items-center justify-center overflow-hidden" style="background-color: #{{ $reply->user->profile_color ?? '10b981' }}">
                                                    <img src="https://api.dicebear.com/9.x/bottts/svg?seed={{ $reply->user->avatar ?? 'Felix' }}&backgroundColor=transparent" class="w-full h-full object-contain p-0.5">
                                                </div>
                                                <div class="flex-1">
                                                    <div class="comment-bubble p-3.5 rounded-xl bg-slate-50 dark:bg-slate-900/30 {{ $reply->user_id === Auth::id() ? 'my-comment-bubble' : '' }}">
                                                        <div class="flex items-center gap-2 mb-1">
                                                            <span class="text-[11px] font-black text-slate-700 dark:text-slate-300 capitalize">
                                                                {{ $reply->user->name }}
                                                                @if($reply->user_id === Auth::id()) <span class="ml-1 text-[7px] bg-indigo-500 text-white px-1.5 py-0.5 rounded-full">SAYA</span> @endif
                                                            </span>
                                                            <span class="text-[9px] font-bold text-slate-400">{{ $reply->created_at->diffForHumans() }}</span>
                                                        </div>
                                                        <p class="text-[12px] text-slate-600 dark:text-slate-400 leading-relaxed">{{ $reply->body }}</p>
                                                    </div>

                                                    {{-- Tombol Hapus Balasan --}}
                                                    @if($reply->user_id === Auth::id())
                                                        <div class="flex mt-1.5 ml-1">
                                                            <form action="{{ route('materi.comment.destroy', $reply->id) }}" method="POST" onsubmit="event.preventDefault(); openDeleteModal(this, 'Balasan dihapus!');">
                                                                @csrf @method('DELETE')
                                                                <button type="submit" class="text-[9px] font-bold text-red-400 hover:text-red-600 uppercase tracking-wider submit-btn">Hapus</button>
                                                            </form>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="flex flex-col items-center justify-center py-10 opacity-40">
                            <p class="text-xs font-black text-slate-500 uppercase tracking-widest text-center">Belum ada diskusi.<br>Mulai obrolan sekarang!</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

    </div>
</div>

{{-- Container untuk Toast Notification Custom --}}
<div id="toast-container" class="fixed bottom-6 right-6 z-[9999] flex flex-col gap-3 pointer-events-none"></div>

{{-- Custom Delete Confirmation Modal --}}
<div id="delete-modal" class="fixed inset-0 z-[99999] hidden items-center justify-center p-4">
    <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" onclick="closeDeleteModal()"></div>
    <div class="relative bg-white dark:bg-slate-800 rounded-3xl p-6 md:p-8 max-w-sm w-full shadow-2xl transform scale-95 opacity-0 transition-all duration-200" id="delete-modal-content">
        <div class="w-14 h-14 bg-red-100 dark:bg-red-900/30 rounded-full flex items-center justify-center mb-4 mx-auto">
            <svg class="w-7 h-7 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
        </div>
        <h3 class="text-lg font-black text-slate-800 dark:text-white text-center mb-2">Hapus Pesan?</h3>
        <p class="text-sm text-slate-500 dark:text-slate-400 text-center mb-6">Pesan ini akan hilang permanen dari diskusi.</p>
        <div class="flex gap-3">
            <button onclick="closeDeleteModal()" class="flex-1 py-3 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-300 font-bold rounded-xl transition-colors text-xs uppercase tracking-wider">Batal</button>
            <button id="confirm-delete-btn" class="flex-1 py-3 bg-red-500 hover:bg-red-600 text-white font-bold rounded-xl transition-colors text-xs uppercase tracking-wider shadow-lg shadow-red-500/30">Ya, Hapus</button>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    // --- FUNGSI FULLSCREEN ORIGINAL (KHUSUS UNTUK PDF SAJA) ---
    const container = document.getElementById("materi-container");
    
    function toggleCustomFullscreen() {
        const isCurrentlyIOSFS = document.body.classList.contains('is-ios-fs');
        if (isCurrentlyIOSFS) { disableIOSFallback(); return; }
        
        const isIOS = /iPad|iPhone|iPod/.test(navigator.userAgent) && !window.MSStream;
        if (!document.fullscreenElement && !document.webkitFullscreenElement && !document.msFullscreenElement) {
            const reqFS = container.requestFullscreen || container.webkitRequestFullscreen || container.msRequestFullscreen;
            if (reqFS && !isIOS) {
                reqFS.call(container).catch(err => { enableIOSFallback(); });
            } else { 
                enableIOSFallback(); 
            }
        } else {
            const extFS = document.exitFullscreen || document.webkitExitFullscreen || document.msExitFullscreen;
            if (extFS) { extFS.call(document); }
        }
    }
    
    function enableIOSFallback() { 
        window.scrollTo(0, 0); 
        document.body.classList.add('is-ios-fs'); 
        
        // FIX ZOOM PDF: Hapus tag meta lama dan buat baru agar HP (iOS/Android) mereset sensor zoom
        let oldMeta = document.querySelector('meta[name="viewport"]');
        if (oldMeta) oldMeta.remove();
        
        let newMeta = document.createElement('meta');
        newMeta.name = "viewport";
        newMeta.content = "width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes";
        document.head.appendChild(newMeta);
    }
    
    function disableIOSFallback() { 
        document.body.classList.remove('is-ios-fs'); 
        
        // FIX ZOOM PDF: Kunci kembali dan paksa browser reset zoom level ke 1.0
        let oldMeta = document.querySelector('meta[name="viewport"]');
        if (oldMeta) oldMeta.remove();
        
        let newMeta = document.createElement('meta');
        newMeta.name = "viewport";
        newMeta.content = "width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no";
        document.head.appendChild(newMeta);

        // Paksa scroll reset agar halaman tidak tersangkut setelah zoom
        setTimeout(() => {
            window.scrollTo(0, 0);
        }, 50);
    }
    
    ['fullscreenchange', 'webkitfullscreenchange', 'msfullscreenchange'].forEach(eventType => {
        document.addEventListener(eventType, () => {
            if (!document.fullscreenElement && !document.webkitFullscreenElement && !document.msFullscreenElement) { 
                disableIOSFallback(); 
            }
        });
    });

    // --- FUNGSI BACA TEKS ---
    function toggleTextExpansion() {
        const textContainer = document.getElementById('textContainer');
        const overlay = document.getElementById('textOverlay');
        const label = document.getElementById('btnTextLabel');
        const icon = document.getElementById('btnTextIcon');
        if (textContainer.style.maxHeight === '180px' || textContainer.style.maxHeight === '') {
            textContainer.style.maxHeight = textContainer.scrollHeight + "px";
            overlay.style.opacity = '0';
            label.innerText = 'MINIMIZE TEKS';
            icon.style.transform = 'rotate(180deg)';
        } else {
            textContainer.style.maxHeight = '180px';
            overlay.style.opacity = '1';
            label.innerText = 'BACA TEKS';
            icon.style.transform = 'rotate(0deg)';
            textContainer.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    }

    // --- FUNGSI CUSTOM TOAST NOTIFICATION ---
    function showToast(message) {
        if (!message) return; 
        const toast = document.createElement('div');
        toast.className = 'flex items-center gap-3 bg-slate-900 text-white px-5 py-3 rounded-2xl shadow-2xl toast-animate-in pointer-events-auto border border-slate-700';
        toast.innerHTML = `
            <div class="w-6 h-6 bg-emerald-500 rounded-full flex items-center justify-center shrink-0">
                <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path></svg>
            </div>
            <p class="text-xs font-bold tracking-wide">${message}</p>
        `;
        document.getElementById('toast-container').appendChild(toast);
        setTimeout(() => {
            toast.classList.replace('toast-animate-in', 'toast-animate-out');
            setTimeout(() => toast.remove(), 400);
        }, 3000);
    }

    // --- FUNGSI CUSTOM DELETE MODAL ---
    let formToDelete = null;
    let deleteToastMsg = '';

    function openDeleteModal(form, toastMsg) {
        formToDelete = form;
        deleteToastMsg = toastMsg;
        const modal = document.getElementById('delete-modal');
        const content = document.getElementById('delete-modal-content');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        setTimeout(() => {
            content.classList.remove('scale-95', 'opacity-0');
            content.classList.add('scale-100', 'opacity-100');
        }, 10);
    }

    function closeDeleteModal() {
        const modal = document.getElementById('delete-modal');
        const content = document.getElementById('delete-modal-content');
        content.classList.remove('scale-100', 'opacity-100');
        content.classList.add('scale-95', 'opacity-0');
        setTimeout(() => {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            formToDelete = null;
        }, 200);
    }

    document.getElementById('confirm-delete-btn').addEventListener('click', function() {
        if (formToDelete) {
            submitAjax(new Event('submit'), formToDelete, deleteToastMsg);
            closeDeleteModal();
        }
    });

    // --- FUNGSI AJAX KIRIM/HAPUS KOMENTAR ---
    async function submitAjax(e, form, toastMessage = null) {
        e.preventDefault();
        const btn = form.querySelector('.submit-btn');
        if(btn) { btn.style.opacity = '0.5'; btn.style.pointerEvents = 'none'; }

        try {
            const formData = new FormData(form);
            const response = await fetch(form.action, {
                method: 'POST', // Laravel HTTP Method Spoofing
                body: formData,
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });

            if (response.ok) {
                const freshPage = await fetch(window.location.href);
                const htmlText = await freshPage.text();
                const parser = new DOMParser();
                const doc = parser.parseFromString(htmlText, 'text/html');
                
                document.getElementById('diskusi-list').innerHTML = doc.getElementById('diskusi-list').innerHTML;
                document.getElementById('diskusi-header').innerHTML = doc.getElementById('diskusi-header').innerHTML;
                
                form.reset();
                const cancelBtn = form.querySelector('.cancel-reply');
                if (cancelBtn) cancelBtn.click();

                showToast(toastMessage);
            }
        } catch (error) {
            console.error('Error:', error);
            showToast('Terjadi kesalahan koneksi.');
        } finally {
            if(btn) { btn.style.opacity = '1'; btn.style.pointerEvents = 'auto'; }
        }
    }
</script>
@endpush