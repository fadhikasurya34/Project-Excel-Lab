{{-- 
    VIEW: Pondasi Topik Misi (Urutan Kurikulum)
    DATA: $groupedLevels (Misi yang dikelompokkan per kategori)
    LOGIC: Drag & Drop menggunakan SortableJS untuk mengatur alur pengerjaan siswa.
--}}

{{-- (Asset) SortableJS untuk fitur pengaturan urutan interaktif --}}
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>

<x-app-layout>
    <style>
        {{-- (Style) Standarisasi UI Terminal: Latar Belakang & Efek Drag --}}
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

        .text-header { letter-spacing: -0.02em; }

        .sortable-ghost {
            opacity: 0.3;
            background: #ecfdf5 !important;
            border: 2px dashed #10b981 !important;
        }

        .level-badge {
            width: 3rem;
            height: 3rem;
            background: white;
            border-radius: 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 900;
            color: #10b981;
            border: 1px solid #e2e8f0;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            transition: all 0.2s;
        }

        .btn-action-sm {
            padding: 0.5rem;
            border-radius: 0.75rem;
            transition: all 0.2s;
        }
    </style>

    <div class="min-h-screen bg-admin p-6 sm:p-10">
        {{-- (Section) Header: Navigasi & Identitas Halaman --}}
        <div class="mb-12 flex flex-col md:flex-row md:items-center md:justify-between gap-6">
            <div>
                <div class="flex items-center space-x-2 mb-2">
                    <span class="px-2 py-0.5 bg-emerald-50 text-emerald-700 rounded text-[10px] font-bold tracking-widest uppercase">Pondasi Terminal</span>
                    <div class="w-1.5 h-1.5 bg-emerald-500 rounded-full shadow-[0_0_8px_rgba(16,185,129,0.6)]"></div>
                </div>
                <h1 class="text-3xl font-extrabold text-slate-900 text-header tracking-tight">Pondasi <span class="text-emerald-600">Topik Misi</span></h1>
                <p class="text-slate-500 font-medium text-sm mt-1">Atur urutan tahapan misi dengan menggeser kartu sesuai alur kurikulum.</p>
            </div>
            
            <a href="{{ route('admin.missions.create') }}" class="px-8 py-4 bg-emerald-600 text-white font-bold rounded-2xl shadow-lg shadow-emerald-100 hover:bg-emerald-700 transition-all uppercase text-[10px] tracking-[0.15em]">
                Tambah topik baru
            </a>
        </div>

        {{-- (Section) Grid Konten: Menampilkan Kategori secara Terpisah --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            @foreach($groupedLevels as $category => $levels)
            <div class="admin-card overflow-hidden flex flex-col h-full border-t-4 border-t-emerald-500">
                {{-- Topic Header --}}
                <div class="bg-slate-50/50 px-8 py-5 border-b border-slate-100 flex justify-between items-center">
                    <h3 class="font-bold text-slate-800 uppercase tracking-wider text-[11px]">Topik: {{ $category }}</h3>
                    <span class="text-[9px] font-black text-emerald-600 bg-white px-3 py-1 rounded-lg border border-emerald-100 shadow-sm uppercase">{{ $levels->count() }} Tahapan</span>
                </div>
                
                {{-- (Process) Container Sortable: Area Drag & Drop per Kategori --}}
                <div class="p-6 space-y-3 sortable-container flex-1" data-category="{{ $category }}">
                    @foreach($levels as $lvl)
                    <div class="flex items-center justify-between p-4 bg-white rounded-2xl border border-slate-100 hover:border-emerald-300 transition-all group cursor-move shadow-sm" 
                         data-id="{{ $lvl->mission->id ?? $lvl->id }}">
                        
                        <div class="flex items-center space-x-4">
                            {{-- Visual Nomor Level --}}
                            <div class="level-badge group-hover:bg-emerald-600 group-hover:text-white group-hover:border-emerald-600">
                                {{ $lvl->level_order }}
                            </div>
                            <div>
                                <span class="font-bold text-slate-800 block text-sm">{{ $lvl->level_name }}</span>
                                <span class="text-[9px] font-bold text-slate-400 uppercase tracking-widest leading-none">Ref: #{{ $lvl->id }}</span>
                            </div>
                        </div>

                        <div class="flex items-center space-x-2">
                            {{-- (Action) Hapus Tahapan: Membersihkan Misi & Level terkait --}}
                            <form action="{{ route('admin.missions.destroy', $lvl->mission->id ?? $lvl->id) }}" method="POST" class="opacity-0 group-hover:opacity-100 transition-opacity">
                                @csrf @method('DELETE')
                                <button type="submit" onclick="return confirm('Hapus tahapan ini?')" class="btn-action-sm text-slate-300 hover:text-red-500 hover:bg-red-50">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </form>
                            
                            {{-- Drag Handle Icon --}}
                            <div class="text-slate-200 group-hover:text-emerald-400 p-1">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M4 8h16M4 16h16"></path></svg>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- (Logic) JavaScript: Menangani Sinkronisasi Urutan Kurikulum --}}
    <script>
        document.querySelectorAll('.sortable-container').forEach(container => {
            new Sortable(container, {
                animation: 300,
                ghostClass: 'sortable-ghost',
                handle: '.cursor-move',
                onEnd: function () {
                    let order = [];
                    {{-- Mapping ID misi berdasarkan urutan visual baru --}}
                    container.querySelectorAll('[data-id]').forEach(el => {
                        let id = el.getAttribute('data-id');
                        if(id) order.push(id);
                    });

                    {{-- Update UI: Mengganti angka pada Badge Urutan secara realtime --}}
                    container.querySelectorAll('.level-badge').forEach((badge, index) => {
                        badge.innerText = index + 1;
                    });

                    {{-- AJAX: Sinkronisasi ke database agar alur pengerjaan siswa terupdate --}}
                    fetch("{{ route('admin.missions.reorder-levels') }}", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": "{{ csrf_token() }}"
                        },
                        body: JSON.stringify({ order: order })
                    })
                    .then(res => res.json())
                    .then(data => {
                        if(data.status !== 'success') {
                            alert('Gagal menyimpan urutan: ' + (data.message || 'Error Server'));
                            window.location.reload();
                        }
                    })
                    .catch(err => {
                        console.error('Error:', err);
                        window.location.reload();
                    });
                }
            });
        });
    </script>
</x-app-layout>