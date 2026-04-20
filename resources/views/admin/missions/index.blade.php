{{-- 
    VIEW: Manajemen Topik Misi
    DATA: $categories (Statistik per topik), $missions (Statistik global)
    DESC: Mengelola kategori tantangan laboratorium (Kurikulum Virtual Lab).
--}}

<x-app-layout>
    <style>
        {{-- (Style) Tema Admin Terminal: Emerald (Misi) & Amber (XP) --}}
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
            border-color: #10b981;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.04);
        }

        .table-header {
            background: #fcfdfe;
            border-bottom: 2px solid #f1f5f9;
        }

        .text-header { letter-spacing: -0.02em; }
        
        .btn-emerald {
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

        {{-- (Section) Header: Judul & Akses Pembuatan Topik --}}
        <div class="flex flex-col md:flex-row md:items-center justify-between mb-12 gap-6">
            <div>
                <div class="flex items-center space-x-2 mb-2">
                    <span class="px-2 py-0.5 bg-emerald-50 text-emerald-700 rounded text-[10px] font-bold tracking-widest uppercase">Misi Terminal</span>
                    <div class="w-1.5 h-1.5 bg-emerald-500 rounded-full shadow-[0_0_8px_rgba(16,185,129,0.6)]"></div>
                </div>
                <h1 class="text-3xl font-extrabold text-slate-900 text-header tracking-tight">Manajemen <span class="text-emerald-600">Topik Misi</span></h1>
                <p class="text-slate-500 font-medium text-sm mt-1">Kelola tantangan laboratorium berdasarkan kategori topik excel.</p>
            </div>
            
            <a href="{{ route('admin.missions.create') }}" class="btn-emerald inline-flex items-center px-8 py-4">
                <svg class="w-5 h-5 mr-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path d="M12 4v16m8-8H4"></path></svg>
                Tambah topik baru
            </a>
        </div>

        {{-- (Section) Statistik: Summary Distribusi Misi & Reward --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
            <div class="admin-card p-6 flex items-center justify-between">
                <div>
                    <p class="text-[10px] font-bold text-slate-400 tracking-widest uppercase mb-1">Total Topik</p>
                    <h3 class="text-3xl font-black text-slate-900">{{ $categories->count() }}</h3>
                </div>
                <div class="w-12 h-12 bg-emerald-50 text-emerald-600 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path d="M4 6h16M4 10h16M4 14h16M4 18h16"></path></svg>
                </div>
            </div>

            <div class="admin-card p-6 flex items-center justify-between">
                <div>
                    <p class="text-[10px] font-bold text-slate-400 tracking-widest uppercase mb-1">Total Misi</p>
                    <h3 class="text-3xl font-black text-slate-900">{{ $missions->count() }}</h3>
                </div>
                <div class="w-12 h-12 bg-emerald-50 text-emerald-600 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                </div>
            </div>
            
            <div class="admin-card p-6 flex items-center justify-between">
                <div>
                    <p class="text-[10px] font-bold text-slate-400 tracking-widest uppercase mb-1">Total Reward XP</p>
                    <h3 class="text-3xl font-black text-slate-900">{{ number_format($missions->sum('max_score')) }}</h3>
                </div>
                <div class="w-12 h-12 bg-amber-50 text-amber-600 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
            </div>
        </div>

        {{-- (Section) Tabel Topik: Menampilkan Komposisi Misi (Syntax vs Visual) --}}
        <div class="admin-card overflow-hidden shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead class="table-header">
                        <tr>
                            <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">Nama Topik Misi</th>
                            <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">Komposisi Tipe</th>
                            <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">Jumlah Misi</th>
                            <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">Manajemen</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($categories as $cat)
                        <tr class="hover:bg-slate-50 transition-colors group">
                            <td class="px-8 py-6">
                                <div class="flex flex-col">
                                    <span class="text-base font-bold text-slate-800 tracking-tight capitalize leading-tight">{{ $cat->category }}</span>
                                    <span class="text-[10px] text-slate-400 font-bold mt-1 tracking-widest uppercase">Kurikulum Virtual Lab</span>
                                </div>
                            </td>
                            <td class="px-8 py-6 text-center">
                                <div class="flex items-center justify-center space-x-2">
                                    <span class="px-3 py-1 bg-slate-100 text-slate-600 rounded-lg text-[9px] font-bold uppercase">{{ $cat->syntax_count }} Syntax</span>
                                    <span class="px-3 py-1 bg-slate-100 text-slate-600 rounded-lg text-[9px] font-bold uppercase">{{ $cat->visual_count }} Visual</span>
                                </div>
                            </td>
                            <td class="px-8 py-6 text-center">
                                <div class="inline-flex items-center px-4 py-1.5 bg-emerald-50 text-emerald-700 border border-emerald-100 rounded-xl">
                                    <span class="font-black text-sm mr-1.5">{{ $cat->mission_count }}</span>
                                    <span class="text-[8px] font-bold uppercase tracking-tighter">Misi</span>
                                </div>
                            </td>
                            <td class="px-8 py-6 text-right">
                                <div class="flex items-center justify-end space-x-3">
                                    <a href="{{ route('admin.missions.topic', $cat->category) }}" class="inline-flex px-5 py-2.5 bg-emerald-600 text-white text-[10px] font-bold rounded-xl hover:bg-emerald-700 transition-all shadow-md shadow-emerald-50 uppercase tracking-widest">
                                        Kelola
                                    </a>

                                    {{-- Trigger Modal Edit via JavaScript --}}
                                    <button onclick="openEditModal('{{ addslashes($cat->category) }}', '{{ addslashes($cat->description) }}')" class="p-2 text-slate-300 hover:text-emerald-600 transition-colors">
                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    </button>
                                    
                                    <form action="{{ route('admin.missions.destroy-topic', $cat->category) }}" method="POST" class="inline">
                                        @csrf @method('DELETE')
                                        <button type="submit" onclick="return confirm('Hapus seluruh topik ini?')" class="p-2 text-slate-300 hover:text-red-600 transition-colors">
                                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-8 py-24 text-center">
                                <p class="text-[10px] text-slate-300 font-black uppercase tracking-[0.3em]">Belum ada topik misi terdaftar</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
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

    {{-- (Section) Modal: Edit Data Topik --}}
    <div id="editTopicModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm transition-opacity" onclick="closeEditModal()"></div>
            <div class="bg-white rounded-[2rem] shadow-2xl z-50 w-full max-w-md overflow-hidden transform transition-all border border-slate-100 p-10">
                <div class="flex items-center justify-between mb-8">
                    <h3 class="text-xl font-black text-slate-900 tracking-tight">Ubah Data Topik</h3>
                    <button onclick="closeEditModal()" class="text-slate-400 hover:text-slate-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
                <form id="editTopicForm" method="POST" class="space-y-6">
                    @csrf @method('PATCH')
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2 ml-1">Nama Topik Baru</label>
                        <input type="text" id="newCategoryInput" name="new_category" 
                            class="w-full rounded-xl border-slate-200 p-4 font-bold text-slate-700 focus:ring-emerald-500 focus:border-emerald-500 bg-slate-50 transition-all" required>
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2 ml-1">Deskripsi Topik</label>
                        <textarea id="descriptionInput" name="description" rows="4"
                            class="w-full rounded-xl border-slate-200 p-4 font-bold text-slate-700 focus:ring-emerald-500 focus:border-emerald-500 bg-slate-50 transition-all"></textarea>
                    </div>
                    <div class="flex space-x-3 pt-4">
                        <button type="button" onclick="closeEditModal()" class="flex-1 py-4 bg-slate-100 text-slate-500 font-bold rounded-xl uppercase tracking-widest text-[10px]">Batal</button>
                        <button type="submit" class="flex-1 py-4 bg-emerald-600 text-white font-bold rounded-xl shadow-lg uppercase tracking-widest text-[10px]">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openEditModal(category, description) {
            const form = document.getElementById('editTopicForm');
            const catInput = document.getElementById('newCategoryInput');
            const descInput = document.getElementById('descriptionInput');
            
            form.action = `/admin/missions/topic/${encodeURIComponent(category)}/update`;
            catInput.value = category;
            descInput.value = description || ""; 
            
            document.getElementById('editTopicModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeEditModal() {
            document.getElementById('editTopicModal').classList.add('hidden');
            document.body.style.overflow = 'auto';
        }
    </script>
</x-app-layout>