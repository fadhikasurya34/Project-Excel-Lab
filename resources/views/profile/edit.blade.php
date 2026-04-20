{{-- //* (View) Pengaturan Profil Siswa - 1x1 Balanced Edition */ --}}

@extends('layouts.siswa')

@section('title', 'Pengaturan Profil')

@push('styles')
<style>
    /* //* (Visual) Identitas Kartu & Transisi Sesuai Referensi */
    .glass-card {
        transition: all 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        border-radius: 2.5rem;
    }
    
    /* //* (Themes) Glow Identitas Sesuai Dashboard */
    .card-blue:hover { border-color: #3b82f6 !important; box-shadow: 0 20px 40px -10px rgba(59, 130, 246, 0.3); }
    .card-emerald:hover { border-color: #10b981 !important; box-shadow: 0 20px 40px -10px rgba(16, 185, 129, 0.3); }
    .card-purple:hover { border-color: #a855f7 !important; box-shadow: 0 20px 40px -10px rgba(168, 85, 247, 0.3); }
    .card-red:hover { border-color: #ef4444 !important; box-shadow: 0 20px 40px -10px rgba(239, 68, 68, 0.3); }

    /* //* (Dekorasi) Siluet Teks Latar Belakang */
    .card-silhouette {
        position: absolute; top: -0.5rem; right: -0.5rem;
        font-family: 'Bangers', cursive; font-size: 5rem;
        line-height: 1; opacity: 0.05; transform: rotate(15deg);
        pointer-events: none; z-index: 0; color: #64748b; transition: all 0.3s ease; white-space: nowrap;
    }
    .glass-card:hover .card-silhouette { opacity: 0.12; transform: rotate(10deg) scale(1.05); }

    /* //* (UI) Mekanik Tombol Pegas 3D - STANDARD REFERENSI */
    .btn-menu-pegas {
        transition: all 0.1s ease;
        border-bottom-width: 6px;
        border-radius: 1rem; /* //* rounded-2xl */
        font-family: inherit;
    }
    .btn-menu-pegas:active {
        transform: translateY(4px);
        border-bottom-width: 2px;
    }

    .btn-back-pegas-6 {
        transition: all 0.1s ease;
        border-bottom-width: 6px !important;
    }
    .btn-back-pegas-6:active {
        transform: translateY(4px);
        border-bottom-width: 0px !important;
    }

    /* //* (Typography) Ukuran Teks Sesuai Skala Referensi */
    .text-title-card { font-size: 1.125rem; font-weight: 700; }
    @media (min-width: 1024px) { .text-title-card { font-size: 1.25rem; } }
    
    .text-desc-card { font-size: 11px; font-weight: 500; line-height: 1.625; }
    @media (min-width: 1024px) { .text-desc-card { font-size: 12px; } }

    /* //* (Forms) Input Styling */
    .glass-card input {
        border-radius: 1.25rem !important;
        border: 2px solid #e2e8f0 !important;
        background-color: #f8fafc !important;
        padding: 0.75rem 1.25rem !important;
        font-weight: 700 !important;
        font-size: 0.875rem !important;
    }
    .dark .glass-card input { background-color: rgba(30, 41, 59, 0.5) !important; border-color: #334155 !important; color: white !important; }

    /* //* (Security) Eye Toggle Precision */
    .password-wrapper { position: relative; width: 100%; display: flex; align-items: center; }
    .eye-toggle {
        position: absolute; right: 1rem; top: 50%; transform: translateY(-40%);
        z-index: 20; color: #94a3b8; cursor: pointer; padding: 0.25rem;
    }

    /* //* (Option Box) Selection Styling */
    .option-box input[type="radio"] { display: none; }
    .option-box label {
        display: block; cursor: pointer; border-radius: 1.25rem; border: 4px solid transparent;
        background-color: #f8fafc; padding: 0.35rem; transition: all 0.4s;
    }
    .dark .option-box label { background-color: rgba(30, 41, 59, 0.5); }
    .option-box input[type="radio"]:checked + label {
        border-color: #3b82f6; background-color: #eff6ff; transform: scale(1.05);
    }
    .color-swatch { width: 100%; padding-bottom: 100%; border-radius: 0.75rem; }

    /* //* (Notification) Compact Top-Center Toast */
    .toast-top {
        position: fixed; top: 1.5rem; left: 50%; transform: translateX(-50%);
        z-index: 1000; background: white; border-radius: 1.5rem;
        border: 2px solid #6366f1; border-bottom: 6px solid #4f46e5;
        min-width: 260px; padding: 1rem 1.5rem; text-align: center;
        animation: toast-down 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }
    .dark .toast-top { background: #1e293b; }
    @keyframes toast-down { from { transform: translate(-50%, -100%) scale(0.8); opacity: 0; } to { transform: translate(-50%, 0) scale(1); opacity: 1; } }
</style>
@endpush

@section('header_left')
    {{-- //* (Nav) Back Button - Deep Color Matching Sidebar */ --}}
    <a href="{{ route('dashboard') }}" class="p-2 bg-white dark:bg-slate-800 border-2 border-slate-200 dark:border-slate-700 rounded-xl btn-back-pegas-6 text-slate-600 dark:text-slate-300 shadow-sm">
        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3.5"><path d="M15 19l-7-7 7-7" /></svg>
    </a>
    <div class="flex flex-col text-left ml-3 leading-none">
        <span class="text-base font-extrabold tracking-tight dark:text-white uppercase leading-none">Pengaturan</span>
        <div class="flex items-center space-x-1.5 mt-1.5">
            <span class="w-1.5 h-1.5 bg-blue-500 rounded-full animate-pulse"></span>
            <span class="text-[7px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest leading-none">Profil Akun</span>
        </div>
    </div>
@endsection

@section('content')
<div class="px-4 sm:px-10 py-8 flex flex-col items-center">
    
    {{-- //* (Notification) Compact Top Center Sync Message */ --}}
    @if(session('status'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)" x-transition.opacity class="toast-top shadow-xl flex items-center space-x-3">
            <div class="w-8 h-8 bg-indigo-50 dark:bg-indigo-900/30 rounded-xl flex items-center justify-center text-xl animate-pulse">✨</div>
            <div class="text-left">
                <p class="text-[10px] font-black text-slate-900 dark:text-white uppercase tracking-tight leading-none">Berhasil!</p>
                <p class="text-[9px] font-bold text-slate-500 dark:text-slate-400 mt-1">Data telah disinkronkan.</p>
            </div>
        </div>
    @endif

    {{-- //* (Container) 1x1 Vertical Layout - Adaptive Column */ --}}
    <div class="w-full max-w-md sm:max-w-2xl lg:max-w-3xl mx-auto space-y-6 lg:space-y-8 transition-all duration-500">
        
        <div class="mb-4 text-left px-2">
            <h1 class="text-2xl lg:text-2xl font-black text-slate-900 dark:text-white leading-tight">Konfigurasi Profil</h1>
            <p class="text-sm lg:text-sm text-slate-500 dark:text-slate-400 font-medium mt-1">Kelola identitas karakter dan parameter keamanan laboratoriamu.</p>
        </div>

        {{-- //* (Card 1) Karakter & Visual */ --}}
        <div class="glass-card card-blue bg-white dark:bg-slate-900 border-2 border-slate-200 dark:border-slate-800 p-6 lg:p-8 border-b-[8px] relative overflow-hidden shadow-sm hover:-translate-y-2 active:scale-[0.99]">
            <div class="card-silhouette">CHAR</div>
            <div class="flex flex-col items-start gap-5 z-10 w-full relative">
                <div class="w-12 h-12 lg:w-14 lg:h-14 bg-blue-50 dark:bg-blue-900/20 rounded-2xl flex items-center justify-center text-2xl lg:text-3xl shadow-inner border border-blue-100 dark:border-blue-800">🤖</div>
                <div class="w-full">
                    <h3 class="text-title-card text-slate-900 dark:text-white capitalize leading-tight">Visual Karakter</h3>
                    <p class="text-desc-card text-slate-500 dark:text-slate-400 mt-2">Kustomisasi avatar robot dan aura profil laboratoriamu.</p>
                </div>

                <form method="post" action="{{ route('profile.update') }}" class="w-full space-y-6 mt-2">
                    @csrf @method('patch')
                    @php
                        $avatars = ['Felix', 'Aneka', 'Oliver', 'Jasper', 'Luna', 'Zeus', 'Mimi', 'Buster'];
                        $colors = ['10b981', '3b82f6', 'f59e0b', 'a855f7', 'f43f5e', '0ea5e9'];
                    @endphp
                    <div>
                        <label class="text-[8px] font-black text-slate-400 uppercase tracking-[0.2em] block mb-2">Avatar</label>
                        <div class="grid grid-cols-4 md:grid-cols-8 gap-2">
                            @foreach($avatars as $avatar)
                                <div class="option-box">
                                    <input type="radio" name="avatar" id="avatar_{{ $avatar }}" value="{{ $avatar }}" {{ Auth::user()->avatar == $avatar ? 'checked' : '' }}>
                                    <label for="avatar_{{ $avatar }}"><img src="https://api.dicebear.com/9.x/bottts/svg?seed={{ $avatar }}&backgroundColor=transparent" class="w-full h-auto"></label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div>
                        <label class="text-[8px] font-black text-slate-400 uppercase tracking-[0.2em] block mb-2">Aura Warna</label>
                        <div class="grid grid-cols-6 gap-2">
                            @foreach($colors as $hex)
                                <div class="option-box">
                                    <input type="radio" name="profile_color" id="color_{{ $hex }}" value="{{ $hex }}" {{ Auth::user()->profile_color == $hex ? 'checked' : '' }}>
                                    <label for="color_{{ $hex }}"><div class="color-swatch" style="background-color: #{{ $hex }};"></div></label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <input type="hidden" name="name" value="{{ Auth::user()->name }}">
                    <input type="hidden" name="email" value="{{ Auth::user()->email }}">
                    <button type="submit" class="btn-menu-pegas w-full py-3.5 bg-blue-600 hover:bg-blue-700 text-white font-black text-[10px] tracking-widest uppercase border-blue-800 shadow-lg shadow-blue-100">Simpan Karakter</button>
                </form>
            </div>
        </div>

        {{-- //* (Card 2) Informasi Dasar */ --}}
        <div class="glass-card card-emerald bg-white dark:bg-slate-900 border-2 border-slate-200 dark:border-slate-800 p-6 lg:p-8 border-b-[8px] relative overflow-hidden shadow-sm hover:-translate-y-2 active:scale-[0.99]">
            <div class="card-silhouette">INFO</div>
            <div class="w-full z-10 relative">
                <div class="w-12 h-12 lg:w-14 lg:h-14 bg-emerald-50 dark:bg-emerald-900/20 rounded-2xl flex items-center justify-center text-2xl lg:text-3xl shadow-inner border border-emerald-100 dark:border-emerald-800 mb-5">📧</div>
                @include('profile.partials.update-profile-information-form')
            </div>
        </div>

        {{-- //* (Card 3) Keamanan Password */ --}}
        <div class="glass-card card-purple bg-white dark:bg-slate-900 border-2 border-slate-200 dark:border-slate-800 p-6 lg:p-8 border-b-[8px] relative overflow-hidden shadow-sm hover:-translate-y-2 active:scale-[0.99]">
            <div class="card-silhouette">PASS</div>
            <div class="w-full z-10 password-parent relative">
                <div class="w-12 h-12 lg:w-14 lg:h-14 bg-purple-50 dark:bg-purple-900/20 rounded-2xl flex items-center justify-center text-2xl lg:text-3xl shadow-inner border border-purple-100 dark:border-purple-800 mb-5">🔑</div>
                @include('profile.partials.update-password-form')
            </div>
        </div>

        {{-- //* (Card 4) Danger Zone */ --}}
        <div class="glass-card card-red bg-white dark:bg-slate-900 border-2 border-slate-200 dark:border-slate-800 p-6 lg:p-8 border-b-[8px] relative overflow-hidden shadow-sm hover:-translate-y-2 active:scale-[0.99]">
            <div class="card-silhouette">DEL</div>
            <div class="w-full z-10 relative">
                <div class="w-12 h-12 lg:w-14 lg:h-14 bg-red-50 dark:bg-red-900/20 rounded-2xl flex items-center justify-center text-2xl lg:text-3xl shadow-inner border border-red-100 dark:border-red-800 mb-5">⚠️</div>
                @include('profile.partials.delete-user-form')
            </div>
        </div>

        <footer class="py-10 border-t-2 border-slate-100 dark:border-slate-800 flex flex-col md:flex-row items-center justify-between opacity-50 gap-6">
            <div class="flex flex-col text-left">
                <span class="text-[11px] font-black text-slate-400 uppercase tracking-widest leading-none">© 2026 UNNES Informatics Education</span>
                <span class="text-[10px] font-bold text-slate-400 mt-1 capitalize">{{ Auth::user()->name }} - Settings Control</span>
            </div>
            <span class="text-[11px] font-black text-indigo-500 tracking-widest uppercase px-4 py-2 bg-indigo-50 dark:bg-indigo-950/30 rounded-full border border-indigo-100 dark:border-indigo-900/50">Terminal v1.2</span>
        </footer>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // //* (Logic) Fitur Mata Password Presisi */
        const passwordInputs = document.querySelectorAll('.password-parent input[type="password"]');
        passwordInputs.forEach(input => {
            const wrapper = document.createElement('div');
            wrapper.className = 'password-wrapper';
            input.parentNode.insertBefore(wrapper, input);
            wrapper.appendChild(input);

            const toggle = document.createElement('div');
            toggle.className = 'eye-toggle';
            toggle.innerHTML = `
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path class="eye-open" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path class="eye-open" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    <path class="eye-closed hidden" stroke-linecap="round" stroke-linejoin="round" d="M3 3l18 18m-5.357-1.643A9.953 9.953 0 0112 19c-4.478 0-8.268-2.943-9.542-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29" />
                </svg>`;
            wrapper.appendChild(toggle);

            toggle.addEventListener('click', () => {
                const isPass = input.type === 'password';
                input.type = isPass ? 'text' : 'password';
                toggle.querySelectorAll('.eye-open').forEach(el => el.classList.toggle('hidden', isPass));
                toggle.querySelector('.eye-closed').classList.toggle('hidden', !isPass);
            });
        });

        // //* (Logic) Apply Pegas Style to Partials Submit Buttons */
        const submitButtons = document.querySelectorAll('.glass-card button[type="submit"]');
        submitButtons.forEach(btn => {
            if(!btn.classList.contains('btn-menu-pegas')) {
                btn.classList.add('btn-menu-pegas');
                const card = btn.closest('.glass-card');
                if(card.classList.contains('card-emerald')) { btn.style.background = '#10b981'; btn.style.borderColor = '#059669'; }
                if(card.classList.contains('card-purple')) { btn.style.background = '#a855f7'; btn.style.borderColor = '#7c3aed'; }
                if(card.classList.contains('card-red')) { btn.style.background = '#ef4444'; btn.style.borderColor = '#dc2626'; }
                btn.style.color = '#fff';
            }
        });
    });
</script>
@endpush