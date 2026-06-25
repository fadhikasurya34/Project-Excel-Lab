{{-- 
    VIEW: Pusat Bantuan & Panduan Admin
    DESC: Halaman panduan operasional manajemen materi dan misi untuk administrator.
--}}

<x-app-layout>
    <style>
        {{-- (Style) Standarisasi UI Terminal: Latar Belakang & Kartu Elevasi Indigo --}}
        .bg-admin {
            background-color: #f8fafc;
            background-image: radial-gradient(#e2e8f0 0.8px, transparent 0.8px);
            background-size: 32px 32px;
        }

        .admin-card {
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 1.5rem;
            transition: all 0.3s ease;
        }

        .btn-tab { 
            padding: 0.75rem 1.5rem; 
            border-radius: 1rem; 
            font-size: 0.75rem; 
            font-weight: 800; 
            text-transform: uppercase; 
            letter-spacing: 0.05em; 
            transition: all 0.2s; 
        }

        .text-header { letter-spacing: -0.02em; }
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
        
        [x-cloak] { display: none !important; }

        /* Style khusus wadah gambar tutorial agar proporsional */
        .tutorial-image-container {
            width: 100%;
            border-radius: 1rem;
            overflow: hidden;
            border: 1px solid #e2e8f0;
            background-color: #f8fafc;
            margin: 1.25rem 0;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        }
        .tutorial-image-container img {
            width: 100%;
            height: auto;
            object-fit: cover;
            display: block;
        }

        /* Siluet nomor langkah besar */
        .step-number {
            position: absolute;
            left: -1rem;
            top: -1rem;
            font-size: 4rem;
            font-weight: 900;
            color: #eff6ff;
            line-height: 1;
            z-index: 0;
            pointer-events: none;
        }
    </style>

    <div class="min-h-screen bg-admin p-6 sm:p-10" x-data="{ activeTab: 'materi' }">
        <div class="max-w-4xl mx-auto">
            
            {{-- (Section) Header: Navigasi Kembali & Judul Halaman --}}
            <div class="flex flex-col md:flex-row md:items-center justify-between mb-10 gap-6">
                <div>
                    <a href="{{ route('admin.dashboard') }}" class="group flex items-center text-slate-400 hover:text-indigo-600 transition-colors mb-2 text-[10px] font-bold tracking-widest uppercase">
                        <svg class="w-4 h-4 mr-1 transform group-hover:-translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                            <path d="M15 19l-7-7 7-7"></path>
                        </svg>
                        Kembali ke dashboard
                    </a>
                    <h1 class="text-3xl font-extrabold text-slate-900 text-header tracking-tight">Panduan <span class="text-indigo-600">Penggunaan</span></h1>
                    <p class="text-slate-500 font-medium text-sm mt-1">Pusat bantuan operasional manajemen Virtual Lab Excel.</p>
                </div>
                
                <div class="flex items-center bg-white p-2 rounded-2xl shadow-sm border border-slate-200 pr-6">
                    <div class="w-10 h-10 rounded-xl bg-indigo-50 flex items-center justify-center text-indigo-600 font-bold shadow-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-[9px] text-slate-400 font-bold uppercase leading-none tracking-widest">Bantuan</p>
                        <p class="text-xs font-bold text-slate-700 mt-1 capitalize">Documentation</p>
                    </div>
                </div>
            </div>

            {{-- (Section) Tab Navigation --}}
            <div class="flex bg-white p-2 rounded-2xl shadow-sm border border-slate-200 w-fit mb-8 overflow-x-auto">
                <button @click="activeTab = 'materi'" 
                        :class="activeTab === 'materi' ? 'bg-indigo-600 text-white shadow-md shadow-indigo-200' : 'text-slate-400 hover:text-indigo-600 hover:bg-slate-50'" 
                        class="btn-tab whitespace-nowrap">
                    Manajemen Materi
                </button>
                <button @click="activeTab = 'misi'" 
                        :class="activeTab === 'misi' ? 'bg-indigo-600 text-white shadow-md shadow-indigo-200' : 'text-slate-400 hover:text-indigo-600 hover:bg-slate-50'" 
                        class="btn-tab ml-1 whitespace-nowrap">
                    Manajemen Misi
                </button>
            </div>

            <div class="grid grid-cols-1 gap-6 items-start">
                
                {{-- TAB 1: PANDUAN MANAJEMEN MATERI --}}
                <div x-show="activeTab === 'materi'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" x-cloak class="space-y-6">
                    
                    {{-- Judul Utama Tab Materi --}}
                    <div class="admin-card p-6 border-l-4 border-l-indigo-600 shadow-sm flex items-center space-x-4">
                        <div class="p-3 bg-indigo-50 text-indigo-600 rounded-2xl shrink-0">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477-4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-slate-800 tracking-tight">Tutorial Pembuatan Materi Dasar</h3>
                            <p class="text-xs text-slate-500 font-medium mt-1">Langkah berurutan untuk membuat folder topik dan menambahkan modul materi baru.</p>
                        </div>
                    </div>

                    {{-- Wadah Tunggal Panduan --}}
                    <div class="admin-card p-8 sm:p-10 shadow-sm space-y-12">
                        
                        {{-- Langkah 1 --}}
                        <div class="relative">
                            <div class="step-number">1</div>
                            <div class="relative z-10 pl-4 border-l-2 border-indigo-100">
                                <h4 class="text-lg font-black text-slate-800">Menambahkan Topik (Folder) Baru</h4>
                                <p class="text-sm text-slate-600 mt-1 leading-relaxed">Buka menu <strong>Manajemen Materi</strong> di sebelah kiri. Kemudian, klik tombol <span class="px-2 py-0.5 bg-blue-600 text-white rounded text-xs">+ Tambah Topik Baru</span> yang berada di pojok kanan atas.</p>
                                
                                <div class="tutorial-image-container">
                                    <img src="{{ asset('images/TM1.png') }}" alt="Klik Tambah Topik Baru">
                                </div>
                            </div>
                        </div>

                        {{-- Langkah 2 --}}
                        <div class="relative">
                            <div class="step-number">2</div>
                            <div class="relative z-10 pl-4 border-l-2 border-indigo-100">
                                <h4 class="text-lg font-black text-slate-800">Mengisi Informasi Topik</h4>
                                <p class="text-sm text-slate-600 mt-1 leading-relaxed">Pada kotak dialog yang muncul, isi kolom <strong>Nama Topik Modul</strong> dan <strong>Deskripsi Singkat</strong> sesuai dengan materi yang akan diajarkan. Setelah selesai, klik tombol <span class="px-2 py-0.5 bg-blue-600 text-white rounded text-xs">Simpan Topik</span>.</p>
                                
                                <div class="tutorial-image-container">
                                    <img src="{{ asset('images/TM2.png') }}" alt="Isi Form Topik">
                                </div>
                            </div>
                        </div>

                        {{-- Langkah 3 --}}
                        <div class="relative">
                            <div class="step-number">3</div>
                            <div class="relative z-10 pl-4 border-l-2 border-indigo-100">
                                <h4 class="text-lg font-black text-slate-800">Menambahkan Modul ke Dalam Topik</h4>
                                <p class="text-sm text-slate-600 mt-1 leading-relaxed">Setelah folder topik berhasil dibuat, buka folder tersebut (klik opsi <strong>Kelola</strong>). Di dalam halaman topik, klik tombol <span class="px-2 py-0.5 bg-blue-600 text-white rounded text-xs">+ Tambah Modul Baru</span> untuk mulai memasukkan isi materi.</p>
                                
                                <div class="tutorial-image-container">
                                    <img src="{{ asset('images/TM3.png') }}" alt="Tambah Modul Baru">
                                </div>
                            </div>
                        </div>

                        {{-- Langkah 4 --}}
                        <div class="relative">
                            <div class="step-number">4</div>
                            <div class="relative z-10 pl-4 border-l-2 border-indigo-100">
                                <h4 class="text-lg font-black text-slate-800">Mengatur Tipe Modul (Teori/Praktikum)</h4>
                                <p class="text-sm text-slate-600 mt-1 leading-relaxed">Isikan Judul dan Deskripsi Modul. Yang paling penting, pilih <strong>Tipe Konten</strong> yang sesuai:</p>
                                <ul class="list-disc ml-5 mt-2 text-sm text-slate-600 space-y-1">
                                    <li>Pilih <strong>Materi Teori</strong> jika isinya berupa bahan bacaan, video YouTube, atau file PDF (Google Drive).</li>
                                    <li>Pilih <strong>Simulasi Praktikum</strong> jika Anda ingin mengunggah gambar *screenshot* Excel untuk dijadikan simulasi klik interaktif.</li>
                                </ul>
                                <p class="text-sm text-slate-600 mt-2">Terakhir, klik <span class="px-2 py-0.5 bg-blue-600 text-white rounded text-xs">Simpan Modul</span>.</p>

                                <div class="tutorial-image-container">
                                    <img src="{{ asset('images/TM4.png') }}" alt="Pilih Tipe Modul">
                                </div>
                            </div>
                        </div>

                        {{-- Langkah 5 --}}
                        <div class="relative">
                            <div class="step-number">5</div>
                            <div class="relative z-10 pl-4 border-l-2 border-indigo-100">
                                <h4 class="text-lg font-black text-slate-800">Mengisi Konten Materi Teori</h4>
                                <p class="text-sm text-slate-600 mt-1 leading-relaxed">Jika Anda mengelola modul bertipe <strong>Materi Teori</strong>, ikuti panduan pengisian form berikut:</p>
                                
                                <ul class="list-none ml-0 mt-3 text-sm text-slate-600 space-y-4">
                                    <li class="flex items-start">
                                        <span class="text-red-500 font-black text-lg mr-3 leading-none">1</span>
                                        <div class="flex-1">
                                            <strong>Link Video Tutorial (YouTube)</strong><br>
                                            <p class="mt-1 mb-2 leading-relaxed">Penting! Jika Anda menyalin link dari tombol "Bagikan" YouTube, link yang Anda dapat biasanya seperti ini: <br><code class="text-[10px] bg-slate-100 text-slate-500 px-1 py-0.5 rounded">https://youtu.be/BCvWHPbmNxc?si=...</code></p>
                                            <p class="mb-2 leading-relaxed">Jangan langsung ditempel! Ambil <strong>11 karakter kode videonya saja</strong> (contoh: <code>BCvWHPbmNxc</code>), lalu letakkan di belakang format link <strong class="text-indigo-500">embed</strong> berikut:</p>
                                            <span class="text-[11px] text-indigo-600 font-mono font-bold bg-indigo-50 px-2 py-1.5 rounded border border-indigo-100 block">https://www.youtube.com/embed/<span class="text-red-500">KODE_VIDEO</span></span>
                                        </div>
                                    </li>
                                    <li class="flex items-start">
                                        <span class="text-red-500 font-black text-lg mr-3 leading-none">2</span>
                                        <div>
                                            <strong>Bahan Bacaan Teks</strong><br>
                                            Tuliskan penjelasan materi di sini. Kolom ini mendukung fitur baris baru (Enter) agar teks rapi saat dibaca.
                                        </div>
                                    </li>
                                    <li class="flex items-start">
                                        <span class="text-red-500 font-black text-lg mr-3 leading-none">3</span>
                                        <div>
                                            <strong>Link Dokumen PDF (Google Drive)</strong><br>
                                            Agar PDF langsung terbaca, pastikan akses file diubah menjadi <em>"Siapa saja yang memiliki link"</em> dan akhiran link menggunakan format <em>preview</em>.<br>
                                            <span class="text-[10px] sm:text-xs text-indigo-500 font-mono bg-indigo-50 px-1 rounded block mt-1">Contoh: https://drive.google.com/file/d/KODE_FILE/preview</span>
                                        </div>
                                    </li>
                                    <li class="flex items-start">
                                        <span class="text-red-500 font-black text-lg mr-3 leading-none">4</span>
                                        <div>
                                            <strong>Simpan & Publikasikan</strong><br>
                                            Klik tombol ini untuk menyimpan pembaruan materi. Minimal isi salah satu dari ketiga konten di atas agar materi dapat disajikan ke siswa.
                                        </div>
                                    </li>
                                </ul>

                                <div class="tutorial-image-container mt-5">
                                    <img src="{{ asset('images/TMM1.png') }}" alt="Form Materi Teori">
                                </div>
                            </div>
                        </div>

                        {{-- Langkah 6 --}}
                        <div class="relative">
                            <div class="step-number">6</div>
                            <div class="relative z-10 pl-4 border-l-2 border-indigo-100">
                                <h4 class="text-lg font-black text-slate-800">Menambahkan Langkah Simulasi Praktikum</h4>
                                <p class="text-sm text-slate-600 mt-1 leading-relaxed">Jika Anda memilih modul <strong>Simulasi Praktikum</strong>, Anda perlu menyusun alur <em>(Storyboard)</em> berupa gambar-gambar *screenshot* Excel.</p>
                                
                                <ul class="list-none ml-0 mt-3 text-sm text-slate-600 space-y-4">
                                    <li class="flex items-start">
                                        <span class="text-red-500 font-black text-lg mr-3 leading-none">1</span>
                                        <div><strong>Unggah Screenshot:</strong> Klik <em>Choose File</em> untuk mengunggah gambar tangkapan layar antarmuka Excel untuk langkah ini.</div>
                                    </li>
                                    <li class="flex items-start">
                                        <span class="text-red-500 font-black text-lg mr-3 leading-none">2</span>
                                        <div><strong>Instruksi Singkat:</strong> Berikan petunjuk umum apa yang harus diperhatikan siswa pada gambar ini.</div>
                                    </li>
                                    <li class="flex items-start">
                                        <span class="text-red-500 font-black text-lg mr-3 leading-none">3</span>
                                        <div><strong>Unggah Langkah:</strong> Simpan gambar tersebut ke dalam sistem.</div>
                                    </li>
                                    <li class="flex items-start">
                                        <span class="text-red-500 font-black text-lg mr-3 leading-none">4</span>
                                        <div><strong>Editor Visual Target:</strong> Setelah langkah tersimpan, klik <strong>Ikon Kursor (Panah Biru)</strong> di sebelah kanan untuk mulai mengatur area titik klik <em>(Hotspot)</em> pada gambar tersebut.</div>
                                    </li>
                                </ul>

                                <div class="tutorial-image-container mt-4">
                                    <img src="{{ asset('images/TMM2.png') }}" alt="Tambah Langkah Simulasi">
                                </div>
                            </div>
                        </div>

                        {{-- Langkah 7 --}}
                        <div class="relative">
                            <div class="step-number">7</div>
                            <div class="relative z-10 pl-4 border-l-2 border-indigo-100">
                                <h4 class="text-lg font-black text-slate-800">Mengatur Titik Klik Interaktif (Hotspot)</h4>
                                <p class="text-sm text-slate-600 mt-1 leading-relaxed">Di halaman Editor Visual Target, Anda bisa menentukan area mana saja yang harus diklik oleh praktikan.</p>
                                
                                <ul class="list-none ml-0 mt-3 text-sm text-slate-600 space-y-4">
                                    <li class="flex items-start">
                                        <span class="text-red-500 font-black text-lg mr-3 leading-none">5</span>
                                        <div><strong>Plotting Titik Koordinat:</strong> Arahkan kursor Anda dan <strong>klik langsung pada gambar layar Excel</strong> (Misal: klik pada sel B2). Ikon plus <strong>(+)</strong> akan muncul menandai titik tersebut.</div>
                                    </li>
                                    <li class="flex items-start">
                                        <span class="text-red-500 font-black text-lg mr-3 leading-none">6</span>
                                        <div><strong>Instruksi Interaksi:</strong> Ketikkan instruks/penjelasan khusus untuk titik klik tersebut (Misal: <em>"Klik CELL B2"</em>). Ini yang akan dibaca oleh praktikan saat misi berjalan.</div>
                                    </li>
                                    <li class="flex items-start">
                                        <span class="text-red-500 font-black text-lg mr-3 leading-none">7</span>
                                        <div><strong>Simpan Titik:</strong> Klik tombol ini untuk menyimpan titik *hotspot*. Anda bisa menambahkan banyak titik dalam satu gambar. Jika sudah selesai, klik tombol <strong>Kembali</strong> di bagian atas.</div>
                                    </li>
                                </ul>

                                <div class="tutorial-image-container mt-4">
                                    <img src="{{ asset('images/TMM3.png') }}" alt="Editor Visual Target">
                                </div>
                            </div>
                        </div>

                        {{-- Langkah 8 --}}
                        <div class="relative">
                            <div class="step-number">8</div>
                            <div class="relative z-10 pl-4 border-l-2 border-transparent">
                                <h4 class="text-lg font-black text-slate-800">Menyusun Rangkaian Simulasi</h4>
                                <p class="text-sm text-slate-600 mt-1 leading-relaxed">Sebuah materi simulasi yang utuh biasanya terdiri dari beberapa rangkaian langkah gambar yang saling menyambung.</p>
                                
                                <ul class="list-none ml-0 mt-3 text-sm text-slate-600 space-y-4">
                                    <li class="flex items-start">
                                        <span class="text-red-500 font-black text-lg mr-3 leading-none">8</span>
                                        <div>
                                            <strong>Menambah Langkah Baru:</strong> Ulangi proses pengunggahan gambar (Langkah 6) untuk menambahkan <em>Langkah 2, Langkah 3</em>, dan seterusnya sampai simulasi selesai.<br><br>
                                            Susunan langkah ini akan muncul secara berurutan saat siswa mengerjakan simulasi di mode *Point & Click*.
                                        </div>
                                    </li>
                                </ul>

                                <div class="tutorial-image-container mt-4">
                                    <img src="{{ asset('images/TMM4.png') }}" alt="Menambah Langkah Baru">
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                {{-- TAB 2: PANDUAN MANAJEMEN MISI --}}
                <div x-show="activeTab === 'misi'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" x-cloak class="space-y-6">
                    
                    {{-- Judul Utama Tab Misi --}}
                    <div class="admin-card p-6 border-l-4 border-l-emerald-500 shadow-sm flex items-center space-x-4">
                        <div class="p-3 bg-emerald-50 text-emerald-600 rounded-2xl shrink-0">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-slate-800 tracking-tight">Tutorial Manajemen Misi</h3>
                            <p class="text-xs text-slate-500 font-medium mt-1">Panduan lengkap membuat folder tantangan, mengatur tipe misi, hingga menentukan urutan level evaluasi siswa.</p>
                        </div>
                    </div>

                    {{-- Wadah Tunggal Panduan Misi --}}
                    <div class="admin-card p-8 sm:p-10 shadow-sm space-y-12">
                        
                        {{-- Langkah 1 --}}
                        <div class="relative">
                            <div class="step-number">1</div>
                            <div class="relative z-10 pl-4 border-l-2 border-emerald-100">
                                <h4 class="text-lg font-black text-slate-800">Membuat Topik (Folder) Misi</h4>
                                <p class="text-sm text-slate-600 mt-1 leading-relaxed">Buka menu <strong>Manajemen Misi</strong>. Seperti halnya materi, langkah pertama adalah membuat folder wadah misi. Klik tombol <span class="px-2 py-0.5 bg-emerald-500 text-white rounded text-xs">+ Tambah Topik Baru</span>, lalu isi nama folder beserta keterangannya sesuai dengan kelompok evaluasi yang ingin dibuat.</p>
                                
                                <div class="tutorial-image-container">
                                    <img src="{{ asset('images/TS1.png') }}" alt="Membuat Topik Misi">
                                </div>
                            </div>
                        </div>

                        {{-- Langkah 2 --}}
                        <div class="relative">
                            <div class="step-number">2</div>
                            <div class="relative z-10 pl-4 border-l-2 border-emerald-100">
                                <h4 class="text-lg font-black text-slate-800">Mulai Menambahkan Misi</h4>
                                <p class="text-sm text-slate-600 mt-1 leading-relaxed">Setelah folder topik berhasil dibuat, klik tombol <strong>Kelola</strong> pada folder tersebut untuk masuk ke dalamnya. Kemudian, klik tombol <span class="px-2 py-0.5 bg-emerald-500 text-white rounded text-xs">+ Tambah Misi Baru</span>.</p>
                                
                                <div class="tutorial-image-container">
                                    <img src="{{ asset('images/TS2.png') }}" alt="Tambah Misi Baru">
                                </div>
                            </div>
                        </div>

                        {{-- Langkah 3 --}}
                        <div class="relative">
                            <div class="step-number">3</div>
                            <div class="relative z-10 pl-4 border-l-2 border-emerald-100">
                                <h4 class="text-lg font-black text-slate-800">Mengatur Detail Tipe Misi & XP</h4>
                                <p class="text-sm text-slate-600 mt-1 leading-relaxed">Pada jendela yang muncul, perhatikan 3 pengaturan utama ini:</p>
                                
                                <ul class="list-none ml-0 mt-3 text-sm text-slate-600 space-y-4">
                                    <li class="flex items-start">
                                        <span class="text-red-500 font-black text-lg mr-3 leading-none">3</span>
                                        <div><strong>Judul Misi:</strong> Tuliskan nama tantangan evaluasi (Contoh: Penguasaan Rumus IF).</div>
                                    </li>
                                    <li class="flex items-start">
                                        <span class="text-red-500 font-black text-lg mr-3 leading-none">4</span>
                                        <div>
                                            <strong>Tipe Misi:</strong> Pilih jenis evaluasi yang Anda inginkan.
                                            <ul class="list-disc ml-5 mt-1 space-y-1">
                                                <li>Pilih <strong class="text-emerald-600">Syntax Assembly</strong> jika soal berupa merangkai/menyusun blok rumus Excel.</li>
                                                <li>Pilih <strong class="text-emerald-600">Point & Click</strong> jika soal berupa simulasi layout/antarmuka Excel.</li>
                                            </ul>
                                        </div>
                                    </li>
                                    <li class="flex items-start">
                                        <span class="text-red-500 font-black text-lg mr-3 leading-none">5</span>
                                        <div><strong>Max Reward:</strong> Atur jumlah XP maksimal yang akan diperoleh siswa jika berhasil menyelesaikan misi ini tanpa kesalahan. Terakhir, klik <strong>Simpan</strong>.</div>
                                    </li>
                                </ul>

                                <div class="tutorial-image-container mt-4">
                                    <img src="{{ asset('images/TS3.png') }}" alt="Mengisi Form Misi">
                                </div>
                            </div>
                        </div>

                        {{-- Langkah 4 --}}
                        <div class="relative">
                            <div class="step-number">4</div>
                            <div class="relative z-10 pl-4 border-l-2 border-transparent">
                                <h4 class="text-lg font-black text-slate-800">Mengatur Urutan Level Misi</h4>
                                <p class="text-sm text-slate-600 mt-1 leading-relaxed">Misi yang berada di posisi teratas otomatis menjadi "Level 1", posisi kedua menjadi "Level 2", dan seterusnya.</p>
                                
                                <ul class="list-none ml-0 mt-3 text-sm text-slate-600 space-y-4">
                                    <li class="flex items-start">
                                        <span class="text-red-500 font-black text-lg mr-3 leading-none">6</span>
                                        <div><strong>Ubah Urutan:</strong> Untuk mengubah urutan (level), klik dan tahan ikon garis dua <strong>(=)</strong> di sebelah kiri nama misi, lalu tarik (geser) ke atas atau ke bawah sesuai urutan yang Anda inginkan.</div>
                                    </li>
                                </ul>

                                <div class="tutorial-image-container mt-4">
                                    <img src="{{ asset('images/TS4.png') }}" alt="Mengatur Urutan Misi">
                                </div>
                                
                                <div class="mt-6 p-4 bg-emerald-50 border border-emerald-100 rounded-2xl flex items-start">
                                    <svg class="w-5 h-5 text-emerald-500 mr-3 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    <p class="text-xs text-emerald-700 leading-relaxed font-bold">
                                        <strong>Selanjutnya:</strong> Setelah misi terbuat, klik tombol <span class="px-2 py-0.5 bg-emerald-600 text-white rounded text-[10px] mx-1">EDITOR</span> untuk memasukkan soal/pertanyaan ke dalam misi tersebut. Cara pengisiannya kurang lebih sama dengan pengisian konten pada Materi Teori & Praktikum.
                                    </p>
                                </div>
                            </div>
                        </div>
                        {{-- Langkah 5 --}}
                        <div class="relative">
                            <div class="step-number">5</div>
                            <div class="relative z-10 pl-4 border-l-2 border-emerald-100">
                                <h4 class="text-lg font-black text-slate-800">Mengisi Konten Misi "Syntax Assembly"</h4>
                                <p class="text-sm text-slate-600 mt-1 leading-relaxed">Jika Anda memilih misi tipe perakitan sintaks, klik tombol <strong>EDITOR</strong>. Anda akan masuk ke halaman penyusunan soal:</p>
                                
                                <ul class="list-none ml-0 mt-3 text-sm text-slate-600 space-y-4">
                                    <li class="flex items-start">
                                        <span class="text-red-500 font-black text-lg mr-3 leading-none">1</span>
                                        <div><strong>Skenario Utama:</strong> Unggah gambar tabel atau data Excel yang menjadi bahan acuan untuk soal ini.</div>
                                    </li>
                                    <li class="flex items-start">
                                        <span class="text-red-500 font-black text-lg mr-3 leading-none">2</span>
                                        <div><strong>Instruksi Misi:</strong> Tuliskan narasi soal secara detail agar siswa tahu rumus apa yang harus mereka rakit.</div>
                                    </li>
                                    <li class="flex items-start">
                                        <span class="text-red-500 font-black text-lg mr-3 leading-none">3</span>
                                        <div class="flex-1">
                                            <strong>Kunci Jawaban Rumus:</strong> Masukkan rumus Excel yang benar.
                                            <div class="mt-2 p-3 bg-slate-800 rounded-xl text-slate-300 text-xs font-medium space-y-1">
                                                <span class="text-emerald-400 font-black block mb-1">ATURAN PENGISIAN RUMUS:</span>
                                                - Wajib menyertakan tanda sama dengan (<code class="text-white">=</code>) di awal rumus.<br>
                                                - Gunakan titik koma (<code class="text-white">;</code>) sebagai pemisah argumen.<br>
                                                <span class="text-xs text-slate-400 block mt-2">Contoh: <code class="text-white">=IF(B2>=75;"LULUS";"REMEDIAL")</code></span>
                                            </div>
                                        </div>
                                    </li>
                                    <li class="flex items-start">
                                        <span class="text-red-500 font-black text-lg mr-3 leading-none">4</span>
                                        <div><strong>Blok Pengalih (Distractor):</strong> Tambahkan potongan-potongan sintaks pengecoh yang dipisahkan dengan koma (Misal: <code><, 70, "GAGAL"</code>). Ini akan membuat soal lebih menantang. Setelah itu, klik <strong>Update & Publikasikan Misi</strong>.</div>
                                    </li>
                                </ul>

                                <div class="tutorial-image-container mt-4">
                                    <img src="{{ asset('images/TSS1.png') }}" alt="Editor Syntax Assembly">
                                </div>
                            </div>
                        </div>

                        {{-- Langkah 6 --}}
                        <div class="relative">
                            <div class="step-number">6</div>
                            <div class="relative z-10 pl-4 border-l-2 border-emerald-100">
                                <h4 class="text-lg font-black text-slate-800">Memulai Misi "Point & Click"</h4>
                                <p class="text-sm text-slate-600 mt-1 leading-relaxed">Berbeda dengan materi praktikum biasa, misi <em>Point & Click</em> digunakan untuk menguji kemampuan siswa mengeklik menu Excel secara mandiri.</p>
                                
                                <ul class="list-none ml-0 mt-3 text-sm text-slate-600 space-y-4">
                                    <li class="flex items-start">
                                        <span class="text-red-500 font-black text-lg mr-3 leading-none">1</span>
                                        <div><strong>Screenshot Excel:</strong> Unggah gambar antarmuka Excel untuk tahapan soal ini.</div>
                                    </li>
                                    <li class="flex items-start">
                                        <span class="text-red-500 font-black text-lg mr-3 leading-none">2</span>
                                        <div><strong>Instruksi Prosedur:</strong> Berikan perintah apa yang harus dilakukan siswa pada tahap ini.</div>
                                    </li>
                                    <li class="flex items-start">
                                        <span class="text-red-500 font-black text-lg mr-3 leading-none">3</span>
                                        <div><strong>Unggah:</strong> Simpan langkah prosedur tersebut.</div>
                                    </li>
                                    <li class="flex items-start">
                                        <span class="text-red-500 font-black text-lg mr-3 leading-none">4</span>
                                        <div><strong>Editor Visual:</strong> Klik ikon <strong>Kursor (Panah Hijau)</strong> pada langkah yang baru saja dibuat untuk mulai menentukan area jawaban (Target Klik).</div>
                                    </li>
                                </ul>

                                <div class="tutorial-image-container mt-4">
                                    <img src="{{ asset('images/TSS2.png') }}" alt="Storyboard Point & Click">
                                </div>
                            </div>
                        </div>

                        {{-- Langkah 7 --}}
                        <div class="relative">
                            <div class="step-number">7</div>
                            <div class="relative z-10 pl-4 border-l-2 border-emerald-100">
                                <h4 class="text-lg font-black text-slate-800">Menentukan Area Jawaban & Hint</h4>
                                <p class="text-sm text-slate-600 mt-1 leading-relaxed">Di halaman Editor Multi-Target, Anda menentukan area mana yang benar untuk diklik oleh siswa.</p>
                                
                                <ul class="list-none ml-0 mt-3 text-sm text-slate-600 space-y-4">
                                    <li class="flex items-start">
                                        <span class="text-red-500 font-black text-lg mr-3 leading-none">5</span>
                                        <div><strong>Petakan Jawaban:</strong> Klik tepat pada area gambar yang merupakan jawaban benar. Sebuah tanda (+) akan muncul di area tersebut.</div>
                                    </li>
                                    <li class="flex items-start">
                                        <span class="text-red-500 font-black text-lg mr-3 leading-none">6</span>
                                        <div>
                                            <strong>Tambahkan Hint:</strong> Jika pada modul Materi kolom ini berisi "Penjelasan", maka pada Misi kolom ini berisi <strong>Petunjuk (Hint)</strong>. <br>Tuliskan petunjuk yang membantu siswa jika mereka salah klik (Misal: <em>"Cari tombol berikon kotak di pojok kanan atas"</em>).
                                        </div>
                                    </li>
                                    <li class="flex items-start">
                                        <span class="text-red-500 font-black text-lg mr-3 leading-none">7</span>
                                        <div><strong>Simpan Target:</strong> Klik untuk menyimpan titik jawaban.</div>
                                    </li>
                                </ul>

                                <div class="tutorial-image-container mt-4">
                                    <img src="{{ asset('images/TSS3.png') }}" alt="Menentukan Titik Target">
                                </div>
                            </div>
                        </div>

                        {{-- Langkah 8 --}}
                        <div class="relative">
                            <div class="step-number">8</div>
                            <div class="relative z-10 pl-4 border-l-2 border-transparent">
                                <h4 class="text-lg font-black text-slate-800">Menyusun Rangkaian Misi Point & Click</h4>
                                <p class="text-sm text-slate-600 mt-1 leading-relaxed">Misi seringkali membutuhkan urutan penyelesaian beruntun.</p>
                                
                                <ul class="list-none ml-0 mt-3 text-sm text-slate-600 space-y-4">
                                    <li class="flex items-start">
                                        <span class="text-red-500 font-black text-lg mr-3 leading-none">8</span>
                                        <div>
                                            <strong>Langkah Berlapis:</strong> Jika soal praktik membutuhkan lebih dari satu klik, ulangi proses pada <strong>Langkah 6</strong> untuk mengunggah gambar prosedur selanjutnya (misal: <em>Langkah 2</em>). <br><br>Siswa harus menyelesaikan klik pada Langkah 1 dengan benar sebelum layar beralih ke Langkah 2.
                                        </div>
                                    </li>
                                </ul>

                                <div class="tutorial-image-container mt-4">
                                    <img src="{{ asset('images/TSS4.png') }}" alt="Rangkaian Misi">
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

            </div>   
        </div>
    </div>
</x-app-layout>