{{-- 
    VIEW: Form Tambah Modul Materi/Praktik
    DESC: Langkah pertama pembuatan modul sebelum masuk ke penyusunan Storyboard.
--}}

<x-app-layout>
    <style>
        {{-- (Style) Standarisasi UI Terminal: Latar Belakang & Kartu --}}
        .bg-admin {
            background-color: #f8fafc;
            background-image: radial-gradient(#e2e8f0 0.8px, transparent 0.8px);
            background-size: 32px 32px;
        }

        .admin-card {
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 1.5rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02);
        }

        .form-input-premium {
            width: 100%;
            border-radius: 1rem;
            border: 1px solid #e2e8f0;
            background-color: #f8fafc;
            padding: 1rem;
            font-size: 0.875rem;
            font-weight: 600;
            color: #334155;
            transition: all 0.2s ease;
        }
        .form-input-premium:focus {
            outline: none;
            border-color: #6366f1;
            background-color: #ffffff;
            box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.05);
        }

        .btn-indigo {
            width: 100%;
            padding: 1rem;
            background-color: #4f46e5;
            color: white;
            font-weight: 800;
            border-radius: 1rem;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            font-size: 0.75rem;
            box-shadow: 0 10px 15px -3px rgba(79, 70, 229, 0.2);
            transition: all 0.2s ease;
        }
        .btn-indigo:hover {
            background-color: #4338ca;
            transform: translateY(-1px);
            box-shadow: 0 12px 20px -3px rgba(79, 70, 229, 0.3);
        }

        .text-header { letter-spacing: -0.02em; }
    </style>

    <div class="min-h-screen bg-admin p-6 sm:p-10">
        <div class="max-w-3xl mx-auto">
            
            {{-- (Section) Header: Navigasi Kembali & Identitas Halaman --}}
            <div class="flex flex-col md:flex-row md:items-end justify-between mb-12 gap-6">
                <div>
                    <div class="flex items-center space-x-2 mb-3">
                        <span class="px-2 py-0.5 bg-indigo-50 text-indigo-700 rounded text-[10px] font-bold tracking-widest uppercase">Konfigurasi Modul</span>
                        <div class="w-1.5 h-1.5 bg-emerald-500 rounded-full shadow-[0_0_8px_rgba(16,185,129,0.6)]"></div>
                    </div>
                    <h1 class="text-3xl font-extrabold text-slate-900 text-header tracking-tight">Buat <span class="text-indigo-600">Modul Baru</span></h1>
                    <p class="text-slate-500 font-medium text-sm mt-1">Langkah awal untuk menyusun media pembelajaran baru.</p>
                </div>

                <a href="{{ route('admin.materials.index') }}" class="inline-flex items-center text-slate-400 hover:text-indigo-600 font-bold text-[10px] tracking-widest uppercase transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    Batal
                </a>
            </div>

            {{-- (Section) Form Card: Input Data Dasar Modul --}}
            <div class="admin-card p-8 sm:p-10">
                <form action="{{ route('admin.materials.store') }}" method="POST" class="space-y-8">
                    @csrf
                    
                    <div class="space-y-6">
                        {{-- (Field) Nama Modul: Identitas utama materi --}}
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2 ml-1">Nama atau Judul Modul</label>
                            <input type="text" name="title" 
                                class="form-input-premium" 
                                placeholder="Misal: Logika IF Dasar pada Excel" 
                                required>
                        </div>

                        {{-- (Field) Kategori: Menentukan jenis alur pembelajaran (Teori vs Simulasi) --}}
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2 ml-1">Kategori Pembelajaran</label>
                            <select name="category" class="form-input-premium appearance-none">
                                <option value="materi">Materi Instruksional (Teori & Langkah)</option>
                                <option value="praktik">Simulasi Praktik (Latihan Mandiri)</option>
                            </select>
                        </div>

                        {{-- (Field) Deskripsi: Penjelasan tujuan kompetensi materi --}}
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2 ml-1">Deskripsi Singkat</label>
                            <textarea name="description" rows="4" 
                                class="form-input-premium" 
                                placeholder="Jelaskan tujuan akhir dari modul ini..." 
                                required></textarea>
                        </div>

                        {{-- (Action) Button Simpan: Navigasi otomatis ke Storyboard Builder --}}
                        <div class="pt-6 border-t border-slate-50">
                            <button type="submit" class="btn-indigo">
                                Simpan & Atur Storyboard
                            </button>
                            
                            <div class="flex items-center justify-center space-x-2 mt-6 opacity-40">
                                <svg class="w-3 h-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="3" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                                <p class="text-[9px] font-bold text-slate-500 uppercase tracking-[0.2em]">Penyimpanan Aman ke Repositori Terminal</p>
                            </div>
                        </div>
                    </div>
                </form>
            </div> {{-- End: Form Card --}}

        </div>
    </div>
</x-app-layout>