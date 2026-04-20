{{-- 
    VIEW: Manajemen Squad Kelas
    DATA: $classrooms
    LOGIC: Menggunakan Alpine.js untuk kontrol Modal (Create/Edit) dan interaksi Row.
--}}

<x-app-layout>
    <style>
        {{-- (Style) Custom CSS untuk UI Dashboard Admin --}}
        .bg-admin {
            background-color: #f8fafc;
            background-image: radial-gradient(#e2e8f0 0.8px, transparent 0.8px);
            background-size: 32px 32px;
        }

        .admin-card {
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 1.5rem;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .admin-card:hover {
            border-color: #d8b4fe; 
            box-shadow: 0 10px 25px -5px rgba(168, 85, 247, 0.08);
        }
        .admin-card:active {
            border-color: #a855f7;
            box-shadow: 0 0 0 3px rgba(168, 85, 247, 0.1);
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
            border-color: #a855f7;
            background-color: #ffffff;
            box-shadow: 0 0 0 4px rgba(168, 85, 247, 0.05);
        }

        .btn-action {
            width: 2.5rem;
            height: 2.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 0.75rem;
            transition: all 0.2s;
        }

        .text-header { letter-spacing: -0.02em; }
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
        
        [x-cloak] { display: none !important; }
    </style>

    {{-- (Process) Inisialisasi State Alpine.js --}}
    <div class="min-h-screen bg-admin p-6 sm:p-10" 
         x-data="{ 
            showCreateModal: false, 
            showEditModal: false,
            selectedRow: null,
            editData: { id: '', name: '', teacher: '' }
         }">

        {{-- (Notification) Toast System: Feedback sinkronisasi data --}}
        @if(session('success') || session('error') || session('status'))
            <div x-data="{ show: true, progress: 100 }"
                x-show="show"
                x-init="let interval = setInterval(() => { progress -= 1; if(progress <= 0) { show = false; clearInterval(interval); } }, 30);"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="translate-y-10 opacity-0 scale-90"
                x-transition:enter-end="translate-y-0 opacity-100 scale-100"
                class="fixed bottom-10 right-10 z-[200]">
                
                <div class="bg-slate-900 border {{ session('error') ? 'border-red-500/30' : 'border-purple-500/30' }} p-5 rounded-2xl shadow-[0_20px_50px_rgba(0,0,0,0.3)] flex items-center space-x-4 min-w-[320px] overflow-hidden relative">
                    <div class="absolute -top-10 -right-10 w-24 h-24 {{ session('error') ? 'bg-red-600/20' : 'bg-purple-600/20' }} blur-3xl"></div>
                    
                    <div class="flex-shrink-0 w-10 h-10 {{ session('error') ? 'bg-red-600' : 'bg-purple-600' }} rounded-xl flex items-center justify-center shadow-lg">
                        <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                            @if(session('error'))
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            @else
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                            @endif
                        </svg>
                    </div>

                    <div class="flex-1">
                        <p class="text-[10px] font-black {{ session('error') ? 'text-red-400' : 'text-purple-400' }} uppercase tracking-[0.2em] leading-none mb-1">Squad Sync</p>
                        <p class="text-sm font-bold text-white tracking-tight leading-tight">
                            {{ session('success') ?? session('status') ?? session('error') }}
                        </p>
                    </div>

                    <div class="absolute bottom-0 left-0 h-1 {{ session('error') ? 'bg-red-600' : 'bg-purple-600' }} transition-all ease-linear" :style="'width: ' + progress + '%'"></div>
                </div>
            </div>
        @endif
        
        {{-- (Section) Header: Judul & Tombol Tambah --}}
        <div class="flex flex-col md:flex-row md:items-center justify-between mb-10 gap-6">
            <div>
                <div class="flex items-center space-x-2 mb-2">
                    <span class="px-2 py-0.5 bg-purple-50 text-purple-700 rounded text-[10px] font-bold tracking-widest uppercase">Ekosistem Kelas</span>
                    <div class="w-1.5 h-1.5 bg-emerald-500 rounded-full shadow-[0_0_8px_rgba(16,185,129,0.6)]"></div>
                </div>
                <h1 class="text-3xl font-extrabold text-slate-900 text-header tracking-tight">Manajemen <span class="text-purple-600">Squad Kelas</span></h1>
                <p class="text-slate-500 font-medium text-sm mt-1">Kelola kode akses dan pantau keanggotaan kelas praktikan.</p>
            </div>
            
            <button @click="showCreateModal = true" 
                    class="inline-flex items-center justify-center px-8 py-4 bg-purple-600 hover:bg-purple-700 text-white text-[11px] font-bold rounded-2xl transition shadow-xl shadow-purple-100 tracking-widest uppercase active:scale-95">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3"><path d="M12 4v16m8-8H4"></path></svg>
                Generate kelas baru
            </button>
        </div>

        {{-- (Section) Ringkasan Statistik --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
            <div class="admin-card p-6 flex items-center justify-between">
                <div>
                    <p class="text-[10px] font-bold text-slate-400 tracking-widest uppercase mb-1">Total Squad</p>
                    <h3 class="text-3xl font-black text-slate-900">{{ $classrooms->count() }}</h3>
                </div>
                <div class="w-12 h-12 bg-purple-50 text-purple-600 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                </div>
            </div>
            
            <div class="admin-card p-6 flex items-center justify-between">
                <div>
                    <p class="text-[10px] font-bold text-slate-400 tracking-widest uppercase mb-1">Siswa Terdaftar</p>
                    <h3 class="text-3xl font-black text-slate-900">{{ $classrooms->sum('users_count') }}</h3>
                </div>
                <div class="w-12 h-12 bg-orange-50 text-orange-600 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197"></path></svg>
                </div>
            </div>

            <div class="admin-card p-6 flex items-center justify-between">
                <div>
                    <p class="text-[10px] font-bold text-slate-400 tracking-widest uppercase mb-1">Rerata Populasi</p>
                    <h3 class="text-3xl font-black text-slate-900">{{ $classrooms->count() > 0 ? round($classrooms->sum('users_count') / $classrooms->count()) : 0 }}</h3>
                </div>
                <div class="w-12 h-12 bg-emerald-50 text-emerald-600 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                </div>
            </div>
        </div>

        {{-- (Section) Tabel Utama Squad --}}
        <div class="admin-card overflow-hidden shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse min-w-[800px]">
                    <thead class="bg-slate-50/80 border-b border-slate-100">
                        <tr>
                            <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">Squad Kelas</th>
                            <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">Guru Pengampu</th>
                            <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">Kode Akses</th>
                            <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">Populasi</th>
                            <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">Manajemen</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($classrooms as $kelas)
                        <tr @click="selectedRow = '{{ $kelas->id }}'" 
                            :class="selectedRow === '{{ $kelas->id }}' ? 'bg-purple-50/50' : 'hover:bg-slate-50/50'"
                            class="transition-all cursor-pointer">
                            <td class="px-8 py-6">
                                <div class="flex items-center space-x-4">
                                    <div class="w-10 h-10 bg-white border border-slate-100 text-purple-600 rounded-xl flex items-center justify-center text-lg shadow-sm">🏛️</div>
                                    <div class="flex flex-col">
                                        <span class="text-base font-bold text-slate-900 leading-tight">{{ $kelas->name }}</span>
                                        <span class="text-[9px] text-slate-400 font-bold tracking-widest mt-1 uppercase">Unit Belajar Komunitas</span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-8 py-6 text-center">
                                <span class="text-sm font-semibold text-slate-600">{{ $kelas->teacher_name }}</span>
                            </td>
                            <td class="px-8 py-6 text-center">
                                <code class="px-4 py-2 bg-slate-900 text-emerald-400 font-mono text-xs rounded-lg border-b-2 border-slate-700 tracking-widest">
                                    {{ $kelas->class_code }}
                                </code>
                            </td>
                            <td class="px-8 py-6 text-center">
                                <div class="inline-flex items-center px-4 py-1.5 bg-white border border-slate-100 rounded-xl transition-all">
                                    <span class="font-black text-purple-600 text-sm mr-1.5">{{ $kelas->users_count }}</span>
                                    <span class="text-[8px] text-slate-400 font-bold uppercase">Siswa</span>
                                </div>
                            </td>
                            <td class="px-8 py-6 text-right">
                                <div class="flex items-center justify-end space-x-2">
                                    <a href="{{ route('admin.classrooms.show', $kelas->id) }}" 
                                       class="btn-action bg-purple-50 text-purple-600 hover:bg-purple-600 hover:text-white" title="Monitor">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    </a>

                                    <button @click.stop="editData = { id: '{{ $kelas->id }}', name: '{{ $kelas->name }}', teacher: '{{ $kelas->teacher_name }}' }; showEditModal = true" 
                                            :class="selectedRow === '{{ $kelas->id }}' ? 'text-blue-600 bg-blue-50' : 'text-slate-300 hover:text-blue-400'"
                                            class="btn-action transition-all" title="Edit Kelas">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </button>
                                    
                                    <form action="{{ route('admin.classrooms.destroy', $kelas->id) }}" method="POST" class="m-0 flex items-center" onsubmit="return confirm('Hapus kelas ini secara permanen?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" 
                                                @click.stop
                                                :class="selectedRow === '{{ $kelas->id }}' ? 'text-red-600 bg-red-50' : 'text-slate-300 hover:text-red-400'"
                                                class="btn-action transition-all">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-8 py-24 text-center">
                                <p class="text-[10px] text-slate-300 font-black uppercase tracking-[0.3em]">Belum ada squad kelas terdaftar</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- (Section) Modal: Buat Kelas Baru --}}
        <div x-show="showCreateModal" class="fixed inset-0 z-[100] flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4" x-cloak x-transition>
            <div class="bg-white w-full max-w-lg rounded-[2rem] shadow-2xl p-10 border border-slate-100" @click.away="showCreateModal = false">
                <h2 class="text-2xl font-black text-slate-900 tracking-tight">Buat Squad Baru</h2>
                <p class="text-sm text-slate-500 mb-8 font-medium">Hasilkan kode akses unik untuk praktikan.</p>
                
                <form action="{{ route('admin.classrooms.store') }}" method="POST" class="space-y-6">
                    @csrf
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2 ml-1">Nama Kelas</label>
                        <input type="text" name="name" class="form-input-premium" placeholder="Contoh: X Akuntansi 1" required>
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2 ml-1">Guru Pengampu</label>
                        <input type="text" name="teacher_name" class="form-input-premium" placeholder="Nama Lengkap Guru" required>
                    </div>
                    <div class="flex gap-4 pt-4">
                        <button type="button" @click="showCreateModal = false" class="flex-1 py-4 bg-slate-100 text-slate-500 font-bold rounded-xl text-[10px] uppercase tracking-widest">Batal</button>
                        <button type="submit" class="flex-[2] py-4 bg-purple-600 text-white font-bold rounded-xl text-[10px] uppercase tracking-widest shadow-lg shadow-purple-100">Hasilkan Kode Akses</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- (Section) Modal: Edit Data Squad --}}
        <div x-show="showEditModal" class="fixed inset-0 z-[100] flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4" x-cloak x-transition>
            <div class="bg-white w-full max-w-lg rounded-[2rem] shadow-2xl p-10 border border-slate-100" @click.away="showEditModal = false">
                <h2 class="text-2xl font-black text-slate-900 tracking-tight">Edit Data Squad</h2>
                <p class="text-sm text-slate-500 mb-8 font-medium">Perbarui identitas atau pengampu squad.</p>
                
                <form :action="'/admin/classrooms/' + editData.id" method="POST" class="space-y-6">
                    @csrf @method('PUT')
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2 ml-1">Nama Kelas</label>
                        <input type="text" name="name" x-model="editData.name" class="form-input-premium" required>
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2 ml-1">Guru Pengampu</label>
                        <input type="text" name="teacher_name" x-model="editData.teacher" class="form-input-premium" required>
                    </div>
                    <div class="flex gap-4 pt-4">
                        <button type="button" @click="showEditModal = false" class="flex-1 py-4 bg-slate-100 text-slate-500 font-bold rounded-xl text-[10px] uppercase tracking-widest">Batal</button>
                        <button type="submit" class="flex-[2] py-4 bg-blue-600 text-white font-bold rounded-xl text-[10px] uppercase tracking-widest shadow-lg shadow-blue-100">Simpan Perubahan</button>
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