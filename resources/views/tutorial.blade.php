{{-- //* (View) Pusat Bantuan & Tutorial (Sisi Siswa) --}}

@extends('layouts.siswa')

@section('title', 'Pusat Bantuan & Tutorial')

@section('header_left')
    <div class="flex flex-col text-left ml-3 leading-none">
        <span class="text-base font-extrabold tracking-tight dark:text-white uppercase leading-none">Pusat Bantuan</span>
        <div class="flex items-center space-x-1.5 mt-1.5">
            <span class="w-1.5 h-1.5 bg-blue-500 rounded-full animate-pulse"></span>
            <span class="text-[7px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest leading-none">Panduan Virtual Lab</span>
        </div>
    </div>
@endsection

@section('content')
<div class="px-4 sm:px-10 py-8 main-wrapper" x-data="{ activeTab: 'belajar' }">
    <div class="max-w-4xl mx-auto inner-wrapper">
        
        {{-- Header Halaman --}}
        <div class="mb-10 text-center">
            <div class="inline-flex items-center justify-center w-20 h-20 rounded-[2rem] bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 mb-6 shadow-sm border-2 border-blue-200 dark:border-blue-800">
                <svg class="w-10 h-10" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <h1 class="text-3xl lg:text-4xl font-black text-slate-900 dark:text-white mb-4 uppercase tracking-tight">
                Panduan Petualangan Excel
            </h1>
            <p class="text-slate-600 dark:text-slate-400 font-medium max-w-xl mx-auto text-sm leading-relaxed">
                Bingung harus mulai dari mana? Ikuti alur belajar di bawah ini dan pelajari cara menyelesaikan setiap misi untuk menjadi Grandmaster Excel!
            </p>
        </div>

        {{-- Navigasi Tab --}}
        <div class="flex flex-col sm:flex-row justify-center gap-4 mb-12">
            <button @click="activeTab = 'belajar'" 
                    :class="activeTab === 'belajar' ? 'bg-emerald-500 text-white shadow-[0_8px_20px_-5px_rgba(16,185,129,0.4)] border-emerald-600 translate-y-1 border-b-2' : 'bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-300 border-slate-200 dark:border-slate-700 btn-pegas hover:bg-slate-50 dark:hover:bg-slate-700'"
                    class="flex items-center justify-center gap-3 px-8 py-4 rounded-2xl font-black uppercase tracking-widest text-xs border-2 transition-all duration-200">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                Peta Alur Belajar
            </button>

            <button @click="activeTab = 'pengoperasian'" 
                    :class="activeTab === 'pengoperasian' ? 'bg-blue-500 text-white shadow-[0_8px_20px_-5px_rgba(59,130,246,0.4)] border-blue-600 translate-y-1 border-b-2' : 'bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-300 border-slate-200 dark:border-slate-700 btn-pegas hover:bg-slate-50 dark:hover:bg-slate-700'"
                    class="flex items-center justify-center gap-3 px-8 py-4 rounded-2xl font-black uppercase tracking-widest text-xs border-2 transition-all duration-200">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                Cara Pengoperasian
            </button>
        </div>

        {{-- ================================================================= --}}
        {{-- KONTEN TAB 1: ALUR BELAJAR (Gaya Timeline Petualangan)            --}}
        {{-- ================================================================= --}}
        <div x-show="activeTab === 'belajar'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" x-cloak>
            
            <div class="relative wrap overflow-hidden p-4 md:p-8">
                {{-- Garis putus-putus penghubung alur --}}
                <div class="absolute border-opacity-100 border-l-4 border-dashed border-slate-300 dark:border-slate-700 h-full" style="left: 36px;"></div>

                {{-- Step 1: Gabung Kelas --}}
                <div class="mb-12 flex items-start w-full relative">
                    <div class="z-20 w-12 h-12 flex items-center justify-center bg-indigo-500 rounded-2xl shadow-lg border-4 border-white dark:border-slate-900 shrink-0">
                        <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    </div>
                    <div class="ml-6 bg-white dark:bg-slate-800 border-2 border-slate-100 dark:border-slate-700 p-6 rounded-[2rem] shadow-sm w-full">
                        <h3 class="text-sm font-black text-indigo-500 uppercase tracking-widest mb-1">Tahap 1</h3>
                        <h4 class="text-xl font-bold text-slate-800 dark:text-white mb-3">Bergabung ke Squad Kelas</h4>
                        <p class="text-[13px] text-slate-600 dark:text-slate-400 leading-relaxed font-medium">
                            Masuk ke menu <strong>Kelas</strong> dan minta <em>Kode Akses</em> dari gurumu. Dengan bergabung di kelas, kamu akan mendapatkan notifikasi tugas terbaru dan terhubung dengan teman-teman sekelasmu.
                        </p>
                    </div>
                </div>

                {{-- Step 2: Materi Teori --}}
                <div class="mb-12 flex items-start w-full relative">
                    <div class="z-20 w-12 h-12 flex items-center justify-center bg-blue-500 rounded-2xl shadow-lg border-4 border-white dark:border-slate-900 shrink-0">
                        <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477-4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                    </div>
                    <div class="ml-6 bg-white dark:bg-slate-800 border-2 border-slate-100 dark:border-slate-700 p-6 rounded-[2rem] shadow-sm w-full">
                        <h3 class="text-sm font-black text-blue-500 uppercase tracking-widest mb-1">Tahap 2</h3>
                        <h4 class="text-xl font-bold text-slate-800 dark:text-white mb-3">Persenjatai Dirimu dengan Materi</h4>
                        <p class="text-[13px] text-slate-600 dark:text-slate-400 leading-relaxed font-medium mb-3">
                            Jangan langsung terjun ke medan perang! Buka menu <strong>Materi</strong> dan pelajari konsepnya. Ada 3 jenis amunisi yang bisa kamu akses:
                        </p>
                        <ul class="space-y-2 text-[12px] font-bold text-slate-600 dark:text-slate-300">
                            <li class="flex items-center gap-2"><span class="w-2 h-2 rounded-full bg-red-500"></span> Modul Interaktif (PDF)</li>
                            <li class="flex items-center gap-2"><span class="w-2 h-2 rounded-full bg-blue-500"></span> Video Tutorial Interaktif</li>
                            <li class="flex items-center gap-2"><span class="w-2 h-2 rounded-full bg-emerald-500"></span> Teks Panduan Langkah-demi-Langkah</li>
                        </ul>
                    </div>
                </div>

                {{-- Step 3: Misi Praktikum --}}
                <div class="mb-12 flex items-start w-full relative">
                    <div class="z-20 w-12 h-12 flex items-center justify-center bg-emerald-500 rounded-2xl shadow-lg border-4 border-white dark:border-slate-900 shrink-0">
                        <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path><path d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <div class="ml-6 bg-white dark:bg-slate-800 border-2 border-emerald-200 dark:border-emerald-800 p-6 rounded-[2rem] shadow-xl w-full">
                        <div class="inline-block px-3 py-1 bg-emerald-100 dark:bg-emerald-900/50 text-emerald-600 dark:text-emerald-400 rounded-lg text-[9px] font-black uppercase tracking-widest mb-3">
                            Inti Pembelajaran
                        </div>
                        <h3 class="text-sm font-black text-emerald-500 uppercase tracking-widest mb-1">Tahap 3</h3>
                        <h4 class="text-xl font-bold text-slate-800 dark:text-white mb-3">Mulai Misi Eksekusi (Praktikum)</h4>
                        <p class="text-[13px] text-slate-600 dark:text-slate-400 leading-relaxed font-medium mb-4">
                            Uji kemampuanmu di menu <strong>Misi</strong>. Kamu akan diberikan antarmuka simulasi Microsoft Excel yang terasa nyata. Ada dua tipe serangan yang harus kamu kuasai:
                        </p>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="bg-slate-50 dark:bg-slate-900/50 p-4 rounded-2xl border border-slate-100 dark:border-slate-800">
                                <h5 class="text-xs font-black text-slate-800 dark:text-white uppercase mb-1">⌨️ Misi Sintaks</h5>
                                <p class="text-[11px] text-slate-500">Tantangan mengetik! Kamu harus menuliskan rumus Excel yang tepat (misal: <code>=SUM(A1:A5)</code>) ke dalam kotak dialog yang muncul.</p>
                            </div>
                            <div class="bg-slate-50 dark:bg-slate-900/50 p-4 rounded-2xl border border-slate-100 dark:border-slate-800">
                                <h5 class="text-xs font-black text-slate-800 dark:text-white uppercase mb-1">🖱️ Misi Point & Click</h5>
                                <p class="text-[11px] text-slate-500">Tantangan visual! Kamu harus mencari dan mengklik icon/menu yang benar tepat pada gambar lembar kerja Excel.</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Step 4: Leaderboard --}}
                <div class="flex items-start w-full relative">
                    <div class="z-20 w-12 h-12 flex items-center justify-center bg-yellow-500 rounded-2xl shadow-lg border-4 border-white dark:border-slate-900 shrink-0">
                        <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path></svg>
                    </div>
                    <div class="ml-6 bg-white dark:bg-slate-800 border-2 border-slate-100 dark:border-slate-700 p-6 rounded-[2rem] shadow-sm w-full">
                        <h3 class="text-sm font-black text-yellow-500 uppercase tracking-widest mb-1">Tahap Akhir</h3>
                        <h4 class="text-xl font-bold text-slate-800 dark:text-white mb-3">Kuasai Papan Peringkat</h4>
                        <p class="text-[13px] text-slate-600 dark:text-slate-400 leading-relaxed font-medium">
                            Setiap aksi sukses di Misi akan memberimu <strong>Poin XP</strong>. Kumpulkan XP sebanyak-banyaknya untuk berebut posisi Puncak di menu <strong>Peringkat</strong>. Buktikan kamu layak mendapat gelar <em>Grandmaster</em>!
                        </p>
                    </div>
                </div>

            </div>
        </div>

        {{-- ================================================================= --}}
        {{-- KONTEN TAB 2: PENGOPERASIAN (Gaya Accordion FAQ)                  --}}
        {{-- ================================================================= --}}
        <div x-show="activeTab === 'pengoperasian'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" x-cloak>
            
            <div class="space-y-4" x-data="{ activeAccordion: null }">
                
                {{-- Accordion 1 --}}
                <div class="bg-white dark:bg-slate-800 border-2 border-slate-200 dark:border-slate-700 rounded-[1.5rem] overflow-hidden transition-all duration-300" :class="activeAccordion === 1 ? 'shadow-lg border-blue-300 dark:border-blue-700' : 'shadow-sm'">
                    <button @click="activeAccordion = activeAccordion === 1 ? null : 1" class="w-full px-6 py-5 flex items-center justify-between focus:outline-none">
                        <span class="font-bold text-slate-800 dark:text-white text-left">Bagaimana cara memperbesar video atau dokumen?</span>
                        <svg class="w-5 h-5 text-slate-400 transform transition-transform duration-300" :class="activeAccordion === 1 ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                    <div x-show="activeAccordion === 1" x-collapse x-cloak>
                        <div class="px-6 pb-6 pt-2 text-[13px] text-slate-600 dark:text-slate-400 leading-relaxed font-medium border-t border-slate-100 dark:border-slate-700">
                            Saat kamu membuka materi, akan ada tombol khusus <strong>"Perbesar Layar / Perbesar Video"</strong> di bawah kotak dokumen/video tersebut. Gunakan tombol itu agar tampilan tidak terpotong (terutama saat di HP/Tablet). Untuk menutupnya, klik tombol bulat silang merah di pojok kanan atas layar.
                        </div>
                    </div>
                </div>

                {{-- Accordion 2 --}}
                <div class="bg-white dark:bg-slate-800 border-2 border-slate-200 dark:border-slate-700 rounded-[1.5rem] overflow-hidden transition-all duration-300" :class="activeAccordion === 2 ? 'shadow-lg border-blue-300 dark:border-blue-700' : 'shadow-sm'">
                    <button @click="activeAccordion = activeAccordion === 2 ? null : 2" class="w-full px-6 py-5 flex items-center justify-between focus:outline-none">
                        <span class="font-bold text-slate-800 dark:text-white text-left">Apa itu sistem Nyawa & Tiket Remedial?</span>
                        <svg class="w-5 h-5 text-slate-400 transform transition-transform duration-300" :class="activeAccordion === 2 ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                    <div x-show="activeAccordion === 2" x-collapse x-cloak>
                        <div class="px-6 pb-6 pt-2 text-[13px] text-slate-600 dark:text-slate-400 leading-relaxed font-medium border-t border-slate-100 dark:border-slate-700">
                            Dalam pengerjaan Misi, kamu diberi <strong>Nyawa (Hati)</strong>. Jika kamu salah menjawab/salah klik, nyawa akan berkurang.<br><br>
                            Jika nilaimu jelek karena kehabisan nyawa, kamu bisa mengulang Misi menggunakan <strong>Tiket Remedial (Ikon Tiket Kuning)</strong>. Perhatikan jumlah sisa tiketmu di profil!
                        </div>
                    </div>
                </div>

                {{-- Accordion 3 --}}
                <div class="bg-white dark:bg-slate-800 border-2 border-slate-200 dark:border-slate-700 rounded-[1.5rem] overflow-hidden transition-all duration-300" :class="activeAccordion === 3 ? 'shadow-lg border-blue-300 dark:border-blue-700' : 'shadow-sm'">
                    <button @click="activeAccordion = activeAccordion === 3 ? null : 3" class="w-full px-6 py-5 flex items-center justify-between focus:outline-none">
                        <span class="font-bold text-slate-800 dark:text-white text-left">Bagaimana cara bertanya jika saya bingung?</span>
                        <svg class="w-5 h-5 text-slate-400 transform transition-transform duration-300" :class="activeAccordion === 3 ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                    <div x-show="activeAccordion === 3" x-collapse x-cloak>
                        <div class="px-6 pb-6 pt-2 text-[13px] text-slate-600 dark:text-slate-400 leading-relaxed font-medium border-t border-slate-100 dark:border-slate-700">
                            Kami menyediakan fitur <strong>Ruang Diskusi</strong> di setiap modul Materi Teori. Gulir (scroll) hingga ke bagian paling bawah dari halaman materi, ketikkan pertanyaanmu di sana, dan gurumu (atau teman sekelas) bisa membalas secara langsung layaknya *live chat*.
                        </div>
                    </div>
                </div>

                {{-- Accordion 4 --}}
                <div class="bg-white dark:bg-slate-800 border-2 border-slate-200 dark:border-slate-700 rounded-[1.5rem] overflow-hidden transition-all duration-300" :class="activeAccordion === 4 ? 'shadow-lg border-blue-300 dark:border-blue-700' : 'shadow-sm'">
                    <button @click="activeAccordion = activeAccordion === 4 ? null : 4" class="w-full px-6 py-5 flex items-center justify-between focus:outline-none">
                        <span class="font-bold text-slate-800 dark:text-white text-left">Cara mengganti foto Profil (Avatar) & Warna?</span>
                        <svg class="w-5 h-5 text-slate-400 transform transition-transform duration-300" :class="activeAccordion === 4 ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                    <div x-show="activeAccordion === 4" x-collapse x-cloak>
                        <div class="px-6 pb-6 pt-2 text-[13px] text-slate-600 dark:text-slate-400 leading-relaxed font-medium border-t border-slate-100 dark:border-slate-700">
                            Buka <strong>Sidebar Kiri</strong> (Klik ikon avatar/wajahmu di pojok kanan atas), lalu pilih menu <strong>Pengaturan Profil</strong> (ikon gerigi). Di sana kamu bisa mengunggah foto baru dan memilih warna aura profil sesuai kepribadianmu.
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </div>
</div>
@endsection