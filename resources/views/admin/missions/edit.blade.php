{{-- 
    VIEW: Editor Konten Misi
    DATA: $mission, $step (Optional)
    DESC: Mengatur aset visual, instruksi naratif, dan kunci jawaban rumus Excel.
--}}

<x-app-layout>
    <style>
        {{-- (Style) Standarisasi UI Terminal Emerald --}}
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
        }
        .btn-emerald:active { transform: scale(0.98); }

        .text-header { letter-spacing: -0.02em; }
    </style>

    <div class="min-h-screen bg-admin p-6 sm:p-10" x-data="missionEditor()">
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

        <div class="max-w-6xl mx-auto">
            {{-- Header: Navigasi & Identitas Misi --}}
            <div class="flex flex-col md:flex-row md:items-end justify-between mb-12 gap-6">
                <div>
                    <a href="{{ route('admin.missions.topic', $mission->level->category) }}" class="group inline-flex items-center text-emerald-600 font-bold text-[10px] tracking-widest uppercase hover:text-emerald-800 transition-colors mb-4">
                        <svg class="w-4 h-4 mr-2 transform group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3">
                            <path d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Kembali ke daftar misi
                    </a>
                    <h1 class="text-3xl font-black text-slate-900 text-header tracking-tight">Editor <span class="text-emerald-600">Konten Misi</span></h1>
                    <p class="text-slate-500 font-medium text-sm mt-1">Konfigurasi aset visual dan logika sintaks untuk tantangan praktikan.</p>
                </div>
            </div>

            {{-- Form Utama --}}
            <form action="{{ route('admin.missions.update-content', $mission->id) }}" method="POST" enctype="multipart/form-data" class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                @csrf
                @method('PATCH')

                {{-- Kolom Kiri: Manajemen Media --}}
                <div class="lg:col-span-7 space-y-6">
                    <div class="admin-card p-8 h-full flex flex-col">
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-6 ml-2 text-center">Screenshot Tabel Excel (Skenario Utama)</label>
                        
                        <div class="relative flex-1 rounded-3xl overflow-hidden bg-slate-50 border-2 border-dashed border-slate-200 min-h-[420px] flex items-center justify-center group transition-all hover:border-emerald-300 shadow-inner">
                        <img id="img-preview" 
                            src="{{ str_contains($mission->mission_image, 'http') ? $mission->mission_image : asset('storage/' . $mission->mission_image) }}" 
                            class="max-w-[92%] max-h-[380px] object-contain transition-all duration-500 rounded-lg opacity-100 shadow-2xl">
                            <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none bg-slate-900/5 backdrop-blur-[2px]">
                                <span class="bg-white px-6 py-2.5 rounded-full text-[10px] font-black text-emerald-600 shadow-xl tracking-[0.2em] uppercase">Ganti Media</span>
                            </div>
                        </div>

                        <div class="mt-8">
                            <input type="file" name="mission_image" id="file-input" class="hidden" onchange="previewFile(event)">
                            <label for="file-input" class="w-full py-4 bg-slate-100 text-slate-600 rounded-xl font-bold text-[10px] uppercase tracking-widest flex items-center justify-center cursor-pointer hover:bg-emerald-600 hover:text-white transition-all shadow-sm">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                                Unggah Media Skenario Baru
                            </label>
                        </div>
                    </div>
                </div>

                {{-- Kolom Kanan: Narasi & Kunci Jawaban --}}
                <div class="lg:col-span-5 space-y-6">
                    <div class="admin-card p-8 space-y-8 border-t-4 border-t-emerald-600">
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-3 ml-1">Instruksi Misi</label>
                            <textarea name="question" rows="5" 
                                class="form-input-premium" 
                                placeholder="Jelaskan apa yang harus dilakukan praktikan...">{{ in_array($mission->question, ['Instruksi pengerjaan belum diatur.', 'Instruksi belum diatur.']) ? '' : $mission->question }}</textarea>
                        </div>

                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-3 ml-1">Kunci Jawaban Rumus</label>
                            <div class="relative group">
                                <div class="absolute left-5 top-1/2 -translate-y-1/2 font-mono text-emerald-500 font-black text-lg select-none">=</div>
                                <input type="text" name="key_answer" 
                                    value="{{ in_array($mission->key_answer, ['Kunci belum diatur.', 'Kunci jawaban belum diatur.']) ? '' : str_replace('=', '', $mission->key_answer) }}" 
                                    class="form-input-premium !pl-12 font-mono text-emerald-600" 
                                    placeholder="Contoh: IF(B2>=75; 'Lulus'; 'Gagal')">
                            </div>
                        </div>

                        <div x-show="type === 'Syntax Assembly'">
                            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-3 ml-1">Blok Pengalih (Distractors)</label>
                            <input type="text" name="distractors" value="{{ $mission->distractors }}" 
                                   class="form-input-premium text-xs" 
                                   placeholder="Pisahkan dengan koma (Contoh: SUM, AVERAGE, <, >)">
                        </div>

                        <div class="pt-4">
                            <button type="submit" class="btn-emerald">
                                Update & Publikasikan Misi
                            </button>
                            
                            <div class="mt-8 flex flex-col items-center border-t border-slate-50 pt-6">
                                <span class="text-[9px] font-bold text-slate-300 uppercase tracking-[0.3em]">Status Publikasi</span>
                                <p class="text-[10px] font-bold mt-2 uppercase tracking-widest {{ in_array($mission->question, ['Instruksi pengerjaan belum diatur.', 'Instruksi belum diatur.']) ? 'text-amber-500' : 'text-emerald-500' }}">
                                    {{ in_array($mission->question, ['Instruksi pengerjaan belum diatur.', 'Instruksi belum diatur.']) ? 'Draft / Belum Siap' : 'Aktif di Laboratorium' }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        function previewFile(event) {
            const reader = new FileReader();
            const preview = document.getElementById('img-preview');
            reader.onload = () => {
                preview.src = reader.result;
                preview.classList.remove('opacity-30');
                preview.classList.add('opacity-100', 'shadow-2xl');
            };
            if(event.target.files[0]) {
                reader.readAsDataURL(event.target.files[0]);
            }
        }

        function missionEditor() {
            return {
                type: "{{ $mission->mission_type }}",
            }
        }
    </script>
</x-app-layout>