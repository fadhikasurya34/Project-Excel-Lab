{{-- 
    VIEW: Buat Topik Misi (Langkah 1)
    DESC: Inisialisasi kategori tantangan laboratorium sebelum menyusun detail misi.
--}}

<x-app-layout>
    <style>
        /* (Style) Latar Belakang & Kartu Emerald */
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
            border-color: #10b981;
            background-color: #ffffff;
            box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.05);
        }

        .btn-emerald {
            width: 100%;
            padding: 1.25rem;
            background-color: #10b981;
            color: white;
            font-weight: 800;
            border-radius: 1rem;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            font-size: 0.75rem;
            box-shadow: 0 10px 15px -3px rgba(16, 185, 129, 0.2);
            transition: all 0.2s ease;
        }

        .btn-emerald:hover {
            background-color: #059669;
            transform: translateY(-1px);
            box-shadow: 0 12px 20px -3px rgba(16, 185, 129, 0.3);
        }

        .btn-emerald:active { transform: scale(0.98); }

        .text-header { letter-spacing: -0.02em; }
    </style>

    <div class="min-h-screen bg-admin p-6 sm:p-10">

        {{-- (Notification) Toast System: Feedback sinkronisasi data --}}
        @if(session('success') || session('error') || session('status'))
            <div x-data="{ show: true, progress: 100 }"
                x-show="show"
                x-init="let interval = setInterval(() => { progress -= 1; if(progress <= 0) { show = false; clearInterval(interval); } }, 30);"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="translate-y-10 opacity-0 scale-90"
                x-transition:enter-end="translate-y-0 opacity-100 scale-100"
                class="fixed bottom-10 right-10 z-[200]">
                
                <div class="bg-slate-900 border {{ session('error') ? 'border-red-500/30' : 'border-emerald-500/30' }} p-5 rounded-2xl shadow-[0_20px_50px_rgba(0,0,0,0.3)] flex items-center space-x-4 min-w-[320px] overflow-hidden relative">
                    <div class="absolute -top-10 -right-10 w-24 h-24 {{ session('error') ? 'bg-red-600/20' : 'bg-emerald-600/20' }} blur-3xl"></div>
                    
                    <div class="flex-shrink-0 w-10 h-10 {{ session('error') ? 'bg-red-600' : 'bg-emerald-600' }} rounded-xl flex items-center justify-center shadow-lg">
                        <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                            @if(session('error'))
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            @else
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                            @endif
                        </svg>
                    </div>

                    <div class="flex-1">
                        <p class="text-[10px] font-black {{ session('error') ? 'text-red-400' : 'text-emerald-400' }} uppercase tracking-[0.2em] leading-none mb-1">System Update</p>
                        <p class="text-sm font-bold text-white tracking-tight leading-tight">
                            {{ session('success') ?? session('status') ?? session('error') }}
                        </p>
                    </div>

                    <div class="absolute bottom-0 left-0 h-1 {{ session('error') ? 'bg-red-600' : 'bg-emerald-600' }} transition-all ease-linear" :style="'width: ' + progress + '%'"></div>
                </div>
            </div>
        @endif

        <div class="max-w-3xl mx-auto">
            
            {{-- (Section) Header: Navigasi Batal & Identitas --}}
            <div class="flex flex-col md:flex-row md:items-end justify-between mb-12 gap-6">
                <div>
                    <div class="flex items-center space-x-2 mb-3">
                        <span class="px-2 py-0.5 bg-emerald-50 text-emerald-700 rounded text-[10px] font-bold tracking-widest uppercase">Konfigurasi Topik</span>
                        <div class="w-1.5 h-1.5 bg-emerald-500 rounded-full shadow-[0_0_8px_rgba(16,185,129,0.6)]"></div>
                    </div>
                    <h1 class="text-3xl font-extrabold text-slate-900 text-header tracking-tight">Buat <span class="text-emerald-600">Topik Misi</span></h1>
                    <p class="text-slate-500 font-medium text-sm mt-1">Langkah awal untuk menyusun kategori tantangan laboratorium.</p>
                </div>

                <a href="{{ route('admin.missions.index') }}" class="group inline-flex items-center text-slate-400 hover:text-emerald-600 font-bold text-[10px] tracking-widest uppercase transition-colors mb-1">
                    <svg class="w-4 h-4 mr-2 transform group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    Batal
                </a>
            </div>

            {{-- (Section) Error Validation: Muncul jika ada field yang terlewat --}}
            @if ($errors->any())
                <div class="mb-8 p-6 bg-red-50 border-l-4 border-red-500 rounded-r-2xl shadow-sm">
                    <div class="flex items-center mb-3">
                        <svg class="w-5 h-5 text-red-500 mr-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                        <p class="text-xs font-black text-red-800 uppercase tracking-widest">Input Tidak Valid</p>
                    </div>
                    <ul class="list-disc list-inside text-sm text-red-600 font-bold space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- (Section) Form Card: Input Data Dasar Kategori --}}
            <div class="admin-card p-8 sm:p-12">
                <form action="{{ route('admin.missions.store-step1') }}" method="POST" class="space-y-8">
                    @csrf
                    
                    <div class="space-y-6">
                        {{-- (Field) Nama Topik --}}
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2 ml-1">Nama Topik Misi</label>
                            <input type="text" name="category" 
                                class="form-input-premium" 
                                placeholder="Misal: Manipulasi Tabel Excel" 
                                value="{{ old('category') }}"
                                required>
                        </div>

                        {{-- (Field) Deskripsi --}}
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2 ml-1">Deskripsi Singkat</label>
                            <textarea name="description" rows="4"
                                class="form-input-premium" 
                                placeholder="Jelaskan gambaran umum tantangan pada topik ini..."
                                required>{{ old('description') }}</textarea>
                        </div>

                        {{-- (Action) Button Simpan --}}
                        <div class="pt-6 border-t border-slate-50">
                            <button type="submit" class="btn-emerald">
                                Buat & Buka Daftar Misi
                            </button>
                            
                            <div class="flex items-center justify-center space-x-2 mt-6 opacity-40">
                                <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <p class="text-[9px] font-bold text-slate-500 uppercase tracking-widest">Sinkronisasi data ke repositori misi terminal</p>
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