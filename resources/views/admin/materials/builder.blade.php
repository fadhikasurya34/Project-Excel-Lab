{{-- //* (View) Editor Visual Target (Hotspot) - Cloudinary Version */ --}}

<x-app-layout>
    {{-- //* (Asset) SortableJS untuk manajemen urutan drag & drop */ --}}
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>

    <style>
        /* //* (Style) UI Theme & Marker Config */
        .bg-admin {
            background-color: #f8fafc;
            background-image: radial-gradient(#e2e8f0 0.8px, transparent 0.8px);
            background-size: 32px 32px;
        }

        .admin-card {
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 1.25rem;
            transition: all 0.3s ease;
        }

        .canvas-wrapper {
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 1.5rem;
            padding: 1rem;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.02);
        }

        .hotspot-marker {
            position: absolute;
            width: 32px;
            height: 32px;
            background: #4f46e5;
            border: 3px solid white;
            border-radius: 9999px;
            box-shadow: 0 8px 15px rgba(79, 70, 229, 0.3);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 11px;
            font-weight: 800;
            transform: translate(-50%, -50%);
            z-index: 20;
            pointer-events: none;
        }

        #preview-marker {
            position: absolute;
            width: 32px;
            height: 32px;
            background: #818cf8;
            border: 3px solid white;
            border-radius: 9999px;
            display: none; 
            align-items: center;
            justify-content: center;
            transform: translate(-50%, -50%);
            z-index: 30;
            pointer-events: none;
        }

        .custom-crosshair {
            cursor: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 32 32"><line x1="16" y1="8" x2="16" y2="24" stroke="%234f46e5" stroke-width="2"/><line x1="8" y1="16" x2="24" y2="16" stroke="%234f46e5" stroke-width="2"/></svg>') 16 16, crosshair;
        }

        .text-header { letter-spacing: -0.02em; }
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
    </style>

    {{-- //* (Notification) Toast System --}}
    @if(session('success') || session('error') || session('status'))
        <div x-data="{ show: true, progress: 100 }"
            x-show="show"
            x-init="let interval = setInterval(() => { progress -= 1; if(progress <= 0) { show = false; clearInterval(interval); } }, 30);"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="translate-y-10 opacity-0 scale-90"
            x-transition:enter-end="translate-y-0 opacity-100 scale-100"
            class="fixed bottom-10 right-10 z-[200]">
            
            <div class="bg-slate-900 border {{ session('error') ? 'border-red-500/30' : 'border-indigo-500/30' }} p-5 rounded-2xl shadow-[0_20px_50px_rgba(0,0,0,0.3)] flex items-center space-x-4 min-w-[320px] overflow-hidden relative">
                <div class="absolute -top-10 -right-10 w-24 h-24 {{ session('error') ? 'bg-red-600/20' : 'bg-indigo-600/20' }} blur-3xl"></div>
                
                <div class="flex-shrink-0 w-10 h-10 {{ session('error') ? 'bg-red-600' : 'bg-indigo-600' }} rounded-xl flex items-center justify-center shadow-lg">
                    <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                        @if(session('error'))
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        @else
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                        @endif
                    </svg>
                </div>

                <div class="flex-1">
                    <p class="text-[10px] font-black {{ session('error') ? 'text-red-400' : 'text-indigo-400' }} uppercase tracking-[0.2em] leading-none mb-1">System Update</p>
                    <p class="text-sm font-bold text-white tracking-tight leading-tight">
                        {{ session('success') ?? session('status') ?? session('error') }}
                    </p>
                </div>

                <div class="absolute bottom-0 left-0 h-1 {{ session('error') ? 'bg-red-600' : 'bg-indigo-600' }} transition-all ease-linear" :style="'width: ' + progress + '%'"></div>
            </div>
        </div>
    @endif

    <div class="min-h-screen bg-admin p-6 sm:p-10">
        
        {{-- //* (Header) --}}
        <div class="flex flex-col md:flex-row md:items-center justify-between mb-12 gap-6">
            <div>
                <div class="flex items-center space-x-2 mb-2">
                    <a href="{{ route('admin.materials.steps', $material->id) }}" class="px-2 py-0.5 bg-indigo-50 text-indigo-700 rounded text-[10px] font-bold tracking-widest uppercase hover:bg-indigo-100 transition-all flex items-center">
                        <svg class="w-3 h-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                        Storyboard
                    </a>
                    <div class="w-1.5 h-1.5 bg-emerald-500 rounded-full shadow-[0_0_8px_rgba(16,185,129,0.6)]"></div>
                </div>
                <h1 class="text-3xl font-extrabold text-slate-900 text-header">Editor <span class="text-indigo-600">Visual Target</span></h1>
                <p class="text-slate-500 font-medium text-sm mt-1">Plotting urutan interaksi pengerjaan laboratorium.</p>
            </div>
            
            <div class="flex items-center bg-white p-2 rounded-2xl shadow-sm border border-slate-200 pr-6">
                <div class="w-10 h-10 rounded-xl bg-slate-900 flex items-center justify-center text-white font-bold shadow-md">
                    {{ $step->step_order }}
                </div>
                <div class="ml-3">
                    <p class="text-[9px] text-slate-400 font-black uppercase leading-none">Langkah Aktif</p>
                    <p class="text-xs font-bold text-slate-700 mt-1 capitalize">{{ $material->title }}</p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">
            
            {{-- //* Area Interaksi Plotting --}}
            <div class="lg:col-span-8 space-y-4">
                <div class="canvas-wrapper">
                    <div class="relative w-full overflow-hidden rounded-xl bg-slate-50 border border-slate-100 custom-crosshair shadow-inner" onclick="setPoint(event)">
                        {{-- //* UPDATE: Direct Cloudinary URL --}}
                        <img id="canvas" src="{{ $step->step_image }}" 
                             class="w-full h-auto block pointer-events-none select-none shadow-sm">
                        
                        <div id="hotspot-container">
                            @if($step->hotspots)
                                @foreach($step->hotspots->sortBy('order') as $hs)
                                    <div class="hotspot-marker" style="left: {{ $hs->x_percent }}%; top: {{ $hs->y_percent }}%;">
                                        {{ $loop->iteration }}
                                    </div>
                                @endforeach
                            @endif
                        </div>

                        <div id="preview-marker">
                            <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="4"><path d="M12 4v16m8-8H4"></path></svg>
                        </div>
                    </div>
                </div>
                <div class="flex items-center justify-center space-x-2 opacity-60">
                    <div class="px-3 py-1 bg-slate-200 rounded-full text-[9px] font-black uppercase tracking-tighter">Tip</div>
                    <span class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">Klik area pada gambar untuk menentukan koordinat interaksi</span>
                </div>
            </div>

            {{-- //* Sidebar Konfigurasi --}}
            <div class="lg:col-span-4 space-y-6">
                <div class="admin-card p-8 border-t-4 border-t-indigo-600">
                    <h2 class="text-lg font-bold text-slate-800 tracking-tight mb-6">Konfigurasi Titik</h2>
                    
                    <form action="{{ route('admin.materials.store-hotspot') }}" method="POST" id="hotspot-form" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="step_id" value="{{ $step->id }}">
                        <input type="hidden" id="x_input" name="x_percent">
                        <input type="hidden" id="y_input" name="y_percent">

                        <div class="space-y-6">
                            <div>
                                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2 ml-1">Instruksi Interaksi</label>
                                <textarea name="content" required 
                                    class="w-full rounded-2xl border-slate-200 text-sm focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 bg-slate-50 p-4 min-h-[100px] transition-all" 
                                    placeholder="Misal: 'Klik pada kolom Total Harga...'"></textarea>
                            </div>
                            
                            <div>
                                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2 ml-1">Video Guide (Opsional)</label>
                                <div class="relative overflow-hidden bg-slate-50 border border-dashed border-slate-300 rounded-xl p-4 hover:border-indigo-400 transition-colors">
                                    <input type="file" name="video" class="absolute inset-0 opacity-0 cursor-pointer">
                                    <div class="text-center">
                                        <p class="text-[10px] font-bold text-indigo-600 uppercase">Upload MP4 ke Cloud</p>
                                    </div>
                                </div>
                            </div>

                            <button type="submit" id="save-btn" disabled 
                                class="w-full py-4 bg-slate-900 text-white rounded-xl font-bold uppercase text-[10px] tracking-[0.2em] shadow-lg disabled:bg-slate-100 disabled:text-slate-400 hover:bg-slate-800 transition-all active:scale-95">
                                Simpan Titik Baru
                            </button>
                        </div>
                    </form>
                </div>

                {{-- //* Daftar Urutan & Status Media --}}
                <div class="admin-card p-8">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-lg font-bold text-slate-800 tracking-tight">Daftar Urutan</h2>
                        <span class="px-2 py-1 bg-slate-100 text-slate-500 rounded text-[9px] font-bold">{{ count($step->hotspots ?? []) }} Titik</span>
                    </div>
                    
                    <div id="sortable-list" class="space-y-3 max-h-[400px] overflow-y-auto pr-2 custom-scrollbar">
                        @forelse($step->hotspots->sortBy('order') as $hs)
                        <div class="p-4 bg-white border border-slate-100 rounded-2xl flex justify-between items-center group cursor-move hover:border-indigo-300 transition-all" data-id="{{ $hs->id }}">
                            <div class="flex items-center min-w-0">
                                <div class="w-7 h-7 bg-slate-100 text-slate-500 text-[10px] font-black rounded-lg flex items-center justify-center mr-4 group-hover:bg-indigo-600 group-hover:text-white transition-colors">
                                    {{ $loop->iteration }}
                                </div>
                                <div class="min-w-0">
                                    <p class="text-xs font-bold text-slate-700 truncate capitalize">{{ $hs->content }}</p>
                                    @if($hs->video_path)
                                        <div class="flex items-center mt-1">
                                            <span class="text-[8px] bg-emerald-50 text-emerald-600 px-1.5 py-0.5 rounded font-black uppercase tracking-tighter flex items-center border border-emerald-100">
                                                <svg class="w-2.5 h-2.5 mr-1" fill="currentColor" viewBox="0 0 20 20"><path d="M2 6a2 2 0 012-2h12a2 2 0 012 2v8a2 2 0 01-2 2H4a2 2 0 01-2-2V6zm6 2l4 2-4 2V8z"></path></svg>
                                                Cloud Video
                                            </span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            
                            <form action="{{ route('admin.materials.destroy-hotspot', $hs->id) }}" method="POST" class="opacity-0 group-hover:opacity-100 transition-opacity">
                                @csrf @method('DELETE')
                                <button type="submit" onclick="return confirm('Hapus titik ini?')" class="p-2 text-slate-300 hover:text-red-500">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </form>
                        </div>
                        @empty
                        <div class="py-12 text-center text-slate-400">
                            <p class="text-[10px] font-black uppercase tracking-[0.2em]">Belum Ada Data</p>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        {{-- Footer --}}
        <footer class="mt-20 py-8 border-t border-slate-200 flex flex-col md:flex-row items-center justify-between opacity-50 px-2">
            <div class="text-left leading-tight">
                <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">© 2026 UNNES Informatics Education</p>
                <p class="text-[8px] font-medium text-slate-400 uppercase mt-1">Virtual Lab Excel Visual Builder</p>
            </div>
            <span class="text-[9px] font-bold text-slate-400 uppercase tracking-tighter">Cloud Sync Active</span>
        </footer>
    </div>

    <script>
        // //* (Logic) SortableJS
        const el = document.getElementById('sortable-list');
        if (el) {
            Sortable.create(el, {
                animation: 300,
                ghostClass: 'bg-indigo-50',
                onEnd: function() {
                    let order = [];
                    document.querySelectorAll('#sortable-list [data-id]').forEach((item) => {
                        order.push(item.getAttribute('data-id'));
                    });
                    
                    document.body.style.cursor = 'wait';
                    
                    fetch("{{ route('admin.materials.reorder-hotspots') }}", {
                        method: "POST",
                        headers: { "Content-Type": "application/json", "X-CSRF-TOKEN": "{{ csrf_token() }}" },
                        body: JSON.stringify({ order: order })
                    }).then(() => window.location.reload());
                }
            });
        }

        // //* (Logic) Point Setter
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
            previewMarker.style.display = "flex"; 

            btn.disabled = false;
            btn.innerHTML = `<span>Simpan Titik #${ {{ count($step->hotspots ?? []) }} + 1 }</span>`;
        }
    </script>
</x-app-layout>