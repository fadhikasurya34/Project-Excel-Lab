{{-- 
    VIEW: Monitoring Aktivitas Siswa (Admin)
    DATA: $students (Siswa terdaftar), $allTopics (Untuk filter/info)
    LOGIC: Client-side mapping, filtering, dan sorting via Alpine.js.
--}}

<x-app-layout>
    <style>
        {{-- (Style) Standarisasi UI Terminal Khusus Monitoring (Aksen Orange) --}}
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
            cursor: default;
        }

        .admin-card:hover {
            border-color: #fdba74; 
            box-shadow: 0 10px 25px -5px rgba(249, 115, 22, 0.08);
        }
        .admin-card:active {
            border-color: #f97316;
            box-shadow: 0 0 0 3px rgba(249, 115, 22, 0.1);
        }

        .form-input-premium {
            width: 100%;
            border-radius: 1rem;
            border: 1px solid #e2e8f0;
            background-color: #ffffff;
            padding: 0.875rem 1rem;
            font-size: 0.875rem;
            font-weight: 600;
            color: #334155;
            transition: all 0.2s;
        }
        .form-input-premium:focus {
            outline: none;
            border-color: #f97316;
            box-shadow: 0 0 0 4px rgba(249, 115, 22, 0.05);
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
    </style>

    <div class="min-h-screen bg-admin p-6 sm:p-10" 
         x-data="{ 
            search: '', 
            sortBy: 'xp', 
            sortOrder: 'desc',
            selectedRow: null,
            users: {{ $students->map(function($user) {
                return [
                    'id' => $user->id,
                    'name' => (string) $user->name,
                    'email' => (string) $user->email,
                    'xp' => (int) $user->total_xp,
                    'avatar' => 'https://api.dicebear.com/9.x/bottts/svg?seed='.($user->avatar ?? 'Felix').'&backgroundColor=transparent',
                    'profile_color' => (string) ($user->profile_color ?? 'f97316'),
                    'classroom' => (string) ($user->classrooms->first()->name ?? 'Tanpa Squad'),
                    'show_url' => route('admin.users.show', $user->id),
                    'reset_url' => route('admin.users.reset-xp', $user->id),
                    'delete_url' => route('admin.users.destroy', $user->id)
                ];
            })->toJson() }},
            
            get filteredUsers() {
                let filtered = this.users.filter(u => 
                    u.name.toLowerCase().includes(this.search.toLowerCase()) || 
                    u.email.toLowerCase().includes(this.search.toLowerCase())
                );
                
                return filtered.sort((a, b) => {
                    let modifier = this.sortOrder === 'asc' ? 1 : -1;
                    let valA = a[this.sortBy];
                    let valB = b[this.sortBy];

                    if (this.sortBy === 'name') {
                        return valA.localeCompare(valB) * modifier;
                    }
                    return (valA - valB) * modifier;
                });
            }
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
                
                <div class="bg-slate-900 border {{ session('error') ? 'border-red-500/30' : 'border-orange-500/30' }} p-5 rounded-2xl shadow-[0_20px_50px_rgba(0,0,0,0.3)] flex items-center space-x-4 min-w-[320px] overflow-hidden relative">
                    <div class="absolute -top-10 -right-10 w-24 h-24 {{ session('error') ? 'bg-red-600/20' : 'bg-orange-600/20' }} blur-3xl"></div>
                    
                    <div class="flex-shrink-0 w-10 h-10 {{ session('error') ? 'bg-red-600' : 'bg-orange-600' }} rounded-xl flex items-center justify-center shadow-lg">
                        <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                            @if(session('error'))
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            @else
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                            @endif
                        </svg>
                    </div>

                    <div class="flex-1">
                        <p class="text-[10px] font-black {{ session('error') ? 'text-red-400' : 'text-orange-400' }} uppercase tracking-[0.2em] leading-none mb-1">Activity Monitor</p>
                        <p class="text-sm font-bold text-white tracking-tight leading-tight">
                            {{ session('success') ?? session('status') ?? session('error') }}
                        </p>
                    </div>

                    <div class="absolute bottom-0 left-0 h-1 {{ session('error') ? 'bg-red-600' : 'bg-orange-600' }} transition-all ease-linear" :style="'width: ' + progress + '%'"></div>
                </div>
            </div>
        @endif

        {{-- (Section) Header: Identitas & Summary Counter --}}
        <div class="flex flex-col md:flex-row md:items-center justify-between mb-10 gap-6">
            <div>
                <div class="flex items-center space-x-2 mb-2">
                    <span class="px-2 py-0.5 bg-orange-50 text-orange-700 rounded text-[10px] font-bold tracking-widest uppercase">Monitoring Praktikan</span>
                    <div class="w-1.5 h-1.5 bg-emerald-500 rounded-full shadow-[0_0_8px_rgba(16,185,129,0.6)]"></div>
                </div>
                <h1 class="text-3xl font-extrabold text-slate-900 text-header tracking-tight">Data <span class="text-orange-600">Aktivitas Siswa</span></h1>
                <p class="text-slate-500 font-medium text-sm mt-1">Pantau perolehan XP, progres materi, dan manajemen akun praktikan.</p>
            </div>
            
            <div class="flex items-center bg-white p-2 rounded-2xl shadow-sm border border-slate-200 pr-6">
                <div class="w-10 h-10 rounded-xl bg-slate-900 flex items-center justify-center text-white font-bold shadow-md">
                    {{ count($students) }}
                </div>
                <div class="ml-3">
                    <p class="text-[9px] text-slate-400 font-bold uppercase leading-none tracking-widest">Total Terdaftar</p>
                    <p class="text-xs font-bold text-slate-700 mt-1 capitalize" x-text="users.length + ' Siswa'"></p>
                </div>
            </div>
        </div>

        {{-- (Section) Ringkasan Statistik Siswa & Global Power --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
            <div class="admin-card p-6 flex items-center justify-between">
                <div>
                    <p class="text-[10px] font-bold text-slate-400 tracking-widest uppercase mb-1">Siswa Aktif</p>
                    <h3 class="text-3xl font-black text-slate-900" x-text="users.length"></h3>
                </div>
                <div class="w-12 h-12 bg-orange-50 text-orange-600 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                </div>
            </div>
            
            <div class="admin-card p-6 flex items-center justify-between">
                <div>
                    <p class="text-[10px] font-bold text-slate-400 tracking-widest uppercase mb-1">Topik Lab</p>
                    <h3 class="text-3xl font-black text-slate-900">{{ \App\Models\Level::distinct('category')->count() }}</h3>
                </div>
                <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M4 6h16M4 10h16M4 14h16M4 18h16"></path></svg>
                </div>
            </div>

            <div class="admin-card p-6 flex items-center justify-between">
                <div>
                    <p class="text-[10px] font-bold text-slate-400 tracking-widest uppercase mb-1">Global Power</p>
                    <h3 class="text-3xl font-black text-slate-900">{{ number_format($students->sum('total_xp')) }}</h3>
                </div>
                <div class="w-12 h-12 bg-emerald-50 text-emerald-600 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                </div>
            </div>
        </div>

        {{-- (Section) Controls: Pencarian & Pengurutan Dinamis --}}
        <div class="mb-8 grid grid-cols-1 lg:grid-cols-4 gap-4">
            <div class="lg:col-span-2 relative group">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 group-focus-within:text-orange-500 transition-colors">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
                <input type="text" x-model="search" placeholder="Cari nama atau email praktikan..." 
                       class="form-input-premium !pl-11 shadow-sm">
            </div>

            <div>
                <select x-model="sortBy" class="form-input-premium appearance-none cursor-pointer shadow-sm">
                    <option value="xp">Urut Perolehan XP</option>
                    <option value="name">Urut Nama Praktikan</option>
                </select>
            </div>

            <button @click="sortOrder = (sortOrder === 'asc' ? 'desc' : 'asc')" 
                    class="px-5 py-3 bg-white border border-slate-200 rounded-2xl text-xs font-bold text-slate-600 hover:border-orange-500 transition-all flex items-center justify-between shadow-sm">
                <span x-text="sortOrder === 'desc' ? 'Urutan Teratas' : 'Urutan Terbawah'"></span>
                <svg class="w-4 h-4 ml-2 transition-transform duration-300" :class="sortOrder === 'asc' ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3"><path d="M19 9l-7 7-7-7"/></svg>
            </button>
        </div>

        {{-- (Section) Tabel Data Utama: Daftar Siswa Terdaftar --}}
        <div class="admin-card overflow-hidden shadow-sm !cursor-default">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse min-w-[1000px]">
                    <thead class="bg-slate-50/80 border-b border-slate-100">
                        <tr>
                            <th class="pl-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest w-16 text-center">No</th>
                            <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">Identitas Praktikan</th>
                            <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">Squad / Kelas</th>
                            <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">Power (XP)</th>
                            <th class="pr-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">Manajemen</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <template x-for="(siswa, index) in filteredUsers" :key="siswa.id">
                            <tr @click="selectedRow = siswa.id" 
                                :class="selectedRow === siswa.id ? 'bg-orange-50/50' : 'hover:bg-slate-50/50'"
                                class="transition-all cursor-pointer">
                                <td class="pl-8 py-6 text-center">
                                    <span class="text-xs font-black text-slate-300" x-text="index + 1"></span>
                                </td>
                                <td class="px-8 py-6">
                                    <div class="flex items-center space-x-4">
                                        <div class="w-12 h-12 rounded-2xl border-2 border-white shadow-md overflow-hidden shrink-0 transition-transform group-hover:scale-110" 
                                             :style="'background-color: #' + siswa.profile_color">
                                            <img :src="siswa.avatar" class="w-full h-full pt-1">
                                        </div>
                                        <div class="flex flex-col">
                                            <span class="text-base font-bold text-slate-800 leading-tight" x-text="siswa.name"></span>
                                            <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider mt-1" x-text="siswa.email"></span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-8 py-6 text-center">
                                    <span class="px-4 py-1.5 bg-blue-50 text-blue-700 rounded-xl text-[10px] font-bold uppercase border border-blue-100" x-text="siswa.classroom"></span>
                                </td>
                                <td class="px-8 py-6 text-center">
                                    <div class="inline-flex items-center px-4 py-1.5 bg-white border border-slate-100 rounded-xl transition-all">
                                        <span class="font-black text-slate-700 text-sm mr-1.5 group-hover:text-emerald-600" x-text="new Intl.NumberFormat().format(siswa.xp)"></span>
                                        <span class="text-[8px] text-slate-400 font-bold uppercase tracking-tighter">XP</span>
                                    </div>
                                </td>
                                <td class="pr-8 py-6 text-right">
                                    <div class="flex items-center justify-end space-x-2">
                                        <a :href="siswa.show_url" 
                                           @click.stop
                                           class="btn-action bg-orange-50 text-orange-600 hover:bg-orange-600 hover:text-white shadow-sm" title="Lihat Profil">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                        </a>

                                        <form :action="siswa.reset_url" method="POST" class="m-0 flex items-center" onsubmit="return confirm('PERINGATAN: Aksi ini akan mereset seluruh XP dan menghapus riwayat progres. Lanjutkan?')">
                                            @csrf
                                            <button type="submit" 
                                                    @click.stop
                                                    :class="selectedRow === siswa.id ? 'text-amber-600 bg-amber-50' : 'text-slate-300 hover:text-amber-500'"
                                                    class="btn-action transition-all" title="Reset Keseluruhan">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99"></path></svg>
                                            </button>
                                        </form>

                                        <form :action="siswa.delete_url" method="POST" class="m-0 flex items-center" onsubmit="return confirm('Hapus akun praktikan ini secara permanen?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" 
                                                    @click.stop
                                                    :class="selectedRow === siswa.id ? 'text-red-600 bg-red-50' : 'text-slate-300 hover:text-red-500'"
                                                    class="btn-action transition-all" title="Hapus Akun">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
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

            {{-- Footer Terminal --}}
                <footer class="mt-20 py-8 border-t border-slate-200 flex flex-col md:flex-row items-center justify-between opacity-50 px-2">
                    <div class="text-left leading-tight">
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">© 2026 UNNES Informatics Education</p>
                        <p class="text-[9px] font-medium text-slate-400 uppercase mt-1">Penelitian Pengembangan Virtual Lab Excel</p>
                    </div>
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-tighter">Production v1.0</span>
                </footer>
        </div>
        
    </div>
</x-app-layout>