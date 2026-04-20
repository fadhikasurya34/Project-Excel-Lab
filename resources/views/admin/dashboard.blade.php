{{-- //* (View) Dashboard Utama Admin --}}
{{-- 
    DATA: $stats (Summary), $topStudents (Leaderboard), $chartData (XP Growth)
    LOGIC: Integrasi Chart.js untuk visualisasi data aktivitas lab.
--}}

<x-app-layout>
    {{-- (Asset) Chart.js untuk rendering grafik analitik --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        {{-- (Style) Standarisasi UI Terminal: Radial Grid & Kartu Elevasi --}}
        .bg-admin {
            background-color: #f8fafc;
            background-image: radial-gradient(#e2e8f0 0.8px, transparent 0.8px);
            background-size: 32px 32px;
        }

        .admin-card {
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 1.25rem;
            transition: all 0.3s ease;
        }
        .admin-card:hover {
            border-color: #6366f1; 
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.04);
            transform: translateY(-2px);
        }

        .nav-card {
            background: white;
            border: 1px solid #f1f5f9;
            border-radius: 1rem;
            padding: 1.5rem;
            transition: all 0.2s ease;
            cursor: pointer;
        }
        .nav-card:hover { background: #f8fafc; border-color: #6366f1; }
        .text-header { letter-spacing: -0.02em; }
    </style>

    <div class="min-h-screen bg-admin p-6 sm:p-10">
        
        {{-- (Section) Header: Status Koneksi & Profil Petugas --}}
        <div class="flex flex-col md:flex-row md:items-center justify-between mb-12 gap-6">
            <div>
                <div class="flex items-center space-x-2 mb-2">
                    <span class="px-2 py-0.5 bg-indigo-50 text-indigo-700 rounded text-[10px] font-bold tracking-widest uppercase">Terminal Kendali</span>
                    <div class="w-1.5 h-1.5 bg-emerald-500 rounded-full shadow-[0_0_8px_rgba(16,185,129,0.6)] animate-pulse"></div>
                </div>
                <h1 class="text-3xl font-extrabold text-slate-900 text-header">Laporan <span class="text-indigo-600">Aktivitas Lab</span></h1>
                <p class="text-slate-500 font-medium text-sm mt-1">Pantau perkembangan pengerjaan praktikan secara aktual.</p>
            </div>
            
            <div class="flex items-center bg-white p-2 rounded-2xl shadow-sm border border-slate-200 pr-6">
                <div class="w-10 h-10 rounded-xl bg-slate-900 flex items-center justify-center text-white font-bold shadow-md uppercase">
                    {{ substr(Auth::user()->name, 0, 1) }}
                </div>
                <div class="ml-3">
                    <p class="text-[9px] text-slate-400 font-black uppercase leading-none">Petugas Lab</p>
                    <p class="text-xs font-bold text-slate-700 mt-1 capitalize">{{ Auth::user()->name }}</p>
                </div>
            </div>
        </div>

        {{-- (Section) Row 1: Key Performance Indicators (Statistik Ringkas) --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
            {{-- Counter Siswa --}}
            <div class="admin-card p-6">
                <p class="text-[10px] font-bold text-slate-400 tracking-widest uppercase mb-4">Total Praktikan</p>
                <div class="flex items-end justify-between">
                    <h3 class="text-4xl font-black text-slate-900">{{ number_format($stats['total_siswa']) }}</h3>
                    <div class="p-2 bg-indigo-50 text-indigo-600 rounded-lg">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197"></path></svg>
                    </div>
                </div>
            </div>

            {{-- Average XP & Max Possible XP --}}
            <div class="admin-card p-6 border-l-4 border-l-emerald-500">
                <p class="text-[10px] font-bold text-slate-400 tracking-widest uppercase mb-4">Rerata Skor (XP)</p>
                <div class="flex items-end justify-between">
                    <div>
                        <h3 class="text-4xl font-black text-slate-900">{{ number_format($stats['avg_xp']) }}</h3>
                        <p class="text-[10px] font-bold text-emerald-500 uppercase tracking-widest mt-1">/ {{ number_format($stats['max_possible_xp']) }} MAX</p>
                    </div>
                    <div class="p-2 bg-emerald-50 text-emerald-600 rounded-lg">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                    </div>
                </div>
            </div>

            {{-- Mission Completion & Global Rate --}}
            <div class="admin-card p-6 border-l-4 border-l-blue-500">
                <p class="text-[10px] font-bold text-slate-400 tracking-widest uppercase mb-4">Total Penyelesaian</p>
                <div class="flex items-end justify-between">
                    <div>
                        <h3 class="text-4xl font-black text-slate-900">{{ number_format($stats['misi_selesai']) }}<span class="text-xl text-slate-400 font-bold ml-1">x</span></h3>
                        <p class="text-[10px] font-bold text-blue-500 uppercase tracking-widest mt-1">
                            DARI {{ number_format($stats['misi_keseluruhan']) }} MODUL ({{ $stats['completion_rate'] }}%)
                        </p>
                    </div>
                    <div class="p-2 bg-blue-50 text-blue-600 rounded-lg">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                </div>
            </div>

            {{-- Ticket Usage/Remedial --}}
            <div class="admin-card p-6 border-l-4 border-l-amber-500">
                <p class="text-[10px] font-bold text-slate-400 tracking-widest uppercase mb-4">Total Remedial</p>
                <div class="flex items-end justify-between">
                    <h3 class="text-4xl font-black text-slate-900">{{ number_format($stats['remedial']) }}</h3>
                    <div class="p-2 bg-amber-50 text-amber-600 rounded-lg">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"></path></svg>
                    </div>
                </div>
            </div>
        </div>

        {{-- (Section) Row 2: Visual Analitik & Leaderboard --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-10">
            {{-- Grafik XP Pertumbuhan --}}
            <div class="lg:col-span-2 bg-white p-8 rounded-2xl border border-slate-200 shadow-sm">
                <div class="mb-8">
                    <h2 class="text-lg font-bold text-slate-800 tracking-tight">Grafik Pertumbuhan XP</h2>
                    <p class="text-xs text-slate-400 font-medium mt-0.5">Akumulasi poin harian seluruh praktikan (7 hari terakhir).</p>
                </div>
                <div class="h-[320px] w-full">
                    <canvas id="activityChart"></canvas>
                </div>
            </div>

            {{-- Daftar Top Siswa --}}
            <div class="bg-white p-8 rounded-2xl border border-slate-200 shadow-sm">
                <h2 class="text-lg font-bold text-slate-800 tracking-tight mb-6">Top Performa</h2>
                <div class="space-y-5">
                @forelse($topStudents as $index => $ranking)
                    <div class="flex items-center justify-between pb-4 border-b border-slate-50 last:border-0 last:pb-0">
                        <div class="flex items-center space-x-4">
                            <div class="w-8 h-8 rounded bg-slate-900 text-white flex items-center justify-center text-[10px] font-bold">
                                {{ $index + 1 }}
                            </div>
                            <div class="min-w-0">
                                <p class="text-sm font-bold text-slate-700 capitalize leading-tight truncate w-32">
                                    {{ explode(' ', $ranking->user->name)[0] }}
                                </p>
                                <p class="text-[10px] font-medium text-slate-400 uppercase tracking-tight truncate">
                                    {{ $ranking->user->classrooms->first()->name ?? 'Tanpa Squad' }}
                                </p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-black text-indigo-600">{{ number_format($ranking->total_xp) }}</p>
                            <p class="text-[9px] font-bold text-slate-300 uppercase">XP</p>
                        </div>
                    </div>
                @empty
                    <div class="py-10 text-center text-slate-400 text-xs uppercase font-bold tracking-widest">Belum Ada Aktivitas</div>
                @endforelse
                </div>
                <a href="{{ route('admin.users.index') }}" class="block w-full mt-8 py-3 bg-slate-900 text-white rounded-xl text-center text-[10px] font-bold tracking-widest uppercase hover:bg-slate-800 transition-colors">
                    Manajemen Siswa
                </a>
            </div>
        </div>

        {{-- (Section) Row 3: Navigasi Cepat ke Engine Modul --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <a href="{{ route('admin.materials.index') }}" class="nav-card group flex items-center space-x-4">
                <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center group-hover:bg-blue-600 group-hover:text-white transition-all">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                </div>
                <div>
                    <h4 class="font-bold text-slate-900 text-sm">Editor Materi</h4>
                    <p class="text-[10px] text-slate-400 font-medium uppercase tracking-widest mt-1">Kelola Konten Belajar</p>
                </div>
            </a>

            <a href="{{ route('admin.missions.index') }}" class="nav-card group flex items-center space-x-4">
                <div class="w-12 h-12 bg-emerald-50 text-emerald-600 rounded-xl flex items-center justify-center group-hover:bg-emerald-600 group-hover:text-white transition-all">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                </div>
                <div>
                    <h4 class="font-bold text-slate-900 text-sm">Engine Misi</h4>
                    <p class="text-[10px] text-slate-400 font-medium uppercase tracking-widest mt-1">Logika & Simulasi Lab</p>
                </div>
            </a>

            <a href="{{ route('admin.classrooms.index') }}" class="nav-card group flex items-center space-x-4">
                <div class="w-12 h-12 bg-purple-50 text-purple-600 rounded-xl flex items-center justify-center group-hover:bg-purple-600 group-hover:text-white transition-all">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                </div>
                <div>
                    <h4 class="font-bold text-slate-900 text-sm">Squad Management</h4>
                    <p class="text-[10px] text-slate-400 font-medium uppercase tracking-widest mt-1">Kontrol Akses Kelas</p>
                </div>
            </a>
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

    {{-- (Logic) JavaScript: Engine Rendering Chart Pertumbuhan XP --}}
    <script>
        const ctx = document.getElementById('activityChart').getContext('2d');
        const rawData = @json($chartData ?? []);
        
        const labels = rawData.length ? rawData.map(d => d.date) : ['No Data'];
        const values = rawData.length ? rawData.map(d => d.total_xp) : [0];

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Perolehan XP Harian',
                    data: values,
                    borderColor: '#6366f1',
                    backgroundColor: 'rgba(99, 102, 241, 0.05)',
                    fill: true, tension: 0.4, borderWidth: 4, pointRadius: 4,
                    pointBackgroundColor: '#6366f1'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { 
                        beginAtZero: true, 
                        grid: { color: '#f1f5f9', borderDash: [4, 4] }
                    },
                    x: { grid: { display: false } }
                }
            }
        });
    </script>
</x-app-layout>