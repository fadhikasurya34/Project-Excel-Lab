{{-- 
    VIEW: Storyboard Modul
    DATA: $material, $material->activities
    LOGIC: Penyusunan alur materi dengan SortableJS & Proteksi Upload.
--}}

{{-- (Asset) SortableJS --}}
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>

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
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02);
            transition: all 0.3s ease;
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
        .btn-indigo:disabled { background-color: #e2e8f0; color: #94a3b8; cursor: not-allowed; box-shadow: none; }

        .sortable-ghost { opacity: 0.3; background: #f1f5f9 !important; border: 2px dashed #6366f1 !important; }

        .btn-action {
            width: 2.75rem; height: 2.75rem;
            display: flex; align-items: center; justify-content: center;
            border-radius: 0.875rem; transition: all 0.2s;
        }
    </style>

    {{-- (Process) Alpine.js untuk handle loading state --}}
    <div class="min-h-screen bg-admin p-6 sm:p-10" x-data="{ isUploading: false }">
        
        {{-- (Section) Header --}}
        <div class="mb-12 flex flex-col md:flex-row md:items-center md:justify-between gap-6">
            <div>
                <div class="flex items-center space-x-2 mb-3">
                    <span class="px-2 py-0.5 bg-indigo-50 text-indigo-700 rounded text-[10px] font-bold tracking-widest uppercase">Penyusunan Alur</span>
                    <div class="w-1.5 h-1.5 bg-emerald-500 rounded-full shadow-[0_0_8px_rgba(16,185,129,0.6)]"></div>
                </div>
                <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">Storyboard: <span class="text-indigo-600">{{ $material->title }}</span></h1>
                <p class="text-slate-500 font-medium text-sm mt-1">Tarik dan lepas kartu untuk mengatur urutan langkah pembelajaran.</p>
            </div>
            
            <a href="{{ route('admin.materials.index') }}" class="group inline-flex items-center text-slate-400 hover:text-indigo-600 font-bold text-[10px] tracking-widest uppercase transition-colors">
                <svg class="w-4 h-4 mr-2 transform group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Kembali ke list
            </a>
        </div>

        {{-- (Alert) Menampilkan error jika upload gagal (Misal: Gambar terlalu besar) --}}
        @if ($errors->any())
            <div class="mb-8 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 rounded-r-xl shadow-sm">
                <p class="text-xs font-bold uppercase tracking-widest mb-1">Gagal Mengunggah:</p>
                <ul class="text-sm list-disc list-inside">
                    @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
                </ul>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
            {{-- (Section) Kolom Kiri: Form Upload Langkah Baru --}}
            <div class="lg:col-span-1">
                <div class="admin-card p-8 sticky top-10 border-t-4 border-t-indigo-600">
                    <h3 class="text-lg font-bold text-slate-800 mb-6 tracking-tight">Tambah Langkah</h3>
                    
                    <form action="{{ route('admin.materials.store-step', $material->id) }}" 
                          method="POST" enctype="multipart/form-data" class="space-y-6"
                          @submit="isUploading = true">
                        @csrf
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-3 ml-1">Screenshot Excel</label>
                            <input type="file" name="image" class="w-full text-[10px] text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:font-bold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 cursor-pointer transition-all" required>
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-3 ml-1">Instruksi Singkat</label>
                            <textarea name="instruction" rows="4" class="form-input-premium" placeholder="Misal: Perhatikan sel B4 untuk hasil..." required></textarea>
                        </div>

                        <button type="submit" :disabled="isUploading" class="btn-indigo shadow-indigo-100 flex items-center justify-center">
                            <span x-show="!isUploading">Unggah Langkah Materi</span>
                            <span x-show="isUploading" class="flex items-center italic">
                                <svg class="animate-spin h-4 w-4 mr-2" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                Sedang Mengunggah...
                            </span>
                        </button>
                    </form>
                </div>
            </div>

            {{-- (Section) Kolom Kanan: Daftar Langkah Urut (Sortable) --}}
            <div class="lg:col-span-2 space-y-4" id="sortable-steps">
                @forelse($material->activities->sortBy('step_order') as $index => $step)
                <div data-id="{{ $step->id }}" class="admin-card p-6 flex flex-col md:flex-row gap-6 group hover:border-indigo-300 transition-all cursor-move relative overflow-hidden">
                    
                    <div class="w-full md:w-48 h-28 bg-slate-100 rounded-xl overflow-hidden flex-shrink-0 border border-slate-100">
                        <img src="{{ asset('storage/' . $step->step_image) }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500 pointer-events-none">
                    </div>

                    <div class="flex-1 flex flex-col justify-between">
                        <div class="flex items-start justify-between mb-4">
                            <div>
                                <span class="step-label inline-block px-3 py-1 bg-indigo-50 text-indigo-600 text-[10px] font-bold rounded-lg tracking-widest uppercase border border-indigo-100">
                                    Langkah {{ $index + 1 }}
                                </span>
                                <p class="text-slate-700 font-bold text-sm mt-3 leading-relaxed">{{ $step->instruction }}</p>
                            </div>
                            
                            <div class="flex space-x-2 ml-4">
                                <a href="{{ route('admin.materials.builder', $step->id) }}" class="btn-action bg-indigo-50 text-indigo-600 hover:bg-indigo-600 hover:text-white" title="Atur Hotspot">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5"></path></svg>
                                </a>
                                <form action="{{ route('admin.materials.steps.destroy', $step->id) }}" method="POST">
                                    @csrf @method('DELETE')
                                    <button type="submit" onclick="return confirm('Hapus langkah ini?')" class="btn-action bg-red-50 text-red-500 hover:bg-red-500 hover:text-white">
                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="absolute left-0 top-0 bottom-0 w-1 bg-slate-100 group-hover:bg-indigo-500 transition-colors"></div>
                </div>
                @empty
                <div class="bg-white border-2 border-dashed border-slate-200 rounded-[2rem] p-20 text-center">
                    <p class="text-slate-400 font-bold tracking-widest text-[10px] uppercase">Belum ada langkah yang disusun</p>
                </div>
                @endforelse
            </div>
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


    {{-- (Logic) Sortable JS --}}
    <script>
        const el = document.getElementById('sortable-steps');
        if (el) {
            Sortable.create(el, {
                animation: 250,
                ghostClass: 'sortable-ghost',
                handle: '.admin-card',
                onEnd: function() {
                    let order = [];
                    document.querySelectorAll('#sortable-steps [data-id]').forEach((item, index) => {
                        order.push(item.getAttribute('data-id'));
                        item.querySelector('.step-label').innerText = 'Langkah ' + (index + 1);
                    });

                    fetch("{{ route('admin.materials.reorder-steps') }}", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": "{{ csrf_token() }}"
                        },
                        body: JSON.stringify({ order: order })
                    })
                    .then(res => res.json())
                    .then(data => {
                        if(data.status !== 'success') alert('Gagal memperbarui urutan!');
                    });
                }
            });
        }
    </script>
    
</x-app-layout>