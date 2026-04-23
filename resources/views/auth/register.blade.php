<!DOCTYPE html>
<html lang="id" x-data="{ 
    darkMode: localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches),
    showPassword: false,
    toast: { 
        show: false, 
        type: 'error', 
        title: 'Pendaftaran Gagal', 
        message: 'Mohon periksa kembali data anda.' 
    },
    toggleTheme() { 
        this.darkMode = !this.darkMode; 
        localStorage.setItem('theme', this.darkMode ? 'dark' : 'light'); 
    },
    init() {
        @if ($errors->any())
            this.toast.show = true;
            this.toast.message = '{{ $errors->first() }}';
            setTimeout(() => { this.toast.show = false }, 5000);
        @endif
    }
}" :class="{'dark': darkMode}" class="h-full antialiased">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Emerald Terminal - Daftar</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: { sans: ['Plus Jakarta Sans', 'sans-serif'] },
                    colors: { 
                        excel: { light: '#10b981', dark: '#059669', deep: '#035a41' } 
                    }
                }
            }
        }
    </script>

    <style>
        [x-cloak] { display: none !important; }
        body { transition: background-color 0.5s ease; font-family: 'Plus Jakarta Sans', sans-serif; }

        .bg-lab {
            background-color: #f0fdf4;
            background-image: radial-gradient(#cbd5e1 2px, transparent 2px);
            background-size: 24px 24px;
        }
        .dark .bg-lab { 
            background-color: #060a0f; 
            background-image: radial-gradient(#064e3b 1px, transparent 1px); 
        }

        /* MEKANIK DEEP BUTTON */
        .btn-excel { transition: all 0.1s cubic-bezier(0.4, 0, 0.2, 1); border-bottom: 5px solid #035a41; }
        .btn-excel:active { transform: translateY(3px) scale(0.98); border-bottom-width: 1px; }
        .btn-secondary { border-bottom-color: #94a3b8; }
        .dark .btn-secondary { border-bottom-color: #1e293b; }

        /* INPUT & DARK MODE TEXT FIX */
        .input-lab { background: #f8fafc; border: 2px solid #e2e8f0; color: #0f172a; transition: all 0.2s; }
        .dark .input-lab { background: #0d1117; border-color: #1e293b; color: #ffffff !important; }
        .input-lab:focus { border-color: #10b981; background: white; box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.15); }
        .dark .input-lab:focus { background: #0d1117; border-color: #10b981; }

        .card-emerald { border-bottom: 8px solid #10b981; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.4); }

        /* TOAST GAGAL */
        .toast-top {
            position: fixed; top: 1rem; left: 50%; transform: translateX(-50%);
            z-index: 1000; animation: toast-down 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }
        @keyframes toast-down { from { transform: translate(-50%, -100%) scale(0.8); opacity: 0; } to { transform: translate(-50%, 0) scale(1); opacity: 1; } }

        .wrapper { min-height: 100dvh; display: flex; align-items: center; justify-content: center; padding: 1rem; position: relative; z-index: 10; }

        /* --- MODE 1: MOBILE VERTICAL (Default) --- */
        .main-container { display: flex; flex-direction: column; width: 100%; max-width: 340px; margin: auto; background: white; border-radius: 2.5rem; overflow: hidden; }
        .panel-logo { padding: 1.5rem; }
        .panel-form { padding: 2rem 1.5rem; }
        .btn-group { display: flex; gap: 0.75rem; width: 100%; }
        .btn-group > * { flex: 1; }

        /* --- MODE 2: MOBILE HORIZONTAL (Landscape HP) --- */
        @media (orientation: landscape) and (max-height: 500px) {
            .main-container { flex-direction: row !important; max-width: 600px !important; } 
            .panel-logo { width: 30% !important; padding: 1rem !important; border-right: 2px solid #e2e8f0; border-bottom: 0 !important; }
            .panel-form { width: 70% !important; padding: 1rem 1.25rem !important; }
            
            /* Rampingkan form isian */
            .form-content { max-width: 320px !important; margin: 0 auto; } 
            .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem; }
            .form-full { grid-column: span 2; }
            
            .logo-box { width: 3.5rem !important; height: 3.5rem !important; }
            .brand-title { font-size: 1.15rem !important; }
            .form-header { margin-bottom: 0.5rem !important; }
            .form-header h1 { font-size: 1.4rem !important; }
            .form-header p { display: none; }
            
            .input-lab { padding: 0.45rem 0.75rem !important; font-size: 0.75rem !important; border-radius: 10px !important; }
            .space-y-4 > :not([hidden]) ~ :not([hidden]) { margin-top: 0.45rem !important; }
            
            .btn-group { width: 100% !important; display: flex !important; margin-top: 0.5rem; }
            .btn-excel { padding: 0.4rem !important; font-size: 0.75rem !important; }
        }

        /* --- MODE 3: WINDOWS/DESKTOP --- */
        @media (min-width: 1024px) {
            .main-container { flex-direction: row; max-width: 820px; }
            .panel-logo { width: 35%; padding: 3rem; border-right: 2px solid #f1f5f9; }
            .panel-form { width: 65%; padding: 3rem 4rem; }
            .logo-box { width: 10rem; height: 10rem; }
            .form-content { max-width: 420px; margin: 0 auto; }
            .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
            .form-full { grid-column: span 2; }
        }
    </style>
</head>
<body class="bg-lab antialiased">

    <div x-show="toast.show" x-cloak class="toast-top bg-white dark:bg-slate-800 border-2 border-red-500 border-bottom-[6px] border-b-red-700 rounded-2xl p-3 shadow-2xl flex items-center space-x-3 min-w-[280px]">
        <div class="w-10 h-10 bg-red-50 dark:bg-red-900/30 rounded-xl flex items-center justify-center shrink-0">
            <img src="{{ asset('images/alert.png') }}" class="w-6 h-6 object-contain" alt="Alert">
        </div>
        <div class="flex-1">
            <p class="text-[10px] font-black text-slate-900 dark:text-white uppercase tracking-tighter leading-none" x-text="toast.title"></p>
            <p class="text-[11px] font-bold text-slate-500 dark:text-slate-400 mt-1 leading-tight" x-text="toast.message"></p>
        </div>
        <button @click="toast.show = false" class="text-slate-400 hover:text-red-500 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
        </button>
    </div>

    <div class="wrapper">
        <div class="main-container card-emerald dark:bg-slate-900 shadow-2xl">
            
            <div class="panel-logo bg-emerald-50 dark:bg-emerald-950/20 flex flex-row md:flex-col items-center justify-center gap-4 dark:border-emerald-900/50">
                <div class="logo-box w-20 h-20 md:w-44 md:h-44 shrink-0 flex items-center justify-center relative">
                    <img src="{{ asset('images/excel.png') }}" class="max-w-full max-h-full object-contain absolute transition-opacity duration-300" :class="darkMode ? 'opacity-0' : 'opacity-100'">
                    <img src="{{ asset('images/excel 2.png') }}" class="max-w-full max-h-full object-contain absolute transition-opacity duration-300" :class="darkMode ? 'opacity-100' : 'opacity-0'">
                </div>
                <div class="text-left md:text-center leading-none">
                    <h2 class="brand-title font-extrabold text-2xl md:text-4xl tracking-tight text-emerald-600 dark:text-emerald-400 uppercase leading-none">VIRTUAL LAB</h2>
                    <div class="brand-badge mt-2 hidden md:inline-flex items-center px-2 py-0.5 bg-emerald-500 text-white rounded-full">
                        <span class="w-1.5 h-1.5 bg-white rounded-full animate-pulse mr-1.5"></span>
                        <span class="text-[8px] font-black tracking-widest uppercase">Akses Baru</span>
                    </div>
                </div>
            </div>

            <div class="panel-form flex flex-col justify-center">
                <div class="form-content w-full">
                    <div class="form-header mb-6">
                        <h1 class="text-2xl md:text-3xl font-black text-slate-900 dark:text-white uppercase leading-none tracking-tighter">DAFTAR</h1>
                        <p class="text-xs text-slate-400 dark:text-slate-500 font-medium mt-2">Buat akun praktikan untuk memulai misi lab.</p>
                    </div>

                    <form method="POST" action="{{ route('register') }}" class="space-y-4">
                        @csrf
                        <div class="form-grid space-y-4 md:space-y-0">
                            <div class="form-full space-y-1">
                                <label class="text-[10px] font-black text-slate-400 dark:text-slate-500 tracking-widest ml-1">Username</label>
                                <input type="text" name="name" required autofocus
                                       class="input-lab w-full px-4 py-3 rounded-xl text-sm font-bold focus:outline-none placeholder-slate-300 dark:placeholder-slate-700 shadow-inner" 
                                       placeholder="Nama Lengkap">
                            </div>

                            <div class="form-full space-y-1">
                                <label class="text-[10px] font-black text-slate-400 dark:text-slate-500 tracking-widest ml-1">Email</label>
                                <input type="email" name="email" required
                                       class="input-lab w-full px-4 py-3 rounded-xl text-sm font-bold focus:outline-none placeholder-slate-300 dark:placeholder-slate-700 shadow-inner" 
                                       placeholder="username@mail.com">
                            </div>

                            <div class="space-y-1">
                                <label class="text-[10px] font-black text-slate-400 dark:text-slate-500 tracking-widest ml-1">Password</label>
                                <input :type="showPassword ? 'text' : 'password'" name="password" required 
                                       class="input-lab w-full px-4 py-3 rounded-xl text-sm font-bold focus:outline-none placeholder-slate-300 dark:placeholder-slate-700 shadow-inner" 
                                       placeholder="••••••••">
                            </div>

                            <div class="space-y-1">
                                <label class="text-[10px] font-black text-slate-400 dark:text-slate-500 tracking-widest ml-1">Konfirmasi</label>
                                <input :type="showPassword ? 'text' : 'password'" name="password_confirmation" required 
                                       class="input-lab w-full px-4 py-3 rounded-xl text-sm font-bold focus:outline-none placeholder-slate-300 dark:placeholder-slate-700 shadow-inner" 
                                       placeholder="••••••••">
                            </div>
                        </div>

                        <div class="flex items-center px-2 pt-1">
                            <label class="flex items-center cursor-pointer group">
                                <input type="checkbox" @change="showPassword = !showPassword" class="sr-only peer">
                                <div class="w-3.5 h-3.5 border-2 border-slate-200 dark:border-slate-700 rounded bg-slate-50 dark:bg-slate-800 peer-checked:bg-emerald-500 peer-checked:border-emerald-500 transition-all flex items-center justify-center shadow-inner">
                                    <svg class="w-2.5 h-2.5 text-white opacity-0 peer-checked:opacity-100" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="4"><path d="M5 13l4 4L19 7"/></svg>
                                </div>
                                <span class="ml-2 text-[10px] font-bold text-slate-400 dark:text-slate-500 tracking-wider">Lihat sandi</span>
                            </label>
                        </div>

                        <div class="pt-2 md:pt-4 btn-group">
                            <button type="submit" class="btn-excel bg-emerald-600 hover:bg-emerald-500 text-white font-black text-xs md:text-sm py-2.5 rounded-xl shadow-lg shadow-emerald-500/20 uppercase tracking-tighter">
                                Buat akun
                            </button>
                            <a href="{{ route('login') }}" class="btn-excel btn-secondary bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-300 font-black text-xs md:text-sm py-2.5 rounded-xl text-center border-slate-300 dark:border-slate-700 uppercase tracking-tighter">
                                Masuk
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <button @click="toggleTheme()" class="fixed bottom-4 right-4 md:bottom-6 md:right-6 w-10 h-10 flex items-center justify-center rounded-xl bg-white dark:bg-slate-800 border-2 border-slate-100 dark:border-slate-700 shadow-xl z-50 hover:scale-110 active:scale-95 transition-transform duration-200">
        <svg x-show="!darkMode" class="w-5 h-5 text-slate-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
        <svg x-show="darkMode" x-cloak class="w-5 h-5 text-yellow-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
    </button>
</body>
</html>