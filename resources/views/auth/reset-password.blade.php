<!DOCTYPE html>
<html lang="id" x-data="{ 
    darkMode: localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches),
    showPassword: false,
    toast: { 
        show: false, 
        type: 'error', 
        icon: '⚠️', 
        title: 'Gagal Update', 
        message: '' 
    },
    toggleTheme() { 
        this.darkMode = !this.darkMode; 
        localStorage.setItem('theme', this.darkMode ? 'dark' : 'light'); 
    },
    init() {
        {{-- Munculkan toast jika ada error validasi --}}
        @if ($errors->any())
            this.toast.show = true;
            this.toast.title = 'Terjadi Kesalahan';
            this.toast.message = '{{ $errors->first() }}';
            setTimeout(() => { this.toast.show = false }, 5000);
        @endif

        {{-- Munculkan toast sukses jika ada status --}}
        @if (session('status'))
            this.toast.show = true;
            this.toast.type = 'success';
            this.toast.icon = '✅';
            this.toast.title = 'Berhasil';
            this.toast.message = '{{ session('status') }}';
            setTimeout(() => { this.toast.show = false }, 5000);
        @endif
    }
}" :class="{'dark': darkMode}" class="h-full antialiased">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Reset Password - Virtual Lab Excel</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Bangers&family=Plus+Jakarta+Sans:wght@600;700;800&display=swap" rel="stylesheet">
    
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: { sans: ['Plus Jakarta Sans', 'sans-serif'], game: ['Bangers', 'cursive'] },
                    colors: { excel: { light: '#10b981', dark: '#059669', deep: '#047857' } }
                }
            }
        }
    </script>

    <style>
        [x-cloak] { display: none !important; }
        body { transition: background-color 0.5s ease; }

        /* Background Dot Grid */
        .bg-lab {
            background-color: #f0fdf4;
            background-image: radial-gradient(#cbd5e1 2px, transparent 2px);
            background-size: 24px 24px;
        }
        .dark .bg-lab { 
            background-color: #060a0f; 
            background-image: radial-gradient(#064e3b 1px, transparent 1px); 
        }

        /* Tombol Pegas 3D */
        .btn-excel { transition: all 0.1s ease; border-bottom: 4px solid #047857; }
        .btn-excel:active { transform: translateY(2px); border-bottom-width: 1px; }

        .btn-secondary { border-bottom: 4px solid #94a3b8; }
        .dark .btn-secondary { border-bottom-color: #1e293b; }

        .card-emerald {
            border-bottom: 8px solid #10b981;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.4);
        }

        /* Toast Styling */
        .toast-top {
            position: fixed; top: 1.5rem; left: 50%; transform: translateX(-50%);
            z-index: 1000; background: white; border-radius: 1.5rem;
            border: 2px solid #ef4444; border-bottom: 6px solid #b91c1c;
            min-width: 280px; padding: 0.8rem 1.2rem;
            animation: toast-down 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }
        .dark .toast-top { background: #1e293b; border-color: #ef4444; border-bottom-color: #991b1b; }
        .toast-success { border-color: #10b981 !important; border-bottom-color: #047857 !important; }

        @keyframes toast-down { 
            from { transform: translate(-50%, -100%) scale(0.8); opacity: 0; } 
            to { transform: translate(-50%, 0) scale(1); opacity: 1; } 
        }

        .wrapper {
            min-height: 100dvh; display: flex; align-items: center; justify-content: center;
            padding: 1rem; position: relative; z-index: 10;
        }

        .main-container { display: flex; flex-direction: column; width: 100%; max-width: 340px; margin: auto; transition: all 0.3s ease; }

        /* LANDSCAPE OPTIMIZATION */
        @media (orientation: landscape) and (max-height: 500px) {
            .main-container { flex-direction: row !important; max-width: 600px !important; }
            .panel-logo { width: 35% !important; padding: 1rem !important; gap: 0.2rem !important; border-right-width: 2px !important; border-bottom-width: 0 !important; }
            .panel-form { width: 65% !important; padding: 1.2rem 1.5rem !important; }
            .logo-box { width: 4.5rem !important; height: 4.5rem !important; }
            .brand-title { font-size: 1.4rem !important; }
            .form-header h1 { font-size: 1.2rem !important; }
            .input-lab { padding: 0.45rem 0.8rem !important; font-size: 0.75rem !important; }
            .label-text { font-size: 0.65rem !important; }
            .btn-excel { padding: 0.5rem !important; font-size: 0.8rem !important; }
            .form-content { max-width: 300px; margin: 0 auto; }
        }

        @media (min-width: 768px) and (min-height: 501px) {
            .main-container { flex-direction: row; max-width: 780px; }
            .panel-logo { width: 40%; padding: 3rem; }
            .panel-form { width: 60%; padding: 3rem; }
            .logo-box { width: 10rem; height: 10rem; }
        }

        /* Dark Mode Input Fix */
        .input-lab { background: #f8fafc; border: 2px solid #e2e8f0; color: #0f172a; transition: all 0.2s; }
        .dark .input-lab { background: #020617; border-color: #1e293b; color: #ffffff !important; }
        .input-lab:focus { border-color: #10b981; background: white; box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.15); }
        .dark .input-lab:focus { background: #0f172a !important; color: #ffffff !important; }
    </style>
</head>
<body class="bg-lab antialiased">

    {{-- Notifikasi --}}
    <div x-show="toast.show" x-cloak 
         :class="toast.type === 'success' ? 'toast-success' : ''"
         class="toast-top shadow-xl flex items-center space-x-3">
        <div class="w-8 h-8 rounded-xl flex items-center justify-center text-lg animate-pulse" 
             :class="toast.type === 'success' ? 'bg-emerald-50 dark:bg-emerald-900/30' : 'bg-red-50 dark:bg-red-900/30'"
             x-text="toast.icon"></div>
        <div class="text-left flex-1">
            <p class="text-[10px] font-black text-slate-900 dark:text-white uppercase leading-none" x-text="toast.title"></p>
            <p class="text-[9px] font-bold text-slate-500 dark:text-slate-400 mt-1" x-text="toast.message"></p>
        </div>
        <button @click="toast.show = false" class="text-slate-300 hover:text-red-500"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
    </div>

    <div class="wrapper">
        <div class="main-container card-emerald bg-white dark:bg-slate-900 rounded-[2.5rem] overflow-hidden">
            
            {{-- PANEL 1: BRANDING --}}
            <div class="panel-logo bg-emerald-50 dark:bg-emerald-950/30 flex flex-row md:flex-col items-center justify-center gap-4 border-b-2 md:border-b-0 md:border-r-2 border-emerald-100 dark:border-emerald-800 p-6">
                <div class="logo-box w-20 h-20 md:w-44 md:h-44 shrink-0 flex items-center justify-center relative hover:scale-105 transition-transform">
                    <img src="{{ asset('images/Excel.png') }}" class="max-w-full max-h-full object-contain absolute transition-opacity duration-300" :class="darkMode ? 'opacity-0' : 'opacity-100'">
                    <img src="{{ asset('images/Excel 2.png') }}" class="max-w-full max-h-full object-contain absolute transition-opacity duration-300" :class="darkMode ? 'opacity-100' : 'opacity-0'">
                </div>
                <div class="text-left md:text-center">
                    <h2 class="brand-title font-game text-2xl md:text-4xl tracking-widest text-emerald-600 dark:text-emerald-400 uppercase leading-none">Virtual Lab</h2>
                    <div class="brand-badge mt-2 hidden md:inline-flex items-center px-2 py-0.5 bg-emerald-500 text-white rounded-full">
                        <span class="w-1.5 h-1.5 bg-white rounded-full animate-pulse mr-1.5"></span>
                        <span class="text-[8px] font-black uppercase tracking-widest">Reset Akses</span>
                    </div>
                </div>
            </div>

            {{-- PANEL 2: FORM --}}
            <div class="panel-form p-8 flex flex-col justify-center">
                <div class="form-content w-full">
                    <div class="form-header mb-6">
                        <h1 class="text-2xl md:text-3xl font-black text-slate-900 dark:text-white uppercase leading-none tracking-tighter">Sandi Baru</h1>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-2">Amankan Kembali Akun Anda</p>
                    </div>

                    <form method="POST" action="{{ route('password.store') }}" class="space-y-4">
                        @csrf
                        <input type="hidden" name="token" value="{{ $request->route('token') }}">

                        <div class="space-y-1">
                            <label class="label-text text-[9px] font-black text-slate-400 uppercase ml-1 tracking-widest">Konfirmasi Email</label>
                            <input type="email" name="email" value="{{ old('email', $request->email) }}" required readonly
                                   class="input-lab w-full px-5 py-3 rounded-2xl text-sm font-bold opacity-70 cursor-not-allowed">
                        </div>

                        <div class="space-y-1">
                            <label class="label-text text-[9px] font-black text-slate-400 uppercase ml-1 tracking-widest">Kata Sandi Baru</label>
                            <div class="relative">
                                <input :type="showPassword ? 'text' : 'password'" name="password" required autofocus
                                       class="input-lab w-full px-5 py-3 rounded-2xl text-sm font-bold focus:outline-none placeholder-slate-400 dark:placeholder-slate-600 shadow-inner" 
                                       placeholder="Minimal 8 karakter">
                                <button type="button" @click="showPassword = !showPassword" 
                                        class="absolute right-3 top-1/2 -translate-y-1/2 p-2 text-slate-300 hover:text-emerald-500">
                                    <svg x-show="!showPassword" class="w-4 h-4 md:w-5 md:h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                    <svg x-show="showPassword" x-cloak class="w-4 h-4 md:w-5 md:h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" /></svg>
                                </button>
                            </div>
                        </div>

                        <div class="space-y-1">
                            <label class="label-text text-[9px] font-black text-slate-400 uppercase ml-1 tracking-widest">Ulangi Sandi</label>
                            <input :type="showPassword ? 'text' : 'password'" name="password_confirmation" required 
                                   class="input-lab w-full px-5 py-3 rounded-2xl text-sm font-bold focus:outline-none placeholder-slate-400 dark:placeholder-slate-600 shadow-inner" 
                                   placeholder="Ketik ulang sandi baru">
                        </div>

                        {{-- Navigasi Tombol Ganda --}}
                        <div class="pt-4 flex flex-col sm:flex-row gap-3">
                            <button type="submit" 
                                    class="flex-[2] btn-excel bg-emerald-600 hover:bg-emerald-500 text-white font-game text-lg md:text-xl py-3 rounded-2xl tracking-[0.1em] shadow-lg shadow-emerald-500/20 uppercase">
                                UPDATE SANDI
                            </button>
                            <a href="{{ route('login') }}" 
                               class="flex-1 btn-excel btn-secondary bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-300 font-game text-lg md:text-xl py-3 rounded-2xl tracking-[0.1em] text-center border-slate-300 dark:border-slate-700 uppercase">
                                BATAL
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <button @click="toggleTheme()" 
            class="fixed bottom-4 right-4 md:bottom-6 md:right-6 w-11 h-11 flex items-center justify-center rounded-2xl bg-white dark:bg-slate-800 border-2 border-slate-100 dark:border-slate-700 shadow-xl z-50 hover:scale-110 active:scale-95 transition-transform">
        <svg x-show="!darkMode" class="w-5 h-5 text-slate-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
        <svg x-show="darkMode" x-cloak class="w-5 h-5 text-yellow-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
    </button>
</body>
</html>