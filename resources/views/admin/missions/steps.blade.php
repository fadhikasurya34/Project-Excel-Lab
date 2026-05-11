{{-- 
    VIEW: Storyboard Prosedur Misi (Cloudinary Ready)
    DATA: $mission, $mission->steps
    LOGIC: Upload langkah misi (Image + Instruction) & Sorting.
--}}

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
            border-color: #10b981;
            background-color: #ffffff;
            box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.05);
        }

        .btn-emerald {
            width: 100%;
            padding: 1rem;
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
        .btn-emerald:hover { background-color: #059669; transform: translateY(-1px); }
        .btn-emerald:disabled { background-color: #e2e8f0; color: #94a3b8; cursor: not-allowed; box-shadow: none; }

        .btn-action {
            width: 2.75rem; height: 2.75rem;
            display: flex; align-items: center; justify-content: center;
            border-radius: 0.875rem; transition: all 0.2s;
        }

        .sortable-ghost { opacity: 0.3; background: #ecfdf5 !important; border: 2px dashed #10b981 !important; }
        .text-header { letter-spacing: -0.02em; }

        /* Modal Overlay Style */
        .modal-overlay {
            position: fixed; inset: 0; z-index: 300;
            background: rgba(15, 23, 42, 0.8); backdrop-filter: blur(8px);
            display: flex; align-items: center; justify-content: center; padding: 1.5rem;
        }
    </style>

    <div class="min-h-screen bg-admin p-6 sm:p-10" x-data="missionStepManager()">
        
        {{-- (Notification) Toast System --}}
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

        {{-- (Section) Header --}}
        <div class="mb-12 flex flex-col md:flex-row md:items-center md:justify-between gap-6">
            <div>
                <div class="flex items-center space-x-2 mb-3">
                    <span class="px-2 py-0.5 bg-emerald-50 text-emerald-700 rounded text-[10px] font-bold tracking-widest uppercase">Editor Prosedur</span>
                    <div class="w-1.5 h-1.5 bg-emerald-500 rounded-full shadow-[0_0_8px_rgba(16,185,129,0.6)]"></div>
                </div>
                <h1 class="text-3xl font-extrabold text-slate-900 text-header tracking-tight">Storyboard Misi: <span class="text-emerald-600">{{ $mission->title }}</span></h1>
                <p class="text-slate-500 font-medium text-sm mt-1">Susun urutan prosedur klik yang harus diikuti oleh praktikan.</p>
            </div>
            
            <a href="{{ route('admin.missions.topic', $mission->level->category) }}" class="group inline-flex items-center text-slate-400 hover:text-emerald-600 font-bold text-[10px] tracking-widest uppercase transition-colors">
                <svg class="w-4 h-4 mr-2 transform group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Kembali ke daftar
            </a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
            {{-- (Section) Kolom Kiri: Form Tambah Langkah --}}
            <div class="lg:col-span-1">
                <div class="admin-card p-8 sticky top-10 border-t-4 border-t-emerald-600">
                    <h3 class="text-lg font-bold text-slate-800 mb-6 tracking-tight">Tambah Langkah</h3>
                    
                    <form action="{{ route('admin.missions.store-step', $mission->id) }}" 
                          method="POST" enctype="multipart/form-data" class="space-y-6"
                          @submit="isUploading = true">
                        @csrf
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-3 ml-1">Screenshot Excel</label>
                            <input type="file" name="image" class="w-full text-[10px] text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:font-bold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100 cursor-pointer transition-all" required>
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-3 ml-1">Instruksi Prosedur</label>
                            <textarea name="instruction" rows="3" class="form-input-premium" placeholder="Misal: Klik pada sel B2..." required></textarea>
                        </div>

                        <button type="submit" :disabled="isUploading" class="btn-emerald shadow-emerald-100 flex items-center justify-center">
                            <span x-show="!isUploading">Unggah Langkah Prosedur</span>
                            <span x-show="isUploading" class="flex items-center italic">
                                <svg class="animate-spin h-4 w-4 mr-2" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                Memproses ke Cloud...
                            </span>
                        </button>
                    </form>
                </div>
            </div>

            {{-- (Section) Kolom Kanan: Daftar Langkah (Sortable) --}}
            <div class="lg:col-span-2 space-y-4" id="sortable-mission-steps">
                @forelse($mission->steps->sortBy('step_order') as $index => $step)
                <div data-id="{{ $step->id }}" class="admin-card p-6 flex flex-col md:flex-row gap-6 group hover:border-emerald-300 transition-all cursor-move relative overflow-hidden">
                    
                    <div class="w-full md:w-48 h-32 bg-slate-100 rounded-xl overflow-hidden flex-shrink-0 border border-slate-100 shadow-inner">
                        <img src="{{ str_replace('/upload/', '/upload/f_auto,q_auto,w_500/', $step->step_image) }}" 
                            class="w-full h-auto object-contain max-h-[50vh]" 
                            alt="Langkah {{ $index + 1 }}">
                    </div>

                    <div class="flex-1 flex flex-col justify-between">
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex-1">
                                <span class="step-label inline-block px-3 py-1 bg-emerald-50 text-emerald-600 text-[10px] font-bold rounded-lg tracking-widest uppercase border border-emerald-100">
                                    Langkah {{ $index + 1 }}
                                </span>
                                <p class="text-slate-700 font-bold text-sm mt-3 leading-relaxed">{{ $step->instruction }}</p>
                            </div>
                            
                            <div class="flex space-x-2 ml-4">
                                {{-- Tombol Edit Baru --}}
                                <button type="button" @click="openEditModal({{ json_encode($step) }})" class="btn-action bg-indigo-50 text-indigo-600 hover:bg-indigo-600 hover:text-white" title="Edit Langkah">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                </button>

                                <a href="{{ route('admin.missions.builder', $step->id) }}" class="btn-action bg-emerald-50 text-emerald-600 hover:bg-emerald-600 hover:text-white" title="Plot Koordinat">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5"></path></svg>
                                </a>
                                <form action="{{ route('admin.missions.destroy-step', $step->id) }}" method="POST">
                                    @csrf @method('DELETE')
                                    <button type="submit" onclick="return confirm('Hapus langkah prosedur ini? Seluruh data cloud akan ikut terhapus.')" class="btn-action bg-red-50 text-red-500 hover:bg-red-500 hover:text-white">
                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="absolute left-0 top-0 bottom-0 w-1 bg-slate-100 group-hover:bg-emerald-500 transition-colors"></div>
                </div>
                @empty
                <div class="bg-white border-2 border-dashed border-slate-200 rounded-[2rem] p-20 text-center">
                    <p class="text-slate-400 font-bold tracking-widest text-[10px] uppercase">Belum ada langkah prosedur yang disusun</p>
                </div>
                @endforelse
            </div>
        </div>

        {{-- (Section) Modal Edit --}}
        <div x-show="editModalOpen" x-cloak class="modal-overlay" x-transition.opacity>
            <div class="admin-card w-full max-w-lg p-8 shadow-2xl" @click.away="editModalOpen = false">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-xl font-black text-slate-800 tracking-tight">Edit Langkah</h3>
                    <button @click="editModalOpen = false" class="text-slate-400 hover:text-slate-600">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <form :action="editUrl" method="POST" enctype="multipart/form-data" class="space-y-6" @submit="isUploading = true">
                    @csrf @method('PATCH')
                    
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-3 ml-1">Ganti Screenshot (Opsional)</label>
                        <div class="mb-3" x-show="editingStep.step_image">
                            <img :src="editingStep.step_image ? editingStep.step_image.replace('/upload/', '/upload/f_auto,q_auto,w_300/') : ''" class="h-24 rounded-lg border border-slate-200">
                        </div>
                        <input type="file" name="image" class="w-full text-[10px] text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:font-bold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100 cursor-pointer transition-all">
                        <p class="text-[9px] text-slate-400 mt-2 font-medium">*Biarkan kosong jika tidak ingin mengubah gambar.</p>
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-3 ml-1">Instruksi Prosedur</label>
                        <textarea name="instruction" x-model="editingStep.instruction" rows="3" class="form-input-premium" required></textarea>
                    </div>

                    <div class="pt-4">
                        <button type="submit" :disabled="isUploading" class="btn-emerald shadow-emerald-100 flex items-center justify-center">
                            <span x-show="!isUploading">Simpan Perubahan</span>
                            <span x-show="isUploading" class="flex items-center italic">
                                <svg class="animate-spin h-4 w-4 mr-2" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle></svg>
                                Mengupdate...
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- (Logic) Script --}}
    <script>
        function missionStepManager() {
            return {
                isUploading: false,
                editModalOpen: false,
                editingStep: {},
                editUrl: '',

                openEditModal(step) {
                    this.editingStep = step;
                    this.editUrl = `/admin/missions/steps/${step.id}/update`;
                    this.editModalOpen = true;
                },

                init() {
                    const el = document.getElementById('sortable-mission-steps');
                    if (el) {
                        Sortable.create(el, {
                            animation: 250,
                            ghostClass: 'sortable-ghost',
                            handle: '.admin-card',
                            onEnd: function() {
                                let order = [];
                                document.querySelectorAll('#sortable-mission-steps [data-id]').forEach((item, index) => {
                                    order.push(item.getAttribute('data-id'));
                                    item.querySelector('.step-label').innerText = 'Langkah ' + (index + 1);
                                });

                                fetch("{{ route('admin.missions.reorder-steps') }}", {
                                    method: "POST",
                                    headers: { "Content-Type": "application/json", "X-CSRF-TOKEN": "{{ csrf_token() }}" },
                                    body: JSON.stringify({ order: order })
                                })
                                .then(res => res.json())
                                .then(data => {
                                    if(data.status !== 'success') alert('Gagal memperbarui urutan!');
                                });
                            }
                        });
                    }
                }
            }
        }
    </script>
</x-app-layout>