{{-- 
    VIEW: Pengaturan Profil Admin
    DATA: Auth::user()
    DESC: Manajemen identitas resmi petugas lab dan pembaruan kredensial keamanan (Super Admin).
--}}

<x-app-layout>
    <style>
        {{-- (Style) Standarisasi UI Terminal: Latar Belakang & Kartu Elevasi Indigo --}}
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
            transition: all 0.2s ease;
        }
        .form-input-premium:focus {
            outline: none;
            border-color: #6366f1;
            background-color: #ffffff;
            box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.05);
        }

        .btn-indigo-main {
            background-color: #6366f1;
            color: white;
            font-weight: 800;
            border-radius: 0.875rem;
            box-shadow: 0 10px 15px -3px rgba(99, 102, 241, 0.2);
            transition: all 0.2s ease;
        }
        .btn-indigo-main:hover {
            background-color: #4f46e5;
            transform: translateY(-1px);
        }

        .text-header { letter-spacing: -0.02em; }
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
        
        [x-cloak] { display: none !important; }
    </style>

    <div class="min-h-screen bg-admin p-6 sm:p-10" x-data="{ tab: 'misi', search: '' }">
        <div class="max-w-7xl mx-auto">
            
            {{-- (Section) Header: Navigasi Kembali & Identitas Level Akses --}}
            <div class="flex flex-col md:flex-row md:items-center justify-between mb-10 gap-6">
                <div>
                    <a href="{{ route('admin.dashboard') }}" class="group flex items-center text-slate-400 hover:text-indigo-600 transition-colors mb-2 text-[10px] font-bold tracking-widest uppercase">
                        <svg class="w-4 h-4 mr-1 transform group-hover:-translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                            <path d="M15 19l-7-7 7-7"></path>
                        </svg>
                        Kembali ke dashboard
                    </a>
                    <h1 class="text-3xl font-extrabold text-slate-900 text-header tracking-tight">Akun <span class="text-indigo-600">Administrator</span></h1>
                    <p class="text-slate-500 font-medium text-sm mt-1">Konfigurasi kredensial dan keamanan akses pusat kendali.</p>
                </div>
                
                <div class="flex items-center bg-white p-2 rounded-2xl shadow-sm border border-slate-200 pr-6">
                    <div class="w-10 h-10 rounded-xl bg-slate-900 flex items-center justify-center text-white font-bold shadow-md">
                        {{ substr(Auth::user()->name, 0, 1) }}
                    </div>
                    <div class="ml-3">
                        <p class="text-[9px] text-slate-400 font-bold uppercase leading-none tracking-widest">Akses Level</p>
                        <p class="text-xs font-bold text-slate-700 mt-1 capitalize">Super Admin Panel</p>
                    </div>
                </div>
            </div>

            {{-- (Process) Notification Toast: Feedback visual setelah pembaruan data --}}
            @if(session('status'))
                <div x-data="{ show: true, progress: 100 }"
                    x-show="show"
                    x-init="let interval = setInterval(() => { progress -= 1; if(progress <= 0) { show = false; clearInterval(interval); } }, 30);"
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="translate-y-10 opacity-0 scale-90"
                    x-transition:enter-end="translate-y-0 opacity-100 scale-100"
                    class="fixed bottom-10 right-10 z-[200]">
                    
                    <div class="bg-slate-900 border border-indigo-500/30 p-5 rounded-2xl shadow-[0_20px_50px_rgba(99,102,241,0.2)] flex items-center space-x-4 min-w-[320px] overflow-hidden relative">
                        <div class="absolute -top-10 -right-10 w-24 h-24 bg-indigo-600/20 blur-3xl"></div>
                        
                        <div class="flex-shrink-0 w-10 h-10 bg-indigo-600 rounded-xl flex items-center justify-center shadow-lg shadow-indigo-500/40">
                            <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>

                        <div class="flex-1">
                            <p class="text-[10px] font-black text-indigo-400 uppercase tracking-[0.2em] leading-none mb-1">System Update</p>
                            <p class="text-sm font-bold text-white tracking-tight leading-tight">
                                {{ session('status') === 'profile-updated' ? 'Identitas berhasil disinkronkan' : 'Kredensial akses telah diperbarui' }}
                            </p>
                        </div>

                        <div class="absolute bottom-0 left-0 h-1 bg-indigo-600 transition-all ease-linear" :style="'width: ' + progress + '%'"></div>
                    </div>
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
                
                {{-- (Section) Kolom Kiri: Form Identitas & Kredensial Password --}}
                <div class="lg:col-span-8 space-y-8">
                    
                    {{-- Form Informasi Profil --}}
                    <div class="admin-card p-8 sm:p-10 border-t-4 border-t-indigo-600 shadow-sm">
                        <div class="flex items-center space-x-4 mb-8">
                            <div class="p-3 bg-indigo-50 text-indigo-600 rounded-2xl">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-slate-800 tracking-tight">Informasi Profil</h3>
                                <p class="text-xs text-slate-400 font-medium">Update data identitas resmi petugas lab.</p>
                            </div>
                        </div>

                        <form method="post" action="{{ route('profile.update') }}" class="space-y-6">
                            @csrf @method('patch')

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2 ml-1">Nama Lengkap</label>
                                    <input type="text" name="name" class="form-input-premium" value="{{ old('name', Auth::user()->name) }}" required>
                                </div>
                                <div>
                                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2 ml-1">Alamat Email</label>
                                    <input type="email" name="email" class="form-input-premium" value="{{ old('email', Auth::user()->email) }}" required>
                                </div>
                            </div>

                            <div class="flex items-center justify-end pt-6 border-t border-slate-50">
                                <button type="submit" class="btn-indigo-main px-8 py-3 text-[10px] uppercase tracking-widest">Simpan Perubahan</button>
                            </div>
                        </form>
                    </div>

                    {{-- Form Update Password --}}
                    <div class="admin-card p-8 sm:p-10 border-t-4 border-t-slate-900 shadow-sm">
                        <div class="flex items-center space-x-4 mb-8">
                            <div class="p-3 bg-slate-100 text-slate-600 rounded-2xl">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-slate-800 tracking-tight">Keamanan & Kredensial</h3>
                                <p class="text-xs text-slate-400 font-medium">Pastikan akses akun terlindungi dengan enkripsi terbaru.</p>
                            </div>
                        </div>

                        <form method="post" action="{{ route('password.update') }}" class="space-y-6">
                            @csrf @method('put')

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <div>
                                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2 ml-1">Password Saat Ini</label>
                                    <input type="password" name="current_password" class="form-input-premium">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2 ml-1">Password Baru</label>
                                    <input type="password" name="password" class="form-input-premium">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2 ml-1">Konfirmasi</label>
                                    <input type="password" name="password_confirmation" class="form-input-premium">
                                </div>
                            </div>

                            <div class="flex items-center justify-end pt-6 border-t border-slate-50">
                                <button type="submit" class="btn-indigo-main !bg-slate-900 px-8 py-3 text-[10px] uppercase tracking-widest hover:!bg-slate-800">Ganti Password</button>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- (Section) Kolom Kanan: Status Account & Security Info --}}
                <div class="lg:col-span-4 space-y-6">
                    <div class="admin-card p-8 flex flex-col items-center text-center shadow-sm">
                        <div class="w-24 h-24 rounded-[2.5rem] bg-indigo-600 flex items-center justify-center text-white text-3xl font-black shadow-xl shadow-indigo-100 mb-6">
                            {{ substr(Auth::user()->name, 0, 1) }}
                        </div>
                        <h2 class="text-xl font-black text-slate-800 leading-tight capitalize">{{ Auth::user()->name }}</h2>
                        <p class="text-[10px] font-bold text-indigo-500 uppercase tracking-[0.2em] mt-2">{{ Auth::user()->role }} Account</p>
                        
                        <div class="w-full h-px bg-slate-100 my-8"></div>
                        
                        <div class="w-full space-y-4 text-left">
                            <div class="flex justify-between items-center text-[10px] font-bold uppercase tracking-widest">
                                <span class="text-slate-400">Terdaftar Sejak</span>
                                <span class="text-slate-700">{{ Auth::user()->created_at->format('d M Y') }}</span>
                            </div>
                            <div class="flex justify-between items-center text-[10px] font-bold uppercase tracking-widest">
                                <span class="text-slate-400">Status Server</span>
                                <span class="text-emerald-500 flex items-center">
                                    <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full mr-2 animate-pulse"></span> Online
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="p-6 bg-slate-900 rounded-[2rem] text-white">
                        <p class="text-[9px] font-bold text-slate-400 uppercase tracking-[0.2em] mb-4">Peringatan Keamanan</p>
                        <p class="text-xs leading-relaxed text-slate-300 font-medium">
                            Panel admin ini menggunakan enkripsi <span class="text-indigo-400">Bcrypt 12-rounds</span>. Jangan bagikan email atau password Anda kepada siapapun termasuk petugas teknis UNNES.
                        </p>
                    </div>
                </div>
            </div>

            {{-- Footer Terminal --}}
            <footer class="mt-20 py-8 border-t border-slate-200 flex flex-col md:flex-row items-center justify-between opacity-50 px-2">
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">© 2026 UNNES Informatics Education</p>
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-tighter">Terminal Auth Engine v4.0</span>
            </footer>
        </div>
    </div>
</x-app-layout>