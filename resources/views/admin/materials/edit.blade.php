{{-- 
    VIEW: Edit Identitas Modul
    DATA: $material
--}}

<x-app-layout>
    <style>
        {{-- (Style) Standarisasi UI Terminal --}}
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
            padding: 1.25rem;
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
            padding: 1.25rem;
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
        .btn-indigo:active { transform: scale(0.98); }

        .text-header { letter-spacing: -0.02em; }
    </style>

    <div class="min-h-screen bg-admin p-6 sm:p-10">
        <div class="max-w-3xl mx-auto">
            
            {{-- (Section) Header & Navigasi --}}
            <div class="flex flex-col md:flex-row md:items-end justify-between mb-12 gap-6">
                <div>
                    <div class="flex items-center space-x-2 mb-3">
                        <span class="px-2 py-0.5 bg-indigo-50 text-indigo-700 rounded text-[10px] font-bold tracking-widest uppercase">Editor Modul</span>
                        <div class="w-1.5 h-1.5 bg-emerald-500 rounded-full shadow-[0_0_8px_rgba(16,185,129,0.6)]"></div>
                    </div>
                    <h1 class="text-3xl font-extrabold text-slate-900 text-header tracking-tight">Edit <span class="text-indigo-600">Identitas Modul</span></h1>
                    <p class="text-slate-500 font-medium text-sm mt-1">Perbarui judul, kategori, atau deskripsi materi Excel ini.</p>
                </div>

                <a href="{{ route('admin.materials.index') }}" class="group inline-flex items-center text-slate-400 hover:text-indigo-600 font-bold text-[10px] tracking-widest uppercase transition-colors mb-1">
                    <svg class="w-4 h-4 mr-2 transform group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    Kembali ke list
                </a>
            </div>

            {{-- (Action) Form Update Modul --}}
            <div class="admin-card p-8 sm:p-12">
                <form action="{{ route('admin.materials.update', $material->id) }}" method="POST" class="space-y-8">
                    @csrf 
                    @method('PATCH')
                    
                    <div class="space-y-6">
                        {{-- (Field) Judul Modul --}}
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2 ml-1">Judul modul</label>
                            <input type="text" name="title" value="{{ $material->title }}"
                                class="form-input-premium"
                                placeholder="Misal: Logika IF Dasar pada Excel"
                                required>
                        </div>

                        {{-- (Field) Kategori --}}
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2 ml-1">Kategori modul</label>
                            <select name="category"
                                class="form-input-premium appearance-none">
                                <option value="materi" {{ $material->category == 'materi' ? 'selected' : '' }}>Materi instruksional (Belajar)</option>
                                <option value="praktik" {{ $material->category == 'praktik' ? 'selected' : '' }}>Simulasi praktik (Latihan)</option>
                            </select>
                        </div>

                        {{-- (Field) Deskripsi --}}
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2 ml-1">Deskripsi singkat</label>
                            <textarea name="description" rows="4"
                                class="form-input-premium"
                                placeholder="Jelaskan poin utama yang akan dipelajari..."
                                required>{{ $material->description }}</textarea>
                        </div>

                        {{-- (Action) Tombol Simpan --}}
                        <div class="pt-6 border-t border-slate-50">
                            <button type="submit" class="btn-indigo">
                                Perbarui data modul
                            </button>
                            
                            <div class="flex items-center justify-center space-x-2 mt-6 opacity-40">
                                <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <p class="text-[9px] font-bold text-slate-500 uppercase tracking-widest">Sinkronisasi data ke repositori terminal</p>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            {{-- Footer Terminal --}}
            <footer class="mt-20 py-8 border-t border-slate-200 flex flex-col md:flex-row items-center justify-between opacity-50 px-2">
                <div class="text-left leading-tight">
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">© 2026 UNNES Informatics Education</p>
                    <p class="text-[9px] font-medium text-slate-400 uppercase mt-1">Penelitian Pengembangan Virtual Lab Excel</p>
                </div>
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-tighter">Production v1.0</span>
            </footer>
        </div>
    </div>
</x-app-layout>