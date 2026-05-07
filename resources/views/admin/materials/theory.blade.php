{{-- 
    VIEW: Editor Materi Teori (Admin)
    DATA: $material (With activities)
    DESC: Halaman khusus untuk mengelola konten PDF atau Video via Link External.
--}}

<x-app-layout>
    <style>
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
        .admin-card:hover {
            border-color: #2563eb;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.04);
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
            transition: all 0.2s;
        }
        .form-input-premium:focus {
            outline: none;
            border-color: #2563eb;
            background-color: #ffffff;
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.05);
        }

        .btn-blue-main {
            background-color: #2563eb;
            color: white;
            font-weight: 800;
            border-radius: 0.875rem;
            box-shadow: 0 10px 15px -3px rgba(37, 99, 235, 0.2);
            transition: all 0.2s ease;
        }
        .btn-blue-main:hover {
            background-color: #1d4ed8;
            transform: translateY(-1px);
        }

        .text-header { letter-spacing: -0.02em; }
    </style>

    <div class="min-h-screen bg-admin p-4 sm:p-10">
        <div class="max-w-7xl mx-auto">
            
            {{-- (Section) Header --}}
            <div class="mb-10 flex flex-col md:flex-row md:items-start justify-between gap-6">
                <div>
                    <a href="{{ route('admin.materials.topic', $material->category_id) }}" class="group inline-flex items-center text-blue-600 font-bold text-[10px] tracking-widest uppercase hover:text-blue-800 transition-colors mb-3">
                        <svg class="w-4 h-4 mr-2 transform group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3">
                            <path d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Kembali ke daftar modul
                    </a>
                    <div class="flex items-center space-x-2 mb-2">
                        <span class="px-2 py-0.5 bg-blue-50 text-blue-700 rounded text-[10px] font-bold tracking-widest uppercase">Editor Konten</span>
                        <div class="w-1.5 h-1.5 bg-blue-500 rounded-full shadow-[0_0_8px_rgba(37,99,235,0.6)]"></div>
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 text-header tracking-tight">
                        Kelola <span class="text-blue-600">Materi Teori</span>
                    </h1>
                    <p class="text-slate-500 font-medium text-xs sm:text-sm mt-1 max-w-2xl leading-relaxed">
                        Modul: <span class="font-bold text-slate-700 capitalize">{{ strtolower($material->title) }}</span>
                    </p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                
                {{-- (Panel Kiri) Form Konfigurasi --}}
                <div class="lg:col-span-4 space-y-6">
                    <div class="admin-card p-8">
                        <form action="{{ route('admin.materials.store-step', $material->id) }}" method="POST" class="space-y-6">
                            @csrf
                            
                            <div>
                                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-3 ml-1">Link Konten Eksternal</label>
                                <input type="url" name="external_url" 
                                    class="form-input-premium" 
                                    value="{{ $material->activities->first()->step_image ?? '' }}"
                                    placeholder="Tempel link GDrive / YouTube / PDF..." 
                                    required>
                                <div class="mt-3 flex items-start space-x-2 opacity-70">
                                    <svg class="w-3.5 h-3.5 text-amber-500 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"></path></svg>
                                    <p class="text-[9px] text-slate-500 font-medium leading-relaxed">
                                        Gunakan link <strong>Preview</strong> untuk Google Drive.
                                    </p>
                                </div>
                            </div>

                            <div>
                                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-3 ml-1">Ringkasan / Instruksi</label>
                                <textarea name="instruction" rows="6" class="form-input-premium" placeholder="Tuliskan panduan singkat untuk siswa..." required>{{ $material->activities->first()->instruction ?? '' }}</textarea>
                            </div>

                            <button type="submit" class="btn-blue-main w-full py-4 text-[10px] uppercase tracking-widest active:scale-95 transition-all">
                                Simpan & Publikasikan
                            </button>
                        </form>
                    </div>

                    {{-- Tips Card --}}
                    <div class="bg-slate-900 rounded-[1.5rem] p-6 text-white shadow-xl shadow-slate-200 relative overflow-hidden">
                        <div class="absolute -bottom-10 -left-10 w-32 h-32 bg-blue-600/20 rounded-full blur-2xl"></div>
                        <h4 class="text-xs font-black uppercase tracking-widest mb-3 relative z-10 flex items-center">
                            <span class="w-2 h-2 bg-blue-500 rounded-full mr-2"></span>
                            Tips Terminal
                        </h4>
                        <p class="text-[10px] font-medium leading-relaxed opacity-70 relative z-10">
                            Untuk PDF, pastikan izin berbagi diatur ke "Siapa saja yang memiliki link" agar siswa bisa membacanya tanpa login Google.
                        </p>
                    </div>
                </div>

                {{-- (Panel Kanan) Browser Preview --}}
                <div class="lg:col-span-8">
                    <div class="admin-card overflow-hidden h-full flex flex-col min-h-[600px]">
                        {{-- Fake Browser Header --}}
                        <div class="p-4 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between px-6">
                            <div class="flex space-x-1.5">
                                <div class="w-2.5 h-2.5 rounded-full bg-slate-200"></div>
                                <div class="w-2.5 h-2.5 rounded-full bg-slate-200"></div>
                                <div class="w-2.5 h-2.5 rounded-full bg-slate-200"></div>
                            </div>
                            <span class="text-[9px] font-black text-slate-400 uppercase tracking-[0.2em]">Live Content Preview</span>
                            <div class="w-10"></div> {{-- Spacer --}}
                        </div>

                        <div class="flex-1 p-6 bg-slate-50">
                            @if($material->activities->first() && $material->activities->first()->step_image)
                                <div class="w-full h-full rounded-xl overflow-hidden bg-white shadow-inner border border-slate-200">
                                    <iframe src="{{ $material->activities->first()->step_image }}" class="w-full h-full" frameborder="0" allow="autoplay"></iframe>
                                </div>
                            @else
                                <div class="w-full h-full flex flex-col items-center justify-center text-center opacity-40">
                                    <div class="w-20 h-20 bg-slate-200 rounded-3xl flex items-center justify-center mb-4 border-2 border-dashed border-slate-300">
                                        <svg class="w-10 h-10 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                    </div>
                                    <p class="text-[10px] font-black text-slate-500 uppercase tracking-[0.2em]">Konten Belum Terhubung</p>
                                    <p class="text-[9px] font-bold text-slate-400 mt-2 uppercase tracking-tighter">Link eksternal akan muncul di sini secara real-time</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>