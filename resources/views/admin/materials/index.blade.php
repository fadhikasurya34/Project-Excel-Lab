{{-- 
    VIEW: Manajemen Konten Modul (Admin)
    DATA: $categories (Dari MaterialCategory), $stats (Statistik Global)
    DESC: Mengelola folder/topik kurikulum sebelum mengisi modul materi.
--}}

<x-app-layout>
    <style>
        {{-- (Style) Tema Admin Terminal: Blue (Materi) --}}
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
            border-color: #2563eb;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.04);
        }

        .table-header {
            background: #fcfdfe;
            border-bottom: 2px solid #f1f5f9;
        }

        .btn-blue-main {
            background-color: #2563eb;
            color: white;
            font-weight: 800;
            border-radius: 1rem;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            font-size: 0.75rem;
            box-shadow: 0 10px 15px -3px rgba(37, 99, 235, 0.2);
            transition: all 0.2s ease;
        }
        .btn-blue-main:hover {
            background-color: #1d4ed8;
            transform: translateY(-1px);
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

        .text-header { letter-spacing: -0.02em; }
        [x-cloak] { display: none !important; }
    </style>

    <div class="min-h-screen bg-admin p-4 sm:p-10" 
         x-data="{ 
            openTopicModal: false, 
            editMode: false, 
            topicData: { id: '', name: '', description: '' } 
         }">

        {{-- (Notification) Toast System --}}
        @if(session('success') || session('error'))
            <div x-data="{ show: true, progress: 100 }"
                x-show="show"
                x-init="let interval = setInterval(() => { progress -= 1; if(progress <= 0) { show = false; clearInterval(interval); } }, 30);"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="translate-y-10 opacity-0 scale-90"
                x-transition:enter-end="translate-y-0 opacity-100 scale-100"
                class="fixed bottom-6 right-4 sm:bottom-10 sm:right-10 z-[200]">
                
                <div class="bg-slate-900 border {{ session('error') ? 'border-red-500/30' : 'border-blue-500/30' }} p-5 rounded-2xl shadow-[0_20px_50px_rgba(0,0,0,0.3)] flex items-center space-x-4 min-w-[280px] sm:min-w-[320px] overflow-hidden relative">
                    <div class="absolute -top-10 -right-10 w-24 h-24 {{ session('error') ? 'bg-red-600/20' : 'bg-blue-600/20' }} blur-3xl"></div>
                    <div class="flex-shrink-0 w-10 h-10 {{ session('error') ? 'bg-red-600' : 'bg-blue-600' }} rounded-xl flex items-center justify-center shadow-lg">
                        <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                            <path stroke-linecap="round" stroke-linejoin="round" d="{{ session('error') ? 'M6 18L18 6M6 6l12 12' : 'M5 13l4 4L19 7' }}" />
                        </svg>
                    </div>
                    <div class="flex-1">
                        <p class="text-[10px] font-black {{ session('error') ? 'text-red-400' : 'text-blue-400' }} uppercase tracking-[0.2em] leading-none mb-1">System Update</p>
                        <p class="text-sm font-bold text-white tracking-tight leading-tight">{{ session('success') ?? session('error') }}</p>
                    </div>
                    <div class="absolute bottom-0 left-0 h-1 {{ session('error') ? 'bg-red-600' : 'bg-blue-600' }} transition-all ease-linear" :style="'width: ' + progress + '%'"></div>
                </div>
            </div>
        @endif

        {{-- (Section) Header --}}
        <div class="flex flex-col md:flex-row md:items-center justify-between mb-8 sm:mb-12 gap-6">
            <div>
                <div class="flex items-center space-x-2 mb-2">
                    <span class="px-2 py-0.5 bg-blue-50 text-blue-700 rounded text-[10px] font-bold tracking-widest uppercase">Materi Terminal</span>
                    <div class="w-1.5 h-1.5 bg-emerald-500 rounded-full shadow-[0_0_8px_rgba(37,99,235,0.6)]"></div>
                </div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 text-header tracking-tight">Manajemen <span class="text-blue-600">Topik Modul</span></h1>
                <p class="text-slate-500 font-medium text-xs sm:text-sm mt-1">Kelola folder topik kurikulum sebelum mengisi modul interaktif.</p>
            </div>
            <button @click="openTopicModal = true; editMode = false; topicData = { id: '', name: '', description: '' }" 
                    class="btn-blue-main inline-flex items-center justify-center px-6 sm:px-8 py-3.5 sm:py-4 w-full md:w-auto">
                <svg class="w-5 h-5 mr-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path d="M12 4v16m8-8H4"></path></svg>
                Tambah topik baru
            </button>
        </div>

        {{-- (Section) Statistik --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4 sm:gap-6 mb-10">
            <div class="admin-card p-5 sm:p-6 flex items-center justify-between">
                <div>
                    <p class="text-[10px] font-bold text-slate-400 tracking-widest uppercase mb-1">Total Topik</p>
                    <h3 class="text-2xl sm:text-3xl font-black text-slate-900">{{ $categories->count() }}</h3>
                </div>
                <div class="w-10 h-10 sm:w-12 sm:h-12 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"></path></svg>
                </div>
            </div>

            {{-- UPDATE: Kartu Total Modul sekarang menggunakan count dari relasi kategori yang diload di halaman ini --}}
            <div class="admin-card p-5 sm:p-6 flex items-center justify-between">
                <div>
                    <p class="text-[10px] font-bold text-slate-400 tracking-widest uppercase mb-1">Total Modul</p>
                    <h3 class="text-2xl sm:text-3xl font-black text-slate-900">{{ $categories->sum('materials_count') }}</h3>
                </div>
                <div class="w-10 h-10 sm:w-12 sm:h-12 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253"></path></svg>
                </div>
            </div>
            <div class="admin-card p-5 sm:p-6 flex items-center justify-between sm:col-span-2 md:col-span-1">
                <div>
                    <p class="text-[10px] font-bold text-slate-400 tracking-widest uppercase mb-1">Total Langkah Konten</p>
                    <h3 class="text-2xl sm:text-3xl font-black text-slate-900">{{ $stats['total_langkah'] }}</h3>
                </div>
                <div class="w-10 h-10 sm:w-12 sm:h-12 bg-slate-50 text-slate-600 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                </div>
            </div>
        </div>

        {{-- (Section) Tabel Topik - RESPONSIVE PORTRAIT --}}
        <div class="admin-card overflow-hidden shadow-sm border-none md:border md:border-slate-200">
            <table class="w-full text-left border-collapse block md:table">
                <thead class="table-header hidden md:table-header-group">
                    <tr>
                        <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">Nama Topik Modul</th>
                        <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">Jumlah Modul</th>
                        <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">Manajemen</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 block md:table-row-group">
                    @forelse($categories as $cat)
                    <tr class="hover:bg-slate-50 transition-colors group block md:table-row p-5 md:p-0 mb-4 md:mb-0 bg-white md:bg-transparent rounded-2xl md:rounded-none border border-slate-100 md:border-none shadow-sm md:shadow-none">
                        
                        {{-- Identitas Topik --}}
                        <td class="block md:table-cell py-2 md:py-6 md:px-8">
                            <div class="flex flex-col">
                                <span class="text-base font-bold text-slate-800 tracking-tight capitalize leading-tight">{{ $cat->name }}</span>
                                <span class="text-[10px] text-slate-400 font-bold mt-1 tracking-widest uppercase">Kurikulum Virtual Lab</span>
                            </div>
                        </td>

                        {{-- Jumlah Modul --}}
                        <td class="block md:table-cell py-3 md:py-6 md:px-8 md:text-center">
                            <div class="flex md:contents items-center justify-between">
                                <span class="md:hidden text-[10px] font-bold text-slate-400 uppercase tracking-widest">Total</span>
                                <div class="inline-flex items-center px-4 py-1.5 bg-blue-50 text-blue-700 border border-blue-100 rounded-xl">
                                    <span class="font-black text-sm mr-1.5">{{ $cat->materials_count }}</span>
                                    <span class="text-[8px] font-bold uppercase tracking-tighter">Modul</span>
                                </div>
                            </div>
                        </td>

                        {{-- Manajemen --}}
                        <td class="block md:table-cell py-4 md:py-6 md:px-8 md:text-right border-t border-slate-50 md:border-none mt-4 md:mt-0">
                            <div class="flex items-center justify-between md:justify-end md:space-x-3">
                                <span class="md:hidden text-[10px] font-bold text-slate-400 uppercase tracking-widest">Opsi Kelola</span>
                                <div class="flex items-center space-x-2 sm:space-x-3">
                                    {{-- Tombol KELOLA (Warna Biru) --}}
                                    <a href="{{ route('admin.materials.topic', $cat->id) }}" class="inline-flex px-4 py-2 sm:px-5 sm:py-2.5 bg-blue-600 text-white text-[9px] sm:text-[10px] font-bold rounded-xl hover:bg-blue-700 transition-all shadow-md shadow-blue-50 uppercase tracking-widest">
                                        Kelola
                                    </a>
                                    <button @click="openTopicModal = true; editMode = true; topicData = { id: '{{ $cat->id }}', name: '{{ $cat->name }}', description: '{{ $cat->description }}' }" 
                                            class="p-2 text-slate-400 hover:text-blue-600 transition-colors">
                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    </button>
                                    <form action="{{ route('admin.materials.destroy-topic', $cat->id) }}" method="POST" class="inline" onsubmit="return confirm('Hapus folder beserta isinya?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="p-2 text-slate-400 hover:text-red-600 transition-colors">
                                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr class="block md:table-row">
                        <td colspan="3" class="px-8 py-24 text-center block md:table-cell">
                            <p class="text-[10px] text-slate-300 font-black uppercase tracking-[0.3em]">Belum ada topik materi terdaftar</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- (Section) Modal: CRUD Folder --}}
        <div x-show="openTopicModal" x-cloak class="fixed inset-0 z-[150] flex items-center justify-center p-4">
            <div @click="openTopicModal = false" class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity"></div>
            <div class="bg-white rounded-[2.5rem] shadow-2xl z-[160] w-full max-w-lg overflow-hidden transform transition-all border border-slate-100 p-8 sm:p-12">
                <div class="flex items-center justify-between mb-8">
                    <h3 class="text-xl font-black text-slate-900 tracking-tight" x-text="editMode ? 'Ubah Informasi Topik' : 'Tambah Topik Modul Baru'"></h3>
                    <button @click="openTopicModal = false" class="text-slate-400 hover:text-slate-600 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <form :action="editMode ? '/admin/materials/topic/' + topicData.id : '/admin/materials/topic'" method="POST" class="space-y-6">
                    @csrf
                    <template x-if="editMode">
                        <input type="hidden" name="_method" value="PATCH">
                    </template>
                    
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2 ml-1">Nama Topik Modul</label>
                        <input type="text" name="name" x-model="topicData.name" class="form-input-premium" placeholder="Misal: Fungsi Logika Dasar" required>
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2 ml-1">Deskripsi Singkat</label>
                        <textarea name="description" x-model="topicData.description" rows="3" class="form-input-premium" placeholder="Jelaskan cakupan materi topik ini..." required></textarea>
                    </div>

                    <div class="flex space-x-3 pt-4">
                        <button type="button" @click="openTopicModal = false" class="flex-1 py-4 bg-slate-100 text-slate-500 font-bold rounded-2xl uppercase tracking-widest text-[10px]">Batal</button>
                        <button type="submit" class="flex-1 py-4 bg-blue-600 text-white font-bold rounded-2xl shadow-lg uppercase tracking-widest text-[10px]" x-text="editMode ? 'Simpan Perubahan' : 'Simpan Topik'"></button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</x-app-layout>