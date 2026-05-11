<?php

namespace App\Http\Controllers;

use App\Models\Mission;
use App\Models\Progress;
use App\Models\Level;
use App\Models\ScoresAndRanking;
use App\Models\RetryTicket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MisiController extends Controller
{
    /** * //* (View) Menampilkan menu utama daftar kategori materi 
     */
    public function index()
    {
        $categories = Level::where('level_order', '>', 0)
            ->select('category')
            ->distinct()
            ->get();
            
        return view('misi.index', compact('categories'));
    }

    /** * //* (View) Menampilkan peta level dan sisa tiket remedial harian 
     */
    public function showLevels(string $category) 
    {
        $levels = Level::where('category', $category)
            ->where('level_order', '>', 0) 
            ->with(['missions'])
            ->orderBy('level_order', 'asc')
            ->get();
        
        $userId = Auth::id();
        $today = now()->toDateString();

        $ticketRecord = RetryTicket::where('user_id', $userId)
            ->where('date', $today)
            ->first();

        $usedCount = $ticketRecord ? $ticketRecord->used_count : 0;
        $remainingTickets = max(0, 3 - $usedCount);

        $userProgress = Progress::where('user_id', $userId)
            ->where('status', 'completed')
            ->pluck('mission_id')
            ->toArray();

        return view('misi.levels', compact('levels', 'category', 'userProgress', 'remainingTickets'));
    }

    /** * //* (View) Menampilkan simulasi lab interaktif 
     */
    public function show(string $id)
    {
        $mission = Mission::with(['level', 'steps.hotspots' => function($q) {
            $q->orderBy('order', 'asc');
        }])->findOrFail($id);

        $stepsData = $mission->steps->map(function($step) {
            return [
                'id' => $step->id,
                'instruction' => $step->instruction,
                'image' => asset('storage/' . $step->step_image),
                'hotspots' => $step->hotspots 
            ];
        });

        $availableBlocks = collect([]);

        if ($mission->mission_type === 'Syntax Assembly') {
            preg_match_all('/(".+?")|[A-Z0-9\%]+|[\(\)\,\;\:\=\"\>\<\$\.\!\?\%]/', strtoupper($mission->key_answer), $matches);
            $blocks = $matches[0];
            $distractors = $mission->distractors ? explode(',', $mission->distractors) : [];
            
            $availableBlocks = collect(array_merge($blocks, $distractors))
                ->map(fn($b) => trim($b))
                ->filter(fn($b) => $b !== "" && $b !== null)
                ->shuffle() 
                ->values();
        }

        $view = ($mission->mission_type === 'Point & Click') ? 'misi.point_click' : 'misi.syntax_assembly';

        return view($view, [
            'mission' => $mission,
            'stepsData' => $stepsData,
            'availableBlocks' => $availableBlocks,
        ]);
    }

    /** * //* (Action) Proses penukaran tiket remedial 
     */
    public function retryMission(string $id)
    {
        $userId = Auth::id();
        $today = now()->toDateString();
        $maxDailyQuota = 3;

        $ticket = RetryTicket::firstOrCreate(
            ['user_id' => $userId, 'date' => $today],
            ['used_count' => 0]
        );

        if ($ticket->used_count >= $maxDailyQuota) {
            return back()->with('error', 'Yah, jatah tiket retry kamu hari ini sudah habis!');
        }

        return DB::transaction(function () use ($userId, $id, $ticket) {
            $mission = Mission::findOrFail($id);
            Progress::where('user_id', $userId)
                ->where('mission_id', $mission->id)
                ->update([
                    'status' => 'in_progress',
                    'attempts' => 0 
                ]);

            $ticket->increment('used_count');

            return redirect()->route('misi.show', $mission->id)
                ->with('success', 'Tiket berhasil ditukar! Silakan perbaiki skormu.');
        });
    }

    /** * //* (Process) Validasi jawaban siswa via AJAX 
     */
    public function checkAnswer(Request $request, string $id)
    {
        try {
            $mission = Mission::with('level')->findOrFail($id);
            $userId = Auth::id();

            $progress = Progress::firstOrCreate(
                ['user_id' => $userId, 'mission_id' => $mission->id],
                ['attempts' => 0, 'status' => 'in_progress']
            );

            if ($mission->mission_type === 'Point & Click') {
                $frontendAttempts = $request->attempts ?? 0;
                $finalScore = $this->calculateFinalScore((int)$mission->max_score, (int)$frontendAttempts);
                return $this->handleSuccess($mission, (float)$finalScore);
            }

            $userAnswer = $this->normalizeFormula($request->answer);
            $correctAnswer = $this->normalizeFormula($mission->key_answer);

            if ($userAnswer === $correctAnswer) {
                $finalScore = $this->calculateFinalScore((int)$mission->max_score, (int)$progress->attempts);
                return $this->handleSuccess($mission, (float)$finalScore);
            }

            $progress->attempts += 1;
            $progress->save();

            return response()->json([
                'status' => 'error', 
                'message' => $this->generateFeedback((string)$userAnswer, (string)$correctAnswer),
                'attempts' => $progress->attempts,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Sistem Error: ' . $e->getMessage() . ' (Baris ' . $e->getLine() . ')',
                'attempts' => 0
            ]);
        }
    }

    /** * //* (Helper) Kalkulasi skor dengan penalti 5% setelah percobaan ke-3 
     */
    private function calculateFinalScore(int $maxScore, int $attempts)
    {
        if ($attempts <= 3) return $maxScore;
        $penalty = ($attempts - 3) * ($maxScore * 0.05);
        return max($maxScore - $penalty, $maxScore * 0.4);
    }

    /** * //* (Action) KUNCI UTAMA: Penanganan sukses & Sinkronisasi XP absolut + Statistik
     */
    private function handleSuccess(Mission $mission, float $earnedScore) {
        return DB::transaction(function () use ($mission, $earnedScore) {
            $userId = Auth::id();
            $currentMax = $mission->max_score;

            // 1. Validasi skor agar tidak melebihi Max Score saat ini
            $validatedScore = min($earnedScore, $currentMax);

            // 2. Ambil progres lama untuk perbandingan High Score
            $oldProgress = Progress::where('user_id', $userId)
                ->where('mission_id', $mission->id)
                ->first();

            $oldScore = $oldProgress ? $oldProgress->score : 0;
            $finalHighScore = max($oldScore, $validatedScore);

            // 3. Update status pengerjaan
            Progress::updateOrCreate(
                ['user_id' => $userId, 'mission_id' => $mission->id],
                [
                    'status' => 'completed', 
                    'score' => $finalHighScore,
                    'completion_time' => now()
                ]
            );

            // 4. HITUNG ULANG STATISTIK UNTUK RANKING & SQUAD DETAIL
            
            // A. Total XP Akumulatif
            $totalXpNow = Progress::where('user_id', $userId)
                ->where('status', 'completed')
                ->sum('score');

            // B. Jumlah Misi Selesai
            $missionsCount = Progress::where('user_id', $userId)
                ->where('status', 'completed')
                ->count();

            // C. Jumlah Modul (Level) Selesai
            $modulesCount = Level::where('level_order', '>', 0)
                ->whereDoesntHave('missions', function($query) use ($userId) {
                    $query->whereNotIn('id', function($sub) use ($userId) {
                        $sub->select('mission_id')->from('progress')
                            ->where('user_id', $userId)
                            ->where('status', 'completed');
                    });
                })->count();

            // 5. Simpan ke Tabel ScoresAndRanking
            $scoreRecord = ScoresAndRanking::firstOrNew(['user_id' => $userId]);
            $scoreRecord->total_xp = $totalXpNow;
            $scoreRecord->completed_missions_count = $missionsCount;
            $scoreRecord->completed_modules_count = $modulesCount;
            $scoreRecord->save();

            $msg = ($validatedScore > $oldScore) 
                ? "Selamat! Rekor baru tercatat: {$validatedScore} XP."
                : "Misi selesai! Skor kali ini ({$validatedScore}) belum melewati High Score kamu.";

            return response()->json([
                'status' => 'success',
                'message' => $msg,
                'next_url' => route('misi.category.levels', $mission->level->category)
            ]);
        });
    }

    /** * //* (Helper) Standarisasi rumus Excel
     */
    private function normalizeFormula(?string $formula) {
        if (!$formula) return "";
        $search  = ["'", '“', '”', '‘', '’', ' '];
        $replace = ['"', '"', '"', '"', '"', ''];
        $clean = strtoupper(trim(str_replace($search, $replace, (string)$formula)));
        
        return $clean;
    }

    /**
     * //* (Helper) Hierarki Feedback (Nudge) - Super Akurat & Cerdas
     */
    private function generateFeedback(string $userAnswer, string $correctAnswer)
    {
        if (empty($userAnswer)) return "KOTAK RAKITAN MASIH KOSONG. MULAI RAKIT RUMUSMU!";

        $pattern = '/(".+?")|[A-Z0-9\%]+|[\(\)\,\;\:\=\"\>\<\$\.\!\?\%]/';
        
        preg_match_all($pattern, strtoupper($correctAnswer), $matchesCorrect);
        preg_match_all($pattern, strtoupper($userAnswer), $matchesUser);

        $tokensCorrect = $matchesCorrect[0];
        $tokensUser = $matchesUser[0];

        $countsCorrect = array_count_values($tokensCorrect);
        $countsUser = array_count_values($tokensUser);

        // 1. PRIORITAS 1: Peringatan Mutlak (Tanda =)
        if (isset($countsCorrect['=']) && !isset($countsUser['='])) {
            return "JANGAN LUPA, RUMUS EXCEL SELALU DIAWALI TANDA SAMA DENGAN (=).";
        }

        // 2. PRIORITAS 2: Cek Keseimbangan Kurung
        if (substr_count($userAnswer, '(') !== substr_count($userAnswer, ')')) {
            return "ADA MASALAH PADA PASANGAN KURUNG. COBA CEK LAGI KESEIMBANGANNYA.";
        }

        // 3. PRIORITAS 3: Peringatan Absolut Fungsi Utama Excel.
        $mainFunctions = ['IF', 'VLOOKUP', 'HLOOKUP', 'SUM', 'AVERAGE', 'MIN', 'MAX', 'AND', 'OR', 'NOT', 'COUNT'];
        foreach ($mainFunctions as $func) {
            if (isset($countsCorrect[$func]) && !isset($countsUser[$func])) {
                return "LOGIKA RUMUS INI BUTUH FUNGSI '{$func}'.";
            }
        }

        // 4. PRIORITAS 4: Deteksi Komponen yang Kurang (Missing)
        foreach ($countsCorrect as $token => $count) {
            if (!isset($countsUser[$token]) || $countsUser[$token] < $count) {
                // Hint pintar untuk $ (Absolute Reference)
                if ($token === '$') {
                    return "ADA REFERENSI SEL YANG HARUS DIKUNCI (ABSOLUT). JANGAN LUPA TANDA DOLLAR ($).";
                }
                return "INVENTARIS KOMPONENMU MASIH ADA YANG KURANG LENGKAP.";
            }
        }

        // 5. PRIORITAS 5: Deteksi Komponen yang Tidak Seharusnya (Penyusup)
        foreach ($countsUser as $token => $count) {
            if (!isset($countsCorrect[$token])) {
                return "ADA KOMPONEN YANG TIDAK SEHARUSNYA BERADA DI RUMUS INI.";
            }
            if ($count > $countsCorrect[$token]) {
                return "TERDETEKSI PEMAKAIAN BLOK YANG BERLEBIHAN. PERIKSA EFISIENSI RUMUSMU.";
            }
        }

        // 6. PRIORITAS 6: Deteksi Kesalahan Urutan (Sequence Error)
        for ($i = 0; $i < count($tokensCorrect); $i++) {
            if (isset($tokensUser[$i]) && $tokensUser[$i] !== $tokensCorrect[$i]) {
                if ($i === 0 && $tokensUser[$i] !== '=') {
                    return "AWALI RAKITAN DENGAN TANDA SAMA DENGAN (=) LALU FUNGSI YANG TEPAT.";
                }
                return "KOMPONEN SUDAH BENAR, TAPI URUTAN LOGIKANYA MASIH PERLU DIPERBAIKI.";
            }
        }

        // Default Fallback
        return "KOMPONEN SUDAH LENGKAP, PERIKSA KEMBALI POSISI DETAILNYA.";
    }

    /**
     * //* (Admin Sync) Opsional: Jika Admin mengubah max_score misi 
     */
    public function syncGlobalXpAfterAdminChange(string $missionId)
    {
        $mission = Mission::findOrFail($missionId);
        $newMax = $mission->max_score;

        DB::transaction(function () use ($missionId, $newMax) {
            Progress::where('mission_id', $missionId)
                ->where('score', '>', $newMax)
                ->update(['score' => $newMax]);

            $userIds = Progress::where('mission_id', $missionId)->pluck('user_id');
            foreach ($userIds as $userId) {
                $total = Progress::where('user_id', $userId)->where('status', 'completed')->sum('score');
                ScoresAndRanking::updateOrCreate(['user_id' => $userId], ['total_xp' => $total]);
            }
        });
    }
}