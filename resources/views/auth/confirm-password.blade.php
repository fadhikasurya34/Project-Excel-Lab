<!DOCTYPE html>
<html lang="id" x-data="{ 
    darkMode: localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches),
    toggleTheme() { 
        this.darkMode = !this.darkMode; 
        localStorage.setItem('theme', this.darkMode ? 'dark' : 'light'); 
    } 
}" :class="{'dark': darkMode}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Konfirmasi Keamanan - Virtual Lab Excel</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: { sans: ['Plus Jakarta Sans', 'sans-serif'] },
                    borderRadius: { '25': '25px' },
                    colors: {
                        brand: {
                            light: '#60a5fa',
                            dark: '#3b82f6'
                        }
                    }
                }
            }
        }
    </script>

    <style>
        .smooth-layout { transition: all 0.5s ease-out; }
        [x-cloak] { display: none !important; }
        body { overflow: hidden; }

        input:-webkit-autofill {
            -webkit-text-fill-color: inherit !important;
            -webkit-box-shadow: 0 0 0px 1000px transparent inset !important;
            transition: background-color 5000s ease-in-out 0s;
        }

        .btn-3d {
            transition: all 0.1s;
            box-shadow: 0 4px 0 #2563eb;
        }
        .btn-3d:active {
            transform: translateY(3px);
            box-shadow: 0 1px 0 #2563eb;
        }

        .glow-effect {
            box-shadow: 0 0 30px rgba(96, 165, 250, 0.3);
        }

        .deep-container {
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25),
                        inset 0 1px 1px rgba(255, 255, 255, 0.1);
        }
        .dark .deep-container {
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.6),
                        inset 0 1px 0px rgba(255, 255, 255, 0.05);
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4 bg-slate-50 dark:bg-[#080c14] transition-colors duration-500 font-sans text-left text-slate-900 dark:text-white">

    <div class="smooth-layout relative flex flex-col md:flex-row items-center justify-center w-full max-w-[280px] md:max-w-[540px]">
        
        <div class="smooth-layout z-20 glow-effect
                    w-28 h-28 md:w-44 md:h-44 
                    bg-brand-light dark:bg-brand-dark rounded-25 shadow-2xl
                    flex items-center justify-center
                    mb-[-2.5rem] md:mb-0 md:mr-[-3.5rem] 
                    shrink-0 border-4 border-white dark:border-slate-800">
            
            <div class="relative w-16 h-16 md:w-28 md:h-28 bg-white/20 rounded-full border-2 border-white/30 flex items-center justify-center">
                <svg class="w-10 h-10 md:w-16 md:h-16 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                </svg>
            </div>
        </div>

        <div class="smooth-layout z-10 deep-container
                    bg-white dark:bg-slate-900 w-full 
                    p-5 pt-14 md:pt-8 md:pl-20 md:pr-7
                    rounded-25 border-2 border-slate-50 dark:border-slate-800/50
                    flex flex-col overflow-hidden relative">
            
            <div class="mb-4">
                <h2 class="text-brand-light dark:text-brand-light text-[8px] font-black uppercase tracking-[0.2em] leading-none">Security Area</h2>
                <h1 class="text-slate-900 dark:text-white text-base font-extrabold tracking-tight leading-tight mt-1">Konfirmasi Kata Sandi</h1>
            </div>

            <p class="text-[10px] text-slate-500 dark:text-slate-400 mb-5 leading-relaxed">
                Ini adalah area aman. Harap konfirmasi kata sandi Anda sebelum melanjutkan akses ke Virtual Lab.
            </p>

            <form method="POST" action="{{ route('password.confirm') }}" class="space-y-4">
                @csrf

                <div class="relative text-left group">
                    <input type="password" name="password" id="password" required autofocus autocomplete="current-password" placeholder=" " 
                        class="block w-full px-4 py-2 text-sm font-semibold text-slate-900 dark:text-white bg-transparent border-2 border-slate-100 dark:border-slate-800 rounded-25 appearance-none focus:outline-none focus:border-brand-light transition-all peer" />
                    <label for="password" class="absolute text-slate-400 dark:text-slate-500 text-[10px] font-bold duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] px-2 left-4 pointer-events-none 
                        peer-focus:text-brand-light 
                        peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 
                        peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 
                        peer-focus:bg-white dark:peer-focus:bg-slate-900
                        peer-[:not(:placeholder-shown)]:bg-white dark:peer-[:not(:placeholder-shown)]:bg-slate-900
                        peer-[:not(:placeholder-shown)]:top-2 peer-[:not(:placeholder-shown)]:scale-75 peer-[:not(:placeholder-shown)]:-translate-y-4 leading-none">
                        Kata Sandi
                    </label>
                    <x-input-error :messages="$errors->get('password')" class="mt-1" />
                </div>

                <div class="flex justify-end pt-1">
                    <button type="submit" 
                        class="btn-3d bg-brand-light text-white text-[10px] font-black px-8 py-3 rounded-25 uppercase tracking-widest leading-none">
                        Konfirmasi
                    </button>
                </div>
            </form>
        </div>
    </div>

    <button @click="toggleTheme()" class="fixed bottom-6 right-6 p-3.5 rounded-25 bg-white dark:bg-slate-800 shadow-xl border-2 border-slate-100 dark:border-slate-700 transition-all hover:scale-110 active:rotate-12 focus:outline-none z-50">
        <svg x-show="!darkMode" class="w-5 h-5 text-slate-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
        <svg x-show="darkMode" x-cloak class="w-5 h-5 text-yellow-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
    </button>

</body>
</html>