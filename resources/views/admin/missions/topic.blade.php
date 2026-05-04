{{-- 
    VIEW: Daftar Misi per Topik
    DATA: $missions, $category, $allTopics, $topicData
    LOGIC: Reordering misi via SortableJS & Manajemen Metadata (Modal).
--}}

{{-- (Asset) SortableJS --}}
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>

<x-app-layout>
    <style>
        {{-- (Style) UI Terminal Emerald --}}
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

        .level-badge {
            width: 3rem;
            height: 3rem;
            background: #0f172a;
            color: white;
            border-radius: 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 900;
            font-size: 1.125rem;
            box-shadow: inset 0 2px 4px rgba(255,255,255,0.1);
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

        .btn-emerald-main {
            background-color: #10b981;
            color: white;
            font-weight: 800;
            border-radius: 0.875rem;
            box-shadow: 0 10px 15px -3px rgba(16, 185, 129, 0.2);
            transition: all 0.2s ease;
        }
        .btn-emerald-main:hover {
            background-color: #059669;
            transform: translateY(-1px);
        }

        .text-header { letter-spacing: -0.02em; }
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
        
        .sortable-ghost {
            opacity: 0.3;
            background: #ecfdf5 !important;
            border: 2px dashed #10b981 !important;
        }
    </style>

    <div class="min-h-screen bg-admin p-4 sm:p-10">

        {{-- (Notification) Toast System --}}
        @if(session('success') || session('error') || session('status'))
            <div x-data="{ show: true, progress: 100 }"
                x-show="show"
                x-init="let interval = setInterval(() => { progress -= 1; if(progress <= 0) { show = false; clearInterval(interval); } }, 30);"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="translate-y-10 opacity-0 scale-90"
                x-transition:enter-end="translate-y-0 opacity-100 scale-100"
                class="fixed bottom-6 right-4 sm:bottom-10 sm:right-10 z-[200]">
                
                <div class="bg-slate-900 border {{ session('error') ? 'border-red-500/30' : 'border-emerald-500/30' }} p-5 rounded-2xl shadow-[0_20px_50px_rgba(0,0,0,0.3)] flex items-center space-x-4 min-w-[280px] sm:min-w-[320px] overflow-hidden relative">
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
                        <p class="text-sm font-bold text-white tracking-tight leading-tight">{{ session('success') ?? session('status') ?? session('error') }}</p>
                    </div>
                    <div class="absolute bottom-0 left-0 h-1 {{ session('error') ? 'bg-red-600' : 'bg-emerald-600' }} transition-all ease-linear" :style="'width: ' + progress + '%'"></div>
                </div>
            </div>
        @endif

        {{-- (Section) Header --}}
        <div class="mb-8 sm:mb-10 flex flex-col md:flex-row md:items-start justify-between gap-6">
            <div>
                <a href="{{ route('admin.missions.index') }}" class="group inline-flex items-center text-emerald-600 font-bold text-[10px] tracking-widest uppercase hover:text-emerald-800 transition-colors mb-3">
                    <svg class="w-4 h-4 mr-2 transform group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3">
                        <path d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Kembali ke topik
                </a>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 text-header tracking-tight capitalize">
                    Topik: <span class="text-emerald-600">{{ $category }}</span>
                </h1>
                <p class="text-slate-500 font-medium text-xs sm:text-sm mt-1 max-w-2xl leading-relaxed">
                    {{ $topicData->description ?? 'Konfigurasi urutan dan konten teknis untuk setiap tahapan misi pada topik ini.' }}
                </p>
            </div>
            <button onclick="openAddModal()" class="btn-emerald-main px-6 sm:px-8 py-3.5 sm:py-4 text-[10px] sm:text-[11px] tracking-[0.15em] flex items-center justify-center uppercase active:scale-95 w-full md:w-auto">
                <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path d="M12 4v16m8-8H4"></path></svg>
                Tambah misi baru
            </button>
        </div>

        {{-- (Section) Tabel Misi - FIXED NO DOUBLE INFO --}}
        <div class="admin-card overflow-hidden shadow-sm relative border-none md:border md:border-slate-200">
            {{-- Loading Overlay --}}
            <div id="loading-overlay" class="absolute inset-0 bg-white/60 backdrop-blur-[2px] z-50 hidden items-center justify-center">
                <div class="flex items-center space-x-3 bg-slate-900 text-white px-6 py-3 rounded-2xl shadow-2xl">
                    <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                    <span class="text-[10px] font-bold uppercase tracking-widest">Menyimpan urutan...</span>
                </div>
            </div>

            <table class="w-full text-left border-collapse block md:table">
                <thead class="bg-slate-50/80 border-b border-slate-100 hidden md:table-header-group">
                    <tr>
                        <th class="w-20 px-8 py-5 text-center text-[10px] font-black text-slate-400 uppercase tracking-widest">Geser</th>
                        <th class="px-4 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">Identitas Misi</th>
                        <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">Tipe</th>
                        <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">Urutan</th>
                        <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">Manajemen</th>
                    </tr>
                </thead>
                <tbody id="sortable-list" class="divide-y divide-slate-100 block md:table-row-group">
                    @forelse($missions as $m)
                    <tr data-id="{{ $m->id }}" class="hover:bg-emerald-50/30 transition-all group block md:table-row p-5 md:p-0 mb-4 md:mb-0 bg-white md:bg-transparent rounded-2xl md:rounded-none border border-slate-100 md:border-none shadow-sm md:shadow-none">
                        
                        {{-- Handle Geser --}}
                        <td class="block md:table-cell py-2 md:py-6 md:px-8 text-slate-300 group-hover:text-emerald-500 cursor-move">
                            <div class="flex items-center justify-between md:justify-center">
                                <span class="md:hidden text-[10px] font-bold text-slate-400 uppercase tracking-widest">Sortir Alur</span>
                                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path d="M4 8h16M4 16h16"></path></svg>
                            </div>
                        </td>

                        {{-- Identitas --}}
                        <td class="block md:table-cell py-3 md:py-6 md:px-4">
                            <div class="flex flex-col">
                                <span class="text-base font-bold text-slate-800 tracking-tight leading-tight capitalize">{{ $m->title }}</span>
                                <span class="text-[10px] text-slate-400 font-bold mt-1 uppercase tracking-wider">{{ $m->max_score }} XP Reward</span>
                            </div>
                        </td>

                        {{-- Tipe (Bersih: Tanpa Badge Angka) --}}
                        <td class="block md:table-cell py-3 md:py-6 md:px-8 md:text-center">
                            <div class="flex md:block items-center justify-between">
                                <span class="md:hidden text-[10px] font-bold text-slate-400 uppercase tracking-widest">Tipe</span>
                                <span class="inline-flex px-3 py-1 bg-slate-100 text-slate-600 rounded-lg text-[9px] font-black uppercase tracking-widest whitespace-nowrap">{{ $m->mission_type }}</span>
                            </div>
                        </td>

                        {{-- Urutan (Hanya Satu Sumber Info) --}}
                        <td class="block md:table-cell py-3 md:py-6 md:px-8 md:text-center">
                            <div class="flex md:contents items-center justify-between">
                                <span class="md:hidden text-[10px] font-bold text-slate-400 uppercase tracking-widest">Urutan</span>
                                <div class="inline-flex level-badge group-hover:bg-emerald-600 group-hover:scale-105 transition-all">
                                    {{ $m->level->level_order }}
                                </div>
                            </div>
                        </td>

                        {{-- Manajemen --}}
                        <td class="block md:table-cell py-4 md:py-6 md:px-8 text-right border-t border-slate-50 md:border-none mt-4 md:mt-0">
                            <div class="flex items-center justify-between md:justify-end md:space-x-4">
                                <span class="md:hidden text-[10px] font-bold text-slate-400 uppercase tracking-widest">Opsi Kelola</span>
                                <div class="flex items-center space-x-2 sm:space-x-4">
                                    <a href="{{ route($m->mission_type === 'Point & Click' ? 'admin.missions.steps' : 'admin.missions.edit', $m->id) }}" 
                                       class="inline-flex items-center px-4 py-2 sm:px-5 sm:py-2.5 bg-emerald-600 text-white text-[9px] sm:text-[10px] font-black rounded-xl hover:bg-emerald-700 transition-all shadow-md shadow-emerald-50 uppercase tracking-widest">
                                        Editor
                                    </a>
                                    <button onclick='openEditModal(@json($m))' class="w-9 h-9 flex items-center justify-center text-slate-400 hover:text-emerald-600 hover:bg-emerald-50 rounded-xl transition-all">
                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    </button>
                                    <form action="{{ route('admin.missions.destroy', $m->id) }}" method="POST" class="m-0" onsubmit="return confirm('Hapus misi?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="w-9 h-9 flex items-center justify-center text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-xl transition-all">
                                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr class="block md:table-row">
                        <td colspan="5" class="px-8 py-24 text-center block md:table-cell">
                            <p class="text-[10px] text-slate-300 font-black uppercase tracking-[0.3em]">Belum ada tahapan misi pada topik ini</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- (Section) Modal: Form --}}
    <div id="missionModal" class="fixed inset-0 z-[100] hidden overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm transition-opacity" onclick="closeModal()"></div>
            <div class="bg-white rounded-[2rem] shadow-2xl z-50 w-full max-w-lg overflow-hidden transform transition-all border border-slate-100 p-6 sm:p-10">
                <h3 id="modalTitle" class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight mb-8">Tambah Misi Baru</h3>
                <form id="missionForm" method="POST" class="space-y-6">
                    @csrf
                    <input type="hidden" name="category" value="{{ $category }}">
                    <div id="methodField"></div>
                    <div class="space-y-5">
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2 ml-1">Judul Misi</label>
                            <input type="text" name="title" id="form_title" class="form-input-premium" placeholder="Misal: Rumus Logika AND" required>
                        </div>
                        <div id="moveTopicContainer" class="hidden">
                            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2 ml-1">Pindahkan Topik</label>
                            <select name="target_category" id="form_target_category" class="form-input-premium appearance-none">
                                @foreach($allTopics as $topicName)
                                    <option value="{{ $topicName }}" {{ $topicName == $category ? 'selected' : '' }}>{{ $topicName }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="grid grid-cols-2 gap-4 sm:gap-6">
                            <div>
                                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2 ml-1">Tipe Misi</label>
                                <select name="mission_type" id="form_type" class="form-input-premium appearance-none">
                                    <option value="Syntax Assembly">Syntax Assembly</option>
                                    <option value="Point & Click">Point & Click</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2 ml-1">Max Reward</label>
                                <div class="relative">
                                    <input type="number" name="max_score" id="form_xp" value="100" class="form-input-premium !pr-10">
                                    <span class="absolute right-4 top-1/2 -translate-y-1/2 font-black text-slate-300 text-[9px]">XP</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="flex space-x-4 pt-4">
                        <button type="button" onclick="closeModal()" class="flex-1 py-4 bg-slate-100 text-slate-500 font-bold rounded-xl uppercase tracking-widest text-[10px]">Batal</button>
                        <button type="submit" class="flex-1 py-4 bg-emerald-600 text-white font-bold rounded-xl shadow-lg uppercase tracking-widest text-[10px]">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        const sortableList = document.getElementById('sortable-list');
        const loader = document.getElementById('loading-overlay');
        
        if(sortableList) {
            Sortable.create(sortableList, {
                animation: 250, 
                ghostClass: 'sortable-ghost',
                handle: '.cursor-move',
                onEnd: function() {
                    const order = [];
                    loader.classList.replace('hidden', 'flex');

                    document.querySelectorAll('#sortable-list tr').forEach((tr, index) => {
                        const id = tr.getAttribute('data-id');
                        if(id) {
                            order.push(id);
                            // Update badge urutan (Hanya satu badge per baris sekarang)
                            const badge = tr.querySelector('.level-badge');
                            if(badge) badge.innerText = index + 1;
                        }
                    });

                    fetch("{{ route('admin.missions.reorder-levels') }}", {
                        method: "POST", 
                        headers: { "Content-Type": "application/json", "X-CSRF-TOKEN": "{{ csrf_token() }}" },
                        body: JSON.stringify({ order: order })
                    }).finally(() => {
                        loader.classList.replace('flex', 'hidden');
                    });
                }
            });
        }

        function openAddModal() {
            document.getElementById('modalTitle').innerText = "Tambah Misi Baru";
            document.getElementById('missionForm').action = "{{ route('admin.missions.store-quick') }}";
            document.getElementById('methodField').innerHTML = "";
            document.getElementById('form_title').value = "";
            document.getElementById('moveTopicContainer').classList.add('hidden');
            document.getElementById('missionModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function openEditModal(mission) {
            document.getElementById('modalTitle').innerText = "Ubah Metadata Misi";
            document.getElementById('missionForm').action = `/admin/missions/${mission.id}`;
            document.getElementById('methodField').innerHTML = '@method("PATCH")';
            document.getElementById('form_title').value = mission.title;
            document.getElementById('form_type').value = mission.mission_type;
            document.getElementById('form_xp').value = mission.max_score;
            document.getElementById('moveTopicContainer').classList.remove('hidden');
            document.getElementById('missionModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeModal() { 
            document.getElementById('missionModal').classList.add('hidden'); 
            document.body.style.overflow = 'auto';
        }
    </script>
</x-app-layout>