<?php

namespace App\Http\Controllers;

use App\Models\ScoresAndRanking;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class PeringkatController extends Controller
{
    /** (View) Menampilkan halaman utama papan peringkat (Global) */
    public function index()
    {
        // FIX: Hanya ambil yang punya XP > 0 dan batasi Top 50 saja
        $rankings = ScoresAndRanking::with('user')
            ->where('total_xp', '>', 0)
            ->orderBy('total_xp', 'desc')
            ->take(50)
            ->get();

        return view('peringkat.index', compact('rankings'));
    }

    /** (View) Menampilkan daftar leaderboard berdasarkan XP tertinggi (Global/Kelas) */
    public function show(string $type)
    {
        // 1. Logika: Menggunakan Eager Loading 'user' dengan filter XP > 0
        $query = ScoresAndRanking::with('user')
            ->where('total_xp', '>', 0)
            ->orderBy('total_xp', 'desc');

        // 2. Filter: Logika khusus untuk membatasi peringkat hanya di dalam satu kelas
        if ($type === 'kelas') {
            /** @var User $user */
            $user = Auth::user();
            
            // Mengambil class_id dari user yang login (asumsi kolomnya class_id di tabel users)
            $userClassId = $user->class_id; 
            
            if (!$userClassId) {
                return redirect()->back()->with('error', 'Kamu belum terdaftar di kelas manapun.');
            }

            $query->whereHas('user', function($q) use ($userClassId) {
                $q->where('class_id', $userClassId);
            });
        }

        // 3. Eksekusi query untuk mengambil maksimal Top 50 data ranking
        $rankings = $query->take(50)->get();

        return view('peringkat.index', compact('rankings', 'type'));
    }
}