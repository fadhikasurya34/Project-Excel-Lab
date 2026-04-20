<x-app-layout>
    <style>
        .bg-admin { background-color: #f8fafc; background-image: radial-gradient(#e2e8f0 0.8px, transparent 0.8px); background-size: 32px 32px; }
        .admin-card { background: white; border: 1px solid #e2e8f0; border-radius: 1.5rem; transition: all 0.3s ease; }
        .form-input-premium { width: 100%; border-radius: 1rem; border: 1px solid #e2e8f0; padding: 0.875rem 1rem; font-size: 0.875rem; font-weight: 600; color: #334155; }
        .btn-tab { padding: 0.625rem 1.25rem; border-radius: 0.75rem; font-size: 0.75rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.05em; transition: all 0.2s; }
        .mission-checkbox:checked + label { border-color: #a855f7; background-color: #faf5ff; box-shadow: 0 4px 12px rgba(168, 85, 247, 0.1); }
        [x-cloak] { display: none !important; }
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
    </style>

    <div class="min-h-screen bg-admin p-6 sm:p-10" 
         x-data="{ 
            activeTab: 'monitoring',
            search: '', 
            missionSearch: '',
            sortBy: 'xp', 
            sortOrder: 'desc',
            selectedMissions: [],
            showTaskModal: false,
            showEditTaskModal: false,
            editTaskData: { id: '', name: '' },

            {{-- Data Missions dari PHP ke JS --}}
            availableMissions: @js($availableMissions),
            
            {{-- Data Users Mapping --}}
            users: {{ $classroom->users->map(function($user) {
                return [
                    'id' => $user->id,
                    'name' => (string) $user->name,
                    'email' => (string) $user->email,
                    'xp' => (int) ($user->total_xp ?? 0),
                    'materials' => (int) ($user->material_activities_count ?? 0),
                    'missions' => (int) ($user->progress_count ?? 0),
                    'badge_medal' => (string) ($user->rank_status['medal'] ?? '-'),
                    'badge_title' => (string) ($user->rank_status['title'] ?? 'Newbie'),
                    'avatar' => 'https://api.dicebear.com/9.x/bottts/svg?seed='.($user->avatar ?? $user->name).'&backgroundColor=transparent',
                    'profile_color' => (string) ($user->profile_color ?? 'a855f7'),
                    'scores' => $user->progress->pluck('score', 'mission_id')->toArray() 
                ];
            })->toJson() }},

            {{-- Logic: Filter Misi Berdasarkan Search --}}
            get filteredMissions() {
                return this.availableMissions.filter(m => 
                    m.title.toLowerCase().includes(this.missionSearch.toLowerCase())
                );
            },

            {{-- Logic: Hitung Total XP Maksimal dari Misi yang Dicentang --}}
            get totalMaxSelected() {
                let max = 0;
                this.selectedMissions.forEach(id => {
                    let m = this.availableMissions.find(x => x.id == id);
                    if(m) max += parseInt(m.max_score);
                });
                return max;
            },

            {{-- Logic: Hitung XP Kumulatif Siswa --}}
            calculateChecklistScore(userScores) {
                if (this.selectedMissions.length === 0) return 0;
                let total = 0;
                this.selectedMissions.forEach(id => {
                    total += (userScores[id] || 0);
                });
                return total;
            },

            {{-- Logic: Konversi ke Nilai 1-100 --}}
            calculateFinalGrade(userScores) {
                let xp = this.calculateChecklistScore(userScores);
                let max = this.totalMaxSelected;
                return max > 0 ? ((xp / max) * 100).toFixed(1) : 0;
            },

            get filteredUsers() {
                let filtered = this.users.filter(u => 
                    u.name.toLowerCase().includes(this.search.toLowerCase())
                );
                return filtered.sort((a, b) => {
                    let mod = this.sortOrder === 'asc' ? 1 : -1;
                    return (a[this.sortBy] - b[this.sortBy]) * mod;
                });
            }
         }">

        {{-- Toast Notification --}}
        @if(session('success') || session('error'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)" class="fixed bottom-10 right-10 z-[200]">
                <div class="bg-slate-900 border {{ session('error') ? 'border-red-500' : 'border-purple-500' }} p-4 rounded-2xl shadow-2xl flex items-center space-x-3">
                    <div class="w-8 h-8 {{ session('error') ? 'bg-red-600' : 'bg-purple-600' }} rounded-lg flex items-center justify-center text-white font-bold">!</div>
                    <p class="text-sm font-bold text-white">{{ session('success') ?? session('error') }}</p>
                </div>
            </div>
        @endif

        {{-- Header Section --}}
        <div class="flex flex-col md:flex-row md:items-end justify-between mb-10 gap-6">
            <div>
                <a href="{{ route('admin.classrooms.index') }}" class="group inline-flex items-center text-purple-600 font-bold text-[10px] tracking-widest uppercase mb-4">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3"><path d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    Kembali ke list squad
                </a>
                <h1 class="text-4xl font-extrabold text-slate-900 tracking-tight">{{ $classroom->name }}</h1>
                <p class="text-slate-500 font-medium text-sm mt-1">Kelola progres, ambil nilai dengan konversi 1-100, dan buat tugas.</p>
            </div>

            <div class="flex bg-white p-1.5 rounded-2xl shadow-sm border border-slate-200">
                <button @click="activeTab = 'monitoring'" :class="activeTab === 'monitoring' ? 'bg-slate-900 text-white shadow-lg' : 'text-slate-400 hover:text-slate-600'" class="btn-tab">Monitoring</button>
                <button @click="activeTab = 'grades'" :class="activeTab === 'grades' ? 'bg-purple-600 text-white shadow-lg' : 'text-slate-400 hover:text-slate-600'" class="btn-tab ml-1">Ambil Nilai</button>
                <button @click="activeTab = 'tasks'" :class="activeTab === 'tasks' ? 'bg-orange-500 text-white shadow-lg' : 'text-slate-400 hover:text-slate-600'" class="btn-tab ml-1">Daftar Task</button>
            </div>
        </div>

        {{-- TAB 1: MONITORING --}}
        <div x-show="activeTab === 'monitoring'" x-transition>
            <div class="admin-card overflow-hidden">
                <div class="p-6 border-b border-slate-50">
                    <input type="text" x-model="search" placeholder="Cari nama praktikan..." class="form-input-premium !w-72" />
                </div>
                <table class="w-full text-left">
                    <thead class="bg-slate-50 border-b border-slate-100">
                        <tr class="text-[10px] font-black text-slate-400 uppercase tracking-widest">
                            <th class="pl-8 py-5 text-center w-16">No</th>
                            <th class="px-8 py-5">Praktikan</th>
                            <th class="px-8 py-5 text-center">Modul</th>
                            <th class="px-8 py-5 text-center">Misi</th>
                            <th class="px-8 py-5 text-center">Total XP</th>
                            <th class="px-8 py-5 text-center">Predikat</th>
                            <th class="pr-8 py-5 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50 text-sm font-bold">
                        <template x-for="(siswa, index) in filteredUsers" :key="siswa.id">
                            <tr class="hover:bg-purple-50/30 transition-all">
                                <td class="pl-8 py-5 text-center text-slate-300" x-text="index + 1"></td>
                                <td class="px-8 py-5 flex items-center space-x-4">
                                    <div class="w-10 h-10 rounded-xl shrink-0" :style="'background-color: #' + siswa.profile_color">
                                        <img :src="siswa.avatar" class="w-full h-full">
                                    </div>
                                    <div class="flex flex-col leading-tight">
                                        <span class="text-slate-800" x-text="siswa.name"></span>
                                        <span class="text-[9px] text-slate-400 uppercase font-black" x-text="siswa.email"></span>
                                    </div>
                                </td>
                                <td class="px-8 py-5 text-center"><span class="px-3 py-1 bg-blue-50 text-blue-600 rounded-full text-[10px] font-black" x-text="siswa.materials"></span></td>
                                <td class="px-8 py-5 text-center"><span class="px-3 py-1 bg-emerald-50 text-emerald-600 rounded-full text-[10px] font-black" x-text="siswa.missions"></span></td>
                                <td class="px-8 py-5 text-center font-mono text-slate-700" x-text="siswa.xp"></td>
                                <td class="px-8 py-5 text-center"><span class="text-[9px] font-black uppercase text-purple-500" x-text="siswa.badge_title"></span></td>
                                <td class="pr-8 py-5 text-right">
                                    <form :action="'/admin/classrooms/{{ $classroom->id }}/kick/' + siswa.id" method="POST" onsubmit="return confirm('Keluarkan siswa ini?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="p-2 text-slate-300 hover:text-red-500"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3 3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg></button>
                                    </form>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>

        {{-- TAB 2: AMBIL NILAI (Checklist + Konversi) --}}
        <div x-show="activeTab === 'grades'" x-transition x-cloak>
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                <div class="lg:col-span-4">
                    <div class="admin-card p-6 sticky top-10">
                        <h3 class="text-xs font-black uppercase tracking-widest text-slate-400 mb-4 flex justify-between">
                            Pilih Misi Evaluasi
                            <span class="text-purple-600" x-text="selectedMissions.length + ' dipilih'"></span>
                        </h3>
                        
                        {{-- Search Misi --}}
                        <div class="relative mb-4">
                            <input type="text" x-model="missionSearch" placeholder="Cari topik misi..." 
                                   class="w-full pl-10 pr-4 py-3 rounded-xl border border-slate-100 text-xs font-bold focus:ring-2 focus:ring-purple-500 transition-all">
                            <svg class="w-4 h-4 absolute left-3 top-3.5 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </div>

                        <div class="space-y-2 max-h-[350px] overflow-y-auto pr-2 custom-scrollbar">
                            <template x-for="m in filteredMissions" :key="m.id">
                                <div class="relative">
                                    <input type="checkbox" :id="'m-'+m.id" :value="m.id" x-model="selectedMissions" class="hidden mission-checkbox">
                                    <label :for="'m-'+m.id" class="flex items-center p-3 border border-slate-50 rounded-xl cursor-pointer hover:border-purple-200 transition-all">
                                        <div class="w-4 h-4 border-2 border-slate-200 rounded flex items-center justify-center mr-3 transition-all" :class="selectedMissions.includes(m.id.toString()) ? 'bg-purple-600 border-purple-600 shadow-lg' : ''">
                                            <svg x-show="selectedMissions.includes(m.id.toString())" class="w-2.5 h-2.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="4"><path d="M5 13l4 4L19 7"></path></svg>
                                        </div>
                                        <div class="flex-1">
                                            <p class="text-[11px] font-black text-slate-700 leading-tight" x-text="m.title"></p>
                                            <p class="text-[8px] text-slate-400 font-bold uppercase mt-0.5" x-text="m.max_score + ' XP'"></p>
                                        </div>
                                    </label>
                                </div>
                            </template>
                        </div>

                        <div class="mt-6 pt-4 border-t border-slate-50">
                            <div class="flex justify-between items-center text-[10px] font-black text-slate-400 uppercase mb-4">
                                <span>Max XP Terpilih:</span>
                                <span class="bg-purple-50 text-purple-600 px-2 py-1 rounded" x-text="totalMaxSelected"></span>
                            </div>
                            <button @click="showTaskModal = true" :disabled="selectedMissions.length === 0" class="w-full py-4 bg-slate-900 text-white rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-slate-800 disabled:opacity-30 transition-all">
                                Simpan Sebagai Task
                            </button>
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-8">
                    <div class="admin-card overflow-hidden">
                        <table class="w-full text-left">
                            <thead class="bg-slate-50 border-b border-slate-100 text-[10px] font-black text-slate-400 uppercase tracking-widest">
                                <tr>
                                    <th class="px-8 py-5">Praktikan</th>
                                    <th class="px-8 py-5 text-center">Kumulatif XP</th>
                                    <th class="px-8 py-5 text-center">Nilai (1-100)</th>
                                    <th class="px-8 py-5 text-right">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50">
                                <template x-for="siswa in filteredUsers" :key="siswa.id">
                                    <tr class="hover:bg-purple-50/30 transition-all">
                                        <td class="px-8 py-6 font-bold text-slate-700" x-text="siswa.name"></td>
                                        <td class="px-8 py-6 text-center font-mono font-bold text-slate-500" x-text="calculateChecklistScore(siswa.scores)"></td>
                                        <td class="px-8 py-6 text-center">
                                            <span class="text-2xl font-black text-purple-600 font-mono" x-text="calculateFinalGrade(siswa.scores)"></span>
                                        </td>
                                        <td class="px-8 py-6 text-right">
                                            <span class="px-3 py-1 rounded-full text-[9px] font-black uppercase" :class="calculateFinalGrade(siswa.scores) >= 75 ? 'bg-emerald-100 text-emerald-600' : 'bg-red-50 text-red-400'" x-text="calculateFinalGrade(siswa.scores) >= 75 ? 'TUNTAS' : 'REMIDI'"></span>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- TAB 3: DAFTAR TASK (CRUD + Export) --}}
        <div x-show="activeTab === 'tasks'" x-transition x-cloak>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @forelse($classroom->tasks as $task)
                <div class="admin-card p-6 border-l-4 border-purple-500 hover:shadow-xl transition-all relative">
                    <div class="flex justify-between items-start mb-6">
                        <div class="leading-tight">
                            <h4 class="text-lg font-black text-slate-800">{{ $task->name }}</h4>
                            <p class="text-[10px] text-slate-400 font-bold uppercase mt-1">Dibuat: {{ $task->created_at->format('d/m/Y') }}</p>
                        </div>
                        <div class="flex gap-1">
                            <button @click="editTaskData = { id: '{{ $task->id }}', name: '{{ $task->name }}' }; showEditTaskModal = true" class="p-2 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </button>
                            <form action="{{ route('admin.tasks.destroy', $task->id) }}" method="POST" onsubmit="return confirm('Hapus tugas ini secara permanen?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="p-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </form>
                        </div>
                    </div>
                    <div class="flex justify-between items-center mb-6">
                        <span class="bg-purple-50 text-purple-600 text-[9px] font-black px-2 py-1 rounded-md">{{ $task->missions->count() }} MISI</span>
                        <span class="text-[9px] font-bold text-slate-300">Max XP: {{ $task->missions->sum('max_score') }}</span>
                    </div>
                    <a href="{{ route('admin.tasks.export', $task->id) }}" class="w-full py-3 bg-emerald-600 text-white rounded-xl text-[9px] font-black uppercase tracking-widest hover:bg-emerald-700 transition-all block text-center">Export Nilai (.CSV)</a>
                </div>
                @empty
                <div class="col-span-full py-24 text-center admin-card bg-slate-50/50 border-dashed">
                    <p class="text-[10px] text-slate-300 font-black uppercase tracking-[0.3em]">Belum ada task tersimpan untuk squad ini</p>
                </div>
                @endforelse
            </div>
        </div>

        {{-- MODAL: SIMPAN TASK BARU --}}
        <div x-show="showTaskModal" class="fixed inset-0 z-[300] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm" x-cloak x-transition>
            <div class="bg-white rounded-[2.5rem] p-10 w-full max-w-md shadow-2xl" @click.away="showTaskModal = false">
                <h3 class="text-2xl font-black text-slate-900 mb-2">Simpan Task Baru</h3>
                <form action="{{ route('admin.classrooms.store-task', $classroom->id) }}" method="POST">
                    @csrf
                    <template x-for="id in selectedMissions">
                        <input type="hidden" name="mission_ids[]" :value="id">
                    </template>
                    <div class="my-6">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 block">Judul Tugas/Task</label>
                        <input type="text" name="task_name" required placeholder="Contoh: Kuis Pertemuan 1" class="form-input-premium">
                    </div>
                    <div class="flex space-x-3">
                        <button type="button" @click="showTaskModal = false" class="flex-1 py-4 bg-slate-100 text-slate-500 rounded-2xl text-[10px] font-black uppercase tracking-widest">Batal</button>
                        <button type="submit" class="flex-1 py-4 bg-purple-600 text-white rounded-2xl text-[10px] font-black uppercase tracking-widest shadow-lg shadow-purple-200">Simpan Task</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- MODAL: EDIT TASK --}}
        <div x-show="showEditTaskModal" class="fixed inset-0 z-[300] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm" x-cloak x-transition>
            <div class="bg-white rounded-[2.5rem] p-10 w-full max-w-md shadow-2xl" @click.away="showEditTaskModal = false">
                <h3 class="text-2xl font-black text-slate-900 mb-2">Perbarui Nama Task</h3>
                <form :action="'/admin/tasks/' + editTaskData.id" method="POST">
                    @csrf @method('PUT')
                    <div class="my-6">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 block">Nama Baru</label>
                        <input type="text" name="task_name" x-model="editTaskData.name" required class="form-input-premium">
                    </div>
                    <div class="flex space-x-3">
                        <button type="button" @click="showEditTaskModal = false" class="flex-1 py-4 bg-slate-100 text-slate-500 rounded-2xl text-[10px] font-black uppercase tracking-widest">Batal</button>
                        <button type="submit" class="flex-1 py-4 bg-blue-600 text-white rounded-2xl text-[10px] font-black uppercase tracking-widest shadow-lg shadow-blue-200">Simpan Perubahan</button>
                    </div>
                </form>
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
</x-app-layout>