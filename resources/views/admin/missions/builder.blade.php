{{-- 
    VIEW: Editor Multi-Target (Misi Admin)
    DATA: $mission, $step (Langkah Misi)
    DESC: Plotting koordinat jawaban yang wajib diklik siswa dalam simulasi lab.
--}}

{{-- (Asset) SortableJS untuk manajemen urutan target --}}
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>

<x-app-layout>
    <style>
        {{-- (Style) Standarisasi UI Terminal & Marker Emerald --}}
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

        .canvas-wrapper {
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 1.5rem;
            padding: 0.75rem;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.05);
        }

        .hotspot-marker {
            position: absolute;
            width: 30px;
            height: 30px;
            background: #10b981;
            border: 3px solid white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 10px;
            font-weight: 800;
            transform: translate(-50%, -50%);
            box-shadow: 0 4px 10px rgba(16, 185, 129, 0.3);
            z-index: 10;
        }

        #preview-marker {
            position: absolute;
            width: 30px;
            height: 30px;
            background: #34d399;
            border: 3px solid white;
            border-radius: 50%;
            transform: translate(-50%, -50%);
            z-index: 20;
            box-shadow: 0 0 15px rgba(52, 211, 153, 0.5);
            display: none;
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
            border-color: #10b981;
            background-color: #ffffff;
            box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.05);
        }

        .custom-crosshair {
            cursor: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 32 32"><line x1="16" y1="8" x2="16" y2="24" stroke="%2310b981" stroke-width="2"/><line x1="8" y1="16" x2="24" y2="16" stroke="%2310b981" stroke-width="2"/></svg>') 16 16, crosshair;
        }

        .text-header { letter-spacing: -0.02em; }
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
        .sortable-ghost { opacity: 0.3; background: #ecfdf5 !important; border: 2px dashed #10b981 !important; }
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

        {{-- (Section) Header: Navigasi & Status Langkah --}}
        <div class="flex flex-col md:flex-row md:items-center justify-between mb-10 gap-6">
            <div>
                <a href="{{ route('admin.missions.steps', $mission->id) }}" class="group flex items-center text-slate-400 hover:text-emerald-600 transition-colors mb-2 text-[10px] font-bold tracking-widest uppercase">
                    <svg class="w-4 h-4 mr-1 transform group-hover:-translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path d="M15 19l-7-7 7-7"></path></svg>
                    Kembali ke storyboard
                </a>
                <h1 class="text-3xl font-extrabold text-slate-900 text-header tracking-tight">Editor <span class="text-emerald-600">Multi-Target</span></h1>
                <p class="text-slate-500 font-medium text-sm mt-1">Plotting urutan interaksi yang wajib diselesaikan siswa.</p>
            </div>
            
            <div class="flex items-center bg-white p-2 rounded-2xl shadow-sm border border-slate-200 pr-6">
                <div class="w-10 h-10 rounded-xl bg-slate-900 flex items-center justify-center text-white font-bold shadow-md">
                    {{ $step->step_order }}
                </div>
                <div class="ml-3">
                    <p class="text-[9px] text-slate-400 font-bold uppercase leading-none tracking-widest">Langkah Misi</p>
                    <p class="text-xs font-bold text-slate-700 mt-1 capitalize">{{ $mission->title }}</p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            {{-- (Section) Interactive Canvas --}}
            <div class="lg:col-span-8 space-y-4">
                <div class="canvas-wrapper">
                    <div class="relative overflow-hidden rounded-xl custom-crosshair shadow-inner" onclick="setPoint(event)">
                        <img id="canvas" src="{{ asset('storage/' . $step->step_image) }}" 
                               class="w-full h-auto block pointer-events-none select-none">
                        
                        {{-- Render Target Tersimpan --}}
                        <div id="hotspot-wrapper">
                            @if($step->hotspots)
                                @foreach($step->hotspots->sortBy('order') as $hs)
                                    <div class="hotspot-marker" style="left: {{ $hs->x_percent }}%; top: {{ $hs->y_percent }}%;">
                                        {{ $loop->iteration }}
                                    </div>
                                @endforeach
                            @endif
                        </div>

                        {{-- Marker Preview --}}
                        <div id="preview-marker" class="flex items-center justify-center">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="4"><path d="M12 4v16m8-8H4"></path></svg>
                        </div>
                    </div>
                </div>
                <div class="flex items-center space-x-3 text-slate-400 px-2">
                    <div class="p-1.5 bg-emerald-50 text-emerald-600 rounded-lg">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <span class="text-xs font-medium">Klik pada gambar di atas untuk menentukan koordinat klik jawaban siswa.</span>
                </div>
            </div>

            {{-- (Section) Panel Kontrol --}}
            <div class="lg:col-span-4 space-y-6">
                {{-- Form Tambah Titik --}}
                <div class="admin-card p-8 border-t-4 border-t-emerald-600 shadow-sm">
                    <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider mb-6">Tambah Titik Baru</h3>
                    <form action="{{ route('admin.missions.store-hotspot') }}" method="POST" id="hotspot-form">
                        @csrf
                        <input type="hidden" name="step_id" value="{{ $step->id }}">
                        <input type="hidden" id="x_input" name="x_percent">
                        <input type="hidden" id="y_input" name="y_percent">

                        <div class="space-y-6">
                            <div>
                                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2 ml-1">Instruksi Pengerjaan</label>
                                <textarea name="content" required class="form-input-premium p-4" rows="3" placeholder="Misal: Klik cell C10..."></textarea>
                            </div>
                            
                            <button type="submit" id="save-btn" disabled class="w-full py-4 bg-slate-100 text-slate-400 rounded-xl font-bold uppercase text-[10px] tracking-widest transition-all">
                                Pilih titik di gambar
                            </button>
                        </div>
                    </form>
                </div>

                {{-- Daftar Urutan --}}
                <div class="admin-card p-8 shadow-sm">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider">Urutan Target</h3>
                        <span class="px-2 py-1 bg-slate-50 text-slate-400 rounded text-[10px] font-bold">{{ count($step->hotspots ?? []) }} Titik</span>
                    </div>

                    <div id="sortable-list" class="space-y-3 max-h-[40vh] overflow-y-auto pr-2 custom-scrollbar">
                        @forelse($step->hotspots->sortBy('order') as $hs)
                        <div class="p-4 bg-white border border-slate-100 rounded-2xl flex justify-between items-center group cursor-move hover:border-emerald-300 hover:bg-emerald-50/20 transition-all shadow-sm" data-id="{{ $hs->id }}">
                            <div class="flex items-center overflow-hidden">
                                <div class="w-8 h-8 bg-slate-50 text-slate-400 text-[10px] font-black rounded-xl flex items-center justify-center mr-4 group-hover:bg-emerald-600 group-hover:text-white transition-all shadow-sm">
                                    {{ $loop->iteration }}
                                </div>
                                <p class="text-sm font-bold text-slate-700 truncate max-w-[150px] capitalize">{{ $hs->content }}</p>
                            </div>
                            
                            <form action="{{ route('admin.missions.destroy-hotspot', $hs->id) }}" method="POST" class="m-0 flex items-center">
                                @csrf @method('DELETE')
                                <button type="submit" class="w-9 h-9 flex items-center justify-center text-slate-300 hover:text-red-500 hover:bg-red-50 rounded-xl transition-all">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                        <path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </form>
                        </div>
                        @empty
                        <div class="text-center py-10">
                            <p class="text-[10px] font-bold text-slate-300 uppercase tracking-widest">Belum ada titik target</p>
                        </div>
                        @endforelse
                    </div>
                </div>
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
    

    <script>
        {{-- Sortable Logic --}}
        const el = document.getElementById('sortable-list');
        if (el) {
            Sortable.create(el, {
                animation: 250,
                ghostClass: 'sortable-ghost',
                onEnd: function() {
                    let order = [];
                    document.querySelectorAll('#sortable-list [data-id]').forEach((item) => {
                        order.push(item.getAttribute('data-id'));
                    });
                    
                    fetch("{{ route('admin.missions.reorder-hotspots') }}", {
                        method: "POST",
                        headers: { 
                            "Content-Type": "application/json", 
                            "X-CSRF-TOKEN": "{{ csrf_token() }}" 
                        },
                        body: JSON.stringify({ order: order })
                    }).then(() => window.location.reload());
                }
            });
        }

        {{-- Coordinate Plotting Logic --}}
        function setPoint(event) {
            const img = document.getElementById('canvas');
            const previewMarker = document.getElementById('preview-marker');
            const btn = document.getElementById('save-btn');
            const rect = img.getBoundingClientRect();
            
            const offsetX = event.clientX - rect.left;
            const offsetY = event.clientY - rect.top;

            const xPercent = (offsetX / rect.width) * 100;
            const yPercent = (offsetY / rect.height) * 100;

            document.getElementById('x_input').value = xPercent.toFixed(6);
            document.getElementById('y_input').value = yPercent.toFixed(6);

            previewMarker.style.left = xPercent + "%";
            previewMarker.style.top = yPercent + "%";
            previewMarker.style.display = 'flex';

            btn.disabled = false;
            btn.classList.remove('bg-slate-100', 'text-slate-400');
            btn.classList.add('bg-emerald-600', 'text-white', 'shadow-lg', 'shadow-emerald-100');
            btn.innerHTML = `Simpan titik #${ {{ count($step->hotspots ?? []) }} + 1 }`;
        }

        {{-- Form Submit State --}}
        document.getElementById('hotspot-form').addEventListener('submit', function() {
            const btn = document.getElementById('save-btn');
            if(!btn.disabled) {
                btn.disabled = true;
                btn.innerHTML = `<span class="animate-pulse">Menyimpan...</span>`;
            }
        });
    </script>
</x-app-layout>