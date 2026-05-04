<x-app-layout>
    <style>
        .bg-admin { background-color: #f8fafc; background-image: radial-gradient(#e2e8f0 0.8px, transparent 0.8px); background-size: 32px 32px; }
        .admin-card { background: white; border: 1px solid #e2e8f0; border-radius: 1.5rem; transition: all 0.3s ease; }
        .form-input-premium { width: 100%; border-radius: 1rem; border: 1px solid #e2e8f0; padding: 0.875rem 1rem; font-size: 0.875rem; font-weight: 600; color: #334155; }
        .btn-tab { padding: 0.625rem 1.25rem; border-radius: 0.75rem; font-size: 0.75rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.05em; transition: all 0.2s; }
        .mission-checkbox:checked + label { border-color: #a855f7; background-color: #faf5ff; box-shadow: 0 4px 12px rgba(168, 85, 247, 0.1); }
        
        /* FIXED: Sembunyikan scroller tapi fungsi geser tetap jalan */
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }

        [x-cloak] { display: none !important; }
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
    </style>

    <div class="min-h-screen bg-admin p-4 sm:p-10" 
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
            availableMissions: @js($availableMissions),
            users: {{ $classroom->users->map(function($user) {
                return [
                    'id' => $user->id,
                    'name' => (string) $user->name,
                    'email' => (string) $user->email,
                    'xp' => (int) ($user->total_xp ?? 0),
                    'materials' => (int) $user->completedMaterials->count(), 
                    'missions' => (int) $user->progress->where('status', 'completed')->count(),
                    'badge_medal' => (string) ($user->rank_status['medal'] ?? '-'),
                    'badge_title' => (string) ($user->rank_status['title'] ?? 'Newbie'),
                    'avatar' => 'https://api.dicebear.com/9.x/bottts/svg?seed='.($user->avatar ?? $user->name).'&backgroundColor=transparent',
                    'profile_color' => (string) ($user->profile_color ?? 'a855f7'),
                    'scores' => $user->progress->pluck('score', 'mission_id')->toArray() 
                ];
            })->toJson() }},

            get filteredMissions() {
                return this.availableMissions.filter(m => m.title.toLowerCase().includes(this.missionSearch.toLowerCase()));
            },
            get totalMaxSelected() {
                let max = 0;
                this.selectedMissions.forEach(id => {
                    let m = this.availableMissions.find(x => x.id == id);
                    if(m) max += parseInt(m.max_score);
                });
                return max;
            },
            calculateChecklistScore(userScores) {
                if (this.selectedMissions.length === 0) return 0;
                let total = 0;
                this.selectedMissions.forEach(id => { total += (userScores[id] || 0); });
                return total;
            },
            calculateFinalGrade(userScores) {
                let xp = this.calculateChecklistScore(userScores);
                let max = this.totalMaxSelected;
                return max > 0 ? ((xp / max) * 100).toFixed(1) : 0;
            },
            get filteredUsers() {
                let filtered = this.users.filter(u => u.name.toLowerCase().includes(this.search.toLowerCase()));
                return filtered.sort((a, b) => {
                    let mod = this.sortOrder === 'asc' ? 1 : -1;
                    return (a[this.sortBy] - b[this.sortBy]) * mod;
                });
            }
         }">

        {{-- Toast Notification --}}
        @if(session('success') || session('error'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)" class="fixed bottom-6 right-4 sm:bottom-10 sm:right-10 z-[200]">
                <div class="bg-slate-900 border {{ session('error') ? 'border-red-500' : 'border-purple-500' }} p-4 rounded-2xl shadow-2xl flex items-center space-x-3">
                    <div class="w-8 h-8 {{ session('error') ? 'bg-red-600' : 'bg-purple-600' }} rounded-lg flex items-center justify-center text-white font-bold">!</div>
                    <p class="text-sm font-bold text-white">{{ session('success') ?? session('error') }}</p>
                </div>
            </div>
        @endif

        {{-- Header Section --}}
        <div class="flex flex-col md:flex-row md:items-end justify-between mb-8 sm:mb-10 gap-6">
            <div>
                <a href="{{ route('admin.classrooms.index') }}" class="group inline-flex items-center text-purple-600 font-bold text-[10px] tracking-widest uppercase mb-4">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3"><path d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    Kembali ke list squad
                </a>
                <h1 class="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight">{{ $classroom->name }}</h1>
                <p class="text-slate-500 font-medium text-xs sm:text-sm mt-1 leading-relaxed">Kelola progres, ambil nilai konversi 1-100, dan buat tugas.</p>
            </div>

            {{-- FIXED: Menambahkan class no-scrollbar --}}
            <div class="flex bg-white p-1.5 rounded-2xl shadow-sm border border-slate-200 overflow-x-auto no-scrollbar md:overflow-visible">
                <button @click="activeTab = 'monitoring'" :class="activeTab === 'monitoring' ? 'bg-slate-900 text-white shadow-lg' : 'text-slate-400 hover:text-slate-600'" class="btn-tab whitespace-nowrap">Monitoring</button>
                <button @click="activeTab = 'grades'" :class="activeTab === 'grades' ? 'bg-purple-600 text-white shadow-lg' : 'text-slate-400 hover:text-slate-600'" class="btn-tab ml-1 whitespace-nowrap">Ambil Nilai</button>
                <button @click="activeTab = 'tasks'" :class="activeTab === 'tasks' ? 'bg-orange-500 text-white shadow-lg' : 'text-slate-400 hover:text-slate-600'" class="btn-tab ml-1 whitespace-nowrap">Daftar Task</button>
            </div>
        </div>

        {{-- TAB 1: MONITORING --}}
        <div x-show="activeTab === 'monitoring'" x-transition>
            <div class="admin-card overflow-hidden">
                <div class="p-4 sm:p-6 border-b border-slate-50 bg-white">
                    <input type="text" x-model="search" placeholder="Cari nama praktikan..." class="form-input-premium w-full sm:!w-72" />
                </div>
                {{-- FIXED: Menambah md:table-fixed agar header dan body sejajar --}}
                <table class="w-full text-left block md:table md:table-fixed">
                    <thead class="bg-slate-50 border-b border-slate-100 hidden md:table-header-group">
                        <tr class="text-[10px] font-black text-slate-400 uppercase tracking-widest">
                            <th class="pl-8 py-5 text-center w-16">No</th>
                            <th class="px-8 py-5 w-auto">Praktikan</th>
                            <th class="px-8 py-5 text-center w-32">Modul</th>
                            <th class="px-8 py-5 text-center w-32">Misi</th>
                            <th class="px-8 py-5 text-center w-40">Total XP</th>
                            <th class="px-8 py-5 text-center w-40">Predikat</th>
                            <th class="pr-8 py-5 text-right w-24">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50 text-sm font-bold block md:table-row-group">
                        <template x-for="(siswa, index) in filteredUsers" :key="siswa.id">
                            <tr class="hover:bg-purple-50/30 transition-all block md:table-row p-4 mb-4 bg-white md:bg-transparent rounded-2xl border border-slate-100 md:border-none shadow-sm md:shadow-none">
                                <td class="hidden md:table-cell pl-8 py-5 text-center text-slate-300" x-text="index + 1"></td>
                                <td class="block md:table-cell py-2 md:py-5 md:px-8">
                                    <div class="flex items-center space-x-4">
                                        <div class="w-10 h-10 rounded-xl shrink-0" :style="'background-color: #' + siswa.profile_color">
                                            <img :src="siswa.avatar" class="w-full h-full">
                                        </div>
                                        <div class="flex flex-col leading-tight min-w-0">
                                            <span class="text-slate-800 truncate" x-text="siswa.name"></span>
                                            <span class="text-[9px] text-slate-400 uppercase font-black truncate" x-text="siswa.email"></span>
                                        </div>
                                    </div>
                                </td>
                                <td class="block md:table-cell py-3 md:py-5 md:px-8">
                                    <div class="flex md:contents items-center justify-between">
                                        <span class="md:hidden text-[10px] font-black text-slate-400 uppercase tracking-widest">Modul</span>
                                        <div class="md:text-center">
                                            <span class="px-3 py-1 bg-blue-50 text-blue-600 rounded-lg text-[9px] font-black uppercase" x-text="siswa.materials"></span>
                                        </div>
                                    </div>
                                </td>
                                <td class="block md:table-cell py-3 md:py-5 md:px-8">
                                    <div class="flex md:contents items-center justify-between">
                                        <span class="md:hidden text-[10px] font-black text-slate-400 uppercase tracking-widest">Misi</span>
                                        <div class="md:text-center">
                                            <span class="px-3 py-1 bg-emerald-50 text-emerald-600 rounded-lg text-[9px] font-black uppercase" x-text="siswa.missions"></span>
                                        </div>
                                    </div>
                                </td>
                                <td class="block md:table-cell py-3 md:py-5 md:px-8 md:text-center">
                                    <div class="flex md:contents items-center justify-between">
                                        <span class="md:hidden text-[10px] font-black text-slate-400 uppercase tracking-widest">Power</span>
                                        <span class="font-mono text-slate-700" x-text="new Intl.NumberFormat().format(siswa.xp)"></span>
                                    </div>
                                </td>
                                <td class="block md:table-cell py-3 md:py-5 md:px-8 md:text-center">
                                    <div class="flex md:contents items-center justify-between">
                                        <span class="md:hidden text-[10px] font-black text-slate-400 uppercase tracking-widest">Pangkat</span>
                                        <span class="text-[9px] font-black uppercase text-purple-500" x-text="siswa.badge_title"></span>
                                    </div>
                                </td>
                                <td class="block md:table-cell py-4 md:py-5 md:pr-8 text-right border-t border-slate-50 md:border-none mt-4 md:mt-0">
                                    <div class="flex items-center justify-between md:justify-end">
                                        <span class="md:hidden text-[10px] font-black text-slate-400 uppercase tracking-widest">Opsi</span>
                                        <form :action="'/admin/classrooms/{{ $classroom->id }}/kick/' + siswa.id" method="POST" onsubmit="return confirm('Keluarkan?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="p-2 text-slate-300 hover:text-red-500 transition-colors">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3 3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>

        {{-- TAB 2: AMBIL NILAI --}}
        <div x-show="activeTab === 'grades'" x-transition x-cloak>
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                <div class="lg:col-span-4">
                    <div class="admin-card p-6 sticky top-6">
                        <h3 class="text-xs font-black uppercase tracking-widest text-slate-400 mb-4 flex justify-between">Misi Evaluasi <span class="text-purple-600" x-text="selectedMissions.length"></span></h3>
                        <div class="relative mb-4"><input type="text" x-model="missionSearch" placeholder="Cari misi..." class="form-input-premium !py-2.5 !text-xs shadow-sm"></div>
                        <div class="space-y-2 max-h-[300px] overflow-y-auto pr-2 custom-scrollbar">
                            <template x-for="m in filteredMissions" :key="m.id">
                                <label class="flex items-center p-3 border border-slate-50 rounded-xl cursor-pointer hover:border-purple-200 transition-all">
                                    <input type="checkbox" :value="m.id" x-model="selectedMissions" class="hidden mission-checkbox">
                                    <div class="w-4 h-4 border-2 border-slate-200 rounded mr-3 transition-all shrink-0" :class="selectedMissions.includes(m.id.toString()) ? 'bg-purple-600 border-purple-600' : ''"></div>
                                    <div class="flex-1 min-w-0"><p class="text-[11px] font-black text-slate-700 leading-tight truncate" x-text="m.title"></p><p class="text-[8px] text-slate-400 font-bold uppercase mt-0.5" x-text="m.max_score + ' XP'"></p></div>
                                </label>
                            </template>
                        </div>
                        <div class="mt-6 pt-4 border-t border-slate-50"><button @click="showTaskModal = true" :disabled="selectedMissions.length === 0" class="w-full py-4 bg-slate-900 text-white rounded-2xl text-[10px] font-black uppercase tracking-widest disabled:opacity-30">Simpan Task</button></div>
                    </div>
                </div>

                <div class="lg:col-span-8">
                    <div class="admin-card overflow-hidden">
                        <table class="w-full text-left block md:table md:table-fixed">
                            <thead class="bg-slate-50 border-b border-slate-100 text-[10px] font-black text-slate-400 uppercase tracking-widest hidden md:table-header-group">
                                <tr>
                                    <th class="px-8 py-5 w-auto">Praktikan</th>
                                    <th class="px-8 py-5 text-center w-40">Kumulatif XP</th>
                                    <th class="px-8 py-5 text-center w-40">Skor (1-100)</th>
                                    <th class="px-8 py-5 text-right w-32">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50 block md:table-row-group">
                                <template x-for="siswa in filteredUsers" :key="siswa.id">
                                    <tr class="hover:bg-purple-50/30 transition-all block md:table-row p-5 mb-4 bg-white md:bg-transparent rounded-2xl shadow-sm md:shadow-none">
                                        <td class="block md:table-cell py-1 md:py-6 md:px-8 font-bold text-slate-700 truncate" x-text="siswa.name"></td>
                                        <td class="block md:table-cell py-2 md:py-6 md:px-8 md:text-center">
                                            <div class="flex md:contents items-center justify-between">
                                                <span class="md:hidden text-[10px] font-black text-slate-400 uppercase">XP</span>
                                                <span class="font-mono font-bold text-slate-500" x-text="calculateChecklistScore(siswa.scores)"></span>
                                            </div>
                                        </td>
                                        <td class="block md:table-cell py-2 md:py-6 md:px-8 md:text-center">
                                            <div class="flex md:contents items-center justify-between">
                                                <span class="md:hidden text-[10px] font-black text-slate-400 uppercase tracking-widest">Nilai</span>
                                                <span class="text-2xl font-black text-purple-600 font-mono" x-text="calculateFinalGrade(siswa.scores)"></span>
                                            </div>
                                        </td>
                                        <td class="block md:table-cell py-4 md:py-6 md:px-8 md:text-right border-t border-slate-50 md:border-none mt-3 md:mt-0">
                                            <div class="flex md:contents items-center justify-between">
                                                <span class="md:hidden text-[10px] font-black text-slate-400 uppercase">Status</span>
                                                <span class="px-3 py-1 rounded-full text-[9px] font-black uppercase" :class="calculateFinalGrade(siswa.scores) >= 75 ? 'bg-emerald-100 text-emerald-600' : 'bg-red-50 text-red-400'" x-text="calculateFinalGrade(siswa.scores) >= 75 ? 'TUNTAS' : 'REMIDI'"></span>
                                            </div>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- TAB 3: DAFTAR TASK --}}
        <div x-show="activeTab === 'tasks'" x-transition x-cloak>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($classroom->tasks as $task)
                <div class="admin-card p-6 border-l-4 border-purple-500 hover:shadow-xl transition-all relative">
                    <div class="flex justify-between items-start mb-6">
                        <div class="leading-tight min-w-0">
                            <h4 class="text-lg font-black text-slate-800 truncate">{{ $task->name }}</h4>
                            <p class="text-[10px] text-slate-400 font-bold uppercase mt-1">Dibuat: {{ $task->created_at->format('d/m/Y') }}</p>
                        </div>
                        <div class="flex gap-1 shrink-0">
                            <button @click="editTaskData = { id: '{{ $task->id }}', name: '{{ $task->name }}' }; showEditTaskModal = true" class="p-2 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </button>
                            <form action="{{ route('admin.tasks.destroy', $task->id) }}" method="POST" onsubmit="return confirm('Hapus tugas?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="p-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-100">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </form>
                        </div>
                    </div>
                    <div class="flex justify-between items-center mb-6">
                        <span class="bg-purple-50 text-purple-600 text-[9px] font-black px-2 py-1 rounded-md uppercase">{{ $task->missions->count() }} MISI</span>
                        <span class="text-[9px] font-bold text-slate-300">Max XP: {{ $task->missions->sum('max_score') }}</span>
                    </div>
                    <a href="{{ route('admin.tasks.export', $task->id) }}" class="w-full py-3 bg-emerald-600 text-white rounded-xl text-[9px] font-black uppercase tracking-widest hover:bg-emerald-700 transition-all block text-center shadow-lg shadow-emerald-100">Export Nilai (.CSV)</a>
                </div>
                @empty
                <div class="col-span-full py-24 text-center admin-card bg-slate-50/50 border-dashed border-2">
                    <p class="text-[10px] text-slate-300 font-black uppercase tracking-[0.3em]">Belum ada task tersimpan</p>
                </div>
                @endforelse
            </div>
        </div>

        {{-- Modal Area --}}
        <div x-show="showTaskModal" class="fixed inset-0 z-[300] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm" x-cloak x-transition>
            <div class="bg-white rounded-[2.5rem] p-6 sm:p-10 w-full max-w-md shadow-2xl" @click.away="showTaskModal = false">
                <h3 class="text-xl sm:text-2xl font-black text-slate-900 mb-2">Simpan Task Baru</h3>
                <form action="{{ route('admin.classrooms.store-task', $classroom->id) }}" method="POST">
                    @csrf
                    <template x-for="id in selectedMissions"><input type="hidden" name="mission_ids[]" :value="id"></template>
                    <div class="my-6">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 block">Judul Tugas/Task</label>
                        <input type="text" name="task_name" required placeholder="Contoh: Kuis Pertemuan 1" class="form-input-premium shadow-sm">
                    </div>
                    <div class="flex space-x-3">
                        <button type="button" @click="showTaskModal = false" class="flex-1 py-4 bg-slate-100 text-slate-500 rounded-2xl text-[10px] font-black uppercase tracking-widest">Batal</button>
                        <button type="submit" class="flex-1 py-4 bg-purple-600 text-white rounded-2xl text-[10px] font-black uppercase tracking-widest shadow-lg">Simpan</button>
                    </div>
                </form>
            </div>
        </div>

        <div x-show="showEditTaskModal" class="fixed inset-0 z-[300] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm" x-cloak x-transition>
            <div class="bg-white rounded-[2.5rem] p-6 sm:p-10 w-full max-w-md shadow-2xl" @click.away="showEditTaskModal = false">
                <h3 class="text-xl sm:text-2xl font-black text-slate-900 mb-2">Perbarui Nama Task</h3>
                <form :action="'/admin/tasks/' + editTaskData.id" method="POST">
                    @csrf @method('PUT')
                    <div class="my-6">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 block">Nama Baru</label>
                        <input type="text" name="task_name" x-model="editTaskData.name" required class="form-input-premium shadow-sm">
                    </div>
                    <div class="flex space-x-3">
                        <button type="button" @click="showEditTaskModal = false" class="flex-1 py-4 bg-slate-100 text-slate-500 rounded-2xl text-[10px] font-black uppercase tracking-widest">Batal</button>
                        <button type="submit" class="flex-1 py-4 bg-blue-600 text-white rounded-2xl text-[10px] font-black uppercase tracking-widest shadow-lg">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>