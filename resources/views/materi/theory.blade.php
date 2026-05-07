{{-- //* (View) Player Materi Teori (Sisi Siswa) --}}

@extends('layouts.siswa')

@section('title', 'Materi Teori - ' . $material->title)

@push('styles')
<style>
    /* //* (UI) Kontainer Iframe Responsif */
    .video-container {
        position: relative;
        padding-bottom: 56.25%; /* Ratio 16:9 */
        height: 0;
        overflow: hidden;
        border-radius: 1.5rem;
        background: #000;
        box-shadow: 0 20px 50px -10px rgba(0, 0, 0, 0.2);
    }

    .video-container iframe {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        border: 0;
    }

    /* //* (Card) Glass Effect untuk Deskripsi */
    .description-card {
        background: rgba(255, 255, 255, 0.8);
        backdrop-filter: blur(10px);
        border: 2px solid #e2e8f0;
        border-radius: 2rem;
    }

    .btn-back-pegas {
        transition: all 0.1s ease;
        border-bottom-width: 6px;
    }
    .btn-back-pegas:active {
        transform: translateY(2px);
        border-bottom-width: 0px;
    }

    .btn-finish-pegas {
        transition: all 0.1s ease;
        border-bottom-width: 6px;
        background-color: #4f46e5;
        color: white;
    }
    .btn-finish-pegas:active {
        transform: translateY(4px);
        border-bottom-width: 2px;
    }
</style>
@endpush

@section('header_left')
    {{-- //* (Nav) Kembali ke Daftar Modul --}}
    <a href="{{ route('materi.category.list', $material->category_id) }}" class="p-2 bg-white dark:bg-slate-800 border-2 border-slate-200 dark:border-slate-700 rounded-xl btn-back-pegas text-slate-600 dark:text-slate-300 shadow-sm active:scale-90 transition-transform">
        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
            <path d="M15 19l-7-7 7-7" />
        </svg>
    </a>
    <div class="flex flex-col text-left ml-3 leading-none">
        <span class="text-base font-extrabold tracking-tight dark:text-white uppercase leading-none">Materi Teori</span>
        <div class="flex items-center space-x-1.5 mt-1.5">
            <span class="w-1.5 h-1.5 bg-indigo-500 rounded-full animate-pulse"></span>
            <span class="text-[7px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest leading-none">Sedang Dipelajari</span>
        </div>
    </div>
@endsection

@section('content')
<div class="px-4 sm:px-10 py-8">
    <div class="max-w-6xl mx-auto">
        
        {{-- //* (Player) Area Konten Utama (PDF/Video) --}}
        <div class="mb-8">
            @php
                // Mengambil URL dari aktivitas pertama (asumsi materi teori simpan URL di activity)
                $contentUrl = $material->activities->first()->step_image ?? null;
            @endphp

            @if($contentUrl)
                <div class="video-container border-4 border-white shadow-2xl">
                    <iframe src="{{ $contentUrl }}" allow="autoplay"></iframe>
                </div>
            @else
                <div class="bg-slate-200 rounded-[2rem] h-96 flex items-center justify-center border-4 border-dashed border-slate-300">
                    <p class="text-slate-500 font-bold uppercase tracking-widest text-xs">Konten tidak tersedia</p>
                </div>
            @endif
        </div>

        {{-- //* (Info) Detail Materi --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2">
                <div class="description-card p-8 lg:p-10 shadow-sm">
                    <div class="flex items-center space-x-3 mb-4">
                        <span class="px-3 py-1 bg-indigo-100 text-indigo-600 rounded-lg text-[10px] font-black uppercase tracking-widest">
                            {{ $material->material_type }}
                        </span>
                        <span class="text-slate-300">/</span>
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">
                            ID Modul: #{{ str_pad($material->id, 4, '0', STR_PAD_LEFT) }}
                        </span>
                    </div>
                    
                    <h1 class="text-2xl lg:text-3xl font-black text-slate-900 dark:text-white mb-4 capitalize">
                        {{ strtolower($material->title) }}
                    </h1>
                    
                    <div class="prose prose-slate max-w-none">
                        <p class="text-slate-600 dark:text-slate-400 leading-relaxed font-medium">
                            {{ $material->description }}
                        </p>
                    </div>

                    <hr class="my-8 border-slate-100">

                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-4">
                            <div class="w-10 h-10 bg-indigo-50 rounded-full flex items-center justify-center">
                                <svg class="w-5 h-5 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-width="2.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest leading-none mb-1">Instruksi</p>
                                <p class="text-xs font-bold text-slate-700">Pelajari materi di atas dengan seksama.</p>
                            </div>
                        </div>

                        <a href="{{ route('materi.category.list', $material->category_id) }}" 
                           class="btn-finish-pegas px-8 py-3 rounded-2xl font-black text-[10px] uppercase tracking-widest border-indigo-800 shadow-lg shadow-indigo-100">
                            Selesai Belajar
                        </a>
                    </div>
                </div>
            </div>

            {{-- //* (Sidebar) Info Tambahan --}}
            <div class="space-y-6">
                <div class="bg-white border-2 border-slate-100 p-6 rounded-[2rem] shadow-sm">
                    <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-4">Tips Belajar</h4>
                    <ul class="space-y-3">
                        <li class="flex items-start space-x-3 text-xs font-bold text-slate-600">
                            <span class="text-indigo-500 mt-0.5">●</span>
                            <span>Gunakan mode fullscreen pada player untuk tampilan lebih jelas.</span>
                        </li>
                        <li class="flex items-start space-x-3 text-xs font-bold text-slate-600">
                            <span class="text-indigo-500 mt-0.5">●</span>
                            <span>Catat poin-poin penting sebelum lanjut ke praktikum.</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection