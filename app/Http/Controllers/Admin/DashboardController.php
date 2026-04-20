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
    /**
     * Menampilkan dashboard statistik, grafik aktivitas, dan ranking tertinggi.
     */
    public function index()
    {
        // 0. AUTO-HEALING: Sanitasi Data Residu
        $this->syncAllUserXp();

        // 1. Kalkulasi Batas Maksimal (Absolute Max)
        $absoluteMaxXp = Mission::sum('max_score');
        
        // 2. Total Misi Keseluruhan
        $totalMissionsAvailable = Mission::count();

        // 3. Rerata XP dari tabel yang sudah disanitasi
        $avgXp = ScoresAndRanking::avg('total_xp') ?? 0;

        // 4. Misi Selesai & Penggunaan Tiket
        $totalCompleted = Progress::where('status', 'completed')->count();
        $totalRemedialUsed = RetryTicket::sum('used_count');

        // 5. Completion Rate (Persentase Penyelesaian Global)
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

        // 6. Top Performa (Leaderboard Admin)
        $topStudents = ScoresAndRanking::with(['user.classrooms'])
            ->orderByDesc('total_xp')
            ->take(5)
            ->get();

        // 7. DATA GRAFIK: Agregasi perolehan XP harian dalam 7 hari terakhir
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

    /**
     * Helper: Rutinitas Self-Healing Database.
     */
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

        // Hapus ranking user yang progresnya sudah kosong/reset
        $userIdsWithProgress = $realScores->pluck('user_id')->toArray();
        ScoresAndRanking::whereNotIn('user_id', $userIdsWithProgress)->update(['total_xp' => 0]);
    }
}