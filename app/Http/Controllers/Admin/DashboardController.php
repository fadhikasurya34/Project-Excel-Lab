<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Material;
use App\Models\Mission;
use App\Models\MaterialActivity;
use App\Models\Progress;
use App\Models\RetryTicket;
use App\Models\ScoresAndRanking;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /** (View) Menampilkan dashboard statistik, grafik aktivitas, dan ranking tertinggi. */
    public function index()
    {
        // Sanitasi Data Residu (Auto-Healing)
        $this->syncAllUserXp();

        // Kalkulasi Batas Maksimal (Absolute Max)
        $absoluteMaxXp = Mission::sum('max_score');
        
        // Total Misi Keseluruhan
        $totalMissionsAvailable = Mission::count();

        // Rerata XP dari tabel yang sudah disanitasi
        $avgXp = ScoresAndRanking::avg('total_xp') ?? 0;

        // Misi Selesai & Penggunaan Tiket Remedial
        $totalCompleted = Progress::where('status', 'completed')->count();
        $totalRemedialUsed = RetryTicket::sum('used_count');

        // Completion Rate (Persentase Penyelesaian Global)
        $totalSiswa = User::where('role', 'siswa')->count();
        $maxPossibleCompletions = $totalSiswa * $totalMissionsAvailable;
        $completionRate = $maxPossibleCompletions > 0 
            ? round(($totalCompleted / $maxPossibleCompletions) * 100) 
            : 0;

        $stats = [
            'total_siswa'      => $totalSiswa,
            'total_materi'     => Material::count(),
            'total_misi'       => $totalMissionsAvailable,
            'total_langkah'    => MaterialActivity::count(),
            'avg_xp'           => round($avgXp),
            'max_possible_xp'  => $absoluteMaxXp, 
            'misi_selesai'     => $totalCompleted,
            'misi_keseluruhan' => $totalMissionsAvailable, 
            'completion_rate'  => $completionRate,
            'remedial'         => $totalRemedialUsed,
        ];

        // Top Performa (Leaderboard Admin)
        $topStudents = ScoresAndRanking::with(['user.classrooms'])
            ->orderByDesc('total_xp')
            ->take(5)
            ->get();

        // Data Grafik: Agregasi perolehan XP harian dalam 7 hari terakhir
        $chartData = Progress::where('status', 'completed')
            ->where('completion_time', '>=', Carbon::now()->subDays(6))
            ->select(
                DB::raw('DATE(completion_time) as date'),
                DB::raw('SUM(score) as daily_score')
            )
            ->groupBy('date')
            ->orderBy('date', 'ASC')
            ->get()
            ->map(fn($d) => [
                'date' => Carbon::parse($d->date)->format('d M'),
                'total_xp' => (int)$d->daily_score
            ]);

        return view('admin.dashboard', compact('stats', 'topStudents', 'chartData'));
    }

    /** (Helper) Rutinitas penyelarasan ulang data XP pengguna di database. */
    private function syncAllUserXp()
    {
        // Agregasi nilai absolut dari tabel progress
        $realScores = Progress::where('status', 'completed')
            ->select('user_id', DB::raw('SUM(score) as real_total'))
            ->groupBy('user_id')
            ->get();

        // Timpa (overwrite) tabel ranking dengan nilai asli
        foreach ($realScores as $score) {
            ScoresAndRanking::updateOrCreate(
                ['user_id' => $score->user_id],
                ['total_xp' => $score->real_total]
            );
        }

        // Hapus (reset) ranking user yang progresnya sudah kosong
        $userIdsWithProgress = $realScores->pluck('user_id')->toArray();
        ScoresAndRanking::whereNotIn('user_id', $userIdsWithProgress)->update(['total_xp' => 0]);
    }
}