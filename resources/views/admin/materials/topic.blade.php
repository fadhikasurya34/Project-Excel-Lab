{{-- 
    VIEW: Daftar Modul di Dalam Topik (Admin)
    DATA: $materials, $category, $allTopics, $topicData
    DESC: Tempat mengelola isi modul (Teori/Praktikum) untuk kategori tertentu.
--}}

<x-app-layout>
    <style>
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
            border-color: #4f46e5;
            background-color: #ffffff;
            box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.05);
        }

        .btn-blue-main {
            background-color: #4f46e5;
            color: white;
            font-weight: 800;
            border-radius: 0.875rem;
            box-shadow: 0 10px 15px -3px rgba(79, 70, 229, 0.2);
            transition: all 0.2s ease;
        }

        .text-header { letter-spacing: -0.02em; }
    </style>

    <div class="min-h-screen bg-admin p-4 sm:p-10">

        {{-- (Section) Header --}}
        <div class="mb-10 flex flex-col md:flex-row md:items-start justify-between gap-6">
            <div>
                <a href="{{ route('admin.materials.index') }}" class="group inline-flex items-center text-blue-600 font-bold text-[10px] tracking-widest uppercase hover:text-blue-800 transition-colors mb-3">
                    <svg class="w-4 h-4 mr-2 transform group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3">
                        <path d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Kembali ke topik utama
                </a>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 text-header tracking-tight capitalize">
                    Topik: <span class="text-blue-600">{{ $category }}</span>
                </h1>
                <p class="text-slate-500 font-medium text-xs sm:text-sm mt-1 max-w-2xl leading-relaxed">
                    {{ $topicData->description ?? 'Kelola daftar modul instruksional pada topik ini.' }}
                </p>
            </div>
            <button onclick="openAddModal()" class="btn-blue-main px-6 sm:px-8 py-3.5 sm:py-4 text-[10px] sm:text-[11px] tracking-[0.15em] flex items-center justify-center uppercase active:scale-95 w-full md:w-auto">
                <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path d="M12 4v16m8-8H4"></path></svg>
                Tambah modul baru
            </button>
        </div>

        {{-- (Section) Daftar Modul --}}
        <div class="admin-card overflow-hidden shadow-sm relative border-none md:border md:border-slate-200">
            <table class="w-full text-left border-collapse block md:table">
                <thead class="bg-slate-50/80 border-b border-slate-100 hidden md:table-header-group">
                    <tr>
                        <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">Nama Modul</th>
                        <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">Tipe</th>
                        <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 block md:table-row-group">
                    @forelse($materials as $m)
                        {{-- Logika filter: Jangan tampilkan modul shell/placeholder yang namanya sama persis dengan kategori --}}
                        @if($m->title !== $category)
                        <tr class="hover:bg-blue-50/30 transition-all group block md:table-row p-5 md:p-0 mb-4 md:mb-0 bg-white md:bg-transparent rounded-2xl border border-slate-100 md:border-none shadow-sm md:shadow-none">
                            <td class="block md:table-cell py-3 md:py-6 md:px-8">
                                <div class="flex flex-col">
                                    <span class="text-base font-bold text-slate-800 tracking-tight leading-tight capitalize">{{ $m->title }}</span>
                                    <span class="text-[10px] text-slate-400 font-medium mt-1 italic line-clamp-1">"{{ $m->description }}"</span>
                                </div>
                            </td>
                            <td class="block md:table-cell py-3 md:py-6 md:px-8 md:text-center">
                                <div class="flex md:block items-center justify-between">
                                    <span class="md:hidden text-[10px] font-bold text-slate-400 uppercase tracking-widest">Tipe Konten</span>
                                    <span class="inline-flex px-3 py-1 {{ $m->material_type == 'teori' ? 'bg-blue-50 text-blue-600' : 'bg-emerald-50 text-emerald-600' }} rounded-lg text-[9px] font-black uppercase tracking-widest border border-current opacity-70">
                                        {{ $m->material_type }}
                                    </span>
                                </div>
                            </td>
                            <td class="block md:table-cell py-4 md:py-6 md:px-8 text-right border-t border-slate-50 md:border-none mt-4 md:mt-0">
                                <div class="flex items-center justify-between md:justify-end space-x-4">
                                    <span class="md:hidden text-[10px] font-bold text-slate-400 uppercase tracking-widest">Manajemen</span>
                                    <div class="flex items-center space-x-2">
                                        <a href="{{ route('admin.materials.steps', $m->id) }}" class="inline-flex items-center px-5 py-2 bg-blue-600 text-white text-[10px] font-black rounded-xl hover:bg-blue-700 transition-all uppercase tracking-widest shadow-md shadow-blue-100">
                                            Editor
                                        </a>
                                        <button onclick='openEditModal(@json($m))' class="w-9 h-9 flex items-center justify-center text-slate-400 hover:text-blue-600 hover:bg-blue-50 rounded-xl transition-all">
                                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                        </button>
                                        <form action="{{ route('admin.materials.destroy', $m->id) }}" method="POST" class="m-0" onsubmit="return confirm('Hapus modul?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="w-9 h-9 flex items-center justify-center text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-xl transition-all">
                                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @endif
                    @empty
                        <tr class="block md:table-row">
                            <td colspan="3" class="px-8 py-24 text-center block md:table-cell">
                                <div class="w-16 h-16 bg-slate-50 rounded-2xl flex items-center justify-center mx-auto mb-4 border border-dashed border-slate-200">
                                    <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                                </div>
                                <p class="text-[10px] text-slate-400 font-black uppercase tracking-[0.3em]">Belum ada modul di topik ini</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- (Section) Modal: Tambah/Edit Modul --}}
    <div id="materialModal" class="fixed inset-0 z-[100] hidden overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-slate-950/40 backdrop-blur-sm transition-opacity" onclick="closeModal()"></div>
            <div class="bg-white rounded-[2rem] shadow-2xl z-50 w-full max-w-lg overflow-hidden transform transition-all border border-slate-100 p-8 sm:p-10">
                <h3 id="modalTitle" class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight mb-8">Tambah Modul</h3>
                <form id="materialForm" method="POST" class="space-y-6">
                    @csrf
                    <input type="hidden" name="category" value="{{ $category }}">
                    <div id="methodField"></div>
                    
                    <div class="space-y-5">
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2 ml-1">Judul Modul</label>
                            <input type="text" name="title" id="form_title" class="form-input-premium" placeholder="Misal: Cara Pakai VLOOKUP" required>
                        </div>
                        
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2 ml-1">Deskripsi Ringkas</label>
                            <textarea name="description" id="form_description" rows="3" class="form-input-premium" placeholder="Apa yang dipelajari di modul ini?" required></textarea>
                        </div>

                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2 ml-1">Tipe Konten</label>
                            <select name="material_type" id="form_type" class="form-input-premium appearance-none">
                                <option value="teori">Materi Teori (PDF/Video GDrive)</option>
                                <option value="praktikum">Simulasi Praktikum (Hotspot Klik)</option>
                            </select>
                        </div>

                        <div id="moveTopicContainer" class="hidden">
                            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2 ml-1">Pindahkan ke Topik Lain</label>
                            <select name="target_category" id="form_target_category" class="form-input-premium appearance-none">
                                @foreach($allTopics as $t)
                                    <option value="{{ $t }}" {{ $t == $category ? 'selected' : '' }}>{{ $t }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="flex space-x-4 pt-4">
                        <button type="button" onclick="closeModal()" class="flex-1 py-4 bg-slate-100 text-slate-500 font-bold rounded-xl uppercase tracking-widest text-[10px]">Batal</button>
                        <button type="submit" class="flex-1 py-4 bg-blue-600 text-white font-bold rounded-xl shadow-lg uppercase tracking-widest text-[10px]">Simpan Modul</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openAddModal() {
            document.getElementById('modalTitle').innerText = "Tambah Modul Baru";
            document.getElementById('materialForm').action = "{{ route('admin.materials.store-quick') }}";
            document.getElementById('methodField').innerHTML = "";
            document.getElementById('form_title').value = "";
            document.getElementById('form_description').value = "";
            document.getElementById('moveTopicContainer').classList.add('hidden');
            document.getElementById('materialModal').classList.remove('hidden');
        }

        function openEditModal(m) {
            document.getElementById('modalTitle').innerText = "Ubah Metadata Modul";
            document.getElementById('materialForm').action = `/admin/materials/${m.id}`;
            document.getElementById('methodField').innerHTML = '@method("PATCH")';
            document.getElementById('form_title').value = m.title;
            document.getElementById('form_description').value = m.description;
            document.getElementById('form_type').value = m.material_type;
            document.getElementById('moveTopicContainer').classList.remove('hidden');
            document.getElementById('materialModal').classList.remove('hidden');
        }

        function closeModal() { 
            document.getElementById('materialModal').classList.add('hidden'); 
        }
    </script>
</x-app-layout>